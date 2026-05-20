<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'predicted_demand',
        'current_stock',
        'suggested_reorder_qty',
        'days_until_stockout',
        'lead_time_days',
        'reorder_point',
        'confidence',
        'status',
        'period_from',
        'period_to',
    ];

    protected $casts = [
        'predicted_demand'  => 'integer',
        'current_stock'     => 'integer',
        'suggested_reorder_qty' => 'integer',
        'days_until_stockout'   => 'integer',
        'lead_time_days'        => 'integer',
        'reorder_point'         => 'integer',
        'confidence'            => 'integer',
        'period_from'           => 'date',
        'period_to'             => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOk($query)
    {
        return $query->where('status', 'ok');
    }

    public function scopeWatch($query)
    {
        return $query->where('status', 'watch');
    }

    public function scopeReorderNow($query)
    {
        return $query->where('status', 'reorder_now');
    }

    public function scopeCritical($query)
    {
        return $query->where('status', 'critical');
    }

    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('status', ['watch', 'reorder_now', 'critical']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Recalculate all derived fields from the raw numbers and persist.
     */
    public function recalculate(): self
    {
        $predicted   = max(0, (int) $this->predicted_demand);
        $stock       = max(0, (int) $this->current_stock);
        $lead        = max(1,  (int) $this->lead_time_days);

        // Days until out-of-stock: daily demand = predicted / 30
        $daysUntil = $predicted > 0
            ? (int) floor($stock / ($predicted / 30))
            : null;

        // Reorder point = lead-time demand (safe stock = 30 % buffer)
        $reorderPoint = (int) ceil(($predicted / 30) * $lead * 1.30);

        // Suggested reorder: top up to predicted demand + 30 % buffer
        $suggested = max(0, (int) ceil(($predicted * 1.30) - $stock));

        // ── Status logic ──────────────────────────────────────────────────────
        if ($stock <= 0) {
            $status = 'critical';
        } elseif ($daysUntil !== null && $daysUntil <= $lead) {
            $status = 'critical';
        } elseif ($stock < $reorderPoint) {
            $status = 'reorder_now';
        } elseif ($stock < $reorderPoint * 1.5) {
            $status = 'watch';
        } else {
            $status = 'ok';
        }

        $this->fill([
            'days_until_stockout' => $daysUntil,
            'reorder_point'       => $reorderPoint,
            'suggested_reorder_qty' => $suggested,
            'status'              => $status,
        ])->save();

        return $this;
    }

    /**
     * Build a badge CSS class based on prediction status.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'critical'    => 'bg-red-100 text-red-800 border-red-200',
            'reorder_now' => 'bg-orange-100 text-orange-800 border-orange-200',
            'watch'       => 'bg-amber-100 text-amber-800 border-amber-200',
            default       => 'bg-green-100 text-green-800 border-green-200',
        };
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'critical'    => 'Critical',
            'reorder_now' => 'Reorder Now',
            'watch'       => 'Watch',
            default       => 'OK',
        };
    }
}
