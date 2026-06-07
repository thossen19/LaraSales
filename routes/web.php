<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\SalesSetupController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\FixedAssetsController;

use App\Models\Refline;
use App\Models\Setting;
use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\PaymentTerm;
use App\Models\Shipper;
use App\Models\SalesPoint;
use App\Models\Printer;
use App\Models\ContactCategory;
use App\Models\Attachment;
use App\Models\Company;
use App\Models\BankAccount;
use App\Models\QuickEntry;
use App\Models\QuickEntryLine;
use App\Models\Tag;
use App\Models\Currency;

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
    return view('welcome');
});

Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Sales Module - Transactions
Route::get('/sales/quotations', [SalesController::class, 'quotationsIndex'])->name('sales.quotations.index');
Route::match(['GET', 'POST'], '/sales/quotations/create', function () {
    $message = '';
    $error = '';

    $customers = \DB::table('customers')->where('status', 'active')->orderBy('name')->get();
    $salesTypes = \DB::table('sales_types')->where('status', 'active')->orderBy('type_name')->get();
    $locations = \Schema::hasTable('locations') ? \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get() : [];
    $shippers = \App\Models\Shipper::where('inactive', false)->orderBy('shipper_name')->get();
    $items = \DB::table('items')->where('is_active', true)->whereIn('mb_flag', ['B', 'M', 'D', 'S'])->orderBy('code')->get(['code', 'name', 'unit_of_measure', 'cost_price']);

    $defaultCart = [
        'customer_id' => '', 'branch_id' => '',
        'reference' => '', 'ord_date' => date('Y-m-d'),
        'delivery_date' => date('Y-m-d', strtotime('+30 days')),
        'sales_type' => '', 'payment' => '', 'location' => '',
        'ship_via' => '', 'deliver_to' => '',
        'delivery_address' => '', 'phone' => '',
        'cust_ref' => '', 'comments' => '',
        'freight_cost' => 0, 'line_items' => [],
        'stock_id' => '', 'item_description' => '', 'qty' => 1, 'price' => 0, 'Disc' => 0,
    ];

    if (request('NewQuotation')) {
        session(['quote_items' => null, 'quote_edit_index' => null]);
    }

    if (!session()->has('quote_items')) {
        session(['quote_items' => $defaultCart]);
    }

    if (request()->isMethod('POST')) {
        $cart = array_merge($defaultCart, session('quote_items', []));
        $cart['customer_id'] = request('customer_id', $cart['customer_id']);
        $cart['branch_id'] = request('branch_id', $cart['branch_id']);
        $cart['reference'] = request('ref', $cart['reference']);
        $cart['ord_date'] = request('OrderDate', $cart['ord_date']);
        $cart['delivery_date'] = request('delivery_date', $cart['delivery_date']);
        $cart['sales_type'] = request('sales_type', $cart['sales_type']);
        $cart['payment'] = request('payment', $cart['payment']);
        $cart['location'] = request('Location', $cart['location']);
        $cart['ship_via'] = request('ship_via', $cart['ship_via']);
        $cart['deliver_to'] = request('deliver_to', $cart['deliver_to']);
        $cart['delivery_address'] = request('delivery_address', $cart['delivery_address']);
        $cart['phone'] = request('phone', $cart['phone']);
        $cart['cust_ref'] = request('cust_ref', $cart['cust_ref']);
        $cart['comments'] = request('Comments', $cart['comments']);
        $cart['freight_cost'] = (float) request('freight_cost', $cart['freight_cost']);
        $cart['stock_id'] = request('stock_id', $cart['stock_id'] ?? '');
        $cart['item_description'] = request('item_description', $cart['item_description'] ?? '');
        $cart['qty'] = request('qty', $cart['qty'] ?? 1);
        $cart['price'] = request('price', $cart['price'] ?? 0);
        $cart['Disc'] = request('Disc', $cart['Disc'] ?? 0);

        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['line_items'][$delete_id])) {
            unset($cart['line_items'][$delete_id]);
            $cart['line_items'] = array_values($cart['line_items']);
            session(['quote_items' => $cart, 'quote_edit_index' => null]);
        }

        if (request('AddItem')) {
            $stock_id = request('stock_id', '');
            $qty = (float) request('qty', 0);
            $price = (float) request('price', 0);
            $discPct = (float) request('Disc', 0);
            $description = request('item_description', '');

            if (empty($stock_id)) {
                $error = 'You must select an item.';
            } elseif ($qty <= 0) {
                $error = 'The quantity entered is invalid.';
            } elseif ($price < 0) {
                $error = 'Price must be entered and cannot be less than 0.';
            } elseif ($discPct < 0 || $discPct > 100) {
                $error = 'Discount percent must be between 0 and 100.';
            } else {
                $item = \DB::table('items')->where('code', $stock_id)->first();
                $cart['line_items'][] = [
                    'stock_id' => $stock_id,
                    'item_description' => $description ?: ($item->name ?? ''),
                    'quantity' => $qty,
                    'units' => $item->unit_of_measure ?? 'each',
                    'price' => $price,
                    'discount_percent' => $discPct / 100,
                ];
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                $cart['price'] = 0;
                $cart['Disc'] = 0;
                session(['quote_items' => $cart, 'quote_edit_index' => null]);
            }
        }

        if (request('UpdateItem')) {
            $edit_idx = session('quote_edit_index');
            if ($edit_idx !== null && isset($cart['line_items'][$edit_idx])) {
                $qty = (float) request('qty', 0);
                $price = (float) request('price', 0);
                $discPct = (float) request('Disc', 0);
                $description = request('item_description', '');

                if ($qty <= 0) {
                    $error = 'The quantity entered is invalid.';
                } elseif ($price < 0) {
                    $error = 'Price must be entered and cannot be less than 0.';
                } elseif ($discPct < 0 || $discPct > 100) {
                    $error = 'Discount percent must be between 0 and 100.';
                } else {
                    $cart['line_items'][$edit_idx]['quantity'] = $qty;
                    $cart['line_items'][$edit_idx]['price'] = $price;
                    $cart['line_items'][$edit_idx]['discount_percent'] = $discPct / 100;
                    $cart['line_items'][$edit_idx]['item_description'] = $description;
                    session(['quote_items' => $cart, 'quote_edit_index' => null]);
                }
            }
        }

        if (request('CancelItemChanges')) {
            session(['quote_edit_index' => null]);
        }

        $edit_id = request('Edit');
        if ($edit_id !== null && isset($cart['line_items'][$edit_id])) {
            session(['quote_edit_index' => $edit_id]);
        }

        if (request('ProcessOrder')) {
            $input_error = 0;
            if (empty($cart['customer_id'])) { $error = 'There is no customer selected.'; $input_error = 1; }
            elseif (empty($cart['branch_id'])) { $error = 'This customer has no branch defined.'; $input_error = 1; }
            elseif (count($cart['line_items']) == 0) { $error = 'You must enter at least one non empty item line.'; $input_error = 1; }
            elseif (empty($cart['reference'])) { $error = 'You must enter a reference.'; $input_error = 1; }
            elseif (empty($cart['ord_date'])) { $error = 'The entered date is invalid.'; $input_error = 1; }

            if (!$input_error) {
                // Check for duplicate reference
                $existing = \DB::table('sales_quotations')->where('quotation_number', $cart['reference'])->exists();
                if ($existing) {
                    $error = 'The reference "' . $cart['reference'] . '" is already used.';
                    $input_error = 1;
                }
            }

            if (!$input_error) {
                $subtotal = 0;
                $lineTotals = [];
                foreach ($cart['line_items'] as $i => $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    $lineTotals[$i] = $lineTotal;
                    $subtotal += $lineTotal;
                }
                $totalBeforeTax = $subtotal + $cart['freight_cost'];

                $customerRec = \DB::table('customers')->where('id', $cart['customer_id'])->first();
                $salesPersonId = $customerRec->sales_person_id ?? null;

                $quoteId = \DB::table('sales_quotations')->insertGetId([
                    'quotation_number' => $cart['reference'],
                    'quotation_date' => $cart['ord_date'],
                    'expiry_date' => $cart['delivery_date'],
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'],
                    'sales_type_id' => $cart['sales_type'] ?: null,
                    'sales_person_id' => $salesPersonId,
                    'status' => 'draft',
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $totalBeforeTax,
                    'discount_amount' => 0,
                    'customer_notes' => $cart['comments'],
                    'terms_and_conditions' => '',
                    'internal_notes' => '',
                    'delivery_address' => $cart['delivery_address'],
                    'deliver_to' => $cart['deliver_to'],
                    'reference' => $cart['reference'],
                    'freight_cost' => $cart['freight_cost'],
                    'payment' => $cart['payment'] ?? '',
                    'location' => $cart['location'],
                    'ship_via' => $cart['ship_via'],
                    'phone' => $cart['phone'],
                    'cust_ref' => $cart['cust_ref'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($cart['line_items'] as $i => $li) {
                    $lineTotal = $lineTotals[$i];
                    $discAmt = $li['quantity'] * $li['price'] * $li['discount_percent'];
                    \DB::table('quotation_line_items')->insert([
                        'quotation_id' => $quoteId,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int) $li['quantity'],
                        'unit_price' => $li['price'],
                        'discount_percentage' => $li['discount_percent'] * 100,
                        'discount_amount' => $discAmt,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                        'line_total' => $lineTotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                session(['quote_items' => null, 'quote_edit_index' => null]);
                return redirect()->route('sales.quotations.create', ['AddedQU' => $quoteId]);
            }
        }

        session(['quote_items' => $cart]);
    }

    $cart = array_merge($defaultCart, session('quote_items', []));

    $addedQU = request('AddedQU');
    $edit_index = session('quote_edit_index');

    $branches = $cart['customer_id'] ? \DB::table('customer_branches')->where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get() : [];
    $customerInfo = $cart['customer_id'] ? \DB::table('customers')->where('id', $cart['customer_id'])->first() : null;

    $defaultRef = '';
    $defaultRefLine = \DB::table('reflines')->where('trans_type', 32)->where('default', true)->first();
    if ($defaultRefLine) {
        $lastRef = \DB::table('sales_quotations')->max('id');
        $nextNum = ($lastRef ?: 0) + 1;
        $pattern = $defaultRefLine->pattern ?: '{001}';
        $defaultRef = $defaultRefLine->prefix . preg_replace_callback('/\{(\d+)\}/', function($m) use ($nextNum) {
            return str_pad($nextNum, strlen($m[1]), '0', STR_PAD_LEFT);
        }, $pattern);
    } else {
        $lastId = \DB::table('sales_quotations')->max('id') ?: 0;
        $defaultRef = 'QT-' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }

    $paymentTerms = \DB::table('payment_terms')->where('inactive', false)->orderBy('terms')->get();

    return view('sales.quotations.create', compact(
        'message', 'error', 'customers', 'salesTypes', 'locations', 'shippers',
        'items', 'cart', 'addedQU', 'edit_index', 'branches', 'customerInfo', 'defaultRef', 'paymentTerms'
    ));
})->name('sales.quotations.create');
Route::get('/sales/quotations/{quotation}', [SalesController::class, 'quotationsShow'])->name('sales.quotations.show');
Route::match(['GET', 'POST'], '/sales/quotations/{quotation}/edit', [SalesController::class, 'quotationsEdit'])->name('sales.quotations.edit');
Route::put('/sales/quotations/{quotation}', [SalesController::class, 'quotationsUpdate'])->name('sales.quotations.update');
Route::delete('/sales/quotations/{quotation}', [SalesController::class, 'quotationsDestroy'])->name('sales.quotations.destroy');
Route::match(['GET', 'POST'], '/sales/orders/create', [SalesController::class, 'ordersCreate'])->name('sales.orders.create');
Route::post('/sales/orders', [SalesController::class, 'ordersStore'])->name('sales.orders.store');
Route::get('/sales/orders/{order}', [SalesController::class, 'ordersShow'])->name('sales.orders.show');
Route::match(['GET', 'POST'], '/sales/orders/{order}/edit', [SalesController::class, 'ordersEdit'])->name('sales.orders.edit');
Route::put('/sales/orders/{order}', [SalesController::class, 'ordersUpdate'])->name('sales.orders.update');
Route::delete('/sales/orders/{order}', [SalesController::class, 'ordersDestroy'])->name('sales.orders.destroy');
Route::match(['GET', 'POST'], '/sales/delivery/direct', [SalesController::class, 'directDelivery'])->name('sales.delivery.direct');
Route::match(['GET', 'POST'], '/sales/invoice/direct', [SalesController::class, 'directInvoice'])->name('sales.invoice.direct');
Route::match(['GET', 'POST'], '/sales/delivery/from-order', [SalesController::class, 'deliveryFromOrder'])->name('sales.delivery.from-order');
Route::match(['GET', 'POST'], '/sales/invoice/from-delivery', [SalesController::class, 'invoiceFromDelivery'])->name('sales.invoice.from-delivery');
Route::get('/sales/invoice/prepaid', function () { return view('sales.invoice.prepaid'); })->name('sales.invoice.prepaid');
Route::get('/sales/delivery/template', function () { return view('sales.delivery.template'); })->name('sales.delivery.template');
Route::get('/sales/invoice/template', function () { return view('sales.invoice.template'); })->name('sales.invoice.template');
Route::get('/sales/invoice/recurrent', function () { return view('sales.invoice.recurrent'); })->name('sales.invoice.recurrent');

Route::match(['GET', 'POST'], '/sales/payments', [SalesController::class, 'paymentsIndex'])->name('sales.payments.index');
Route::get('/sales/payments/{payment}/print', [SalesController::class, 'paymentsPrint'])->name('sales.payments.print');
Route::match(['GET', 'POST'], '/sales/credit-invoice', [SalesController::class, 'creditInvoice'])->name('sales.credit-invoice');
Route::match(['GET', 'POST'], '/sales/credit-notes', [SalesController::class, 'creditNotesIndex'])->name('sales.credit-notes.index');
Route::get('/sales/credit-notes/{creditNote}/print', [SalesController::class, 'creditNotesPrint'])->name('sales.credit-notes.print');
Route::match(['GET', 'POST'], '/sales/allocations/customer-allocate', [SalesController::class, 'customerAllocate'])->name('sales.allocations.customer-allocate');
Route::get('/sales/allocation', function () { return view('sales.allocation.index'); })->name('sales.allocation.index');

Route::get('/sales/inquiries/quotations', [App\Http\Controllers\SalesController::class, 'quotationsInquiry'])->name('sales.inquiries.quotations');
Route::get('/sales/inquiries/orders', [App\Http\Controllers\SalesController::class, 'ordersInquiry'])->name('sales.inquiries.orders');
Route::get('/sales/inquiries/transactions', [App\Http\Controllers\SalesController::class, 'transactionsInquiry'])->name('sales.inquiries.transactions');
Route::get('/sales/inquiries/allocation', [App\Http\Controllers\SalesController::class, 'allocationInquiry'])->name('sales.inquiries.allocation');

// Sales Module - Reports
Route::get('/sales/reports', function () { return view('sales.reports.index'); })->name('sales.reports');
Route::get('/sales/reports/customer', function () { return view('sales.reports.customer'); })->name('sales.reports.customer');
Route::get('/sales/reports/sales', function () { return view('sales.reports.sales'); })->name('sales.reports.sales');

// Sales Module - Customer Management
Route::get('/sales/customers', [SalesController::class, 'customersIndex'])->name('sales.customers.index');
Route::get('/sales/customers/create', [SalesController::class, 'customersCreate'])->name('sales.customers.create');
Route::post('/sales/customers', [SalesController::class, 'customersStore'])->name('sales.customers.store');
Route::get('/sales/customers/branches', [SalesController::class, 'branchesIndex'])->name('sales.customers.branches');
Route::post('/sales/customers/branches', [SalesController::class, 'branchesStore'])->name('sales.customers.branches.store');
Route::get('/sales/customers/branches/{branch}/edit', [SalesController::class, 'branchesEdit'])->name('sales.customers.branches.edit');
Route::put('/sales/customers/branches/{branch}', [SalesController::class, 'branchesUpdate'])->name('sales.customers.branches.update');
Route::delete('/sales/customers/branches/{branch}', [SalesController::class, 'branchesDestroy'])->name('sales.customers.branches.destroy');
Route::get('/sales/customers/{customer}', [SalesController::class, 'customersShow'])->name('sales.customers.show');
Route::get('/sales/customers/{customer}/edit', [SalesController::class, 'customersEdit'])->name('sales.customers.edit');
Route::put('/sales/customers/{customer}', [SalesController::class, 'customersUpdate'])->name('sales.customers.update');
Route::delete('/sales/customers/{customer}', [SalesController::class, 'customersDestroy'])->name('sales.customers.destroy');

// Sales Module - Setup
Route::match(['GET', 'POST'], '/sales/setup/groups', [SalesSetupController::class, 'groupsIndex'])->name('sales.setup.groups');
Route::get('/sales/setup/recurrent-invoices', [SalesSetupController::class, 'recurrentInvoicesIndex'])->name('sales.setup.recurrent-invoices');
Route::match(['GET', 'POST'], '/sales/setup/types', [SalesSetupController::class, 'typesIndex'])->name('sales.setup.types');
Route::match(['GET', 'POST'], '/sales/setup/persons', [SalesSetupController::class, 'personsIndex'])->name('sales.setup.persons');
Route::match(['GET', 'POST'], '/sales/setup/areas', [SalesSetupController::class, 'areasIndex'])->name('sales.setup.areas');
Route::match(['GET', 'POST'], '/sales/setup/credit-status', [SalesSetupController::class, 'creditStatusIndex'])->name('sales.setup.credit-status');

// Legacy routes
Route::get('/sales/orders', function () {
    return view('sales.orders.index');
})->name('sales.orders.index');

// Purchases Module - Transactions
Route::get('/purchases', function () {
    return redirect()->route('purchases.suppliers.index');
})->name('purchases.index');
Route::get('/purchases/orders', function () {
    return view('purchases.orders.index');
})->name('purchases.orders.index');
Route::match(['GET', 'POST'], '/purchases/orders/create', [PurchasesController::class, 'createOrder'])->name('purchases.orders.create');
Route::match(['GET', 'POST'], '/purchases/orders/outstanding', [PurchasesController::class, 'outstandingOrders'])->name('purchases.orders.outstanding');
Route::match(['GET', 'POST'], '/purchases/orders/receive', [PurchasesController::class, 'receiveOrder'])->name('purchases.orders.receive');
Route::get('/purchases/orders/print/{id}', [PurchasesController::class, 'printOrder'])->name('purchases.orders.print');
Route::match(['GET', 'POST'], '/purchases/grn/direct', [PurchasesController::class, 'directGrn'])->name('purchases.grn.direct');
Route::match(['GET', 'POST'], '/purchases/invoice/direct', [PurchasesController::class, 'directInvoice'])->name('purchases.invoice.direct');
Route::match(['GET', 'POST'], '/purchases/payments', [PurchasesController::class, 'paymentEntry'])->name('purchases.payments.index');
Route::match(['GET', 'POST'], '/purchases/invoices', [PurchasesController::class, 'supplierInvoice'])->name('purchases.invoices.index');
Route::match(['GET', 'POST'], '/purchases/credit-notes', [PurchasesController::class, 'supplierCreditNote'])->name('purchases.credit-notes.index');
Route::match(['GET', 'POST'], '/purchases/allocation', [PurchasesController::class, 'allocationIndex'])->name('purchases.allocation.index');
Route::get('/purchases/allocation/{id}', [PurchasesController::class, 'allocationView'])->name('purchases.allocation.view');

// Purchases Module - Inquiries and Reports
Route::match(['GET', 'POST'], '/purchases/inquiries/orders', [PurchasesController::class, 'ordersInquiry'])->name('purchases.inquiries.orders');
Route::match(['GET', 'POST'], '/purchases/inquiries/transactions', [PurchasesController::class, 'supplierInquiry'])->name('purchases.inquiries.transactions');
Route::match(['GET', 'POST'], '/purchases/inquiries/allocation', [PurchasesController::class, 'allocationInquiry'])->name('purchases.inquiries.allocation');
Route::get('/purchases/reports/supplier', function () {
    return view('purchases.reports.supplier');
})->name('purchases.reports.supplier');
Route::match(['GET', 'POST'], '/purchases/suppliers', [PurchasesController::class, 'suppliersIndex'])->name('purchases.suppliers.index');

// Inventory Module - Transactions
Route::match(['GET', 'POST'], '/inventory/transfers', function () {
    $message = '';
    $error = '';

    $locations = \Schema::hasTable('locations') ? \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get() : [];
    $costableItems = \DB::table('items')
        ->where('is_active', true)
        ->whereIn('mb_flag', ['B', 'M'])
        ->orderBy('code')
        ->get(['code', 'name', 'unit_of_measure', 'cost_price']);

    // Initialize session cart
    if (!session()->has('transfer_items')) {
        session(['transfer_items' => [
            'from_loc' => '',
            'to_loc' => '',
            'tran_date' => date('Y-m-d'),
            'reference' => '',
            'memo_' => '',
            'line_items' => [],
        ]]);
    }

    if (request()->isMethod('POST')) {
        $cart = session('transfer_items', []);
        $cart['from_loc'] = request('FromStockLocation', $cart['from_loc']);
        $cart['to_loc'] = request('ToStockLocation', $cart['to_loc']);
        $cart['tran_date'] = request('AdjDate', $cart['tran_date']);
        $cart['reference'] = request('ref', $cart['reference']);
        $cart['memo_'] = request('memo_', $cart['memo_']);

        // Delete item
        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['line_items'][$delete_id])) {
            unset($cart['line_items'][$delete_id]);
            $cart['line_items'] = array_values($cart['line_items']);
            session(['transfer_items' => $cart, 'transfer_edit_index' => null, 'transfer_stock_id' => null]);
        }

        // Add item
        if (request('AddItem')) {
            $stock_id = request('stock_id', '');
            $qty = (float) request('qty', 0);

            if (empty($stock_id)) {
                $error = 'You must select an item.';
            } elseif ($qty <= 0) {
                $error = 'The quantity entered must be a positive number.';
            } else {
                // Check for duplicate
                $found = false;
                foreach ($cart['line_items'] as $li) {
                    if ($li['stock_id'] == $stock_id) {
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    $error = 'For Part :' . $stock_id . ' This item is already on this document. You can change the quantity on the existing line if necessary.';
                } else {
                    $item = \DB::table('items')->where('code', $stock_id)->first();
                    $cart['line_items'][] = [
                        'stock_id' => $stock_id,
                        'item_description' => $item->name ?? '',
                        'quantity' => $qty,
                        'units' => $item->unit_of_measure ?? 'each',
                        'standard_cost' => (float) ($item->cost_price ?? 0),
                    ];
                    session(['transfer_items' => $cart, 'transfer_edit_index' => null, 'transfer_stock_id' => null]);
                }
            }
        }

        // Update item
        if (request('UpdateItem')) {
            $edit_idx = session('transfer_edit_index');
            if ($edit_idx !== null && isset($cart['line_items'][$edit_idx])) {
                $qty = (float) request('qty', 0);
                if ($qty <= 0) {
                    $error = 'The quantity entered must be a positive number.';
                } else {
                    $cart['line_items'][$edit_idx]['quantity'] = $qty;
                    session(['transfer_items' => $cart, 'transfer_edit_index' => null, 'transfer_stock_id' => null]);
                }
            }
        }

        if (request('CancelItemChanges')) {
            session(['transfer_edit_index' => null, 'transfer_stock_id' => null]);
        }

        // Edit button
        $edit_id = request('Edit');
        if ($edit_id !== null && isset($cart['line_items'][$edit_id])) {
            session(['transfer_edit_index' => $edit_id]);
        }

        // Process Transfer
        if (request('Process')) {
            if (count($cart['line_items']) == 0) {
                $error = 'You must enter at least one non empty item line.';
            } elseif (empty($cart['from_loc'])) {
                $error = 'Please select the source location.';
            } elseif (empty($cart['to_loc'])) {
                $error = 'Please select the destination location.';
            } elseif ($cart['from_loc'] == $cart['to_loc']) {
                $error = 'The locations to transfer from and to must be different.';
            } elseif (empty($cart['reference'])) {
                $error = 'You must enter a reference.';
            } else {
                // Process the transfer
                $trans_no = \DB::table('stock_moves')->max('trans_no') + 1;

                $itemCount = 0;
                foreach ($cart['line_items'] as $line) {
                    // Debit from source (negative)
                    \DB::table('stock_moves')->insert([
                        'stock_id' => $line['stock_id'],
                        'loc_code' => $cart['from_loc'],
                        'tran_date' => $cart['tran_date'],
                        'qty' => -abs($line['quantity']),
                        'standard_cost' => $line['standard_cost'],
                        'trans_type' => 16,
                        'trans_no' => $trans_no,
                        'memo' => $cart['memo_'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Credit to destination (positive)
                    \DB::table('stock_moves')->insert([
                        'stock_id' => $line['stock_id'],
                        'loc_code' => $cart['to_loc'],
                        'tran_date' => $cart['tran_date'],
                        'qty' => abs($line['quantity']),
                        'standard_cost' => $line['standard_cost'],
                        'trans_type' => 16,
                        'trans_no' => $trans_no,
                        'memo' => $cart['memo_'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $itemCount++;
                }

                // Clear cart
                session(['transfer_items' => null, 'transfer_edit_index' => null, 'transfer_stock_id' => null]);

                $message = 'Inventory transfer has been processed';
                return redirect()->route('inventory.transfers', ['AddedID' => $trans_no]);
            }
        }

        session(['transfer_items' => $cart]);
    }

    $cart = session('transfer_items', ['from_loc' => '', 'to_loc' => '', 'tran_date' => date('Y-m-d'), 'reference' => '', 'memo_' => '', 'line_items' => []]);

    $addedID = request('AddedID');
    $edit_index = session('transfer_edit_index');

    // Calculate low stock items (qty on hand in source location)
    $lowStockItems = [];
    if ($cart['from_loc']) {
        foreach ($cart['line_items'] as $li) {
            $qoh = \DB::table('stock_moves')
                ->where('stock_id', $li['stock_id'])
                ->where('loc_code', $cart['from_loc'])
                ->where('tran_date', '<=', $cart['tran_date'])
                ->sum('qty');
            if ($qoh < $li['quantity']) {
                $lowStockItems[] = $li['stock_id'];
            }
        }
    }

    // Generate default reference
    $defaultRef = '';
    $defaultRefLine = \DB::table('reflines')->where('trans_type', 16)->where('default', true)->first();
    if ($defaultRefLine) {
        $lastRef = \DB::table('stock_moves')
            ->where('trans_type', 16)
            ->orderBy('id', 'desc')
            ->value('trans_no');
        $nextNum = ($lastRef ?: 0) + 1;
        $pattern = $defaultRefLine->pattern ?: '{001}';
        $defaultRef = $defaultRefLine->prefix . preg_replace_callback('/\{(\d+)\}/', function($m) use ($nextNum) {
            return str_pad($nextNum, strlen($m[1]), '0', STR_PAD_LEFT);
        }, $pattern);
    }

    return view('inventory.transfers', compact(
        'message', 'error', 'locations', 'costableItems', 'cart',
        'addedID', 'edit_index', 'lowStockItems', 'defaultRef'
    ));
})->name('inventory.transfers');
Route::match(['GET', 'POST'], '/inventory/adjust', function () {
    $message = '';
    $error = '';

    $locations = \Schema::hasTable('locations') ? \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get() : [];
    $costableItems = \DB::table('items')
        ->where('is_active', true)
        ->whereIn('mb_flag', ['B', 'M'])
        ->orderBy('code')
        ->get(['code', 'name', 'unit_of_measure', 'cost_price', 'material_cost']);

    // Initialize session cart
    if (!session()->has('adj_items')) {
        session(['adj_items' => [
            'location' => '',
            'tran_date' => date('Y-m-d'),
            'reference' => '',
            'memo_' => '',
            'line_items' => [],
        ]]);
    }

    if (request()->isMethod('POST')) {
        $cart = session('adj_items', []);
        $cart['location'] = request('StockLocation', $cart['location']);
        $cart['tran_date'] = request('AdjDate', $cart['tran_date']);
        $cart['reference'] = request('ref', $cart['reference']);
        $cart['memo_'] = request('memo_', $cart['memo_']);

        // Delete item
        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['line_items'][$delete_id])) {
            unset($cart['line_items'][$delete_id]);
            $cart['line_items'] = array_values($cart['line_items']);
            session(['adj_items' => $cart, 'adj_edit_index' => null, 'adj_selected_id' => null]);
        }

        // Add item
        if (request('AddItem')) {
            $stock_id = request('stock_id', '');
            $qty = (float) request('qty', 0);
            $std_cost = (float) request('std_cost', 0);

            if (empty($stock_id)) {
                $error = 'You must select an item.';
            } elseif ($qty == 0) {
                $error = 'The quantity entered is invalid.';
            } elseif ($std_cost < 0) {
                $error = 'The entered standard cost is negative or invalid.';
            } else {
                $found = false;
                foreach ($cart['line_items'] as $li) {
                    if ($li['stock_id'] == $stock_id) {
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    $error = 'For Part :' . $stock_id . ' This item is already on this document. You can change the quantity on the existing line if necessary.';
                } else {
                    $item = \DB::table('items')->where('code', $stock_id)->first();
                    $cart['line_items'][] = [
                        'stock_id' => $stock_id,
                        'item_description' => $item->name ?? '',
                        'quantity' => $qty,
                        'units' => $item->unit_of_measure ?? 'each',
                        'standard_cost' => $std_cost ?: (float) ($item->material_cost ?? 0),
                    ];
                    session(['adj_items' => $cart, 'adj_edit_index' => null, 'adj_selected_id' => null]);
                }
            }
        }

        // Update item
        if (request('UpdateItem')) {
            $edit_idx = session('adj_edit_index');
            if ($edit_idx !== null && isset($cart['line_items'][$edit_idx])) {
                $qty = (float) request('qty', 0);
                $std_cost = (float) request('std_cost', 0);
                if ($qty == 0) {
                    $error = 'The quantity entered is invalid.';
                } elseif ($std_cost < 0) {
                    $error = 'The entered standard cost is negative or invalid.';
                } else {
                    $cart['line_items'][$edit_idx]['quantity'] = $qty;
                    $cart['line_items'][$edit_idx]['standard_cost'] = $std_cost;
                    session(['adj_items' => $cart, 'adj_edit_index' => null, 'adj_selected_id' => null]);
                }
            }
        }

        if (request('CancelItemChanges')) {
            session(['adj_edit_index' => null, 'adj_selected_id' => null]);
        }

        // Edit button
        $edit_id = request('Edit');
        if ($edit_id !== null && isset($cart['line_items'][$edit_id])) {
            session(['adj_edit_index' => $edit_id, 'adj_selected_id' => $edit_id]);
        }

        // Process Adjustment
        if (request('Process')) {
            if (count($cart['line_items']) == 0) {
                $error = 'You must enter at least one non empty item line.';
            } elseif (empty($cart['location'])) {
                $error = 'Please select the location.';
            } elseif (empty($cart['reference'])) {
                $error = 'You must enter a reference.';
            } else {
                $trans_no = \DB::table('stock_moves')->max('trans_no') + 1;

                foreach ($cart['line_items'] as $line) {
                    \DB::table('stock_moves')->insert([
                        'stock_id' => $line['stock_id'],
                        'loc_code' => $cart['location'],
                        'tran_date' => $cart['tran_date'],
                        'qty' => $line['quantity'],
                        'standard_cost' => $line['standard_cost'],
                        'trans_type' => 17,
                        'trans_no' => $trans_no,
                        'memo' => $cart['memo_'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                session(['adj_items' => null, 'adj_edit_index' => null, 'adj_selected_id' => null]);

                $message = 'Items adjustment has been processed';
                return redirect()->route('inventory.adjust', ['AddedID' => $trans_no]);
            }
        }

        session(['adj_items' => $cart]);
    }

    $cart = session('adj_items', ['location' => '', 'tran_date' => date('Y-m-d'), 'reference' => '', 'memo_' => '', 'line_items' => []]);

    $addedID = request('AddedID');
    $edit_index = session('adj_edit_index');

    // Calculate QOH and low stock items
    $qohCache = [];
    $lowStockItems = [];
    if ($cart['location']) {
        foreach ($cart['line_items'] as $li) {
            $qoh = (float) \DB::table('stock_moves')
                ->where('stock_id', $li['stock_id'])
                ->where('loc_code', $cart['location'])
                ->where('tran_date', '<=', $cart['tran_date'])
                ->sum('qty');
            $qohCache[$li['stock_id']] = $qoh;
            if ($qoh < abs($li['quantity']) && $li['quantity'] < 0) {
                $lowStockItems[] = $li['stock_id'];
            }
        }
    }

    // Generate default reference
    $defaultRef = '';
    $defaultRefLine = \DB::table('reflines')->where('trans_type', 17)->where('default', true)->first();
    if ($defaultRefLine) {
        $lastRef = \DB::table('stock_moves')
            ->where('trans_type', 17)
            ->orderBy('id', 'desc')
            ->value('trans_no');
        $nextNum = ($lastRef ?: 0) + 1;
        $pattern = $defaultRefLine->pattern ?: '{001}';
        $defaultRef = $defaultRefLine->prefix . preg_replace_callback('/\{(\d+)\}/', function($m) use ($nextNum) {
            return str_pad($nextNum, strlen($m[1]), '0', STR_PAD_LEFT);
        }, $pattern);
    }

    return view('inventory.adjust', compact(
        'message', 'error', 'locations', 'costableItems', 'cart',
        'addedID', 'edit_index', 'qohCache', 'lowStockItems', 'defaultRef'
    ));
})->name('inventory.adjust');

// Inventory Module - Inquiries and Reports
Route::match(['GET', 'POST'], '/inventory/inquiries/movements', function () {
    $systypes = [
        0 => 'Journal Entry', 1 => 'Bank Payment', 2 => 'Bank Deposit',
        4 => 'Funds Transfer', 10 => 'Sales Invoice', 11 => 'Customer Credit Note',
        12 => 'Customer Payment', 13 => 'Delivery Note', 16 => 'Location Transfer',
        17 => 'Inventory Adjustment', 18 => 'Purchase Order', 20 => 'Supplier Invoice',
        21 => 'Supplier Credit Note', 22 => 'Supplier Payment', 25 => 'Purchase Order Delivery',
        26 => 'Work Order', 28 => 'Work Order Issue', 29 => 'Work Order Production',
        30 => 'Sales Order', 32 => 'Sales Quotation', 35 => 'Cost Update', 40 => 'Dimension',
    ];

    $stock_id = request('stock_id', session('movements_stock_id', ''));
    $location = request('StockLocation', session('movements_location', ''));
    $fromDate = request('AfterDate', date('Y-m-d', strtotime('-30 days')));
    $toDate = request('BeforeDate', date('Y-m-d'));
    $showMoves = request('ShowMoves', false);
    if ($showMoves) session(['movements_show' => true]);

    if (request('stock_id')) session(['movements_stock_id' => request('stock_id')]);
    if (request('StockLocation')) session(['movements_location' => request('StockLocation')]);

    $locations = \Schema::hasTable('locations') ? \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get() : [];
    $items = \DB::table('items')->where('is_active', true)->orderBy('code')->get(['code', 'name']);

    $movements = [];
    $beforeQty = 0;
    $afterQty = 0;
    $totalIn = 0;
    $totalOut = 0;

    if ($showMoves && $stock_id) {
        $query = \DB::table('stock_moves')
            ->where('stock_id', $stock_id)
            ->where('tran_date', '>=', $fromDate)
            ->where('tran_date', '<=', $toDate)
            ->orderBy('tran_date')
            ->orderBy('id');

        if ($location) {
            $query->where('loc_code', $location);
        }

        $movements = $query->get();

        $beforeQty = (float) \DB::table('stock_moves')
            ->where('stock_id', $stock_id)
            ->when($location, fn($q) => $q->where('loc_code', $location))
            ->where('tran_date', '<', $fromDate)
            ->sum('qty');

        $afterQty = $beforeQty;
        foreach ($movements as $m) {
            $afterQty += $m->qty;
            if ($m->qty > 0) $totalIn += $m->qty;
            else $totalOut += -$m->qty;
        }
    }

    $displayLocation = !$location;

    return view('inventory.inquiries.movements', compact(
        'systypes', 'stock_id', 'location', 'fromDate', 'toDate',
        'locations', 'items', 'movements', 'beforeQty', 'afterQty',
        'totalIn', 'totalOut', 'displayLocation'
    ));
})->name('inventory.inquiries.movements');
Route::match(['GET', 'POST'], '/inventory/inquiries/status', function () {
    $stock_id = request('stock_id', session('status_stock_id', ''));
    if (request('stock_id')) session(['status_stock_id' => request('stock_id')]);

    $items = \DB::table('items')
        ->where('is_active', true)
        ->whereIn('mb_flag', ['B', 'M'])
        ->orderBy('code')
        ->get(['code', 'name', 'mb_flag']);

    $locDetails = [];
    $isService = false;
    $mbFlag = '';

    if ($stock_id) {
        $item = \DB::table('items')->where('code', $stock_id)->first();
        $mbFlag = $item->mb_flag ?? '';
        $isService = $mbFlag == 'D';

        $locations = \DB::table('locations')
            ->where('inactive', false)
            ->orderBy('loc_code')
            ->get(['loc_code', 'location_name']);

        foreach ($locations as $loc) {
            $qoh = (float) \DB::table('stock_moves')
                ->where('stock_id', $stock_id)
                ->where('loc_code', $loc->loc_code)
                ->sum('qty');

            $reorder = (float) \DB::table('loc_stock')
                ->where('stock_id', $stock_id)
                ->where('loc_code', $loc->loc_code)
                ->value('reorder_level') ?? 0;

            $demand = 0;
            $onOrder = 0;
            $available = $qoh - $demand;

            $locDetails[] = (object) [
                'location_name' => $loc->location_name,
                'loc_code' => $loc->loc_code,
                'qoh' => $qoh,
                'reorder_level' => $reorder,
                'demand' => $demand,
                'available' => $available,
                'on_order' => $onOrder,
            ];
        }
    }

    return view('inventory.inquiries.status', compact(
        'stock_id', 'items', 'locDetails', 'isService', 'mbFlag'
    ));
})->name('inventory.inquiries.status');
Route::get('/inventory/reports', function () {
    return view('inventory.reports.index');
})->name('inventory.reports.index');

// Inventory Module - Maintenance
Route::match(['GET', 'POST'], '/inventory/items', function () {
    $stock_id = request('stock_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $show_inactive = session('items_show_inactive', false);
    if (request()->has('show_inactive')) {
        if (request('show_inactive')) {
            session(['items_show_inactive' => true]);
            $show_inactive = true;
        } else {
            session()->forget('items_show_inactive');
            $show_inactive = false;
        }
    }

    $categories = \DB::table('stock_category')->where('inactive', false)->orderBy('description')->get();
    $taxTypes = \DB::table('tax_types')->orderBy('name')->get(['id', 'name']);
    $units = \DB::table('item_units')->orderBy('name')->get(['id', 'name']);
    $glAccounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    // Load item data for editing
    $item = null;
    if ($stock_id) {
        $item = \DB::table('items')->where('code', $stock_id)->first();
    }

    // When a category is selected for new item, load defaults from category on first load
    // This is handled via request parameters from onchange submit on category_id/mb_flag
    if ($stock_id == '' && request('category_id')) {
        $catRec = \DB::table('stock_category')->where('id', (int) request('category_id'))->first();
    }

    $stockTypes = ['B' => 'Purchased', 'M' => 'Manufactured', 'D' => 'Service', 'S' => 'Sales Kit', 'F' => 'Fixed Asset'];

    $del_image = request('del_image') ? true : false;

    // Handle image upload
    $upload_error = '';
    if (request()->hasFile('pic') && request()->file('pic')->isValid()) {
        $stockId = request('NewStockID', '');
        if ($stockId) {
            $file = request()->file('pic');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $upload_error = 'Only graphics files can be uploaded';
            } else {
                $file->storeAs('public/items', $stockId . '.' . $ext);
            }
        }
    }

    // Handle addupdate
    if (request('addupdate')) {
        $input_error = 0;
        $NewStockID = trim(request('NewStockID', ''));
        $description = trim(request('description', ''));
        $long_description = request('long_description', '');

        if (strlen($description) == 0) {
            $input_error = 1;
            $error = 'The item name must be entered.';
        } elseif (strlen($NewStockID) == 0) {
            $input_error = 1;
            $error = 'The item code cannot be empty';
        } elseif (preg_match('/[ &\'\+\t\"]/', $NewStockID)) {
            $input_error = 1;
            $error = 'The item code cannot contain any of the following characters - & + OR a space OR quotes';
        }

        if (!$input_error) {
            $existingItem = \DB::table('items')->where('code', $NewStockID)->first();
            $data = [
                'name' => $description,
                'long_description' => $long_description,
                'description' => $long_description,
                'category_id' => (int) request('category_id', 0),
                'tax_type_id' => (int) request('tax_type_id', 0),
                'unit_of_measure' => request('units', 'each'),
                'mb_flag' => request('mb_flag', 'B'),
                'sales_account' => request('sales_account', ''),
                'inventory_account' => request('inventory_account', ''),
                'cogs_account' => request('cogs_account', ''),
                'adjustment_account' => request('adjustment_account', ''),
                'wip_account' => request('wip_account', ''),
                'dimension_id' => (int) request('dimension_id', 0),
                'dimension2_id' => (int) request('dimension2_id', 0),
                'no_sale' => request('no_sale') ? true : false,
                'no_purchase' => request('no_purchase') ? true : false,
                'editable' => request('editable') ? true : false,
                'is_active' => !request('inactive'),
            ];

            if ($existingItem) {
                \DB::table('items')->where('code', $NewStockID)->update($data);
                $message = 'Item has been updated.';
            } else {
                $data['company_id'] = session('company_id', 1);
                $data['code'] = $NewStockID;
                $data['is_stock_item'] = in_array($data['mb_flag'], ['B', 'M']);
                $data['is_service'] = $data['mb_flag'] == 'D';
                \DB::table('items')->insert($data);
                $message = 'A new item has been added.';
            }
            $stock_id = $NewStockID;
        }
    }

    // Clone
    if (request('clone')) {
        // stays on same page editing item with original data for cloning
    }

    // Delete
    if (request('delete')) {
        $NewStockID = request('NewStockID', '');
        $used = (\Schema::hasTable('stock_moves') && \DB::table('stock_moves')->where('stock_id', $NewStockID)->exists())
             || \DB::table('bom')->where('parent', $NewStockID)->exists()
             || \DB::table('bom')->where('component', $NewStockID)->exists();
        if ($used) {
            $error = 'This item is in use and cannot be deleted.';
        } else {
            \DB::table('items')->where('code', $NewStockID)->delete();
            $message = 'Selected item has been deleted.';
            $stock_id = '';
        }
    }

    // Cancel
    if (request('cancel')) {
        $stock_id = '';
    }

    // Load item data for editing
    $item = null;
    $catRec = null;
    if ($stock_id) {
        $item = \DB::table('items')->where('code', $stock_id)->first();
    }

    // Auto-fill category defaults for new items
    $autoFill = [];
    if ($stock_id == '' && request('category_id') && !request('addupdate')) {
        $catRec = \DB::table('stock_category')->where('id', (int) request('category_id'))->first();
        if ($catRec) {
            $autoFill = [
                'tax_type_id' => $catRec->dflt_tax_type,
                'units' => $catRec->dflt_units,
                'mb_flag' => $catRec->dflt_mb_flag,
                'inventory_account' => $catRec->dflt_inventory_act,
                'cogs_account' => $catRec->dflt_cogs_act,
                'sales_account' => $catRec->dflt_sales_act,
                'adjustment_account' => $catRec->dflt_adjustment_act,
                'wip_account' => $catRec->dflt_wip_act,
                'dimension_id' => $catRec->dflt_dim1,
                'dimension2_id' => $catRec->dflt_dim2,
                'no_sale' => $catRec->dflt_no_sale,
                'no_purchase' => $catRec->dflt_no_purchase,
            ];
        }
    }

    $items = \DB::table('items')
        ->when(!$show_inactive, fn($q) => $q->where('is_active', true))
        ->orderBy('code')
        ->get(['code', 'name']);

    return view('inventory.items.index', compact(
        'stock_id', 'item', 'items', 'categories', 'taxTypes', 'units',
        'glAccounts', 'use_dimension', 'dimensions', 'stockTypes',
        'message', 'error', 'show_inactive', 'autoFill', 'catRec'
    ));
})->name('inventory.items.index');
Route::match(['GET', 'POST'], '/inventory/items/foreign-codes', function () {
    $selected_id = request('selected_id', '');
    if ($selected_id === '') $selected_id = -1;
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $stock_id = request('stock_id', session('foreign_codes_stock', ''));
    if ($stock_id) session(['foreign_codes_stock' => $stock_id]);

    // Load defaults from selected item
    $dflt = null;
    if ($stock_id) {
        $dflt = \DB::table('items')->where('code', $stock_id)->first();
    }
    $dflt_desc = $dflt ? $dflt->name : '';
    $dflt_cat = $dflt ? ($dflt->category_id ?? 0) : 0;
    $units = $dflt ? $dflt->unit_of_measure : '';
    $dec = 0;

    if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
        $input_error = 0;
        $item_code = request('item_code', '');

        if (!$stock_id) {
            $input_error = 1;
            $error = 'There is no item selected.';
        } elseif (!is_numeric(request('quantity')) || (float) request('quantity') <= 0) {
            $input_error = 1;
            $error = 'The quantity entered was not a positive number.';
        } elseif (strlen(request('description', '')) == 0) {
            $input_error = 1;
            $error = 'Item code description cannot be empty.';
        } elseif ($selected_id == -1) {
            $exists = \DB::table('item_codes')
                ->where('item_code', $item_code)
                ->exists();
            if ($exists) {
                $input_error = 1;
                $error = 'This item code is already assigned to a foreign item code.';
            }
        }

        if (!$input_error) {
            $data = [
                'item_code' => $item_code,
                'stock_id' => $stock_id,
                'description' => request('description', ''),
                'category_id' => (int) request('category_id', 0),
                'quantity' => (float) request('quantity', 1),
                'is_foreign' => true,
            ];

            if ($Mode == 'ADD_ITEM') {
                \DB::table('item_codes')->insert($data);
                $message = 'New item code has been added.';
            } else {
                \DB::table('item_codes')->where('id', $selected_id)->update($data);
                $message = 'Item code has been updated.';
            }
            $selected_id = -1;
            $Mode = 'RESET';
        }
    }

    if ($Mode == 'Delete') {
        if ($selected_id > 0) {
            \DB::table('item_codes')->where('id', $selected_id)->delete();
            $message = 'Item code has been successfully deleted.';
        }
        $selected_id = -1;
        $Mode = 'RESET';
    }

    if ($Mode == 'RESET') {
        $selected_id = -1;
    }

    $items = \DB::table('items')
        ->where('is_active', true)
        ->orderBy('code')
        ->get(['code', 'name']);

    $codes = \DB::table('item_codes')
        ->where('stock_id', $stock_id)
        ->where('is_foreign', true)
        ->orderBy('item_code')
        ->get();

    $categories = \DB::table('stock_category')
        ->where('inactive', false)
        ->orderBy('description')
        ->get(['id', 'description']);

    // Edit data
    $edit_code = null;
    if ($selected_id > 0 && $Mode == 'Edit') {
        $edit_code = \DB::table('item_codes')->where('id', $selected_id)->first();
    }

    $edit_item_code = $edit_code ? $edit_code->item_code : '';
    $edit_quantity = $edit_code ? $edit_code->quantity : 1;
    $edit_description = $edit_code ? $edit_code->description : $dflt_desc;
    $edit_category_id = $edit_code ? $edit_code->category_id : $dflt_cat;

    return view('inventory.items.foreign-codes', compact(
        'stock_id', 'items', 'codes', 'categories', 'message', 'error',
        'selected_id', 'Mode', 'edit_code', 'edit_item_code', 'edit_quantity',
        'edit_description', 'edit_category_id', 'units', 'dec', 'dflt_desc', 'dflt_cat'
    ));
})->name('inventory.items.foreign-codes');
Route::match(['GET', 'POST'], '/inventory/items/sales-kits', function () {
    $selected_id = request('selected_id', '');
    if ($selected_id === '') $selected_id = -1;
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $item_code = request('item_code', '');
    $selected_kit = $item_code;

    // Kits list: distinct item_codes where is_foreign=0 and item_code != stock_id
    $kits = \DB::table('item_codes')
        ->where('is_foreign', false)
        ->whereColumn('item_code', '!=', 'stock_id')
        ->select('item_code')
        ->distinct()
        ->orderBy('item_code')
        ->get()
        ->pluck('item_code')
        ->toArray();
    // Also include stock items with mb_flag='S'
    $kitItems = \DB::table('items')
        ->where('mb_flag', 'S')
        ->where('is_active', true)
        ->orderBy('code')
        ->pluck('code')
        ->toArray();
    $allKits = array_unique(array_merge($kits, $kitItems));
    sort($allKits);

    // Get kit properties (first row's description/category)
    $props = null;
    if ($item_code) {
        $firstRow = \DB::table('item_codes')
            ->where('item_code', $item_code)
            ->where('is_foreign', false)
            ->first();
        // Also check stock_master for sales kit type
        $stockItem = \DB::table('items')->where('code', $item_code)->first();
        $props = (object) [
            'description' => $firstRow->description ?? ($stockItem->name ?? ''),
            'category_id' => $firstRow->category_id ?? ($stockItem->category_id ?? 0),
        ];
    }

    // Handle update_name (kit properties update)
    if (request('update_name')) {
        $newDesc = request('description', '');
        $newCat = (int) request('category', 0);
        if ($item_code) {
            \DB::table('item_codes')
                ->where('item_code', $item_code)
                ->where('is_foreign', false)
                ->update(['description' => $newDesc, 'category_id' => $newCat]);
            $message = 'Kit common properties has been updated';
        }
    }

    // Handle ADD_ITEM / UPDATE_ITEM
    if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
        $input_error = 0;
        $component = request('component', '');
        $quantity = request('quantity', 0);
        $description = request('description', '');
        $category = (int) request('category', 0);
        $kit_code = request('kit_code', '');

        if (!is_numeric($quantity) || (float) $quantity <= 0) {
            $input_error = 1;
            $error = 'The quantity entered must be numeric and greater than zero.';
        } elseif (strlen($description) == 0) {
            $input_error = 1;
            $error = 'Item code description cannot be empty.';
        } elseif ($selected_id == -1 && $selected_kit == '') {
            // New kit/alias
            if (strlen($kit_code) == 0) {
                $input_error = 1;
                $error = 'Kit/alias code cannot be empty.';
            } else {
                $exists = \DB::table('item_codes')
                    ->where('item_code', $kit_code)
                    ->exists();
                if ($exists) {
                    $input_error = 1;
                    $error = 'This item code is already assigned to stock item or sale kit.';
                }
            }
        }

        // Check recursion: component contains kit?
        if (!$input_error && $selected_kit) {
            $childKits = \DB::table('item_codes')
                ->where('item_code', $component)
                ->where('is_foreign', false)
                ->whereColumn('item_code', '!=', 'stock_id')
                ->pluck('stock_id');
            if ($childKits->contains($selected_kit)) {
                $input_error = 1;
                $error = 'The selected component contains directly or on any lower level the kit under edition. Recursive kits are not allowed.';
            }
        }

        // Check duplicate component in kit
        if (!$input_error && $selected_kit) {
            $existing = \DB::table('item_codes')
                ->where('item_code', $selected_kit)
                ->where('stock_id', $component)
                ->where('is_foreign', false)
                ->when($selected_id > 0, fn($q) => $q->where('id', '!=', $selected_id))
                ->exists();
            if ($existing) {
                $input_error = 1;
                $error = 'The selected component is already in this kit. You can modify its quantity but it cannot appear more than once in the same kit.';
            }
        }

        if (!$input_error) {
            if ($selected_id == -1) {
                // New component
                if ($selected_kit == '') {
                    $selected_kit = $kit_code;
                    $msg = 'New alias code has been created.';
                } else {
                    $msg = 'New component has been added to selected kit.';
                }

                \DB::table('item_codes')->insert([
                    'item_code' => $selected_kit,
                    'stock_id' => $component,
                    'description' => $description,
                    'category_id' => $category,
                    'quantity' => (float) $quantity,
                    'is_foreign' => false,
                ]);
                $message = $msg;
            } else {
                // Update component
                \DB::table('item_codes')->where('id', $selected_id)->update([
                    'stock_id' => $component,
                    'quantity' => (float) $quantity,
                ]);
                $message = 'Component of selected kit has been updated.';
            }
            $Mode = 'RESET';
            $selected_id = -1;
        }
    }

    // Handle Delete
    if ($Mode == 'Delete') {
        if ($selected_id > 0) {
            // Check if this is the last component and kit is used elsewhere
            $componentRow = \DB::table('item_codes')->where('id', $selected_id)->first();
            if ($componentRow) {
                $kitCode = $componentRow->item_code;
                $count = \DB::table('item_codes')
                    ->where('item_code', $kitCode)
                    ->where('is_foreign', false)
                    ->count();
                // Check if used in other kits
                $usedIn = \DB::table('item_codes')
                    ->where('stock_id', $kitCode)
                    ->where('is_foreign', false)
                    ->whereColumn('item_code', '!=', 'stock_id')
                    ->count();

                if ($count <= 1 && $usedIn > 0) {
                    $usedNames = \DB::table('item_codes')
                        ->where('stock_id', $kitCode)
                        ->where('is_foreign', false)
                        ->whereColumn('item_code', '!=', 'stock_id')
                        ->pluck('item_code');
                    $error = 'This item cannot be deleted because it is the last item in the kit used by following kits: ' . $usedNames->implode(', ');
                } else {
                    \DB::table('item_codes')->where('id', $selected_id)->delete();
                    $message = 'The component item has been deleted from this kit';
                }
            }
        }
        $Mode = 'RESET';
        $selected_id = -1;
    }

    if ($Mode == 'RESET') {
        $selected_id = -1;
    }

    // Get kit components for display
    $kitComponents = collect();
    if ($selected_kit) {
        $kitComponents = \DB::table('item_codes')
            ->where('item_code', $selected_kit)
            ->where('is_foreign', false)
            ->orderBy('id')
            ->get();
    }

    $categories = \DB::table('stock_category')
        ->where('inactive', false)
        ->orderBy('description')
        ->get(['id', 'description']);

    // Items list for component selector (exclude sales kits)
    $componentItems = \DB::table('items')
        ->where('is_active', true)
        ->where(function ($q) {
            $q->where('mb_flag', '!=', 'S')
              ->orWhereNull('mb_flag');
        })
        ->orderBy('code')
        ->get(['code', 'name']);

    // Edit data
    $editComponent = null;
    $editComponentCode = '';
    $editQuantity = 1;
    $dec = 0;
    $units = 'kits';

    if ($selected_id > 0 && $Mode == 'Edit') {
        $editComponent = \DB::table('item_codes')->where('id', $selected_id)->first();
        if ($editComponent) {
            $editComponentCode = $editComponent->stock_id;
            $editQuantity = $editComponent->quantity;
            $compItem = \DB::table('items')->where('code', $editComponent->stock_id)->first();
            $dec = 0;
            $units = $compItem ? $compItem->unit_of_measure : 'kits';
        }
    }

    return view('inventory.items.sales-kits', compact(
        'allKits', 'item_code', 'props', 'kitComponents', 'categories',
        'componentItems', 'message', 'error', 'selected_id', 'Mode',
        'editComponent', 'editComponentCode', 'editQuantity', 'dec', 'units',
        'selected_kit', 'item_code'
    ));
})->name('inventory.items.sales-kits');
Route::match(['GET', 'POST'], '/inventory/items/categories', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $stockTypes = ['B' => 'Purchased', 'M' => 'Manufactured', 'D' => 'Service', 'S' => 'Sales Kit', 'F' => 'Fixed Asset'];
    $taxTypes = \DB::table('tax_types')->where('inactive', false)->orderBy('name')->get(['id', 'name']);
    $units = \DB::table('item_units')->where('inactive', false)->orderBy('name')->get(['id', 'name']);
    $glAccounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    // Default GL accounts from settings
    $defInvSalesAct = \DB::table('settings')->where('key', 'default_inv_sales_act')->value('value') ?? '';
    $defInvAct = \DB::table('settings')->where('key', 'default_inventory_act')->value('value') ?? '';
    $defCogsAct = \DB::table('settings')->where('key', 'default_cogs_act')->value('value') ?? '';
    $defAdjAct = \DB::table('settings')->where('key', 'default_adj_act')->value('value') ?? '';
    $defWipAct = \DB::table('settings')->where('key', 'default_wip_act')->value('value') ?? '';

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $description = request('description', '');
        if (strlen($description) == 0) {
            $error = 'The item category description cannot be empty.';
        } else {
            $data = [
                'description' => $description,
                'dflt_tax_type' => request('tax_type_id'),
                'dflt_units' => request('units', 'each'),
                'dflt_mb_flag' => request('mb_flag', 'B'),
                'dflt_sales_act' => request('sales_account', ''),
                'dflt_inventory_act' => request('inventory_account', ''),
                'dflt_cogs_act' => request('cogs_account', ''),
                'dflt_adjustment_act' => request('adjustment_account', ''),
                'dflt_wip_act' => request('wip_account', ''),
                'dflt_dim1' => (int) request('dim1', 0),
                'dflt_dim2' => (int) request('dim2', 0),
                'dflt_no_sale' => request('no_sale') ? true : false,
                'dflt_no_purchase' => request('no_purchase') ? true : false,
            ];

            if ($selected_id) {
                \DB::table('stock_category')->where('id', $selected_id)->update($data);
                $message = 'Selected item category has been updated';
            } else {
                \DB::table('stock_category')->insert($data);
                $message = 'New item category has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode == 'Delete') {
        if ($selected_id) {
            if (\DB::table('stock_master')->where('category_id', $selected_id)->exists()) {
                $error = 'Cannot delete this item category because items have been created using this item category.';
            } else {
                \DB::table('stock_category')->where('id', $selected_id)->delete();
                $message = 'Selected item category has been deleted';
            }
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $cat = \DB::table('stock_category')->where('id', request('toggle_inactive'))->first();
        if ($cat) {
            \DB::table('stock_category')->where('id', $cat->id)->update(['inactive' => !$cat->inactive]);
        }
        return redirect()->route('inventory.items.categories', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['stock_cat_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('stock_cat_show_inactive');
    }
    $show_inactive = session('stock_cat_show_inactive', false);

    $categories = \DB::table('stock_category')
        ->leftJoin('tax_types', 'stock_category.dflt_tax_type', '=', 'tax_types.id')
        ->select('stock_category.*', 'tax_types.name as tax_name')
        ->when(!$show_inactive, fn($q) => $q->where('stock_category.inactive', false))
        ->orderBy('stock_category.description')
        ->get();

    // Load edit data
    $edit_category = null;
    $description = '';
    $tax_type_id = '';
    $mb_flag = 'B';
    $units_val = 'each';
    $sales_account = $defInvSalesAct;
    $inventory_account = $defInvAct;
    $cogs_account = $defCogsAct;
    $adjustment_account = $defAdjAct;
    $wip_account = $defWipAct;
    $dim1 = 0;
    $dim2 = 0;
    $no_sale = false;
    $no_purchase = false;

    if ($Mode == 'Edit' && $selected_id) {
        $edit_category = \DB::table('stock_category')->where('id', $selected_id)->first();
        if ($edit_category) {
            $description = $edit_category->description;
            $tax_type_id = $edit_category->dflt_tax_type;
            $mb_flag = $edit_category->dflt_mb_flag;
            $units_val = $edit_category->dflt_units;
            $sales_account = $edit_category->dflt_sales_act;
            $inventory_account = $edit_category->dflt_inventory_act;
            $cogs_account = $edit_category->dflt_cogs_act;
            $adjustment_account = $edit_category->dflt_adjustment_act;
            $wip_account = $edit_category->dflt_wip_act;
            $dim1 = $edit_category->dflt_dim1;
            $dim2 = $edit_category->dflt_dim2;
            $no_sale = $edit_category->dflt_no_sale;
            $no_purchase = $edit_category->dflt_no_purchase;
        }
    } elseif (request()->isMethod('post') && !$Mode) {
        // Form submitted via mb_flag onchange — preserve POST values
        $mb_flag = request('mb_flag', 'B');
        $description = request('description', '');
        $tax_type_id = request('tax_type_id', '');
        $units_val = request('units', 'each');
        $sales_account = request('sales_account', $defInvSalesAct);
        $inventory_account = request('inventory_account', $defInvAct);
        $cogs_account = request('cogs_account', $defCogsAct);
        $adjustment_account = request('adjustment_account', $defAdjAct);
        $wip_account = request('wip_account', $defWipAct);
        $dim1 = (int) request('dim1', 0);
        $dim2 = (int) request('dim2', 0);
        $no_sale = request('no_sale') ? true : false;
        $no_purchase = request('no_purchase') ? true : false;
    }

    $fixed_asset = ($mb_flag == 'F');
    $is_service = ($mb_flag == 'D');
    $is_manufactured = ($mb_flag == 'M');

    return view('inventory.items.categories', compact(
        'selected_id', 'Mode', 'message', 'error', 'stockTypes', 'taxTypes', 'units',
        'glAccounts', 'use_dimension', 'dimensions', 'categories', 'show_inactive',
        'description', 'tax_type_id', 'mb_flag', 'units_val',
        'sales_account', 'inventory_account', 'cogs_account',
        'adjustment_account', 'wip_account', 'dim1', 'dim2',
        'no_sale', 'no_purchase', 'fixed_asset', 'is_service', 'is_manufactured'
    ));
})->name('inventory.items.categories');
Route::match(['GET', 'POST'], '/inventory/locations', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $loc_code = strtoupper(request('loc_code', ''));
        $location_name = request('location_name', '');

        if (strlen($loc_code) == 0 || strlen($loc_code) > 5) {
            $error = 'The location code must be five characters or less long.';
        } elseif (strlen($location_name) == 0) {
            $error = 'The location name must be entered.';
        } else {
            $data = [
                'location_name' => $location_name,
                'delivery_address' => request('delivery_address', ''),
                'phone' => request('phone', ''),
                'phone2' => request('phone2', ''),
                'fax' => request('fax', ''),
                'email' => request('email', ''),
                'contact' => request('contact', ''),
            ];

            if ($selected_id) {
                \DB::table('locations')->where('loc_code', $selected_id)->update($data);
                $message = 'Selected location has been updated';
            } else {
                $data['loc_code'] = $loc_code;
                \DB::table('locations')->insert($data);
                $message = 'New location has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode == 'Delete') {
        if ($selected_id) {
            $blocked = false;
            foreach (['stock_moves', 'workorders', 'cust_branch', 'bom', 'grn_batch', 'purch_orders', 'sales_orders', 'sales_pos'] as $tbl) {
                if (\Schema::hasTable($tbl) && \DB::table($tbl)->where('loc_code', $selected_id)->exists()) {
                    $error = 'Cannot delete this location because it is used by some related records in other tables.';
                    $blocked = true;
                    break;
                }
            }
            if (!$blocked) {
                \DB::table('locations')->where('loc_code', $selected_id)->delete();
                $message = 'Selected location has been deleted';
            }
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $loc = \DB::table('locations')->where('loc_code', request('toggle_inactive'))->first();
        if ($loc) {
            \DB::table('locations')->where('loc_code', $loc->loc_code)->update(['inactive' => !$loc->inactive]);
        }
        return redirect()->route('inventory.locations', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['show_inactive_locations' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('show_inactive_locations');
    }
    $show_inactive = session('show_inactive_locations', false);

    $locations = \DB::table('locations')
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('loc_code')
        ->get();

    $edit_loc = null;
    $loc_code = '';
    $location_name = '';
    $delivery_address = '';
    $phone = '';
    $phone2 = '';
    $fax = '';
    $email = '';
    $contact = '';

    if ($Mode == 'Edit' && $selected_id) {
        $edit_loc = \DB::table('locations')->where('loc_code', $selected_id)->first();
        if ($edit_loc) {
            $loc_code = $edit_loc->loc_code;
            $location_name = $edit_loc->location_name;
            $delivery_address = $edit_loc->delivery_address;
            $phone = $edit_loc->phone;
            $phone2 = $edit_loc->phone2;
            $fax = $edit_loc->fax;
            $email = $edit_loc->email;
            $contact = $edit_loc->contact;
        }
    }

    return view('inventory.locations', compact(
        'selected_id', 'Mode', 'message', 'error', 'locations', 'show_inactive',
        'loc_code', 'location_name', 'delivery_address', 'phone', 'phone2',
        'fax', 'email', 'contact', 'edit_loc'
    ));
})->name('inventory.locations');
Route::match(['GET', 'POST'], '/inventory/units-of-measure', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $abbr = request('abbr', '');
        $description = request('description', '');
        $decimals = (int) request('decimals', 0);

        if (strlen($abbr) == 0) {
            $error = 'The unit of measure code cannot be empty.';
        } elseif (strlen($abbr) > 20) {
            $error = 'The unit of measure code is too long.';
        } elseif (strlen($description) == 0) {
            $error = 'The unit of measure description cannot be empty.';
        } else {
            if ($selected_id) {
                $existing = \DB::table('item_units')->where('id', $selected_id)->first();
                if ($existing) {
                    \DB::table('item_units')->where('id', $selected_id)->update([
                        'name' => $description,
                        'decimals' => $decimals,
                    ]);
                }
                $message = 'Selected unit has been updated';
            } else {
                \DB::table('item_units')->insert([
                    'id' => $abbr,
                    'name' => $description,
                    'decimals' => $decimals,
                ]);
                $message = 'New unit has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode == 'Delete') {
        if ($selected_id) {
            $used = \DB::table('stock_category')->where('dflt_units', $selected_id)->exists()
                  || \Schema::hasTable('stock_master') && \DB::table('stock_master')->where('units', $selected_id)->exists();
            if ($used) {
                $error = 'Cannot delete this unit of measure because items have been created using this unit.';
            } else {
                \DB::table('item_units')->where('id', $selected_id)->delete();
                $message = 'Selected unit has been deleted';
            }
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $u = \DB::table('item_units')->where('id', request('toggle_inactive'))->first();
        if ($u) {
            \DB::table('item_units')->where('id', $u->id)->update(['inactive' => !$u->inactive]);
        }
        return redirect()->route('inventory.units-of-measure', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['units_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('units_show_inactive');
    }
    $show_inactive = session('units_show_inactive', false);

    $units = \DB::table('item_units')
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('id')
        ->get();

    $edit_unit = null;
    $abbr = '';
    $description = '';
    $decimals = 0;
    $unitInUse = false;

    if ($Mode == 'Edit' && $selected_id) {
        $edit_unit = \DB::table('item_units')->where('id', $selected_id)->first();
        if ($edit_unit) {
            $abbr = $edit_unit->id;
            $description = $edit_unit->name;
            $decimals = $edit_unit->decimals;
            $unitInUse = \DB::table('stock_category')->where('dflt_units', $selected_id)->exists()
                      || (\Schema::hasTable('stock_master') && \DB::table('stock_master')->where('units', $selected_id)->exists());
        }
    }

    return view('inventory.units-of-measure', compact(
        'selected_id', 'Mode', 'message', 'error', 'units', 'show_inactive',
        'abbr', 'description', 'decimals', 'edit_unit', 'unitInUse'
    ));
})->name('inventory.units-of-measure');
Route::match(['GET', 'POST'], '/inventory/reorder-levels', function () {
    $message = '';
    $error = '';
    $stock_id = request('stock_id', session('reorder_stock_item', ''));
    $show_inactive = session('reorder_show_inactive', false);

    if (request('show_inactive')) {
        session(['reorder_show_inactive' => true]);
        $show_inactive = true;
    } elseif (request()->has('show_inactive')) {
        session()->forget('reorder_show_inactive');
        $show_inactive = false;
    }

    $locations = \DB::table('locations')
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('location_name')
        ->get();

    if ($stock_id) {
        session(['reorder_stock_item' => $stock_id]);
    } else {
        $first = \DB::table('items')->orderBy('code')->first();
        if ($first) {
            $stock_id = $first->code;
            session(['reorder_stock_item' => $stock_id]);
        }
    }

    $item = $stock_id ? \DB::table('items')->where('code', $stock_id)->first() : null;

    if (!$item) {
        $error = 'Please select an inventory item.';
    }

    if (request('UpdateData') && $stock_id) {
        foreach ($locations as $loc) {
            $val = request($loc->loc_code, '');
            if (is_numeric($val) && $val >= 0) {
                \DB::table('loc_stock')->updateOrInsert(
                    ['stock_id' => $stock_id, 'loc_code' => $loc->loc_code],
                    ['reorder_level' => (float) $val]
                );
            }
        }
        $message = 'Reorder levels has been updated.';
    }

    $loc_data = collect($locations)->map(function ($loc) use ($stock_id) {
        $rec = \DB::table('loc_stock')
            ->where('stock_id', $stock_id)
            ->where('loc_code', $loc->loc_code)
            ->first();
        return (object) [
            'loc' => $loc,
            'reorder_level' => $rec ? (float) $rec->reorder_level : 0,
        ];
    });

    $items = \DB::table('items')
        ->when(!$show_inactive, fn($q) => $q->where('is_active', true))
        ->orderBy('code')
        ->get(['code', 'name', 'description']);

    return view('inventory.reorder-levels', compact(
        'stock_id', 'item', 'loc_data', 'locations', 'items',
        'message', 'error', 'show_inactive'
    ));
})->name('inventory.reorder-levels');
Route::match(['GET', 'POST'], '/inventory/items/import-csv', function () {
    $action = request('action', 'import');
    $message = '';
    $errors = [];

    $company = \App\Models\Company::find(session('company_id', 1));
    $company_prefs = $company ? (array) json_decode($company->settings ?? '{}') : [];

    // Export
    if (request('export')) {
        $etype = (int) request('export_type', 1);
        $sales_type_id = (int) request('sales_type_id', 0);
        $currency = $company_prefs['curr_default'] ?? 'USD';

        $rows = [];
        $headers = [];

        if ($etype == 1) {
            $headers = ['TYPE','ITEM_CODE','STOCK_ID','DESCRIPTION','CATEGORY','UNITS','DUMMY','MB_FLAG','PRICE','DIMENSION'];
            $items = \DB::table('items')
                ->leftJoin('stock_category', 'items.category_id', '=', 'stock_category.id')
                ->leftJoin('dimensions', 'items.dimension_id', '=', 'dimensions.id')
                ->where('is_active', true)
                ->get(['items.code', 'items.name', 'stock_category.description as category', 'items.unit_of_measure', 'items.mb_flag', 'dimensions.name as dimension']);
            foreach ($items as $row) {
                $rows[] = ['ITEM',$row->code,$row->code,$row->name,$row->category ?? '',$row->unit_of_measure ?? 'each','',$row->mb_flag ?? 'B','',$row->dimension ?? ''];
            }
        } elseif ($etype == 2) {
            $headers = ['TYPE','STOCK_ID','DUMMY1','DUMMY2','DUMMY3','DUMMY4','DUMMY5','DUMMY6','CURRENCY','PRICE'];
            $prices = \DB::table('prices')
                ->join('items', 'prices.stock_id', '=', 'items.code')
                ->where('prices.sales_type_id', $sales_type_id)
                ->where('items.is_active', true)
                ->get(['items.code', 'prices.curr_abrev', 'prices.price']);
            foreach ($prices as $row) {
                $rows[] = ['PRICE',$row->code,'','','','','','',$row->curr_abrev,$row->price];
            }
        } elseif ($etype == 3) {
            $headers = ['TYPE','STOCK_ID','DUMMY','SUPPLIER_DESCRIPTION','SUPPLIER','SUPPLIERS_UOM','CONVERSION_FACTOR','DUMMY1','CURRENCY','PRICE'];
            $purch = \DB::table('purch_data')
                ->join('items', 'purch_data.stock_id', '=', 'items.code')
                ->leftJoin('suppliers', 'purch_data.supplier_id', '=', 'suppliers.supplier_id')
                ->get(['items.code','purch_data.supplier_description','suppliers.supp_name','purch_data.suppliers_uom','purch_data.conversion_factor','purch_data.price']);
            foreach ($purch as $row) {
                $rows[] = ['BUY',$row->code,'',$row->supplier_description ?? '',$row->supp_name ?? '','',$row->suppliers_uom ?? 1,'',$currency,$row->price];
            }
        } elseif ($etype == 4) {
            $headers = ['TYPE','ABBR','NAME','DUMMY1','DUMMY2','DUMMY3','DECIMALS','DUMMY4','DUMMY5','DUMMY6'];
            $units = \DB::table('item_units')->where('inactive', false)->get(['id','name','decimals']);
            foreach ($units as $u) {
                $rows[] = ['UOM',$u->id,$u->name,'','','',$u->decimals,'','',''];
            }
        } elseif ($etype == 5) {
            $headers = ['TYPE','ITEM_CODE','STOCK_ID','DESCRIPTION','CATEGORY','DUMMY','QUANTITY','DUMMY1','DUMMY2','DUMMY3'];
            $kits = \DB::table('item_codes')
                ->whereColumn('item_code', '!=', 'stock_id')
                ->where('is_foreign', false)
                ->where('inactive', false)
                ->leftJoin('stock_category', 'item_codes.category_id', '=', 'stock_category.id')
                ->get(['item_codes.item_code','item_codes.stock_id','item_codes.description','stock_category.description as category','item_codes.quantity']);
            foreach ($kits as $row) {
                $rows[] = ['KIT',$row->item_code,$row->stock_id,$row->description,$row->category ?? '','',$row->quantity,'','',''];
            }
        } elseif ($etype == 6) {
            $headers = ['TYPE','PARENT','COMPONENT','LOC_CODE','DUMMY1','DUMMY2','QUANTITY','DUMMY3','DUMMY4','DUMMY5'];
            $bom = \DB::table('bom')
                ->join('items', 'bom.parent', '=', 'items.code')
                ->where('items.is_active', true)
                ->get(['bom.parent','bom.component','bom.loc_code','bom.quantity']);
            foreach ($bom as $row) {
                $rows[] = ['BOM',$row->parent,$row->component,$row->loc_code ?? '','','',$row->quantity,'','',''];
            }
        } elseif ($etype == 7) {
            $headers = ['TYPE','ITEM_CODE','STOCK_ID','DESCRIPTION','CATEGORY','DUMMY','QUANTITY','DUMMY1','DUMMY2','DUMMY3'];
            $foreign = \DB::table('item_codes')
                ->where('is_foreign', true)
                ->where('inactive', false)
                ->leftJoin('stock_category', 'item_codes.category_id', '=', 'stock_category.id')
                ->get(['item_codes.item_code','item_codes.stock_id','item_codes.description','stock_category.description as category','item_codes.quantity']);
            foreach ($foreign as $row) {
                $rows[] = ['FOREIGN',$row->item_code,$row->stock_id,$row->description,$row->category ?? '','',$row->quantity,'','',''];
            }
        }

        if (!empty($rows)) {
            $out = fopen('php://temp', 'r+');
            fputcsv($out, $headers);
            foreach ($rows as $r) fputcsv($out, $r);
            rewind($out);
            $csv_content = stream_get_contents($out);
            fclose($out);

            $names = ['','items.csv','prices.csv','supp_prices.csv','uom.csv','kits.csv','bom.csv','foreign.csv'];
            $fname = $names[$etype] ?? 'export.csv';
            return response($csv_content, 200, [
                'Content-Type' => 'text/x-csv',
                'Content-Disposition' => "attachment; filename=$fname",
            ]);
        } else {
            $message = 'No Results to download.';
        }
    }

    // Import
    if (request('import')) {
        if (request()->hasFile('imp') && request()->file('imp')->isValid()) {
            $sep = request('sep', ',');
            $fp = fopen(request()->file('imp')->getRealPath(), 'r');
            if (!$fp) {
                $errors[] = 'can not open file';
            } else {
                $lines = $i = $j = $k = $b = $u = $p = $pr = 0;
                while (($data = fgetcsv($fp, 4096, $sep)) !== false) {
                    $lines++;
                    if ($lines == 1) continue; // skip header
                    $data = array_pad($data, 10, '');
                    list($type, $code, $id, $description, $category, $units, $qty, $mb_flag, $currency, $price) = $data;
                    $type = strtoupper(trim($type));
                    $mb_flag = strtoupper(trim($mb_flag));
                    if ($mb_flag == 'S') $mb_flag = 'D';

                    if ($type == '' || $type == 'NOTE') continue;

                    if ($type == 'UOM') {
                        $existing = \DB::table('item_units')->where('id', $code)->first();
                        if ($existing) {
                            \DB::table('item_units')->where('id', $code)->update(['decimals' => (int)$qty]);
                        } else {
                            \DB::table('item_units')->insert(['id' => $code, 'name' => $id, 'decimals' => (int)$qty]);
                        }
                        $u++;
                    }
                    if ($type == 'ITEM') {
                        $dim = 0;
                        // handle category
                        $catRow = null;
                        if ($category) {
                            $catRow = \DB::table('stock_category')->where('description', $category)->first();
                            if (!$catRow) {
                                $catId = \DB::table('stock_category')->insertGetId([
                                    'description' => $category,
                                    'dflt_units' => $units ?: 'each',
                                    'dflt_mb_flag' => $mb_flag ?: 'B',
                                    'dflt_tax_type' => (int) request('tax_type_id', 0),
                                    'dflt_sales_act' => request('sales_account', ''),
                                    'dflt_inventory_act' => request('inventory_account', ''),
                                    'dflt_cogs_act' => request('cogs_account', ''),
                                    'dflt_adjustment_act' => request('adjustment_account', ''),
                                    'dflt_wip_act' => request('wip_account', ''),
                                ]);
                                $catRow = (object) ['id' => $catId];
                            }
                        }

                        $existing = \DB::table('items')->where('code', $id)->first();
                        if ($existing) {
                            \DB::table('items')->where('code', $id)->update([
                                'name' => $description,
                                'category_id' => $catRow ? $catRow->id : $existing->category_id,
                                'unit_of_measure' => $units ?: $existing->unit_of_measure,
                                'is_active' => true,
                            ]);
                            $j++;
                        } else {
                            \DB::table('items')->insert([
                                'company_id' => session('company_id', 1),
                                'code' => $id,
                                'name' => $description ?: $id,
                                'category_id' => $catRow ? $catRow->id : 0,
                                'unit_of_measure' => $units ?: 'each',
                                'is_active' => true,
                                'is_stock_item' => in_array($mb_flag, ['M','B']),
                                'is_service' => $mb_flag == 'D',
                            ]);
                            $i++;
                        }

                        // add loc_stock
                        $location = request('location', '');
                        if ($location && in_array($mb_flag, ['M','B'])) {
                            \DB::table('loc_stock')->updateOrInsert(
                                ['stock_id' => $id, 'loc_code' => $location],
                                ['reorder_level' => 0]
                            );
                        }

                        if ($type != 'BOM' && $price != '') {
                            $stmt_id = (int) request('sales_type_id', 0);
                            $curr = $currency ?: ($company_prefs['curr_default'] ?? 'USD');
                            $existingPrice = \DB::table('prices')
                                ->where('stock_id', $code)
                                ->where('sales_type_id', $stmt_id)
                                ->where('curr_abrev', $curr)
                                ->first();
                            if ($existingPrice) {
                                \DB::table('prices')->where('id', $existingPrice->id)->update(['price' => (float)$price]);
                            } else {
                                \DB::table('prices')->insert([
                                    'stock_id' => $code,
                                    'sales_type_id' => $stmt_id,
                                    'curr_abrev' => $curr,
                                    'price' => (float)$price,
                                ]);
                            }
                            $pr++;
                        }
                    }
                    if ($type == 'KIT' || $type == 'FOREIGN') {
                        $foreign = ($type == 'FOREIGN') ? 1 : 0;
                        $catRow = null;
                        if ($category) {
                            $catRow = \DB::table('stock_category')->where('description', $category)->first();
                        }
                        $existingCode = \DB::table('item_codes')
                            ->where('item_code', $code)
                            ->where('stock_id', $id)
                            ->first();
                        if ($existingCode) {
                            \DB::table('item_codes')->where('id', $existingCode->id)->update([
                                'description' => $description,
                                'category_id' => $catRow ? $catRow->id : $existingCode->category_id,
                                'quantity' => (float)$qty,
                                'is_foreign' => $foreign,
                            ]);
                        } else {
                            \DB::table('item_codes')->insert([
                                'item_code' => $code,
                                'stock_id' => $id,
                                'description' => $description,
                                'category_id' => $catRow ? $catRow->id : 0,
                                'quantity' => (float)$qty,
                                'is_foreign' => $foreign,
                            ]);
                        }
                        $k++;
                    }
                    if ($type == 'BOM') {
                        $exists = \DB::table('bom')
                            ->where('parent', $code)
                            ->where('component', $id)
                            ->first();
                        if ($exists) {
                            \DB::table('bom')->where('id', $exists->id)->update([
                                'loc_code' => request('location', ''),
                                'quantity' => (float)$qty,
                            ]);
                        } else {
                            \DB::table('bom')->insert([
                                'parent' => $code,
                                'component' => $id,
                                'loc_code' => request('location', ''),
                                'quantity' => (float)$qty,
                            ]);
                        }
                        $b++;
                    }
                    if ($type == 'PRICE') {
                        $stmt_id = (int) request('sales_type_id', 0);
                        $curr = $currency ?: ($company_prefs['curr_default'] ?? 'USD');
                        if ($price != '') {
                            $existingPrice = \DB::table('prices')
                                ->where('stock_id', $code)
                                ->where('sales_type_id', $stmt_id)
                                ->where('curr_abrev', $curr)
                                ->first();
                            if ($existingPrice) {
                                \DB::table('prices')->where('id', $existingPrice->id)->update(['price' => (float)$price]);
                            } else {
                                \DB::table('prices')->insert([
                                    'stock_id' => $code,
                                    'sales_type_id' => $stmt_id,
                                    'curr_abrev' => $curr,
                                    'price' => (float)$price,
                                ]);
                            }
                            $pr++;
                        }
                    }
                }
                fclose($fp);

                $parts = [];
                if ($i + $j > 0) $parts[] = "$i item posts created, $j item posts updated.";
                if ($k > 0) $parts[] = "$k sales kit components added or updated.";
                if ($b > 0) $parts[] = "$b BOM components added or updated.";
                if ($u > 0) $parts[] = "$u Units of Measure added or updated.";
                if ($p > 0) $parts[] = "$p Purchasing Data items added or updated.";
                if ($pr > 0) $parts[] = "$pr Prices items added or updated.";
                if (!empty($parts)) $message = implode(' ', $parts);
            }
        } else {
            $errors[] = 'No CSV file selected';
        }
    }

    // Sales types for export
    $salesTypes = [];
    if (\Schema::hasTable('sales_types')) {
        $salesTypes = \DB::table('sales_types')->orderBy('id')->get();
    }

    $locations = \Schema::hasTable('locations') ? \DB::table('locations')->orderBy('location_name')->get() : [];
    $taxTypes = \Schema::hasTable('tax_types') ? \DB::table('tax_types')->orderBy('name')->get() : [];
    $accounts = \Schema::hasTable('chart_master') ? \DB::table('chart_master')->orderBy('account_code')->get() : [];

    return view('inventory.items.import-csv', compact(
        'action', 'message', 'errors', 'salesTypes', 'locations', 'taxTypes', 'accounts', 'company_prefs'
    ));
})->name('inventory.items.import-csv');

// Inventory Module - Pricing and Costs
Route::match(['GET', 'POST'], '/inventory/pricing/sales', function () {
    $selected_id = request('selected_id', '');
    if ($selected_id === '') $selected_id = -1;
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $stock_id = request('stock_id', session('sales_pricing_stock', ''));
    if ($stock_id) session(['sales_pricing_stock' => $stock_id]);

    $curr_abrev = request('curr_abrev', session('company_currency', function () {
        return \DB::table('settings')->where('key', 'curr_default')->value('value') ?? 'USD';
    }));
    if (is_callable($curr_abrev)) $curr_abrev = $curr_abrev();

    // Handle CRUD
    if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
        $input_error = 0;
        $sales_type_id = (int) request('sales_type_id', 0);
        $currency = request('curr_abrev', '');
        $price = request('price', 0);

        if (!is_numeric($price) || (float) $price < 0) {
            $input_error = 1;
            $error = 'The price entered must be numeric.';
        } elseif ($Mode == 'ADD_ITEM') {
            $exists = \DB::table('prices')
                ->where('stock_id', $stock_id)
                ->where('sales_type_id', $sales_type_id)
                ->where('curr_abrev', $currency)
                ->exists();
            if ($exists) {
                $input_error = 1;
                $error = 'The sales pricing for this item, sales type and currency has already been added.';
            }
        }

        if (!$input_error) {
            if ($selected_id > 0) {
                \DB::table('prices')->where('id', $selected_id)->update([
                    'sales_type_id' => $sales_type_id,
                    'curr_abrev' => $currency,
                    'price' => (float) $price,
                ]);
                $message = 'This price has been updated.';
            } else {
                \DB::table('prices')->insert([
                    'stock_id' => $stock_id,
                    'sales_type_id' => $sales_type_id,
                    'curr_abrev' => $currency,
                    'price' => (float) $price,
                ]);
                $message = 'The new price has been added.';
            }
            $selected_id = -1;
            $Mode = 'RESET';
        }
    }

    if ($Mode == 'Delete') {
        if ($selected_id > 0) {
            \DB::table('prices')->where('id', $selected_id)->delete();
            $message = 'The selected price has been deleted.';
        }
        $selected_id = -1;
        $Mode = 'RESET';
    }

    if ($Mode == 'RESET') {
        $selected_id = -1;
    }

    // Items list
    $items = \DB::table('items')
        ->where('is_active', true)
        ->orderBy('code')
        ->get(['code', 'name']);

    // Prices for selected item
    $prices = \DB::table('prices')
        ->where('stock_id', $stock_id)
        ->leftJoin('sales_types', 'prices.sales_type_id', '=', 'sales_types.id')
        ->select('prices.*', 'sales_types.type_name as sales_type')
        ->orderBy('prices.curr_abrev')
        ->orderBy('sales_types.type_name')
        ->get();

    // Currencies
    $currencies = \DB::table('currencies')
        ->where('inactive', false)
        ->orderBy('curr_abrev')
        ->get(['curr_abrev', 'currency']);

    // Sales types
    $salesTypes = \DB::table('sales_types')
        ->orderBy('type_name')
        ->get(['id', 'type_name']);

    // Default price calculation
    $defaultPrice = 0;
    if ($stock_id && $curr_abrev) {
        $sales_type_id = (int) request('sales_type_id', 0);
        if ($sales_type_id) {
            $existingPrice = \DB::table('prices')
                ->where('stock_id', $stock_id)
                ->where('sales_type_id', $sales_type_id)
                ->where('curr_abrev', $curr_abrev)
                ->first();
            if (!$existingPrice) {
                // Try to calculate from kit components or standard cost
                $addPct = \DB::table('settings')->where('key', 'add_pct')->value('value');
                if ($addPct !== null && $addPct != -1) {
                    $item = \DB::table('items')->where('code', $stock_id)->first();
                    if ($item) {
                        $defaultPrice = $item->cost_price * (1 + (float)$addPct / 100);
                    }
                }
            } else {
                $defaultPrice = $existingPrice->price;
            }
        }
    }

    // Edit data
    $editPrice = null;
    if ($selected_id > 0 && $Mode == 'Edit') {
        $editPrice = \DB::table('prices')->where('id', $selected_id)->first();
    }

    return view('inventory.pricing.sales', compact(
        'stock_id', 'items', 'prices', 'currencies', 'salesTypes',
        'message', 'error', 'selected_id', 'Mode', 'editPrice',
        'curr_abrev', 'defaultPrice'
    ));
})->name('inventory.pricing.sales');
Route::match(['GET', 'POST'], '/inventory/pricing/purchasing', function () {
    $selected_id = request('selected_id', '');
    if ($selected_id === '') $selected_id = -1;
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $stock_id = request('stock_id', session('purchasing_pricing_stock', ''));
    if ($stock_id) session(['purchasing_pricing_stock' => $stock_id]);

    // CRUD
    if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') {
        $input_error = 0;
        $supplier_id = (int) request('supplier_id', 0);
        $price = request('price', 0);
        $conversion_factor = request('conversion_factor', 1);
        $suppliers_uom = request('suppliers_uom', '');
        $supplier_description = request('supplier_description', '');

        if (!$stock_id) {
            $input_error = 1;
            $error = 'There is no item selected.';
        } elseif (!is_numeric($price) || (float) $price < 0) {
            $input_error = 1;
            $error = 'The price entered was not numeric.';
        } elseif (!is_numeric($conversion_factor) || (float) $conversion_factor <= 0) {
            $input_error = 1;
            $error = 'The conversion factor entered was not numeric. The conversion factor is the number by which the price must be divided by to get the unit price in our unit of measure.';
        } elseif ($Mode == 'ADD_ITEM') {
            $exists = \DB::table('purch_data')
                ->where('supplier_id', $supplier_id)
                ->where('stock_id', $stock_id)
                ->exists();
            if ($exists) {
                $input_error = 1;
                $error = 'The purchasing data for this supplier has already been added.';
            }
        }

        if (!$input_error) {
            $data = [
                'stock_id' => $stock_id,
                'price' => (float) $price,
                'suppliers_uom' => $suppliers_uom,
                'conversion_factor' => (float) $conversion_factor,
                'supplier_description' => $supplier_description,
            ];

            if ($selected_id > 0) {
                $data['supplier_id'] = (int) request('supplier_id_hidden', $supplier_id);
                \DB::table('purch_data')->where('id', $selected_id)->update($data);
                $message = 'Supplier purchasing data has been updated.';
            } else {
                $data['supplier_id'] = $supplier_id;
                \DB::table('purch_data')->insert($data);
                $message = 'This supplier purchasing data has been added.';
            }
            $selected_id = -1;
            $Mode = 'RESET';
        }
    }

    if ($Mode == 'Delete') {
        if ($selected_id > 0) {
            \DB::table('purch_data')->where('id', $selected_id)->delete();
            $message = 'The purchasing data item has been successfully deleted.';
        }
        $selected_id = -1;
        $Mode = 'RESET';
    }

    if ($Mode == 'RESET') {
        $selected_id = -1;
    }

    // Items (purchasable: mb_flag B or M)
    $items = \DB::table('items')
        ->where('is_active', true)
        ->whereIn('mb_flag', ['B', 'M'])
        ->orderBy('code')
        ->get(['code', 'name']);

    // Suppliers
    $suppliers = \DB::table('suppliers')
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name']);

    // Purchasing data for selected item
    $purchData = collect();
    if ($stock_id) {
        $purchData = \DB::table('purch_data')
            ->where('stock_id', $stock_id)
            ->leftJoin('suppliers', 'purch_data.supplier_id', '=', 'suppliers.id')
            ->select('purch_data.*', 'suppliers.name as supp_name')
            ->orderBy('suppliers.name')
            ->get();
    }

    // Company currency
    $currCode = \DB::table('settings')->where('key', 'curr_default')->value('value') ?? 'USD';

    // Edit data
    $editRecord = null;
    $suppName = '';
    if ($selected_id > 0 && $Mode == 'Edit') {
        $editRecord = \DB::table('purch_data')->where('id', $selected_id)->first();
        if ($editRecord) {
            $supp = \DB::table('suppliers')->where('id', $editRecord->supplier_id)->first();
            $suppName = $supp ? $supp->name : '';
        }
    }

    return view('inventory.pricing.purchasing', compact(
        'stock_id', 'items', 'suppliers', 'purchData', 'message', 'error',
        'selected_id', 'Mode', 'editRecord', 'suppName', 'currCode'
    ));
})->name('inventory.pricing.purchasing');
Route::match(['GET', 'POST'], '/inventory/pricing/standard-costs', function () {
    $stock_id = request('stock_id', session('standard_cost_stock', ''));
    if ($stock_id) session(['standard_cost_stock' => $stock_id]);
    $message = '';
    $error = '';

    // Items (costable: B or M mb_flag)
    $items = \DB::table('items')
        ->where('is_active', true)
        ->whereIn('mb_flag', ['B', 'M'])
        ->orderBy('code')
        ->get(['code', 'name']);

    $item = null;
    $material_cost = 0;
    $labour_cost = 0;
    $overhead_cost = 0;
    $is_manufactured = false;

    if ($stock_id) {
        $item = \DB::table('items')->where('code', $stock_id)->first();
        if ($item) {
            $material_cost = (float) ($item->material_cost ?? $item->cost_price ?? 0);
            $labour_cost = (float) ($item->labour_cost ?? 0);
            $overhead_cost = (float) ($item->overhead_cost ?? 0);
            $is_manufactured = $item->mb_flag == 'M';
        }
    }

    // Reference lines (trans_type = 35 for Cost Update)
    $reflines = \DB::table('reflines')
        ->where('trans_type', 35)
        ->where('inactive', false)
        ->orderBy('prefix')
        ->get(['id', 'prefix', 'pattern', 'description']);

    // Handle Update
    if (request('UpdateData')) {
        $input_error = 0;
        $mat_cost = request('material_cost', 0);
        $lab_cost = request('labour_cost', 0);
        $ovh_cost = request('overhead_cost', 0);

        if (!is_numeric($mat_cost) || !is_numeric($lab_cost) || !is_numeric($ovh_cost)) {
            $input_error = 1;
            $error = 'The entered cost is not numeric.';
        } else {
            $mat_cost = (float) $mat_cost;
            $lab_cost = (float) $lab_cost;
            $ovh_cost = (float) $ovh_cost;
            $new_total = $mat_cost + $lab_cost + $ovh_cost;
            $old_total = $material_cost + $labour_cost + $overhead_cost;

            if ($old_total == $new_total) {
                $input_error = 1;
                $error = 'The new cost is the same as the old cost. Cost was not updated.';
            }
        }

        if (!$input_error && $item) {
            \DB::table('items')->where('code', $stock_id)->update([
                'cost_price' => $new_total,
                'material_cost' => $mat_cost,
                'labour_cost' => $lab_cost,
                'overhead_cost' => $ovh_cost,
            ]);
            $message = 'Cost has been updated.';
            $material_cost = $mat_cost;
            $labour_cost = $lab_cost;
            $overhead_cost = $ovh_cost;
        }
    }

    // Fetch item again after potential update
    if ($stock_id) {
        $item = \DB::table('items')->where('code', $stock_id)->first();
        if ($item) {
            $material_cost = (float) ($item->material_cost ?? $item->cost_price ?? 0);
            $labour_cost = (float) ($item->labour_cost ?? 0);
            $overhead_cost = (float) ($item->overhead_cost ?? 0);
            $is_manufactured = $item->mb_flag == 'M';
        }
    }

    return view('inventory.pricing.standard-costs', compact(
        'stock_id', 'items', 'item', 'material_cost', 'labour_cost',
        'overhead_cost', 'is_manufactured', 'reflines', 'message', 'error'
    ));
})->name('inventory.pricing.standard-costs');

// Fixed Assets Module
Route::match(['GET', 'POST'], '/fixed-assets/purchase', [FixedAssetsController::class, 'purchase'])->name('fixed-assets.purchase');
Route::get('/fixed-assets/transfers', function () { return view('fixed-assets.transfers'); })->name('fixed-assets.transfers');
Route::get('/fixed-assets/disposal', function () { return view('fixed-assets.disposal'); })->name('fixed-assets.disposal');
Route::get('/fixed-assets/sale', function () { return view('fixed-assets.sale'); })->name('fixed-assets.sale');
Route::get('/fixed-assets/depreciation', function () { return view('fixed-assets.depreciation'); })->name('fixed-assets.depreciation');
Route::get('/fixed-assets/inquiries/movements', function () { return view('fixed-assets.inquiries.movements'); })->name('fixed-assets.inquiries.movements');
Route::get('/fixed-assets/inquiries', function () { return view('fixed-assets.inquiries.index'); })->name('fixed-assets.inquiries.index');
Route::get('/fixed-assets/reports', function () { return view('fixed-assets.reports.index'); })->name('fixed-assets.reports.index');
Route::match(['GET', 'POST'], '/fixed-assets', [FixedAssetsController::class, 'index'])->name('fixed-assets.index');
Route::match(['GET', 'POST'], '/fixed-assets/locations', [FixedAssetsController::class, 'locations'])->name('fixed-assets.locations');
Route::match(['GET', 'POST'], '/fixed-assets/categories', [FixedAssetsController::class, 'categories'])->name('fixed-assets.categories');
Route::match(['GET', 'POST'], '/fixed-assets/classes', [FixedAssetsController::class, 'classes'])->name('fixed-assets.classes');

// Dimensions Module
Route::match(['GET', 'POST'], '/dimensions/entry', function () {
    $selected_id = request('selected_id', request('trans_no', ''));
    $Mode = request('Mode', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';
    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $dim_tags = \DB::table('tags')->where('type', 2)->where('inactive', false)->orderBy('name')->get(['id', 'name']);

    // Handle success notifications
    if (request('AddedID')) { $message = 'The dimension has been entered.'; }
    if (request('UpdatedID')) { $message = 'The dimension has been updated.'; }
    if (request('DeletedID')) { $message = 'The dimension has been deleted.'; }
    if (request('ClosedID')) { $message = 'The dimension has been closed. There can be no more changes to it. #' . request('ClosedID'); }
    if (request('ReopenedID')) { $message = 'The dimension has been re-opened. #' . request('ReopenedID'); }

    $ref = request('ref', '');
    $name = request('name', '');
    $type_ = (int) request('type_', 1);
    $date_ = request('date_', date('Y-m-d'));
    $due_date = request('due_date', '');
    $dimension_tags = request('dimension_tags', []);
    $memo_ = request('memo_', '');
    $closed = false;

    // CRUD Operations
    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        if (strlen($name) == 0) {
            $error = 'The dimension name must be entered.';
        } else {
            if ($selected_id) {
                // Update
                \DB::table('dimensions')->where('id', $selected_id)->update([
                    'name' => $name, 'type_' => $type_, 'date_' => $date_, 'due_date' => $due_date, 'memo_' => $memo_
                ]);
                // Update tag associations
                \DB::table('tag_associations')->where('record_id', $selected_id)->where('tag_type', 2)->delete();
                foreach ($dimension_tags as $tag_id) {
                    \DB::table('tag_associations')->insert([
                        'tag_id' => $tag_id, 'record_id' => $selected_id, 'tag_type' => 2, 'created_at' => now(), 'updated_at' => now()
                    ]);
                }
                return redirect(route('dimensions.entry', ['UpdatedID' => $selected_id]));
            } else {
                // Validate reference
                $existing = \DB::table('dimensions')->where('reference', $ref)->first();
                if ($existing) {
                    $error = 'The reference entered already exists.';
                } else {
                    // Add
                    $id = \DB::table('dimensions')->insertGetId([
                        'reference' => $ref, 'code' => $ref, 'name' => $name, 'type_' => $type_, 'date_' => $date_,
                        'due_date' => $due_date, 'memo_' => $memo_, 'notes' => $memo_, 'is_active' => true,
                        'created_at' => now(), 'updated_at' => now()
                    ]);
                    foreach ($dimension_tags as $tag_id) {
                        \DB::table('tag_associations')->insert([
                            'tag_id' => $tag_id, 'record_id' => $id, 'tag_type' => 2, 'created_at' => now(), 'updated_at' => now()
                        ]);
                    }
                    return redirect(route('dimensions.entry', ['AddedID' => $id]));
                }
            }
        }
    }

    if (request('delete')) {
        if ($selected_id) {
            $hasPayments = \DB::table('journal_entries')
                ->where('dimension_id', $selected_id)
                ->orWhere('dimension2_id', $selected_id)
                ->exists();
            if ($hasPayments) {
                $error = 'This dimension cannot be deleted because it has already been processed.';
            } else {
                \DB::table('tag_associations')->where('record_id', $selected_id)->where('tag_type', 2)->delete();
                \DB::table('dimensions')->where('id', $selected_id)->delete();
                return redirect(route('dimensions.entry', ['DeletedID' => $selected_id]));
            }
        }
    }

    if (request('close')) {
        if ($selected_id) {
            \DB::table('dimensions')->where('id', $selected_id)->update(['closed' => true, 'is_active' => false]);
            return redirect(route('dimensions.entry', ['ClosedID' => $selected_id]));
        }
    }

    if (request('reopen')) {
        if ($selected_id) {
            \DB::table('dimensions')->where('id', $selected_id)->update(['closed' => false, 'is_active' => true]);
            return redirect(route('dimensions.entry', ['ReopenedID' => $selected_id]));
        }
    }

    // Auto-generate next reference
    if ($selected_id == '' && !$ref) {
        $maxRef = \DB::table('dimensions')->where('reference', 'like', 'DIM-%')->max('reference');
        $nextNum = $maxRef ? (int) substr($maxRef, 4) + 1 : 1;
        $ref = 'DIM-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    // Load existing dimension for edit
    $edit_dimension = null;
    if ($selected_id) {
        $edit_dimension = \DB::table('dimensions')->where('id', $selected_id)->first();
        if ($edit_dimension) {
            $ref = $edit_dimension->reference ?? $edit_dimension->code;
            $name = $edit_dimension->name;
            $type_ = $edit_dimension->type_ ?? 1;
            $date_ = $edit_dimension->date_ ?? date('Y-m-d');
            $due_date = $edit_dimension->due_date ?? '';
            $memo_ = $edit_dimension->memo_ ?? $edit_dimension->notes ?? '';
            $closed = $edit_dimension->closed ?? false;
            // Get associated tags
            $tagIds = \DB::table('tag_associations')
                ->where('record_id', $selected_id)->where('tag_type', 2)
                ->pluck('tag_id')->toArray();
            $dimension_tags = $tagIds;
        }
    }

    if (request('_reset')) {
        return redirect(route('dimensions.entry'));
    }

    if (!$due_date) {
        $due_date = date('Y-m-d', strtotime('+1 month'));
    }

    return view('dimensions.entry', compact(
        'selected_id', 'message', 'error', 'use_dimension', 'dim_tags',
        'ref', 'name', 'type_', 'date_', 'due_date', 'memo_', 'closed',
        'dimension_tags', 'edit_dimension'
    ));
})->name('dimensions.entry');
Route::match(['GET', 'POST'], '/dimensions/outstanding', function () {
    $outstanding_only = request()->has('outstanding_only') ? (int) request('outstanding_only') : 0;
    $ref = request('OrderNumber', '');
    $type_ = request('type_', '');
    $fromDate = request('FromDate', date('Y-m-d', strtotime('-5 months')));
    $toDate = request('ToDate', date('Y-m-d'));
    $overdueOnly = request('OverdueOnly') ? true : false;
    $openOnly = $outstanding_only ? true : (request('OpenOnly') ? true : false);
    $search = request('SearchOrders') ? true : false;

    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $tags = \DB::table('tags')->where('type', 2)->where('inactive', false)->orderBy('name')->get(['id', 'name']);

    $query = \DB::table('dimensions')
        ->select([
            'dimensions.id',
            \DB::raw('COALESCE(dimensions.reference, dimensions.code) as reference'),
            'dimensions.name',
            'dimensions.type_',
            'dimensions.date_',
            'dimensions.due_date',
            'dimensions.closed',
            'dimensions.memo_',
        ]);

    if ($ref) {
        $query->where(function ($q) use ($ref) {
            $q->where('dimensions.reference', 'like', '%' . $ref . '%')
              ->orWhere('dimensions.code', 'like', '%' . $ref . '%');
        });
    }

    if ($type_ !== '' && $type_ !== null) {
        $query->where('dimensions.type_', (int) $type_);
    }

    if ($fromDate) {
        $query->where('dimensions.date_', '>=', $fromDate);
    }

    if ($toDate) {
        $query->where('dimensions.date_', '<=', $toDate);
    }

    if ($openOnly) {
        $query->where('dimensions.closed', false);
    }

    if ($overdueOnly) {
        $query->where('dimensions.due_date', '<', date('Y-m-d'))
              ->where(function ($q) { $q->whereNull('dimensions.closed')->orWhere('dimensions.closed', false); });
    }

    $query->orderBy('dimensions.due_date', 'asc')
          ->orderBy('dimensions.date_', 'asc');

    $dimensions = $query->get();

    // Compute balances for each dimension
    $dimensions = $dimensions->map(function ($d) use ($fromDate, $toDate) {
        // In FA, get_dimension_balance queries gl_trans with dimension_id.
        // Our journal_entries/lines don't have dimension columns yet, so compute 0.
        $d->balance = 0;
        $d->is_overdue = $d->due_date && $d->due_date < date('Y-m-d') && !$d->closed;
        return $d;
    });

    return view('dimensions.outstanding', compact(
        'outstanding_only', 'ref', 'type_', 'fromDate', 'toDate',
        'overdueOnly', 'openOnly', 'search', 'use_dimension', 'tags',
        'dimensions'
    ));
})->name('dimensions.outstanding');
Route::match(['GET', 'POST'], '/dimensions/inquiries', function () {
    $outstanding_only = 0;
    $ref = request('OrderNumber', '');
    $type_ = request('type_', '');
    $fromDate = request('FromDate', date('Y-m-d', strtotime('-5 months')));
    $toDate = request('ToDate', date('Y-m-d'));
    $overdueOnly = request('OverdueOnly') ? true : false;
    $openOnly = request('OpenOnly') ? true : false;
    $search = request('SearchOrders') ? true : false;

    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $tags = \DB::table('tags')->where('type', 2)->where('inactive', false)->orderBy('name')->get(['id', 'name']);

    $query = \DB::table('dimensions')
        ->select([
            'dimensions.id',
            \DB::raw('COALESCE(dimensions.reference, dimensions.code) as reference'),
            'dimensions.name',
            'dimensions.type_',
            'dimensions.date_',
            'dimensions.due_date',
            'dimensions.closed',
            'dimensions.memo_',
        ]);

    if ($ref) {
        $query->where(function ($q) use ($ref) {
            $q->where('dimensions.reference', 'like', '%' . $ref . '%')
              ->orWhere('dimensions.code', 'like', '%' . $ref . '%');
        });
    }

    if ($type_ !== '' && $type_ !== null) {
        $query->where('dimensions.type_', (int) $type_);
    }

    if ($fromDate) {
        $query->where('dimensions.date_', '>=', $fromDate);
    }

    if ($toDate) {
        $query->where('dimensions.date_', '<=', $toDate);
    }

    if ($openOnly) {
        $query->where('dimensions.closed', false);
    }

    if ($overdueOnly) {
        $query->where('dimensions.due_date', '<', date('Y-m-d'))
              ->where(function ($q) { $q->whereNull('dimensions.closed')->orWhere('dimensions.closed', false); });
    }

    $query->orderBy('dimensions.due_date', 'asc')
          ->orderBy('dimensions.date_', 'asc');

    $dimensions = $query->get();

    $dimensions = $dimensions->map(function ($d) {
        $d->balance = 0;
        $d->is_overdue = $d->due_date && $d->due_date < date('Y-m-d') && !$d->closed;
        return $d;
    });

    return view('dimensions.inquiries.index', compact(
        'outstanding_only', 'ref', 'type_', 'fromDate', 'toDate',
        'overdueOnly', 'openOnly', 'search', 'use_dimension', 'tags',
        'dimensions'
    ));
})->name('dimensions.inquiries.index');
Route::get('/dimensions/reports', function () { return view('dimensions.reports.index'); })->name('dimensions.reports.index');
Route::match(['GET', 'POST'], '/dimensions/tags', function () {
    $tag_type = 2; // TAG_DIMENSION
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $name = request('name', '');
        $description = request('description', '');
        if (strlen($name) == 0) {
            $error = 'The tag name cannot be empty.';
        } else {
            if ($selected_id) {
                Tag::where('id', $selected_id)->update(['name' => $name, 'description' => $description]);
                $message = 'Selected tag settings have been updated';
            } else {
                Tag::create(['type' => $tag_type, 'name' => $name, 'description' => $description]);
                $message = 'New tag has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode === 'Delete') {
        if ($selected_id) {
            $associated = \DB::table('tag_associations')->where('tag_id', $selected_id)->exists();
            if ($associated) {
                $error = 'Cannot delete this tag because records have been created referring to it.';
            } else {
                Tag::where('id', $selected_id)->delete();
                $message = 'Selected tag has been deleted';
            }
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $t = Tag::find(request('toggle_inactive'));
        if ($t) { $t->update(['inactive' => !$t->inactive]); }
        return redirect()->route('dimensions.tags', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['dim_tags_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('dim_tags_show_inactive');
    }

    $show_inactive = session('dim_tags_show_inactive', false);
    $tags = Tag::where('type', $tag_type)
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('name')
        ->get();

    if ($Mode === 'Edit' && $selected_id) {
        $edit_tag = Tag::find($selected_id);
        if (!$edit_tag) { $edit_tag = null; $selected_id = ''; }
    } else {
        $edit_tag = null;
    }

    $name = $edit_tag->name ?? '';
    $description = $edit_tag->description ?? '';

    return view('dimensions.tags', compact('tags', 'edit_tag', 'selected_id', 'message', 'error', 'show_inactive', 'name', 'description'));
})->name('dimensions.tags');

// Banking and General Ledger Module
Route::match(['GET', 'POST'], '/banking/payments', function () {
    $message = '';
    $error = '';

    $trans_type = '2';

    if (!session()->has('pay_items')) {
        session(['pay_items' => [
            'trans_type' => $trans_type,
            'order_id' => 0,
            'reference' => '',
            'tran_date' => date('Y-m-d'),
            'memo_' => '',
            'original_amount' => 0,
            'gl_items' => [],
        ]]);
    }

    if (request()->isMethod('POST')) {
        $cart = session('pay_items', []);

        // Delete item
        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['gl_items'][$delete_id])) {
            unset($cart['gl_items'][$delete_id]);
            $cart['gl_items'] = array_values($cart['gl_items']);
            session(['pay_items' => $cart, 'edit_index' => null]);
        }

        // Clear edit index on new selection changes
        if (request('ClearEdit')) {
            session(['edit_index' => null]);
        }

        // Add item
        if (request('AddItem')) {
            $code_id = request('code_id', '');
            $amount = (float) request('amount', 0);
            if ($amount <= 0) {
                $error = 'The amount entered is not a valid number or is less than zero.';
            } elseif (empty($code_id)) {
                $error = 'Please select a GL account.';
            } else {
                $signed_amount = (float)$amount;
                $cart['gl_items'][] = [
                    'code_id' => $code_id,
                    'dimension_id' => request('dimension_id', ''),
                    'dimension2_id' => request('dimension2_id', ''),
                    'amount' => $signed_amount,
                    'description' => '',
                    'memo' => request('LineMemo', ''),
                ];
                session(['pay_items' => $cart, 'edit_index' => null]);
            }
        }

        // Update item
        if (request('UpdateItem')) {
            $edit_idx = session('edit_index');
            if ($edit_idx !== null && isset($cart['gl_items'][$edit_idx])) {
                $code_id = request('code_id', '');
                $amount = (float) request('amount', 0);
                if ($amount <= 0) {
                    $error = 'The amount entered is not a valid number or is less than zero.';
                } elseif (empty($code_id)) {
                    $error = 'Please select a GL account.';
                } else {
                    $cart['gl_items'][$edit_idx] = [
                        'code_id' => $code_id,
                        'dimension_id' => request('dimension_id', ''),
                        'dimension2_id' => request('dimension2_id', ''),
                        'amount' => (float)$amount,
                        'description' => '',
                        'memo' => request('LineMemo', ''),
                    ];
                    session(['pay_items' => $cart, 'edit_index' => null]);
                }
            }
        }

        // Cancel item changes
        if (request('CancelItemChanges')) {
            session(['edit_index' => null]);
        }

        // Process Payment - save to database
        if (request('Process')) {
            $cart['reference'] = request('ref', $cart['reference']);
            $cart['tran_date'] = request('date_', $cart['tran_date']);
            $cart['memo_'] = request('memo_', $cart['memo_']);

            if (count($cart['gl_items']) < 1) {
                $error = 'You must enter at least one payment line.';
            } else {
                $totalAmt = array_sum(array_column($cart['gl_items'], 'amount'));
                if ($totalAmt == 0) {
                    $error = 'The total bank amount cannot be 0.';
                } else {
                    $jeId = \DB::table('journal_entries')->insertGetId([
                        'company_id' => session('company_id', 1),
                        'entry_number' => $cart['reference'] ?: 'PMT-' . date('YmdHis'),
                        'entry_date' => $cart['tran_date'],
                        'reference_type' => 'payment',
                        'reference_id' => null,
                        'description' => $cart['memo_'] ?: 'Bank Payment Entry',
                        'total_debit' => $totalAmt,
                        'total_credit' => $totalAmt,
                        'is_posted' => true,
                        'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    foreach ($cart['gl_items'] as $item) {
                        $account = \DB::table('accounts')->where('code', $item['code_id'])->first();
                        if ($account) {
                            $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                            $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                            \DB::table('journal_entry_lines')->insert([
                                'journal_entry_id' => $jeId,
                                'account_id' => $account->id,
                                'description' => $item['memo'],
                                'debit_amount' => $debit,
                                'credit_amount' => $credit,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                    // Also write bank_trans
                    $bankAccountId = request('bank_account');
                    if ($bankAccountId) {
                        \DB::table('bank_trans')->insert([
                            'ref_type' => 'payment',
                            'reference_id' => $jeId,
                            'bank_account_id' => $bankAccountId,
                            'trans_date' => $cart['tran_date'],
                            'reference' => $cart['reference'],
                            'amount' => -$totalAmt,
                            'memo' => $cart['memo_'],
                            'created_by' => auth()->id() ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    $message = "Payment $jeId has been entered";
                    session(['pay_items' => null, 'edit_index' => null]);
                    return redirect()->route('banking.payments');
                }
            }
        }

        session(['pay_items' => $cart]);
    }

    $cart = session('pay_items', []);

    // Detect Edit action
    $edit_id = request('Edit');
    if ($edit_id !== null && isset($cart['gl_items'][$edit_id])) {
        session(['edit_index' => $edit_id]);
    }
    $edit_index = session('edit_index');

    $bank_accounts = \DB::table('bank_accounts')->where('inactive', false)->orderBy('bank_account_name')->get();
    $gl_accounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $customers = \DB::table('customers')->where('status', 'active')->orderBy('name')->get(['id', 'name', 'customer_code']);
    $suppliers = \DB::table('suppliers')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $quick_entries = \DB::table('quick_entries')->where('type', 1)->orderBy('description')->get();
    $home_currency = \DB::table('settings')->where('key', 'curr_default')->value('value') ?? 'USD';

    return view('banking.payments', compact(
        'message', 'error', 'cart', 'edit_index', 'bank_accounts', 'gl_accounts',
        'customers', 'suppliers', 'dimensions', 'use_dimension', 'quick_entries',
        'home_currency', 'trans_type'
    ));
})->name('banking.payments');
Route::match(['GET', 'POST'], '/banking/deposits', function () {
    $message = '';
    $error = '';

    if (!session()->has('dep_items')) {
        session(['dep_items' => [
            'trans_type' => '1',
            'order_id' => 0,
            'reference' => '',
            'tran_date' => date('Y-m-d'),
            'memo_' => '',
            'original_amount' => 0,
            'gl_items' => [],
        ]]);
    }

    if (request()->isMethod('POST')) {
        $cart = session('dep_items', []);

        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['gl_items'][$delete_id])) {
            unset($cart['gl_items'][$delete_id]);
            $cart['gl_items'] = array_values($cart['gl_items']);
            session(['dep_items' => $cart, 'dep_edit_index' => null]);
        }

        if (request('AddItem')) {
            $code_id = request('code_id', '');
            $amount = (float) request('amount', 0);
            if ($amount <= 0) {
                $error = 'The amount entered is not a valid number or is less than zero.';
            } elseif (empty($code_id)) {
                $error = 'Please select a GL account.';
            } else {
                $cart['gl_items'][] = [
                    'code_id' => $code_id,
                    'dimension_id' => request('dimension_id', ''),
                    'dimension2_id' => request('dimension2_id', ''),
                    'amount' => -(float)$amount,
                    'description' => '',
                    'memo' => request('LineMemo', ''),
                ];
                session(['dep_items' => $cart, 'dep_edit_index' => null]);
            }
        }

        if (request('UpdateItem')) {
            $edit_idx = session('dep_edit_index');
            if ($edit_idx !== null && isset($cart['gl_items'][$edit_idx])) {
                $code_id = request('code_id', '');
                $amount = (float) request('amount', 0);
                if ($amount <= 0) {
                    $error = 'The amount entered is not a valid number or is less than zero.';
                } elseif (empty($code_id)) {
                    $error = 'Please select a GL account.';
                } else {
                    $cart['gl_items'][$edit_idx] = [
                        'code_id' => $code_id,
                        'dimension_id' => request('dimension_id', ''),
                        'dimension2_id' => request('dimension2_id', ''),
                        'amount' => -(float)$amount,
                        'description' => '',
                        'memo' => request('LineMemo', ''),
                    ];
                    session(['dep_items' => $cart, 'dep_edit_index' => null]);
                }
            }
        }

        if (request('CancelItemChanges')) {
            session(['dep_edit_index' => null]);
        }

        if (request('Process')) {
            $cart['reference'] = request('ref', $cart['reference']);
            $cart['tran_date'] = request('date_', $cart['tran_date']);
            $cart['memo_'] = request('memo_', $cart['memo_']);

            if (count($cart['gl_items']) < 1) {
                $error = 'You must enter at least one deposit line.';
            } else {
                $totalAmt = abs(array_sum(array_column($cart['gl_items'], 'amount')));
                if ($totalAmt == 0) {
                    $error = 'The total bank amount cannot be 0.';
                } else {
                    $jeId = \DB::table('journal_entries')->insertGetId([
                        'company_id' => session('company_id', 1),
                        'entry_number' => $cart['reference'] ?: 'DEP-' . date('YmdHis'),
                        'entry_date' => $cart['tran_date'],
                        'reference_type' => 'deposit',
                        'reference_id' => null,
                        'description' => $cart['memo_'] ?: 'Bank Deposit Entry',
                        'total_debit' => $totalAmt,
                        'total_credit' => $totalAmt,
                        'is_posted' => true,
                        'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    foreach ($cart['gl_items'] as $item) {
                        $account = \DB::table('accounts')->where('code', $item['code_id'])->first();
                        if ($account) {
                            $debit = 0;
                            $credit = abs($item['amount']);
                            \DB::table('journal_entry_lines')->insert([
                                'journal_entry_id' => $jeId,
                                'account_id' => $account->id,
                                'description' => $item['memo'],
                                'debit_amount' => $debit,
                                'credit_amount' => $credit,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                    $bankAccountId = request('bank_account');
                    if ($bankAccountId) {
                        \DB::table('bank_trans')->insert([
                            'ref_type' => 'deposit',
                            'reference_id' => $jeId,
                            'bank_account_id' => $bankAccountId,
                            'trans_date' => $cart['tran_date'],
                            'reference' => $cart['reference'],
                            'amount' => $totalAmt,
                            'memo' => $cart['memo_'],
                            'created_by' => auth()->id() ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    $message = "Deposit $jeId has been entered";
                    session(['dep_items' => null, 'dep_edit_index' => null]);
                    return redirect()->route('banking.deposits');
                }
            }
        }

        session(['dep_items' => $cart]);
    }

    $cart = session('dep_items', []);

    $edit_id = request('Edit');
    if ($edit_id !== null && isset($cart['gl_items'][$edit_id])) {
        session(['dep_edit_index' => $edit_id]);
    }
    $edit_index = session('dep_edit_index');

    $bank_accounts = \DB::table('bank_accounts')->where('inactive', false)->orderBy('bank_account_name')->get();
    $gl_accounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $customers = \DB::table('customers')->where('status', 'active')->orderBy('name')->get(['id', 'name', 'customer_code']);
    $suppliers = \DB::table('suppliers')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $quick_entries = \DB::table('quick_entries')->where('type', 0)->orderBy('description')->get();
    $home_currency = \DB::table('settings')->where('key', 'curr_default')->value('value') ?? 'USD';

    return view('banking.deposits', compact(
        'message', 'error', 'cart', 'edit_index', 'bank_accounts', 'gl_accounts',
        'customers', 'suppliers', 'dimensions', 'use_dimension', 'quick_entries',
        'home_currency'
    ));
})->name('banking.deposits');
Route::match(['GET', 'POST'], '/banking/transfers', function () {
    $message = '';
    $error = '';

    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $home_currency = \DB::table('settings')->where('key', 'curr_default')->value('value') ?? 'USD';
    $bank_accounts = \DB::table('bank_accounts')->where('inactive', false)->orderBy('bank_account_name')->get();
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    if (request()->isMethod('POST') && request('submit')) {
        $fromAccount = request('FromBankAccount', '');
        $toAccount = request('ToBankAccount', '');
        $date = request('DatePaid', date('Y-m-d'));
        $ref = request('ref', '');
        $amount = (float) request('amount', 0);
        $charge = (float) request('charge', 0);
        $targetAmount = request('target_amount');
        $memo = request('memo_', '');
        $dimId = request('dimension_id', '');
        $dim2Id = request('dimension2_id', '');
        $inputError = 0;

        if ($fromAccount == $toAccount) {
            $error = 'The source and destination bank accounts cannot be the same.';
            $inputError = 1;
        } elseif (empty($fromAccount) || empty($toAccount)) {
            $error = 'Please select both source and destination bank accounts.';
            $inputError = 1;
        } elseif ($amount <= 0) {
            $error = 'The entered amount is invalid or less than zero.';
            $inputError = 1;
        } elseif ($charge < 0) {
            $error = 'The entered bank charge is invalid or less than zero.';
            $inputError = 1;
        } elseif ($charge > 0) {
            $chargeAct = \DB::table('bank_accounts')->where('id', $fromAccount)->value('bank_charge_act');
            if (empty($chargeAct)) {
                $error = 'The Bank Charge Account has not been set in System and General GL Setup.';
                $inputError = 1;
            }
        }

        if (!$inputError) {
            $fromBank = \DB::table('bank_accounts')->where('id', $fromAccount)->first();
            $toBank = \DB::table('bank_accounts')->where('id', $toAccount)->first();
            $fromCurrency = $fromBank->bank_curr_code ?? $home_currency;
            $toCurrency = $toBank->bank_curr_code ?? $home_currency;
            $multiCurrency = ($fromCurrency != $toCurrency);

            if ($multiCurrency && (empty($targetAmount) || (float) $targetAmount <= 0)) {
                $error = 'The incoming amount must be specified for multi-currency transfers.';
                $inputError = 1;
            }
        }

        if (!$inputError) {
            // Create journal entry for the transfer
            $totalTransferAmount = $amount + $charge;
            $jeId = \DB::table('journal_entries')->insertGetId([
                'company_id' => session('company_id', 1),
                'entry_number' => $ref ?: 'TRF-' . date('YmdHis'),
                'entry_date' => $date,
                'reference_type' => 'transfer',
                'reference_id' => null,
                'description' => $memo ?: 'Bank Account Transfer',
                'total_debit' => $totalTransferAmount,
                'total_credit' => $totalTransferAmount,
                'is_posted' => true,
                'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $fromAccountModel = \DB::table('accounts')->where('code', $fromBank->account_code)->first();
            $toAccountModel = \DB::table('accounts')->where('code', $toBank->account_code)->first();

            if ($fromAccountModel) {
                \DB::table('journal_entry_lines')->insert([
                    'journal_entry_id' => $jeId,
                    'account_id' => $fromAccountModel->id,
                    'description' => 'Transfer out: ' . ($memo ?: 'Bank transfer'),
                    'debit_amount' => 0,
                    'credit_amount' => $totalTransferAmount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($toAccountModel) {
                $incomingAmount = $multiCurrency ? (float) $targetAmount : $totalTransferAmount;
                \DB::table('journal_entry_lines')->insert([
                    'journal_entry_id' => $jeId,
                    'account_id' => $toAccountModel->id,
                    'description' => 'Transfer in: ' . ($memo ?: 'Bank transfer'),
                    'debit_amount' => $incomingAmount,
                    'credit_amount' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Bank charge entry
            if ($charge > 0 && $fromAccountModel) {
                $chargeAct = \DB::table('bank_accounts')->where('id', $fromAccount)->value('bank_charge_act');
                if ($chargeAct) {
                    $chargeAccountModel = \DB::table('accounts')->where('code', $chargeAct)->first();
                    if ($chargeAccountModel) {
                        \DB::table('journal_entry_lines')->insert([
                            'journal_entry_id' => $jeId,
                            'account_id' => $chargeAccountModel->id,
                            'description' => 'Bank charge for transfer',
                            'debit_amount' => 0,
                            'credit_amount' => $charge,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        // Adjust the from account debit to include charge
                        \DB::table('journal_entry_lines')
                            ->where('journal_entry_id', $jeId)
                            ->where('account_id', $fromAccountModel->id)
                            ->update(['credit_amount' => $totalTransferAmount]);
                    }
                }
            }

            // Write bank_trans records
            if ($fromBank) {
                \DB::table('bank_trans')->insert([
                    'ref_type' => 'transfer',
                    'reference_id' => $jeId,
                    'bank_account_id' => $fromAccount,
                    'trans_date' => $date,
                    'reference' => $ref,
                    'amount' => -$totalTransferAmount,
                    'memo' => $memo,
                    'created_by' => auth()->id() ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($toBank) {
                $incomingAmount = $multiCurrency ? (float) $targetAmount : $totalTransferAmount;
                \DB::table('bank_trans')->insert([
                    'ref_type' => 'transfer',
                    'reference_id' => $jeId,
                    'bank_account_id' => $toAccount,
                    'trans_date' => $date,
                    'reference' => $ref,
                    'amount' => $incomingAmount,
                    'memo' => $memo,
                    'created_by' => auth()->id() ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $message = 'Transfer has been entered';
            return redirect()->route('banking.transfers');
        }
    }

    $fromAccount = request('FromBankAccount', '');
    $toAccount = request('ToBankAccount', '');

    // Currency info
    $fromBank = $fromAccount ? \DB::table('bank_accounts')->where('id', $fromAccount)->first() : null;
    $toBank = $toAccount ? \DB::table('bank_accounts')->where('id', $toAccount)->first() : null;
    $fromCurrency = $fromBank->bank_curr_code ?? $home_currency;
    $toCurrency = $toBank->bank_curr_code ?? $home_currency;
    $multiCurrency = $fromAccount && $toAccount && ($fromCurrency != $toCurrency);

    return view('banking.transfers', compact(
        'message', 'error', 'bank_accounts', 'dimensions', 'use_dimension',
        'home_currency', 'fromCurrency', 'toCurrency', 'multiCurrency'
    ));
})->name('banking.transfers');
Route::match(['GET', 'POST'], '/banking/journal/entry', function () {
    $message = '';
    $error = '';

    // Initialize session cart
    if (!session()->has('journal_items')) {
        session(['journal_items' => [
            'order_id' => 0,
            'reference' => '',
            'tran_date' => date('Y-m-d'),
            'doc_date' => date('Y-m-d'),
            'event_date' => date('Y-m-d'),
            'source_ref' => '',
            'currency' => '',
            'rate' => 0,
            'memo_' => '',
            'gl_items' => [],
            'taxable' => false,
        ]]);
    }

    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $home_currency = \DB::table('settings')->where('key', 'curr_default')->value('value') ?? 'USD';
    $currencies = \DB::table('currencies')->where('inactive', false)->orderBy('curr_abrev')->get(['curr_abrev', 'currency', 'curr_symbol']);
    $gl_accounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name', 'account_type']);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $tax_types = \DB::table('tax_types')->where('inactive', false)->orderBy('name')->get(['id', 'name', 'rate']);
    $quick_entries = \DB::table('quick_entries')->where('type', 2)->orderBy('description')->get();

    if (request()->isMethod('POST')) {
        $cart = session('journal_items', []);

        // Delete item
        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['gl_items'][$delete_id])) {
            unset($cart['gl_items'][$delete_id]);
            $cart['gl_items'] = array_values($cart['gl_items']);
            session(['journal_items' => $cart, 'journal_edit_index' => null]);
        }

        // Add item
        if (request('AddItem')) {
            $code_id = request('code_id', '');
            $amtDebit = (float) request('AmountDebit', 0);
            $amtCredit = (float) request('AmountCredit', 0);

            if (empty($code_id)) {
                $error = 'You must select GL account.';
            } elseif ($amtDebit == 0 && $amtCredit == 0) {
                $error = 'You must enter either a debit amount or a credit amount.';
            } elseif ($amtDebit > 0 && $amtCredit > 0) {
                $error = 'You cannot enter both debit and credit.';
            } elseif ($amtDebit < 0 || $amtCredit < 0) {
                $error = 'Amounts must be positive numbers.';
            } else {
                $amount = $amtDebit > 0 ? $amtDebit : -$amtCredit;
                $cart['gl_items'][] = [
                    'code_id' => $code_id,
                    'dimension_id' => request('dimension_id', ''),
                    'dimension2_id' => request('dimension2_id', ''),
                    'amount' => $amount,
                    'memo' => request('LineMemo', ''),
                    'person_id' => request('person_id', ''),
                ];
                session(['journal_items' => $cart, 'journal_edit_index' => null]);
            }
        }

        // Update item
        if (request('UpdateItem')) {
            $edit_idx = session('journal_edit_index');
            if ($edit_idx !== null && isset($cart['gl_items'][$edit_idx])) {
                $code_id = request('code_id', '');
                $amtDebit = (float) request('AmountDebit', 0);
                $amtCredit = (float) request('AmountCredit', 0);

                if (empty($code_id)) {
                    $error = 'You must select GL account.';
                } elseif ($amtDebit == 0 && $amtCredit == 0) {
                    $error = 'You must enter either a debit amount or a credit amount.';
                } elseif ($amtDebit > 0 && $amtCredit > 0) {
                    $error = 'You cannot enter both debit and credit.';
                } elseif ($amtDebit < 0 || $amtCredit < 0) {
                    $error = 'Amounts must be positive numbers.';
                } else {
                    $amount = $amtDebit > 0 ? $amtDebit : -$amtCredit;
                    $cart['gl_items'][$edit_idx] = [
                        'code_id' => $code_id,
                        'dimension_id' => request('dimension_id', ''),
                        'dimension2_id' => request('dimension2_id', ''),
                        'amount' => $amount,
                        'memo' => request('LineMemo', ''),
                        'person_id' => request('person_id', ''),
                    ];
                    session(['journal_items' => $cart, 'journal_edit_index' => null]);
                }
            }
        }

        if (request('CancelItemChanges')) {
            session(['journal_edit_index' => null]);
        }

        // Quick Entry Go
        if (request('go')) {
            $qe_id = request('quick', '');
            if ($qe_id) {
                $qelines = \DB::table('quick_entry_lines')->where('qid', $qe_id)->get();
                foreach ($qelines as $qel) {
                    $action = $qel->action;
                    $dest_id = $qel->dest_id;
                    $qel_amount = $qel->amount;
                    $qel_memo = $qel->memo;
                    $code_id = $dest_id;
                    if (!empty($code_id)) {
                        $cart['gl_items'][] = [
                            'code_id' => $code_id,
                            'dimension_id' => $qel->dimension_id ?? '',
                            'dimension2_id' => $qel->dimension2_id ?? '',
                            'amount' => (float) $qel_amount,
                            'memo' => $qel_memo,
                            'person_id' => '',
                        ];
                    }
                }
                session(['journal_items' => $cart, 'journal_edit_index' => null]);
            }
        }

        // Process
        if (request('Process')) {
            $cart['tran_date'] = request('date_', $cart['tran_date']);
            $cart['doc_date'] = request('doc_date', $cart['doc_date']);
            $cart['event_date'] = request('event_date', $cart['event_date']);
            $cart['reference'] = request('ref', $cart['reference']);
            $cart['source_ref'] = request('source_ref', '');
            $cart['currency'] = request('currency', $home_currency);
            $cart['memo_'] = request('memo_', $cart['memo_']);
            $cart['taxable'] = request('taxable_trans') ? true : false;

            if (count($cart['gl_items']) < 1) {
                $error = 'You must enter at least one journal line.';
            } else {
                $total = array_sum(array_column($cart['gl_items'], 'amount'));
                if (abs($total) > 0.001) {
                    $error = 'The journal must balance (debits equal to credits) before it can be processed.';
                } else {
                    $jeId = \DB::table('journal_entries')->insertGetId([
                        'company_id' => session('company_id', 1),
                        'entry_number' => $cart['reference'] ?: 'JRN-' . date('YmdHis'),
                        'entry_date' => $cart['tran_date'],
                        'reference_type' => 'journal',
                        'reference_id' => null,
                        'description' => $cart['memo_'] ?: 'Journal Entry',
                        'total_debit' => array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $cart['gl_items'])),
                        'total_credit' => array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $cart['gl_items'])),
                        'is_posted' => true,
                        'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    foreach ($cart['gl_items'] as $item) {
                        $account = \DB::table('accounts')->where('code', $item['code_id'])->first();
                        if ($account) {
                            $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                            $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                            \DB::table('journal_entry_lines')->insert([
                                'journal_entry_id' => $jeId,
                                'account_id' => $account->id,
                                'description' => $item['memo'],
                                'debit_amount' => $debit,
                                'credit_amount' => $credit,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                    $message = "Journal entry has been entered #$jeId";
                    session(['journal_items' => null, 'journal_edit_index' => null]);
                    return redirect()->route('banking.journal.entry');
                }
            }
        }

        session(['journal_items' => $cart]);
    }

    $cart = session('journal_items', []);

    $edit_id = request('Edit');
    if ($edit_id !== null && isset($cart['gl_items'][$edit_id])) {
        session(['journal_edit_index' => $edit_id]);
    }
    $edit_index = session('journal_edit_index');

    $total_debit = array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $cart['gl_items'] ?? []));
    $total_credit = array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $cart['gl_items'] ?? []));

    $tabs_sel = request('_tabs_sel', 'gl');

    return view('banking.journal.entry', compact(
        'message', 'error', 'cart', 'edit_index', 'gl_accounts', 'dimensions',
        'use_dimension', 'home_currency', 'currencies', 'tax_types', 'quick_entries',
        'total_debit', 'total_credit', 'tabs_sel'
    ));
})->name('banking.journal.entry');
Route::match(['GET', 'POST'], '/banking/budget-entry', function () {
    $message = '';
    $error = '';

    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $gl_accounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $fiscal_years = \DB::table('fiscal_years')->orderBy('begin', 'desc')->get();

    if (request()->isMethod('POST')) {
        $account = request('account', '');
        $dim1 = request('dim1', 0);
        $dim2 = request('dim2', 0);
        $fyear = request('fyear', '');
        $begin = request('begin');
        $end = request('end');

        // Save budget
        if (request('add')) {
            $months = [];
            for ($i = 0, $d = $begin; $d <= $end;) {
                $months[] = ['date_' => $d, 'amount' => (float) request('amount' . $i, 0)];
                $dt = new \DateTime($d);
                $dt->modify('+1 month');
                $d = $dt->format('Y-m-d');
                $i++;
            }
            \DB::beginTransaction();
            try {
                foreach ($months as $m) {
                    \DB::table('gl_budget_trans')->updateOrInsert(
                        ['date_' => $m['date_'], 'account' => $account, 'dimension_id' => $dim1, 'dimension2_id' => $dim2],
                        ['amount' => $m['amount'], 'updated_at' => now()]
                    );
                }
                \DB::commit();
                $message = 'The Budget has been saved.';
            } catch (\Exception $e) {
                \DB::rollBack();
                $error = 'Error saving budget: ' . $e->getMessage();
            }
        }

        // Delete budget
        if (request('delete')) {
            \DB::beginTransaction();
            try {
                for ($i = 0, $d = $begin; $d <= $end;) {
                    \DB::table('gl_budget_trans')
                        ->where('date_', $d)
                        ->where('account', $account)
                        ->where('dimension_id', $dim1)
                        ->where('dimension2_id', $dim2)
                        ->delete();
                    $dt = new \DateTime($d);
                    $dt->modify('+1 month');
                    $d = $dt->format('Y-m-d');
                    $i++;
                }
                \DB::commit();
                $message = 'The Budget has been deleted.';
            } catch (\Exception $e) {
                \DB::rollBack();
            }
        }
    }

    $account = request('account', '');
    $dim1 = request('dim1', 0);
    $dim2 = request('dim2', 0);
    $fyear = request('fyear', '');
    $begin = request('begin');
    $end = request('end');

    // Load fiscal year dates if not set
    if (!$begin || !$end) {
        $selected_year = $fyear ? \DB::table('fiscal_years')->where('id', $fyear)->first() : \DB::table('fiscal_years')->orderBy('begin', 'desc')->first();
        if ($selected_year) {
            $begin = $selected_year->begin->format('Y-m-d');
            $end = $selected_year->end->format('Y-m-d');
            if (!$fyear) $fyear = $selected_year->id;
        } else {
            $begin = date('Y-01-01');
            $end = date('Y-12-31');
        }
    }

    $showdims = (($use_dimension == 1 && $dim1 == 0) || ($use_dimension == 2 && $dim1 == 0 && $dim2 == 0));

    // Build monthly rows
    $month_rows = [];
    $total = $btotal = $ltotal = 0;
    for ($i = 0, $d = $begin; $d <= $end;) {
        $budgetAmt = \DB::table('gl_budget_trans')
            ->where('date_', $d)
            ->where('account', $account)
            ->where('dimension_id', $dim1)
            ->where('dimension2_id', $dim2)
            ->value('amount') ?? 0;

        // Dim. incl. sum
        $dimInclTotal = 0;
        if ($showdims) {
            $q = \DB::table('gl_budget_trans')
                ->where('date_', $d)
                ->where('account', $account);
            if ($use_dimension == 1) {
                $dimInclTotal = (float) $q->sum('amount');
            } elseif ($use_dimension == 2) {
                $dimInclTotal = (float) $q->sum('amount');
            }
            $btotal += $dimInclTotal;
        }

        // Last year GL transaction total (from journal entry lines)
        $lyBegin = (new \DateTime($d))->modify('-1 year')->format('Y-m-d');
        $lyEnd = (new \DateTime($d))->modify('-1 year')->format('Y-m-t');
        $accountModel = \DB::table('accounts')->where('code', $account)->first();
        $lamount = 0;
        if ($accountModel) {
            $lamount = (float) \DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
                ->where('accounts.code', $account)
                ->whereBetween('journal_entries.entry_date', [$lyBegin, $lyEnd])
                ->where('journal_entries.is_posted', true)
                ->selectRaw('COALESCE(SUM(journal_entry_lines.debit_amount - journal_entry_lines.credit_amount), 0) as total')
                ->value('total');
        }

        $month_rows[] = [
            'date' => $d,
            'input_name' => 'amount' . $i,
            'budget_amount' => request('amount' . $i, number_format($budgetAmt, 0)),
            'dim_incl' => $dimInclTotal,
            'last_year' => number_format($lamount, 0),
        ];
        $total += (float) request('amount' . $i, $budgetAmt);
        $ltotal += $lamount;

        $dt = new \DateTime($d);
        $dt->modify('+1 month');
        $d = $dt->format('Y-m-d');
        $i++;
    }

    return view('banking.budget-entry', compact(
        'message', 'error', 'fiscal_years', 'gl_accounts', 'dimensions',
        'use_dimension', 'fyear', 'account', 'dim1', 'dim2',
        'begin', 'end', 'showdims', 'month_rows', 'total', 'btotal', 'ltotal'
    ));
})->name('banking.budget-entry');
Route::match(['GET', 'POST'], '/banking/reconcile', function () {
    $message = '';
    $error = '';

    $bank_accounts = \DB::table('bank_accounts')->where('inactive', false)->orderBy('bank_account_name')->get();

    $bank_account = request('bank_account', session('reconcile_bank_account', ''));
    $reconcile_date = request('reconcile_date', session('reconcile_date', date('Y-m-d')));
    $bank_date = request('bank_date', '');
    $selected_statement_id = request('selected_statement', '');

    // Handle statement selection
    if ($bank_account && !$selected_statement_id) {
        $lastStatement = \DB::table('bank_reconcile_statements')
            ->where('bank_account_id', $bank_account)
            ->orderBy('reconcile_date', 'desc')
            ->first();
        if ($lastStatement) {
            $selected_statement_id = $lastStatement->id;
        }
    }

    // Save form inputs in session
    session(['reconcile_bank_account' => $bank_account, 'reconcile_date' => $reconcile_date]);

    if (request()->isMethod('POST')) {
        // Handle Reconcile / Reconcile All
        if (request('Reconcile') || request('ReconcileAll')) {
            if (!$bank_account) {
                $error = 'Please select a bank account.';
            } elseif (!$reconcile_date) {
                $error = 'Please enter a reconcile date.';
            } else {
                $reconcileAll = request('ReconcileAll') ? true : false;

                // Get all transactions for this account up to reconcile date
                $transactions = \DB::table('bank_trans')
                    ->where('bank_account_id', $bank_account)
                    ->where('trans_date', '<=', $reconcile_date)
                    ->orderBy('trans_date')
                    ->orderBy('id')
                    ->get();

                $last = request('last', []);

                foreach ($transactions as $t) {
                    $recName = 'rec_' . $t->id;
                    $isChecked = $reconcileAll ? true : (request($recName) ? true : false);
                    $wasReconciled = isset($last[$t->id]) && $last[$t->id] ? true : false;

                    if ($isChecked != $wasReconciled) {
                        \DB::table('bank_trans')
                            ->where('id', $t->id)
                            ->update(['reconciled' => $isChecked ? $reconcile_date : null]);
                    }
                }

                $message = 'Reconciliation has been saved.';
            }
        }

        // Handle statement selection
        if (request('select_statement')) {
            $selected_statement_id = request('select_statement');
        }
    }

    // Get existing statements for the bank account
    $statements = [];
    if ($bank_account) {
        $statements = \DB::table('bank_reconcile_statements')
            ->where('bank_account_id', $bank_account)
            ->orderBy('reconcile_date', 'desc')
            ->get();
    }

    // Calculate summary
    $begBalance = 0;
    $endBalance = 0;
    $totalAccount = 0;
    $reconciledAmount = 0;
    $lastReconcileDate = null;

    if ($bank_account) {
        $lastStatement = \DB::table('bank_reconcile_statements')
            ->where('bank_account_id', $bank_account)
            ->where('reconcile_date', '<=', $reconcile_date)
            ->orderBy('reconcile_date', 'desc')
            ->first();

        if ($lastStatement) {
            $begBalance = $lastStatement->end_balance;
            $lastReconcileDate = $lastStatement->reconcile_date;
        }

        // Get statement data if selected
        if ($selected_statement_id) {
            $stmt = \DB::table('bank_reconcile_statements')
                ->where('id', $selected_statement_id)
                ->first();
            if ($stmt) {
                $endBalance = $stmt->end_balance;
            }
        }

        // Total account transactions not yet reconciled
        $query = \DB::table('bank_trans')
            ->where('bank_account_id', $bank_account)
            ->where('trans_date', '<=', $reconcile_date);

        if ($lastReconcileDate) {
            // Only transactions after last reconciliation
            $query->where(function ($q) use ($lastReconcileDate) {
                $q->whereNull('reconciled')
                  ->orWhere('reconciled', '>', $lastReconcileDate);
            });
        } else {
            $query->whereNull('reconciled');
        }

        $totalAccount = (float) $query->sum('amount');

        // Reconciled amount (transactions reconciled in current statement period)
        $reconciledQuery = \DB::table('bank_trans')
            ->where('bank_account_id', $bank_account)
            ->whereNotNull('reconciled');

        if ($lastReconcileDate) {
            $reconciledQuery->where('reconciled', '>', $lastReconcileDate);
        }

        $reconciledAmount = (float) $reconciledQuery->where('trans_date', '<=', $reconcile_date)->sum('amount');
    }

    $difference = $endBalance - $begBalance - $reconciledAmount;

    // Load transactions for the table
    $transactions = collect();
    if ($bank_account) {
        $transactions = \DB::table('bank_trans')
            ->where('bank_account_id', $bank_account)
            ->where('trans_date', '<=', $reconcile_date)
            ->orderBy('trans_date')
            ->orderBy('id')
            ->get();
    }

    return view('banking.reconcile', compact(
        'message', 'error', 'bank_accounts', 'bank_account', 'reconcile_date',
        'bank_date', 'statements', 'selected_statement_id',
        'begBalance', 'endBalance', 'totalAccount', 'reconciledAmount', 'difference',
        'lastReconcileDate', 'transactions'
    ));
})->name('banking.reconcile');
Route::match(['GET', 'POST'], '/banking/accruals', function () {
    $message = '';
    $error = '';
    $previewRows = [];

    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $gl_accounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);

    if (request()->isMethod('POST')) {
        $date = request('date_', '');
        $accAct = request('acc_act', '');
        $resAct = request('res_act', '');
        $freq = request('freq', 3);
        $periods = (int) request('periods', 0);
        $amount = (float) request('amount', 0);
        $dimId = request('dimension_id', '');
        $dim2Id = request('dimension2_id', '');
        $memo = request('memo_', '');

        $inputError = 0;

        if (empty($date)) {
            $error = 'The entered date is invalid.';
            $inputError = 1;
        } elseif ($amount == 0) {
            $error = 'The amount cannot be 0.';
            $inputError = 1;
        } elseif ($periods < 1) {
            $error = 'The periods must be greater than 0.';
            $inputError = 1;
        }

        if (!$inputError) {
            $am = round($amount / $periods, 2);
            $remainder = $amount - $am * $periods;
            $memo = $memo ?: "Accruals for $amount";

            // Calculate period dates
            $currentDate = $date;
            for ($i = 0; $i < $periods; $i++) {
                $periodAmt = $am;
                if ($i == 0 && $remainder != 0) {
                    $periodAmt += $remainder;
                }

                $previewRows[] = [
                    'date' => $currentDate,
                    'acc_act' => $accAct,
                    'res_act' => $resAct,
                    'dim_id' => $dimId,
                    'dim2_id' => $dim2Id,
                    'acc_amount' => -$periodAmt,
                    'res_amount' => $periodAmt,
                    'memo' => $memo,
                ];

                if (request('go')) {
                    $jeId = \DB::table('journal_entries')->insertGetId([
                        'company_id' => session('company_id', 1),
                        'entry_number' => 'ACR-' . date('YmdHis') . '-' . $i,
                        'entry_date' => $currentDate,
                        'reference_type' => 'accrual',
                        'reference_id' => null,
                        'description' => $memo,
                        'total_debit' => $periodAmt,
                        'total_credit' => $periodAmt,
                        'is_posted' => true,
                        'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $accAccount = \DB::table('accounts')->where('code', $accAct)->first();
                    $resAccount = \DB::table('accounts')->where('code', $resAct)->first();

                    if ($accAccount) {
                        \DB::table('journal_entry_lines')->insert([
                            'journal_entry_id' => $jeId,
                            'account_id' => $accAccount->id,
                            'description' => $memo,
                            'debit_amount' => $periodAmt > 0 ? $periodAmt : 0,
                            'credit_amount' => $periodAmt < 0 ? -$periodAmt : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    if ($resAccount) {
                        \DB::table('journal_entry_lines')->insert([
                            'journal_entry_id' => $jeId,
                            'account_id' => $resAccount->id,
                            'description' => $memo,
                            'debit_amount' => 0,
                            'credit_amount' => $periodAmt,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Advance date based on frequency
                $dt = new \DateTime($currentDate);
                switch ($freq) {
                    case '1': // Weekly
                        $dt->modify('+7 days');
                        break;
                    case '2': // Bi-weekly
                        $dt->modify('+14 days');
                        break;
                    case '3': // Monthly
                        $dt->modify('+1 month');
                        break;
                    case '4': // Quarterly
                        $dt->modify('+3 months');
                        break;
                }
                $currentDate = $dt->format('Y-m-d');
            }

            if (request('go')) {
                $message = 'Revenue / Cost Accruals have been processed.';
                $previewRows = []; // Clear preview after processing
            }
        }
    }

    return view('banking.accruals', compact(
        'message', 'error', 'gl_accounts', 'dimensions', 'use_dimension', 'previewRows'
    ));
})->name('banking.accruals');
Route::match(['GET', 'POST'], '/banking/inquiries/journal', function () {
    $filterType = request('filterType', '-1');
    $fromDate = request('FromDate', date('Y-m-d', strtotime('-30 days')));
    $toDate = request('ToDate', date('Y-m-d'));
    $ref = request('Ref', '');
    $memo = request('Memo', '');
    $userId = request('userid', '');
    $alsoClosed = request('AlsoClosed') ? true : false;
    $dimension = request('dimension', '');
    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;

    $users = \DB::table('users')->orderBy('name')->get(['id', 'name']);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    // Journal types mapping (FA's systypes_array subset)
    $journal_types = [
        '-1' => 'All Types',
        '0'  => 'Journal Entry',
        '1'  => 'Bank Deposit',
        '2'  => 'Bank Payment',
        '4'  => 'Bank Transfer',
        '40' => 'Accrual',
    ];

    $entries = collect();
    if (request('Search') || request('filterType') || request('Ref') || request('Memo') || request('FromDate') != date('Y-m-d', strtotime('-30 days'))) {
        $query = \DB::table('journal_entries')
            ->leftJoin('users', 'journal_entries.created_by', '=', 'users.id')
            ->select([
                'journal_entries.*',
                'users.name as user_name',
            ]);

        // Filter by date range
        if ($fromDate && $toDate) {
            $query->whereBetween('journal_entries.entry_date', [$fromDate, $toDate]);
        }

        // Filter by reference
        if ($ref) {
            $query->where('journal_entries.entry_number', 'like', '%' . $ref . '%');
        }

        // Filter by description/memo
        if ($memo) {
            $query->where('journal_entries.description', 'like', '%' . $memo . '%');
        }

        // Filter by user
        if ($userId) {
            $query->where('journal_entries.created_by', $userId);
        }

        // Filter by type (map FA type codes to our reference_type)
        if ($filterType != '-1') {
            $typeMap = [
                '0' => 'journal',
                '1' => 'deposit',
                '2' => 'payment',
                '4' => 'transfer',
                '40' => 'accrual',
            ];
            if (isset($typeMap[$filterType])) {
                $query->where('journal_entries.reference_type', $typeMap[$filterType]);
            }
        }

        // Show closed (is_posted) - in FA, closed means voided. We'll treat is_posted = 0 as unposted/closed
        if (!$alsoClosed) {
            $query->where('journal_entries.is_posted', true);
        }

        $query->orderBy('journal_entries.entry_date', 'desc')
              ->orderBy('journal_entries.id', 'desc');

        $entries = $query->paginate(20)->withQueryString();
    }

    return view('banking.inquiries.journal', compact(
        'filterType', 'fromDate', 'toDate', 'ref', 'memo', 'userId', 'alsoClosed',
        'dimension', 'use_dimension', 'users', 'dimensions', 'journal_types', 'entries'
    ));
})->name('banking.inquiries.journal');
Route::match(['GET', 'POST'], '/banking/inquiries/gl', function () {
    $account = request('account', '');
    $fromDate = request('TransFromDate', date('Y-m-d', strtotime('-30 days')));
    $toDate = request('TransToDate', date('Y-m-d'));
    $dimension = request('Dimension', '');
    $dimension2 = request('Dimension2', '');
    $memo = request('Memo', '');
    $amountMin = (float) request('amount_min', 0);
    $amountMax = (float) request('amount_max', 0);

    $use_dimension = \DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0;
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $gl_accounts = \DB::table('accounts')->where('is_active', true)->orderBy('code')->get(['code', 'name']);

    $transactions = collect();
    $runningBalance = 0;
    $openingBalance = 0;
    $showBalances = false;

    if (request('Show') || request('account')) {
        $query = \DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->leftJoin('users', 'journal_entries.created_by', '=', 'users.id')
            ->select([
                'journal_entries.id as je_id',
                'journal_entries.entry_number',
                'journal_entries.entry_date',
                'journal_entries.reference_type',
                'journal_entries.description as je_description',
                'journal_entry_lines.debit_amount',
                'journal_entry_lines.credit_amount',
                'journal_entry_lines.description as line_description',
                'accounts.code as account_code',
                'accounts.name as account_name',
                'users.name as user_name',
            ])
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->where('journal_entries.is_posted', true);

        if ($account) {
            $query->where('accounts.code', $account);
            $showBalances = ($amountMin == 0 && $amountMax == 0);
        }

        if ($memo) {
            $query->where(function ($q) use ($memo) {
                $q->where('journal_entry_lines.description', 'like', '%' . $memo . '%')
                  ->orWhere('journal_entries.description', 'like', '%' . $memo . '%');
            });
        }

        if ($amountMin > 0) {
            $query->where(function ($q) use ($amountMin) {
                $q->where('journal_entry_lines.debit_amount', '>=', $amountMin)
                  ->orWhere('journal_entry_lines.credit_amount', '>=', $amountMin);
            });
        }
        if ($amountMax > 0) {
            $query->where(function ($q) use ($amountMax) {
                $q->where('journal_entry_lines.debit_amount', '<=', $amountMax)
                  ->orWhere('journal_entry_lines.credit_amount', '<=', $amountMax);
            });
        }

        $query->orderBy('journal_entries.entry_date')
              ->orderBy('journal_entries.id')
              ->orderBy('journal_entry_lines.id');

        $transactions = $query->get();

        // Calculate opening balance
        if ($showBalances && $account) {
            $beginDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
            $fiscalYear = \DB::table('fiscal_years')
                ->where('begin', '<=', $fromDate)
                ->where('end', '>=', $fromDate)
                ->first();
            $fyBegin = $fiscalYear ? $fiscalYear->begin->format('Y-m-d') : $fromDate;
            $balFrom = ($fyBegin < $fromDate) ? $fyBegin : $fromDate;
            $balFromDayBefore = date('Y-m-d', strtotime($fromDate . ' -1 day'));

            // Get balance from fiscal year start to day before fromDate
            $openingQuery = \DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
                ->where('accounts.code', $account)
                ->where('journal_entries.is_posted', true)
                ->where('journal_entries.entry_date', '<', $fromDate);

            if ($fyBegin && $fyBegin < $fromDate) {
                $openingQuery->where('journal_entries.entry_date', '>=', $fyBegin);
            }

            $openingBalance = (float) $openingQuery->selectRaw('COALESCE(SUM(journal_entry_lines.debit_amount - journal_entry_lines.credit_amount), 0) as bal')->value('bal');
            $runningBalance = $openingBalance;
        }

        // Apply running balance
        if ($showBalances) {
            $runningBalance = $openingBalance;
            $transactions = $transactions->map(function ($t) use (&$runningBalance) {
                $amount = $t->debit_amount - $t->credit_amount;
                $runningBalance += $amount;
                $t->running_balance = $runningBalance;
                return $t;
            });
        }
    }

    return view('banking.inquiries.gl', compact(
        'account', 'fromDate', 'toDate', 'dimension', 'dimension2', 'memo',
        'amountMin', 'amountMax', 'use_dimension', 'dimensions', 'gl_accounts',
        'transactions', 'openingBalance', 'runningBalance', 'showBalances'
    ));
})->name('banking.inquiries.gl');
Route::match(['GET', 'POST'], '/banking/inquiries/bank-account', function () {
    $bankAccount = request('bank_account', '');
    $fromDate = request('TransAfterDate', date('Y-m-d', strtotime('-30 days')));
    $toDate = request('TransToDate', date('Y-m-d'));

    $bankAccounts = \DB::table('bank_accounts')
        ->join('accounts', 'bank_accounts.account_code', '=', 'accounts.code')
        ->select('bank_accounts.id', 'bank_accounts.account_code', 'accounts.name', 'bank_accounts.bank_curr_code', 'bank_accounts.bank_name')
        ->orderBy('accounts.name')
        ->get();

    $bankAct = null;
    $transactions = collect();
    $openingBalance = 0;
    $runningTotal = 0;
    $totalDebit = 0;
    $totalCredit = 0;

    if (request('Show')) {
        $bankAct = $bankAccounts->firstWhere('id', $bankAccount);

        // Opening balance before the From date
        $openingQuery = \DB::table('bank_trans')
            ->where('bank_account_id', $bankAccount)
            ->where('trans_date', '<', $fromDate);
        $openingBalance = (float) $openingQuery->sum('amount');

        $runningTotal = $openingBalance;
        if ($openingBalance > 0) $totalDebit += $openingBalance;
        else $totalCredit += abs($openingBalance);

        $transactions = \DB::table('bank_trans')
            ->where('bank_account_id', $bankAccount)
            ->where('trans_date', '>=', $fromDate)
            ->where('trans_date', '<=', $toDate)
            ->orderBy('trans_date')
            ->orderBy('id')
            ->get();

        foreach ($transactions as $t) {
            $runningTotal += $t->amount;
            $t->running_balance = $runningTotal;
            if ($t->amount > 0) $totalDebit += $t->amount;
            else $totalCredit += abs($t->amount);
        }
    }

    return view('banking.inquiries.bank-account', compact(
        'bankAccount', 'fromDate', 'toDate', 'bankAccounts', 'bankAct',
        'transactions', 'openingBalance', 'runningTotal', 'totalDebit', 'totalCredit'
    ));
})->name('banking.inquiries.bank-account');
Route::match(['GET', 'POST'], '/banking/inquiries/tax', function () {
    $tax_last = \DB::table('settings')->where('key', 'tax_last')->value('value') ?? 1;
    $tax_prd = \DB::table('settings')->where('key', 'tax_prd')->value('value') ?? 3;

    if (request('Show')) {
        $fromDate = request('TransFromDate');
        $toDate = request('TransToDate');
    } else {
        $edate = date('Y-m-d', strtotime('last day of -' . $tax_last . ' months'));
        $bdate = date('Y-m-01', strtotime($edate));
        $bdate = date('Y-m-d', strtotime('-' . ($tax_prd - 1) . ' months', strtotime($bdate)));
        $fromDate = $bdate;
        $toDate = $edate;
    }

    $taxTypes = \DB::table('tax_types')->where('inactive', false)->orderBy('name')->get();
    $taxData = [];

    foreach ($taxTypes as $tax) {
        $payable = 0;
        $net_output = 0;
        $collectible = 0;
        $net_input = 0;

        if ($tax->sales_gl_code) {
            $salesAccount = \DB::table('accounts')->where('code', $tax->sales_gl_code)->first();
            if ($salesAccount) {
                $salesTotals = \DB::table('journal_entry_lines')
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_entry_lines.account_id', $salesAccount->id)
                    ->where('journal_entries.is_posted', true)
                    ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
                    ->selectRaw('COALESCE(SUM(credit_amount), 0) as total_credit, COALESCE(SUM(debit_amount), 0) as total_debit')
                    ->first();
                $payable = (float) $salesTotals->total_credit;
                $net_output = (float) ($salesTotals->total_credit);
            }
        }

        if ($tax->purchasing_gl_code) {
            $purchaseAccount = \DB::table('accounts')->where('code', $tax->purchasing_gl_code)->first();
            if ($purchaseAccount) {
                $purchaseTotals = \DB::table('journal_entry_lines')
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_entry_lines.account_id', $purchaseAccount->id)
                    ->where('journal_entries.is_posted', true)
                    ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
                    ->selectRaw('COALESCE(SUM(debit_amount), 0) as total_debit, COALESCE(SUM(credit_amount), 0) as total_credit')
                    ->first();
                $collectible = (float) $purchaseTotals->total_debit;
                $net_input = (float) ($purchaseTotals->total_debit);
            }
        }

        $displayCollectible = -$collectible;
        $net = $displayCollectible + $payable;
        $displayNetInput = -$net_input;

        $taxData[] = compact('tax', 'payable', 'net_output', 'collectible', 'net_input', 'displayCollectible', 'displayNetInput', 'net');
    }

    $totalNet = array_sum(array_column($taxData, 'net'));

    return view('banking.inquiries.tax', compact('fromDate', 'toDate', 'taxData', 'totalNet'));
})->name('banking.inquiries.tax');
Route::get('/banking/inquiries/tax-cash', function () { return view('banking.inquiries.tax-cash'); })->name('banking.inquiries.tax-cash');
Route::match(['GET', 'POST'], '/banking/reports/trial-balance', function () {
    $fromDate = request('TransFromDate', date('Y-m-d', strtotime('last day of this month - 30 days')));
    $toDate = request('TransToDate', date('Y-m-d', strtotime('last day of this month')));
    $dimension = (int) request('Dimension', 0);
    $dimension2 = (int) request('Dimension2', 0);
    $noZero = request('NoZero') ? true : false;
    $balanceOnly = request('Balance') ? true : false;
    $groupTotalOnly = request('GroupTotalOnly') ? true : false;
    $show = request('Show') ? true : false;

    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    // Get fiscal year for fromDate
    $fy = \DB::table('fiscal_years')
        ->where('begin', '<=', $fromDate)
        ->where('end', '>=', $fromDate)
        ->first();
    $fyBegin = $fy ? $fy->begin : $fromDate;
    if ($fyBegin > $fromDate) $fyBegin = $fromDate;
    $begin = date('Y-m-d', strtotime($fyBegin . ' -1 day'));

    // Preload all posted journal entry lines grouped by account for the full range
    // We'll use direct queries per account for accuracy matching FA

    $helper = new class($begin, $fromDate, $toDate) {
        public $begin, $fromDate, $toDate;
        public function __construct($b, $f, $t) { $this->begin = $b; $this->fromDate = $f; $this->toDate = $t; }

        public function getBalances($accountCode, $dimId, $dim2Id) {
            $account = \DB::table('accounts')->where('code', $accountCode)->first();
            if (!$account) return ['debit' => 0, 'credit' => 0, 'balance' => 0,
                                   'prev_debit' => 0, 'prev_credit' => 0, 'prev_balance' => 0,
                                   'curr_debit' => 0, 'curr_credit' => 0, 'curr_balance' => 0,
                                   'tot_debit' => 0, 'tot_credit' => 0, 'tot_balance' => 0];

            // prev: from $begin (exclusive) to $fromDate (exclusive) → sum where date >= $begin AND date < $fromDate
            $prev = \DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->where('journal_entries.is_posted', true)
                ->where('journal_entries.entry_date', '>=', $this->begin)
                ->where('journal_entries.entry_date', '<', $this->fromDate)
                ->selectRaw('COALESCE(SUM(debit_amount), 0) as d, COALESCE(SUM(credit_amount), 0) as c')
                ->first();
            $pdeb = (float)$prev->d; $pcre = (float)$prev->c; $pbal = $pdeb - $pcre;

            // curr: from $fromDate to $toDate (inclusive)
            $curr = \DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->where('journal_entries.is_posted', true)
                ->where('journal_entries.entry_date', '>=', $this->fromDate)
                ->where('journal_entries.entry_date', '<=', $this->toDate)
                ->selectRaw('COALESCE(SUM(debit_amount), 0) as d, COALESCE(SUM(credit_amount), 0) as c')
                ->first();
            $cdeb = (float)$curr->d; $ccre = (float)$curr->c; $cbal = $cdeb - $ccre;

            // tot: from $begin (exclusive) to $toDate (inclusive)
            $tot = \DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->where('journal_entries.is_posted', true)
                ->where('journal_entries.entry_date', '>=', $this->begin)
                ->where('journal_entries.entry_date', '<=', $this->toDate)
                ->selectRaw('COALESCE(SUM(debit_amount), 0) as d, COALESCE(SUM(credit_amount), 0) as c')
                ->first();
            $tdeb = (float)$tot->d; $tcre = (float)$tot->c; $tbal = $tdeb - $tcre;

            return compact('pdeb', 'pcre', 'pbal', 'cdeb', 'ccre', 'cbal', 'tdeb', 'tcre', 'tbal');
        }
    };

    // Grand totals
    $gt_pdeb = 0; $gt_pcre = 0; $gt_cdeb = 0; $gt_ccre = 0; $gt_tdeb = 0; $gt_tcre = 0;
    $gt_pbal = 0; $gt_cbal = 0; $gt_tbal = 0;

    // Build the hierarchical tree
    $classes = \DB::table('chart_class')->orderBy('cid')->get();
    $tree = [];

    foreach ($classes as $class) {
        $types = \DB::table('chart_types')
            ->where('class_id', $class->cid)
            ->where(function ($q) { $q->where('parent', -1)->orWhereNull('parent'); })
            ->orderBy('id')
            ->get();

        $classNode = [
            'class' => $class,
            'types' => [],
            'gt_pdeb' => 0, 'gt_pcre' => 0, 'gt_cdeb' => 0, 'gt_ccre' => 0, 'gt_tdeb' => 0, 'gt_tcre' => 0,
            'gt_pbal' => 0, 'gt_cbal' => 0, 'gt_tbal' => 0,
        ];

        $recursiveProcessTypes = function ($typeId) use ($helper, $dimension, $dimension2, $noZero, $balanceOnly, $groupTotalOnly, &$recursiveProcessTypes, &$gt_pdeb, &$gt_pcre, &$gt_cdeb, &$gt_ccre, &$gt_tdeb, &$gt_tcre, &$gt_pbal, &$gt_cbal, &$gt_tbal) {
            $type = \DB::table('chart_types')->where('id', $typeId)->first();
            if (!$type) return ['accounts' => [], 'sub_types' => [], 'total_pdeb' => 0, 'total_pcre' => 0, 'total_cdeb' => 0, 'total_ccre' => 0, 'total_tdeb' => 0, 'total_tcre' => 0, 'total_pbal' => 0, 'total_cbal' => 0, 'total_tbal' => 0];

            $accounts = \DB::table('accounts')
                ->where('account_category', $type->id)
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            $accountsData = [];
            $tpdeb = 0; $tpcre = 0; $tcdeb = 0; $tccre = 0; $ttdeb = 0; $ttcre = 0;
            $tpbal = 0; $tcbal = 0; $ttbal = 0;

            foreach ($accounts as $acc) {
                $bals = $helper->getBalances($acc->code, $dimension, $dimension2);

                if ($noZero && !$bals['pbal'] && !$bals['cbal'] && !$bals['tbal']) continue;

                $accountsData[] = [
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'pdeb' => $bals['pdeb'], 'pcre' => $bals['pcre'], 'pbal' => $bals['pbal'],
                    'cdeb' => $bals['cdeb'], 'ccre' => $bals['ccre'], 'cbal' => $bals['cbal'],
                    'tdeb' => $bals['tdeb'], 'tcre' => $bals['tcre'], 'tbal' => $bals['tbal'],
                ];

                $tpdeb += $bals['pdeb']; $tpcre += $bals['pcre'];
                $tcdeb += $bals['cdeb']; $tccre += $bals['ccre'];
                $ttdeb += $bals['tdeb']; $ttcre += $bals['tcre'];
                $tpbal += $bals['pbal']; $tcbal += $bals['cbal']; $ttbal += $bals['tbal'];
            }

            // Sub-types recursively
            $subTypes = \DB::table('chart_types')
                ->where('parent', $type->id)
                ->orderBy('id')
                ->get();

            $subTypesData = [];
            foreach ($subTypes as $st) {
                $stData = $recursiveProcessTypes($st->id);
                $subTypesData[] = array_merge(['type' => $st], $stData);
                $tpdeb += $stData['total_pdeb']; $tpcre += $stData['total_pcre'];
                $tcdeb += $stData['total_cdeb']; $tccre += $stData['total_ccre'];
                $ttdeb += $stData['total_tdeb']; $ttcre += $stData['total_tcre'];
                $tpbal += $stData['total_pbal']; $tcbal += $stData['total_cbal']; $ttbal += $stData['total_tbal'];
            }

            // Add to grand totals
            $gt_pdeb += $tpdeb; $gt_pcre += $tpcre; $gt_cdeb += $tcdeb; $gt_ccre += $tccre;
            $gt_tdeb += $ttdeb; $gt_tcre += $ttcre; $gt_pbal += $tpbal; $gt_cbal += $tcbal; $gt_tbal += $ttbal;

            return [
                'accounts' => $accountsData,
                'sub_types' => $subTypesData,
                'total_pdeb' => $tpdeb, 'total_pcre' => $tpcre,
                'total_cdeb' => $tcdeb, 'total_ccre' => $tccre,
                'total_tdeb' => $ttdeb, 'total_tcre' => $ttcre,
                'total_pbal' => $tpbal, 'total_cbal' => $tcbal, 'total_tbal' => $ttbal,
            ];
        };

        foreach ($types as $type) {
            $typeData = $recursiveProcessTypes($type->id);
            $classNode['types'][] = array_merge(['type' => $type], $typeData);
            $classNode['gt_pdeb'] += $typeData['total_pdeb'];
            $classNode['gt_pcre'] += $typeData['total_pcre'];
            $classNode['gt_cdeb'] += $typeData['total_cdeb'];
            $classNode['gt_ccre'] += $typeData['total_ccre'];
            $classNode['gt_tdeb'] += $typeData['total_tdeb'];
            $classNode['gt_tcre'] += $typeData['total_tcre'];
            $classNode['gt_pbal'] += $typeData['total_pbal'];
            $classNode['gt_cbal'] += $typeData['total_cbal'];
            $classNode['gt_tbal'] += $typeData['total_tbal'];
        }

        $tree[] = $classNode;
    }

    // Reset grand totals properly by summing class totals
    $gt_pdeb = array_sum(array_column($tree, 'gt_pdeb'));
    $gt_pcre = array_sum(array_column($tree, 'gt_pcre'));
    $gt_cdeb = array_sum(array_column($tree, 'gt_cdeb'));
    $gt_ccre = array_sum(array_column($tree, 'gt_ccre'));
    $gt_tdeb = array_sum(array_column($tree, 'gt_tdeb'));
    $gt_tcre = array_sum(array_column($tree, 'gt_tcre'));
    $gt_pbal = array_sum(array_column($tree, 'gt_pbal'));
    $gt_cbal = array_sum(array_column($tree, 'gt_cbal'));
    $gt_tbal = array_sum(array_column($tree, 'gt_tbal'));

    return view('banking.reports.trial-balance', compact(
        'fromDate', 'toDate', 'dimension', 'dimension2', 'noZero', 'balanceOnly', 'groupTotalOnly',
        'use_dimension', 'dimensions', 'show', 'tree',
        'gt_pdeb', 'gt_pcre', 'gt_cdeb', 'gt_ccre', 'gt_tdeb', 'gt_tcre',
        'gt_pbal', 'gt_cbal', 'gt_tbal', 'begin'
    ));
})->name('banking.reports.trial-balance');
Route::match(['GET', 'POST'], '/banking/reports/balance-sheet', function () {
    $toDate = request('TransToDate', date('Y-m-d'));
    $dimension = (int) request('Dimension', 0);
    $dimension2 = (int) request('Dimension2', 0);
    $accGrp = request('AccGrp', '');
    $show = request('Show') ? true : false;

    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    // BS classes: Assets(1), Liabilities(2), Equity(3)
    $bsClasses = \DB::table('chart_class')
        ->whereIn('ctype', [1, 2, 3])
        ->where('inactive', false)
        ->orderBy('cid')
        ->get();

    // Fiscal year start for "As at" date
    $fy = \DB::table('fiscal_years')
        ->where('begin', '<=', $toDate)
        ->where('end', '>=', $toDate)
        ->first();
    $fromDate = $fy ? $fy->begin : date('Y-01-01');

    $drilldown = ($accGrp !== '');

    // Helper to get net balance for an account from fiscal year start to date
    $getAccountBalance = function ($accountCode) use ($fromDate, $toDate, $dimension, $dimension2) {
        $account = \DB::table('accounts')->where('code', $accountCode)->first();
        if (!$account) return 0;

        $result = \DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.account_id', $account->id)
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '>=', $fromDate)
            ->where('journal_entries.entry_date', '<=', $toDate)
            ->selectRaw('COALESCE(SUM(debit_amount - credit_amount), 0) as net')
            ->first();

        return (float) $result->net;
    };

    // Convert factor based on class type
    $getConvert = function ($ctype) {
        if ($ctype == 1) return 1;  // Assets
        return -1;  // Liabilities, Equity
    };

    $drilldownAccounts = [];
    $drilldownSubTypes = [];
    $drilldownTotal = 0;

    if ($drilldown) {
        // Compute drilldown data for the given AccGrp
        $accType = \DB::table('chart_types')->where('id', $accGrp)->first();
        if ($accType) {
            $accClass = \DB::table('chart_class')->where('cid', $accType->class_id)->first();
            $convert = $getConvert($accClass ? $accClass->ctype : 1);

            // Get accounts directly under this type
            $accounts = \DB::table('accounts')
                ->where('account_category', $accGrp)
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            foreach ($accounts as $acc) {
                $account = \DB::table('accounts')->where('code', $acc->code)->first();
                if (!$account) continue;

                $result = \DB::table('journal_entry_lines')
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_entry_lines.account_id', $account->id)
                    ->where('journal_entries.is_posted', true)
                    ->where('journal_entries.entry_date', '>=', $fromDate)
                    ->where('journal_entries.entry_date', '<=', $toDate)
                    ->selectRaw('COALESCE(SUM(debit_amount - credit_amount), 0) as net')
                    ->first();

                $net = (float) $result->net;
                if ($net != 0) {
                    $drilldownAccounts[] = [
                        'code' => $acc->code,
                        'name' => $acc->name,
                        'amount' => $net * $convert,
                    ];
                    $drilldownTotal += $net;
                }
            }

            // Get sub-types recursively (to compute their totals for drilldown links)
            $processSubTypes = function ($parentId) use ($fromDate, $toDate, $dimension, $dimension2, $convert, &$processSubTypes) {
                $total = 0;
                $subTypes = \DB::table('chart_types')
                    ->where('parent', $parentId)
                    ->where('inactive', false)
                    ->orderBy('id')
                    ->get();

                $result = [];
                foreach ($subTypes as $st) {
                    $stTotal = 0;

                    // Accounts under this sub-type
                    $stAccounts = \DB::table('accounts')
                        ->where('account_category', $st->id)
                        ->where('is_active', true)
                        ->orderBy('code')
                        ->get();

                    foreach ($stAccounts as $sa) {
                        $saAccount = \DB::table('accounts')->where('code', $sa->code)->first();
                        if (!$saAccount) continue;
                        $saResult = \DB::table('journal_entry_lines')
                            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                            ->where('journal_entry_lines.account_id', $saAccount->id)
                            ->where('journal_entries.is_posted', true)
                            ->where('journal_entries.entry_date', '>=', $fromDate)
                            ->where('journal_entries.entry_date', '<=', $toDate)
                            ->selectRaw('COALESCE(SUM(debit_amount - credit_amount), 0) as net')
                            ->first();
                        $stTotal += (float) $saResult->net;
                    }

                    // Sub-sub-types recursively
                    $subSubResult = $processSubTypes($st->id);
                    $stTotal += $subSubResult['total'];

                    $result[] = [
                        'type' => $st,
                        'total' => $stTotal,
                        'displayTotal' => $stTotal * $convert,
                    ];
                    $total += $stTotal;
                }

                return ['types' => $result, 'total' => $total];
            };

            $subTypesResult = $processSubTypes($accGrp);
            $drilldownSubTypes = $subTypesResult['types'];
            $drilldownTotal += $subTypesResult['total'];
        }
    }

    $classData = [];
    $totalEquity = 0;
    $totalLiabilities = 0;
    $totalAssets = 0;
    $eConvert = 1;
    $lConvert = 1;

    foreach ($bsClasses as $class) {
        $convert = $getConvert($class->ctype);

        $topTypes = \DB::table('chart_types')
            ->where('class_id', $class->cid)
            ->where('parent', '-1')
            ->where('inactive', false)
            ->orderBy('id')
            ->get();

        $classTotal = 0;
        $typeData = [];

        foreach ($topTypes as $tt) {
            $result = $displayType($tt->id, $tt->name, $convert, $drilldown);
            $netTotal = $result['total'];
            $classTotal += $netTotal;

            $typeData[] = [
                'type' => $tt,
                'total' => $netTotal,
                'displayTotal' => $netTotal * $convert,
                'rows' => $result['rows'],
            ];
        }

        $classConvertedTotal = $classTotal * $convert;
        $classData[] = [
            'class' => $class,
            'convert' => $convert,
            'total' => $classTotal,
            'displayTotal' => $classConvertedTotal,
            'types' => $typeData,
        ];

        if ($class->ctype == 3) { // Equity
            $totalEquity += $classTotal;
            $eConvert = $convert;
        }
        if ($class->ctype == 2) { // Liabilities
            $totalLiabilities += $classTotal;
            $lConvert = $convert;
        }
        if ($class->ctype == 1) { // Assets
            $totalAssets += $classTotal;
        }
    }

    // Calculate calculated return (net income)
    $calculateClose = array_sum(array_column($classData, 'displayTotal'));
    if ($lConvert == 1) $calculateClose *= -1;

    // Total Liabilities and Equities
    $totalLiabilitiesEquities = $totalLiabilities * $lConvert + $totalEquity * $eConvert + $calculateClose;

    return view('banking.reports.balance-sheet', compact(
        'toDate', 'fromDate', 'dimension', 'dimension2', 'accGrp', 'show',
        'use_dimension', 'dimensions', 'bsClasses', 'classData',
        'totalEquity', 'totalLiabilities', 'totalAssets',
        'eConvert', 'lConvert', 'calculateClose', 'totalLiabilitiesEquities',
        'drilldown', 'drilldownAccounts', 'drilldownSubTypes', 'drilldownTotal'
    ));
})->name('banking.reports.balance-sheet');
Route::match(['GET', 'POST'], '/banking/reports/profit-loss', function () {
    $fromDate = request('TransFromDate', date('Y-m-d', strtotime('last day of this month - 30 days')));
    $toDate = request('TransToDate', date('Y-m-d', strtotime('last day of this month')));
    $compare = (int) request('Compare', 0);
    $dimension = (int) request('Dimension', 0);
    $dimension2 = (int) request('Dimension2', 0);
    $accGrp = request('AccGrp', '');
    $show = request('Show') ? true : false;
    $compareTypes = ['Accumulated', 'Period Y-1', 'Budget'];
    $use_dimension = (int) (\DB::table('settings')->where('key', 'use_dimension')->value('value') ?? 0);
    $dimensions = \DB::table('dimensions')->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);

    $fy = \DB::table('fiscal_years')
        ->where('begin', '<=', $toDate)
        ->where('end', '>=', $toDate)
        ->first();

    // Determine begin/end for comparison
    if ($compare == 0 || $compare == 2) {
        $end = $toDate;
        if ($compare == 2) {
            $begin = $fromDate; // Budget uses same period
        } else {
            $begin = $fy ? $fy->begin : date('Y-01-01'); // Accumulated from FY start
        }
    } elseif ($compare == 1) {
        $begin = date('Y-m-d', strtotime($fromDate . ' -12 months'));
        $end = date('Y-m-d', strtotime($toDate . ' -12 months'));
    }

    $getConvert = function ($ctype) {
        if ($ctype == 1 || $ctype == 5 || $ctype == 6) return 1; // Assets, COGS, Expenses
        return -1; // Liabilities, Equity, Income
    };

    // Helper: get net GL balance for account in date range
    $getGlBalance = function ($accountCode, $dateFrom, $dateTo) {
        $account = \DB::table('accounts')->where('code', $accountCode)->first();
        if (!$account) return 0;
        $result = \DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.account_id', $account->id)
            ->where('journal_entries.is_posted', true)
            ->where('journal_entries.entry_date', '>=', $dateFrom)
            ->where('journal_entries.entry_date', '<=', $dateTo)
            ->selectRaw('COALESCE(SUM(debit_amount - credit_amount), 0) as net')
            ->first();
        return (float) $result->net;
    };

    // Helper: get budget amount for account in date range
    $getBudgetBalance = function ($accountCode, $dateFrom, $dateTo) {
        $result = \DB::table('gl_budget_trans')
            ->where('account', $accountCode)
            ->where('date_', '>=', $dateFrom)
            ->where('date_', '<=', $dateTo)
            ->selectRaw('COALESCE(SUM(amount), 0) as total')
            ->first();
        return (float) $result->total;
    };

    $achieve = function ($d1, $d2) {
        if ($d1 == 0 && $d2 == 0) return 0;
        elseif ($d2 == 0) return 999;
        $ret = $d1 / $d2 * 100.0;
        return min($ret, 999);
    };

    $drilldown = ($accGrp !== '');
    $plClasses = \DB::table('chart_class')
        ->where('inactive', false)
        ->orderBy('cid')
        ->get();

    $drilldownAccounts = [];
    $drilldownSubTypes = [];
    $drilldownPerTotal = 0;
    $drilldownAccTotal = 0;

    if ($drilldown) {
        $accType = \DB::table('chart_types')->where('id', $accGrp)->first();
        if ($accType) {
            $accClass = \DB::table('chart_class')->where('cid', $accType->class_id)->first();
            $convert = $getConvert($accClass ? $accClass->ctype : 4);

            // Accounts directly under this type
            $accounts = \DB::table('accounts')
                ->where('account_category', $accGrp)
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            foreach ($accounts as $acc) {
                if ($compare == 2) {
                    $perBal = $getBudgetBalance($acc->code, $fromDate, $toDate);
                    $accBal = $getBudgetBalance($acc->code, $begin, $end);
                } else {
                    $perBal = $getGlBalance($acc->code, $fromDate, $toDate);
                    $accBal = $getGlBalance($acc->code, $begin, $end);
                }
                if ($perBal == 0 && $accBal == 0) continue;
                $drilldownAccounts[] = [
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'per' => $perBal * $convert,
                    'acc' => $accBal * $convert,
                    'pct' => $achieve($perBal, $accBal),
                ];
                $drilldownPerTotal += $perBal;
                $drilldownAccTotal += $accBal;
            }

            // Sub-types recursively
            $processSubTypes = function ($parentId) use ($fromDate, $toDate, $begin, $end, $compare, $convert, $dimension, $dimension2, $getGlBalance, $getBudgetBalance, $achieve, &$processSubTypes) {
                $total = ['per' => 0, 'acc' => 0];
                $subTypes = \DB::table('chart_types')
                    ->where('parent', $parentId)
                    ->where('inactive', false)
                    ->orderBy('id')
                    ->get();
                $result = [];
                foreach ($subTypes as $st) {
                    $stPer = 0; $stAcc = 0;
                    $stAccounts = \DB::table('accounts')
                        ->where('account_category', $st->id)
                        ->where('is_active', true)
                        ->orderBy('code')
                        ->get();
                    foreach ($stAccounts as $sa) {
                        if ($compare == 2) {
                            $stPer += $getBudgetBalance($sa->code, $fromDate, $toDate);
                            $stAcc += $getBudgetBalance($sa->code, $begin, $end);
                        } else {
                            $stPer += $getGlBalance($sa->code, $fromDate, $toDate);
                            $stAcc += $getGlBalance($sa->code, $begin, $end);
                        }
                    }
                    $subSubResult = $processSubTypes($st->id);
                    $stPer += $subSubResult['total']['per'];
                    $stAcc += $subSubResult['total']['acc'];
                    $result[] = [
                        'type' => $st,
                        'per' => $stPer,
                        'acc' => $stAcc,
                        'displayPer' => $stPer * $convert,
                        'displayAcc' => $stAcc * $convert,
                        'pct' => $achieve($stPer, $stAcc),
                    ];
                    $total['per'] += $stPer;
                    $total['acc'] += $stAcc;
                }
                return ['types' => $result, 'total' => $total];
            };

            $subResult = $processSubTypes($accGrp);
            $drilldownSubTypes = $subResult['types'];
            $drilldownPerTotal += $subResult['total']['per'];
            $drilldownAccTotal += $subResult['total']['acc'];
        }
    }

    // Root level: compute all class totals
    $classData = [];
    $totalPer = 0;
    $totalAcc = 0;

    foreach ($plClasses as $class) {
        $convert = $getConvert($class->ctype);
        $topTypes = \DB::table('chart_types')
            ->where('class_id', $class->cid)
            ->where('parent', '-1')
            ->where('inactive', false)
            ->orderBy('id')
            ->get();

        $classPer = 0;
        $classAcc = 0;
        $typeData = [];

        $processType = function ($typeId, $convert) use ($fromDate, $toDate, $begin, $end, $compare, $getGlBalance, $getBudgetBalance, $achieve, &$processType) {
            $acctPer = 0; $acctAcc = 0;
            $accounts = \DB::table('accounts')
                ->where('account_category', $typeId)
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            $rows = [];
            foreach ($accounts as $acc) {
                if ($compare == 2) {
                    $netPer = $getBudgetBalance($acc->code, $fromDate, $toDate);
                    $netAcc = $getBudgetBalance($acc->code, $begin, $end);
                } else {
                    $netPer = $getGlBalance($acc->code, $fromDate, $toDate);
                    $netAcc = $getGlBalance($acc->code, $begin, $end);
                }
                $acctPer += $netPer;
                $acctAcc += $netAcc;
            }

            $typeTotal = 0;
            $subTypes = \DB::table('chart_types')
                ->where('parent', $typeId)
                ->where('inactive', false)
                ->orderBy('id')
                ->get();

            foreach ($subTypes as $st) {
                $stResult = $processType($st->id, $convert);
                $acctPer += $stResult['per'];
                $acctAcc += $stResult['acc'];
                $typeTotal += $stResult['total'];
                foreach ($stResult['rows'] as $r) {
                    $rows[] = $r;
                }
            }

            return ['per' => $acctPer, 'acc' => $acctAcc, 'total' => $acctPer + $acctAcc, 'rows' => $rows];
        };

        foreach ($topTypes as $tt) {
            $result = $processType($tt->id, $convert);
            $classPer += $result['per'];
            $classAcc += $result['acc'];
            $typeData[] = [
                'type' => $tt,
                'per' => $result['per'],
                'acc' => $result['acc'],
                'displayPer' => $result['per'] * $convert,
                'displayAcc' => $result['acc'] * $convert,
                'pct' => $achieve($result['per'], $result['acc']),
            ];
        }

        $classData[] = [
            'class' => $class,
            'per' => $classPer,
            'acc' => $classAcc,
            'displayPer' => $classPer * $convert,
            'displayAcc' => $classAcc * $convert,
            'pct' => $achieve($classPer, $classAcc),
            'types' => $typeData,
        ];

        $totalPer += $classPer;
        $totalAcc += $classAcc;
    }

    return view('banking.reports.profit-loss', compact(
        'fromDate', 'toDate', 'compare', 'compareTypes', 'dimension', 'dimension2',
        'accGrp', 'show', 'use_dimension', 'dimensions', 'plClasses', 'classData',
        'drilldown', 'drilldownAccounts', 'drilldownSubTypes',
        'drilldownPerTotal', 'drilldownAccTotal',
        'totalPer', 'totalAcc', 'begin', 'end', 'achieve'
    ));
})->name('banking.reports.profit-loss');
Route::get('/banking/reports/banking', function () { return view('banking.reports.banking'); })->name('banking.reports.banking');
Route::get('/banking/reports/gl', function () { return view('banking.reports.gl'); })->name('banking.reports.gl');
Route::match(['GET', 'POST'], '/banking/accounts', function () {
    $account_types = [
        0 => 'Bank',
        3 => 'Credit Card',
        4 => 'Merchant',
        5 => 'Cash',
        6 => 'Online',
    ];

    $currencies = [
        'USD' => 'US Dollars',
        'EUR' => 'Euro',
        'GBP' => 'Pounds',
        'CAD' => 'CA Dollars',
        'AUD' => 'Australian Dollars',
        'JPY' => 'Yen',
        'CHF' => 'Swiss Francs',
        'SEK' => 'Swedish Kroner',
        'NOK' => 'Norwegian Kroner',
    ];

    $selected_id = request('selected_id', request('bank_id', ''));
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $input_error = 0;
        if (empty(request('bank_account_name'))) {
            $input_error = 1;
            $error = 'The bank account name cannot be empty.';
        }

        if (!$input_error && !$selected_id) {
            $exists = BankAccount::where('account_code', request('account_code'))->exists();
            $account = \DB::table('accounts')->where('code', request('account_code'))->first();
            $used = $account ? \DB::table('journal_entry_lines')->where('account_id', $account->id)->exists() : false;
            if ($exists || $used) {
                $input_error = 1;
                $error = 'The GL account selected is already in use or has transactions. Select another empty GL account.';
            }
        }

        if (!$input_error) {
            $data = [
                'account_code' => request('account_code', ''),
                'account_type' => request('account_type', 0),
                'bank_account_name' => request('bank_account_name', ''),
                'bank_name' => request('bank_name', ''),
                'bank_account_number' => request('bank_account_number', ''),
                'bank_address' => request('bank_address', ''),
                'bank_curr_code' => request('BankAccountCurrency', 'USD'),
                'dflt_curr_act' => request()->boolean('dflt_curr_act'),
                'bank_charge_act' => request('bank_charge_act', ''),
            ];

            if ($selected_id) {
                $ba = BankAccount::find($selected_id);
                if ($ba && \DB::table('bank_trans')->where('bank_act', $selected_id)->exists()) {
                    unset($data['account_type'], $data['bank_curr_code'], $data['account_code']);
                }
                BankAccount::where('id', $selected_id)->update($data);
                $message = 'Bank account has been updated';
            } else {
                $ba = BankAccount::create($data);
                $selected_id = $ba->id;
                $message = 'New bank account has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode === 'Delete') {
        $cancel_delete = 0;
        if (\DB::table('bank_trans')->where('bank_act', $selected_id)->exists() ||
            \DB::table('gl_trans')->where('account', request('account_code'))->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this bank account because transactions have been created using this account.';
        }
        if (\DB::table('sales_pos')->where('pos_account', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this bank account because POS definitions have been created using this account.';
        }
        if (!$cancel_delete) {
            BankAccount::where('id', $selected_id)->delete();
            $message = 'Selected bank account has been deleted';
        }
        $selected_id = ''; $Mode = 'RESET';
    }

    if ($Mode === 'RESET') {
        $selected_id = '';
    }

    if (request('show_inactive')) {
        session(['bankacct_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('bankacct_show_inactive');
    }

    $show_inactive = session('bankacct_show_inactive', false);
    $accounts = BankAccount::with('glAccount')
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('bank_account_name')
        ->get();

    if ($Mode === 'Edit' && $selected_id) {
        $edit_acct = BankAccount::find($selected_id);
        if (!$edit_acct) { $edit_acct = null; $selected_id = ''; }
    } else {
        $edit_acct = null;
    }

    if ($selected_id && !$edit_acct) {
        $edit_acct = BankAccount::find($selected_id);
    }

    if (request('toggle_inactive')) {
        $ba = BankAccount::find(request('toggle_inactive'));
        if ($ba) { $ba->update(['inactive' => !$ba->inactive]); }
        return redirect()->route('banking.accounts', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    $glAccounts = Account::where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $allAccounts = Account::orderBy('code')->get(['code', 'name']);
    $selected_tab = request('_tabs_sel', 'settings');

    return view('banking.accounts', compact(
        'account_types', 'currencies', 'accounts', 'edit_acct', 'selected_id',
        'message', 'error', 'show_inactive', 'glAccounts', 'allAccounts', 'selected_tab'
    ));
})->name('banking.accounts');
Route::match(['GET', 'POST'], '/banking/quick-entries', function () {
    $qe_types = [
        '2' => 'Bank Deposit',
        '1' => 'Bank Payment',
        '3' => 'Journal Entry',
        '4' => 'Supplier Invoice/Credit',
    ];

    $quick_actions = [
        '='  => 'Remainder',
        'a'  => 'Amount',
        'a+' => 'Amount, increase base',
        'a-' => 'Amount, reduce base',
        '%'  => '% amount of base',
        '%+' => '% amount of base, increase base',
        '%-' => '% amount of base, reduce base',
        'T'  => 'Taxes added',
        'T+' => 'Taxes added, increase base',
        'T-' => 'Taxes added, reduce base',
        't'  => 'Taxes included',
        't+' => 'Taxes included, increase base',
        't-' => 'Taxes included, reduce base',
    ];

    $selected_id = request('selected_id', '');
    $selected_id2 = request('selected_id2', '');
    $Mode = request('Mode', '');
    $Mode2 = request('Mode2', '');
    $message = '';
    $error = '';

    // Quick Entry CRUD
    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        if (empty(request('description'))) {
            $error = 'The Quick Entry description cannot be empty.';
        } else {
            $data = [
                'description' => request('description', ''),
                'type' => request('type', '3'),
                'usage' => request('usage', ''),
                'base_amount' => (float)str_replace(',', '', request('base_amount', '0')),
                'base_desc' => request('base_desc', ''),
                'bal_type' => request()->boolean('bal_type'),
            ];

            if ($data['bal_type'] && $data['type'] != '3') {
                $error = 'You can only use Balance Based together with Journal Entries.';
            } elseif (!$data['bal_type'] && empty($data['base_desc'])) {
                $error = 'The base amount description cannot be empty.';
            } else {
                if ($selected_id) {
                    QuickEntry::where('id', $selected_id)->update($data);
                    $message = 'Selected quick entry has been updated';
                } else {
                    $qe = QuickEntry::create($data);
                    $selected_id = $qe->id;
                    $message = 'New quick entry has been added';
                }
                $Mode = 'RESET';
            }
        }
    }

    if ($Mode === 'Delete') {
        if (QuickEntryLine::where('qid', $selected_id)->exists()) {
            $error = 'The Quick Entry has Quick Entry Lines. Cannot be deleted.';
        } else {
            QuickEntry::where('id', $selected_id)->delete();
            $message = 'Selected quick entry has been deleted';
            $selected_id = '';
            $Mode = 'RESET';
        }
    }

    // Quick Entry Lines CRUD
    if (in_array($Mode2, ['ADD_ITEM2', 'UPDATE_ITEM2'])) {
        $qid = request('selected_id', '');
        if (empty(request('dest_id'))) {
            $error = 'You must select GL account.';
        } elseif ($selected_id2) {
            QuickEntryLine::where('id', $selected_id2)->update([
                'qid' => $qid,
                'action' => request('actn', ''),
                'dest_id' => request('dest_id', ''),
                'amount' => (float)str_replace(',', '', request('amount', '0')),
                'memo' => request('memo', ''),
                'dimension_id' => request('dimension_id', 0),
                'dimension2_id' => request('dimension2_id', 0),
            ]);
            $message = 'Selected quick entry line has been updated';
        } else {
            QuickEntryLine::create([
                'qid' => $qid,
                'action' => request('actn', ''),
                'dest_id' => request('dest_id', ''),
                'amount' => (float)str_replace(',', '', request('amount', '0')),
                'memo' => request('memo', ''),
                'dimension_id' => request('dimension_id', 0),
                'dimension2_id' => request('dimension2_id', 0),
            ]);
            $message = 'New quick entry line has been added';
        }
        $Mode2 = 'RESET2';
    }

    if ($Mode2 === 'BDel') {
        QuickEntryLine::where('id', $selected_id2)->delete();
        $message = 'Selected quick entry line has been deleted';
        $Mode2 = 'RESET2';
    }

    if ($Mode === 'RESET') {
        $selected_id = '';
    }

    if ($Mode2 === 'RESET2') {
        $selected_id2 = '';
    }

    $entries = QuickEntry::orderBy('description')->get();

    if ($Mode === 'Edit' && $selected_id) {
        $edit_entry = QuickEntry::find($selected_id);
        if (!$edit_entry) { $edit_entry = null; $selected_id = ''; }
    } elseif ($selected_id) {
        $edit_entry = QuickEntry::find($selected_id);
    } else {
        $edit_entry = null;
    }

    $lines = $selected_id ? QuickEntryLine::with('glAccount', 'taxType')->where('qid', $selected_id)->orderBy('id')->get() : collect();

    // Build posted data from edit_entry or defaults
    $post = [
        'description' => $edit_entry->description ?? old('description', ''),
        'type' => $edit_entry->type ?? old('type', '3'),
        'usage' => $edit_entry->usage ?? old('usage', ''),
        'base_desc' => $edit_entry->base_desc ?? old('base_desc', 'Base Amount'),
        'base_amount' => $edit_entry->base_amount ?? old('base_amount', 0),
        'bal_type' => $edit_entry->bal_type ?? old('bal_type', false),
    ];
    if ($Mode === 'RESET' || !$edit_entry) {
        $post = ['description' => '', 'type' => '3', 'usage' => '', 'base_desc' => 'Base Amount', 'base_amount' => 0, 'bal_type' => false];
    }

    // Line editor data
    $line_post = [
        'dest_id' => '',
        'actn' => '',
        'amount' => 0,
        'memo' => '',
        'dimension_id' => 0,
        'dimension2_id' => 0,
    ];
    if ($selected_id2 && $Mode2 === 'BEd') {
        $line = QuickEntryLine::find($selected_id2);
        if ($line) {
            $line_post = [
                'dest_id' => $line->dest_id,
                'actn' => $line->action,
                'amount' => $line->amount,
                'memo' => $line->memo,
                'dimension_id' => $line->dimension_id ?? 0,
                'dimension2_id' => $line->dimension2_id ?? 0,
            ];
        }
    }

    $glAccounts = Account::where('is_active', true)->orderBy('code')->get(['code', 'name']);
    $allAccounts = Account::orderBy('code')->get(['code', 'name']);
    $taxTypes = TaxType::orderBy('name')->get(['id', 'name', 'rate']);

    return view('banking.quick-entries', compact(
        'qe_types', 'quick_actions', 'entries', 'edit_entry', 'selected_id', 'selected_id2',
        'message', 'error', 'lines', 'post', 'line_post', 'glAccounts', 'allAccounts', 'taxTypes', 'Mode', 'Mode2'
    ));
})->name('banking.quick-entries');
Route::match(['GET', 'POST'], '/banking/account-tags', function () {
    $tag_type = 1; // TAG_ACCOUNT
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    // CRUD
    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $name = request('name', '');
        $description = request('description', '');

        if (strlen($name) == 0) {
            $error = 'The tag name cannot be empty.';
        } else {
            if ($selected_id) {
                Tag::where('id', $selected_id)->update(['name' => $name, 'description' => $description]);
                $message = 'Selected tag settings have been updated';
            } else {
                Tag::create(['type' => $tag_type, 'name' => $name, 'description' => $description]);
                $message = 'New tag has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode === 'Delete') {
        if ($selected_id) {
            $associated = \DB::table('tag_associations')->where('tag_id', $selected_id)->exists();
            if ($associated) {
                $error = 'Cannot delete this tag because records have been created referring to it.';
            } else {
                Tag::where('id', $selected_id)->delete();
                $message = 'Selected tag has been deleted';
            }
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $t = Tag::find(request('toggle_inactive'));
        if ($t) { $t->update(['inactive' => !$t->inactive]); }
        return redirect()->route('banking.account-tags', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['account_tags_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('account_tags_show_inactive');
    }

    $show_inactive = session('account_tags_show_inactive', false);
    $tags = Tag::where('type', $tag_type)
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('name')
        ->get();

    if ($Mode === 'Edit' && $selected_id) {
        $edit_tag = Tag::find($selected_id);
        if (!$edit_tag) { $edit_tag = null; $selected_id = ''; }
    } else {
        $edit_tag = null;
    }

    $name = $edit_tag->name ?? '';
    $description = $edit_tag->description ?? '';

    return view('banking.account-tags', compact('tags', 'edit_tag', 'selected_id', 'message', 'error', 'show_inactive', 'name', 'description'));
})->name('banking.account-tags');
Route::match(['GET', 'POST'], '/banking/currencies', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    // CRUD
    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $input_error = 0;
        if (strlen(request('Abbreviation', '')) == 0) { $input_error = 1; $error = 'The currency abbreviation must be entered.'; }
        elseif (strlen(request('CurrencyName', '')) == 0) { $input_error = 1; $error = 'The currency name must be entered.'; }
        elseif (strlen(request('Symbol', '')) == 0) { $input_error = 1; $error = 'The currency symbol must be entered.'; }
        elseif (strlen(request('hundreds_name', '')) == 0) { $input_error = 1; $error = 'The hundredths name must be entered.'; }

        if (!$input_error) {
            $data = [
                'curr_symbol' => request('Symbol', ''),
                'currency' => request('CurrencyName', ''),
                'country' => request('country', ''),
                'hundreds_name' => request('hundreds_name', ''),
                'auto_update' => request()->boolean('auto_update'),
            ];

            if ($selected_id) {
                Currency::where('curr_abrev', $selected_id)->update($data);
                $message = 'Selected currency settings has been updated';
            } else {
                $data['curr_abrev'] = request('Abbreviation', '');
                Currency::create($data);
                $message = 'New currency has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode === 'Delete') {
        $home = \App\Models\Setting::getSetting('company.currency', auth()->user()->company_id ?? 1, 'USD');
        $cancel_delete = 0;
        if ($selected_id == $home) {
            $cancel_delete = 1;
            $error = 'Cannot delete this currency, because the company preferences uses this currency.';
        }
        if (!$cancel_delete && \DB::table('debtors_master')->where('curr_code', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this currency, because customer accounts have been created referring to this currency.';
        }
        if (!$cancel_delete && \DB::table('suppliers')->where('curr_code', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this currency, because supplier accounts have been created referring to this currency.';
        }
        if (!$cancel_delete && \DB::table('bank_accounts')->where('bank_curr_code', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this currency, because there are bank accounts that use this currency.';
        }
        if (!$cancel_delete) {
            Currency::where('curr_abrev', $selected_id)->delete();
            $message = 'Selected currency has been deleted';
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $c = Currency::find(request('toggle_inactive'));
        if ($c) { $c->update(['inactive' => !$c->inactive]); }
        return redirect()->route('banking.currencies', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['currencies_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('currencies_show_inactive');
    }

    $show_inactive = session('currencies_show_inactive', false);
    $currencies = Currency::when(!$show_inactive, fn($q) => $q->where('inactive', false))->orderBy('currency')->get();
    $home = \App\Models\Setting::getSetting('company.currency', auth()->user()->company_id ?? 1, 'USD');

    if ($Mode === 'Edit' && $selected_id) {
        $edit_curr = Currency::find($selected_id);
        if (!$edit_curr) { $edit_curr = null; $selected_id = ''; }
    } else {
        $edit_curr = null;
    }

    $abbreviation = $edit_curr->curr_abrev ?? '';
    $symbol = $edit_curr->curr_symbol ?? '';
    $currency_name = $edit_curr->currency ?? '';
    $country = $edit_curr->country ?? '';
    $hundreds_name = $edit_curr->hundreds_name ?? '';
    $auto_update = $edit_curr->auto_update ?? true;

    return view('banking.currencies', compact(
        'currencies', 'edit_curr', 'selected_id', 'message', 'error',
        'show_inactive', 'home', 'abbreviation', 'symbol', 'currency_name',
        'country', 'hundreds_name', 'auto_update'
    ));
})->name('banking.currencies');
Route::match(['GET', 'POST'], '/banking/exchange-rates', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $company_id = auth()->check() ? auth()->user()->company_id : 1;
    $home = \App\Models\Setting::getSetting('company.currency', $company_id, 'USD');

    // Persist selected currency across requests
    if (!session()->has('exchange_rates_curr')) {
        session(['exchange_rates_curr' => $home]);
    }
    $curr_abrev = request('curr_abrev', session('exchange_rates_curr'));
    session(['exchange_rates_curr' => $curr_abrev]);

    // Exchange rate CRUD
    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $input_error = 0;
        $date_ = request('date_', '');
        if (!strtotime($date_)) {
            $input_error = 1;
            $error = 'The entered date is invalid.';
        }
        $buy_rate = (float) str_replace(',', '', request('BuyRate', '0'));
        if ($buy_rate <= 0) {
            $input_error = 1;
            $error = 'The exchange rate cannot be zero or a negative number.';
        }
        if (!$input_error && !$selected_id) {
            $exists = \DB::table('exchange_rates')->where('curr_code', $curr_abrev)->where('date_', $date_)->exists();
            if ($exists) {
                $input_error = 1;
                $error = 'The exchange rate for the date is already there.';
            }
        }
        if (!$input_error) {
            if ($selected_id) {
                \DB::table('exchange_rates')->where('id', $selected_id)->update([
                    'rate_buy' => $buy_rate,
                    'rate_sell' => $buy_rate,
                ]);
                $message = 'Exchange rate has been updated';
            } else {
                \DB::table('exchange_rates')->insert([
                    'curr_code' => $curr_abrev,
                    'date_' => $date_,
                    'rate_buy' => $buy_rate,
                    'rate_sell' => $buy_rate,
                ]);
                $message = 'Exchange rate has been added';
            }
            $selected_id = '';
        }
    }

    if ($Mode === 'Delete') {
        if ($selected_id) {
            \DB::table('exchange_rates')->where('id', $selected_id)->delete();
            $message = 'Exchange rate has been deleted';
        }
        $selected_id = '';
    }

    // Handle "Get" button for auto-fetching rate
    if (request('get_rate')) {
        // Placeholder - would call external API in production
    }

    // Load edit data
    if ($Mode === 'Edit' && $selected_id) {
        $edit_rate = \DB::table('exchange_rates')->where('id', $selected_id)->first();
        if (!$edit_rate) { $selected_id = ''; }
    } else {
        $edit_rate = null;
    }

    $rates = \DB::table('exchange_rates')
        ->where('curr_code', $curr_abrev)
        ->orderBy('date_', 'desc')
        ->get();

    $currencies = \App\Models\Currency::where('inactive', false)->orderBy('currency')->get();
    $is_home = ($curr_abrev == $home);

    $edit_date = $edit_rate ? $edit_rate->date_ : date('Y-m-d');
    $edit_buyrate = $edit_rate ? $edit_rate->rate_buy : '';

    return view('banking.exchange-rates', compact(
        'currencies', 'curr_abrev', 'rates', 'edit_rate', 'selected_id',
        'message', 'error', 'home', 'is_home', 'edit_date', 'edit_buyrate'
    ));
})->name('banking.exchange-rates');
Route::match(['GET', 'POST'], '/banking/gl-accounts', function () {
    $message = '';
    $error = '';

    // Account groups (mapped from our account_type string to FA-style names)
    $account_groups = [
        'asset'     => 'Assets',
        'liability' => 'Liabilities',
        'equity'    => 'Equity',
        'revenue'   => 'Income',
        'expense'   => 'Expense',
    ];

    // Handle account selection
    $selected_account = request('selected_account', session('gl_selected_account', ''));
    session(['gl_selected_account' => $selected_account]);

    // Show inactive
    if (request('show_inactive')) {
        session(['gl_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('gl_show_inactive');
    }
    $show_inactive = session('gl_show_inactive', false);

    // Tags for this account
    $selected_tags = [];

    // Handle add/update
    if (request()->has('add') || request()->has('update')) {
        $input_error = 0;
        $code = request('account_code', '');
        $code2 = request('account_code2', '');
        $name = request('account_name', '');
        $group = request('account_type', '');

        if (strlen(trim($code)) == 0) {
            $input_error = 1;
            $error = 'The account code must be entered.';
        } elseif (strlen(trim($name)) == 0) {
            $input_error = 1;
            $error = 'The account name cannot be empty.';
        }

        if (!$input_error) {
            $data = [
                'name' => $name,
                'account_code2' => $code2,
                'account_type' => $group,
                'is_active' => !request()->boolean('inactive'),
            ];

            if ($selected_account) {
                Account::where('code', $selected_account)->update($data);
                // Update tags
                $tag_ids = request('account_tags', []);
                \DB::table('tag_associations')->where('record_id', $selected_account)->delete();
                foreach ((array)$tag_ids as $tid) {
                    if ($tid) \DB::table('tag_associations')->insert(['record_id' => $selected_account, 'tag_id' => $tid]);
                }
                $message = 'Account data has been updated.';
            } else {
                $data['code'] = $code;
                try {
                    Account::create($data);
                    // Add tags
                    $tag_ids = request('account_tags', []);
                    foreach ((array)$tag_ids as $tid) {
                        if ($tid) \DB::table('tag_associations')->insert(['record_id' => $code, 'tag_id' => $tid]);
                    }
                    $message = 'New account has been added.';
                    $selected_account = $code;
                    session(['gl_selected_account' => $selected_account]);
                } catch (\Exception $e) {
                    $error = 'Account not added, possible duplicate Account Code.';
                }
            }
        }
    }

    // Handle delete
    if (request()->has('delete')) {
        $cancel_delete = 0;
        if (\DB::table('gl_trans')->where('account', $selected_account)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account because transactions have been created using this account.';
        }
        if (!$cancel_delete && \DB::table('bank_accounts')->where('account_code', $selected_account)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account because it is used by a bank account.';
        }
        if (!$cancel_delete && \DB::table('tax_types')->where('sales_gl_code', $selected_account)->orWhere('purchasing_gl_code', $selected_account)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account because it is used by one or more Taxes.';
        }
        if (!$cancel_delete && \DB::table('quick_entry_lines')->where('dest_id', $selected_account)->whereRaw("UPPER(LEFT(action, 1)) <> 'T'")->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account because it is used by one or more Quick Entry Lines.';
        }
        if (!$cancel_delete) {
            Account::where('code', $selected_account)->delete();
            \DB::table('tag_associations')->where('record_id', $selected_account)->delete();
            $message = 'Selected account has been deleted';
            $selected_account = '';
            session(['gl_selected_account' => '']);
        }
    }

    // Load edit data
    if ($selected_account) {
        $edit_acct = Account::where('code', $selected_account)->first();
        if (!$edit_acct) { $edit_acct = null; $selected_account = ''; session(['gl_selected_account' => '']); }
    } else {
        $edit_acct = null;
    }

    // Load tags for this account
    if ($selected_account) {
        $selected_tags = \DB::table('tag_associations')->where('record_id', $selected_account)->pluck('tag_id')->toArray();
    } else {
        $selected_tags = [];
    }

    $accounts = Account::when(!$show_inactive, fn($q) => $q->where('is_active', true))->orderBy('code')->get(['code', 'name', 'is_active', 'account_type']);
    $tags = \App\Models\Tag::where('type', 1)->where('inactive', false)->orderBy('name')->get();

    $edit_code = $edit_acct->code ?? '';
    $edit_code2 = $edit_acct->account_code2 ?? '';
    $edit_name = $edit_acct->name ?? '';
    $edit_type = $edit_acct->account_type ?? '';
    $edit_inactive = $edit_acct ? !$edit_acct->is_active : false;

    return view('banking.gl-accounts', compact(
        'accounts', 'account_groups', 'tags', 'selected_tags',
        'selected_account', 'edit_acct', 'message', 'error',
        'show_inactive', 'edit_code', 'edit_code2', 'edit_name',
        'edit_type', 'edit_inactive'
    ));
})->name('banking.gl-accounts');
Route::match(['GET', 'POST'], '/banking/gl-groups', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    // Filter by class (cid from GET/POST)
    $filter_cid = request('cid', '');

    // Show inactive
    if (request('show_inactive')) {
        session(['gl_groups_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('gl_groups_show_inactive');
    }
    $show_inactive = session('gl_groups_show_inactive', false);

    // CRUD
    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $input_error = 0;
        $id = request('id', '');
        $name = request('name', '');
        $class_id = request('class_id', '');
        $parent = request('parent', '-1');

        if (strlen(trim($id)) == 0) {
            $input_error = 1;
            $error = 'The account group id cannot be empty.';
        } elseif (strlen(trim($name)) == 0) {
            $input_error = 1;
            $error = 'The account group name cannot be empty.';
        } elseif ($parent === $id) {
            $input_error = 1;
            $error = 'You cannot set an account group to be a subgroup of itself.';
        }

        if (!$input_error) {
            // Check duplicate id
            $existing = \DB::table('chart_types')->where('id', $id)->first();
            if ($existing && $existing->id !== $selected_id) {
                $input_error = 1;
                $error = 'This account group id is already in use.';
            }
        }

        if (!$input_error) {
            if ($selected_id) {
                $old_id = request('old_id', $selected_id);
                if ($old_id !== $id) {
                    // Update children and accounts that reference the old id
                    \DB::table('chart_types')->where('parent', $old_id)->update(['parent' => $id]);
                    \DB::table('accounts')->where('account_type', $old_id)->update(['account_type' => $id]);
                }
                \DB::table('chart_types')->where('id', $selected_id)->update([
                    'id' => $id,
                    'name' => $name,
                    'class_id' => $class_id,
                    'parent' => $parent ?: '-1',
                ]);
                $message = 'Selected account type has been updated';
            } else {
                \DB::table('chart_types')->insert([
                    'id' => $id,
                    'name' => $name,
                    'class_id' => $class_id,
                    'parent' => $parent ?: '-1',
                ]);
                $message = 'New account type has been added';
            }
            $Mode = 'RESET';
        }
    }

    if ($Mode === 'Delete') {
        $cancel_delete = 0;
        if (\DB::table('accounts')->where('account_type', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account group because GL accounts have been created referring to it.';
        }
        if (!$cancel_delete && \DB::table('chart_types')->where('parent', $selected_id)->where('id', '!=', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account group because GL account groups have been created referring to it.';
        }
        if (!$cancel_delete) {
            \DB::table('chart_types')->where('id', $selected_id)->delete();
            $message = 'Selected account group has been deleted';
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $t = \DB::table('chart_types')->where('id', request('toggle_inactive'))->first();
        if ($t) { \DB::table('chart_types')->where('id', $t->id)->update(['inactive' => !$t->inactive]); }
        return redirect()->route('banking.gl-groups', array_filter(['show_inactive' => $show_inactive ? '1' : null, 'cid' => $filter_cid ?: null]));
    }

    if ($Mode === 'RESET') {
        $selected_id = '';
    }

    // Load edit data
    if ($Mode === 'Edit' && $selected_id) {
        $edit_group = \DB::table('chart_types')->where('id', $selected_id)->first();
        if (!$edit_group) { $selected_id = ''; }
    } else {
        $edit_group = null;
    }

    // Query groups
    $groups_query = \DB::table('chart_types');
    if ($filter_cid) {
        $groups_query->where('class_id', $filter_cid);
    }
    if (!$show_inactive) {
        $groups_query->where('inactive', false);
    }
    $groups = $groups_query->orderBy('class_id')->orderBy('id')->get();

    $classes = \DB::table('chart_class')->where('inactive', false)->orderBy('cid')->get();

    $edit_id_value = $edit_group->id ?? '';
    $edit_name = $edit_group->name ?? '';
    $edit_parent = $edit_group->parent ?? '-1';
    $edit_class_id = $edit_group->class_id ?? ($filter_cid ?: '');
    $old_id = $edit_group->id ?? '';

    return view('banking.gl-groups', compact(
        'groups', 'classes', 'edit_group', 'selected_id', 'message', 'error',
        'show_inactive', 'filter_cid', 'edit_id_value', 'edit_name',
        'edit_parent', 'edit_class_id', 'old_id'
    ));
})->name('banking.gl-groups');
Route::match(['GET', 'POST'], '/banking/gl-classes', function () {
    $selected_id = request('selected_id', '');
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    // Show inactive
    if (request('show_inactive')) {
        session(['gl_classes_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('gl_classes_show_inactive');
    }
    $show_inactive = session('gl_classes_show_inactive', false);

    // Class types mapping (FA's $class_types array: ctype => label)
    $class_types = [
        1 => 'Assets',
        2 => 'Liabilities',
        3 => 'Equity',
        4 => 'Income',
        5 => 'Cost of Goods Sold',
        6 => 'Expense',
    ];

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $input_error = 0;
        $id = request('id', '');
        $name = request('name', '');
        $ctype = request('ctype', '');

        if (strlen(trim($id)) == 0) {
            $input_error = 1;
            $error = 'The account class ID cannot be empty.';
        } elseif (strlen(trim($name)) == 0) {
            $input_error = 1;
            $error = 'The account class name cannot be empty.';
        }

        if (!$input_error) {
            if ($selected_id) {
                \DB::table('chart_class')->where('cid', $selected_id)->update([
                    'class_name' => $name,
                    'ctype' => $ctype,
                ]);
                $message = 'Selected account class settings has been updated';
            } else {
                // Check duplicate cid
                if (\DB::table('chart_class')->where('cid', $id)->exists()) {
                    $error = 'This account class ID is already in use.';
                    $input_error = 1;
                } else {
                    \DB::table('chart_class')->insert([
                        'cid' => $id,
                        'class_name' => $name,
                        'ctype' => $ctype,
                        'inactive' => false,
                    ]);
                    $message = 'New account class has been added';
                    $Mode = 'RESET';
                }
            }
        }
    }

    if ($Mode === 'Delete') {
        $cancel_delete = 0;
        if (\DB::table('chart_types')->where('class_id', $selected_id)->exists()) {
            $cancel_delete = 1;
            $error = 'Cannot delete this account class because GL account types have been created referring to it.';
        }
        if (!$cancel_delete) {
            \DB::table('chart_class')->where('cid', $selected_id)->delete();
            $message = 'Selected account class has been deleted';
        }
        $Mode = 'RESET';
    }

    if (request('toggle_inactive')) {
        $t = \DB::table('chart_class')->where('cid', request('toggle_inactive'))->first();
        if ($t) { \DB::table('chart_class')->where('cid', $t->cid)->update(['inactive' => !$t->inactive]); }
        return redirect()->route('banking.gl-classes', array_filter(['show_inactive' => $show_inactive ? '1' : null]));
    }

    if ($Mode === 'RESET') {
        $selected_id = '';
    }

    if ($Mode === 'Edit' && $selected_id) {
        $edit_class = \DB::table('chart_class')->where('cid', $selected_id)->first();
        if (!$edit_class) { $selected_id = ''; }
    } else {
        $edit_class = null;
    }

    $classes = \DB::table('chart_class')
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->orderBy('cid')
        ->get();

    $edit_id_value = $edit_class->cid ?? '';
    $edit_name = $edit_class->class_name ?? '';
    $edit_ctype = $edit_class->ctype ?? '';

    return view('banking.gl-classes', compact(
        'classes', 'class_types', 'edit_class', 'selected_id', 'message', 'error',
        'show_inactive', 'edit_id_value', 'edit_name', 'edit_ctype'
    ));
})->name('banking.gl-classes');
Route::get('/banking/closing', function () { return view('banking.closing'); })->name('banking.closing');
Route::get('/banking/revaluation', function () { return view('banking.revaluation'); })->name('banking.revaluation');
Route::match(['GET', 'POST'], '/banking/journal/import', function () {
    $message = '';
    $error = '';
    $type = request('type', '0');
    $sep = request('sep', ',');
    $bank_account = request('bank_account', '');

    // Import types matching FA constants: ST_JOURNAL=0, ST_BANKDEPOSIT=1, ST_BANKPAYMENT=2
    $import_types = [
        '0' => 'Journal Entry',
        '1' => 'Deposit',
        '2' => 'Payment',
    ];

    if (request('import')) {
        if (request()->hasFile('imp') && request()->file('imp')->isValid()) {
            $file = request()->file('imp');
            $filename = $file->getRealPath();
            $type = request('type', '0');
            $bank_account = request('bank_account', '');
            $sep = request('sep', ',');

            $fp = @fopen($filename, 'r');
            if (!$fp) {
                $error = 'Error opening import file.';
            } else {
                \DB::beginTransaction();
                $curEntryId = null;
                $curDate = null;
                $line = 0;
                $entryCount = 0;
                $errorFlag = false;
                $errCnt = 0;
                $refCount = 0;
                $entry = null;

                while (($data = fgetcsv($fp, 4096, $sep)) !== false) {
                    $line++;
                    if ($line == 1) continue; // skip header
                    if (count($data) < 2) continue; // skip blank lines

                    $entryid = trim($data[0] ?? '');
                    $date = trim($data[1] ?? '');
                    $reference = trim($data[2] ?? '');
                    $code = trim($data[3] ?? '');
                    $dim1_ref = trim($data[4] ?? '');
                    $dim2_ref = trim($data[5] ?? '');
                    $amt = trim($data[6] ?? '');
                    $memo = trim($data[7] ?? '');

                    // New entry group
                    if ($entryid !== $curEntryId) {
                        // Validate date format (YYYY-MM-DD or similar)
                        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                            $error = "Error: date '$date' not properly formatted (line $line in import file '{$file->getClientOriginalName()}')";
                            $errorFlag = true;
                        }

                        // Assign default reference if not specified
                        if ($reference == '') {
                            if ($date == $curDate) {
                                $refCount++;
                            } else {
                                $refCount = 1;
                            }
                            // Format: MM/DD-N
                            $dateParts = date_parse($date);
                            $reference = sprintf('%02d/%02d-%d', $dateParts['month'], $dateParts['day'], $refCount);
                        }

                        // Check reference uniqueness
                        $refExists = \DB::table('journal_entries')->where('entry_number', $reference)->exists();
                        if ($refExists) {
                            $error = "Error: reference '$reference' is already in use (line $line in import file '{$file->getClientOriginalName()}')";
                            $errorFlag = true;
                        }

                        // Write previous entry if it exists and no error
                        if ($curEntryId !== null && !$errorFlag) {
                            $entryId = \DB::table('journal_entries')->insertGetId([
                                'company_id' => session('company_id', 1),
                                'entry_number' => $entry->reference,
                                'entry_date' => $entry->tran_date,
                                'description' => $entry->memo_,
                                'total_debit' => $entry->total_debit,
                                'total_credit' => $entry->total_credit,
                                'is_posted' => true,
                                'posted_at' => now(),
                                'posted_by' => auth()->id() ?? 1,
                                'created_by' => auth()->id() ?? 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            foreach ($entry->gl_items as $item) {
                                \DB::table('journal_entry_lines')->insert([
                                    'journal_entry_id' => $entryId,
                                    'account_id' => $item['account_id'],
                                    'description' => $item['memo'],
                                    'debit_amount' => $item['debit'],
                                    'credit_amount' => $item['credit'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }

                            // For bank deposits/payments, also write to bank_trans
                            if ($type == '1' || $type == '2') {
                                \DB::table('bank_trans')->insert([
                                    'ref_type' => $type == '1' ? 'deposit' : 'payment',
                                    'reference_id' => $entryId,
                                    'bank_account_id' => $bank_account ?: null,
                                    'trans_date' => $entry->tran_date,
                                    'reference' => $entry->reference,
                                    'amount' => $type == '1' ? $entry->total_amount : -$entry->total_amount,
                                    'memo' => $entry->memo_,
                                    'created_by' => auth()->id() ?? 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }

                            $entryCount++;
                        }

                        if ($errorFlag) {
                            $errCnt++;
                        }
                        $errorFlag = false;

                        // Initialize new entry
                        $entry = new \stdClass();
                        $entry->trans_type = $type;
                        $entry->tran_date = $date;
                        $entry->reference = $reference;
                        $entry->memo_ = 'Imported via \'Import Multiple Journal Entries\' plugin';
                        $entry->gl_items = [];
                        $entry->total_debit = 0;
                        $entry->total_credit = 0;
                        $entry->total_amount = 0;

                        $curEntryId = $entryid;
                        $curDate = $date;
                    }

                    if ($entryid == '') {
                        $error = "Error: entryid not specified (line $line in import file '{$file->getClientOriginalName()}')";
                        $errorFlag = true;
                    }

                    // Check that the account code exists
                    $account = \DB::table('accounts')->where('code', $code)->first();
                    if (!$account) {
                        $error = "Error: Could not find account code '$code' (line $line in import file '{$file->getClientOriginalName()}')";
                        $errorFlag = true;
                        continue;
                    }

                    // Check dimension 1
                    $dim1_id = null;
                    if ($dim1_ref != '') {
                        $dim1 = \DB::table('dimensions')->where('code', $dim1_ref)->first();
                        if (!$dim1) {
                            $error = "Error: Could not find dimension with reference '$dim1_ref' (line $line in import file '{$file->getClientOriginalName()}')";
                            $errorFlag = true;
                            continue;
                        }
                        $dim1_id = $dim1->id;
                    }

                    // Check dimension 2
                    $dim2_id = null;
                    if ($dim2_ref != '') {
                        $dim2 = \DB::table('dimensions')->where('code', $dim2_ref)->first();
                        if (!$dim2) {
                            $error = "Error: Could not find dimension with reference '$dim2_ref' (line $line in import file '{$file->getClientOriginalName()}')";
                            $errorFlag = true;
                            continue;
                        }
                        $dim2_id = $dim2->id;
                    }

                    if ($type == '1') { // Deposit: negate amount
                        $amt = -abs((float)$amt);
                    } else {
                        $amt = (float)$amt;
                    }

                    if (!$errorFlag) {
                        if ($amt >= 0) {
                            $debit = $amt;
                            $credit = 0;
                        } else {
                            $debit = 0;
                            $credit = -$amt;
                        }
                        $entry->gl_items[] = [
                            'account_id' => $account->id,
                            'account_code' => $code,
                            'dim1_id' => $dim1_id,
                            'dim2_id' => $dim2_id,
                            'amount' => $amt,
                            'debit' => $debit,
                            'credit' => $credit,
                            'memo' => $memo,
                        ];
                        $entry->total_debit += $debit;
                        $entry->total_credit += $credit;
                        if ($type == '1' || $type == '2') {
                            $entry->total_amount += $amt;
                        }
                    }
                }

                // Process final entries
                if (!$errorFlag && $curEntryId !== null) {
                    $entryId = \DB::table('journal_entries')->insertGetId([
                        'company_id' => session('company_id', 1),
                        'entry_number' => $entry->reference,
                        'entry_date' => $entry->tran_date,
                        'description' => $entry->memo_,
                        'total_debit' => $entry->total_debit,
                        'total_credit' => $entry->total_credit,
                        'is_posted' => true,
                        'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    foreach ($entry->gl_items as $item) {
                        \DB::table('journal_entry_lines')->insert([
                            'journal_entry_id' => $entryId,
                            'account_id' => $item['account_id'],
                            'description' => $item['memo'],
                            'debit_amount' => $item['debit'],
                            'credit_amount' => $item['credit'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    if ($type == '1' || $type == '2') {
                        \DB::table('bank_trans')->insert([
                            'ref_type' => $type == '1' ? 'deposit' : 'payment',
                            'reference_id' => $entryId,
                            'bank_account_id' => $bank_account ?: null,
                            'trans_date' => $entry->tran_date,
                            'reference' => $entry->reference,
                            'amount' => $type == '1' ? $entry->total_amount : -$entry->total_amount,
                            'memo' => $entry->memo_,
                            'created_by' => auth()->id() ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $entryCount++;
                } else if ($errorFlag) {
                    $errCnt++;
                }

                @fclose($fp);

                if (!$errCnt) {
                    \DB::commit();
                    $typeString = $import_types[$type] ?? 'entries';
                    if ($entryCount > 0) {
                        $message = "$entryCount $typeString have been imported.";
                    } else {
                        $error = 'Import file contained no ' . strtolower($typeString) . '.';
                    }
                } else {
                    \DB::rollBack();
                    // $error already set
                }
            }
        } else {
            $error = 'No import file selected';
        }
    }

    $bank_accounts = \DB::table('bank_accounts')->where('inactive', false)->orderBy('bank_account_name')->get();

    return view('banking.journal.import', compact(
        'message', 'error', 'import_types', 'type', 'sep', 'bank_account', 'bank_accounts'
    ));
})->name('banking.journal.import');

// Manufacturing Module
Route::match(['GET', 'POST'], '/manufacturing/work-order-entry', [ManufacturingController::class, 'workOrderEntry'])->name('manufacturing.work-order-entry');

Route::match(['GET', 'POST'], '/manufacturing/outstanding-work-orders', [ManufacturingController::class, 'outstandingOrders'])->name('manufacturing.outstanding-work-orders');

Route::match(['GET', 'POST'], '/manufacturing/costed-bom-inquiry', [ManufacturingController::class, 'costedBomInquiry'])->name('manufacturing.costed-bom-inquiry');

Route::match(['GET', 'POST'], '/manufacturing/item-where-used', [ManufacturingController::class, 'whereUsed'])->name('manufacturing.item-where-used');

Route::match(['GET', 'POST'], '/manufacturing/work-order-inquiry', [ManufacturingController::class, 'workOrderInquiry'])->name('manufacturing.work-order-inquiry');

Route::get('/manufacturing/reports', function () {
    return view('manufacturing.reports');
})->name('manufacturing.reports');

Route::match(['GET', 'POST'], '/manufacturing/bom', [ManufacturingController::class, 'bomEdit'])->name('manufacturing.bom.index');

Route::match(['GET', 'POST'], '/manufacturing/work-centers', [ManufacturingController::class, 'workCentres'])->name('manufacturing.work-centers');

// Accounting Module
Route::get('/accounting/accounts', function () {
    return view('accounting.accounts.index');
})->name('accounting.accounts.index');

Route::get('/accounting/journal', function () {
    return view('accounting.journal.index');
})->name('accounting.journal.index');

Route::get('/accounting/trial-balance', function () {
    return view('accounting.trial-balance');
})->name('accounting.trial-balance');

Route::get('/accounting/balance-sheet', function () {
    return view('accounting.balance-sheet');
})->name('accounting.balance-sheet');

Route::get('/accounting/profit-loss', function () {
    return view('accounting.profit-loss');
})->name('accounting.profit-loss');

// HR Module
Route::match(['GET','POST'], '/hr/attendance', [App\Http\Controllers\HrController::class, 'attendance'])->name('hr.attendance');
Route::match(['GET','POST'], '/hr/payslips', [App\Http\Controllers\HrController::class, 'payslipEntry'])->name('hr.payslips');
Route::match(['GET','POST'], '/hr/document-expiration', [App\Http\Controllers\HrController::class, 'documentExpiration'])->name('hr.document-expiration');
Route::match(['GET','POST'], '/hr/payment-advice', [App\Http\Controllers\HrController::class, 'paymentAdvice'])->name('hr.payment-advice');
Route::match(['GET','POST'], '/hr/employee-advances', [App\Http\Controllers\HrController::class, 'employeeAdvance'])->name('hr.employee-advances');
Route::match(['GET','POST'], '/hr/timesheet', [App\Http\Controllers\HrController::class, 'timesheet'])->name('hr.timesheet');
Route::match(['GET','POST'], '/hr/inquiries/transactions', [App\Http\Controllers\HrController::class, 'employeeTransactionInquiry'])->name('hr.inquiries.transactions');
Route::match(['GET','POST'], '/hr/inquiries/documents', function (\Illuminate\Http\Request $request) {
    $request->merge(['View' => 'yes']);
    return app(\App\Http\Controllers\HrController::class)->documentExpiration($request);
})->name('hr.inquiries.documents');
Route::get('/hr/reports/employee', function () { return view('hr.reports.employee'); })->name('hr.reports.employee');
Route::match(['GET', 'POST'], '/hr/employees', [\App\Http\Controllers\HrController::class, 'employees'])->name('hr.employees.index');
Route::match(['GET', 'POST'], '/hr/document-types', [\App\Http\Controllers\HrController::class, 'documentTypes'])->name('hr.document-types');
Route::match(['GET', 'POST'], '/hr/departments', [\App\Http\Controllers\HrController::class, 'departments'])->name('hr.departments');
Route::match(['GET', 'POST'], '/hr/overtime', [\App\Http\Controllers\HrController::class, 'overtimeRates'])->name('hr.overtime');
Route::match(['GET', 'POST'], '/hr/leave-types', [\App\Http\Controllers\HrController::class, 'leaveTypes'])->name('hr.leave-types');
Route::match(['GET', 'POST'], '/hr/default-settings', [\App\Http\Controllers\HrController::class, 'defaultSettings'])->name('hr.default-settings');
Route::match(['GET', 'POST'], '/hr/job-positions', [\App\Http\Controllers\HrController::class, 'jobPositions'])->name('hr.job-positions');
Route::match(['GET', 'POST'], '/hr/grades', [\App\Http\Controllers\HrController::class, 'grades'])->name('hr.grades');
Route::match(['GET', 'POST'], '/hr/pay-elements', [\App\Http\Controllers\HrController::class, 'payElements'])->name('hr.pay-elements');
Route::match(['GET', 'POST'], '/hr/pay-elements-allocation', [\App\Http\Controllers\HrController::class, 'payElementsAllocation'])->name('hr.pay-elements-allocation');
Route::match(['GET', 'POST'], '/hr/salary-structure', [\App\Http\Controllers\HrController::class, 'salaryStructure'])->name('hr.salary-structure');

// Reports Module
Route::get('/reports/sales', function () {
    return view('reports.sales');
})->name('reports.sales');

Route::get('/reports/purchases', function () {
    return view('reports.purchases');
})->name('reports.purchases');

Route::get('/reports/inventory', function () {
    return view('reports.inventory');
})->name('reports.inventory');

Route::get('/reports/accounting', function () {
    return view('reports.accounting');
})->name('reports.accounting');

// Setup Module
Route::match(['get', 'post'], '/setup/company', function (\Illuminate\Http\Request $req) {
    $salesTypes = [];
    try { $salesTypes = \App\Models\SalesType::all(); } catch (\Exception $e) {}

    $companyId = 1;
    $company = \App\Models\Company::find($companyId);

    if ($req->isMethod('POST') && $req->has('update')) {
        $fields = [
            'coy_name', 'postal_address', 'domicile', 'phone', 'fax', 'email',
            'bcc_email', 'coy_no', 'gst_no', 'curr_default',
            'f_year', 'tax_prd', 'tax_last', 'base_sales', 'add_pct', 'round_to',
            'use_dimension', 'login_tout', 'max_days_in_docs',
        ];
        $booleans = [
            'time_zone', 'company_logo_report', 'barcodes_on_stock', 'ref_no_auto_increase',
            'dim_on_recurrent_invoice', 'long_description_invoice', 'company_logo_on_views',
            'alternative_tax_include_on_docs', 'suppress_tax_rates', 'auto_curr_reval',
            'use_manufacturing', 'use_fixed_assets',
            'shortname_name_in_list', 'print_dialog_direct',
            'no_item_list', 'no_customer_list', 'no_supplier_list',
        ];

        foreach ($fields as $field) {
            $value = $req->input($field, '');
            \App\Models\Setting::setSetting($field, $value, $companyId, 'string', 'company');
        }
        foreach ($booleans as $field) {
            $value = $req->has($field) ? '1' : '0';
            \App\Models\Setting::setSetting($field, $value, $companyId, 'boolean', 'company');
        }

        if ($company) {
            $company->update([
                'name' => $req->input('coy_name', $company->name),
                'address' => $req->input('postal_address', $company->address),
                'phone' => $req->input('phone', $company->phone),
                'email' => $req->input('email', $company->email),
                'tax_id' => $req->input('gst_no', $company->tax_id),
                'registration_number' => $req->input('coy_no', $company->registration_number),
            ]);
        }

        return redirect()->route('setup.company')->with('success', 'Company settings updated successfully.');
    }

    $settings = \App\Models\Setting::where('company_id', $companyId)->where('category', 'company')->get()->keyBy('key');

    return view('setup.company', compact('salesTypes', 'settings', 'company'));
})->name('setup.company');
Route::match(['get', 'post'], '/setup/users', function () {
    $roles = []; $users = []; $editUser = null;
    try {
        $roles = \Spatie\Permission\Models\Role::all();
        $users = \App\Models\User::with('roles')->get();

        $saved = session()->get('success');
        $err = session()->get('error');

        if (request()->isMethod('post')) {
            if (request()->has('delete')) {
                $user = \App\Models\User::find(request('delete'));
                if ($user && $user->id != auth()->id()) {
                    $user->delete();
                    session()->flash('success', 'User has been deleted.');
                } else {
                    session()->flash('error', 'Cannot delete this user.');
                }
                return redirect()->route('setup.users');
            }

            if (request()->has('inactive')) {
                foreach (request('inactive') as $uid => $val) {
                    $u = \App\Models\User::find($uid);
                    if ($u) { $u->is_active = false; $u->save(); }
                }
                // mark unchecked as active
                \App\Models\User::whereNotIn('id', array_keys(request('inactive', [])))->update(['is_active' => true]);
                session()->flash('success', 'User statuses have been updated.');
                return redirect()->route('setup.users');
            }

            $isNew = !request('selected_id');
            $userId = request('user_id');
            $password = request('password');
            $realName = request('real_name');
            $phone = request('phone');
            $email = request('email');
            $roleId = request('role_id');

            if (!$isNew && $password && strlen($password) < 4) {
                session()->flash('error', 'The password must be at least 4 characters long.');
                return redirect()->route('setup.users', ['edit' => request('selected_id')]);
            }

            if ($isNew) {
                $user = \App\Models\User::create([
                    'name' => $realName,
                    'email' => $email,
                    'password' => bcrypt($password),
                    'phone' => $phone,
                    'is_active' => true,
                ]);
                if ($roleId) {
                    $role = \Spatie\Permission\Models\Role::findById($roleId);
                    $user->assignRole($role);
                }
                session()->flash('success', 'A new user has been added.');
            } else {
                $user = \App\Models\User::find(request('selected_id'));
                if ($user) {
                    $user->name = $realName;
                    $user->email = $email;
                    $user->phone = $phone;
                    if ($password) {
                        $user->password = bcrypt($password);
                    }
                    $user->save();
                    if ($roleId) {
                        $role = \Spatie\Permission\Models\Role::findById($roleId);
                        $user->syncRoles([$role->id]);
                    }
                    session()->flash('success', 'The selected user has been updated.');
                }
                return redirect()->route('setup.users');
            }
            return redirect()->route('setup.users');
        }

        if (request()->has('edit')) {
            $editUser = \App\Models\User::with('roles')->find(request('edit'));
        }
    } catch (\Exception $e) {}
    return view('setup.users', compact('roles', 'users', 'editUser'));
})->name('setup.users');
Route::match(['get', 'post'], '/setup/access', function () {
    $allRoles = $selectedRole = $rolePermissions = null;
    $selectedRoleId = null;
    $selectedRoleDesc = '';
    $selectedRoleInactive = '0';

    $permissionGroups = [
        'Sales' => ['sales-orders.view', 'sales-orders.create', 'sales-orders.edit', 'sales-orders.delete', 'sales-orders.confirm', 'sales-orders.cancel'],
        'Purchasing' => ['purchase-orders.view', 'purchase-orders.create', 'purchase-orders.edit', 'purchase-orders.delete', 'purchase-orders.receive', 'purchase-orders.cancel'],
        'Inventory' => ['items.view', 'items.create', 'items.edit', 'items.delete', 'warehouses.view', 'warehouses.create', 'warehouses.edit', 'warehouses.delete', 'inventory.adjust', 'inventory.transfer', 'inventory.view-transactions'],
        'Manufacturing' => ['bom.view', 'bom.create', 'bom.edit', 'bom.delete', 'production-orders.view', 'production-orders.create', 'production-orders.edit', 'production-orders.delete', 'production-orders.release', 'production-orders.start', 'production-orders.complete', 'work-centers.view', 'work-centers.create', 'work-centers.edit', 'work-centers.delete'],
        'Fixed Assets' => ['fixed-assets.view', 'fixed-assets.create', 'fixed-assets.edit', 'fixed-assets.delete', 'fixed-assets.depreciate', 'fixed-assets.dispose'],
        'Dimensions' => ['dimensions.view', 'dimensions.create', 'dimensions.edit', 'dimensions.delete', 'dimensions.assign', 'dimensions.report'],
        'Human Resources' => ['employees.view', 'employees.create', 'employees.edit', 'employees.delete', 'employees.terminate', 'payrolls.view', 'payrolls.create', 'payrolls.process', 'payrolls.pay'],
        'Accounting' => ['accounts.view', 'accounts.create', 'accounts.edit', 'accounts.delete', 'journal-entries.view', 'journal-entries.create', 'journal-entries.edit', 'journal-entries.delete', 'journal-entries.post'],
        'Customer/Supplier' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete'],
        'Reports' => ['reports.sales', 'reports.purchases', 'reports.inventory', 'reports.manufacturing', 'reports.assets', 'reports.hr', 'reports.financial'],
        'Setup' => ['settings.view', 'settings.edit', 'system.backup', 'system.restore'],
    ];

    try {
        $allRoles = \Spatie\Permission\Models\Role::all();

        if (request()->isMethod('post')) {
            $action = request('action');

            if ($action === 'delete') {
                $role = \Spatie\Permission\Models\Role::find(request('role_id'));
                if ($role) { $role->delete(); }
                session()->flash('success', 'Security role has been deleted.');
                return redirect()->route('setup.access');
            }

            if ($action === 'clone' && request('role_id')) {
                $src = \Spatie\Permission\Models\Role::find(request('role_id'));
                if ($src) {
                    $name = request('name') ?: $src->name . ' (clone)';
                    $clone = \Spatie\Permission\Models\Role::create(['name' => $name]);
                    $perms = $src->permissions->pluck('name')->toArray();
                    // Ensure all referenced permissions exist
                    foreach ($perms as $p) {
                        try { \Spatie\Permission\Models\Permission::findOrCreate($p); } catch (\Exception $e) {}
                    }
                    $clone->givePermissionTo($perms);
                    session()->flash('success', 'Role has been cloned.');
                }
                return redirect()->route('setup.access');
            }

            $name = request('name');
            $description = request('description');
            $inactive = request('inactive', '0');
            $roleId = request('role_id');

            if (!$name) {
                session()->flash('error', 'Role name cannot be empty.');
                return redirect()->route('setup.access');
            }

            $selectedPerms = [];
            foreach(array_keys(request()->all()) as $key) {
                if (strpos($key, 'perm_') === 0) {
                    $selectedPerms[] = substr($key, 5);
                }
            }

            // Ensure all referenced permissions exist
            foreach ($selectedPerms as $p) {
                try { \Spatie\Permission\Models\Permission::findOrCreate($p); } catch (\Exception $e) {}
            }

            if ($action === 'insert' || !$roleId) {
                $role = \Spatie\Permission\Models\Role::create(['name' => $name]);
                $role->givePermissionTo($selectedPerms);
                session()->flash('success', 'New security role has been added.');
            } else {
                $role = \Spatie\Permission\Models\Role::find($roleId);
                if ($role) {
                    $role->name = $name;
                    $role->save();
                    $role->syncPermissions($selectedPerms);
                    session()->flash('success', 'Security role has been updated.');
                }
            }
            return redirect()->route('setup.access');
        }

        $selectedRoleId = request('role') ?: request('role_id');
        if ($selectedRoleId) {
            $selectedRole = \Spatie\Permission\Models\Role::with('permissions')->find($selectedRoleId);
            if ($selectedRole) {
                $selectedRoleDesc = $selectedRole->name;
                $rolePermissions = $selectedRole->permissions;
            }
        }
        if (!$rolePermissions) { $rolePermissions = collect(); }
    } catch (\Exception $e) {}

    return view('setup.access', compact('allRoles', 'selectedRole', 'selectedRoleId', 'selectedRoleDesc', 'selectedRoleInactive', 'rolePermissions', 'permissionGroups'));
})->name('setup.access');
Route::match(['GET', 'POST'], '/setup/display', function () {
    if (request()->isMethod('POST')) {
        $prefs = request()->except(['_token', '_method', 'setprefs']);
        session(['display_prefs' => $prefs]);
        if (auth()->check()) {
            auth()->user()->update(['preferences' => $prefs]);
        }
        return redirect()->route('setup.display')->with('success', 'Display preferences updated successfully.');
    }
    $prefs = session('display_prefs', auth()->check() ? (auth()->user()->preferences ?? []) : []);
    return view('setup.display', compact('prefs'));
})->name('setup.display');
Route::match(['GET', 'POST'], '/setup/transaction-references', function () {
    $systypes_array = [
        0 => 'Journal Entry',
        1 => 'Bank Payment',
        2 => 'Bank Deposit',
        4 => 'Funds Transfer',
        10 => 'Sales Invoice',
        11 => 'Customer Credit Note',
        12 => 'Customer Payment',
        13 => 'Delivery Note',
        16 => 'Location Transfer',
        17 => 'Inventory Adjustment',
        18 => 'Purchase Order',
        20 => 'Supplier Invoice',
        21 => 'Supplier Credit Note',
        22 => 'Supplier Payment',
        25 => 'Purchase Order Delivery',
        26 => 'Work Order',
        28 => 'Work Order Issue',
        29 => 'Work Order Production',
        30 => 'Sales Order',
        32 => 'Sales Quotation',
        35 => 'Cost Update',
        40 => 'Dimension',
    ];

    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if (request('show_inactive')) {
            session(['reflines_show_inactive' => true]);
        } else {
            session()->forget('reflines_show_inactive');
        }

        if ($action === 'add' || $action === 'update') {
            $data = request()->validate([
                'trans_type' => 'required|integer',
                'prefix' => 'required|string|max:30',
                'pattern' => 'required|string|max:60',
                'description' => 'nullable|string|max:255',
                'default' => 'nullable|boolean',
            ]);

            $data['default'] = request()->has('default');

            if ($action === 'add') {
                $refline = Refline::create($data);
                if ($data['default']) {
                    Refline::where('trans_type', $data['trans_type'])
                        ->where('id', '!=', $refline->id)
                        ->update(['default' => false]);
                }
            } else {
                $refline = Refline::findOrFail($id);
                $refline->update($data);
                if ($data['default']) {
                    Refline::where('trans_type', $data['trans_type'])
                        ->where('id', '!=', $refline->id)
                        ->update(['default' => false]);
                }
            }

            return redirect()->route('setup.transaction-references')->with('success', 'Reference line saved.');
        }

        if ($action === 'delete') {
            $refline = Refline::findOrFail($id);
            if ($refline->default) {
                return redirect()->route('setup.transaction-references')->with('error', 'Default reference line cannot be deleted.');
            }

            if (request('confirm_delete')) {
                $refline->delete();
                return redirect()->route('setup.transaction-references')->with('success', 'Reference line deleted.');
            }

            return redirect()->route('setup.transaction-references')
                ->with('confirm_delete', $id)
                ->with('delete_message', 'Are you sure you want to delete this reference line?');
        }

        if ($action === 'toggle_inactive') {
            $refline = Refline::findOrFail($id);
            $refline->update(['inactive' => !$refline->inactive]);
            return redirect()->route('setup.transaction-references')->with('success', 'Reference line status updated.');
        }

        if ($action === 'edit') {
            session(['edit_refline_id' => $id]);
            return redirect()->route('setup.transaction-references');
        }

        if ($action === 'cancel') {
            session()->forget('edit_refline_id');
            return redirect()->route('setup.transaction-references');
        }
    }

    $show_inactive = session('reflines_show_inactive', false);
    $reflines = $show_inactive ? Refline::all() : Refline::where('inactive', false)->get();
    $edit_id = session('edit_refline_id');
    $edit_refline = $edit_id ? Refline::find($edit_id) : null;
    $confirm_delete = session('confirm_delete');

    session()->forget(['edit_refline_id', 'confirm_delete', 'delete_message']);

    return view('setup.transaction-references', compact(
        'systypes_array', 'reflines', 'edit_refline', 'show_inactive', 'confirm_delete'
    ));
})->name('setup.transaction-references');
Route::match(['GET', 'POST'], '/setup/taxes', function () {
    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if ($action === 'toggle_show_inactive') {
            if (request('show_inactive')) {
                session(['taxes_show_inactive' => true]);
            } else {
                session()->forget('taxes_show_inactive');
            }
            return redirect()->route('setup.taxes');
        }

        if ($action === 'toggle_inactive') {
            $tax = \App\Models\TaxType::findOrFail($id);
            $tax->update(['inactive' => !$tax->inactive]);
            return redirect()->route('setup.taxes')->with('success', 'Tax type status updated.');
        }

        if (in_array($action, ['add', 'update'])) {
            $data = request()->validate([
                'name' => 'required|string|max:60',
                'rate' => 'required|numeric|min:0',
                'sales_gl_code' => 'nullable|string|max:15',
                'purchasing_gl_code' => 'nullable|string|max:15',
            ]);

            if ($action === 'add') {
                \App\Models\TaxType::create($data);
                return redirect()->route('setup.taxes')->with('success', 'New tax type has been added');
            } else {
                \App\Models\TaxType::where('id', $id)->update($data);
                return redirect()->route('setup.taxes')->with('success', 'Selected tax type has been updated');
            }
        }

        if ($action === 'delete') {
            $tax = \App\Models\TaxType::findOrFail($id);
            $used = \DB::table('tax_group_items')->where('tax_type_id', $id)->exists();
            if ($used) {
                return redirect()->route('setup.taxes')->with('error', 'Cannot delete this tax type because tax groups been created referring to it.');
            }
            $tax->delete();
            return redirect()->route('setup.taxes')->with('success', 'Selected tax type has been deleted');
        }

        if ($action === 'edit') {
            session(['edit_tax_id' => $id]);
            return redirect()->route('setup.taxes');
        }

        if ($action === 'cancel') {
            session()->forget('edit_tax_id');
            return redirect()->route('setup.taxes');
        }
    }

    $edit_id = session('edit_tax_id');
    $edit_tax = $edit_id ? \App\Models\TaxType::find($edit_id) : null;
    session()->forget('edit_tax_id');

    $show_inactive = session('taxes_show_inactive', false);
    $tax_types = $show_inactive
        ? \App\Models\TaxType::with(['salesGlAccount', 'purchasingGlAccount'])->get()
        : \App\Models\TaxType::with(['salesGlAccount', 'purchasingGlAccount'])->where('inactive', false)->get();

    $gl_accounts = \App\Models\Account::orderBy('code')->get(['code', 'name']);

    return view('setup.taxes', compact('tax_types', 'edit_tax', 'gl_accounts', 'show_inactive'));
})->name('setup.taxes');
Route::match(['GET', 'POST'], '/setup/tax-groups', function () {
    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if ($action === 'toggle_show_inactive') {
            if (request('show_inactive')) {
                session(['taxgroups_show_inactive' => true]);
            } else {
                session()->forget('taxgroups_show_inactive');
            }
            return redirect()->route('setup.tax-groups');
        }

        if ($action === 'toggle_inactive') {
            $group = \App\Models\TaxGroup::findOrFail($id);
            $group->update(['inactive' => !$group->inactive]);
            return redirect()->route('setup.tax-groups')->with('success', 'Tax group status updated.');
        }

        if (in_array($action, ['add', 'update'])) {
            request()->validate(['name' => 'required|string|max:60']);

            $taxes = [];
            $tax_shippings = [];
            foreach (request()->all() as $key => $value) {
                if (str_starts_with($key, 'tax_type_id_')) {
                    $tax_type_id = (int) substr($key, strrpos($key, '_') + 1);
                    if ($value) {
                        $taxes[] = $tax_type_id;
                        $tax_shippings[] = request()->has('tax_shipping_'.$tax_type_id) ? 1 : 0;
                    }
                }
            }

            if ($action === 'add') {
                $group = \App\Models\TaxGroup::create(['name' => request('name')]);
                foreach ($taxes as $i => $tax_type_id) {
                    \App\Models\TaxGroupItem::create([
                        'tax_group_id' => $group->id,
                        'tax_type_id' => $tax_type_id,
                        'tax_shipping' => (bool) $tax_shippings[$i],
                    ]);
                }
                return redirect()->route('setup.tax-groups')->with('success', 'New tax group has been added');
            } else {
                $group = \App\Models\TaxGroup::findOrFail($id);
                $group->update(['name' => request('name')]);
                $group->items()->delete();
                foreach ($taxes as $i => $tax_type_id) {
                    \App\Models\TaxGroupItem::create([
                        'tax_group_id' => $group->id,
                        'tax_type_id' => $tax_type_id,
                        'tax_shipping' => (bool) $tax_shippings[$i],
                    ]);
                }
                return redirect()->route('setup.tax-groups')->with('success', 'Selected tax group has been updated');
            }
        }

        if ($action === 'delete') {
            $group = \App\Models\TaxGroup::findOrFail($id);
            $used = \DB::table('cust_branch')->where('tax_group_id', $id)->exists()
                 || \DB::table('suppliers')->where('tax_group_id', $id)->exists();
            if ($used) {
                return redirect()->route('setup.tax-groups')->with('error', 'Cannot delete this tax group because customer branches/suppliers been created referring to it.');
            }
            $group->items()->delete();
            $group->delete();
            return redirect()->route('setup.tax-groups')->with('success', 'Selected tax group has been deleted');
        }

        if ($action === 'edit') {
            session(['edit_taxgroup_id' => $id]);
            return redirect()->route('setup.tax-groups');
        }

        if ($action === 'cancel') {
            session()->forget('edit_taxgroup_id');
            return redirect()->route('setup.tax-groups');
        }
    }

    $edit_id = session('edit_taxgroup_id');
    $edit_group = $edit_id ? \App\Models\TaxGroup::with('items')->find($edit_id) : null;
    session()->forget('edit_taxgroup_id');

    $show_inactive = session('taxgroups_show_inactive', false);
    $groups = $show_inactive
        ? \App\Models\TaxGroup::all()
        : \App\Models\TaxGroup::where('inactive', false)->get();

    $tax_types = \App\Models\TaxType::with('salesGlAccount')->where('inactive', false)->get();

    if ($edit_group) {
        $group_tax_ids = $edit_group->items->pluck('tax_type_id')->toArray();
        $group_shipping_ids = $edit_group->items->where('tax_shipping', true)->pluck('tax_type_id')->toArray();
    } else {
        $group_tax_ids = [];
        $group_shipping_ids = [];
    }

    return view('setup.tax-groups', compact(
        'groups', 'edit_group', 'tax_types', 'show_inactive',
        'group_tax_ids', 'group_shipping_ids'
    ));
})->name('setup.tax-groups');
Route::match(['GET', 'POST'], '/setup/item-tax-types', function () {
    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if ($action === 'toggle_show_inactive') {
            if (request('show_inactive')) {
                session(['itemtaxtypes_show_inactive' => true]);
            } else {
                session()->forget('itemtaxtypes_show_inactive');
            }
            return redirect()->route('setup.item-tax-types');
        }

        if ($action === 'toggle_inactive') {
            $type = \App\Models\ItemTaxType::findOrFail($id);
            $type->update(['inactive' => !$type->inactive]);
            return redirect()->route('setup.item-tax-types')->with('success', 'Item tax type status updated.');
        }

        if (in_array($action, ['add', 'update'])) {
            request()->validate(['name' => 'required|string|max:60']);
            $exempt = request()->boolean('exempt');
            $exempt_from = [];
            if (!$exempt) {
                foreach (\App\Models\TaxType::where('inactive', false)->get() as $tax) {
                    if (request()->boolean('ExemptTax'.$tax->id)) {
                        $exempt_from[] = $tax->id;
                    }
                }
            }

            if ($action === 'add') {
                $type = \App\Models\ItemTaxType::create(['name' => request('name'), 'exempt' => $exempt]);
                foreach ($exempt_from as $tax_type_id) {
                    \App\Models\ItemTaxTypeExemption::create([
                        'item_tax_type_id' => $type->id,
                        'tax_type_id' => $tax_type_id,
                    ]);
                }
                return redirect()->route('setup.item-tax-types')->with('success', 'New item tax type has been added');
            } else {
                $type = \App\Models\ItemTaxType::findOrFail($id);
                $type->update(['name' => request('name'), 'exempt' => $exempt]);
                $type->exemptions()->delete();
                foreach ($exempt_from as $tax_type_id) {
                    \App\Models\ItemTaxTypeExemption::create([
                        'item_tax_type_id' => $type->id,
                        'tax_type_id' => $tax_type_id,
                    ]);
                }
                return redirect()->route('setup.item-tax-types')->with('success', 'Selected item tax type has been updated');
            }
        }

        if ($action === 'delete') {
            $type = \App\Models\ItemTaxType::findOrFail($id);
            $used = \DB::table('stock_master')->where('tax_type_id', $id)->exists()
                 || \DB::table('stock_category')->where('dflt_tax_type', $id)->exists();
            if ($used) {
                return redirect()->route('setup.item-tax-types')->with('error', 'Cannot delete this item tax type because items/categories have been created referring to it.');
            }
            $type->exemptions()->delete();
            $type->delete();
            return redirect()->route('setup.item-tax-types')->with('success', 'Selected item tax type has been deleted');
        }

        if ($action === 'edit') {
            session(['edit_itemtaxtype_id' => $id]);
            return redirect()->route('setup.item-tax-types');
        }

        if ($action === 'cancel') {
            session()->forget('edit_itemtaxtype_id');
            return redirect()->route('setup.item-tax-types');
        }
    }

    $edit_id = session('edit_itemtaxtype_id');
    $edit_type = $edit_id ? \App\Models\ItemTaxType::with('exemptions')->find($edit_id) : null;
    session()->forget('edit_itemtaxtype_id');

    $show_inactive = session('itemtaxtypes_show_inactive', false);
    $types = $show_inactive
        ? \App\Models\ItemTaxType::all()
        : \App\Models\ItemTaxType::where('inactive', false)->get();

    $tax_types = \App\Models\TaxType::where('inactive', false)->get();

    if ($edit_type) {
        $exempt_tax_ids = $edit_type->exemptions->pluck('tax_type_id')->toArray();
    } else {
        $exempt_tax_ids = [];
    }

    return view('setup.item-tax-types', compact(
        'types', 'edit_type', 'tax_types', 'show_inactive', 'exempt_tax_ids'
    ));
})->name('setup.item-tax-types');
Route::match(['GET', 'POST'], '/setup/system-gl', function () {
    $company_id = auth()->check() ? auth()->user()->company_id : 1;

    if (request()->isMethod('POST')) {
        $fields = [
            'past_due_days', 'accounts_alpha', 'retained_earnings_act', 'profit_loss_year_act',
            'exchange_diff_act', 'bank_charge_act', 'tax_algorithm',
            'default_dim_required',
            'default_credit_limit', 'print_invoice_no', 'accumulate_shipping',
            'print_item_images_on_quote', 'legal_text', 'freight_act', 'deferred_income_act',
            'debtors_act', 'default_sales_act', 'default_sales_discount_act',
            'default_prompt_payment_act', 'default_quote_valid_days', 'default_delivery_required',
            'po_over_receive', 'po_over_charge',
            'creditors_act', 'pyt_discount_act', 'grn_clearing_act', 'default_receival_required',
            'show_po_item_codes',
            'allow_negative_stock', 'no_zero_lines_amount', 'loc_notification', 'allow_negative_prices',
            'default_inv_sales_act', 'default_inventory_act', 'default_cogs_act',
            'default_adj_act', 'default_wip_act',
            'default_loss_on_asset_disposal_act', 'depreciation_period',
            'default_workorder_required',
        ];

        foreach ($fields as $field) {
            $value = request($field);
            if ($value === null && in_array($field, ['allow_negative_stock', 'accumulate_shipping', 'show_po_item_codes',
                'no_zero_lines_amount', 'loc_notification', 'allow_negative_prices',
                'print_item_images_on_quote', 'print_invoice_no'])) {
                $value = 0;
            }
            \App\Models\Setting::setSetting($field, $value, $company_id, 'string', 'gl_setup');
        }

        return redirect()->route('setup.system-gl')->with('success', 'The general GL setup has been updated.');
    }

    $get = function($key, $default = '') use ($company_id) {
        return \App\Models\Setting::getSetting($key, $company_id, $default);
    };

    $prefs = [
        'past_due_days' => $get('past_due_days', '30'),
        'accounts_alpha' => $get('accounts_alpha', '1'),
        'retained_earnings_act' => $get('retained_earnings_act'),
        'profit_loss_year_act' => $get('profit_loss_year_act'),
        'exchange_diff_act' => $get('exchange_diff_act'),
        'bank_charge_act' => $get('bank_charge_act'),
        'tax_algorithm' => $get('tax_algorithm', '1'),
        'default_dim_required' => $get('default_dim_required', '0'),
        'default_credit_limit' => $get('default_credit_limit', '0'),
        'print_invoice_no' => $get('print_invoice_no', '0'),
        'accumulate_shipping' => $get('accumulate_shipping', '0'),
        'print_item_images_on_quote' => $get('print_item_images_on_quote', '0'),
        'legal_text' => $get('legal_text'),
        'freight_act' => $get('freight_act'),
        'deferred_income_act' => $get('deferred_income_act'),
        'debtors_act' => $get('debtors_act'),
        'default_sales_act' => $get('default_sales_act'),
        'default_sales_discount_act' => $get('default_sales_discount_act'),
        'default_prompt_payment_act' => $get('default_prompt_payment_act'),
        'default_quote_valid_days' => $get('default_quote_valid_days', '0'),
        'default_delivery_required' => $get('default_delivery_required', '0'),
        'po_over_receive' => $get('po_over_receive', '0'),
        'po_over_charge' => $get('po_over_charge', '0'),
        'creditors_act' => $get('creditors_act'),
        'pyt_discount_act' => $get('pyt_discount_act'),
        'grn_clearing_act' => $get('grn_clearing_act'),
        'default_receival_required' => $get('default_receival_required', '0'),
        'show_po_item_codes' => $get('show_po_item_codes', '0'),
        'allow_negative_stock' => $get('allow_negative_stock', '0'),
        'no_zero_lines_amount' => $get('no_zero_lines_amount', '0'),
        'loc_notification' => $get('loc_notification', '0'),
        'allow_negative_prices' => $get('allow_negative_prices', '0'),
        'default_inv_sales_act' => $get('default_inv_sales_act'),
        'default_inventory_act' => $get('default_inventory_act'),
        'default_cogs_act' => $get('default_cogs_act'),
        'default_adj_act' => $get('default_adj_act'),
        'default_wip_act' => $get('default_wip_act'),
        'default_loss_on_asset_disposal_act' => $get('default_loss_on_asset_disposal_act'),
        'depreciation_period' => $get('depreciation_period', '0'),
        'default_workorder_required' => $get('default_workorder_required', '0'),
    ];

    $gl_accounts = \App\Models\Account::orderBy('code')->get(['code', 'name']);
    $tax_algorithms = [1 => 'Sum per line taxes', 2 => 'Taxes from totals'];
    $acc_types = [0 => 'Alpha-numeric', 1 => 'Numeric'];

    return view('setup.system-gl', compact('prefs', 'gl_accounts', 'tax_algorithms', 'acc_types'));
})->name('setup.system-gl');
Route::match(['GET', 'POST'], '/setup/fiscal-years', function () {
    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if (in_array($action, ['add', 'update'])) {
            if ($id) {
                $fy = \App\Models\FiscalYear::findOrFail($id);
                $fy->update(['closed' => request()->boolean('closed')]);
                return redirect()->route('setup.fiscal-years')->with('success', 'Selected fiscal year has been updated');
            } else {
                request()->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after:from_date',
                ]);
                $exists = \App\Models\FiscalYear::where('begin', request('from_date'))->orWhere('end', request('to_date'))->exists();
                if ($exists) {
                    return redirect()->route('setup.fiscal-years')->with('error', 'A fiscal year with the same begin or end date already exists.');
                }
                \App\Models\FiscalYear::create([
                    'begin' => request('from_date'),
                    'end' => request('to_date'),
                    'closed' => request()->boolean('closed'),
                ]);
                return redirect()->route('setup.fiscal-years')->with('success', 'New fiscal year has been added');
            }
        }

        if ($action === 'delete') {
            $fy = \App\Models\FiscalYear::findOrFail($id);

            $current_fy_id = \App\Models\Setting::getSetting('current_fiscal_year', auth()->user()->company_id ?? 1);
            if ($fy->id == $current_fy_id) {
                return redirect()->route('setup.fiscal-years')->with('error', 'Cannot delete the current fiscal year.');
            }
            if (!$fy->closed) {
                return redirect()->route('setup.fiscal-years')->with('error', 'Cannot delete this fiscal year because the fiscal year is not closed.');
            }

            $fy->delete();
            return redirect()->route('setup.fiscal-years')->with('success', 'Selected fiscal year has been deleted');
        }

        if ($action === 'edit') {
            session(['edit_fy_id' => $id]);
            return redirect()->route('setup.fiscal-years');
        }

        if ($action === 'cancel') {
            session()->forget('edit_fy_id');
            return redirect()->route('setup.fiscal-years');
        }
    }

    $edit_id = session('edit_fy_id');
    $edit_fy = $edit_id ? \App\Models\FiscalYear::find($edit_id) : null;
    session()->forget('edit_fy_id');

    $years = \App\Models\FiscalYear::orderBy('begin')->get();
    $current_fy_id = \App\Models\Setting::getSetting('current_fiscal_year', auth()->user()->company_id ?? 1);

    if (!$edit_fy) {
        $last_fy = \App\Models\FiscalYear::orderBy('end', 'desc')->first();
        $next_begin = $last_fy ? \Carbon\Carbon::parse($last_fy->end)->addDay()->toDateString() : now()->startOfYear()->toDateString();
        $next_end = $last_fy ? \Carbon\Carbon::parse($next_begin)->addYear()->subDay()->toDateString() : now()->endOfYear()->toDateString();
    } else {
        $next_begin = $next_end = null;
    }

    return view('setup.fiscal-years', compact('years', 'edit_fy', 'current_fy_id', 'next_begin', 'next_end'));
})->name('setup.fiscal-years');
Route::match(['GET', 'POST'], '/setup/print-profiles', function () {
    $reports = [
        '' => 'Default printing destination',
        '101' => 'Customer Balances',
        '102' => 'Customer Transactions',
        '103' => 'Customer Sales by Items',
        '104' => 'Inquiries - Customer',
        '105' => 'Customer Allocation Inquiry',
        '106' => 'Price List',
        '107' => 'Sales Invoice',
        '108' => 'Print Sales Orders',
        '109' => 'Sales Order',
        '110' => 'Delivery Note',
        '111' => 'Sales Quotes',
        '112' => 'Customer Payment Receipt',
        '113' => 'Customer Credit Note',
        '201' => 'Supplier Balances',
        '202' => 'Supplier Transactions',
        '203' => 'Inquiries - Supplier',
        '204' => 'Supplier Allocation Inquiry',
        '205' => 'Print Purchase Orders',
        '206' => 'Purchase Order',
        '207' => 'Purchase Order Delivery',
        '208' => 'Supplier Invoice',
        '209' => 'Supplier Credit Note',
        '210' => 'Supplier Payment',
        '301' => 'Inventory Reports',
        '302' => 'Inventory Valuation',
        '303' => 'Inventory Location Stock',
        '304' => 'Inventory Adjustments Report',
        '305' => 'Inventory Item Movements',
        '401' => 'Manufacturing Reports',
        '402' => 'Work Order',
        '403' => 'Work Order Issue',
        '404' => 'Costed Bill Of Material',
        '501' => 'Fixed Assets Reports',
        '502' => 'Fixed Asset Register',
        '503' => 'Fixed Asset Depreciation Schedule',
        '601' => 'Dimension Reports',
        '701' => 'Bank Reports',
        '702' => 'Cheque Printing',
        '703' => 'Bank Account Transfers',
        '704' => 'Payment Order',
        '801' => 'GL Reports',
        '802' => 'Profit and Loss Statement',
        '803' => 'Balance Sheet',
        '804' => 'General Ledger Report',
        '805' => 'Journal Inquiry',
        '806' => 'Trial Balance',
        '807' => 'Aged Debtors',
        '808' => 'Aged Suppliers',
        '809' => 'GL Inquiry Details',
        '901' => 'Tax Reports',
        '902' => 'Tax Inquiry Details',
    ];

    $printer_list = Printer::orderBy('name')->pluck('name', 'name');
    $printers = collect(['' => '', 'Browser support' => 'Browser support', 'PDF' => 'PDF', 'Network printer' => 'Network printer', 'Local printer' => 'Local printer'])
        ->union($printer_list)
        ->toArray();

    if (request()->isMethod('POST')) {
        $profile = request('profile_id');
        $name = request('name');

        if (request('submit')) {
            if (!$profile && empty($name)) {
                return redirect()->route('setup.print-profiles')->with('error', 'Printing profile name cannot be empty.');
            }
            $profile_name = $profile ?: $name;

            $dest = [];
            foreach ($reports as $rep => $descr) {
                $val = request('Prn'.$rep);
                $dest[$rep] = $val;
            }

            \DB::table('print_profiles')->where('profile', $profile_name)->delete();
            foreach ($dest as $rep => $printer) {
                if ($printer || $rep === '') {
                    \DB::table('print_profiles')->insert([
                        'profile' => $profile_name,
                        'report' => $rep,
                        'printer' => $printer ?: null,
                    ]);
                }
            }

            $msg = $profile ? 'Printing profile has been updated' : 'New printing profile has been created';
            return redirect()->route('setup.print-profiles', $profile ? ['profile_id' => $profile] : [])
                ->with('success', $msg);
        }

        if (request('delete')) {
            $used = \DB::table('users')->where('print_profile', $profile)->exists();
            if ($used) {
                return redirect()->route('setup.print-profiles', ['profile_id' => $profile])
                    ->with('error', 'Cannot delete printing profile because it is used by users.');
            }
            \DB::table('print_profiles')->where('profile', $profile)->delete();
            return redirect()->route('setup.print-profiles')
                ->with('success', 'Selected printing profile has been deleted');
        }
    }

    $profile_id = request('profile_id', '');
    $profiles = \DB::table('print_profiles')->select('profile')->distinct()->orderBy('profile')->pluck('profile');

    $prints = [];
    if ($profile_id) {
        $rows = \DB::table('print_profiles')->where('profile', $profile_id)->get();
        foreach ($rows as $row) {
            $prints[$row->report] = $row->printer;
        }
    }

    return view('setup.print-profiles', compact('reports', 'printers', 'profiles', 'profile_id', 'prints'));
})->name('setup.print-profiles');
Route::match(['GET', 'POST'], '/setup/payment-terms', function () {
    $PTT_PRE = 1; $PTT_CASH = 2; $PTT_DAYS = 3; $PTT_FOLLOWING = 4;
    $pterm_types = [$PTT_PRE => 'Prepayment', $PTT_CASH => 'Cash', $PTT_DAYS => 'After No. of Days', $PTT_FOLLOWING => 'Day In Following Month'];

    $term_type = function($myrow) use ($PTT_PRE, $PTT_CASH, $PTT_DAYS, $PTT_FOLLOWING) {
        if ($myrow->day_in_following_month != 0) return $PTT_FOLLOWING;
        $days = $myrow->days_before_due;
        return $days < 0 ? $PTT_PRE : ($days ? $PTT_DAYS : $PTT_CASH);
    };

    $term_days = function($myrow) {
        return $myrow->day_in_following_month ?: $myrow->days_before_due;
    };

    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if ($action === 'toggle_show_inactive') {
            if (request('show_inactive')) { session(['paymentterms_show_inactive' => true]); }
            else { session()->forget('paymentterms_show_inactive'); }
            return redirect()->route('setup.payment-terms');
        }

        if ($action === 'toggle_inactive') {
            $pt = PaymentTerm::findOrFail($id);
            $pt->update(['inactive' => !$pt->inactive]);
            return redirect()->route('setup.payment-terms')->with('success', 'Payment term status updated.');
        }

        if (in_array($action, ['add', 'update'])) {
            request()->validate([
                'terms' => 'required|string|max:60',
                'type' => 'required|integer|in:1,2,3,4',
            ]);
            $type = (int) request('type');
            $days_input = (int) request('DayNumber');
            if ($type === $PTT_CASH) $days = 0;
            elseif ($type === $PTT_PRE) $days = -1;
            else $days = $days_input;

            if ($type === $PTT_FOLLOWING) {
                $from_now = false;
                $day_in_following_month = $days;
                $days_before_due = 0;
            } else {
                $from_now = true;
                $day_in_following_month = 0;
                $days_before_due = $days;
            }

            $data = [
                'terms' => request('terms'),
                'days_before_due' => $days_before_due,
                'day_in_following_month' => $day_in_following_month,
            ];

            if ($action === 'add') {
                PaymentTerm::create($data);
                return redirect()->route('setup.payment-terms')->with('success', 'New payment terms have been added');
            } else {
                PaymentTerm::where('terms_indicator', $id)->update($data);
                return redirect()->route('setup.payment-terms')->with('success', 'Selected payment terms have been updated');
            }
        }

        if ($action === 'delete') {
            $pt = PaymentTerm::findOrFail($id);
            $used_cust = \DB::table('debtors_master')->where('payment_terms', $id)->exists();
            $used_supp = \DB::table('suppliers')->where('payment_terms', $id)->exists();
            if ($used_cust) return redirect()->route('setup.payment-terms')->with('error', 'Cannot delete this payment term, because customer accounts have been created referring to this term.');
            if ($used_supp) return redirect()->route('setup.payment-terms')->with('error', 'Cannot delete this payment term, because supplier accounts have been created referring to this term.');
            $pt->delete();
            return redirect()->route('setup.payment-terms')->with('success', 'Selected payment terms have been deleted');
        }

        if ($action === 'edit') { session(['edit_pterm_id' => $id]); return redirect()->route('setup.payment-terms'); }
        if ($action === 'cancel') { session()->forget('edit_pterm_id'); return redirect()->route('setup.payment-terms'); }
    }

    $edit_id = session('edit_pterm_id');
    $edit_pt = $edit_id ? PaymentTerm::find($edit_id) : null;
    session()->forget('edit_pterm_id');

    $show_inactive = session('paymentterms_show_inactive', false);
    $terms_list = $show_inactive ? PaymentTerm::all() : PaymentTerm::where('inactive', false)->get();

    $edit_type = null;
    $edit_days = 0;
    if ($edit_pt) {
        $edit_type = $term_type($edit_pt);
        $edit_days = $term_days($edit_pt);
    }

    return view('setup.payment-terms', compact('terms_list', 'edit_pt', 'edit_type', 'edit_days', 'show_inactive', 'pterm_types', 'PTT_PRE', 'PTT_CASH', 'PTT_DAYS', 'PTT_FOLLOWING'));
})->name('setup.payment-terms');
Route::match(['GET', 'POST'], '/setup/shipping-company', function () {
    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if ($action === 'toggle_show_inactive') {
            if (request('show_inactive')) { session(['shippers_show_inactive' => true]); }
            else { session()->forget('shippers_show_inactive'); }
            return redirect()->route('setup.shipping-company');
        }

        if ($action === 'toggle_inactive') {
            $s = Shipper::findOrFail($id);
            $s->update(['inactive' => !$s->inactive]);
            return redirect()->route('setup.shipping-company')->with('success', 'Shipping company status updated.');
        }

        if (in_array($action, ['add', 'update'])) {
            request()->validate(['shipper_name' => 'required|string|max:60']);
            $data = [
                'shipper_name' => request('shipper_name'),
                'contact' => request('contact', ''),
                'phone' => request('phone', ''),
                'phone2' => request('phone2', ''),
                'address' => request('address', ''),
            ];
            if ($action === 'add') {
                Shipper::create($data);
                return redirect()->route('setup.shipping-company')->with('success', 'New shipping company has been added');
            } else {
                Shipper::where('shipper_id', $id)->update($data);
                return redirect()->route('setup.shipping-company')->with('success', 'Selected shipping company has been updated');
            }
        }

        if ($action === 'delete') {
            $s = Shipper::findOrFail($id);
            $used_so = \DB::table('sales_orders')->where('ship_via', $id)->exists();
            $used_dt = \DB::table('debtor_trans')->where('ship_via', $id)->exists();
            if ($used_so) return redirect()->route('setup.shipping-company')->with('error', 'Cannot delete this shipping company because sales orders have been created using this shipper.');
            if ($used_dt) return redirect()->route('setup.shipping-company')->with('error', 'Cannot delete this shipping company because invoices have been created using this shipping company.');
            $s->delete();
            return redirect()->route('setup.shipping-company')->with('success', 'Selected shipping company has been deleted');
        }

        if ($action === 'edit') { session(['edit_shipper_id' => $id]); return redirect()->route('setup.shipping-company'); }
        if ($action === 'cancel') { session()->forget('edit_shipper_id'); return redirect()->route('setup.shipping-company'); }
    }

    $edit_id = session('edit_shipper_id');
    $edit_shipper = $edit_id ? Shipper::find($edit_id) : null;
    session()->forget('edit_shipper_id');

    $show_inactive = session('shippers_show_inactive', false);
    $shippers = $show_inactive ? Shipper::all() : Shipper::where('inactive', false)->get();

    return view('setup.shipping-company', compact('shippers', 'edit_shipper', 'show_inactive'));
})->name('setup.shipping-company');
Route::match(['GET', 'POST'], '/setup/points-of-sale', function () {
    if (request()->isMethod('POST')) {
        $action = request('action');
        $id = request('selected_id');

        if ($action === 'toggle_show_inactive') {
            if (request('show_inactive')) { session(['salespos_show_inactive' => true]); }
            else { session()->forget('salespos_show_inactive'); }
            return redirect()->route('setup.points-of-sale');
        }

        if ($action === 'toggle_inactive') {
            $p = SalesPoint::findOrFail($id);
            $p->update(['inactive' => !$p->inactive]);
            return redirect()->route('setup.points-of-sale')->with('success', 'Point of sale status updated.');
        }

        if (in_array($action, ['add', 'update'])) {
            request()->validate(['name' => 'required|string|max:30']);
            $data = [
                'pos_name' => request('name'),
                'pos_location' => request('location', ''),
                'pos_account' => request('account', ''),
                'cash_sale' => request()->boolean('cash'),
                'credit_sale' => request()->boolean('credit'),
            ];
            if ($action === 'add') {
                SalesPoint::create($data);
                return redirect()->route('setup.points-of-sale')->with('success', 'New point of sale has been added');
            } else {
                SalesPoint::where('id', $id)->update($data);
                return redirect()->route('setup.points-of-sale')->with('success', 'Selected point of sale has been updated');
            }
        }

        if ($action === 'delete') {
            $p = SalesPoint::findOrFail($id);
            $used = \DB::table('users')->where('pos', $id)->exists();
            if ($used) return redirect()->route('setup.points-of-sale')->with('error', 'Cannot delete this POS because it is used in users setup.');
            $p->delete();
            return redirect()->route('setup.points-of-sale')->with('success', 'Selected point of sale has been deleted');
        }

        if ($action === 'edit') { session(['edit_salespoint_id' => $id]); return redirect()->route('setup.points-of-sale'); }
        if ($action === 'cancel') { session()->forget('edit_salespoint_id'); return redirect()->route('setup.points-of-sale'); }
    }

    $edit_id = session('edit_salespoint_id');
    $edit_pt = $edit_id ? SalesPoint::find($edit_id) : null;
    session()->forget('edit_salespoint_id');

    $show_inactive = session('salespos_show_inactive', false);
    $points = $show_inactive ? SalesPoint::all() : SalesPoint::where('inactive', false)->get();

    $warehouses = \App\Models\Warehouse::where('is_active', true)->get(['id', 'name']);
    $accounts = \App\Models\Account::orderBy('code')->get(['code', 'name']);

    return view('setup.points-of-sale', compact('points', 'edit_pt', 'show_inactive', 'warehouses', 'accounts'));
})->name('setup.points-of-sale');
Route::match(['GET', 'POST'], '/setup/printers', function () {
    $Mode = request('Mode', '');
    $selected_id = request('selected_id', -1);

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $error = 0;
        if (empty(request('name'))) {
            $error = 1;
            $msg = 'Printer name cannot be empty.';
        } elseif (request('tout') !== '' && request('tout') !== null && (request('tout') < 0 || request('tout') > 60)) {
            $error = 1;
            $msg = 'Timeout cannot be less than zero nor longer than 60 (sec).';
        }

        if ($error) {
            return redirect()->route('setup.printers', $selected_id != -1 ? ['selected_id' => $selected_id, 'Mode' => 'Edit'] : [])
                ->with('error', $msg)->withInput();
        }

        $data = [
            'name' => request('name'),
            'description' => request('descr', ''),
            'queue' => request('queue', ''),
            'host' => request('host', 'localhost'),
            'port' => (int)(request('port', 515)),
            'timeout' => (int)(request('tout', 0)),
        ];

        if ($Mode === 'ADD_ITEM') {
            Printer::create($data);
            session()->flash('success', 'New printer definition has been created');
        } else {
            Printer::where('id', $selected_id)->update($data);
            session()->flash('success', 'Selected printer definition has been updated');
        }
        return redirect()->route('setup.printers');
    }

    if ($Mode === 'Delete') {
        $printer = Printer::findOrFail($selected_id);
        $used = \DB::table('print_profiles')->where('printer', $printer->name)->exists();
        if ($used) {
            return redirect()->route('setup.printers')->with('error', 'Cannot delete this printer definition, because print profile have been created using it.');
        }
        $printer->delete();
        return redirect()->route('setup.printers')->with('success', 'Selected printer definition has been deleted');
    }

    if ($Mode === 'Cancel') {
        return redirect()->route('setup.printers');
    }

    $printers = Printer::orderBy('name')->get();
    $edit_printer = null;
    if ($Mode === 'Edit' && $selected_id != -1) {
        $edit_printer = Printer::find($selected_id);
    }

    return view('setup.printers', compact('printers', 'edit_printer'));
})->name('setup.printers');
Route::match(['GET', 'POST'], '/setup/contact-categories', function () {
    $Mode = request('Mode', '');
    $selected_id = request('selected_id', -1);

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $error = 0;
        if (empty(request('description'))) {
            $error = 1;
            return redirect()->route('setup.contact-categories', $selected_id != -1 ? ['selected_id' => $selected_id, 'Mode' => 'Edit'] : [])
                ->with('error', 'Category description cannot be empty.')->withInput();
        }

        $data = [
            'name' => request('name', ''),
            'description' => request('description', ''),
        ];

        if ($selected_id != -1) {
            if (request('type')) $data['type'] = request('type');
            if (request('subtype')) $data['action'] = request('subtype');
            ContactCategory::where('id', $selected_id)->update($data);
            session()->flash('success', 'Selected contact category has been updated');
        } else {
            $data['type'] = request('type', '');
            $data['action'] = request('subtype', '');
            ContactCategory::create($data);
            session()->flash('success', 'New contact category has been added');
        }
        return redirect()->route('setup.contact-categories');
    }

    if ($Mode === 'Delete') {
        $cat = ContactCategory::findOrFail($selected_id);
        $used = \DB::table('crm_contacts')
            ->where('type', $cat->type)
            ->where('action', $cat->action)
            ->count();
        if ($used > 0) {
            return redirect()->route('setup.contact-categories')->with('error', 'Cannot delete this category because there are contacts related to it.');
        }
        $cat->delete();
        return redirect()->route('setup.contact-categories')->with('success', 'Category has been deleted');
    }

    if ($Mode === 'Cancel') {
        return redirect()->route('setup.contact-categories');
    }

    if (request('toggle_inactive')) {
        $cat = ContactCategory::findOrFail(request('toggle_inactive'));
        $cat->update(['inactive' => !$cat->inactive]);
        return redirect()->route('setup.contact-categories', request()->has('show_inactive') ? ['show_inactive' => '1'] : []);
    }

    if (request('show_inactive')) {
        session(['crmcat_show_inactive' => true]);
    } elseif (request()->has('show_inactive')) {
        session()->forget('crmcat_show_inactive');
    }

    $show_inactive = session('crmcat_show_inactive', false);
    $categories = ContactCategory::orderBy('type')->orderBy('action')
        ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
        ->get();

    $edit_cat = null;
    if ($Mode === 'Edit' && $selected_id != -1) {
        $edit_cat = ContactCategory::find($selected_id);
    }

    return view('setup.contact-categories', compact('categories', 'edit_cat', 'show_inactive'));
})->name('setup.contact-categories');
Route::match(['GET', 'POST'], '/setup/void-transaction', function () {
    $systypes = [
        0  => 'Journal Entry',
        1  => 'Bank Payment',
        2  => 'Bank Deposit',
        4  => 'Funds Transfer',
        10 => 'Sales Invoice',
        11 => 'Customer Credit Note',
        12 => 'Customer Payment',
        13 => 'Delivery Note',
        16 => 'Location Transfer',
        17 => 'Inventory Adjustment',
        20 => 'Supplier Invoice',
        21 => 'Supplier Credit Note',
        22 => 'Supplier Payment',
        25 => 'Purchase Order Delivery',
        26 => 'Work Order',
        28 => 'Work Order Issue',
        29 => 'Work Order Production',
    ];

    $filterType = request('filterType', 0);
    $fromNo = request('FromTransNo', '1');
    $toNo = request('ToTransNo', '999999');
    $selected_id = request('selected_id', -1);
    $action = request('action');
    $message = '';
    $error_msg = '';
    $confirm = false;

    if ($action === 'void' && request('trans_no')) {
        $trans_no = request('trans_no');
        $void_date = request('date_');
        $memo = request('memo_', '');

        if (!is_numeric($trans_no) || $trans_no <= 0) {
            $error_msg = 'The transaction number is expected to be numeric and greater than zero.';
        } elseif (!strtotime($void_date)) {
            $error_msg = 'The entered date is invalid.';
        } else {
            $voided = \DB::table('voided')->where('type', $filterType)->where('trans_no', $trans_no)->exists();
            if ($voided) {
                $error_msg = 'The selected transaction has already been voided.';
            } elseif (!request('confirmed')) {
                $confirm = true;
            } else {
                \DB::table('voided')->insert([
                    'type' => $filterType,
                    'trans_no' => $trans_no,
                    'date_' => $void_date,
                    'memo_' => $memo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($filterType == 18) { $error_msg = 'This transaction type cannot be voided.'; \DB::table('voided')->where('type', 18)->where('trans_no', $trans_no)->delete(); }
                elseif ($filterType == 30) { $error_msg = 'This transaction type cannot be voided.'; \DB::table('voided')->where('type', 30)->where('trans_no', $trans_no)->delete(); }
                elseif ($filterType == 32) { $error_msg = 'This transaction type cannot be voided.'; \DB::table('voided')->where('type', 32)->where('trans_no', $trans_no)->delete(); }
                elseif ($filterType == 35) { $error_msg = 'This transaction type cannot be voided.'; \DB::table('voided')->where('type', 35)->where('trans_no', $trans_no)->delete(); }
                else {
                    $message = 'Selected transaction has been voided.';
                }
            }
        }
    }

    $results = [];
    if ($filterType !== null && $fromNo && $toNo) {
        switch ($filterType) {
            case 0:
                $rows = \DB::table('journal_entries')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'entry_number as ref', 'entry_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 10: case 11: case 12: case 13:
                $rows = \DB::table('debtor_trans')
                    ->where('type', $filterType)
                    ->whereBetween('trans_no', [(int)$fromNo, (int)$toNo])
                    ->orderBy('trans_no', 'desc')
                    ->get(['trans_no', 'reference as ref', 'tran_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 18:
                $rows = \DB::table('purchase_orders')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'order_number as ref', 'order_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 26:
                $rows = \DB::table('production_orders')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'order_number as ref', 'order_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 30:
                $rows = \DB::table('sales_orders')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'order_number as ref', 'order_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 32:
                $rows = \DB::table('sales_quotations')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'quotation_number as ref', 'quotation_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
        }

        $results = array_filter($results, function($r) use ($filterType) {
            return !\DB::table('voided')->where('type', $filterType)->where('trans_no', $r['trans_no'])->exists();
        });
    }

    if ($selected_id != -1) {
        $sel = $selected_id;
    } else {
        $sel = '';
    }

    if (request('cancel')) {
        $selected_id = -1;
        $sel = '';
        $confirm = false;
    }

    $today = date('Y-m-d');

    return view('setup.void-transaction', compact(
        'systypes', 'filterType', 'fromNo', 'toNo', 'results', 'selected_id', 'sel',
        'message', 'error_msg', 'confirm', 'today'
    ));
})->name('setup.void-transaction');
Route::match(['GET', 'POST'], '/setup/view-print-transactions', function () {
    $systypes = [
        0  => 'Journal Entry',
        1  => 'Bank Payment',
        2  => 'Bank Deposit',
        4  => 'Funds Transfer',
        10 => 'Sales Invoice',
        11 => 'Customer Credit Note',
        12 => 'Customer Payment',
        13 => 'Delivery Note',
        16 => 'Location Transfer',
        17 => 'Inventory Adjustment',
        18 => 'Purchase Order',
        20 => 'Supplier Invoice',
        21 => 'Supplier Credit Note',
        22 => 'Supplier Payment',
        25 => 'Purchase Order Delivery',
        26 => 'Work Order',
        28 => 'Work Order Issue',
        29 => 'Work Order Production',
        30 => 'Sales Order',
        32 => 'Sales Quotation',
        35 => 'Cost Update',
        40 => 'Dimension',
    ];

    $filterType = request('filterType', 0);
    $fromNo = request('FromTransNo', '1');
    $toNo = request('ToTransNo', '999999');
    $error_msg = '';

    if (request('ProcessSearch')) {
        if (!is_numeric($fromNo) || $fromNo <= 0) {
            $error_msg = 'The starting transaction number is expected to be numeric and greater than zero.';
        } elseif (!is_numeric($toNo) || $toNo <= 0) {
            $error_msg = 'The ending transaction number is expected to be numeric and greater than zero.';
        }
    }

    $results = [];
    if ($error_msg === '' && request('ProcessSearch')) {
        switch ($filterType) {
            case 0:
                $rows = \DB::table('journal_entries')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'entry_number as ref', 'entry_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 10: case 11: case 12: case 13:
                $rows = \DB::table('debtor_trans')
                    ->where('type', $filterType)
                    ->whereBetween('trans_no', [(int)$fromNo, (int)$toNo])
                    ->orderBy('trans_no', 'desc')
                    ->get(['trans_no', 'reference as ref', 'tran_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 18:
                $rows = \DB::table('purchase_orders')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'order_number as ref', 'order_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 26:
                $rows = \DB::table('production_orders')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'order_number as ref', 'order_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 30:
                $rows = \DB::table('sales_orders')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'order_number as ref', 'order_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
            case 32:
                $rows = \DB::table('sales_quotations')
                    ->whereBetween('id', [(int)$fromNo, (int)$toNo])
                    ->orderBy('id', 'desc')
                    ->get(['id as trans_no', 'quotation_number as ref', 'quotation_date as trans_date']);
                foreach ($rows as $r) $results[] = (array)$r;
                break;
        }
    }

    $printable = in_array($filterType, [10, 11, 12, 13, 18, 22, 26, 30, 32]);

    return view('setup.view-print-transactions', compact(
        'systypes', 'filterType', 'fromNo', 'toNo', 'results', 'error_msg', 'printable'
    ));
})->name('setup.view-print-transactions');
Route::match(['GET', 'POST'], '/setup/attach-documents', function () {
    $systypes = [
        0  => 'Journal Entry', 1  => 'Bank Payment', 2  => 'Bank Deposit', 4  => 'Funds Transfer',
        10 => 'Sales Invoice', 11 => 'Customer Credit Note', 12 => 'Customer Payment', 13 => 'Delivery Note',
        16 => 'Location Transfer', 17 => 'Inventory Adjustment', 18 => 'Purchase Order',
        20 => 'Supplier Invoice', 21 => 'Supplier Credit Note', 22 => 'Supplier Payment',
        25 => 'Purchase Order Delivery', 26 => 'Work Order', 28 => 'Work Order Issue', 29 => 'Work Order Production',
        30 => 'Sales Order', 32 => 'Sales Quotation', 35 => 'Cost Update', 40 => 'Dimension',
        50 => 'Customer', 51 => 'Supplier', 52 => 'Item', 53 => 'Fixed Asset', 54 => 'Bank Account',
    ];

    $attachDir = storage_path('app/attachments');
    if (!is_dir($attachDir)) { mkdir($attachDir, 0755, true); }

    $filterType = request('filterType', 0);
    $trans_no = request('trans_no', '');
    $selected_id = request('selected_id', -1);
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    if (request('vw')) {
        $row = \DB::table('attachments')->find(request('vw'));
        if ($row && $row->filename) {
            $filepath = $attachDir . '/' . $row->unique_name;
            if (file_exists($filepath)) {
                return response(file_get_contents($filepath), 200, [
                    'Content-Type' => $row->filetype ?: 'application/octet-stream',
                    'Content-Length' => $row->filesize,
                    'Content-Disposition' => 'inline',
                ]);
            }
        }
        $error = 'File not found.';
    }

    if (request('dl')) {
        $row = \DB::table('attachments')->find(request('dl'));
        if ($row && $row->filename) {
            $filepath = $attachDir . '/' . $row->unique_name;
            if (file_exists($filepath)) {
                return response()->download($filepath, $row->filename, [
                    'Content-Type' => $row->filetype ?: 'application/octet-stream',
                ]);
            }
        }
        $error = 'File not found.';
    }

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        $file = request()->file('filename');
        $filename = $file ? $file->getClientOriginalName() : '';
        $transNoVal = $trans_no;
        $desc = request('description', '');

        if (in_array($filterType, [52, 53])) {
            $item = \App\Models\Item::where('code', $trans_no)->orWhere('id', $trans_no)->first();
            $transNoVal = $item ? $item->id : $trans_no;
        }

        $transExists = false;
        switch ($filterType) {
            case 50: $transExists = (bool)\App\Models\Customer::find($transNoVal); break;
            case 51: $transExists = (bool)\App\Models\Supplier::find($transNoVal); break;
            case 52: case 53: $transExists = (bool)\App\Models\Item::find($transNoVal); break;
            case 54: $transExists = (bool)\App\Models\Account::find($transNoVal); break;
            case 0: $transExists = \DB::table('journal_entries')->where('id', $transNoVal)->exists(); break;
            case 10: case 11: case 12: case 13:
                $transExists = \DB::table('debtor_trans')->where('trans_no', $transNoVal)->where('type', $filterType)->exists(); break;
            case 18: $transExists = \DB::table('purchase_orders')->where('id', $transNoVal)->exists(); break;
            case 26: $transExists = \DB::table('production_orders')->where('id', $transNoVal)->exists(); break;
            case 30: $transExists = \DB::table('sales_orders')->where('id', $transNoVal)->exists(); break;
            case 32: $transExists = \DB::table('sales_quotations')->where('id', $transNoVal)->exists(); break;
            default: $transExists = (is_numeric($transNoVal) && $transNoVal > 0); break;
        }

        if (!$transExists) {
            $error = 'Selected transaction does not exists.';
        } elseif ($file) {
            $ext = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($ext, ['JPG', 'PNG', 'GIF', 'PDF', 'DOC', 'ODT'])) {
                $error = 'Only graphics,pdf,doc and odt files are supported.';
            } elseif (strlen($filename) > 60) {
                $error = 'File name exceeds maximum of 60 chars. Please change filename and try again.';
            } elseif ($file->getError() === UPLOAD_ERR_INI_SIZE) {
                $error = 'The file size is over the maximum allowed.';
            } elseif (!$file->isValid()) {
                $error = 'Select attachment file.';
            }
        } elseif ($Mode === 'ADD_ITEM') {
            $error = 'Select attachment file.';
        }

        if (!$error) {
            $unique_name = null;
            if ($Mode === 'UPDATE_ITEM') {
                $row = \DB::table('attachments')->find($selected_id);
                if ($row) {
                    $unique_name = $row->unique_name;
                    if ($file && file_exists($attachDir . '/' . $unique_name)) {
                        unlink($attachDir . '/' . $unique_name);
                    }
                }
            }
            if (!$unique_name) { $unique_name = uniqid('', true) . '_' . ($filename ?: 'file'); }

            $filesize = 0; $filetype = '';
            if ($file && $file->isValid()) {
                $filesize = $file->getSize();
                $filetype = $file->getMimeType() ?: 'application/octet-stream';
                $file->move($attachDir, $unique_name);
            }

            if ($Mode === 'ADD_ITEM') {
                \DB::table('attachments')->insert([
                    'type_no' => $filterType,
                    'trans_no' => $transNoVal,
                    'description' => $desc,
                    'filename' => $filename,
                    'unique_name' => $unique_name,
                    'filesize' => $filesize,
                    'filetype' => $filetype,
                    'tran_date' => now(),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $message = 'Attachment has been inserted.';
            } else {
                $upd = ['description' => $desc, 'updated_at' => now()];
                if ($filename) { $upd['filename'] = $filename; $upd['unique_name'] = $unique_name; $upd['filesize'] = $filesize; $upd['filetype'] = $filetype; }
                \DB::table('attachments')->where('id', $selected_id)->update($upd);
                $message = 'Attachment has been updated.';
            }
            $selected_id = -1; $Mode = 'RESET';
        }
    }

    if ($Mode === 'Delete') {
        $row = \DB::table('attachments')->find($selected_id);
        if ($row) {
            $fp = $attachDir . '/' . $row->unique_name;
            if (file_exists($fp)) unlink($fp);
            \DB::table('attachments')->where('id', $selected_id)->delete();
            $message = 'Attachment has been deleted.';
        }
        $selected_id = -1; $Mode = 'RESET';
    }

    if ($Mode === 'Edit' && $selected_id != -1) {
        $row = \DB::table('attachments')->find($selected_id);
        if ($row) {
            $edit_row = $row;
            $trans_no = $row->trans_no;
        } else {
            $edit_row = null; $selected_id = -1;
        }
    } else {
        $edit_row = null;
    }

    $query = \DB::table('attachments');
    $query->where('type_no', $filterType);
    if (in_array($filterType, [50, 51, 52, 53, 54]) && $trans_no) {
        $query->where('trans_no', $trans_no);
    }
    $attachments = $query->orderBy('id', 'desc')->get();

    $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name']);
    $suppliers = \App\Models\Supplier::orderBy('name')->get(['id', 'name']);
    $items = \App\Models\Item::where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']);
    $accounts = \App\Models\Account::where('is_active', true)->orderBy('code')->get(['id', 'code', 'name']);

    return view('setup.attach-documents', compact(
        'systypes', 'filterType', 'trans_no', 'attachments', 'selected_id', 'edit_row',
        'message', 'error', 'customers', 'suppliers', 'items', 'accounts'
    ));
})->name('setup.attach-documents');
Route::get('/setup/system-diagnostics', function () { return view('setup.system-diagnostics'); })->name('setup.system-diagnostics');
Route::get('/setup/settings', function () { return view('setup.settings'); })->name('setup.settings');
Route::get('/setup/system-info', function () { return view('setup.system-info'); })->name('setup.system-info');
Route::match(['GET', 'POST'], '/setup/backup', function () {
    $backupDir = storage_path('app/backup');
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $mysqldump = '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe"';
    $mysql = '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"';

    $conn = config('database.connections.mysql');
    $dbHost = $conn['host'];
    $dbPort = $conn['port'];
    $dbName = $conn['database'];
    $dbUser = $conn['username'];
    $dbPass = $conn['password'];

    $message = '';
    $error = '';

    if (request('creat')) {
        $comments = request('comments', '');
        $comp = request('comp', 'no');
        $filename = date('Y-m-d_H-i-s') . '_' . str_replace('.', '', microtime(true));
        if ($comp === 'zip') {
            $filename .= '.sql.zip';
        } elseif ($comp === 'gzip') {
            $filename .= '.sql.gz';
        } else {
            $filename .= '.sql';
        }
        $filepath = $backupDir . '/' . $filename;

        $cmd = "$mysqldump -h $dbHost -P $dbPort -u $dbUser --password=$dbName $dbName";
        if ($dbPass) {
            $cmd = "$mysqldump -h $dbHost -P $dbPort -u $dbUser --password=\"$dbPass\" $dbName";
        }

        if ($comp === 'zip') {
            $tmpfile = $backupDir . '/' . uniqid() . '.sql';
            $cmd2 = "$cmd > \"$tmpfile\" 2>&1";
            exec($cmd2, $output, $exitCode);
            if ($exitCode === 0 && file_exists($tmpfile)) {
                $zip = new \ZipArchive();
                if ($zip->open($filepath, \ZipArchive::CREATE) === true) {
                    $zip->addFile($tmpfile, basename($tmpfile));
                    if ($comments) { $zip->setArchiveComment($comments); }
                    $zip->close();
                }
                unlink($tmpfile);
                $message = 'Backup successfully generated. Filename: ' . $filename;
            } else {
                $error = 'Database backup failed.';
                if (file_exists($tmpfile)) unlink($tmpfile);
            }
        } elseif ($comp === 'gzip') {
            $tmpfile = $backupDir . '/' . uniqid() . '.sql';
            $cmd2 = "$cmd > \"$tmpfile\" 2>&1";
            exec($cmd2, $output, $exitCode);
            if ($exitCode === 0 && file_exists($tmpfile)) {
                $data = file_get_contents($tmpfile);
                if ($comments) { $data = '-- ' . $comments . "\n" . $data; }
                $gz = gzopen($filepath, 'w9');
                gzwrite($gz, $data);
                gzclose($gz);
                unlink($tmpfile);
                $message = 'Backup successfully generated. Filename: ' . $filename;
            } else {
                $error = 'Database backup failed.';
                if (file_exists($tmpfile)) unlink($tmpfile);
            }
        } else {
            $cmd2 = "$cmd > \"$filepath\" 2>&1";
            exec($cmd2, $output, $exitCode);
            if ($exitCode === 0) {
                if ($comments) {
                    $data = file_get_contents($filepath);
                    file_put_contents($filepath, '-- ' . $comments . "\n" . $data);
                }
                $message = 'Backup successfully generated. Filename: ' . $filename;
            } else {
                $error = 'Database backup failed.';
            }
        }
    }

    if (request('restore')) {
        $backupName = request('backups');
        if ($backupName) {
            $filepath = $backupDir . '/' . basename($backupName);
            if (file_exists($filepath)) {
                $protect = request('protect', 1);
                $cmd = "$mysql -h $dbHost -P $dbPort -u $dbUser";
                if ($dbPass) { $cmd .= " --password=\"$dbPass\""; }
                $cmd .= " $dbName < \"$filepath\" 2>&1";
                exec($cmd, $output, $exitCode);
                if ($exitCode === 0) {
                    $message = 'Restore backup completed.';
                } else {
                    $error = 'Restore backup failed: ' . implode("\n", $output);
                }
            } else {
                $error = 'Backup file not found.';
            }
        } else {
            $error = 'Select backup file first.';
        }
    }

    if (request('deldump')) {
        $backupName = request('backups');
        if ($backupName) {
            $filepath = $backupDir . '/' . basename($backupName);
            if (file_exists($filepath) && unlink($filepath)) {
                $message = 'File successfully deleted. Filename: ' . basename($backupName);
            } else {
                $error = "Can't delete backup file.";
            }
        } else {
            $error = 'Select backup file first.';
        }
    }

    if (request('view')) {
        $backupName = request('backups');
        if ($backupName) {
            $filepath = $backupDir . '/' . basename($backupName);
            if (file_exists($filepath)) {
                $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
                if ($ext === 'gz') {
                    $handle = gzopen($filepath, 'r');
                    $content = '';
                    while (!gzeof($handle)) { $content .= gzgets($handle); }
                    gzclose($handle);
                } elseif ($ext === 'zip') {
                    $zip = new \ZipArchive();
                    if ($zip->open($filepath) === true) {
                        $content = $zip->getFromIndex(0);
                        $zip->close();
                    } else { $content = 'Cannot read zip file.'; }
                } else {
                    $content = file_get_contents($filepath);
                }
                $content = htmlspecialchars(substr($content, 0, 100000));
                $message = 'Backup content (first 100KB):<br><pre style="max-height:400px;overflow:auto;font-size:11px;line-height:1.2">' . $content . '</pre>';
            } else {
                $error = 'Select backup file first.';
            }
        } else {
            $error = 'Select backup file first.';
        }
        $message = str_replace(['<pre>', '</pre>'], ['<pre class="bg-gray-100 p-4 rounded text-xs mt-2">', '</pre>'], $message);
    }

    if (request('download')) {
        $backupName = request('backups');
        if ($backupName) {
            $filepath = $backupDir . '/' . basename($backupName);
            if (file_exists($filepath)) {
                return response()->download($filepath, basename($backupName));
            } else {
                $error = 'Select backup file first.';
            }
        } else {
            $error = 'Select backup file first.';
        }
    }

    if (request('upload')) {
        if (request()->hasFile('uploadfile')) {
            $file = request()->file('uploadfile');
            $fname = trim($file->getClientOriginalName());
            if (!preg_match("/\.sql(\.zip|\.gz)?$/", $fname)) {
                $error = 'You can only upload *.sql backup files';
            } else {
                $file->move($backupDir, $fname);
                $message = 'File uploaded to backup directory';
            }
        } else {
            $error = 'Select backup file first.';
        }
    }

    $files = [];
    $dh = opendir($backupDir);
    while (($f = readdir($dh)) !== false) {
        if (preg_match("/\.sql(\.zip|\.gz)?$/", $f)) {
            $files[] = $f;
        }
    }
    closedir($dh);
    rsort($files);

    $hasZip = class_exists('\\ZipArchive');
    $hasGzip = function_exists('gzopen');

    return view('setup.backup', compact('files', 'message', 'error', 'hasZip', 'hasGzip'));
})->name('setup.backup');
Route::match(['GET', 'POST'], '/setup/companies', function () {
    $selected_id = request('selected_id', -1);
    $Mode = request('Mode', '');
    $message = '';
    $error = '';

    $def_coy = session('default_company', 1);

    if (request('action') === 'toggle_default') {
        session(['default_company' => request('selected_id')]);
        return redirect()->route('setup.companies')->with('success', 'Default company updated.');
    }

    if (in_array($Mode, ['ADD_ITEM', 'UPDATE_ITEM'])) {
        if (empty(request('name'))) {
            $error = 'Company name cannot be empty.';
        }

        if (!$error) {
            $data = [
                'name' => request('name'),
                'email' => request('email', ''),
                'phone' => request('phone', ''),
                'address' => request('address', ''),
                'city' => request('city', ''),
                'country' => request('country', ''),
                'postal_code' => request('postal_code', ''),
                'tax_id' => request('tax_id', ''),
                'registration_number' => request('registration_number', ''),
                'website' => request('website', ''),
                'notes' => request('notes', ''),
                'is_active' => request()->boolean('is_active'),
            ];

            if (request()->hasFile('logo')) {
                $logo = request()->file('logo');
                $logoPath = $logo->store('logos', 'public');
                $data['logo'] = $logoPath;
            }

            if ($Mode === 'ADD_ITEM') {
                $company = Company::create($data);
                if (request()->boolean('default')) {
                    session(['default_company' => $company->id]);
                }
                $message = 'New company has been created.';
            } else {
                Company::where('id', $selected_id)->update($data);
                if (request()->boolean('default')) {
                    session(['default_company' => $selected_id]);
                }
                $message = 'Company has been updated.';
            }
            $Mode = 'RESET'; $selected_id = -1;
        }
    }

    if ($Mode === 'Delete') {
        $coy = Company::find($selected_id);
        if (!$coy) {
            $error = 'Company not found.';
        } elseif ($selected_id == session('current_company_id', auth()->user()->company_id ?? 1)) {
            $error = 'The current company cannot be deleted.';
        } else {
            $coy->delete();
            $message = 'Selected company has been deleted.';
        }
        $Mode = 'RESET'; $selected_id = -1;
    }

    if ($Mode === 'Edit' && $selected_id != -1) {
        $edit_coy = Company::find($selected_id);
        if (!$edit_coy) { $edit_coy = null; $selected_id = -1; }
    } else {
        $edit_coy = null;
    }

    $companies = Company::orderBy('name')->get();

    return view('setup.companies', compact('companies', 'edit_coy', 'selected_id', 'Mode', 'message', 'error', 'def_coy'));
})->name('setup.companies');
