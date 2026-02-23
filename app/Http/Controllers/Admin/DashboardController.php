<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_books' => Book::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'recent_orders' => Order::with('user')
                                ->latest()
                                ->take(5)
                                ->get(),
            'recent_users' => User::latest()
                                ->take(5)
                                ->get()
        ];

        return view('admin.dashboard', compact('stats'));
    }
}