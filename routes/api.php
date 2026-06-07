<?php

use App\Http\Controllers\Api\AccountingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DimensionController;
use App\Http\Controllers\Api\FixedAssetController;
use App\Http\Controllers\Api\HumanResourceController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ManufacturingController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\SetupController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Sales Module
    Route::apiResource('sales-orders', SalesController::class);
    Route::post('/sales-orders/{salesOrder}/confirm', [SalesController::class, 'confirm']);
    Route::post('/sales-orders/{salesOrder}/cancel', [SalesController::class, 'cancel']);

    // Purchases Module
    Route::apiResource('purchase-orders', PurchaseController::class);
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseController::class, 'receive']);
    Route::post('/purchase-orders/{purchaseOrder}/cancel', [PurchaseController::class, 'cancel']);

    // Items and Inventory Module
    Route::apiResource('items', ItemController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::get('/inventory/current', [InventoryController::class, 'currentStock']);
    Route::get('/inventory/transactions', [InventoryController::class, 'transactions']);
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust']);

    // Customers Module
    Route::apiResource('customers', CustomerController::class);
    Route::get('/customers/{customer}/balance', [CustomerController::class, 'balance']);
    Route::get('/customers/{customer}/orders', [CustomerController::class, 'orders']);

    // Suppliers Module
    Route::apiResource('suppliers', SupplierController::class);
    Route::get('/suppliers/{supplier}/balance', [SupplierController::class, 'balance']);
    Route::get('/suppliers/{supplier}/orders', [SupplierController::class, 'orders']);

    // Manufacturing Module
    Route::get('/manufacturing/bom', [ManufacturingController::class, 'bomIndex']);
    Route::post('/manufacturing/bom', [ManufacturingController::class, 'bomStore']);
    Route::get('/manufacturing/bom/{bom}', [ManufacturingController::class, 'bomShow']);
    Route::put('/manufacturing/bom/{bom}', [ManufacturingController::class, 'bomUpdate']);
    Route::get('/manufacturing/production-orders', [ManufacturingController::class, 'productionIndex']);
    Route::post('/manufacturing/production-orders', [ManufacturingController::class, 'productionStore']);
    Route::get('/manufacturing/production-orders/{order}', [ManufacturingController::class, 'productionShow']);
    Route::post('/manufacturing/production-orders/{order}/release', [ManufacturingController::class, 'productionRelease']);
    Route::post('/manufacturing/production-orders/{order}/start', [ManufacturingController::class, 'productionStart']);
    Route::post('/manufacturing/production-orders/{order}/complete', [ManufacturingController::class, 'productionComplete']);
    Route::get('/manufacturing/work-centers', [ManufacturingController::class, 'workCenters']);

    // Fixed Assets Module
    Route::apiResource('fixed-assets', FixedAssetController::class);
    Route::post('/fixed-assets/{asset}/depreciate', [FixedAssetController::class, 'depreciate']);
    Route::post('/fixed-assets/{asset}/dispose', [FixedAssetController::class, 'dispose']);
    Route::get('/fixed-assets/report', [FixedAssetController::class, 'report']);

    // Dimensions Module
    Route::apiResource('dimensions', DimensionController::class);
    Route::post('/dimensions/assign', [DimensionController::class, 'assign']);
    Route::get('/dimensions/assignments', [DimensionController::class, 'assignments']);
    Route::get('/dimensions/tree', [DimensionController::class, 'tree']);
    Route::get('/dimensions/report', [DimensionController::class, 'report']);

    // Human Resources Module
    Route::get('/hr/employees', [HumanResourceController::class, 'employeesIndex']);
    Route::post('/hr/employees', [HumanResourceController::class, 'employeesStore']);
    Route::get('/hr/employees/{employee}', [HumanResourceController::class, 'employeesShow']);
    Route::put('/hr/employees/{employee}', [HumanResourceController::class, 'employeesUpdate']);
    Route::post('/hr/employees/{employee}/terminate', [HumanResourceController::class, 'terminate']);
    Route::get('/hr/payrolls', [HumanResourceController::class, 'payrollsIndex']);
    Route::post('/hr/payrolls', [HumanResourceController::class, 'payrollStore']);
    Route::get('/hr/payrolls/{payroll}', [HumanResourceController::class, 'payrollShow']);
    Route::post('/hr/payrolls/{payroll}/process', [HumanResourceController::class, 'payrollProcess']);
    Route::post('/hr/payrolls/{payroll}/pay', [HumanResourceController::class, 'payrollPay']);
    Route::get('/hr/departments', [HumanResourceController::class, 'departments']);
    Route::get('/hr/report', [HumanResourceController::class, 'report']);

    // Setup Module
    Route::get('/setup/settings', [SetupController::class, 'settings']);
    Route::post('/setup/settings', [SetupController::class, 'updateSettings']);
    Route::post('/setup/initialize', [SetupController::class, 'initializeSettings']);
    Route::get('/setup/system-info', [SetupController::class, 'systemInfo']);
    Route::post('/setup/backup', [SetupController::class, 'backup']);
    Route::post('/setup/restore', [SetupController::class, 'restore']);

    // Accounting Module
    Route::get('/accounting/accounts', [AccountingController::class, 'accounts']);
    Route::get('/accounting/accounts/tree', [AccountingController::class, 'accountTree']);
    Route::get('/accounting/journal-entries', [AccountingController::class, 'journalEntries']);
    Route::post('/accounting/journal-entries', [AccountingController::class, 'storeJournalEntry']);
    Route::get('/accounting/journal-entries/{journalEntry}', [AccountingController::class, 'showJournalEntry']);
    Route::post('/accounting/journal-entries/{journalEntry}/post', [AccountingController::class, 'postJournalEntry']);
    Route::get('/accounting/trial-balance', [AccountingController::class, 'trialBalance']);
    Route::get('/accounting/balance-sheet', [AccountingController::class, 'balanceSheet']);
    Route::get('/accounting/profit-loss', [AccountingController::class, 'profitLoss']);

    // Reports
    Route::get('/reports/sales', [SalesController::class, 'report']);
    Route::get('/reports/purchases', [PurchaseController::class, 'report']);
    Route::get('/reports/inventory', [InventoryController::class, 'report']);
    Route::get('/reports/profit-loss', [AccountingController::class, 'profitLoss']);
    Route::get('/reports/balance-sheet', [AccountingController::class, 'balanceSheet']);
});
