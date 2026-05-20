<?php
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\TwoFactorVerifyController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\AIVoiceSearchController;
use App\Http\Controllers\AIAudioDescriptionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// // This will include all auth routes including password reset
Auth::routes();

// Login routes with AJAX support
Route::post('/login/ajax', [TwoFactorController::class, 'ajaxLogin'])->name('login.ajax');

// 2FA routes
Route::middleware('guest')->group(function () {
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('/two-factor/recover', [TwoFactorController::class, 'recover'])->name('two-factor.recover');
    Route::post('/two-factor/send-email', [TwoFactorController::class, 'sendEmailOTP'])->name('two-factor.send-email');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Or if you want only password reset routes:
Route::get('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/')->with('success', 'Email verified successfully!'); // or wherever you want after verification
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// ── AI Voice Search ────────────────────────────────────────────────
// 'none' auth so the search bar widget works on the landing page
Route::post('/ai/voice-search', [AIVoiceSearchController::class, 'search'])->name('ai.voice-search');
Route::get('/ai/voice-search/test', [AIVoiceSearchController::class, 'searchText'])->name('ai.voice-search.test');

// ── AI Audio Description ────────────────────────────────────────────
Route::get('/ai/audio-description/{book}', [AIAudioDescriptionController::class, 'show'])
    ->whereNumber('book')->name('ai.audio-description.show');
Route::post('/ai/audio-description/{book}/generate', [AIAudioDescriptionController::class, 'generate'])
    ->middleware('auth')->whereNumber('book')->name('ai.audio-description.generate');
Route::get('/ai/audio/tts/{book}', [AIAudioDescriptionController::class, 'tts'])
    ->whereNumber('book')->name('ai.audio.tts');
Route::get('/ai/browse', [AIAudioDescriptionController::class, 'browse'])
    ->name('ai.browse');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
// Book browsing (public)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
// Category browsing (public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class,
'show'])->name('categories.show');


Route::post('/cart/add/{book}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
// Categories routes
Route::resource('categories', CategoryController::class);

// Admin categories routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');

    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.picture.update');
    Route::delete('/profile/picture', [ProfileController::class, 'removePicture'])->name('profile.picture.remove');

    
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Order routes
    // Review routes
    Route::post('/books/{book}/reviews', [ReviewController::class,'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class,'destroy'])->name('reviews.destroy');
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

});
// Admin-only routes (Category & Book management)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Category management
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/categories/create', [CategoryController::class,'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit',[CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class,'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class,'destroy'])->name('categories.destroy');
    // Book management
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});



// Orders routes
Route::middleware('auth')->group(function () {
    // User orders - accessible to all authenticated users
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
    
    // User can view their own order
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // User order creation (from cart)
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    
    // Admin only routes - using inline authorization
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status');
});

// Two-Factor Authentication Routes
Route::middleware(['auth'])->group(function () {
    // Profile 2FA settings
    Route::get('/profile/two-factor', [App\Http\Controllers\TwoFactorController::class, 'index'])
        ->name('profile.two-factor');
    
    Route::post('/profile/two-factor/setup', [App\Http\Controllers\TwoFactorController::class, 'setup'])
        ->name('profile.two-factor.setup');
    
    Route::post('/profile/two-factor/verify-setup', [App\Http\Controllers\TwoFactorController::class, 'verifySetup'])
        ->name('profile.two-factor.verify-setup');
    
    Route::post('/profile/two-factor/disable', [App\Http\Controllers\TwoFactorController::class, 'disable'])
        ->name('profile.two-factor.disable');
    
    Route::get('/profile/two-factor/recovery-codes', [App\Http\Controllers\TwoFactorController::class, 'showRecoveryCodes'])
        ->name('profile.two-factor.recovery-codes');
    
    Route::post('/profile/two-factor/recovery-codes/regenerate', [App\Http\Controllers\TwoFactorController::class, 'regenerateRecoveryCodes'])
        ->name('profile.two-factor.recovery-codes.regenerate');
});

// 2FA Verification Routes (for login)
Route::middleware('two-factor.partial')->group(function () {
    Route::get('/two-factor/verify', [TwoFactorVerifyController::class, 'showVerifyForm'])->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorVerifyController::class, 'verify'])->name('two-factor.verify.submit');
    Route::post('/two-factor/resend', [TwoFactorVerifyController::class, 'resend'])->name('two-factor.resend');
});

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
    
    Route::get('/notifications/fetch', [App\Http\Controllers\NotificationController::class, 'getNotifications'])
        ->name('notifications.fetch');
    
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-as-read');
    
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});



Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/books/import', [App\Http\Controllers\Admin\BookImportController::class, 'showForm'])
        ->name('admin.books.import.form');
    Route::post('/books/import/upload', [App\Http\Controllers\Admin\BookImportController::class, 'upload'])
        ->name('admin.books.import.upload');
    Route::get('/books/import/status/{id}', [App\Http\Controllers\Admin\BookImportController::class, 'getStatus'])
        ->name('admin.books.import.status');
    Route::get('/books/import/template', [App\Http\Controllers\Admin\BookImportController::class, 'downloadTemplate'])
        ->name('admin.books.import.template');
    Route::get('/books/import/errors/{id}', [App\Http\Controllers\Admin\BookImportController::class, 'downloadErrorReport'])
        ->name('admin.books.import.errors');
});


