<?php
// app/Services/OrderExportService.php

namespace App\Services;

use App\Models\Order;
use App\Models\ExportLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExportReadyMail;

class OrderExportService
{
    protected $chunkSize = 1000;
    
    /**
     * Admin Order Export with Filters
     */
    public function exportOrders($exportId, $filters, $format, $userId)
    {
        $exportLog = ExportLog::find($exportId);
        $exportLog->update(['status' => 'processing']);
        
        try {
            $query = $this->buildOrderQuery($filters);
            $orders = $query->get();
            
            $filePath = $this->generateOrderExport($orders, $filters, $format, $exportId);
            
            $exportLog->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'total_records' => $orders->count(),
                'completed_at' => now()
            ]);
            
            return $filePath;
        } catch (\Exception $e) {
            $exportLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Build Order Query with Filters
     */
    protected function buildOrderQuery($filters)
    {
        $query = Order::with(['user', 'items.book']);
        
        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Customer filter
        if (!empty($filters['customer_id'])) {
            $query->where('user_id', $filters['customer_id']);
        }
        
        if (!empty($filters['customer_email'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('email', 'like', '%' . $filters['customer_email'] . '%');
            });
        }
        
        // Payment status filter
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }
        
        // Amount range filter
        if (!empty($filters['amount_min'])) {
            $query->where('total_amount', '>=', $filters['amount_min']);
        }
        
        if (!empty($filters['amount_max'])) {
            $query->where('total_amount', '<=', $filters['amount_max']);
        }
        
        return $query->orderBy('created_at', 'desc');
    }
    
    /**
     * Generate Order Export File
     */
    protected function generateOrderExport($orders, $filters, $format, $exportId)
    {
        $directory = storage_path('app/exports/orders');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filename = "orders_export_{$exportId}_" . date('Ymd_His') . ".{$format}";
        $filepath = $directory . '/' . $filename;
        
        if ($format === 'csv') {
            $handle = fopen($filepath, 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Headers
            $headers = [
                'Order ID', 'Order Number', 'Customer Name', 'Customer Email',
                'Total Amount', 'Status', 'Payment Status', 'Payment Method',
                'Order Date', 'Items Count', 'Shipping Address'
            ];
            fputcsv($handle, $headers);
            
            // Data
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->order_number,
                    $order->user->name,
                    $order->user->email,
                    $order->total_amount,
                    $order->status,
                    $order->payment_status,
                    $order->payment_method,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->items->count(),
                    $order->shipping_address
                ]);
            }
            
            fclose($handle);
        }
        
        return $filepath;
    }
    
    /**
     * Customer Order Export (Personal Orders Only)
     */
    public function exportCustomerOrders($userId, $format = 'pdf')
    {
        $orders = Order::where('user_id', $userId)
            ->with(['items.book'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        if ($format === 'pdf') {
            return $this->generateCustomerInvoicePDF($orders, $userId);
        } else {
            return $this->generateCustomerOrdersCSV($orders, $userId);
        }
    }
    
    /**
     * Generate Customer Invoice PDF
     */
    protected function generateCustomerInvoicePDF($orders, $userId)
    {
        $user = User::find($userId);
        $directory = storage_path('app/exports/customers');
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filename = "customer_orders_{$user->id}_" . date('Ymd') . ".pdf";
        $filepath = $directory . '/' . $filename;
        
        $html = view('exports.customer-invoice', compact('orders', 'user'))->render();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($filepath, $dompdf->output());
        
        return $filepath;
    }
    
    /**
     * Financial Reporting - Revenue Summary
     */
    public function exportRevenueSummary($filters, $format = 'csv')
    {
        $query = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid');
            
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        $summary = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total_amount'),
            'average_order_value' => $query->avg('total_amount'),
            'orders_by_status' => Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'daily_revenue' => $this->getDailyRevenue($filters),
            'top_customers' => $this->getTopCustomers($filters),
            'top_products' => $this->getTopProducts($filters)
        ];
        
        return $this->generateRevenueReport($summary, $filters, $format);
    }
    
    /**
     * Tax Report Export
     */
    public function exportTaxReport($filters, $format = 'csv')
    {
        $taxRate = 0.12; // 12% VAT
        $query = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid');
            
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        $orders = $query->get();
        $totalSales = $orders->sum('total_amount');
        $totalTax = $totalSales * $taxRate;
        $netSales = $totalSales - $totalTax;
        
        $taxReport = [
            'period' => [
                'from' => $filters['date_from'] ?? 'All time',
                'to' => $filters['date_to'] ?? date('Y-m-d')
            ],
            'total_sales' => $totalSales,
            'tax_rate' => $taxRate * 100 . '%',
            'total_tax' => $totalTax,
            'net_sales' => $netSales,
            'orders_count' => $orders->count(),
            'tax_by_month' => $this->getTaxByMonth($filters)
        ];
        
        return $this->generateTaxReport($taxReport, $filters, $format);
    }
    
    /**
     * Helper Methods
     */
    protected function getDailyRevenue($filters)
    {
        $query = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('date');
            
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->limit(30)->get();
    }
    
    protected function getTopCustomers($filters)
    {
        return Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'), DB::raw('COUNT(*) as order_count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
    }
    
    protected function getTopProducts($filters)
    {
        return DB::table('order_items')
            ->join('books', 'order_items.book_id', '=', 'books.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('books.title', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('books.id', 'books.title')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
    }
    
    protected function getTaxByMonth($filters)
    {
        $taxRate = 0.12;
        
        $query = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(total_amount) as sales'));
            
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        $monthlySales = $query->groupBy('month')->get();
        
        foreach ($monthlySales as $item) {
            $item->tax = $item->sales * $taxRate;
            $item->net_sales = $item->sales - $item->tax;
        }
        
        return $monthlySales;
    }
    
    protected function generateRevenueReport($summary, $filters, $format)
    {
        $directory = storage_path('app/exports/financial');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filename = "revenue_summary_" . date('Ymd_His') . ".{$format}";
        $filepath = $directory . '/' . $filename;
        
        if ($format === 'csv') {
            $handle = fopen($filepath, 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['REVENUE SUMMARY REPORT']);
            fputcsv($handle, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['SUMMARY METRICS']);
            fputcsv($handle, ['Total Orders', $summary['total_orders']]);
            fputcsv($handle, ['Total Revenue', number_format($summary['total_revenue'], 2)]);
            fputcsv($handle, ['Average Order Value', number_format($summary['average_order_value'], 2)]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['ORDERS BY STATUS']);
            foreach ($summary['orders_by_status'] as $status) {
                fputcsv($handle, [ucfirst($status->status), $status->count]);
            }
            fputcsv($handle, []);
            
            fputcsv($handle, ['TOP CUSTOMERS']);
            fputcsv($handle, ['Customer', 'Orders', 'Total Spent']);
            foreach ($summary['top_customers'] as $customer) {
                fputcsv($handle, [
                    $customer->user->name,
                    $customer->order_count,
                    number_format($customer->total_spent, 2)
                ]);
            }
            
            fclose($handle);
        }
        
        return $filepath;
    }
    
    protected function generateTaxReport($taxReport, $filters, $format)
    {
        $directory = storage_path('app/exports/financial');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filename = "tax_report_" . date('Ymd_His') . ".{$format}";
        $filepath = $directory . '/' . $filename;
        
        if ($format === 'csv') {
            $handle = fopen($filepath, 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['TAX REPORT']);
            fputcsv($handle, ['Period:', $taxReport['period']['from'] . ' to ' . $taxReport['period']['to']]);
            fputcsv($handle, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Sales (Gross)', number_format($taxReport['total_sales'], 2)]);
            fputcsv($handle, ['Tax Rate', $taxReport['tax_rate']]);
            fputcsv($handle, ['Total Tax Collected', number_format($taxReport['total_tax'], 2)]);
            fputcsv($handle, ['Net Sales', number_format($taxReport['net_sales'], 2)]);
            fputcsv($handle, ['Number of Orders', $taxReport['orders_count']]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['TAX BY MONTH']);
            fputcsv($handle, ['Month', 'Gross Sales', 'Tax', 'Net Sales']);
            foreach ($taxReport['tax_by_month'] as $item) {
                fputcsv($handle, [
                    $item->month,
                    number_format($item->sales, 2),
                    number_format($item->tax, 2),
                    number_format($item->net_sales, 2)
                ]);
            }
            
            fclose($handle);
        }
        
        return $filepath;
    }
}