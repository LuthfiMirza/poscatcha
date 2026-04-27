<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfitReportController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard_admin', [AdminController::class, 'dashboard_admin'])->name('dashboard_admin');
    Route::get('/admin/products', [AdminController::class, 'products_index'])->name('admin.products.index');
    Route::get('/add_product_admin', [AdminController::class, 'add_product'])->name('add_product');
    Route::post('/add_product_admin_process', [AdminController::class, 'add_product_process'])->name('add_product_process');
    Route::get('/edit_product_admin/{id}', [AdminController::class, 'edit_product'])->name('edit_product');
    Route::post('/edit_product_admin_process/{id}', [AdminController::class, 'edit_product_process'])->name('edit_product_process');
    Route::get('/delete_product_admin/{id}', [AdminController::class, 'delete_product'])->name('delete_product');
    Route::post('/delete_product_admin_process/{id}', [AdminController::class, 'delete_product_process'])->name('delete_product_process');
    Route::get('/admin/categories', [AdminController::class, 'categories_index'])->name('admin.categories.index');
    Route::get('/add_category_admin', [AdminController::class, 'add_category'])->name('add_category');
    Route::post('/add_category_admin_process', [AdminController::class, 'add_category_process'])->name('add_category_process');
    Route::get('/edit_category_admin/{id}', [AdminController::class, 'edit_category'])->name('edit_category');
    Route::post('/edit_category_admin_process/{id}', [AdminController::class, 'edit_category_process'])->name('edit_category_process');
    Route::get('/delete_category_admin/{id}', [AdminController::class, 'delete_category'])->name('delete_category');

    Route::get('/sales_data', [AdminController::class, 'sales_data'])->name('sales_data');
    Route::get('/detail_sales_data/{sale_id}', [AdminController::class, 'detail_sales_data'])->name('detail_sales_data');

    Route::get('/stock_movement', [AdminController::class, 'stock_movements'])->name('stock_movement');

    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('/admin/reports/profit', [ProfitReportController::class, 'index'])->name('reports.profit');
    Route::get('/admin/reports/profit/export/excel', [ProfitReportController::class, 'exportExcel'])->name('reports.profit.export.excel');
    Route::get('/admin/reports/profit/export/pdf', [ProfitReportController::class, 'exportPdf'])->name('reports.profit.export.pdf');
    Route::get('/admin/shifts', [ShiftController::class, 'index'])->name('admin.shifts.index');
    Route::get('/admin/shifts/export/excel', [ShiftController::class, 'exportExcel'])->name('admin.shifts.export.excel');
    Route::get('/admin/shifts/export/pdf', [ShiftController::class, 'exportPdf'])->name('admin.shifts.export.pdf');
    Route::get('/admin/chatbot/logs', [AdminController::class, 'chatbot_logs'])->name('admin.chatbot.logs');

    Route::get('/user_data', [AdminController::class, 'user_data'])->name('user_data');
    Route::get('/add_user', [AdminController::class, 'add_user'])->name('add_user');
    Route::post('/add_user_process', [AdminController::class, 'add_user_process'])->name('add_user_process');
    Route::get('/edit_user/{id}', [AdminController::class, 'edit_user'])->name('edit_user');
    Route::post('/edit_user_process/{id}', [AdminController::class, 'edit_user_process'])->name('edit_user_process');
    Route::get('/delete_user/{id}', [AdminController::class, 'delete_user'])->name('delete_user');

    Route::get('/admin_profile', [AdminController::class, 'admin_profile'])->name('admin_profile');
    Route::post('/update_admin_profile', [AdminController::class, 'update_admin_profile'])->name('update_admin_profile');
    Route::post('/update_admin_password', [AdminController::class, 'update_admin_password'])->name('update_admin_password');
});

Route::middleware(['auth', 'verified', 'role:cashier'])->group(function () {
    Route::get('/cashier/shift/open', [ShiftController::class, 'openForm'])->name('cashier.shift.open');
    Route::post('/cashier/shift/open', [ShiftController::class, 'open'])->name('cashier.shift.store');
    Route::get('/cashier/shift/close', [ShiftController::class, 'closeForm'])->name('cashier.shift.close');
    Route::post('/cashier/shift/close', [ShiftController::class, 'close'])->name('cashier.shift.close.store');
    Route::get('/print-receipt/{sale_id}', [CashierController::class, 'printReceipt'])->name('print.receipt');

    Route::middleware('cashier.shift.active')->group(function () {
        Route::get('/list_product', [CashierController::class, 'list_product'])->name('list_product');

        Route::get('selling_product', [CashierController::class, 'selling_product'])->name('selling_product');

        Route::get('/pending_selling_product', [CashierController::class, 'pending_selling_product'])->name('pending_selling_product');
        Route::get('/detail_pending_selling_product/{cart_id}', [CashierController::class, 'detail_pending_selling_product'])->name('detail_pending_selling_product');
        Route::get('/delete_pending_selling_product/{id}', [CashierController::class, 'delete_pending_selling_product'])->name('delete_pending_selling_product');
    });

    Route::get('/cashier_profile', [CashierController::class, 'cashier_profile'])->name('cashier_profile');
    Route::post('/update_cashier_profile', [CashierController::class, 'update_cashier_profile'])->name('update_cashier_profile');
    Route::post('/update_cashier_password', [CashierController::class, 'update_cashier_password'])->name('update_cashier_password');
});


require __DIR__.'/auth.php';