// routes/web.php

Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Export routes
    Route::post('/books/export', [App\Http\Controllers\Admin\BookExportController::class, 'export'])
        ->name('admin.books.export');
    Route::get('/books/export/status/{id}', [App\Http\Controllers\Admin\BookExportController::class, 'getStatus'])
        ->name('admin.books.export.status');
    Route::get('/books/export/download/{id}', [App\Http\Controllers\Admin\BookExportController::class, 'download'])
        ->name('admin.books.export.download');
    Route::get('/books/export/list', [App\Http\Controllers\Admin\BookExportController::class, 'getExports'])
        ->name('admin.books.export.list');
    Route::delete('/books/export/{id}', [App\Http\Controllers\Admin\BookExportController::class, 'deleteExport'])
        ->name('admin.books.export.delete');
});


// Admin Order Exports
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/orders/export', [App\Http\Controllers\Admin\OrderExportController::class, 'index'])
        ->name('admin.orders.export.index');
    Route::post('/orders/export', [App\Http\Controllers\Admin\OrderExportController::class, 'exportOrders'])
        ->name('admin.orders.export');
    Route::get('/orders/export/status/{id}', [App\Http\Controllers\Admin\OrderExportController::class, 'getExportStatus'])
        ->name('admin.orders.export.status');
    Route::get('/orders/export/download/{id}', [App\Http\Controllers\Admin\OrderExportController::class, 'downloadExport'])
        ->name('admin.orders.export.download');
    
    // Financial Reports
    Route::get('/reports/revenue/export', [App\Http\Controllers\Admin\OrderExportController::class, 'exportRevenueSummary'])
        ->name('admin.reports.revenue.export');
    Route::get('/reports/tax/export', [App\Http\Controllers\Admin\OrderExportController::class, 'exportTaxReport'])
        ->name('admin.reports.tax.export');

    // Scheduled Exports
    Route::get('/exports/scheduled', [App\Http\Controllers\Admin\OrderExportController::class, 'listScheduledExports'])
        ->name('admin.exports.scheduled');
    Route::post('/exports/scheduled', [App\Http\Controllers\Admin\OrderExportController::class, 'createScheduledExport'])
        ->name('admin.exports.scheduled.create');

    // ── AI Sales Prediction & Inventory Optimization ─────────────────────────
    Route::get('/predictions', [App\Http\Controllers\Admin\PredictionController::class, 'index'])
        ->name('admin.predictions.index');
    Route::post('/predictions/refresh', [App\Http\Controllers\Admin\PredictionController::class, 'refresh'])
        ->name('admin.predictions.refresh');
    Route::get('/predictions/{prediction}', [App\Http\Controllers\Admin\PredictionController::class, 'show'])
        ->name('admin.predictions.show');

    // ── AI Voice Search & Audio Description ──────────────────────────────────
    Route::get('/ai/audio-descriptions/regenerate', [App\Http\Controllers\AdminAudioController::class, 'regenerate'])
        ->name('admin.ai.audio-descriptions.regenerate');
    Route::get('/ai/usage', [App\Http\Controllers\AdminAudioController::class, 'usage'])
        ->name('admin.ai.usage');
    Route::post('/ai/usage/clear', [App\Http\Controllers\AdminAudioController::class, 'clearOldLogs'])
        ->name('admin.ai.usage.clear');

    // ── Audit Logs ──────────────────────────────────────────────────────────
    Route::middleware(['auth', 'can:viewAny,App\Models\AuditLog'])->group(function () {
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])
            ->name('admin.audit-logs.index');
        Route::get('/audit-logs/{id}', [App\Http\Controllers\Admin\AuditLogController::class, 'show'])
            ->name('admin.audit-logs.show');
        Route::get('/audit-logs/export', [App\Http\Controllers\Admin\AuditLogController::class, 'export'])
            ->name('admin.audit-logs.export');
        Route::post('/audit-logs/verify-integrity', [App\Http\Controllers\Admin\AuditLogController::class, 'verifyIntegrity'])
            ->name('admin.audit-logs.verify');
        Route::post('/audit-logs/backup', [App\Http\Controllers\Admin\AuditLogController::class, 'backup'])
            ->name('admin.audit-logs.backup');
    });

    // ── Database Backup Management ─────────────────────────────────────────
    Route::get('/backups', [App\Http\Controllers\Admin\BackupController::class, 'index'])
        ->name('admin.backups.index');
    Route::post('/backups', [App\Http\Controllers\Admin\BackupController::class, 'store'])
        ->name('admin.backups.store');
    Route::delete('/backups/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])
        ->name('admin.backups.destroy');
    Route::post('/backups/clean', [App\Http\Controllers\Admin\BackupController::class, 'clean'])
        ->name('admin.backups.clean');
    Route::get('/backups/health', [App\Http\Controllers\Admin\BackupController::class, 'health'])
        ->name('admin.backups.health');
});

// Customer Order Export
Route::middleware(['auth'])->group(function () {
    Route::get('/my-orders/export', [App\Http\Controllers\Admin\OrderExportController::class, 'exportMyOrders'])
        ->name('my-orders.export');
});


require __DIR__.'/auth.php';