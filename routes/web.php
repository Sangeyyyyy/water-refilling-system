<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalkInOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\PpmpController;
use App\Http\Controllers\Api\OfficePpmpController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\InventoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if (in_array($user->role, ['admin', 'director', 'manager', 'staff'])) {
            return redirect()->route('home');
        }
    }
    
    $unitPrice = \App\Models\Setting::getUnitPrice();    
    $deliveryDaysStr = \App\Models\Setting::get('delivery_days_text', 'Tuesday and Friday');
    
    return view('welcome', compact('unitPrice', 'deliveryDaysStr'));
})->name('welcome');

Route::get('/order', [OrderController::class, 'create'])->name('orders.create');
Route::post('/order', [OrderController::class, 'store'])->name('orders.store');
Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('orders.success');

Auth::routes(['register' => true]);

Route::middleware(['auth:web,client'])->group(function () { // Grouped authenticated routes
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/admin/history', [AdminOrderController::class, 'history'])->middleware('role:admin,director,manager,staff')->name('admin.history');
    Route::put('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::put('/admin/orders/batch-status', [AdminOrderController::class, 'batchUpdateStatus'])->name('admin.orders.batch-status');
    Route::delete('/admin/orders/batch-delete', [AdminOrderController::class, 'batchDestroy'])->name('admin.orders.batch-delete');
    Route::delete('/admin/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::post('/admin/walkin', [WalkInOrderController::class, 'store'])->name('admin.walkin.store');

    Route::get('/reports/billing/{order}', [ReportController::class, 'billing'])->name('reports.billing');
    Route::get('/reports/delivery-receipt/{order}', [ReportController::class, 'deliveryReceipt'])->name('reports.delivery-receipt');
    Route::get('/reports/delivery-receipts/batch', [ReportController::class, 'batchDeliveryReceipts'])->name('reports.delivery-receipts.batch');
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/reports/hub', [ReportController::class, 'hub'])->name('admin.reports.hub');
        Route::get('/admin/reports', [ReportController::class, 'hub'])->name('admin.reports.index');
        Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
        Route::get('/admin/reports/print/financial', [ReportController::class, 'printFinancial'])->name('admin.reports.print.financial');
        Route::get('/admin/reports/print/operational', [ReportController::class, 'printOperational'])->name('admin.reports.print.operational');
        Route::get('/admin/reports/print/inventory', [ReportController::class, 'printInventory'])->name('admin.reports.print.inventory');

        Route::get('/organization-hub', [\App\Http\Controllers\Admin\OrganizationHubController::class, 'index'])->name('admin.org-hub.index');

        // User Management (Admin only)
        Route::resource('users', UserController::class);
        Route::resource('clients', \App\Http\Controllers\ClientController::class);

        // Organizational Management
        Route::delete('/campuses/bulk-delete', [CampusController::class, 'bulkDestroy'])->name('campuses.bulk-delete');
        Route::delete('/college-offices/bulk-delete', [\App\Http\Controllers\Admin\CollegeOfficeController::class, 'bulkDestroy'])->name('college-offices.bulk-delete');
        Route::delete('/divisions/bulk-delete', [DivisionController::class, 'bulkDestroy'])->name('divisions.bulk-delete');
        Route::delete('/offices/bulk-delete', [OfficeController::class, 'bulkDestroy'])->name('offices.bulk-delete');

        Route::resource('campuses', CampusController::class);
        Route::resource('college-offices', \App\Http\Controllers\Admin\CollegeOfficeController::class);
        Route::resource('divisions', DivisionController::class);
        Route::resource('offices', OfficeController::class);
        Route::get('/ppmps/managers', [PpmpController::class, 'getManagers'])->name('ppmps.managers')->middleware('role:admin,director,manager');
        Route::post('/ppmps/bulk-delete', [PpmpController::class, 'bulkDelete'])->name('ppmps.bulk-delete')->middleware('role:admin,director,manager');
        Route::resource('ppmps', PpmpController::class)->middleware('role:admin,director,manager');
        
        // System Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Inventory Management
        Route::middleware(['role:admin,superadmin,manager,director,unit_admin'])->group(function () {
            Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        });
        
        Route::middleware(['role:admin,superadmin,manager,director'])->group(function () {
            Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
            Route::match(['put', 'patch'], '/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        });

        Route::middleware(['role:admin,superadmin'])->group(function () {
            Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        });

        // Activity Logs
        Route::get('logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.logs');
    });
});

// API for PPMP Balance (Publicly accessible for guest orders)
Route::get('/api/offices/{office}/ppmp-balance', [OfficePpmpController::class, 'getBalance'])->name('api.offices.ppmp-balance');
Route::get('/api/offices/{office}/ppmp-budget-codes', [OfficePpmpController::class, 'getBudgetCodes'])->name('api.offices.ppmp-budget-codes');

// Verification Route
Route::get('/check-client-count-final-verification', function() {
    return "Total Employees in Database: " . \App\Models\Client::count();
});

// Emergency Production Seeding Route - REMOVE AFTER USE

// Emergency Production Seeding Route - REMOVE AFTER USE
Route::get('/force-seed-production-final-sync', function() {
    try {
        set_time_limit(600); // 10 minutes
        ini_set('memory_limit', '512M');
        
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'OfficialDataSeeder', '--force' => true]);
        return "Seeding successful! <br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Seeding failed: " . $e->getMessage();
    }
});
