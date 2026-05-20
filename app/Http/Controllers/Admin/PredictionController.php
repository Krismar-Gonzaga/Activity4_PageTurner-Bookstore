<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\SalesPrediction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PredictionController extends Controller
{
    /**
     * AI Sales Prediction & Inventory Optimization Dashboard.
     *
     * Uses a lightweight statistical forecast based on historical
     * order sales and a configurable lead-time safety buffer.
     */
    public function index(Request $request)
    {
        // ── Filters ──────────────────────────────────────────────────────────
        $statusFilter = $request->filled('status') ? $request->status : null;
        $categoryId   = $request->filled('category') ? $request->category : null;
        $search       = $request->filled('search') ? $request->search : null;

        // ── Build base query with eager-loads ────────────────────────────────
        $query = SalesPrediction::with(['book.category'])
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->when($categoryId,   fn ($q) => $q->whereHas('book', fn ($b) => $b->where('category_id', $categoryId)))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('book', function ($b) use ($search) {
                    $b->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
                });
            });

        $predictions = $query->orderByRaw("
            FIELD(status, 'critical', 'reorder_now', 'watch', 'ok')
        ")->paginate(15)->withQueryString();

        // ── Summary stats (single aggregated SQL per counter — no OOM) ───────
        $statsAgg = SalesPrediction::selectRaw("
                COUNT(*)                                                     AS total_tracked,
                SUM(status = 'ok')                                           AS ok_count,
                SUM(status = 'watch')                                        AS watch_count,
                SUM(status = 'reorder_now')                                  AS reorder_now_count,
                SUM(status = 'critical')                                     AS critical_count,
                SUM(status IN ('watch','reorder_now','critical'))            AS needs_attention,
                SUM(predicted_demand * (SELECT price FROM books WHERE books.id = sales_predictions.book_id)) AS total_predicted_value,
                SUM(current_stock      * (SELECT price FROM books WHERE books.id = sales_predictions.book_id)) AS total_stock_value
            ")->first();

        $totalTracked  = (int) $statsAgg->total_tracked;
        $criticalCount = (int) $statsAgg->critical_count;
        $rnCount       = (int) $statsAgg->reorder_now_count;
        $wCount        = (int) $statsAgg->watch_count;
        $needsAttn     = (int) $statsAgg->needs_attention;

        // Books without a prediction: use count + distinct to avoid 1M-row NOT EXISTS scan
        $booksWithoutPrediction = Book::count() - $totalTracked;

        $summary = [
            'total_tracked'           => $totalTracked,
            'ok_count'                => $totalTracked - $needsAttn,
            'watch_count'             => $wCount,
            'reorder_now_count'       => $rnCount,
            'critical_count'          => $criticalCount,
            'needs_attention'         => $needsAttn,
            'total_predicted_value'   => (float) $statsAgg->total_predicted_value,
            'total_stock_value'       => (float) $statsAgg->total_stock_value,
            'books_without_prediction'=> (int) $booksWithoutPrediction,
        ];

        $categories = Category::withCount('books')->get();

        return view('admin.predictions.index', compact(
            'predictions', 'summary', 'categories'
        ));
    }

    /**
     * Generate or refresh predictions for all books.
     *
     * Chunks through books in batches of 500 so the 1M-row DB never hits
     * an OOM. Per-book 30-day and 180-day sales are computed with a single
     * pre-aggregated SQL query, not N+1 iterations.
     */
    public function refresh(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        // ── Pre-aggregate 30-day and 180-day sales per book in two queries ───
        $sales30 = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->groupBy('order_items.book_id')
            ->select('order_items.book_id')
            ->selectRaw('SUM(order_items.quantity) AS total_qty')
            ->pluck('total_qty', 'order_items.book_id')
            ->toArray();

        $sales180 = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subDays(180))
            ->groupBy('order_items.book_id')
            ->select('order_items.book_id')
            ->selectRaw('SUM(order_items.quantity) AS total_qty')
            ->pluck('total_qty', 'order_items.book_id')
            ->toArray();

        $generated = 0;
        $updated   = 0;
        $now       = now();

        // ── Pre-load all existing prediction book_ids (1 query, 1M rows into memory) ─
        $existingIds = SalesPrediction::query()
            ->select('book_id')
            ->pluck('book_id')
            ->toArray();

        // ── Stream through all books in chunks of 500 ─────────────────────────
        Book::chunkById(500, function ($books) use (
            &$generated, &$updated, $sales30, $sales180, $now, &$existingIds
        ) {
            // Only keep the books that actually changed
            $insertRows = [];

            foreach ($books as $book) {
                $qty30  = (int) ($sales30[$book->id]  ?? 0);
                $qty180 = (int) ($sales180[$book->id] ?? 0);

                $daily30  = $qty30  / 30;
                $daily180 = $qty180 / 180;

                $weightedAvg = round($daily30 * 0.70 + $daily180 * 0.30, 2);
                $predicted30 = max(1, (int) round($weightedAvg * 30));

                $reorderPoint  = (int) ceil(($predicted30 / 30) * 14 * 1.30);
                $suggestedQty  = max(0, (int) ceil(($predicted30 * 1.30) - $book->stock_quantity));
                $daysUntil     = $predicted30 > 0
                    ? (int) floor($book->stock_quantity / ($predicted30 / 30))
                    : null;

                if ($book->stock_quantity <= 0) {
                    $status = 'critical';
                } elseif ($daysUntil !== null && $daysUntil <= 14) {
                    $status = 'critical';
                } elseif ($book->stock_quantity < $reorderPoint) {
                    $status = 'reorder_now';
                } elseif ($book->stock_quantity < $reorderPoint * 1.5) {
                    $status = 'watch';
                } else {
                    $status = 'ok';
                }

                $insertRows[] = [
                    'book_id'              => $book->id,
                    'predicted_demand'     => $predicted30,
                    'current_stock'        => $book->stock_quantity,
                    'suggested_reorder_qty'=> $suggestedQty,
                    'days_until_stockout'  => $daysUntil,
                    'lead_time_days'       => 14,
                    'reorder_point'        => $reorderPoint,
                    'confidence'           => 75,
                    'status'               => $status,
                    'period_from'          => $now,
                    'period_to'            => $now->copy()->addDays(30),
                    'updated_at'           => $now,
                ];
            }

            // ── One single upsert per chunk: ~2 000 queries total ──────────────
            $dbInsertRows = [];
            foreach ($insertRows as $row) {
                $dbInsertRows[$row['book_id']] = $row;
            }

            if ($dbInsertRows) {
                DB::table('sales_predictions')->upsert(
                    array_values($dbInsertRows),
                    ['book_id']
                );

                // Count new vs updated from the pre-loaded set
                foreach (array_keys($dbInsertRows) as $bid) {
                    in_array($bid, $existingIds, true) ? $updated++ : $generated++;
                }

                // Update the in-memory set so we don't double-count across chunks
                $existingIds = array_merge($existingIds, array_keys($dbInsertRows));
            }
        });

        return redirect()->route('admin.predictions.index')
            ->with('success', "Predictions refreshed: $generated new, $updated updated.");
    }

    /**
     * Show a single book's prediction detail with chart-ready data.
     */
    public function show($id)
    {
        $prediction = SalesPrediction::with(['book.reviews', 'book.category'])->findOrFail($id);

        // 6-month sales history for inline sparkline
        $history = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m') as month"),
                DB::raw('SUM(order_items.quantity) as total')
            )
            ->where('order_items.book_id', $prediction->book_id)
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return view('admin.predictions.show', compact('prediction', 'history'));
    }
}
