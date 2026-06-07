<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBranch;
use App\Models\Item;
use App\Models\QuotationLineItem;
use App\Models\SalesQuotation;
use App\Models\JournalEntry;
use App\Models\OrderLineItem;
use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::active()->count(),
            'total_quotations' => SalesQuotation::count(),
            'total_orders' => SalesOrder::count(),
            'pending_orders' => SalesOrder::pending()->count(),
            'delivered_orders' => SalesOrder::delivered()->count(),
        ];

        $recentActivities = [
            'recent_quotations' => SalesQuotation::with('customer')->latest()->take(5)->get(),
            'recent_orders' => SalesOrder::with('customer')->latest()->take(5)->get(),
            'recent_customers' => Customer::latest()->take(5)->get(),
        ];

        return view('dashboard', compact('stats', 'recentActivities'));
    }

    // Sales Quotations
    public function quotationsIndex(): View
    {
        $quotations = SalesQuotation::with(['customer', 'salesPerson'])
            ->latest()
            ->paginate(10);

        return view('sales.quotations.index', compact('quotations'));
    }

    public function quotationsCreate(): View
    {
        $customers = Customer::active()->get();
        $salesPersons = \App\Models\SalesPerson::active()->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $items = Item::where('is_active', true)->get();

        return view('sales.quotations.create', compact('customers', 'salesPersons', 'salesTypes', 'items'));
    }

    public function quotationsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_person_id' => 'required|exists:sales_persons,id',
            'sales_type_id' => 'required|exists:sales_types,id',
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after:quotation_date',
            'customer_notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'line_items' => 'required|array|min:1',
            'line_items.*.item_id' => 'required|exists:items,id',
            'line_items.*.description' => 'nullable|string',
            'line_items.*.quantity' => 'required|numeric|min:0.01',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'line_items.*.tax_rate' => 'required|numeric|min:0',
            'line_items.*.discount_percentage' => 'required|numeric|min:0',
            'line_items.*.line_total' => 'required|numeric|min:0',
        ]);

        $validated['status'] = 'draft';

        $quotation = SalesQuotation::create($validated);
        $quotation->quotation_number = $this->generateQuotationNumber();
        $quotation->save();

        // Create line items
        foreach ($validated['line_items'] as $lineItemData) {
            $item = Item::find($lineItemData['item_id']);
            $lineItemData['quotation_id'] = $quotation->id;
            $lineItemData['item_code'] = $item->code;
            $lineItemData['discount_amount'] = ($lineItemData['quantity'] * $lineItemData['unit_price']) * ($lineItemData['discount_percentage'] / 100);
            $lineItemData['tax_amount'] = (($lineItemData['quantity'] * $lineItemData['unit_price']) - $lineItemData['discount_amount']) * ($lineItemData['tax_rate'] / 100);
            
            QuotationLineItem::create($lineItemData);
        }

        return redirect()
            ->route('sales.quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function quotationsShow(Request $request, SalesQuotation $quotation)
    {
        $quotation->load(['customer', 'salesPerson', 'salesType', 'lineItems']);

        if ($request->boolean('print')) {
            $pdf = Pdf::loadView('sales.quotations.print', compact('quotation'));
            return $pdf->stream('quotation-' . $quotation->quotation_number . '.pdf');
        }

        return view('sales.quotations.show', compact('quotation'));
    }

    public function quotationsEdit(Request $request, SalesQuotation $quotation)
    {
        // Handle POST cart operations
        if ($request->isMethod('POST')) {
            $cart = session('quote_edit_' . $quotation->id) ?? [];

            if ($request->has('CancelOrder')) {
                session(['quote_edit_' . $quotation->id => null, 'quote_edit_index_' . $quotation->id => null]);
                return redirect()->route('sales.quotations.index');
            }

            if ($request->has('AddItem')) {
                $stock_id = $request->input('stock_id');
                if (!$stock_id) {
                    return back()->with('error', 'Please select an item.');
                }
                $item = Item::where('code', $stock_id)->first();
                $line_item = [
                    'stock_id' => $stock_id,
                    'item_description' => $request->input('item_description', $item->name ?? ''),
                    'quantity' => (float)($request->input('qty', 1)),
                    'price' => (float)($request->input('price', 0)),
                    'discount_percent' => (float)($request->input('Disc', 0)) / 100,
                    'units' => $item->unit_of_measure ?? 'each',
                ];
                $cart['line_items'][] = $line_item;
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                $cart['price'] = 0;
                $cart['Disc'] = 0;
                session(['quote_edit_' . $quotation->id => $cart]);
                return redirect()->route('sales.quotations.edit', $quotation);
            }

            if ($request->has('Delete')) {
                $line_no = (int)$request->input('Delete');
                if (isset($cart['line_items'][$line_no])) {
                    unset($cart['line_items'][$line_no]);
                    $cart['line_items'] = array_values($cart['line_items']);
                }
                session(['quote_edit_' . $quotation->id => $cart]);
                return redirect()->route('sales.quotations.edit', $quotation);
            }

            if ($request->has('Edit')) {
                session(['quote_edit_index_' . $quotation->id => (int)$request->input('Edit')]);
                session(['quote_edit_' . $quotation->id => $cart]);
                return redirect()->route('sales.quotations.edit', $quotation);
            }

            if ($request->has('UpdateItem')) {
                $line_no = session('quote_edit_index_' . $quotation->id);
                if ($line_no !== null && isset($cart['line_items'][$line_no])) {
                    $cart['line_items'][$line_no]['item_description'] = $request->input('item_description', $cart['line_items'][$line_no]['item_description']);
                    $cart['line_items'][$line_no]['quantity'] = (float)($request->input('qty', $cart['line_items'][$line_no]['quantity']));
                    $cart['line_items'][$line_no]['price'] = (float)($request->input('price', $cart['line_items'][$line_no]['price']));
                    $cart['line_items'][$line_no]['discount_percent'] = (float)($request->input('Disc', 0)) / 100;
                }
                session(['quote_edit_index_' . $quotation->id => null]);
                session(['quote_edit_' . $quotation->id => $cart]);
                return redirect()->route('sales.quotations.edit', $quotation);
            }

            if ($request->has('CancelItemChanges')) {
                session(['quote_edit_index_' . $quotation->id => null]);
                session(['quote_edit_' . $quotation->id => $cart]);
                return redirect()->route('sales.quotations.edit', $quotation);
            }

            if ($request->has('update')) {
                $cart['freight_cost'] = (float)($request->input('freight_cost', 0));
                session(['quote_edit_' . $quotation->id => $cart]);
                return redirect()->route('sales.quotations.edit', $quotation);
            }

            // Update cart fields from form
            $cart['customer_id'] = $request->input('customer_id', $cart['customer_id'] ?? $quotation->customer_id);
            $cart['branch_id'] = $request->input('branch_id', $cart['branch_id'] ?? $quotation->customer_branch_id);
            $cart['reference'] = $request->input('ref', $cart['reference'] ?? $quotation->quotation_number);
            $cart['ord_date'] = $request->input('OrderDate', $cart['ord_date'] ?? $quotation->quotation_date?->format('Y-m-d'));
            $cart['delivery_date'] = $request->input('delivery_date', $cart['delivery_date'] ?? $quotation->expiry_date?->format('Y-m-d'));
            $cart['sales_type'] = $request->input('sales_type', $cart['sales_type'] ?? $quotation->sales_type_id);
            $cart['payment'] = $request->input('payment', $cart['payment'] ?? '');
            $cart['location'] = $request->input('Location', $cart['location'] ?? $quotation->location ?? '');
            $cart['deliver_to'] = $request->input('deliver_to', $cart['deliver_to'] ?? $quotation->deliver_to ?? '');
            $cart['delivery_address'] = $request->input('delivery_address', $cart['delivery_address'] ?? $quotation->delivery_address ?? '');
            $cart['phone'] = $request->input('phone', $cart['phone'] ?? $quotation->phone ?? '');
            $cart['cust_ref'] = $request->input('cust_ref', $cart['cust_ref'] ?? $quotation->cust_ref ?? '');
            $cart['ship_via'] = $request->input('ship_via', $cart['ship_via'] ?? $quotation->ship_via ?? '');
            $cart['comments'] = $request->input('Comments', $cart['comments'] ?? $quotation->customer_notes ?? '');
            $cart['freight_cost'] = (float)($request->input('freight_cost', $cart['freight_cost'] ?? $quotation->freight_cost ?? 0));
            $cart['stock_id'] = $request->input('stock_id', $cart['stock_id'] ?? '');
            $cart['item_description'] = $request->input('item_description', $cart['item_description'] ?? '');
            $cart['qty'] = $request->input('qty', $cart['qty'] ?? 1);
            $cart['price'] = $request->input('price', $cart['price'] ?? 0);
            $cart['Disc'] = $request->input('Disc', $cart['Disc'] ?? 0);

            if ($request->has('ProcessOrder')) {
                if (empty($cart['customer_id'])) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (empty($cart['branch_id'])) {
                    return back()->with('error', 'Please select a branch.');
                }
                if (empty($cart['reference'])) {
                    return back()->with('error', 'Please enter a reference.');
                }
                if (count($cart['line_items']) == 0) {
                    return back()->with('error', 'Please add at least one item.');
                }
                if (SalesQuotation::where('quotation_number', $cart['reference'])->where('id', '!=', $quotation->id)->exists()) {
                    return back()->with('error', 'Quotation number already exists. Please use a different reference.');
                }

                $subtotal = 0;
                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    $subtotal += $lineTotal;
                }
                $total_amount = $subtotal + ($cart['freight_cost'] ?? 0);

                $quotation->update([
                    'quotation_number' => $cart['reference'],
                    'quotation_date' => $cart['ord_date'],
                    'expiry_date' => $cart['delivery_date'],
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'],
                    'sales_person_id' => $cart['sales_person_id'] ?? $quotation->sales_person_id,
                    'sales_type_id' => $cart['sales_type'] ?: 1,
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $total_amount,
                    'discount_amount' => 0,
                    'freight_cost' => $cart['freight_cost'] ?? 0,
                    'deliver_to' => $cart['deliver_to'] ?? '',
                    'delivery_address' => $cart['delivery_address'] ?? '',
                    'phone' => $cart['phone'] ?? '',
                    'cust_ref' => $cart['cust_ref'] ?? '',
                    'payment' => $cart['payment'] ?? '',
                    'location' => $cart['location'] ?? '',
                    'ship_via' => $cart['ship_via'] ?? '',
                    'customer_notes' => $cart['comments'] ?? '',
                ]);

                // Delete existing line items and re-insert
                $quotation->lineItems()->delete();
                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    $discAmt = $li['quantity'] * $li['price'] * $li['discount_percent'];
                    QuotationLineItem::create([
                        'quotation_id' => $quotation->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int)$li['quantity'],
                        'unit_price' => $li['price'],
                        'discount_percentage' => $li['discount_percent'] * 100,
                        'discount_amount' => $discAmt,
                        'line_total' => $lineTotal,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                session(['quote_edit_' . $quotation->id => null, 'quote_edit_index_' . $quotation->id => null]);
                return redirect()->route('sales.quotations.index')->with('success', 'Quotation updated successfully.');
            }

            session(['quote_edit_' . $quotation->id => $cart]);
            return redirect()->route('sales.quotations.edit', $quotation);
        }

        // GET request - load existing items into session cart
        $quotation->load('lineItems');
        $cart = session('quote_edit_' . $quotation->id) ?? [];

        if (empty($cart)) {
            $cart = [
                'customer_id' => $quotation->customer_id,
                'branch_id' => $quotation->customer_branch_id,
                'reference' => $quotation->quotation_number,
                'ord_date' => $quotation->quotation_date?->format('Y-m-d'),
                'delivery_date' => $quotation->expiry_date?->format('Y-m-d'),
                'sales_type' => $quotation->sales_type_id,
                'payment' => $quotation->payment ?? '',
                'location' => $quotation->location ?? '',
                'deliver_to' => $quotation->deliver_to ?? '',
                'delivery_address' => $quotation->delivery_address ?? '',
                'phone' => $quotation->phone ?? '',
                'cust_ref' => $quotation->cust_ref ?? '',
                'ship_via' => $quotation->ship_via ?? '',
                'comments' => $quotation->customer_notes ?? '',
                'freight_cost' => (float)($quotation->freight_cost ?? 0),
                'line_items' => [],
                'stock_id' => '',
                'item_description' => '',
                'qty' => 1,
                'price' => 0,
                'Disc' => 0,
            ];

            foreach ($quotation->lineItems as $li) {
                $cart['line_items'][] = [
                    'stock_id' => $li->item_code,
                    'item_description' => $li->description,
                    'quantity' => (float)$li->quantity,
                    'price' => (float)$li->unit_price,
                    'discount_percent' => $li->discount_percentage / 100,
                    'units' => 'each',
                ];
            }

            session(['quote_edit_' . $quotation->id => $cart]);
        }

        $customers = Customer::active()->orderBy('name')->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();
        $shippers = \App\Models\Shipper::where('inactive', false)->orderBy('shipper_name')->get();
        $items = Item::where('is_active', true)->orderBy('code')->get();
        $paymentTerms = \DB::table('payment_terms')->where('inactive', false)->orderBy('terms')->get();

        $edit_index = session('quote_edit_index_' . $quotation->id);
        $message = session('success');
        $error = session('error');

        $branches = $cart['customer_id']
            ? \App\Models\CustomerBranch::where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get()
            : [];
        $customerInfo = $cart['customer_id']
            ? Customer::with('branches')->find($cart['customer_id'])
            : null;

        if ($cart['branch_id'] && $customerInfo) {
            $branch = \App\Models\CustomerBranch::find($cart['branch_id']);
            if ($branch) {
                if (empty($cart['deliver_to'])) $cart['deliver_to'] = $branch->branch_name ?? $customerInfo->name;
                if (empty($cart['delivery_address'])) $cart['delivery_address'] = $branch->address ?? $customerInfo->address ?? '';
                if (empty($cart['phone'])) $cart['phone'] = $branch->phone ?? $customerInfo->phone ?? '';
            }
        }

        return view('sales.quotations.edit', compact(
            'customers', 'salesTypes', 'locations', 'shippers', 'items', 'paymentTerms',
            'cart', 'branches', 'customerInfo', 'edit_index', 'message', 'error', 'quotation'
        ));
    }

    public function quotationsUpdate(Request $request, SalesQuotation $quotation): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_person_id' => 'required|exists:sales_persons,id',
            'sales_type_id' => 'required|exists:sales_types,id',
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after:quotation_date',
            'customer_notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'line_items' => 'required|array|min:1',
            'line_items.*.item_id' => 'required|exists:items,id',
            'line_items.*.description' => 'nullable|string',
            'line_items.*.quantity' => 'required|numeric|min:0.01',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'line_items.*.tax_rate' => 'required|numeric|min:0',
            'line_items.*.discount_percentage' => 'required|numeric|min:0',
            'line_items.*.line_total' => 'required|numeric|min:0',
        ]);

        $quotation->update($validated);

        // Delete existing line items
        $quotation->lineItems()->delete();

        // Create new line items
        foreach ($validated['line_items'] as $lineItemData) {
            $item = Item::find($lineItemData['item_id']);
            $lineItemData['quotation_id'] = $quotation->id;
            $lineItemData['item_code'] = $item->code;
            $lineItemData['discount_amount'] = ($lineItemData['quantity'] * $lineItemData['unit_price']) * ($lineItemData['discount_percentage'] / 100);
            $lineItemData['tax_amount'] = (($lineItemData['quantity'] * $lineItemData['unit_price']) - $lineItemData['discount_amount']) * ($lineItemData['tax_rate'] / 100);
            
            QuotationLineItem::create($lineItemData);
        }

        return redirect()
            ->route('sales.quotations.index')
            ->with('success', 'Quotation updated successfully.');
    }

    public function quotationsDestroy(SalesQuotation $quotation): RedirectResponse
    {
        $quotation->delete();

        return redirect()
            ->route('sales.quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    private function generateQuotationNumber(): string
    {
        $prefix = 'SQ-';
        $lastQuotation = SalesQuotation::where('quotation_number', 'like', $prefix . '%')
            ->orderBy('quotation_number', 'desc')
            ->first();

        if ($lastQuotation) {
            $lastNumber = (int) str_replace($prefix, '', $lastQuotation->quotation_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function quotationsInquiry(Request $request): View
    {
        $query = SalesQuotation::with(['customer', 'salesPerson', 'salesType']);

        $trans_no = $request->input('OrderNumber');
        $ref = $request->input('OrderReference');
        $by_delivery = $request->boolean('by_delivery');
        $from = $request->input('OrdersAfterDate');
        $to = $request->input('OrdersToDate');
        $location = $request->input('StockLocation');
        $stock_item = $request->input('SelectStockFromList');
        $customer_id = $request->input('customer_id');
        $show_all = $request->boolean('show_all');

        // FA behavior: if OrderNumber or OrderReference is set, ignore date filters
        $hasNumberOrRef = $trans_no || $ref;

        if ($trans_no) {
            $query->where('quotation_number', 'like', '%' . $trans_no);
        } elseif ($ref) {
            $query->where('reference', 'like', '%' . $ref . '%');
        } elseif (!$hasNumberOrRef) {
            $dateField = $by_delivery ? 'expiry_date' : 'quotation_date';
            if ($from) {
                $query->where($dateField, '>=', $from);
            }
            if ($to) {
                $query->where($dateField, '<=', $to);
            }
        }

        // Location filter
        if ($location && $location !== 'all') {
            $query->where('location', $location);
        }

        // Item filter - search by item code in line items
        if ($stock_item && $stock_item !== 'all') {
            $query->whereHas('lineItems', function ($q) use ($stock_item) {
                $q->where('item_code', $stock_item);
            });
        }

        // Customer filter
        if ($customer_id && $customer_id !== 'all') {
            $query->where('customer_id', $customer_id);
        }

        // FA: for ST_SALESQUOTE when show_all is NOT checked, only show quotes where expiry_date >= today
        if (!$show_all) {
            $query->where('expiry_date', '>=', now()->toDateString());
        }

        // Hide quotations that have already been converted to a sales order
        $query->whereDoesntHave('orders');

        // Order by order_no DESC (like FA)
        $quotations = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Get totals for summary
        $totalQuotations = SalesQuotation::count();
        $acceptedCount = SalesQuotation::accepted()->count();
        $pendingCount = SalesQuotation::whereIn('status', ['draft', 'sent'])->count();
        $totalValue = SalesQuotation::sum('total_amount');

        // Get filter data
        $customers = \App\Models\Customer::active()->orderBy('name')->get();
        $items = \App\Models\Item::where('is_active', true)->orderBy('code')->get();
        $locations = \DB::table('sales_quotations')->select('location')->whereNotNull('location')->distinct()->pluck('location');
        $customerBranches = \App\Models\CustomerBranch::select('id', 'customer_id', 'branch_name', 'branch_code')->get()->groupBy('customer_id');

        return view('sales.inquiries.quotations', compact(
            'quotations', 'totalQuotations', 'acceptedCount', 'pendingCount', 'totalValue',
            'customers', 'items', 'locations', 'customerBranches'
        ));
    }

    public function ordersInquiry(Request $request): View
    {
        $query = SalesOrder::with(['customer', 'customerBranch', 'salesPerson', 'salesType']);

        $trans_no = $request->input('OrderNumber');
        $ref = $request->input('OrderReference');
        $by_delivery = $request->boolean('by_delivery');
        $from = $request->input('OrdersAfterDate');
        $to = $request->input('OrdersToDate');
        $location = $request->input('StockLocation');
        $stock_item = $request->input('SelectStockFromList');
        $customer_id = $request->input('customer_id');
        $show_voided = $request->boolean('show_voided');
        $no_auto = $request->boolean('no_auto');

        $hasNumberOrRef = $trans_no || $ref;

        if ($trans_no) {
            $query->where('order_number', 'like', '%' . $trans_no);
        } elseif ($ref) {
            $query->where('order_number', 'like', '%' . $ref . '%');
        } elseif (!$hasNumberOrRef) {
            $dateField = $by_delivery ? 'delivery_date' : 'order_date';
            if ($from) {
                $query->where($dateField, '>=', $from);
            }
            if ($to) {
                $query->where($dateField, '<=', $to);
            }
        }

        if ($location && $location !== 'all') {
            $query->where('customer_branch_id', $location);
        }

        if ($stock_item && $stock_item !== 'all') {
            $query->whereHas('lineItems', function ($q) use ($stock_item) {
                $q->where('item_code', $stock_item);
            });
        }

        if ($customer_id && $customer_id !== 'all') {
            $query->where('customer_id', $customer_id);
        }

        if ($no_auto) {
            $query->where('order_number', 'not like', 'auto%');
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalOrders = SalesOrder::count();
        $completedCount = SalesOrder::whereIn('status', ['delivered', 'completed'])->count();
        $inProgressCount = SalesOrder::whereIn('status', ['confirmed', 'in_progress'])->count();
        $totalValue = SalesOrder::sum('total_amount');

        $customers = \App\Models\Customer::active()->orderBy('name')->get();
        $items = \App\Models\Item::where('is_active', true)->orderBy('code')->get();
        $locations = \App\Models\CustomerBranch::select('id', 'branch_name')->orderBy('branch_name')->get();

        return view('sales.inquiries.orders', compact(
            'orders', 'totalOrders', 'completedCount', 'inProgressCount', 'totalValue',
            'customers', 'items', 'locations'
        ));
    }

    public function transactionsInquiry(Request $request): View
    {
        $customers = \App\Models\Customer::active()->orderBy('name')->get();
        $selectedCustomer = $request->input('customer_id');
        $typeFilter = $request->input('type_filter');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $transactions = collect();

        if ($request->anyFilled(['customer_id', 'type_filter', 'date_from', 'date_to'])) {
            // Sales Invoices (sales_orders with status=invoiced)
            if (!$typeFilter || $typeFilter === 'all' || $typeFilter === 'invoices') {
                $iq = SalesOrder::with('customer')
                    ->where('status', 'invoiced')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('order_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('order_date', '<=', $dateTo));
                $iq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $o) {
                        $unpaid = $o->total_amount > $o->paid_amount;
                        $transactions->push([
                            'date' => $o->order_date,
                            'type' => 'invoice',
                            'type_label' => 'Invoice',
                            'type_class' => 'bg-blue-100 text-blue-800',
                            'reference' => $o->order_number,
                            'description' => 'Invoice for ' . ($o->customer->name ?? 'N/A'),
                            'debit' => $o->total_amount,
                            'credit' => 0,
                            'amount' => $o->total_amount - $o->paid_amount,
                            'status' => $unpaid ? 'unpaid' : 'paid',
                            'status_label' => $unpaid ? 'Unpaid' : 'Paid',
                            'status_class' => $unpaid ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800',
                            'url_gl' => null,
                            'url_edit' => route('sales.orders.edit', $o),
                            'url_copy' => route('sales.orders.create', ['copy_from' => $o->id]),
                            'copy_label' => 'Copy Invoice',
                            'url_credit' => $unpaid ? route('sales.credit-invoice', ['InvoiceNumber' => $o->id]) : null,
                            'credit_label' => 'Credit This',
                            'credit_icon' => 'fa-credit-card',
                            'url_view' => route('sales.orders.show', $o),
                            'url_print' => route('sales.orders.show', [$o, 'print' => 1]),
                            'sort_date' => $o->order_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Payments (customer_payments table)
            if (!$typeFilter || $typeFilter === 'all' || $typeFilter === 'payments') {
                $pq = \App\Models\CustomerPayment::with('customer')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('payment_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('payment_date', '<=', $dateTo));
                $pq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $p) {
                        $transactions->push([
                            'date' => $p->payment_date,
                            'type' => 'payment',
                            'type_label' => 'Payment',
                            'type_class' => 'bg-green-100 text-green-800',
                            'reference' => $p->payment_number,
                            'description' => 'Payment from ' . ($p->customer->name ?? 'N/A') . ($p->reference ? ' (' . $p->reference . ')' : ''),
                            'debit' => 0,
                            'credit' => $p->amount,
                            'amount' => $p->amount,
                            'status' => $p->status,
                            'status_label' => ucfirst($p->status),
                            'status_class' => 'bg-green-100 text-green-800',
                            'url_gl' => null,
                            'url_edit' => null,
                            'url_copy' => null,
                            'url_credit' => null,
                            'url_view' => null,
                            'url_print' => route('sales.payments.print', $p),
                            'sort_date' => $p->payment_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Unsettled transactions (invoices with unpaid balance)
            if (!$typeFilter || $typeFilter === 'all' || $typeFilter === 'unsettled') {
                $uq = SalesOrder::with('customer')
                    ->where('status', 'invoiced')
                    ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('order_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('order_date', '<=', $dateTo));
                $uq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $o) {
                        $transactions->push([
                            'date' => $o->order_date,
                            'type' => 'unsettled',
                            'type_label' => 'Unsettled',
                            'type_class' => 'bg-red-100 text-red-800',
                            'reference' => $o->order_number,
                            'description' => 'Unsettled invoice for ' . ($o->customer->name ?? 'N/A'),
                            'debit' => $o->total_amount - $o->paid_amount,
                            'credit' => 0,
                            'amount' => $o->total_amount - $o->paid_amount,
                            'status' => 'unsettled',
                            'status_label' => 'Unsettled',
                            'status_class' => 'bg-red-100 text-red-800',
                            'url_gl' => null,
                            'url_edit' => route('sales.orders.edit', $o),
                            'url_copy' => route('sales.orders.create', ['copy_from' => $o->id]),
                            'copy_label' => 'Copy Invoice',
                            'url_credit' => route('sales.credit-invoice', ['InvoiceNumber' => $o->id]),
                            'credit_label' => 'Credit This',
                            'credit_icon' => 'fa-credit-card',
                            'url_view' => route('sales.orders.show', $o),
                            'url_print' => route('sales.orders.show', [$o, 'print' => 1]),
                            'sort_date' => $o->order_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Credit Notes (credit_notes table)
            if (!$typeFilter || $typeFilter === 'all' || $typeFilter === 'credits') {
                $cq = \App\Models\CreditNote::with('customer')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('credit_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('credit_date', '<=', $dateTo));
                $cq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $c) {
                        $transactions->push([
                            'date' => $c->credit_date,
                            'type' => 'credit',
                            'type_label' => 'Credit Note',
                            'type_class' => 'bg-orange-100 text-orange-800',
                            'reference' => $c->credit_note_number,
                            'description' => ($c->reason ?? 'Credit') . ' for ' . ($c->customer->name ?? 'N/A'),
                            'debit' => 0,
                            'credit' => $c->total_amount,
                            'amount' => $c->total_amount,
                            'status' => $c->status,
                            'status_label' => ucfirst($c->status),
                            'status_class' => $c->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                            'url_gl' => null,
                            'url_edit' => null,
                            'url_copy' => null,
                            'url_credit' => $c->sales_order_id ? route('sales.credit-invoice', ['InvoiceNumber' => $c->sales_order_id]) : null,
                            'credit_label' => 'Credit This',
                            'credit_icon' => 'fa-credit-card',
                            'url_view' => null,
                            'url_print' => route('sales.credit-notes.print', $c),
                            'sort_date' => $c->credit_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Delivery Notes (sales_orders with status=delivered)
            if (!$typeFilter || $typeFilter === 'all' || $typeFilter === 'deliveries') {
                $dq = SalesOrder::with('customer')
                    ->where('status', 'delivered')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('order_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('order_date', '<=', $dateTo));
                $dq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $o) {
                        $transactions->push([
                            'date' => $o->delivery_date ?? $o->order_date,
                            'type' => 'delivery',
                            'type_label' => 'Delivery Note',
                            'type_class' => 'bg-cyan-100 text-cyan-800',
                            'reference' => $o->order_number,
                            'description' => 'Delivery for ' . ($o->customer->name ?? 'N/A'),
                            'debit' => 0,
                            'credit' => 0,
                            'amount' => $o->total_amount,
                            'status' => 'delivered',
                            'status_label' => 'Delivered',
                            'status_class' => 'bg-green-100 text-green-800',
                            'url_gl' => null,
                            'url_edit' => route('sales.orders.edit', $o),
                            'url_copy' => route('sales.delivery.from-order', ['order_id' => $o->id]),
                            'copy_label' => 'Copy Delivery',
                            'url_credit' => route('sales.invoice.from-delivery', ['delivery_id' => $o->id]),
                            'credit_label' => 'Invoice',
                            'credit_icon' => 'fa-file-invoice',
                            'url_view' => route('sales.orders.show', $o),
                            'url_print' => route('sales.orders.show', [$o, 'print' => 'delivery']),
                            'sort_date' => ($o->delivery_date ?? $o->order_date)?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Journal Entries (journal_entries table)
            if (!$typeFilter || $typeFilter === 'all' || $typeFilter === 'journal') {
                $jq = \App\Models\JournalEntry::with('company')
                    ->where('is_posted', true)
                    ->when($dateFrom, fn($q) => $q->where('entry_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('entry_date', '<=', $dateTo));
                $jq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $j) {
                        $transactions->push([
                            'date' => $j->entry_date,
                            'type' => 'journal',
                            'type_label' => 'Journal Entry',
                            'type_class' => 'bg-gray-100 text-gray-800',
                            'reference' => $j->entry_number,
                            'description' => $j->description ?? 'Journal entry',
                            'debit' => $j->total_debit,
                            'credit' => $j->total_credit,
                            'amount' => $j->total_debit - $j->total_credit,
                            'status' => $j->is_posted ? 'posted' : 'draft',
                            'status_label' => $j->is_posted ? 'Posted' : 'Draft',
                            'status_class' => $j->is_posted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                            'url_gl' => null,
                            'url_edit' => null,
                            'url_copy' => null,
                            'url_credit' => null,
                            'url_view' => null,
                            'url_print' => null,
                            'sort_date' => $j->entry_date instanceof \Carbon\Carbon ? $j->entry_date->format('Y-m-d') : date('Y-m-d', strtotime($j->entry_date)),
                        ]);
                    }
                });
            }
        }

        $transactions = $transactions->sortByDesc('sort_date');

        // Calculate running balance (ordered chronologically ascending for running balance)
        $sortedAsc = $transactions->sortBy('sort_date');
        $runningBalance = 0;
        $balanceMap = [];
        foreach ($sortedAsc as $key => $t) {
            $runningBalance += $t['debit'] - $t['credit'];
            $balanceMap[$key] = $runningBalance;
        }

        // Attach running balance to each transaction
        $transactions = $transactions->map(function ($t, $key) use ($balanceMap) {
            $t['running_balance'] = $balanceMap[$key] ?? 0;
            return $t;
        })->values();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $balance = $totalDebit - $totalCredit;

        $countInvoices = $transactions->where('type', 'invoice')->count();
        $countPayments = $transactions->where('type', 'payment')->count();
        $countCredits = $transactions->where('type', 'credit')->count();
        $countUnsettled = $transactions->where('type', 'unsettled')->count();
        $countDeliveries = $transactions->where('type', 'delivery')->count();
        $countJournal = $transactions->where('type', 'journal')->count();

        $page = Paginator::resolveCurrentPage('page');
        $perPage = 20;
        $slice = $transactions->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator($slice, $transactions->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('sales.inquiries.transactions', compact(
            'customers', 'paginated', 'totalDebit', 'totalCredit', 'balance',
            'countInvoices', 'countPayments', 'countCredits', 'countUnsettled', 'countDeliveries', 'countJournal',
            'selectedCustomer', 'typeFilter', 'dateFrom', 'dateTo'
        ));
    }

    public function allocationInquiry(Request $request): View
    {
        $customers = \App\Models\Customer::active()->orderBy('name')->get();
        $selectedCustomer = $request->input('customer_id', 'all');
        $dateFrom = $request->input('TransAfterDate');
        $dateTo = $request->input('TransToDate');
        $filterType = $request->input('filterType', 'all');
        $showSettled = $request->boolean('showSettled');

        $transactions = collect();

        if ($request->anyFilled(['customer_id', 'TransAfterDate', 'TransToDate', 'filterType'])) {
            // Invoices (sales_orders status=invoiced)
            if ($filterType === 'all' || $filterType === '0' || $filterType === '1' || $filterType === '2') {
                $iq = \App\Models\SalesOrder::with('customer')
                    ->where('status', 'invoiced')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('order_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('order_date', '<=', $dateTo));
                if (!$showSettled) {
                    $iq->whereRaw('total_amount > COALESCE(paid_amount, 0)');
                }
                $iq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $o) {
                        $balance = $o->total_amount - ($o->paid_amount ?? 0);
                        $dueDate = $o->delivery_date;
                        $overdue = $dueDate && \Carbon\Carbon::parse($dueDate)->isPast() && $balance > 0;
                        $transactions->push([
                            'type' => 1, // ST_SALESINVOICE
                            'type_label' => 'Invoice',
                            'type_class' => 'bg-blue-100 text-blue-800',
                            'trans_no' => $o->id,
                            'reference' => $o->order_number,
                            'order_' => null,
                            'debtor_no' => $o->customer_id,
                            'date' => $o->order_date,
                            'due_date' => $dueDate,
                            'customer_name' => $o->customer->name ?? '',
                            'currency' => 'USD',
                            'total_amount' => $o->total_amount,
                            'allocated' => (float)($o->paid_amount ?? 0),
                            'balance' => $balance,
                            'overdue' => $overdue,
                            'url_view' => route('sales.orders.show', $o),
                            'sort_date' => $o->order_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Deliveries (sales_orders status=delivered)
            if ($filterType === 'all' || $filterType === '3') {
                $dq = \App\Models\SalesOrder::with('customer')
                    ->where('status', 'delivered')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('order_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('order_date', '<=', $dateTo));
                if (!$showSettled) {
                    $dq->whereRaw('total_amount > COALESCE(paid_amount, 0)');
                }
                $dq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $o) {
                        $balance = $o->total_amount - ($o->paid_amount ?? 0);
                        $transactions->push([
                            'type' => 4, // ST_CUSTDELIVERY
                            'type_label' => 'Delivery',
                            'type_class' => 'bg-cyan-100 text-cyan-800',
                            'trans_no' => $o->id,
                            'reference' => $o->order_number,
                            'order_' => null,
                            'debtor_no' => $o->customer_id,
                            'date' => $o->delivery_date ?? $o->order_date,
                            'due_date' => null,
                            'customer_name' => $o->customer->name ?? '',
                            'currency' => 'USD',
                            'total_amount' => $o->total_amount,
                            'allocated' => (float)($o->paid_amount ?? 0),
                            'balance' => $balance,
                            'overdue' => false,
                            'url_view' => route('sales.orders.show', $o),
                            'sort_date' => ($o->delivery_date ?? $o->order_date)?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Payments (customer_payments)
            if ($filterType === 'all' || $filterType === '4' || $filterType === '5') {
                $pq = \App\Models\CustomerPayment::with('customer')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('payment_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('payment_date', '<=', $dateTo));
                if (!$showSettled) {
                    $pq->whereRaw('amount > COALESCE((SELECT COALESCE(SUM(amount),0) FROM customer_payment_allocations WHERE customer_payment_id = customer_payments.id), 0)');
                }
                $pq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $p) {
                        $allocated = (float)\DB::table('customer_payment_allocations')
                            ->where('customer_payment_id', $p->id)
                            ->sum('amount');
                        $transactions->push([
                            'type' => $p->amount >= 0 ? 5 : 6, // ST_CUSTPAYMENT : ST_BANKDEPOSIT
                            'type_label' => $p->amount >= 0 ? 'Payment' : 'Deposit',
                            'type_class' => $p->amount >= 0 ? 'bg-green-100 text-green-800' : 'bg-teal-100 text-teal-800',
                            'trans_no' => $p->id,
                            'reference' => $p->payment_number,
                            'order_' => null,
                            'debtor_no' => $p->customer_id,
                            'date' => $p->payment_date,
                            'due_date' => null,
                            'customer_name' => $p->customer->name ?? '',
                            'currency' => $p->currency ?? 'USD',
                            'total_amount' => $p->amount,
                            'allocated' => $allocated,
                            'balance' => $p->amount - $allocated,
                            'overdue' => false,
                            'url_view' => null,
                            'sort_date' => $p->payment_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Credit Notes
            if ($filterType === 'all' || $filterType === '6') {
                $cq = \App\Models\CreditNote::with('customer')
                    ->when($selectedCustomer && $selectedCustomer !== 'all', fn($q) => $q->where('customer_id', $selectedCustomer))
                    ->when($dateFrom, fn($q) => $q->where('credit_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('credit_date', '<=', $dateTo));
                if (!$showSettled) {
                    $cq->whereRaw('total_amount > COALESCE((SELECT COALESCE(SUM(amount),0) FROM customer_payment_allocations WHERE sales_order_id = credit_notes.sales_order_id AND customer_payment_id IN (SELECT id FROM customer_payments WHERE customer_id = credit_notes.customer_id)), 0)');
                }
                $cq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $c) {
                        $allocated = 0;
                        if ($c->sales_order_id) {
                            $allocated = (float)\DB::table('customer_payment_allocations')
                                ->where('sales_order_id', $c->sales_order_id)
                                ->whereIn('customer_payment_id', function ($q) use ($c) {
                                    $q->select('id')->from('customer_payments')->where('customer_id', $c->customer_id);
                                })->sum('amount');
                        }
                        $transactions->push([
                            'type' => 7, // ST_CUSTCREDIT
                            'type_label' => 'Credit Note',
                            'type_class' => 'bg-orange-100 text-orange-800',
                            'trans_no' => $c->id,
                            'reference' => $c->credit_note_number,
                            'order_' => null,
                            'debtor_no' => $c->customer_id,
                            'date' => $c->credit_date,
                            'due_date' => null,
                            'customer_name' => $c->customer->name ?? '',
                            'currency' => 'USD',
                            'total_amount' => $c->total_amount,
                            'allocated' => $allocated,
                            'balance' => $c->total_amount - $allocated,
                            'overdue' => false,
                            'url_view' => null,
                            'sort_date' => $c->credit_date?->format('Y-m-d') ?? '0000-00-00',
                        ]);
                    }
                });
            }

            // Journal Entries
            if ($filterType === 'all' || $filterType === '7') {
                $jq = \App\Models\JournalEntry::where('is_posted', true)
                    ->when($dateFrom, fn($q) => $q->where('entry_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('entry_date', '<=', $dateTo));
                if (!$showSettled) {
                    $jq->whereRaw('ABS(total_debit - total_credit) > 0');
                }
                $jq->chunk(200, function ($chunk) use (&$transactions) {
                    foreach ($chunk as $j) {
                        $transactions->push([
                            'type' => 8, // ST_JOURNAL
                            'type_label' => 'Journal',
                            'type_class' => 'bg-gray-100 text-gray-800',
                            'trans_no' => $j->id,
                            'reference' => $j->entry_number,
                            'order_' => null,
                            'debtor_no' => null,
                            'date' => $j->entry_date,
                            'due_date' => null,
                            'customer_name' => '',
                            'currency' => 'USD',
                            'total_amount' => $j->total_debit - $j->total_credit,
                            'allocated' => 0,
                            'balance' => $j->total_debit - $j->total_credit,
                            'overdue' => false,
                            'url_view' => null,
                            'sort_date' => $j->entry_date instanceof \Carbon\Carbon ? $j->entry_date->format('Y-m-d') : date('Y-m-d', strtotime($j->entry_date)),
                        ]);
                    }
                });
            }
        }

        $transactions = $transactions->sortByDesc('sort_date');

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $perPage = 20;
        $slice = $transactions->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator($slice, $transactions->count(), $perPage, $page, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('sales.inquiries.allocation', compact(
            'customers', 'paginated', 'selectedCustomer', 'dateFrom', 'dateTo', 'filterType', 'showSettled'
        ));
    }

    public function customerAllocate(Request $request): View|RedirectResponse
    {
        // Handle POST actions
        if ($request->isMethod('POST')) {
            $cart = session('customer_alloc_cart');

            if ($request->has('Cancel')) {
                session(['customer_alloc_cart' => null]);
                return redirect()->route('sales.inquiries.allocation');
            }

            if ($request->has('Process')) {
                if (!$cart) {
                    return back()->with('error', 'Session expired.');
                }

                $totalAllocated = 0;
                $numAllocs = (int)$request->input('TotalNumberOfAllocs', 0);

                for ($i = 0; $i < $numAllocs; $i++) {
                    $allocAmt = (float)($request->input('amount' . $i, 0));
                    $unAlloc = (float)($request->input('un_allocated' . $i, 0));

                    if ($allocAmt < 0) {
                        return back()->with('error', 'Allocation amount cannot be negative.');
                    }
                    if ($allocAmt > $unAlloc) {
                        return back()->with('error', 'One or more transactions are overallocated.');
                    }

                    if (isset($cart['allocs'][$i])) {
                        $cart['allocs'][$i]['current_allocated'] = $allocAmt;
                    }
                    $totalAllocated += $allocAmt;
                }

                $amount = abs($cart['amount']);
                if ($totalAllocated > $amount + 0.005) {
                    return back()->with('error', 'Amount allocated exceeds total amount left to allocate.');
                }

                // Write allocations to DB
                \DB::beginTransaction();
                try {
                    // Clear existing allocations for this source transaction
                    if ($cart['trans_type'] == 5 || $cart['trans_type'] == 6) {
                        \DB::table('customer_payment_allocations')
                            ->where('source_type', 'payment')
                            ->where('customer_payment_id', $cart['trans_no'])
                            ->delete();
                    } elseif ($cart['trans_type'] == 7) {
                        \DB::table('customer_payment_allocations')
                            ->where('source_type', 'credit_note')
                            ->where('credit_note_id', $cart['trans_no'])
                            ->delete();
                    }

                    // Insert new allocations
                    foreach ($cart['allocs'] as $alloc) {
                        if ($alloc['current_allocated'] > 0) {
                            $allocData = [
                                'sales_order_id' => $alloc['type_no'],
                                'amount' => $alloc['current_allocated'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            if ($cart['trans_type'] == 5 || $cart['trans_type'] == 6) {
                                $allocData['source_type'] = 'payment';
                                $allocData['customer_payment_id'] = $cart['trans_no'];
                            } elseif ($cart['trans_type'] == 7) {
                                $allocData['source_type'] = 'credit_note';
                                $allocData['credit_note_id'] = $cart['trans_no'];
                            }
                            \DB::table('customer_payment_allocations')->insert($allocData);

                            // Update invoice paid_amount
                            $invoice = \App\Models\SalesOrder::find($alloc['type_no']);
                            if ($invoice) {
                                $totalAlloc = (float)\DB::table('customer_payment_allocations')
                                    ->where('sales_order_id', $invoice->id)
                                    ->sum('amount');
                                $invoice->update(['paid_amount' => $totalAlloc]);
                            }
                        }
                    }

                    \DB::commit();
                } catch (\Exception $e) {
                    \DB::rollBack();
                    return back()->with('error', 'Failed to process allocations: ' . $e->getMessage());
                }

                session(['customer_alloc_cart' => null]);
                return redirect()->route('sales.inquiries.allocation')->with('success', 'Allocations processed successfully.');
            }

            if ($request->has('UpdateDisplay')) {
                // Re-read from DB (refresh)
                if ($cart) {
                    $cart = $this->loadAllocatableTransactions($cart['customer_id'], $cart['trans_type'], $cart['trans_no']);
                    session(['customer_alloc_cart' => $cart]);
                }
                return redirect()->route('sales.allocations.customer-allocate');
            }
        }

        // GET: Initialize from trans_no, trans_type, debtor_no
        $transNo = $request->input('trans_no');
        $transType = $request->input('trans_type');
        $debtorNo = $request->input('debtor_no');

        if ($transNo && $transType && $debtorNo) {
            $cart = $this->initAllocateCart($transNo, $transType, $debtorNo);
            if (!$cart) {
                return redirect()->route('sales.inquiries.allocation')->with('error', 'Invalid transaction or customer.');
            }
            session(['customer_alloc_cart' => $cart]);
        }

        $cart = session('customer_alloc_cart');
        if (!$cart) {
            return redirect()->route('sales.inquiries.allocation')->with('error', 'This page can only be opened if a transaction has been selected for allocation.');
        }

        $totalAllocated = 0;
        foreach ($cart['allocs'] as $alloc) {
            $totalAllocated += $alloc['current_allocated'];
        }

        $systypes = [
            1 => 'Invoice', 3 => 'Delivery', 4 => 'Payment', 5 => 'Deposit',
            7 => 'Credit Note', 8 => 'Journal',
        ];

        return view('sales.allocations.customer-allocate', compact('cart', 'totalAllocated', 'systypes'));
    }

    private function initAllocateCart($transNo, $transType, $debtorNo): ?array
    {
        $customer = \App\Models\Customer::find($debtorNo);
        if (!$customer) return null;

        $cart = [
            'trans_no' => $transNo,
            'trans_type' => $transType,
            'customer_id' => $debtorNo,
            'customer_name' => $customer->name,
            'currency' => 'USD',
            'date' => null,
            'amount' => 0,
            'bank_amount' => 0,
            'allocs' => [],
        ];

        // Load source transaction data
        if (in_array($transType, [5, 6])) {
            $payment = \App\Models\CustomerPayment::find($transNo);
            if (!$payment || $payment->customer_id != $debtorNo) return null;
            $cart['date'] = $payment->payment_date?->format('Y-m-d');
            $cart['amount'] = $payment->amount;
            $cart['bank_amount'] = $payment->bank_amount;
        } elseif ($transType == 7) {
            $creditNote = \App\Models\CreditNote::find($transNo);
            if (!$creditNote || $creditNote->customer_id != $debtorNo) return null;
            $cart['date'] = $creditNote->credit_date?->format('Y-m-d');
            $cart['amount'] = $creditNote->total_amount;
            $cart['bank_amount'] = $creditNote->total_amount;
        } elseif ($transType == 8) {
            $journal = \App\Models\JournalEntry::find($transNo);
            if (!$journal) return null;
            $cart['date'] = $journal->entry_date?->format('Y-m-d');
            $cart['amount'] = $journal->total_debit - $journal->total_credit;
            $cart['bank_amount'] = $cart['amount'];
        }

        $cart = $this->loadAllocatableTransactions($debtorNo, $transType, $transNo, $cart);

        return $cart;
    }

    private function loadAllocatableTransactions($customerId, $sourceType, $sourceNo, $cart = null): array
    {
        if (!$cart) {
            $cart = session('customer_alloc_cart') ?? [];
            $cart['customer_id'] = $customerId;
        }

        $cart['allocs'] = [];

        // Get existing allocations for this source transaction
        $existingAllocs = collect();
        if (in_array($sourceType, [5, 6])) {
            $existingAllocs = \DB::table('customer_payment_allocations')
                ->where('source_type', 'payment')
                ->where('customer_payment_id', $sourceNo)
                ->get();
        } elseif ($sourceType == 7) {
            $existingAllocs = \DB::table('customer_payment_allocations')
                ->where('source_type', 'credit_note')
                ->where('credit_note_id', $sourceNo)
                ->get();
        }

        $existingByInvoice = [];
        foreach ($existingAllocs as $ea) {
            $existingByInvoice[$ea->sales_order_id] = (float)$ea->amount;
        }

        // Get all invoices for this customer with outstanding balance
        $invoices = \App\Models\SalesOrder::where('customer_id', $customerId)
            ->where('status', 'invoiced')
            ->orderBy('order_date', 'asc')
            ->get();

        foreach ($invoices as $inv) {
            $totalAllocForInvoice = (float)\DB::table('customer_payment_allocations')
                ->where('sales_order_id', $inv->id)
                ->sum('amount');

            $balance = $inv->total_amount - $totalAllocForInvoice;

            if ($balance > 0) {
                $currentAlloc = $existingByInvoice[$inv->id] ?? 0;
                $cart['allocs'][] = [
                    'type' => 1,
                    'type_no' => $inv->id,
                    'ref' => $inv->order_number,
                    'date' => $inv->order_date?->format('Y-m-d'),
                    'due_date' => $inv->delivery_date?->format('Y-m-d'),
                    'amount' => $inv->total_amount,
                    'amount_allocated' => $totalAllocForInvoice,
                    'current_allocated' => $currentAlloc,
                    'balance' => $balance,
                ];
            }
        }

        // Auto-assign unallocated amount to earliest transactions (like FA)
        $sourceAmount = abs($cart['amount'] ?? 0);
        $alreadyAllocated = array_sum(array_column($cart['allocs'], 'current_allocated'));
        $remainingToAssign = $sourceAmount - $alreadyAllocated;

        if ($remainingToAssign > 0) {
            foreach ($cart['allocs'] as &$alloc) {
                if ($remainingToAssign <= 0) break;
                $allocatable = $alloc['balance'] - $alloc['current_allocated'];
                if ($allocatable > 0) {
                    $assign = min($remainingToAssign, $allocatable);
                    $alloc['current_allocated'] += $assign;
                    $remainingToAssign -= $assign;
                }
            }
            unset($alloc);
        }

        return $cart;
    }

    // Sales Orders
    public function ordersIndex(): View
    {
        $orders = SalesOrder::with(['customer', 'salesPerson'])
            ->latest()
            ->paginate(10);

        return view('sales.orders.index', compact('orders'));
    }

    public function ordersCreate(Request $request)
    {
        // Handle POST cart operations
        if ($request->isMethod('POST')) {
            $cart = session('order_items', []);

            if ($request->has('CancelOrder')) {
                session(['order_items' => null, 'order_edit_index' => null]);
                return redirect()->route('sales.orders.index');
            }

            if ($request->has('AddItem')) {
                $stock_id = $request->input('stock_id');
                if (!$stock_id) {
                    return back()->with('error', 'Please select an item.');
                }
                $item = \App\Models\Item::where('code', $stock_id)->first();
                $line_item = [
                    'stock_id' => $stock_id,
                    'item_description' => $request->input('item_description', $item->name ?? ''),
                    'quantity' => (float)($request->input('qty', 1)),
                    'price' => (float)($request->input('price', 0)),
                    'discount_percent' => (float)($request->input('Disc', 0)) / 100,
                    'units' => $item->unit_of_measure ?? 'each',
                ];
                $cart['line_items'][] = $line_item;
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                $cart['price'] = 0;
                $cart['Disc'] = 0;
                session(['order_items' => $cart]);
                return redirect()->route('sales.orders.create');
            }

            if ($request->has('Delete')) {
                $line_no = (int)$request->input('Delete');
                if (isset($cart['line_items'][$line_no])) {
                    unset($cart['line_items'][$line_no]);
                    $cart['line_items'] = array_values($cart['line_items']);
                }
                session(['order_items' => $cart]);
                return redirect()->route('sales.orders.create');
            }

            if ($request->has('Edit')) {
                session(['order_edit_index' => (int)$request->input('Edit')]);
                session(['order_items' => $cart]);
                return redirect()->route('sales.orders.create');
            }

            if ($request->has('UpdateItem')) {
                $line_no = session('order_edit_index');
                if ($line_no !== null && isset($cart['line_items'][$line_no])) {
                    $cart['line_items'][$line_no]['item_description'] = $request->input('item_description', $cart['line_items'][$line_no]['item_description']);
                    $cart['line_items'][$line_no]['quantity'] = (float)($request->input('qty', $cart['line_items'][$line_no]['quantity']));
                    $cart['line_items'][$line_no]['price'] = (float)($request->input('price', $cart['line_items'][$line_no]['price']));
                    $cart['line_items'][$line_no]['discount_percent'] = (float)($request->input('Disc', 0)) / 100;
                }
                session(['order_edit_index' => null]);
                session(['order_items' => $cart]);
                return redirect()->route('sales.orders.create');
            }

            if ($request->has('CancelItemChanges')) {
                session(['order_edit_index' => null]);
                session(['order_items' => $cart]);
                return redirect()->route('sales.orders.create');
            }

            if ($request->has('update')) {
                $cart['freight_cost'] = (float)($request->input('freight_cost', 0));
                session(['order_items' => $cart]);
                return redirect()->route('sales.orders.create');
            }

            // Update cart fields from form
            $cart['customer_id'] = $request->input('customer_id', $cart['customer_id'] ?? '');
            $cart['branch_id'] = $request->input('branch_id', $cart['branch_id'] ?? '');
            $cart['reference'] = $request->input('ref', $cart['reference'] ?? '');
            $cart['ord_date'] = $request->input('OrderDate', $cart['ord_date'] ?? now()->format('Y-m-d'));
            $cart['delivery_date'] = $request->input('delivery_date', $cart['delivery_date'] ?? '');
            $cart['sales_type'] = $request->input('sales_type', $cart['sales_type'] ?? '');
            $cart['payment'] = $request->input('payment', $cart['payment'] ?? '');
            $cart['location'] = $request->input('Location', $cart['location'] ?? '');
            $cart['deliver_to'] = $request->input('deliver_to', $cart['deliver_to'] ?? '');
            $cart['delivery_address'] = $request->input('delivery_address', $cart['delivery_address'] ?? '');
            $cart['phone'] = $request->input('phone', $cart['phone'] ?? '');
            $cart['cust_ref'] = $request->input('cust_ref', $cart['cust_ref'] ?? '');
            $cart['ship_via'] = $request->input('ship_via', $cart['ship_via'] ?? '');
            $cart['comments'] = $request->input('Comments', $cart['comments'] ?? '');
            $cart['freight_cost'] = (float)($request->input('freight_cost', $cart['freight_cost'] ?? 0));
            $cart['stock_id'] = $request->input('stock_id', $cart['stock_id'] ?? '');
            $cart['item_description'] = $request->input('item_description', $cart['item_description'] ?? '');
            $cart['qty'] = $request->input('qty', $cart['qty'] ?? 1);
            $cart['price'] = $request->input('price', $cart['price'] ?? 0);
            $cart['Disc'] = $request->input('Disc', $cart['Disc'] ?? 0);

            if ($request->has('ProcessOrder')) {
                if (empty($cart['customer_id'])) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (empty($cart['branch_id'])) {
                    return back()->with('error', 'Please select a branch.');
                }
                if (empty($cart['reference'])) {
                    return back()->with('error', 'Please enter a reference.');
                }
                if (count($cart['line_items']) == 0) {
                    return back()->with('error', 'Please add at least one item.');
                }
                if (SalesOrder::where('order_number', $cart['reference'])->exists()) {
                    return back()->with('error', 'Order number already exists. Please use a different reference.');
                }

                $subtotal = 0;
                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    $subtotal += $lineTotal;
                }
                $total_amount = $subtotal + $cart['freight_cost'];

                $order = SalesOrder::create([
                    'order_number' => $cart['reference'],
                    'order_date' => $cart['ord_date'],
                    'delivery_date' => $cart['delivery_date'],
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'],
                    'sales_person_id' => 1,
                    'sales_type_id' => $cart['sales_type'] ?: 1,
                    'payment' => $cart['payment'] ?? '',
                    'location' => $cart['location'] ?? '',
                    'ship_via' => $cart['ship_via'] ?? '',
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $total_amount,
                    'discount_amount' => 0,
                    'delivery_address' => $cart['delivery_address'],
                    'customer_notes' => $cart['comments'],
                    'internal_notes' => $cart['cust_ref'],
                    'quotation_id' => $cart['quotation_id'] ?? null,
                ]);

                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    OrderLineItem::create([
                        'order_id' => $order->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int)$li['quantity'],
                        'unit_price' => $li['price'],
                        'discount_percentage' => $li['discount_percent'] * 100,
                        'discount_amount' => $li['quantity'] * $li['price'] * $li['discount_percent'],
                        'line_total' => $lineTotal,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                session(['order_items' => null, 'order_edit_index' => null]);
                return redirect()->route('sales.orders.create', ['AddedID' => $order->id]);
            }

            session(['order_items' => $cart]);
            return redirect()->route('sales.orders.create');
        }

        // GET request - show the form
        $cart = session('order_items') ?? [];

        // Pre-populate from quotation if specified
        $quotationId = $request->input('quotation_id');
        if ($quotationId && empty($cart)) {
            $quotation = SalesQuotation::with('lineItems')->find($quotationId);
            if ($quotation) {
                $cart['customer_id'] = $quotation->customer_id;
                $cart['branch_id'] = $quotation->customer_branch_id;
                $cart['reference'] = $quotation->quotation_number;
                $cart['ord_date'] = $quotation->quotation_date?->format('Y-m-d') ?? now()->format('Y-m-d');
                $cart['delivery_date'] = $quotation->expiry_date?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d');
                $cart['sales_type'] = $quotation->sales_type_id;
                $cart['payment'] = $quotation->payment ?? '';
                $cart['location'] = $quotation->location ?? '';
                $cart['deliver_to'] = $quotation->deliver_to ?? '';
                $cart['delivery_address'] = $quotation->delivery_address ?? '';
                $cart['phone'] = $quotation->phone ?? '';
                $cart['cust_ref'] = $quotation->cust_ref ?? '';
                $cart['ship_via'] = $quotation->ship_via ?? '';
                $cart['comments'] = $quotation->customer_notes ?? '';
                $cart['freight_cost'] = (float)($quotation->freight_cost ?? 0);
                $cart['quotation_id'] = $quotation->id;
                $cart['line_items'] = [];
                foreach ($quotation->lineItems as $li) {
                    $cart['line_items'][] = [
                        'stock_id' => $li->item_code,
                        'item_description' => $li->description,
                        'quantity' => (float)$li->quantity,
                        'price' => (float)$li->unit_price,
                        'discount_percent' => ($li->discount_percentage ?? 0) / 100,
                        'units' => 'each',
                    ];
                }
                session(['order_items' => $cart]);
            }
        }

        // Pre-populate from existing order if copy_from is specified
        $copyFromId = $request->input('copy_from');
        if ($copyFromId && empty($cart)) {
            $sourceOrder = SalesOrder::with('lineItems', 'customer')->find($copyFromId);
            if ($sourceOrder) {
                $cart['customer_id'] = $sourceOrder->customer_id;
                $cart['branch_id'] = $sourceOrder->customer_branch_id;
                $cart['reference'] = '';
                $cart['ord_date'] = now()->format('Y-m-d');
                $cart['delivery_date'] = now()->addDays(7)->format('Y-m-d');
                $cart['sales_type'] = $sourceOrder->sales_type_id;
                $cart['payment'] = $sourceOrder->payment ?? '';
                $cart['location'] = $sourceOrder->location ?? '';
                $cart['deliver_to'] = $sourceOrder->deliver_to ?? '';
                $cart['delivery_address'] = $sourceOrder->delivery_address ?? '';
                $cart['phone'] = $sourceOrder->phone ?? '';
                $cart['cust_ref'] = $sourceOrder->cust_ref ?? '';
                $cart['ship_via'] = $sourceOrder->ship_via ?? '';
                $cart['comments'] = $sourceOrder->customer_notes ?? '';
                $cart['freight_cost'] = (float)($sourceOrder->freight_cost ?? 0);
                $cart['line_items'] = [];
                foreach ($sourceOrder->lineItems as $li) {
                    $cart['line_items'][] = [
                        'stock_id' => $li->item_code,
                        'item_description' => $li->description,
                        'quantity' => (float)$li->quantity,
                        'price' => (float)$li->unit_price,
                        'discount_percent' => ($li->discount_percentage ?? 0) / 100,
                        'units' => 'each',
                    ];
                }
                session(['order_items' => $cart]);
            }
        }

        $customers = Customer::active()->orderBy('name')->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();
        $items = \App\Models\Item::where('is_active', true)->orderBy('code')->get();
        $paymentTerms = \DB::table('payment_terms')->where('inactive', false)->orderBy('terms_indicator')->get();

        $lastOrder = SalesOrder::max('id') ?: 0;
        $defaultRef = 'SO-' . str_pad($lastOrder + 1, 6, '0', STR_PAD_LEFT);

        $edit_index = session('order_edit_index');
        $message = session('success');
        $error = session('error');
        $addedID = $request->input('AddedID');

        $defaultCart = [
            'customer_id' => '',
            'branch_id' => '',
            'reference' => $defaultRef,
            'ord_date' => now()->format('Y-m-d'),
            'delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'sales_type' => '',
            'payment' => '',
            'location' => '',
            'deliver_to' => '',
            'delivery_address' => '',
            'phone' => '',
            'cust_ref' => '',
            'ship_via' => '',
            'comments' => '',
            'freight_cost' => 0,
            'line_items' => [],
            'stock_id' => '',
            'item_description' => '',
            'qty' => 1,
            'price' => 0,
            'Disc' => 0,
        ];

        $cart = array_merge($defaultCart, $cart);

        $branches = $cart['customer_id']
            ? \App\Models\CustomerBranch::where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get()
            : [];
        $customerInfo = $cart['customer_id']
            ? Customer::with('branches')->find($cart['customer_id'])
            : null;

        if ($cart['branch_id'] && $customerInfo) {
            $branch = \App\Models\CustomerBranch::find($cart['branch_id']);
            if ($branch) {
                if (empty($cart['deliver_to'])) $cart['deliver_to'] = $branch->branch_name ?? $customerInfo->name;
                if (empty($cart['delivery_address'])) $cart['delivery_address'] = $branch->address ?? $customerInfo->address ?? '';
                if (empty($cart['phone'])) $cart['phone'] = $branch->phone ?? $customerInfo->phone ?? '';
            }
        }

        return view('sales.orders.create', compact(
            'customers', 'salesTypes', 'locations', 'shippers', 'items', 'paymentTerms',
            'cart', 'branches', 'customerInfo', 'defaultRef', 'edit_index', 'message', 'error', 'addedID'
        ));
    }

    public function ordersStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'customer_branch_id' => 'nullable|exists:customer_branches,id',
            'sales_person_id' => 'required|exists:sales_persons,id',
            'sales_type_id' => 'required|exists:sales_types,id',
            'quotation_id' => 'nullable|exists:sales_quotations,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after:order_date',
            'customer_notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'payment' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:50',
        ]);

        $order = SalesOrder::create($validated);
        $order->order_number = $this->generateOrderNumber();
        $order->save();

        return redirect()
            ->route('sales.orders.index')
            ->with('success', 'Order created successfully.');
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'SO-';
        $lastOrder = SalesOrder::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) str_replace($prefix, '', $lastOrder->order_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function ordersShow(Request $request, SalesOrder $order)
    {
        $order->load(['customer', 'customerBranch', 'salesPerson', 'salesType', 'lineItems']);

        if ($request->input('print') === 'delivery') {
            $delivery = $order;
            $pdf = Pdf::loadView('sales.delivery.print', compact('delivery'));
            return $pdf->stream('delivery-' . $delivery->order_number . '.pdf');
        }

        if ($request->boolean('print')) {
            $pdf = Pdf::loadView('sales.orders.print', compact('order'));
            return $pdf->stream('order-' . $order->order_number . '.pdf');
        }

        return view('sales.orders.show', compact('order'));
    }

    public function ordersEdit(Request $request, SalesOrder $order)
    {
        // Handle POST cart operations
        if ($request->isMethod('POST')) {
            $cart = session('order_edit_items_' . $order->id, []);

            if ($request->has('CancelOrder')) {
                session(['order_edit_items_' . $order->id => null, 'order_edit_index_' . $order->id => null]);
                return redirect()->route('sales.orders.index');
            }

            if ($request->has('AddItem')) {
                $stock_id = $request->input('stock_id');
                if (!$stock_id) {
                    return back()->with('error', 'Please select an item.');
                }
                $item = \App\Models\Item::where('code', $stock_id)->first();
                $line_item = [
                    'stock_id' => $stock_id,
                    'item_description' => $request->input('item_description', $item->name ?? ''),
                    'quantity' => (float)($request->input('qty', 1)),
                    'price' => (float)($request->input('price', 0)),
                    'discount_percent' => (float)($request->input('Disc', 0)) / 100,
                    'units' => $item->unit_of_measure ?? 'each',
                ];
                $cart['line_items'][] = $line_item;
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                $cart['price'] = 0;
                $cart['Disc'] = 0;
                session(['order_edit_items_' . $order->id => $cart]);
                return redirect()->route('sales.orders.edit', $order);
            }

            if ($request->has('Delete')) {
                $line_no = (int)$request->input('Delete');
                if (isset($cart['line_items'][$line_no])) {
                    unset($cart['line_items'][$line_no]);
                    $cart['line_items'] = array_values($cart['line_items']);
                }
                session(['order_edit_items_' . $order->id => $cart]);
                return redirect()->route('sales.orders.edit', $order);
            }

            if ($request->has('Edit')) {
                session(['order_edit_index_' . $order->id => (int)$request->input('Edit')]);
                session(['order_edit_items_' . $order->id => $cart]);
                return redirect()->route('sales.orders.edit', $order);
            }

            if ($request->has('UpdateItem')) {
                $line_no = session('order_edit_index_' . $order->id);
                if ($line_no !== null && isset($cart['line_items'][$line_no])) {
                    $cart['line_items'][$line_no]['item_description'] = $request->input('item_description', $cart['line_items'][$line_no]['item_description']);
                    $cart['line_items'][$line_no]['quantity'] = (float)($request->input('qty', $cart['line_items'][$line_no]['quantity']));
                    $cart['line_items'][$line_no]['price'] = (float)($request->input('price', $cart['line_items'][$line_no]['price']));
                    $cart['line_items'][$line_no]['discount_percent'] = (float)($request->input('Disc', 0)) / 100;
                }
                session(['order_edit_index_' . $order->id => null]);
                session(['order_edit_items_' . $order->id => $cart]);
                return redirect()->route('sales.orders.edit', $order);
            }

            if ($request->has('CancelItemChanges')) {
                session(['order_edit_index_' . $order->id => null]);
                session(['order_edit_items_' . $order->id => $cart]);
                return redirect()->route('sales.orders.edit', $order);
            }

            if ($request->has('update')) {
                $cart['freight_cost'] = (float)($request->input('freight_cost', 0));
                session(['order_edit_items_' . $order->id => $cart]);
                return redirect()->route('sales.orders.edit', $order);
            }

            // Update cart fields from form
            $cart['customer_id'] = $request->input('customer_id', $cart['customer_id'] ?? $order->customer_id);
            $cart['branch_id'] = $request->input('branch_id', $cart['branch_id'] ?? $order->customer_branch_id);
            $cart['reference'] = $request->input('ref', $cart['reference'] ?? $order->order_number);
            $cart['ord_date'] = $request->input('OrderDate', $cart['ord_date'] ?? $order->order_date?->format('Y-m-d'));
            $cart['delivery_date'] = $request->input('delivery_date', $cart['delivery_date'] ?? $order->delivery_date?->format('Y-m-d'));
            $cart['sales_type'] = $request->input('sales_type', $cart['sales_type'] ?? $order->sales_type_id);
            $cart['payment'] = $request->input('payment', $cart['payment'] ?? '');
            $cart['location'] = $request->input('Location', $cart['location'] ?? '');
            $cart['deliver_to'] = $request->input('deliver_to', $cart['deliver_to'] ?? '');
            $cart['delivery_address'] = $request->input('delivery_address', $cart['delivery_address'] ?? $order->delivery_address ?? '');
            $cart['phone'] = $request->input('phone', $cart['phone'] ?? '');
            $cart['cust_ref'] = $request->input('cust_ref', $cart['cust_ref'] ?? '');
            $cart['ship_via'] = $request->input('ship_via', $cart['ship_via'] ?? '');
            $cart['comments'] = $request->input('Comments', $cart['comments'] ?? $order->customer_notes ?? '');
            $cart['freight_cost'] = (float)($request->input('freight_cost', $cart['freight_cost'] ?? 0));
            $cart['stock_id'] = $request->input('stock_id', $cart['stock_id'] ?? '');
            $cart['item_description'] = $request->input('item_description', $cart['item_description'] ?? '');
            $cart['qty'] = $request->input('qty', $cart['qty'] ?? 1);
            $cart['price'] = $request->input('price', $cart['price'] ?? 0);
            $cart['Disc'] = $request->input('Disc', $cart['Disc'] ?? 0);

            if ($request->has('UpdateOrder')) {
                if (empty($cart['customer_id'])) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (empty($cart['branch_id'])) {
                    return back()->with('error', 'Please select a branch.');
                }
                if (SalesOrder::where('order_number', $cart['reference'])->where('id', '!=', $order->id)->exists()) {
                    return back()->with('error', 'Order number already exists. Please use a different reference.');
                }

                $subtotal = 0;
                foreach ($cart['line_items'] ?? [] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    $subtotal += $lineTotal;
                }
                $total_amount = $subtotal + ($cart['freight_cost'] ?? 0);

                $order->update([
                    'order_number' => $cart['reference'],
                    'order_date' => $cart['ord_date'],
                    'delivery_date' => $cart['delivery_date'],
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'],
                    'sales_person_id' => $cart['sales_person_id'] ?? $order->sales_person_id,
                    'sales_type_id' => $cart['sales_type'] ?: 1,
                    'payment' => $cart['payment'] ?? '',
                    'location' => $cart['location'] ?? '',
                    'ship_via' => $cart['ship_via'] ?? '',
                    'status' => $cart['status'] ?? $order->status,
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $total_amount,
                    'discount_amount' => 0,
                    'delivery_address' => $cart['delivery_address'],
                    'customer_notes' => $cart['comments'],
                    'internal_notes' => $cart['cust_ref'],
                ]);

                // Delete existing line items and re-insert
                $order->lineItems()->delete();
                foreach ($cart['line_items'] ?? [] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    OrderLineItem::create([
                        'order_id' => $order->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int)$li['quantity'],
                        'unit_price' => $li['price'],
                        'discount_percentage' => $li['discount_percent'] * 100,
                        'discount_amount' => $li['quantity'] * $li['price'] * $li['discount_percent'],
                        'line_total' => $lineTotal,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                session(['order_edit_items_' . $order->id => null, 'order_edit_index_' . $order->id => null]);
                return redirect()->route('sales.inquiries.orders')->with('success', 'Order updated successfully.');
            }

            session(['order_edit_items_' . $order->id => $cart]);
            return redirect()->route('sales.orders.edit', $order);
        }

        // GET request - load existing items into session cart
        $order->load('lineItems');
        $cart = session('order_edit_items_' . $order->id) ?? [];

        if (empty($cart)) {
            $cart = [
                'customer_id' => $order->customer_id,
                'branch_id' => $order->customer_branch_id,
                'reference' => $order->order_number,
                'ord_date' => $order->order_date?->format('Y-m-d'),
                'delivery_date' => $order->delivery_date?->format('Y-m-d'),
                'sales_type' => $order->sales_type_id,
                'payment' => $order->payment ?? '',
                'location' => $order->location ?? '',
                'deliver_to' => '',
                'delivery_address' => $order->delivery_address ?? '',
                'phone' => '',
                'cust_ref' => $order->internal_notes ?? '',
                'ship_via' => $order->ship_via ?? '',
                'comments' => $order->customer_notes ?? '',
                'freight_cost' => 0,
                'line_items' => [],
                'stock_id' => '',
                'item_description' => '',
                'qty' => 1,
                'price' => 0,
                'Disc' => 0,
            ];

            foreach ($order->lineItems as $li) {
                $cart['line_items'][] = [
                    'stock_id' => $li->item_code,
                    'item_description' => $li->description,
                    'quantity' => (float)$li->quantity,
                    'price' => (float)$li->unit_price,
                    'discount_percent' => $li->discount_percentage / 100,
                    'units' => 'each',
                ];
            }

            session(['order_edit_items_' . $order->id => $cart]);
        }

        $customers = Customer::active()->orderBy('name')->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();
        $items = \App\Models\Item::where('is_active', true)->orderBy('code')->get();
        $paymentTerms = \DB::table('payment_terms')->where('inactive', false)->orderBy('terms_indicator')->get();

        $edit_index = session('order_edit_index_' . $order->id);
        $message = session('success');
        $error = session('error');

        $branches = $cart['customer_id']
            ? \App\Models\CustomerBranch::where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get()
            : [];
        $customerInfo = $cart['customer_id']
            ? Customer::with('branches')->find($cart['customer_id'])
            : null;

        if ($cart['branch_id'] && $customerInfo) {
            $branch = \App\Models\CustomerBranch::find($cart['branch_id']);
            if ($branch) {
                if (empty($cart['deliver_to'])) $cart['deliver_to'] = $branch->branch_name ?? $customerInfo->name;
                if (empty($cart['delivery_address'])) $cart['delivery_address'] = $branch->address ?? $customerInfo->address ?? '';
                if (empty($cart['phone'])) $cart['phone'] = $branch->phone ?? $customerInfo->phone ?? '';
            }
        }

        return view('sales.orders.edit', compact(
            'customers', 'salesTypes', 'locations', 'shippers', 'items', 'paymentTerms',
            'cart', 'branches', 'customerInfo', 'edit_index', 'message', 'error', 'order'
        ));
    }

    public function ordersUpdate(Request $request, SalesOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'customer_branch_id' => 'nullable|exists:customer_branches,id',
            'sales_person_id' => 'required|exists:sales_persons,id',
            'sales_type_id' => 'required|exists:sales_types,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'customer_notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'payment' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:50',
            'status' => 'required|in:pending,confirmed,in_progress,delivered,cancelled',
        ]);

        $order->update($validated);

        // Handle line items if provided
        if ($request->has('items') && is_array($request->input('items'))) {
            $order->lineItems()->delete();
            foreach ($request->input('items') as $item) {
                if (!empty($item['item_code'])) {
                    $qty = (float)($item['quantity'] ?? 1);
                    $price = (float)($item['unit_price'] ?? 0);
                    $discount = (float)($item['discount_percentage'] ?? 0);
                    $lineTotal = $qty * $price * (1 - $discount / 100);
                    OrderLineItem::create([
                        'order_id' => $order->id,
                        'item_code' => $item['item_code'],
                        'description' => $item['description'] ?? '',
                        'quantity' => (int)$qty,
                        'unit_price' => $price,
                        'discount_percentage' => $discount,
                        'discount_amount' => $qty * $price * ($discount / 100),
                        'line_total' => $lineTotal,
                        'tax_rate' => $item['tax_rate'] ?? 0,
                        'tax_amount' => $item['tax_amount'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()
            ->route('sales.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function ordersDestroy(SalesOrder $order): RedirectResponse
    {
        $order->delete();

        return redirect()
            ->route('sales.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    // Customers
    public function customersIndex(): View
    {
        $query = Customer::with(['salesGroup', 'salesPerson', 'creditStatus']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('customer_code', 'like', '%' . $search . '%')
                  ->orWhere('cust_ref', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (!request('show_inactive')) {
            $query->where('status', '!=', 'inactive');
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('sales.customers.index', compact('customers'));
    }

    public function customersCreate(): View
    {
        $salesGroups = \App\Models\SalesGroup::active()->get();
        $salesPersons = \App\Models\SalesPerson::active()->get();
        $creditStatuses = \App\Models\CreditStatus::active()->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $currencies = ['USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound'];

        return view('sales.customers.create', compact('salesGroups', 'salesPersons', 'creditStatuses', 'salesTypes', 'currencies'));
    }

    public function customersStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cust_ref' => 'nullable|string|max:50',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'curr_code' => 'nullable|string|size:3',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'pymt_discount' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|integer|min:0',
            'tax_id' => 'nullable|string|max:50',
            'sales_group_id' => 'nullable|exists:sales_groups,id',
            'sales_type_id' => 'nullable|exists:sales_types,id',
            'sales_person_id' => 'nullable|exists:sales_persons,id',
            'credit_status_id' => 'nullable|exists:credit_statuses,id',
            'status' => 'required|in:active,inactive,hold',
            'notes' => 'nullable|string',
        ]);

        $validated['customer_code'] = $this->generateCustomerCode();
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms'] = $validated['payment_terms'] ?? 30;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['pymt_discount'] = $validated['pymt_discount'] ?? 0;
        $validated['curr_code'] = $validated['curr_code'] ?? 'USD';

        $customer = Customer::create($validated);

        return redirect()
            ->route('sales.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    private function generateCustomerCode(): string
    {
        $prefix = 'CUST-';
        $lastCustomer = Customer::where('customer_code', 'like', $prefix . '%')
            ->orderBy('customer_code', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = (int) str_replace($prefix, '', $lastCustomer->customer_code);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function customersShow(Customer $customer): View
    {
        $customer->load(['salesGroup', 'salesPerson', 'creditStatus', 'branches']);
        return view('sales.customers.show', compact('customer'));
    }

    public function customersEdit(Customer $customer): View
    {
        $salesGroups = \App\Models\SalesGroup::active()->get();
        $salesPersons = \App\Models\SalesPerson::active()->get();
        $creditStatuses = \App\Models\CreditStatus::active()->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $currencies = ['USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound'];

        $customer->load(['branches']);

        return view('sales.customers.edit', compact(
            'customer', 'salesGroups', 'salesPersons', 'creditStatuses',
            'salesTypes', 'currencies'
        ));
    }

    public function customersUpdate(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cust_ref' => 'nullable|string|max:50',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'curr_code' => 'nullable|string|size:3',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'pymt_discount' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|integer|min:0',
            'tax_id' => 'nullable|string|max:50',
            'sales_group_id' => 'nullable|exists:sales_groups,id',
            'sales_type_id' => 'nullable|exists:sales_types,id',
            'sales_person_id' => 'nullable|exists:sales_persons,id',
            'credit_status_id' => 'nullable|exists:credit_statuses,id',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms'] = $validated['payment_terms'] ?? 30;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['pymt_discount'] = $validated['pymt_discount'] ?? 0;
        $validated['curr_code'] = $validated['curr_code'] ?? 'USD';
        $validated['status'] = $request->boolean('inactive') ? 'inactive' : 'active';

        $customer->update($validated);

        return redirect()
            ->route('sales.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function customersDestroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('sales.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    // Customer Branches
    public function branchesIndex(): View
    {
        $customers = Customer::active()->orderBy('name')->get();
        $selectedCustomer = request('customer_id', 'all');

        $branches = collect();
        if ($selectedCustomer !== 'all') {
            $branches = CustomerBranch::with(['customer', 'salesPerson', 'salesArea', 'salesGroup', 'warehouse', 'taxGroup'])
                ->where('customer_id', $selectedCustomer)
                ->orderBy('branch_name')
                ->get();
        }

        $salesPersons = \App\Models\SalesPerson::active()->get();
        $salesAreas = \App\Models\SalesArea::active()->get();
        $salesGroups = \App\Models\SalesGroup::active()->get();
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
        $taxGroups = \App\Models\TaxGroup::all();
        $shippers = \App\Models\Shipper::all();

        return view('sales.customers.branches', compact(
            'branches', 'customers', 'selectedCustomer',
            'salesPersons', 'salesAreas', 'salesGroups',
            'warehouses', 'taxGroups', 'shippers'
        ));
    }

    public function branchesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_name' => 'required|string|max:255',
            'branch_ref' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:50',
            'rep_lang' => 'nullable|string|max:10',
            'address' => 'required|string',
            'br_post_address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|integer|min:0',
            'sales_person_id' => 'nullable|exists:sales_persons,id',
            'area_id' => 'nullable|exists:sales_areas,id',
            'group_no' => 'nullable|exists:sales_groups,id',
            'default_location' => 'nullable|exists:warehouses,id',
            'default_ship_via' => 'nullable|integer',
            'tax_group_id' => 'nullable|exists:tax_groups,id',
            'bank_account' => 'nullable|string|max:60',
            'notes' => 'nullable|string',
        ]);

        $validated['branch_code'] = $this->generateBranchCode($validated['customer_id']);
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms'] = $validated['payment_terms'] ?? 30;
        $validated['contact_person'] = $validated['contact_person'] ?? $validated['contact_name'] ?? '';

        CustomerBranch::create($validated);

        return redirect()
            ->route('sales.customers.branches', ['customer_id' => $validated['customer_id']])
            ->with('success', 'Branch created successfully.');
    }

    public function branchesEdit(CustomerBranch $branch): View
    {
        $customers = Customer::active()->get();
        $salesPersons = \App\Models\SalesPerson::active()->get();
        $salesAreas = \App\Models\SalesArea::active()->get();
        $salesGroups = \App\Models\SalesGroup::active()->get();
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
        $taxGroups = \App\Models\TaxGroup::all();
        $shippers = \App\Models\Shipper::all();

        $branch->load(['customer', 'salesPerson', 'salesArea', 'salesGroup', 'warehouse', 'taxGroup']);

        return view('sales.customers.branches.edit', compact(
            'branch', 'customers',
            'salesPersons', 'salesAreas', 'salesGroups',
            'warehouses', 'taxGroups', 'shippers'
        ));
    }

    public function branchesUpdate(Request $request, CustomerBranch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_name' => 'required|string|max:255',
            'branch_ref' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:50',
            'rep_lang' => 'nullable|string|max:10',
            'address' => 'required|string',
            'br_post_address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|integer|min:0',
            'sales_person_id' => 'nullable|exists:sales_persons,id',
            'area_id' => 'nullable|exists:sales_areas,id',
            'group_no' => 'nullable|exists:sales_groups,id',
            'default_location' => 'nullable|exists:warehouses,id',
            'default_ship_via' => 'nullable|integer',
            'tax_group_id' => 'nullable|exists:tax_groups,id',
            'bank_account' => 'nullable|string|max:60',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['payment_terms'] = $validated['payment_terms'] ?? 30;
        $validated['inactive'] = $request->boolean('inactive');
        $validated['contact_person'] = $validated['contact_person'] ?? $validated['contact_name'] ?? '';

        $branch->update($validated);

        return redirect()
            ->route('sales.customers.branches', ['customer_id' => $branch->customer_id])
            ->with('success', 'Branch updated successfully.');
    }

    public function branchesDestroy(CustomerBranch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()
            ->route('sales.customers.branches')
            ->with('success', 'Branch deleted successfully.');
    }

    public function directInvoice(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('POST')) {
            $cart = session('invoice_items', []);

            if ($request->has('CancelOrder')) {
                session(['invoice_items' => null, 'invoice_edit_index' => null]);
                return redirect()->route('sales.invoice.direct');
            }

            if ($request->has('AddItem')) {
                $stock_id = $request->input('stock_id');
                if (!$stock_id) {
                    return back()->with('error', 'Please select an item.');
                }
                $item = Item::where('code', $stock_id)->first();
                $line_item = [
                    'stock_id' => $stock_id,
                    'item_description' => $request->input('item_description', $item->name ?? ''),
                    'quantity' => (float)($request->input('qty', 1)),
                    'price' => (float)($request->input('price', 0)),
                    'discount_percent' => (float)($request->input('Disc', 0)) / 100,
                    'units' => $item->unit_of_measure ?? 'each',
                ];
                $cart['line_items'][] = $line_item;
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                $cart['price'] = 0;
                $cart['Disc'] = 0;
                session(['invoice_items' => $cart]);
                return redirect()->route('sales.invoice.direct');
            }

            if ($request->has('Delete')) {
                $line_no = (int)$request->input('Delete');
                if (isset($cart['line_items'][$line_no])) {
                    unset($cart['line_items'][$line_no]);
                    $cart['line_items'] = array_values($cart['line_items']);
                }
                session(['invoice_items' => $cart]);
                return redirect()->route('sales.invoice.direct');
            }

            if ($request->has('Edit')) {
                session(['invoice_edit_index' => (int)$request->input('Edit')]);
                session(['invoice_items' => $cart]);
                return redirect()->route('sales.invoice.direct');
            }

            if ($request->has('UpdateItem')) {
                $line_no = session('invoice_edit_index');
                if ($line_no !== null && isset($cart['line_items'][$line_no])) {
                    $cart['line_items'][$line_no]['item_description'] = $request->input('item_description', $cart['line_items'][$line_no]['item_description']);
                    $cart['line_items'][$line_no]['quantity'] = (float)($request->input('qty', $cart['line_items'][$line_no]['quantity']));
                    $cart['line_items'][$line_no]['price'] = (float)($request->input('price', $cart['line_items'][$line_no]['price']));
                    $cart['line_items'][$line_no]['discount_percent'] = (float)($request->input('Disc', 0)) / 100;
                }
                session(['invoice_edit_index' => null]);
                session(['invoice_items' => $cart]);
                return redirect()->route('sales.invoice.direct');
            }

            if ($request->has('CancelItemChanges')) {
                session(['invoice_edit_index' => null]);
                session(['invoice_items' => $cart]);
                return redirect()->route('sales.invoice.direct');
            }

            if ($request->has('update')) {
                $cart['freight_cost'] = (float)($request->input('freight_cost', 0));
                session(['invoice_items' => $cart]);
                return redirect()->route('sales.invoice.direct');
            }

            $cart['customer_id'] = $request->input('customer_id', $cart['customer_id'] ?? '');
            $cart['branch_id'] = $request->input('branch_id', $cart['branch_id'] ?? '');
            $cart['reference'] = $request->input('ref', $cart['reference'] ?? '');
            $cart['ord_date'] = $request->input('OrderDate', $cart['ord_date'] ?? now()->format('Y-m-d'));
            $cart['delivery_date'] = $request->input('delivery_date', $cart['delivery_date'] ?? '');
            $cart['sales_type'] = $request->input('sales_type', $cart['sales_type'] ?? '');
            $cart['payment'] = $request->input('payment', $cart['payment'] ?? '');
            $cart['location'] = $request->input('Location', $cart['location'] ?? '');
            $cart['deliver_to'] = $request->input('deliver_to', $cart['deliver_to'] ?? '');
            $cart['delivery_address'] = $request->input('delivery_address', $cart['delivery_address'] ?? '');
            $cart['phone'] = $request->input('phone', $cart['phone'] ?? '');
            $cart['cust_ref'] = $request->input('cust_ref', $cart['cust_ref'] ?? '');
            $cart['ship_via'] = $request->input('ship_via', $cart['ship_via'] ?? '');
            $cart['comments'] = $request->input('Comments', $cart['comments'] ?? '');
            $cart['freight_cost'] = (float)($request->input('freight_cost', $cart['freight_cost'] ?? 0));
            $cart['stock_id'] = $request->input('stock_id', $cart['stock_id'] ?? '');
            $cart['item_description'] = $request->input('item_description', $cart['item_description'] ?? '');
            $cart['qty'] = $request->input('qty', $cart['qty'] ?? 1);
            $cart['price'] = $request->input('price', $cart['price'] ?? 0);
            $cart['Disc'] = $request->input('Disc', $cart['Disc'] ?? 0);

            if ($request->has('ProcessOrder')) {
                if (empty($cart['customer_id'])) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (empty($cart['branch_id'])) {
                    return back()->with('error', 'Please select a branch.');
                }
                if (empty($cart['reference'])) {
                    return back()->with('error', 'Please enter a reference.');
                }
                if (count($cart['line_items']) == 0) {
                    return back()->with('error', 'Please add at least one item.');
                }
                if (SalesOrder::where('order_number', $cart['reference'])->exists()) {
                    return back()->with('error', 'Invoice number already exists. Please use a different reference.');
                }

                $subtotal = 0;
                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    $subtotal += $lineTotal;
                }
                $total_amount = $subtotal + $cart['freight_cost'];

                $invoice = SalesOrder::create([
                    'order_number' => $cart['reference'],
                    'order_date' => $cart['ord_date'],
                    'delivery_date' => $cart['delivery_date'],
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'],
                    'sales_person_id' => 1,
                    'sales_type_id' => $cart['sales_type'] ?: 1,
                    'payment' => $cart['payment'] ?? '',
                    'location' => $cart['location'] ?? '',
                    'ship_via' => $cart['ship_via'] ?? '',
                    'status' => 'invoiced',
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $total_amount,
                    'discount_amount' => 0,
                    'delivery_address' => $cart['delivery_address'],
                    'customer_notes' => $cart['comments'],
                    'internal_notes' => $cart['cust_ref'],
                ]);

                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['price'] * (1 - $li['discount_percent']);
                    OrderLineItem::create([
                        'order_id' => $invoice->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int)$li['quantity'],
                        'unit_price' => $li['price'],
                        'discount_percentage' => $li['discount_percent'] * 100,
                        'discount_amount' => $li['quantity'] * $li['price'] * $li['discount_percent'],
                        'line_total' => $lineTotal,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                session(['invoice_items' => null, 'invoice_edit_index' => null]);
                return redirect()->route('sales.invoice.direct', ['AddedID' => $invoice->id]);
            }

            session(['invoice_items' => $cart]);
            return redirect()->route('sales.invoice.direct');
        }

        if ($request->has('NewInvoice')) {
            session(['invoice_items' => null, 'invoice_edit_index' => null]);
        }

        $cart = session('invoice_items') ?? [];

        $customers = Customer::active()->orderBy('name')->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();
        $items = Item::where('is_active', true)->orderBy('code')->get();
        $paymentTerms = \DB::table('payment_terms')->where('inactive', false)->orderBy('terms_indicator')->get();

        $lastInvoice = SalesOrder::where('status', 'invoiced')->max('id') ?: 0;
        $defaultRef = 'INV-' . str_pad($lastInvoice + 1, 6, '0', STR_PAD_LEFT);

        $edit_index = session('invoice_edit_index');
        $message = session('success');
        $error = session('error');
        $addedID = $request->input('AddedID');

        $defaultCart = [
            'customer_id' => '',
            'branch_id' => '',
            'reference' => $defaultRef,
            'ord_date' => now()->format('Y-m-d'),
            'delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'sales_type' => '',
            'payment' => '',
            'location' => '',
            'deliver_to' => '',
            'delivery_address' => '',
            'phone' => '',
            'cust_ref' => '',
            'ship_via' => '',
            'comments' => '',
            'freight_cost' => 0,
            'line_items' => [],
            'stock_id' => '',
            'item_description' => '',
            'qty' => 1,
            'price' => 0,
            'Disc' => 0,
        ];

        $cart = array_merge($defaultCart, $cart);

        $branches = $cart['customer_id']
            ? CustomerBranch::where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get()
            : [];
        $customerInfo = $cart['customer_id']
            ? Customer::with('branches')->find($cart['customer_id'])
            : null;

        if ($cart['branch_id'] && $customerInfo) {
            $branch = CustomerBranch::find($cart['branch_id']);
            if ($branch) {
                if (empty($cart['deliver_to'])) $cart['deliver_to'] = $branch->branch_name ?? $customerInfo->name;
                if (empty($cart['delivery_address'])) $cart['delivery_address'] = $branch->address ?? $customerInfo->address ?? '';
                if (empty($cart['phone'])) $cart['phone'] = $branch->phone ?? $customerInfo->phone ?? '';
            }
        }

        return view('sales.invoice.direct', compact(
            'customers', 'salesTypes', 'locations', 'shippers', 'items', 'paymentTerms',
            'cart', 'branches', 'customerInfo', 'defaultRef', 'edit_index', 'message', 'error', 'addedID'
        ));
    }

    public function directDelivery(Request $request): View|RedirectResponse
    {
        if ($request->has('NewDelivery')) {
            session(['delivery_items' => null, 'delivery_edit_index' => null]);
        }

        if ($request->isMethod('POST')) {
            $cart = session('delivery_items', []);

            if ($request->has('CancelOrder')) {
                session(['delivery_items' => null, 'delivery_edit_index' => null]);
                return redirect()->route('sales.delivery.direct');
            }

            if ($request->has('AddItem')) {
                $stock_id = $request->input('stock_id');
                if (!$stock_id) {
                    return back()->with('error', 'Please select an item.');
                }
                $item = Item::where('code', $stock_id)->first();
                $line_item = [
                    'stock_id' => $stock_id,
                    'item_description' => $request->input('item_description', $item->name ?? ''),
                    'quantity' => (float)($request->input('qty', 1)),
                    'units' => $item->unit_of_measure ?? 'each',
                ];
                $cart['line_items'][] = $line_item;
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                session(['delivery_items' => $cart]);
                return redirect()->route('sales.delivery.direct');
            }

            if ($request->has('Delete')) {
                $line_no = (int)$request->input('Delete');
                if (isset($cart['line_items'][$line_no])) {
                    unset($cart['line_items'][$line_no]);
                    $cart['line_items'] = array_values($cart['line_items']);
                }
                session(['delivery_items' => $cart]);
                return redirect()->route('sales.delivery.direct');
            }

            if ($request->has('Edit')) {
                session(['delivery_edit_index' => (int)$request->input('Edit')]);
                session(['delivery_items' => $cart]);
                return redirect()->route('sales.delivery.direct');
            }

            if ($request->has('UpdateItem')) {
                $line_no = session('delivery_edit_index');
                if ($line_no !== null && isset($cart['line_items'][$line_no])) {
                    $cart['line_items'][$line_no]['item_description'] = $request->input('item_description', $cart['line_items'][$line_no]['item_description']);
                    $cart['line_items'][$line_no]['quantity'] = (float)($request->input('qty', $cart['line_items'][$line_no]['quantity']));
                }
                session(['delivery_edit_index' => null]);
                session(['delivery_items' => $cart]);
                return redirect()->route('sales.delivery.direct');
            }

            if ($request->has('CancelItemChanges')) {
                session(['delivery_edit_index' => null]);
                session(['delivery_items' => $cart]);
                return redirect()->route('sales.delivery.direct');
            }

            $cart['customer_id'] = $request->input('customer_id', $cart['customer_id'] ?? '');
            $cart['branch_id'] = $request->input('branch_id', $cart['branch_id'] ?? '');
            $cart['reference'] = $request->input('ref', $cart['reference'] ?? '');
            $cart['ord_date'] = $request->input('OrderDate', $cart['ord_date'] ?? now()->format('Y-m-d'));
            $cart['sales_type'] = $request->input('sales_type', $cart['sales_type'] ?? '');
            $cart['location'] = $request->input('Location', $cart['location'] ?? '');
            $cart['deliver_to'] = $request->input('deliver_to', $cart['deliver_to'] ?? '');
            $cart['delivery_address'] = $request->input('delivery_address', $cart['delivery_address'] ?? '');
            $cart['phone'] = $request->input('phone', $cart['phone'] ?? '');
            $cart['cust_ref'] = $request->input('cust_ref', $cart['cust_ref'] ?? '');
            $cart['ship_via'] = $request->input('ship_via', $cart['ship_via'] ?? '');
            $cart['comments'] = $request->input('Comments', $cart['comments'] ?? '');
            $cart['stock_id'] = $request->input('stock_id', $cart['stock_id'] ?? '');
            $cart['item_description'] = $request->input('item_description', $cart['item_description'] ?? '');
            $cart['qty'] = $request->input('qty', $cart['qty'] ?? 1);

            if ($request->has('ProcessOrder')) {
                if (empty($cart['customer_id'])) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (empty($cart['branch_id'])) {
                    return back()->with('error', 'Please select a branch.');
                }
                if (empty($cart['reference'])) {
                    return back()->with('error', 'Please enter a reference.');
                }
                if (count($cart['line_items']) == 0) {
                    return back()->with('error', 'Please add at least one item.');
                }
                if (SalesOrder::where('order_number', $cart['reference'])->exists()) {
                    return back()->with('error', 'Delivery number already exists. Please use a different reference.');
                }

                $delivery = SalesOrder::create([
                    'order_number' => $cart['reference'],
                    'order_date' => $cart['ord_date'],
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'],
                    'sales_person_id' => 1,
                    'sales_type_id' => $cart['sales_type'] ?: 1,
                    'location' => $cart['location'] ?? '',
                    'ship_via' => $cart['ship_via'] ?? '',
                    'status' => 'delivered',
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'discount_amount' => 0,
                    'delivery_address' => $cart['delivery_address'],
                    'customer_notes' => $cart['comments'],
                    'internal_notes' => $cart['cust_ref'],
                ]);

                foreach ($cart['line_items'] as $li) {
                    OrderLineItem::create([
                        'order_id' => $delivery->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int)$li['quantity'],
                        'unit_price' => 0,
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'line_total' => 0,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                session(['delivery_items' => null, 'delivery_edit_index' => null]);
                return redirect()->route('sales.delivery.direct', ['AddedID' => $delivery->id]);
            }

            session(['delivery_items' => $cart]);
            return redirect()->route('sales.delivery.direct');
        }

        $cart = session('delivery_items') ?? [];

        $customers = Customer::active()->orderBy('name')->get();
        $salesTypes = \App\Models\SalesType::active()->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();
        $items = Item::where('is_active', true)->orderBy('code')->get();

        $lastDelivery = SalesOrder::where('status', 'delivered')->max('id') ?: 0;
        $defaultRef = 'DLV-' . str_pad($lastDelivery + 1, 6, '0', STR_PAD_LEFT);

        $edit_index = session('delivery_edit_index');
        $message = session('success');
        $error = session('error');
        $addedID = $request->input('AddedID');

        $defaultCart = [
            'customer_id' => '',
            'branch_id' => '',
            'reference' => $defaultRef,
            'ord_date' => now()->format('Y-m-d'),
            'sales_type' => '',
            'location' => '',
            'deliver_to' => '',
            'delivery_address' => '',
            'phone' => '',
            'cust_ref' => '',
            'ship_via' => '',
            'comments' => '',
            'line_items' => [],
            'stock_id' => '',
            'item_description' => '',
            'qty' => 1,
        ];

        $cart = array_merge($defaultCart, $cart);

        $branches = $cart['customer_id']
            ? CustomerBranch::where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get()
            : [];
        $customerInfo = $cart['customer_id']
            ? Customer::with('branches')->find($cart['customer_id'])
            : null;

        if ($cart['branch_id'] && $customerInfo) {
            $branch = CustomerBranch::find($cart['branch_id']);
            if ($branch) {
                if (empty($cart['deliver_to'])) $cart['deliver_to'] = $branch->branch_name ?? $customerInfo->name;
                if (empty($cart['delivery_address'])) $cart['delivery_address'] = $branch->address ?? $customerInfo->address ?? '';
                if (empty($cart['phone'])) $cart['phone'] = $branch->phone ?? $customerInfo->phone ?? '';
            }
        }

        return view('sales.delivery.direct', compact(
            'customers', 'salesTypes', 'locations', 'shippers', 'items',
            'cart', 'branches', 'customerInfo', 'defaultRef', 'edit_index', 'message', 'error', 'addedID'
        ));
    }

    public function invoiceFromDelivery(Request $request): View|RedirectResponse
    {
        $customers = Customer::active()->orderBy('name')->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();

        // Handle POST: process invoice from delivery
        if ($request->isMethod('POST')) {
            if ($request->has('select_invoice')) {
                $deliveryId = $request->input('select_invoice');
                $delivery = SalesOrder::with('lineItems')->find($deliveryId);
                if (!$delivery) {
                    return back()->with('error', 'Delivery not found.');
                }
                session(['invoice_from_delivery' => $deliveryId]);
                return redirect()->route('sales.invoice.from-delivery', ['create_invoice_from' => $deliveryId]);
            }

            if ($request->has('CancelInvoice')) {
                session(['invoice_from_delivery' => null]);
                return redirect()->route('sales.invoice.from-delivery');
            }

            if ($request->has('ConfirmInvoice')) {
                $deliveryId = $request->input('delivery_id') ?: session('invoice_from_delivery');
                $delivery = SalesOrder::with('lineItems')->find($deliveryId);
                if (!$delivery || $delivery->status !== 'delivered') {
                    return back()->with('error', 'Delivery not found or already invoiced.');
                }

                $invRef = 'INV-' . str_pad((SalesOrder::where('status', 'invoiced')->max('id') ?: 0) + 1, 6, '0', STR_PAD_LEFT);
                $invoice = SalesOrder::create([
                    'order_number' => $invRef,
                    'order_date' => $request->input('invoice_date', now()->format('Y-m-d')),
                    'delivery_date' => $delivery->delivery_date,
                    'customer_id' => $delivery->customer_id,
                    'customer_branch_id' => $delivery->customer_branch_id,
                    'sales_person_id' => $delivery->sales_person_id,
                    'sales_type_id' => $delivery->sales_type_id,
                    'payment' => $delivery->payment ?? '',
                    'location' => $delivery->location ?? '',
                    'ship_via' => $delivery->ship_via ?? '',
                    'status' => 'invoiced',
                    'delivery_address' => $delivery->delivery_address,
                    'customer_notes' => $delivery->customer_notes,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'discount_amount' => 0,
                ]);

                foreach ($delivery->lineItems as $li) {
                    OrderLineItem::create([
                        'order_id' => $invoice->id,
                        'item_code' => $li->item_code,
                        'description' => $li->description,
                        'quantity' => $li->quantity,
                        'unit_price' => $li->unit_price,
                        'discount_percentage' => $li->discount_percentage,
                        'discount_amount' => $li->discount_amount,
                        'line_total' => $li->line_total,
                        'tax_rate' => $li->tax_rate,
                        'tax_amount' => $li->tax_amount,
                    ]);
                }

                $delivery->update(['status' => 'invoiced']);
                session(['invoice_from_delivery' => null]);
                return redirect()->route('sales.invoice.from-delivery')->with('success', 'Invoice #' . $invoice->id . ' created from Delivery #' . $delivery->order_number);
            }

            if ($request->has('BatchInvoice')) {
                $selectedIds = $request->input('selected_deliveries', []);
                if (empty($selectedIds)) {
                    return back()->with('error', 'No deliveries selected.');
                }

                $invoiced = [];
                $errors = [];
                foreach ($selectedIds as $deliveryId) {
                    $delivery = SalesOrder::with('lineItems')->find($deliveryId);
                    if (!$delivery || $delivery->status !== 'delivered') {
                        $errors[] = "Delivery #{$deliveryId} not found or already invoiced.";
                        continue;
                    }

                    $invRef = 'INV-' . str_pad((SalesOrder::where('status', 'invoiced')->max('id') ?: 0) + 1, 6, '0', STR_PAD_LEFT);
                    $invoice = SalesOrder::create([
                        'order_number' => $invRef,
                        'order_date' => now()->format('Y-m-d'),
                        'delivery_date' => $delivery->delivery_date,
                        'customer_id' => $delivery->customer_id,
                        'customer_branch_id' => $delivery->customer_branch_id,
                        'sales_person_id' => $delivery->sales_person_id,
                        'sales_type_id' => $delivery->sales_type_id,
                        'payment' => $delivery->payment ?? '',
                        'location' => $delivery->location ?? '',
                        'ship_via' => $delivery->ship_via ?? '',
                        'status' => 'invoiced',
                        'delivery_address' => $delivery->delivery_address,
                        'customer_notes' => $delivery->customer_notes,
                        'subtotal' => 0,
                        'tax_amount' => 0,
                        'total_amount' => 0,
                        'discount_amount' => 0,
                    ]);

                    foreach ($delivery->lineItems as $li) {
                        OrderLineItem::create([
                            'order_id' => $invoice->id,
                            'item_code' => $li->item_code,
                            'description' => $li->description,
                            'quantity' => $li->quantity,
                            'unit_price' => $li->unit_price,
                            'discount_percentage' => $li->discount_percentage,
                            'discount_amount' => $li->discount_amount,
                            'line_total' => $li->line_total,
                            'tax_rate' => $li->tax_rate,
                            'tax_amount' => $li->tax_amount,
                        ]);
                    }

                    $delivery->update(['status' => 'invoiced']);
                    $invoiced[] = $delivery->order_number;
                }

                $msg = count($invoiced) . ' delivery(s) invoiced: ' . implode(', ', $invoiced);
                if (!empty($errors)) {
                    $msg .= '. Errors: ' . implode('; ', $errors);
                }
                return redirect()->route('sales.invoice.from-delivery')->with('success', $msg);
            }

            return back();
        }

        // GET: entry mode or search mode
        $createFromId = $request->input('create_invoice_from') ?: session('invoice_from_delivery');
        if ($request->input('create_invoice_from')) {
            session(['invoice_from_delivery' => $createFromId]);
        }
        $message = session('success');
        $error = session('error');

        $delivery = null;
        if ($createFromId) {
            $delivery = SalesOrder::with('customer', 'salesPerson', 'salesType', 'lineItems')->find($createFromId);
            if (!$delivery) {
                session(['invoice_from_delivery' => null]);
                $createFromId = null;
            }
        }

        // Search mode
        $searchDeliveryNo = $request->input('delivery_no', '');
        $searchCustomer = $request->input('customer_id', '');
        $searchLocation = $request->input('location', '');
        $searchFrom = $request->input('from_date', '');
        $searchTo = $request->input('to_date', '');
        $outstandingOnly = $request->has('outstanding_only') ? $request->boolean('outstanding_only') : true;

        $deliveries = collect();
        if (!$createFromId) {
            $query = SalesOrder::with('customer')->withCount('lineItems')
                ->whereIn('status', ['delivered', 'invoiced']);

            if ($outstandingOnly) {
                $query->where('status', 'delivered');
            }

            if ($searchDeliveryNo) {
                $query->where('order_number', 'like', '%' . $searchDeliveryNo . '%');
            }
            if ($searchCustomer) {
                $query->where('customer_id', $searchCustomer);
            }
            if ($searchLocation) {
                $query->where('location', $searchLocation);
            }
            if ($searchFrom) {
                $query->whereDate('delivery_date', '>=', $searchFrom);
            }
            if ($searchTo) {
                $query->whereDate('delivery_date', '<=', $searchTo);
            }

            $deliveries = $query->orderBy('id', 'desc')->get();
        }

        $salesTypes = \App\Models\SalesType::active()->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();

        return view('sales.invoice.from-delivery', compact(
            'customers', 'locations', 'deliveries', 'delivery', 'createFromId',
            'searchDeliveryNo', 'searchCustomer', 'searchLocation', 'searchFrom', 'searchTo',
            'outstandingOnly', 'message', 'error', 'salesTypes', 'shippers'
        ));
    }

    public function paymentsIndex(Request $request): View|RedirectResponse
    {
        $customers = Customer::active()->orderBy('name')->get();
        $bankAccounts = \DB::table('bank_accounts')->where('inactive', false)->orderBy('bank_account_name')->get();

        if ($request->isMethod('POST')) {
            if ($request->has('CancelPayment')) {
                session(['payment_cart' => null]);
                return redirect()->route('sales.payments.index');
            }

            if ($request->has('ProcessPayment')) {
                $customerId = $request->input('customer_id');
                $bankAccountId = $request->input('bank_account_id');
                $paymentDate = $request->input('payment_date', now()->format('Y-m-d'));
                $amount = (float)($request->input('amount', 0));
                $reference = $request->input('reference', '');
                $memo = $request->input('memo', '');
                $allocations = $request->input('alloc_amount', []);

                if (!$customerId) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (!$bankAccountId) {
                    return back()->with('error', 'Please select a bank account.');
                }
                if ($amount <= 0) {
                    return back()->with('error', 'Please enter a valid payment amount.');
                }

                $payNo = 'PAY-' . str_pad((\DB::table('customer_payments')->max('id') ?: 0) + 1, 6, '0', STR_PAD_LEFT);
                $payment = \App\Models\CustomerPayment::create([
                    'customer_id' => $customerId,
                    'bank_account_id' => $bankAccountId,
                    'payment_number' => $payNo,
                    'payment_date' => $paymentDate,
                    'reference' => $reference,
                    'amount' => $amount,
                    'discount' => 0,
                    'bank_amount' => $amount,
                    'bank_charge' => 0,
                    'currency' => 'USD',
                    'exchange_rate' => 1,
                    'memo' => $memo,
                    'status' => 'approved',
                    'created_by' => auth()->id() ?? 1,
                ]);

                // Save allocations
                $allocTotal = 0;
                foreach ($allocations as $invoiceId => $allocAmount) {
                    $allocAmount = (float)($allocAmount ?? 0);
                    if ($allocAmount > 0) {
                        $invoice = SalesOrder::find($invoiceId);
                        if ($invoice) {
                            $allocTotal += $allocAmount;
                            \App\Models\CustomerPaymentAllocation::create([
                                'customer_payment_id' => $payment->id,
                                'sales_order_id' => $invoiceId,
                                'amount' => $allocAmount,
                            ]);
                            $invoice->increment('paid_amount', $allocAmount);
                        }
                    }
                }

                // Write bank_trans entry
                \DB::table('bank_trans')->insert([
                    'ref_type' => 'customer_payment',
                    'reference_id' => $payment->id,
                    'bank_account_id' => $bankAccountId,
                    'trans_date' => $paymentDate,
                    'reference' => $reference ?: $payNo,
                    'amount' => $amount,
                    'memo' => $memo,
                    'reconciled' => null,
                    'created_by' => auth()->id() ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                session(['payment_cart' => null]);
                return redirect()->route('sales.payments.index')->with('success', 'Payment ' . $payNo . ' recorded successfully.');
            }

            // Select customer to load invoices (only when no other action button pressed)
            $customerId = $request->input('customer_id');
            if ($customerId && !$request->has('CancelPayment') && !$request->has('ProcessPayment')) {
                return redirect()->route('sales.payments.index', ['customer_id' => $customerId]);
            }
        }

        // GET
        $selectedCustomerId = $request->input('customer_id') ?: old('customer_id') ?: session('selected_customer');
        $message = session('success');
        $error = session('error');

        // Load unpaid invoices for selected customer (status = 'invoiced' with balance)
        $invoices = collect();
        if ($selectedCustomerId) {
            $invoices = SalesOrder::where('customer_id', $selectedCustomerId)
                ->where('status', 'invoiced')
                ->whereRaw('total_amount > paid_amount')
                ->orderBy('order_date', 'desc')
                ->get();
        }

        // Recent payments
        $recentPayments = \App\Models\CustomerPayment::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('sales.payments.index', compact(
            'customers', 'bankAccounts', 'invoices', 'recentPayments',
            'selectedCustomerId', 'message', 'error'
        ));
    }

    public function creditNotesIndex(Request $request): View|RedirectResponse
    {
        $customers = Customer::active()->orderBy('name')->get();
        $items = Item::where('is_active', true)->orderBy('code')->get();

        if ($request->isMethod('POST')) {
            $cart = session('credit_note_cart') ?? [];

            if ($request->has('CancelCredit')) {
                session(['credit_note_cart' => null]);
                return redirect()->route('sales.credit-notes.index');
            }

            if ($request->has('AddItem')) {
                $stockId = $request->input('stock_id');
                if (!$stockId) {
                    return back()->with('error', 'Please select an item.');
                }
                $item = Item::where('code', $stockId)->first();
                $cart['line_items'][] = [
                    'stock_id' => $stockId,
                    'description' => $request->input('item_description', $item->name ?? ''),
                    'quantity' => (float)($request->input('qty', 1)),
                    'unit_price' => (float)($request->input('price', 0)),
                    'discount_percent' => (float)($request->input('discount', 0)) / 100,
                    'units' => $item->unit_of_measure ?? 'each',
                ];
                $cart['stock_id'] = '';
                $cart['item_description'] = '';
                $cart['qty'] = 1;
                $cart['price'] = 0;
                $cart['discount'] = 0;
                session(['credit_note_cart' => $cart]);
                return redirect()->route('sales.credit-notes.index');
            }

            if ($request->has('DeleteItem')) {
                $lineNo = (int)$request->input('DeleteItem');
                if (isset($cart['line_items'][$lineNo])) {
                    unset($cart['line_items'][$lineNo]);
                    $cart['line_items'] = array_values($cart['line_items']);
                }
                session(['credit_note_cart' => $cart]);
                return redirect()->route('sales.credit-notes.index');
            }

            if ($request->has('ProcessCredit')) {
                $customerId = $request->input('customer_id');
                $creditDate = $request->input('credit_date', now()->format('Y-m-d'));
                $ref = $request->input('reference', '');
                $reason = $request->input('reason', '');
                $memo = $request->input('memo', '');
                $invoiceId = $request->input('sales_order_id');
                $branchId = $request->input('branch_id');

                if (!$customerId) {
                    return back()->with('error', 'Please select a customer.');
                }
                if (empty($cart['line_items'])) {
                    return back()->with('error', 'Please add at least one item.');
                }

                $subtotal = 0;
                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['unit_price'] * (1 - $li['discount_percent']);
                    $subtotal += $lineTotal;
                }
                $total = $subtotal;

                $cnNo = 'CN-' . str_pad((\DB::table('credit_notes')->max('id') ?: 0) + 1, 6, '0', STR_PAD_LEFT);
                $creditNote = \App\Models\CreditNote::create([
                    'credit_note_number' => $cnNo,
                    'customer_id' => $customerId,
                    'customer_branch_id' => $branchId ?: null,
                    'sales_order_id' => $invoiceId ?: null,
                    'credit_date' => $creditDate,
                    'reference' => $ref,
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $total,
                    'discount_amount' => 0,
                    'reason' => $reason,
                    'memo' => $memo,
                    'status' => 'approved',
                    'created_by' => auth()->id() ?? 1,
                ]);

                foreach ($cart['line_items'] as $li) {
                    $lineTotal = $li['quantity'] * $li['unit_price'] * (1 - $li['discount_percent']);
                    \App\Models\CreditNoteItem::create([
                        'credit_note_id' => $creditNote->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['description'],
                        'quantity' => $li['quantity'],
                        'unit_price' => $li['unit_price'],
                        'discount_percentage' => $li['discount_percent'] * 100,
                        'discount_amount' => $li['quantity'] * $li['unit_price'] * $li['discount_percent'],
                        'line_total' => $lineTotal,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                session(['credit_note_cart' => null]);
                return redirect()->route('sales.credit-notes.index')->with('success', 'Credit Note ' . $cnNo . ' created successfully.');
            }

            // Update cart fields
            $cart['customer_id'] = $request->input('customer_id', $cart['customer_id'] ?? '');
            $cart['branch_id'] = $request->input('branch_id', $cart['branch_id'] ?? '');
            $cart['reference'] = $request->input('reference', $cart['reference'] ?? '');
            $cart['credit_date'] = $request->input('credit_date', $cart['credit_date'] ?? now()->format('Y-m-d'));
            $cart['reason'] = $request->input('reason', $cart['reason'] ?? '');
            $cart['memo'] = $request->input('memo', $cart['memo'] ?? '');
            $newInvoiceId = $request->input('sales_order_id', $cart['sales_order_id'] ?? '');
            $oldInvoiceId = $cart['sales_order_id'] ?? '';
            $cart['sales_order_id'] = $newInvoiceId;
            if ($newInvoiceId && $newInvoiceId != $oldInvoiceId && !$request->has('AddItem') && !$request->has('DeleteItem') && !$request->has('ProcessCredit')) {
                $invoice = SalesOrder::with('lineItems')->find($newInvoiceId);
                if ($invoice) {
                    $cart['line_items'] = [];
                    foreach ($invoice->lineItems as $li) {
                        $cart['line_items'][] = [
                            'stock_id' => $li->item_code,
                            'description' => $li->description,
                            'quantity' => (float)$li->quantity,
                            'unit_price' => (float)$li->unit_price,
                            'discount_percent' => ($li->discount_percentage ?? 0) / 100,
                            'units' => 'each',
                        ];
                    }
                }
            }
            $cart['stock_id'] = $request->input('stock_id', $cart['stock_id'] ?? '');
            $cart['item_description'] = $request->input('item_description', $cart['item_description'] ?? '');
            $cart['qty'] = $request->input('qty', $cart['qty'] ?? 1);
            $cart['price'] = $request->input('price', $cart['price'] ?? 0);
            $cart['discount'] = $request->input('discount', $cart['discount'] ?? 0);

            session(['credit_note_cart' => $cart]);
            return redirect()->route('sales.credit-notes.index');
        }

        // GET
        $cart = session('credit_note_cart') ?? [];

        // Pre-populate from invoice if invoice_id is specified
        $invoiceId = $request->input('invoice_id');
        if ($invoiceId && empty($cart)) {
            $invoice = SalesOrder::with('lineItems', 'customer')->find($invoiceId);
            if ($invoice) {
                $cart['customer_id'] = $invoice->customer_id;
                $cart['branch_id'] = $invoice->customer_branch_id;
                $cart['credit_date'] = now()->format('Y-m-d');
                $cart['reason'] = 'Credit for Invoice #' . $invoice->order_number;
                $cart['sales_order_id'] = $invoice->id;
                foreach ($invoice->lineItems as $li) {
                    $cart['line_items'][] = [
                        'stock_id' => $li->item_code,
                        'description' => $li->description,
                        'quantity' => (float)$li->quantity,
                        'unit_price' => (float)$li->unit_price,
                        'discount_percent' => ($li->discount_percentage ?? 0) / 100,
                        'units' => 'each',
                    ];
                }
                session(['credit_note_cart' => $cart]);
            }
        }

        // Pre-populate from existing credit note if copy_from_credit is specified
        $copyCreditId = $request->input('copy_from_credit');
        if ($copyCreditId && empty($cart)) {
            $sourceCn = \App\Models\CreditNote::with('items', 'customer')->find($copyCreditId);
            if ($sourceCn) {
                $cart['customer_id'] = $sourceCn->customer_id;
                $cart['branch_id'] = $sourceCn->customer_branch_id;
                $cart['credit_date'] = now()->format('Y-m-d');
                $cart['reason'] = $sourceCn->reason;
                $cart['sales_order_id'] = $sourceCn->sales_order_id;
                foreach ($sourceCn->items as $li) {
                    $cart['line_items'][] = [
                        'stock_id' => $li->item_code,
                        'description' => $li->description,
                        'quantity' => (float)$li->quantity,
                        'unit_price' => (float)$li->unit_price,
                        'discount_percent' => ($li->discount_percentage ?? 0) / 100,
                        'units' => 'each',
                    ];
                }
                session(['credit_note_cart' => $cart]);
            }
        }

        // Pre-populate customer if customer_id is specified
        $customerId = $request->input('customer_id');
        if ($customerId && empty($cart)) {
            $cart['customer_id'] = $customerId;
            session(['credit_note_cart' => $cart]);
        }

        $defaultCart = [
            'customer_id' => '',
            'branch_id' => '',
            'reference' => '',
            'credit_date' => now()->format('Y-m-d'),
            'reason' => '',
            'memo' => '',
            'sales_order_id' => '',
            'line_items' => [],
            'stock_id' => '',
            'item_description' => '',
            'qty' => 1,
            'price' => 0,
            'discount' => 0,
        ];

        $cart = array_merge($defaultCart, $cart);

        // Load invoices for selected customer
        $invoices = collect();
        if ($cart['customer_id']) {
            $invoices = SalesOrder::where('customer_id', $cart['customer_id'])
                ->where('status', 'invoiced')
                ->orderBy('order_date', 'desc')
                ->get();
        }

        $branches = $cart['customer_id']
            ? CustomerBranch::where('customer_id', $cart['customer_id'])->orderBy('branch_name')->get()
            : [];

        $customerInfo = $cart['customer_id']
            ? Customer::with('branches')->find($cart['customer_id'])
            : null;

        $recentCredits = \App\Models\CreditNote::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $message = session('success');
        $error = session('error');

        return view('sales.credit-notes.index', compact(
            'customers', 'items', 'invoices', 'branches', 'customerInfo',
            'recentCredits', 'cart', 'message', 'error'
        ));
    }

    public function paymentsPrint(\App\Models\CustomerPayment $payment)
    {
        $payment->load('customer', 'allocations.salesOrder');
        $pdf = Pdf::loadView('sales.payments.print', compact('payment'));
        return $pdf->stream('payment-' . $payment->payment_number . '.pdf');
    }

    public function creditNotesPrint(\App\Models\CreditNote $creditNote)
    {
        $creditNote->load('customer', 'items');
        $pdf = Pdf::loadView('sales.credit-notes.print', compact('creditNote'));
        return $pdf->stream('credit-note-' . $creditNote->credit_note_number . '.pdf');
    }

    public function creditInvoice(Request $request): View|RedirectResponse
    {
        $customers = \App\Models\Customer::active()->orderBy('name')->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();
        $accounts = \App\Models\Account::where('is_active', true)->orderBy('code')->get();
        $taxTypes = \App\Models\TaxType::where('inactive', false)->orderBy('name')->get();

        // GET: initialize cart from InvoiceNumber
        $invoiceNumber = $request->input('InvoiceNumber');
        $modifyCredit = $request->input('ModifyCredit');

        if ($request->isMethod('GET')) {
            $cart = null;
            if ($invoiceNumber) {
                $invoice = \App\Models\SalesOrder::with(['lineItems', 'customer', 'customerBranch', 'salesType'])->find($invoiceNumber);
                if (!$invoice || $invoice->status !== 'invoiced') {
                    return redirect()->route('sales.credit-notes.index')->with('error', 'Invalid or uninvoiced order.');
                }
                $cart = $this->initCreditInvoiceCart($invoice);
                session(['credit_invoice_cart' => $cart]);
            } elseif ($modifyCredit) {
                $creditNote = \App\Models\CreditNote::with(['items', 'customer', 'branch', 'originalInvoice'])->find($modifyCredit);
                if ($creditNote) {
                    $cart = session('credit_invoice_cart');
                    $cart['modify_id'] = $creditNote->id;
                    $cart['reference'] = $creditNote->reference;
                    $cart['credit_date'] = $creditNote->credit_date?->format('Y-m-d') ?? now()->format('Y-m-d');
                    $cart['credit_type'] = $creditNote->reason === 'Return' || $creditNote->reason === 'Write Off' ? $creditNote->reason : 'Return';
                    $cart['return_location'] = $creditNote->return_location ?? '';
                    $cart['write_off_gl'] = $creditNote->write_off_gl ?? '';
                    $cart['memo'] = $creditNote->memo ?? '';
                    $cart['freight_cost'] = 0;
                    $cart['line_items'] = [];
                    foreach ($creditNote->items as $li) {
                        $cart['line_items'][] = [
                            'stock_id' => $li->item_code,
                            'description' => $li->description,
                            'invoiced_qty' => 0,
                            'units' => 'each',
                            'credit_qty' => (float)$li->quantity,
                            'unit_price' => (float)$li->unit_price,
                            'discount_percent' => (float)$li->discount_percentage,
                            'line_total' => (float)$li->line_total,
                        ];
                    }
                    session(['credit_invoice_cart' => $cart]);
                }
            }

            if (!$cart) {
                return redirect()->route('sales.credit-notes.index')->with('error', 'This page can only be opened if an invoice has been selected for crediting.');
            }
        }

        // POST handling
        if ($request->isMethod('POST')) {
            $cart = session('credit_invoice_cart');

            if (!$cart) {
                return redirect()->route('sales.credit-notes.index')->with('error', 'Session expired. Please select an invoice again.');
            }

            // Update
            if ($request->has('Update')) {
                $cart['freight_cost'] = (float)($request->input('ChargeFreightCost', 0));
                $cart['credit_date'] = $request->input('CreditDate', now()->format('Y-m-d'));
                $cart['shipper_id'] = $request->input('ShipperID', $cart['shipper_id'] ?? '');
                $cart['credit_type'] = $request->input('CreditType', 'Return');
                $cart['return_location'] = $request->input('Location', $cart['return_location'] ?? '');
                $cart['write_off_gl'] = $request->input('WriteOffGLCode', '');
                $cart['memo'] = $request->input('CreditText', $cart['memo'] ?? '');

                foreach ($cart['line_items'] as $idx => &$li) {
                    $qty = $request->input('Line' . $idx);
                    if ($qty !== null) {
                        $li['credit_qty'] = (float)$qty;
                    }
                    $desc = $request->input('Line' . $idx . 'Desc');
                    if ($desc !== null && strlen($desc) > 0) {
                        $li['description'] = $desc;
                    }
                    $li['line_total'] = $li['credit_qty'] * $li['unit_price'] * (1 - $li['discount_percent'] / 100);
                }
                unset($li);

                // Recalculate totals
                $cart['items_total'] = array_sum(array_column($cart['line_items'], 'line_total'));

                if (!$request->has('ProcessCredit')) {
                    session(['credit_invoice_cart' => $cart]);
                    return redirect()->route('sales.credit-invoice')->with('success', 'Updated.');
                }
            }

            // ProcessCredit
            if ($request->has('ProcessCredit')) {
                $cart['freight_cost'] = (float)($request->input('ChargeFreightCost', $cart['freight_cost'] ?? 0));
                $cart['credit_date'] = $request->input('CreditDate', $cart['credit_date'] ?? now()->format('Y-m-d'));
                $cart['credit_type'] = $request->input('CreditType', $cart['credit_type'] ?? 'Return');
                $cart['return_location'] = $request->input('Location', $cart['return_location'] ?? '');
                $cart['write_off_gl'] = $request->input('WriteOffGLCode', $cart['write_off_gl'] ?? '');
                $cart['memo'] = $request->input('CreditText', $cart['memo'] ?? '');
                if (!$request->has('Update')) {
                    foreach ($cart['line_items'] as $idx => &$li) {
                        $qty = $request->input('Line' . $idx);
                        if ($qty !== null) {
                            $li['credit_qty'] = (float)$qty;
                        }
                        $desc = $request->input('Line' . $idx . 'Desc');
                        if ($desc !== null && strlen($desc) > 0) {
                            $li['description'] = $desc;
                        }
                        $li['line_total'] = $li['credit_qty'] * $li['unit_price'] * (1 - $li['discount_percent'] / 100);
                    }
                    unset($li);
                }

                // Validation
                $errors = [];
                if (!$cart['invoice_id']) {
                    $errors[] = 'Invoice not specified.';
                }
                if (!$cart['customer_id']) {
                    $errors[] = 'Customer not specified.';
                }
                if (empty($cart['line_items'])) {
                    $errors[] = 'No items to credit.';
                }

                // Check quantities
                foreach ($cart['line_items'] as $idx => $li) {
                    if ($li['credit_qty'] < 0) {
                        $errors[] = 'Credit quantity for ' . $li['stock_id'] . ' cannot be negative.';
                    }
                    if ($li['invoiced_qty'] > 0 && $li['credit_qty'] > ($li['invoiced_qty'] - $li['already_credited'])) {
                        $errors[] = 'Credit quantity for ' . $li['stock_id'] . ' exceeds available quantity (' . ($li['invoiced_qty'] - $li['already_credited']) . ').';
                    }
                }

                if (!empty($errors)) {
                    session(['credit_invoice_cart' => $cart]);
                    return back()->with('error', implode(' ', $errors));
                }

                $itemsTotal = array_sum(array_column($cart['line_items'], 'line_total'));
                $subtotal = $itemsTotal + $cart['freight_cost'];

                // Create Credit Note
                $cnNo = 'CN-' . str_pad((\DB::table('credit_notes')->max('id') ?: 0) + 1, 6, '0', STR_PAD_LEFT);

                $creditNoteData = [
                    'credit_note_number' => $cnNo,
                    'customer_id' => $cart['customer_id'],
                    'customer_branch_id' => $cart['branch_id'] ?: null,
                    'sales_order_id' => $cart['invoice_id'],
                    'credit_date' => $cart['credit_date'],
                    'reference' => $cart['reference'] ?? '',
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $subtotal,
                    'discount_amount' => 0,
                    'reason' => $cart['credit_type'] ?? 'Return',
                    'memo' => $cart['memo'] ?? '',
                    'status' => 'approved',
                    'created_by' => auth()->id() ?? 1,
                ];

                if ($cart['modify_id'] ?? false) {
                    $creditNote = \App\Models\CreditNote::find($cart['modify_id']);
                    if ($creditNote) {
                        $creditNote->update($creditNoteData);
                        $creditNote->items()->delete();
                    } else {
                        $creditNote = \App\Models\CreditNote::create($creditNoteData);
                    }
                } else {
                    $creditNote = \App\Models\CreditNote::create($creditNoteData);
                }

                foreach ($cart['line_items'] as $li) {
                    if ($li['credit_qty'] <= 0) continue;
                    \App\Models\CreditNoteItem::create([
                        'credit_note_id' => $creditNote->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['description'],
                        'quantity' => $li['credit_qty'],
                        'unit_price' => $li['unit_price'],
                        'discount_percentage' => $li['discount_percent'],
                        'discount_amount' => $li['credit_qty'] * $li['unit_price'] * ($li['discount_percent'] / 100),
                        'line_total' => $li['line_total'],
                        'tax_rate' => $li['tax_rate'] ?? 0,
                        'tax_amount' => $li['tax_amount'] ?? 0,
                    ]);
                }

                session(['credit_invoice_cart' => null]);
                return redirect()->route('sales.credit-notes.index')->with('success', 'Credit Note ' . $cnNo . ' has been processed.');
            }
        }

        $cart = session('credit_invoice_cart');
        if (!$cart) {
            return redirect()->route('sales.credit-notes.index')->with('error', 'Session expired. Please select an invoice again.');
        }

        $itemsTotal = array_sum(array_column($cart['line_items'], 'line_total'));

        return view('sales.credit-invoice.index', compact(
            'cart', 'customers', 'shippers', 'locations', 'accounts', 'taxTypes', 'itemsTotal'
        ));
    }

    private function initCreditInvoiceCart(\App\Models\SalesOrder $invoice): array
    {
        $cart = [
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'customer_name' => $invoice->customer->name ?? '',
            'branch_id' => $invoice->customer_branch_id,
            'branch_name' => $invoice->customerBranch->branch_name ?? '',
            'currency' => 'USD',
            'reference' => '',
            'credit_date' => now()->format('Y-m-d'),
            'invoice_date' => $invoice->order_date?->format('Y-m-d') ?? '',
            'shipper_id' => $invoice->ship_via ?? '',
            'freight_cost' => (float)($invoice->freight_cost ?? 0),
            'credit_type' => 'Return',
            'return_location' => '',
            'write_off_gl' => '',
            'memo' => '',
            'modify_id' => null,
            'line_items' => [],
        ];

        foreach ($invoice->lineItems as $li) {
            $alreadyCredited = \App\Models\CreditNoteItem::where('item_code', $li->item_code)
                ->whereHas('creditNote', function ($q) use ($invoice) {
                    $q->where('sales_order_id', $invoice->id);
                })
                ->sum('quantity');

            $cart['line_items'][] = [
                'stock_id' => $li->item_code,
                'description' => $li->description,
                'invoiced_qty' => (float)$li->quantity,
                'units' => $li->units ?? 'each',
                'credit_qty' => max(0, (float)$li->quantity - (float)$alreadyCredited),
                'unit_price' => (float)$li->unit_price,
                'discount_percent' => (float)($li->discount_percentage ?? 0),
                'already_credited' => (float)$alreadyCredited,
                'tax_rate' => (float)($li->tax_rate ?? 0),
                'tax_amount' => (float)($li->tax_amount ?? 0),
                'line_total' => max(0, (float)$li->quantity - (float)$alreadyCredited) * (float)$li->unit_price * (1 - ($li->discount_percentage ?? 0) / 100),
            ];
        }

        return $cart;
    }

    public function deliveryFromOrder(Request $request): View|RedirectResponse
    {
        $customers = Customer::active()->orderBy('name')->get();
        $locations = \DB::table('locations')->where('inactive', false)->orderBy('location_name')->get();

        // Handle POST: start delivery from order
        if ($request->isMethod('POST')) {
            if ($request->has('select_order')) {
                $orderId = $request->input('select_order');
                $order = SalesOrder::with('lineItems')->findOrFail($orderId);
                session(['delivery_from_order' => $orderId]);
                return redirect()->route('sales.delivery.from-order', ['order_id' => $orderId]);
            }

            if ($request->has('CancelDelivery')) {
                session(['delivery_from_order' => null]);
                return redirect()->route('sales.delivery.from-order');
            }

            if ($request->has('ProcessDelivery')) {
                $orderId = $request->input('order_id') ?: session('delivery_from_order');
                if (!$orderId) {
                    return back()->with('error', 'No order selected.');
                }
                $order = SalesOrder::with('lineItems')->find($orderId);
                if (!$order) {
                    return back()->with('error', 'Order not found.');
                }

                // Build line items from submitted quantities
                $quantities = $request->input('qty', []);
                $lineItems = [];
                $hasItems = false;
                foreach ($order->lineItems as $li) {
                    $qty = (float)($quantities[$li->id] ?? 0);
                    if ($qty > 0) {
                        $hasItems = true;
                        $lineItems[] = [
                            'stock_id' => $li->item_code,
                            'item_description' => $li->description,
                            'quantity' => $qty,
                            'units' => 'each',
                        ];
                    }
                }

                if (!$hasItems) {
                    return back()->with('error', 'Please enter at least one item quantity to deliver.');
                }

                // Create delivery record
                $deliveryRef = 'DLV-' . str_pad((SalesOrder::where('status', 'delivered')->max('id') ?: 0) + 1, 6, '0', STR_PAD_LEFT);
                $delivery = SalesOrder::create([
                    'order_number' => $deliveryRef,
                    'order_date' => now()->format('Y-m-d'),
                    'customer_id' => $order->customer_id,
                    'customer_branch_id' => $order->customer_branch_id,
                    'sales_person_id' => $order->sales_person_id,
                    'sales_type_id' => $order->sales_type_id,
                    'payment' => $order->payment ?? '',
                    'location' => $order->location ?? '',
                    'ship_via' => $order->ship_via ?? '',
                    'status' => 'delivered',
                    'delivery_address' => $order->delivery_address,
                    'customer_notes' => $order->customer_notes,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'discount_amount' => 0,
                ]);

                foreach ($lineItems as $li) {
                    OrderLineItem::create([
                        'order_id' => $delivery->id,
                        'item_code' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => (int)$li['quantity'],
                        'unit_price' => 0,
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'line_total' => 0,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                    ]);
                }

                $order->update(['status' => 'delivered']);
                session(['delivery_from_order' => null]);
                return redirect()->route('sales.delivery.from-order')->with('success', 'Delivery #' . $delivery->id . ' created from Order #' . $order->order_number);
            }

            return back();
        }

        // GET: search mode or delivery mode
        $deliveryOrderId = $request->input('order_id') ?: session('delivery_from_order');
        if ($request->input('order_id')) {
            session(['delivery_from_order' => $deliveryOrderId]);
        }
        $message = session('success');
        $error = session('error');

        $order = null;
        if ($deliveryOrderId) {
            $order = SalesOrder::with('lineItems')->find($deliveryOrderId);
            if (!$order) {
                session(['delivery_from_order' => null]);
                $deliveryOrderId = null;
            }
        }

        // Search filters
        $searchOrderNo = $request->input('order_no', '');
        $searchCustomer = $request->input('customer_id', '');
        $searchLocation = $request->input('location', '');
        $searchFrom = $request->input('from_date', '');
        $searchTo = $request->input('to_date', '');

        if (!$deliveryOrderId) {
            // Query outstanding orders (not delivered/cancelled/invoiced)
            $query = SalesOrder::with('customer')
                ->whereNotIn('status', ['delivered', 'cancelled', 'invoiced']);

            if ($searchOrderNo) {
                $query->where('order_number', 'like', '%' . $searchOrderNo . '%');
            }
            if ($searchCustomer) {
                $query->where('customer_id', $searchCustomer);
            }
            if ($searchLocation) {
                $query->where('location', $searchLocation);
            }
            if ($searchFrom) {
                $query->whereDate('order_date', '>=', $searchFrom);
            }
            if ($searchTo) {
                $query->whereDate('order_date', '<=', $searchTo);
            }

            $orders = $query->orderBy('order_date', 'desc')->get();
        } else {
            $orders = collect();
        }

        $salesTypes = \App\Models\SalesType::active()->get();
        $shippers = \DB::table('shippers')->where('inactive', false)->orderBy('shipper_name')->get();
        $items = Item::where('is_active', true)->orderBy('code')->get();

        return view('sales.delivery.from-order', compact(
            'customers', 'locations', 'orders', 'deliveryOrderId', 'order',
            'searchOrderNo', 'searchCustomer', 'searchLocation', 'searchFrom', 'searchTo',
            'salesTypes', 'shippers', 'items', 'message', 'error'
        ));
    }

    private function generateBranchCode(int $customerId): string
    {
        $customer = Customer::find($customerId);
        $prefix = $customer->customer_code . '-';
        
        $lastBranch = CustomerBranch::where('customer_id', $customerId)
            ->where('branch_code', 'like', $prefix . '%')
            ->orderBy('branch_code', 'desc')
            ->first();

        if ($lastBranch) {
            $lastNumber = (int) str_replace($prefix, '', $lastBranch->branch_code);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
