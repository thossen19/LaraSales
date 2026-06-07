<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\TaxGroup;
use App\Models\PaymentTerm;
use App\Models\Currency;
use App\Models\Dimension;
use App\Models\Item;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentAllocation;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchasesController extends Controller
{
    public function suppliersIndex(Request $request): View|RedirectResponse
    {
        $supplier_id = $request->input('supplier_id', '');
        $show_inactive = $request->has('show_inactive');
        $submit = $request->input('submit', '');
        $delete = $request->input('delete', '');

        if ($submit) {
            $validated = $request->validate([
                'supp_name' => 'required|string|max:255',
                'supp_ref' => 'required|string|max:30',
                'address' => 'nullable|string',
                'physical_address' => 'nullable|string',
                'gst_no' => 'nullable|string|max:40',
                'website' => 'nullable|string|max:255',
                'supp_account_no' => 'nullable|string|max:255',
                'bank_account' => 'nullable|string|max:255',
                'credit_limit' => 'nullable|numeric|min:0',
                'curr_code' => 'required|string|max:3',
                'tax_group_id' => 'nullable|exists:tax_groups,id',
                'tax_included' => 'nullable|boolean',
                'payment_terms' => 'nullable|string|max:255',
                'payable_account' => 'nullable|string|max:255',
                'purchase_account' => 'nullable|string|max:255',
                'payment_discount_account' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'dimension_id' => 'nullable|integer|min:0',
                'dimension2_id' => 'nullable|integer|min:0',
                'contact_person' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:50',
                'phone2' => 'nullable|string|max:50',
                'fax' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'rep_lang' => 'nullable|string|max:20',
                'inactive' => 'nullable|boolean',
            ]);

            $data = [
                'name' => $request->supp_name,
                'supp_ref' => $request->supp_ref,
                'address' => $request->address ?? '',
                'physical_address' => $request->physical_address ?? '',
                'gst_no' => $request->gst_no ?? '',
                'website' => $request->website ?? '',
                'supp_account_no' => $request->supp_account_no ?? '',
                'bank_account' => $request->bank_account ?? '',
                'credit_limit' => $request->credit_limit ?? 0,
                'curr_code' => $request->curr_code,
                'tax_group_id' => $request->tax_group_id,
                'tax_included' => $request->boolean('tax_included'),
                'payment_terms' => $request->payment_terms ?? '',
                'payable_account' => $request->payable_account ?? '',
                'purchase_account' => $request->purchase_account ?? '',
                'payment_discount_account' => $request->payment_discount_account ?? '',
                'notes' => $request->notes ?? '',
                'dimension_id' => $request->dimension_id ?? 0,
                'dimension2_id' => $request->dimension2_id ?? 0,
                'inactive' => $request->boolean('inactive'),
                'is_active' => !$request->boolean('inactive'),
            ];

            // Copy FA contact fields only for new suppliers
            if (!$supplier_id) {
                $data['contact_person'] = $request->contact_person ?? '';
                $data['phone'] = $request->phone ?? '';
                $data['phone2'] = $request->phone2 ?? '';
                $data['fax'] = $request->fax ?? '';
                $data['email'] = $request->email ?? '';
                $data['rep_lang'] = $request->rep_lang ?? '';
            } else {
                $existing = Supplier::find($supplier_id);
                if ($existing) {
                    $data['contact_person'] = $request->contact_person ?? $existing->contact_person;
                    $data['phone'] = $request->phone ?? $existing->phone;
                    $data['phone2'] = $request->phone2 ?? $existing->phone2;
                    $data['fax'] = $request->fax ?? $existing->fax;
                    $data['email'] = $request->email ?? $existing->email;
                    $data['rep_lang'] = $request->rep_lang ?? $existing->rep_lang;
                }
            }

            if ($supplier_id) {
                $supplier = Supplier::findOrFail($supplier_id);
                $supplier->update($data);
                session()->flash('success', 'Supplier has been updated.');
            } else {
                $supplier = Supplier::create($data);
                $supplier_id = $supplier->id;
                session()->flash('success', 'A new supplier has been added.');
            }

            return redirect()->route('purchases.suppliers.index', ['supplier_id' => $supplier_id, 'show_inactive' => $show_inactive ? '1' : null]);
        }

        if ($delete) {
            $supplier = Supplier::find($supplier_id);
            if ($supplier) {
                $po_count = \DB::table('purchase_orders')->where('supplier_id', $supplier_id)->count();
                if ($po_count > 0) {
                    session()->flash('error', 'Cannot delete the supplier record because purchase orders have been created against this supplier.');
                } else {
                    $supplier->delete();
                    $supplier_id = '';
                    session()->flash('success', '#' . $supplier_id . ' Supplier has been deleted.');
                }
            }
            return redirect()->route('purchases.suppliers.index', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        // Build suppliers list
        $suppliersQuery = Supplier::query();
        if (!$show_inactive) {
            $suppliersQuery->where('inactive', false);
        }
        $suppliers = $suppliersQuery->orderBy('name')->get();

        // Load selected supplier
        $edit_supplier = null;
        if ($supplier_id) {
            $edit_supplier = Supplier::find($supplier_id);
        }

        // Compute form field values from edit_supplier or defaults
        $form = [];
        $src = $edit_supplier;
        $form['supp_name'] = $src ? $src->name : '';
        $form['supp_ref'] = $src ? $src->supp_ref : '';
        $form['address'] = $src ? $src->address : '';
        $form['physical_address'] = $src ? $src->physical_address : '';
        $form['gst_no'] = $src ? $src->gst_no : '';
        $form['website'] = $src ? $src->website : '';
        $form['supp_account_no'] = $src ? $src->supp_account_no : '';
        $form['bank_account'] = $src ? $src->bank_account : '';
        $form['credit_limit'] = $src ? number_format($src->credit_limit, 2) : '0.00';
        $form['curr_code'] = $src ? $src->curr_code : 'USD';
        $form['tax_group_id'] = $src ? $src->tax_group_id : '';
        $form['tax_included'] = $src ? $src->tax_included : false;
        $form['payment_terms'] = $src ? $src->payment_terms : '';
        $form['payable_account'] = $src ? $src->payable_account : '';
        $form['purchase_account'] = $src ? $src->purchase_account : '';
        $form['payment_discount_account'] = $src ? $src->payment_discount_account : '';
        $form['notes'] = $src ? $src->notes : '';
        $form['dimension_id'] = $src ? ($src->dimension_id ?? 0) : 0;
        $form['dimension2_id'] = $src ? ($src->dimension2_id ?? 0) : 0;
        $form['inactive'] = $src ? $src->inactive : false;
        $form['contact_person'] = $src ? $src->contact_person : '';
        $form['phone'] = $src ? $src->phone : '';
        $form['phone2'] = $src ? $src->phone2 : '';
        $form['fax'] = $src ? $src->fax : '';
        $form['email'] = $src ? $src->email : '';
        $form['rep_lang'] = $src ? $src->rep_lang : '';

        $tax_groups = TaxGroup::orderBy('name')->get();
        $currencies = Currency::orderBy('curr_abrev')->get();
        $payment_terms = PaymentTerm::orderBy('terms')->get();
        $dimensions = Dimension::orderBy('id')->get();
        $languages = ['en_GB' => 'English (UK)', 'en_US' => 'English (US)', 'fr_FR' => 'French', 'de_DE' => 'German', 'es_ES' => 'Spanish'];

        return view('purchases.suppliers.index', compact(
            'suppliers', 'edit_supplier', 'supplier_id', 'show_inactive',
            'tax_groups', 'currencies', 'payment_terms', 'dimensions', 'languages',
            'form'
        ));
    }

    public function createOrder(Request $request): View|RedirectResponse
    {
        // Handle ModifyOrderNumber (load existing PO into cart for editing)
        if ($request->isMethod('GET') && $request->has('ModifyOrderNumber')) {
            $po = PurchaseOrder::with('items.item')->find($request->ModifyOrderNumber);
            if ($po) {
                $cart = $this->poToCart($po);
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }
        }

        // Initialize cart from session
        $cart = session('po_cart', $this->initPoCart());

        if ($request->isMethod('POST')) {
            // Handle supplier change
            if ($request->has('supplier_id') && $request->supplier_id != ($cart['supplier_id'] ?? '')) {
                $cart['supplier_id'] = $request->supplier_id;
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $cart['curr_code'] = $supplier->curr_code ?? 'USD';
                    $loc = Location::where('inactive', false)->first();
                    if ($loc) {
                        $cart['location'] = $loc->loc_code;
                        $cart['delivery_address'] = $loc->delivery_address ?? '';
                    }
                }
                // Reset edit line
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['req_del_date'], $cart['item_description']);
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }

            // Handle Edit action
            $edit_id = $this->findSubmit('Edit', $request);
            if ($edit_id !== null && $edit_id >= 0) {
                $cart['edit_line'] = $edit_id;
                if (isset($cart['items'][$edit_id])) {
                    $item = $cart['items'][$edit_id];
                    $cart['stock_id'] = $item['stock_id'];
                    $cart['qty'] = $item['quantity'];
                    $cart['price'] = $item['price'];
                    $cart['req_del_date'] = $item['req_del_date'] ?? '';
                    $cart['item_description'] = $item['item_description'];
                }
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }

            // Handle Delete action
            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id >= 0) {
                if (isset($cart['items'][$delete_id])) {
                    unset($cart['items'][$delete_id]);
                    $cart['items'] = array_values($cart['items']); // re-index
                }
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['req_del_date'], $cart['item_description']);
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }

            // Copy header fields to cart (always)
            $cart['order_date'] = $request->order_date ?? date('Y-m-d');
            $cart['reference'] = $request->reference ?? '';
            $cart['supp_ref'] = $request->supp_ref ?? '';
            $cart['delivery_address'] = $request->delivery_address ?? '';
            $cart['location'] = $request->location ?? $cart['location'] ?? '';
            $cart['comments'] = $request->comments ?? '';
            $cart['dimension_id'] = $request->dimension_id ?? 0;
            $cart['dimension2_id'] = $request->dimension2_id ?? 0;

            // Handle stock_id change (auto-populate item defaults)
            $has_specific_action = $request->has('EnterLine') || $request->has('UpdateLine')
                || $request->has('CancelUpdate') || $request->has('CancelOrder') || $request->has('Commit')
                || $this->findSubmit('Edit', $request) !== null || $this->findSubmit('Delete', $request) !== null;
            if ($request->has('stock_id') && !$has_specific_action) {
                $cart['stock_id'] = $request->stock_id;
                $item_info = Item::where('code', $request->stock_id)->first();
                if ($item_info) {
                    $cart['price'] = $item_info->purchase_price ?? 0;
                    $cart['qty'] = 1;
                    $cart['item_description'] = $item_info->name ?? '';
                    $cart['req_del_date'] = date('Y-m-d', strtotime('+7 days'));
                }
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }

            // Handle EnterLine / UpdateLine
            if ($request->has('EnterLine') || $request->has('UpdateLine')) {
                $stock_id = $request->stock_id ?? '';
                $qty = (float)($request->qty ?? 1);
                $price = (float)($request->price ?? 0);
                $req_del_date = $request->req_del_date ?? '';
                $item_description = $request->item_description ?? '';

                if (!$stock_id) {
                    session()->flash('error', 'Please select an item.');
                } else {
                    $item_info = Item::where('code', $stock_id)->first();
                    $unit = $item_info ? ($item_info->unit_of_measure ?? 'each') : 'each';
                    if (!$item_description) {
                        $item_description = $item_info ? ($item_info->name ?? '') : '';
                    }

                    $line_data = [
                        'stock_id' => $stock_id,
                        'item_description' => $item_description,
                        'quantity' => max(1, $qty),
                        'price' => max(0, $price),
                        'unit' => $unit,
                        'req_del_date' => $req_del_date,
                        'qty_received' => 0,
                    ];

                    if ($request->has('UpdateLine') && $cart['edit_line'] >= 0) {
                        // Update existing line
                        $line_no = $cart['edit_line'];
                        if (isset($cart['items'][$line_no])) {
                            // Preserve received quantity
                            $line_data['qty_received'] = $cart['items'][$line_no]['qty_received'] ?? 0;
                            $cart['items'][$line_no] = $line_data;
                        }
                    } else {
                        // Check for duplicate
                        $duplicate = false;
                        foreach ($cart['items'] as $existing) {
                            if ($existing['stock_id'] == $stock_id) {
                                session()->flash('warning', 'The selected item is already on this order.');
                                $duplicate = true;
                                break;
                            }
                        }
                        if (!$duplicate) {
                            $cart['items'][] = $line_data;
                        }
                    }
                }

                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['req_del_date'], $cart['item_description']);
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }

            // Handle Cancel Update (just clear edit fields)
            if ($request->has('CancelUpdate')) {
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['req_del_date'], $cart['item_description']);
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.orders.create');
            }

            // Handle Cancel Order
            if ($request->has('CancelOrder')) {
                session()->forget('po_cart');
                session()->flash('success', 'Purchase order entry has been cancelled.');
                return redirect()->route('purchases.orders.create');
            }

            // Handle Commit (Place Order / Update Order)
            if ($request->has('Commit')) {
                $validation_errors = [];

                if (!$cart['supplier_id']) {
                    $validation_errors[] = 'There is no supplier selected.';
                }
                if (!$cart['order_date']) {
                    $validation_errors[] = 'The entered order date is invalid.';
                }
                if (!$cart['location']) {
                    $validation_errors[] = 'There is no location specified to move any items into.';
                }
                if (empty($cart['items'])) {
                    $validation_errors[] = 'The order cannot be placed because there are no lines entered on this order.';
                }

                if (!empty($validation_errors)) {
                    foreach ($validation_errors as $err) {
                        session()->flash('error', $err);
                    }
                    session(['po_cart' => $cart]);
                    return redirect()->route('purchases.orders.create');
                }

                // Calculate totals
                $subtotal = 0;
                foreach ($cart['items'] as $item) {
                    $subtotal += $item['quantity'] * $item['price'];
                }
                $total = $subtotal;

                // Create or update the purchase order
                $supplier = Supplier::find($cart['supplier_id']);
                if (!$supplier) {
                    session()->flash('error', 'Supplier not found.');
                    session(['po_cart' => $cart]);
                    return redirect()->route('purchases.orders.create');
                }

                $order_data = [
                    'company_id' => 1,
                    'supplier_id' => $cart['supplier_id'],
                    'order_date' => $cart['order_date'],
                    'reference' => $cart['reference'] ?? '',
                    'supp_ref' => $cart['supp_ref'] ?? '',
                    'expected_date' => null,
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'shipping_amount' => 0,
                    'total_amount' => $total,
                    'paid_amount' => 0,
                    'balance_amount' => $total,
                    'payment_status' => 'pending',
                    'delivery_address' => $cart['delivery_address'] ?? '',
                    'location' => $cart['location'] ?? '',
                    'curr_code' => $supplier->curr_code ?? 'USD',
                    'dimension_id' => $cart['dimension_id'] ?? 0,
                    'dimension2_id' => $cart['dimension2_id'] ?? 0,
                    'notes' => $cart['comments'] ?? '',
                ];

                $is_update = !empty($cart['order_no']);
                if ($is_update) {
                    $order = PurchaseOrder::find($cart['order_no']);
                    if ($order) {
                        $order->update($order_data);
                        $order_data['status'] = $order->status;
                        // Delete old items and re-create
                        $order->items()->delete();
                    } else {
                        $is_update = false;
                        $order = PurchaseOrder::create(array_merge($order_data, [
                            'status' => 'pending',
                            'created_by' => auth()->id() ?? 1,
                        ]));
                    }
                } else {
                    $order = PurchaseOrder::create(array_merge($order_data, [
                        'status' => 'pending',
                        'created_by' => auth()->id() ?? 1,
                    ]));
                }

                // Create order items
                $warehouse = \App\Models\Warehouse::where('name', 'like', '%' . ($cart['location'] ?? '') . '%')->first();
                $warehouse_id = $warehouse ? $warehouse->id : 1;
                foreach ($cart['items'] as $item) {
                    $line_total = $item['quantity'] * $item['price'];
                    $item_model = Item::where('code', $item['stock_id'])->first();
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'item_id' => $item_model->id ?? 0,
                        'warehouse_id' => $warehouse_id,
                        'description' => $item['item_description'],
                        'quantity' => $item['quantity'],
                        'received_quantity' => $item['qty_received'] ?? 0,
                        'unit_price' => $item['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'tax_percentage' => 0,
                        'tax_amount' => 0,
                        'subtotal' => $line_total,
                        'total' => $line_total,
                    ]);
                }

                session()->forget('po_cart');
                $msg = $is_update
                    ? 'Purchase Order has been updated. #' . $order->order_number
                    : 'Purchase Order has been entered. #' . $order->order_number;
                session()->flash('success', $msg);
                return redirect()->route('purchases.orders.create');
            }

            // Handle Update (refresh display - no action needed, just re-render)
            session(['po_cart' => $cart]);
            return redirect()->route('purchases.orders.create');
        }

        // GET request - generate reference if not set
        if (!$cart['reference']) {
            $cart['reference'] = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            session(['po_cart' => $cart]);
        }

        // Load data for the form
        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $items = Item::where('is_active', true)->where('no_purchase', false)->orderBy('code')->get(['code', 'name', 'unit_of_measure']);
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();
        $dimensions = Dimension::orderBy('id')->get();
        $active_dimensions = Dimension::where('is_active', true)->orderBy('name')->get();

        $supplier = null;
        if ($cart['supplier_id']) {
            $supplier = Supplier::find($cart['supplier_id']);
        }

        // Get item info for current selection in add row
        $selected_item_info = null;
        if (!empty($cart['stock_id'])) {
            $selected_item_info = Item::where('code', $cart['stock_id'])->first();
        }

        $mode = 'po';
        return view('purchases.orders.create', compact(
            'cart', 'suppliers', 'items', 'locations', 'dimensions', 'active_dimensions', 'supplier', 'selected_item_info', 'mode'
        ));
    }

    public function outstandingOrders(Request $request): View
    {
        // Load dropdown data for filters
        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();
        $items = Item::where('is_active', true)->where('no_purchase', false)->orderBy('code')->get(['code', 'name']);

        // Get filter values from request
        $order_no = $request->input('order_no', '');
        $from_date = $request->input('from_date', date('Y-m-d', strtotime('-30 days')));
        $to_date = $request->input('to_date', date('Y-m-d'));
        $location = $request->input('location', '');
        $item_code = $request->input('item_code', '');
        $supplier_id = $request->input('supplier_id', '');

        // Build query
        $query = PurchaseOrder::with('supplier')
            ->whereIn('status', ['pending', 'partial']);

        if ($order_no !== '') {
            $query->where(function ($q) use ($order_no) {
                $q->where('order_number', 'LIKE', "%{$order_no}%")
                  ->orWhere('id', is_numeric($order_no) ? $order_no : 0);
            });
        } else {
            if ($from_date) {
                $query->whereDate('order_date', '>=', $from_date);
            }
            if ($to_date) {
                $query->whereDate('order_date', '<=', $to_date);
            }
            if ($location !== '') {
                $query->where('location', $location);
            }
            if ($item_code !== '') {
                $query->whereHas('items', function ($q) use ($item_code) {
                    $q->whereHas('item', function ($iq) use ($item_code) {
                        $iq->where('code', $item_code);
                    });
                });
            }
        }

        if ($supplier_id !== '') {
            $query->where('supplier_id', $supplier_id);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);

        // Compute overdue status for each order
        $overdue_ids = [];
        $today = now()->toDateString();
        foreach ($orders as $o) {
            if ($o->expected_date && $o->expected_date < $today && $o->status !== 'received') {
                $overdue_ids[] = $o->id;
            }
        }

        return view('purchases.orders.outstanding', compact(
            'orders', 'suppliers', 'locations', 'items',
            'order_no', 'from_date', 'to_date', 'location', 'item_code', 'supplier_id',
            'overdue_ids'
        ));
    }

    /**
     * Initialize a new empty PO cart.
     */
    private function initPoCart(): array
    {
        return [
            'trans_type' => 'po',
            'order_no' => 0,
            'supplier_id' => '',
            'order_date' => date('Y-m-d'),
            'reference' => '',
            'supp_ref' => '',
            'delivery_address' => '',
            'location' => '',
            'comments' => '',
            'dimension_id' => 0,
            'dimension2_id' => 0,
            'items' => [],
            'edit_line' => -1,
        ];
    }

    /**
     * Convert a PurchaseOrder with items to a session cart array.
     */
    private function poToCart(PurchaseOrder $po): array
    {
        $cart = $this->initPoCart();
        $cart['order_no'] = $po->id;
        $cart['supplier_id'] = $po->supplier_id;
        $cart['order_date'] = $po->order_date instanceof \Carbon\Carbon ? $po->order_date->format('Y-m-d') : $po->order_date;
        $cart['reference'] = $po->reference ?? $po->order_number;
        $cart['supp_ref'] = $po->supp_ref ?? '';
        $cart['delivery_address'] = $po->delivery_address ?? '';
        $cart['location'] = $po->location ?? '';
        $cart['comments'] = $po->notes ?? '';
        $cart['dimension_id'] = $po->dimension_id ?? 0;
        $cart['dimension2_id'] = $po->dimension2_id ?? 0;

        foreach ($po->items as $line) {
            $item_code = $line->item ? $line->item->code : '';
            $cart['items'][] = [
                'stock_id' => $item_code,
                'item_description' => $line->description ?? '',
                'quantity' => $line->quantity,
                'price' => $line->unit_price,
                'unit' => $line->item->unit_of_measure ?? 'each',
                'req_del_date' => '',
                'qty_received' => $line->received_quantity,
            ];
        }

        return $cart;
    }

    public function receiveOrder(Request $request): View|RedirectResponse
    {
        $po_id = $request->input('id', $request->input('PONumber', 0));
        $po = PurchaseOrder::with(['items.item', 'supplier'])->find($po_id);

        if (!$po) {
            session()->flash('error', 'Purchase order not found.');
            return redirect()->route('purchases.orders.outstanding');
        }

        if ($request->isMethod('POST')) {
            $ref = $request->input('ref', 'GRN-' . date('Ymd') . '-' . $po_id);
            $delivery_date = $request->input('delivery_date', date('Y-m-d'));
            $location = $request->input('location', $po->location ?? '');

            $any_received = false;
            foreach ($po->items as $line) {
                $receive_qty = (int)($request->input('receive_qty_' . $line->id, 0));
                if ($receive_qty > 0) {
                    $any_received = true;
                    $new_received = $line->received_quantity + $receive_qty;
                    if ($new_received > $line->quantity) {
                        $new_received = $line->quantity;
                    }
                    $line->update(['received_quantity' => $new_received]);
                }
            }

            if (!$any_received) {
                session()->flash('error', 'There is nothing to process. Please enter valid quantities greater than zero.');
                return redirect()->route('purchases.orders.receive', ['id' => $po_id]);
            }

            // Update PO status
            $total_qty = $po->items->sum('quantity');
            $total_received = $po->items->sum('received_quantity');
            $new_status = ($total_received >= $total_qty) ? 'received' : 'partial';
            $po->update(['status' => $new_status]);

            session()->flash('success', 'Purchase Order Delivery has been processed.');
            return redirect()->route('purchases.orders.outstanding');
        }

        $locations = Location::where('inactive', false)->orderBy('location_name')->get();

        return view('purchases.orders.receive', compact('po', 'locations'));
    }

    public function printOrder(int $id)
    {
        $po = PurchaseOrder::with(['items.item', 'supplier'])->findOrFail($id);

        $pdf = Pdf::loadView('purchases.orders.print', compact('po'));
        return $pdf->download('PO-' . $po->order_number . '.pdf');
    }

    public function directGrn(Request $request): View|RedirectResponse
    {
        $cart = session('po_cart', $this->initPoCart());
        $cart['trans_type'] = 'grn';

        if ($request->isMethod('POST')) {
            // Copy header fields to cart
            $cart['order_date'] = $request->order_date ?? date('Y-m-d');
            $cart['reference'] = $request->reference ?? '';
            $cart['supp_ref'] = $request->supp_ref ?? '';
            $cart['delivery_address'] = $request->delivery_address ?? '';
            $cart['location'] = $request->location ?? $cart['location'] ?? '';
            $cart['comments'] = $request->comments ?? '';
            $cart['dimension_id'] = $request->dimension_id ?? 0;
            $cart['dimension2_id'] = $request->dimension2_id ?? 0;

            // Handle EnterLine / UpdateLine
            if ($request->has('EnterLine') || $request->has('UpdateLine')) {
                $stock_id = $request->stock_id ?? '';
                $qty = (float)($request->qty ?? 1);
                $price = (float)($request->price ?? 0);
                $item_description = $request->item_description ?? '';

                if ($stock_id) {
                    $item_info = Item::where('code', $stock_id)->first();
                    $unit = $item_info ? ($item_info->unit_of_measure ?? 'each') : 'each';
                    if (!$item_description) {
                        $item_description = $item_info ? ($item_info->name ?? '') : '';
                    }
                    $line_data = [
                        'stock_id' => $stock_id,
                        'item_description' => $item_description,
                        'quantity' => max(1, $qty),
                        'price' => max(0, $price),
                        'unit' => $unit,
                        'req_del_date' => '',
                        'qty_received' => 0,
                    ];
                    if ($request->has('UpdateLine') && $cart['edit_line'] >= 0) {
                        $cart['items'][$cart['edit_line']] = $line_data;
                    } else {
                        $cart['items'][] = $line_data;
                    }
                }
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['po_cart' => $cart]);
                return redirect()->route('purchases.grn.direct');
            }

            if ($request->has('CancelOrder')) {
                session()->forget('po_cart');
                session()->flash('success', 'Direct GRN entry has been cancelled.');
                return redirect()->route('purchases.grn.direct');
            }

            if ($request->has('Commit')) {
                if (!$cart['supplier_id'] || empty($cart['items'])) {
                    if (!$cart['supplier_id']) session()->flash('error', 'There is no supplier selected.');
                    if (empty($cart['items'])) session()->flash('error', 'The GRN cannot be placed because there are no lines entered.');
                    session(['po_cart' => $cart]);
                    return redirect()->route('purchases.grn.direct');
                }
                $subtotal = 0;
                foreach ($cart['items'] as $item) { $subtotal += $item['quantity'] * $item['price']; }
                $supplier = Supplier::find($cart['supplier_id']);
                $order = PurchaseOrder::create([
                    'company_id' => 1,
                    'supplier_id' => $cart['supplier_id'],
                    'order_date' => $cart['order_date'],
                    'reference' => $cart['reference'] ?? '',
                    'supp_ref' => $cart['supp_ref'] ?? '',
                    'expected_date' => null,
                    'status' => 'received',
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'shipping_amount' => 0,
                    'total_amount' => $subtotal,
                    'paid_amount' => 0,
                    'balance_amount' => $subtotal,
                    'payment_status' => 'pending',
                    'delivery_address' => $cart['delivery_address'] ?? '',
                    'location' => $cart['location'] ?? '',
                    'curr_code' => $supplier->curr_code ?? 'USD',
                    'dimension_id' => $cart['dimension_id'] ?? 0,
                    'dimension2_id' => $cart['dimension2_id'] ?? 0,
                    'notes' => $cart['comments'] ?? '',
                    'created_by' => auth()->id() ?? 1,
                ]);
                $warehouse = \App\Models\Warehouse::where('name', 'like', '%' . ($cart['location'] ?? '') . '%')->first();
                foreach ($cart['items'] as $item) {
                    $line_total = $item['quantity'] * $item['price'];
                    $item_model = Item::where('code', $item['stock_id'])->first();
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'item_id' => $item_model->id ?? 0,
                        'warehouse_id' => $warehouse ? $warehouse->id : 1,
                        'description' => $item['item_description'],
                        'quantity' => $item['quantity'],
                        'received_quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'tax_percentage' => 0,
                        'tax_amount' => 0,
                        'subtotal' => $line_total,
                        'total' => $line_total,
                    ]);
                }
                session()->forget('po_cart');
                session()->flash('success', 'Direct GRN has been entered.');
                return redirect()->route('purchases.grn.direct');
            }

            session(['po_cart' => $cart]);
            return redirect()->route('purchases.grn.direct');
        }

        // GET - generate reference
        if (!$cart['reference']) {
            $cart['reference'] = 'GRN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            session(['po_cart' => $cart]);
        }

        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $items = Item::where('is_active', true)->where('no_purchase', false)->orderBy('code')->get(['code', 'name', 'unit_of_measure']);
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();
        $dimensions = Dimension::orderBy('id')->get();
        $supplier = $cart['supplier_id'] ? Supplier::find($cart['supplier_id']) : null;
        $selected_item_info = !empty($cart['stock_id']) ? Item::where('code', $cart['stock_id'])->first() : null;
        $mode = 'grn';

        return view('purchases.orders.create', compact(
            'cart', 'suppliers', 'items', 'locations', 'dimensions', 'supplier', 'selected_item_info', 'mode'
        ));
    }

    public function directInvoice(Request $request): View|RedirectResponse
    {
        $cart = session('invoice_cart', $this->initInvoiceCart());

        if ($request->isMethod('POST')) {
            // Handle supplier change
            if ($request->has('supplier_id') && $request->supplier_id != ($cart['supplier_id'] ?? '')) {
                $cart['supplier_id'] = $request->supplier_id;
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $cart['curr_code'] = $supplier->curr_code ?? 'USD';
                    $cart['tax_included'] = $supplier->tax_included ?? false;
                    // Calculate due date from payment terms
                    if ($supplier->payment_terms) {
                        $terms = PaymentTerm::where('terms_indicator', $supplier->payment_terms)->first();
                        if ($terms) {
                            $days = $terms->days_before_due ?? 0;
                            $cart['due_date'] = date('Y-m-d', strtotime('+' . $days . ' days'));
                        }
                    }
                    $loc = Location::where('inactive', false)->first();
                    if ($loc) {
                        $cart['location'] = $loc->loc_code;
                        $cart['delivery_address'] = $loc->delivery_address ?? '';
                    }
                }
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['invoice_cart' => $cart]);
                return redirect()->route('purchases.invoice.direct');
            }

            // Copy header fields
            $cart['invoice_date'] = $request->invoice_date ?? date('Y-m-d');
            $cart['due_date'] = $request->due_date ?? $cart['due_date'] ?? date('Y-m-d');
            $cart['reference'] = $request->reference ?? '';
            $cart['supp_reference'] = $request->supp_reference ?? '';
            $cart['delivery_address'] = $request->delivery_address ?? '';
            $cart['location'] = $request->location ?? $cart['location'] ?? '';
            $cart['comments'] = $request->comments ?? '';
            $cart['dimension_id'] = $request->dimension_id ?? 0;
            $cart['dimension2_id'] = $request->dimension2_id ?? 0;
            $cart['cash_account_id'] = $request->cash_account_id ?? '';

            // Handle Edit action
            $edit_id = $this->findSubmit('Edit', $request);
            if ($edit_id !== null && $edit_id >= 0) {
                $cart['edit_line'] = $edit_id;
                if (isset($cart['items'][$edit_id])) {
                    $item = $cart['items'][$edit_id];
                    $cart['stock_id'] = $item['stock_id'];
                    $cart['qty'] = $item['quantity'];
                    $cart['price'] = $item['price'];
                    $cart['item_description'] = $item['item_description'];
                }
                session(['invoice_cart' => $cart]);
                return redirect()->route('purchases.invoice.direct');
            }

            // Handle Delete action
            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id >= 0) {
                if (isset($cart['items'][$delete_id])) {
                    unset($cart['items'][$delete_id]);
                    $cart['items'] = array_values($cart['items']);
                }
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['invoice_cart' => $cart]);
                return redirect()->route('purchases.invoice.direct');
            }

            // Handle stock_id change (auto-populate)
            $has_specific_action = $request->has('EnterLine') || $request->has('UpdateLine')
                || $request->has('CancelUpdate') || $request->has('CancelOrder') || $request->has('Commit')
                || $this->findSubmit('Edit', $request) !== null || $this->findSubmit('Delete', $request) !== null;
            if ($request->has('stock_id') && !$has_specific_action) {
                $cart['stock_id'] = $request->stock_id;
                $item_info = Item::where('code', $request->stock_id)->first();
                if ($item_info) {
                    $cart['price'] = $this->getPurchasePrice($cart['supplier_id'], $request->stock_id) ?: ($item_info->purchase_price ?? 0);
                    $cart['qty'] = 1;
                    $cart['item_description'] = $item_info->name ?? '';
                }
                session(['invoice_cart' => $cart]);
                return redirect()->route('purchases.invoice.direct');
            }

            // Handle EnterLine / UpdateLine
            if ($request->has('EnterLine') || $request->has('UpdateLine')) {
                $stock_id = $request->stock_id ?? '';
                $qty = (float)($request->qty ?? 1);
                $price = (float)($request->price ?? 0);
                $item_description = $request->item_description ?? '';

                if (!$stock_id) {
                    session()->flash('error', 'Please select an item.');
                } else {
                    $item_info = Item::where('code', $stock_id)->first();
                    $unit = $item_info ? ($item_info->unit_of_measure ?? 'each') : 'each';
                    if (!$item_description) {
                        $item_description = $item_info ? ($item_info->name ?? '') : '';
                    }
                    $line_data = [
                        'stock_id' => $stock_id,
                        'item_description' => $item_description,
                        'quantity' => max(1, $qty),
                        'price' => max(0, $price),
                        'unit' => $unit,
                    ];

                    if ($request->has('UpdateLine') && $cart['edit_line'] >= 0) {
                        $cart['items'][$cart['edit_line']] = $line_data;
                    } else {
                        $duplicate = false;
                        foreach ($cart['items'] as $existing) {
                            if ($existing['stock_id'] == $stock_id) {
                                session()->flash('warning', 'The selected item is already on this order.');
                                $duplicate = true;
                                break;
                            }
                        }
                        if (!$duplicate) {
                            $cart['items'][] = $line_data;
                        }
                    }
                }

                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['invoice_cart' => $cart]);
                return redirect()->route('purchases.invoice.direct');
            }

            // Handle CancelUpdate
            if ($request->has('CancelUpdate')) {
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['invoice_cart' => $cart]);
                return redirect()->route('purchases.invoice.direct');
            }

            // Handle Cancel Invoice
            if ($request->has('CancelOrder')) {
                session()->forget('invoice_cart');
                session()->flash('success', 'Direct purchase invoice entry has been cancelled.');
                return redirect()->route('purchases.invoice.direct');
            }

            // Handle Commit (Process Invoice)
            if ($request->has('Commit')) {
                $validation_errors = [];

                if (!$cart['supplier_id']) {
                    $validation_errors[] = 'There is no supplier selected.';
                }
                if (!$cart['invoice_date']) {
                    $validation_errors[] = 'The entered invoice date is invalid.';
                }
                if (!$cart['due_date']) {
                    $validation_errors[] = 'The entered due date is invalid.';
                }
                if (empty(trim($cart['supp_reference'] ?? ''))) {
                    $validation_errors[] = 'You must enter a supplier\'s invoice reference.';
                }
                // Check duplicate supp_reference for this supplier
                if (!empty($cart['supp_reference']) && $cart['supplier_id']) {
                    $existing = SupplierInvoice::where('supplier_id', $cart['supplier_id'])
                        ->where('supp_reference', $cart['supp_reference'])
                        ->where('status', '!=', 'cancelled')
                        ->count();
                    if ($existing > 0) {
                        $validation_errors[] = 'This invoice number has already been entered. It cannot be entered again.';
                    }
                }
                if (!$cart['location']) {
                    $validation_errors[] = 'There is no location specified to move any items into.';
                }
                if (empty($cart['items'])) {
                    $validation_errors[] = 'The invoice cannot be placed because there are no lines entered.';
                }

                if (!empty($validation_errors)) {
                    foreach ($validation_errors as $err) {
                        session()->flash('error', $err);
                    }
                    session(['invoice_cart' => $cart]);
                    return redirect()->route('purchases.invoice.direct');
                }

                // Calculate totals
                $subtotal = 0;
                foreach ($cart['items'] as $item) {
                    $subtotal += $item['quantity'] * $item['price'];
                }
                $total = $subtotal;
                $supplier = Supplier::find($cart['supplier_id']);
                if (!$supplier) {
                    session()->flash('error', 'Supplier not found.');
                    session(['invoice_cart' => $cart]);
                    return redirect()->route('purchases.invoice.direct');
                }

                // Generate invoice number
                $inv_prefix = 'PI-' . date('Ymd') . '-';
                $last_inv = SupplierInvoice::where('invoice_number', 'like', $inv_prefix . '%')
                    ->orderBy('invoice_number', 'desc')->first();
                $seq = $last_inv ? (intval(substr($last_inv->invoice_number, -4)) + 1) : 1;
                $invoice_number = $inv_prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

                $invoice = SupplierInvoice::create([
                    'company_id' => 1,
                    'supplier_id' => $cart['supplier_id'],
                    'invoice_number' => $invoice_number,
                    'reference' => $cart['reference'] ?? '',
                    'supp_reference' => $cart['supp_reference'] ?? '',
                    'invoice_date' => $cart['invoice_date'],
                    'due_date' => $cart['due_date'],
                    'location' => $cart['location'] ?? '',
                    'delivery_address' => $cart['delivery_address'] ?? '',
                    'currency' => $supplier->curr_code ?? 'USD',
                    'exchange_rate' => 1,
                    'subtotal' => $subtotal,
                    'tax_total' => 0,
                    'total_amount' => $total,
                    'alloc' => 0,
                    'cash_account_id' => $cart['cash_account_id'] ?: null,
                    'dimension_id' => $cart['dimension_id'] ?? 0,
                    'dimension2_id' => $cart['dimension2_id'] ?? 0,
                    'comments' => $cart['comments'] ?? '',
                    'status' => $cart['cash_account_id'] ? 'paid' : 'approved',
                    'created_by' => auth()->id() ?? 1,
                ]);

                foreach ($cart['items'] as $item) {
                    $line_total = $item['quantity'] * $item['price'];
                    $item_model = Item::where('code', $item['stock_id'])->first();
                    SupplierInvoiceItem::create([
                        'supplier_invoice_id' => $invoice->id,
                        'item_id' => $item_model->id ?? null,
                        'stock_id' => $item['stock_id'],
                        'description' => $item['item_description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'unit' => $item['unit'] ?? 'each',
                        'line_total' => $line_total,
                        'gl_code' => null,
                        'dimension_id' => 0,
                        'dimension2_id' => 0,
                    ]);
                }

                // If cash account selected, mark as paid
                if ($cart['cash_account_id']) {
                    $invoice->update(['status' => 'paid']);
                }

                session()->forget('invoice_cart');
                session()->flash('success', 'Direct Purchase Invoice has been entered. #' . $invoice_number);
                return redirect()->route('purchases.invoice.direct');
            }

            session(['invoice_cart' => $cart]);
            return redirect()->route('purchases.invoice.direct');
        }

        // GET - generate reference
        if (!$cart['reference']) {
            $cart['reference'] = 'PI-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            session(['invoice_cart' => $cart]);
        }

        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $items = Item::where('is_active', true)->where('no_purchase', false)->orderBy('code')->get(['code', 'name', 'unit_of_measure']);
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();
        $dimensions = Dimension::orderBy('id')->get();
        $cashAccounts = BankAccount::where('inactive', false)->orderBy('bank_account_name')->get();

        $supplier = $cart['supplier_id'] ? Supplier::find($cart['supplier_id']) : null;
        $selected_item_info = !empty($cart['stock_id']) ? Item::where('code', $cart['stock_id'])->first() : null;

        return view('purchases.invoice.direct', compact(
            'cart', 'suppliers', 'items', 'locations', 'dimensions', 'cashAccounts',
            'supplier', 'selected_item_info'
        ));
    }

    public function paymentEntry(Request $request): View|RedirectResponse
    {
        $supplier_id = $request->input('supplier_id', session('payment_supplier_id', ''));
        $bank_account_id = $request->input('bank_account_id', session('payment_bank_account_id', ''));
        $payment_date = $request->input('payment_date', session('payment_date', date('Y-m-d')));
        $reference = $request->input('reference', session('payment_reference', ''));
        $memo = $request->input('memo', session('payment_memo', ''));
        $discount = $request->input('discount', session('payment_discount', '0'));
        $amount = $request->input('amount', session('payment_amount', '0'));
        $bank_amount = $request->input('bank_amount', session('payment_bank_amount', ''));
        $bank_charge = $request->input('bank_charge', session('payment_bank_charge', '0'));
        $dimension_id = $request->input('dimension_id', session('payment_dimension_id', 0));
        $dimension2_id = $request->input('dimension2_id', session('payment_dimension2_id', 0));
        $allocations = session('payment_allocations', []);

        if ($request->isMethod('POST')) {
            // Handle supplier change
            if ($request->has('supplier_id') && $request->supplier_id != session('payment_supplier_id')) {
                $supplier_id = $request->supplier_id;
                session(['payment_supplier_id' => $supplier_id]);
                session(['payment_allocations' => []]);
                // Set default dimension from supplier
                $supp = Supplier::find($supplier_id);
                if ($supp) {
                    session(['payment_dimension_id' => $supp->dimension_id ?? 0]);
                    session(['payment_dimension2_id' => $supp->dimension2_id ?? 0]);
                }
                // Load default bank account from previous payments
                $last_payment = SupplierPayment::where('supplier_id', $supplier_id)
                    ->orderBy('id', 'desc')->first();
                if ($last_payment) {
                    session(['payment_bank_account_id' => $last_payment->bank_account_id]);
                }
                return redirect()->route('purchases.payments.index');
            }

            // Handle bank account change
            if ($request->has('bank_account_id') && $request->bank_account_id != session('payment_bank_account_id')) {
                session(['payment_bank_account_id' => $request->bank_account_id]);
                return redirect()->route('purchases.payments.index');
            }

            // Store form fields in session
            session([
                'payment_supplier_id' => $request->supplier_id ?? '',
                'payment_bank_account_id' => $request->bank_account_id ?? '',
                'payment_date' => $request->payment_date ?? date('Y-m-d'),
                'payment_reference' => $request->reference ?? '',
                'payment_memo' => $request->memo ?? '',
                'payment_discount' => $request->discount ?? '0',
                'payment_amount' => $request->amount ?? '0',
                'payment_bank_amount' => $request->bank_amount ?? '',
                'payment_bank_charge' => $request->bank_charge ?? '0',
                'payment_dimension_id' => $request->dimension_id ?? 0,
                'payment_dimension2_id' => $request->dimension2_id ?? 0,
            ]);

            // Update allocations from POST
            $alloc_post = [];
            $total_allocated = 0;
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'alloc_') === 0 && is_numeric(substr($key, 6))) {
                    $inv_id = (int)substr($key, 6);
                    $amt = (float)$value;
                    if ($amt > 0) {
                        $alloc_post[$inv_id] = $amt;
                        $total_allocated += $amt;
                    }
                }
            }
            session(['payment_allocations' => $alloc_post]);

            // Handle Process Payment
            if ($request->has('ProcessPayment')) {
                $validation_errors = [];

                if (!$supplier_id) {
                    $validation_errors[] = 'There is no supplier selected.';
                }
                if (!$bank_account_id) {
                    $validation_errors[] = 'There is no bank account selected.';
                }
                $pay_amount = (float)($request->amount ?? 0);
                if ($pay_amount <= 0) {
                    $validation_errors[] = 'The entered amount is invalid or less than zero.';
                }
                if (!$payment_date) {
                    $validation_errors[] = 'The entered date is invalid.';
                }
                if (!$reference) {
                    $validation_errors[] = 'The reference must not be empty.';
                }

                if (!empty($validation_errors)) {
                    foreach ($validation_errors as $err) {
                        session()->flash('error', $err);
                    }
                    return redirect()->route('purchases.payments.index');
                }

                // Generate payment number
                $pay_prefix = 'PMT-' . date('Ymd') . '-';
                $last_pmt = SupplierPayment::where('payment_number', 'like', $pay_prefix . '%')
                    ->orderBy('payment_number', 'desc')->first();
                $seq = $last_pmt ? (intval(substr($last_pmt->payment_number, -4)) + 1) : 1;
                $payment_number = $pay_prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

                $discount_amt = (float)($request->discount ?? 0);
                $bank_charge_amt = (float)($request->bank_charge ?? 0);
                $bank_amt = $request->bank_amount !== '' ? (float)$request->bank_amount : $pay_amount;
                $supplier = Supplier::find($supplier_id);

                $payment = SupplierPayment::create([
                    'company_id' => 1,
                    'supplier_id' => $supplier_id,
                    'bank_account_id' => $bank_account_id,
                    'payment_number' => $payment_number,
                    'payment_date' => $payment_date,
                    'reference' => $reference,
                    'amount' => $pay_amount,
                    'discount' => $discount_amt,
                    'bank_amount' => $bank_amt,
                    'bank_charge' => $bank_charge_amt,
                    'currency' => $supplier->curr_code ?? 'USD',
                    'exchange_rate' => 1,
                    'dimension_id' => $dimension_id ?? 0,
                    'dimension2_id' => $dimension2_id ?? 0,
                    'memo' => $memo,
                    'status' => 'approved',
                    'created_by' => auth()->id() ?? 1,
                ]);

                // Save allocations and update invoice alloc amounts
                $total_alloc_saved = 0;
                foreach ($alloc_post as $inv_id => $alloc_amt) {
                    $invoice = SupplierInvoice::find($inv_id);
                    if ($invoice && $alloc_amt > 0) {
                        SupplierPaymentAllocation::create([
                            'supplier_payment_id' => $payment->id,
                            'supplier_invoice_id' => $inv_id,
                            'amount' => $alloc_amt,
                        ]);
                        $invoice->increment('alloc', $alloc_amt);
                        $total_alloc_saved += $alloc_amt;
                    }
                }

                // Also allocate discount if specified (proportionally or to the first invoice)
                if ($discount_amt > 0 && !empty($alloc_post)) {
                    reset($alloc_post);
                    $first_inv_id = key($alloc_post);
                    $first_inv = SupplierInvoice::find($first_inv_id);
                    if ($first_inv) {
                        $first_inv->increment('alloc', $discount_amt);
                    }
                }

                // Clear session
                $keys = ['payment_supplier_id', 'payment_bank_account_id', 'payment_date',
                    'payment_reference', 'payment_memo', 'payment_discount', 'payment_amount',
                    'payment_bank_amount', 'payment_bank_charge', 'payment_dimension_id',
                    'payment_dimension2_id', 'payment_allocations'];
                foreach ($keys as $k) { session()->forget($k); }

                session()->flash('success', 'Supplier Payment has been successfully entered. #' . $payment_number);
                return redirect()->route('purchases.payments.index');
            }

            return redirect()->route('purchases.payments.index');
        }

        // GET - generate reference if needed
        if (!$reference) {
            $reference = 'PMT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            session(['payment_reference' => $reference]);
        }

        // Load dropdown data
        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $bankAccounts = BankAccount::where('inactive', false)->orderBy('bank_account_name')->get();
        $dimensions = Dimension::orderBy('id')->get();

        // Get selected supplier
        $supplier = $supplier_id ? Supplier::find($supplier_id) : null;

        // Get outstanding invoices for this supplier
        $outstanding_invoices = [];
        if ($supplier_id) {
            $outstanding_invoices = SupplierInvoice::where('supplier_id', $supplier_id)
                ->whereIn('status', ['approved', 'partial'])
                ->whereRaw('total_amount > alloc')
                ->orderBy('invoice_date')
                ->get();
        }

        // Get bank account info for display
        $bankAccount = $bank_account_id ? BankAccount::find($bank_account_id) : null;
        $supplier_currency = $supplier ? ($supplier->curr_code ?? 'USD') : 'USD';
        $bank_currency = $bankAccount ? ($bankAccount->bank_curr_code ?? 'USD') : 'USD';
        $show_bank_amount = ($bank_currency !== $supplier_currency);

        return view('purchases.payments.index', compact(
            'suppliers', 'bankAccounts', 'dimensions',
            'supplier_id', 'bank_account_id', 'payment_date', 'reference', 'memo',
            'discount', 'amount', 'bank_amount', 'bank_charge',
            'dimension_id', 'dimension2_id', 'allocations',
            'supplier', 'bankAccount', 'outstanding_invoices',
            'supplier_currency', 'bank_currency', 'show_bank_amount'
        ));
    }

    public function supplierInvoice(Request $request): View|RedirectResponse
    {
        $cart = session('supp_inv_cart', $this->initSuppInvCart());

        if ($request->isMethod('POST')) {
            // Handle supplier change
            if ($request->has('supplier_id') && $request->supplier_id != ($cart['supplier_id'] ?? '')) {
                $cart['supplier_id'] = $request->supplier_id;
                // Clear cart items when supplier changes
                $cart['grn_items'] = [];
                $cart['gl_items'] = [];
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $cart['curr_code'] = $supplier->curr_code ?? 'USD';
                    $cart['tax_included'] = $supplier->tax_included ?? false;
                    // Calculate due date from payment terms
                    if ($supplier->payment_terms) {
                        $terms = PaymentTerm::where('terms_indicator', $supplier->payment_terms)->first();
                        if ($terms) {
                            $days = $terms->days_before_due ?? 0;
                            $cart['due_date'] = date('Y-m-d', strtotime('+' . $days . ' days'));
                        }
                    }
                }
                $cart['reference'] = $cart['tran_date'] = date('Y-m-d');
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Copy header fields
            $cart['tran_date'] = $request->tran_date ?? date('Y-m-d');
            $cart['due_date'] = $request->due_date ?? $cart['due_date'] ?? date('Y-m-d');
            $cart['reference'] = $request->reference ?? '';
            $cart['supp_reference'] = $request->supp_reference ?? '';
            $cart['comments'] = $request->comments ?? '';
            $cart['dimension_id'] = $request->dimension_id ?? 0;
            $cart['dimension2_id'] = $request->dimension2_id ?? 0;

            // Handle Add GRN item
            $grn_id = $this->findSubmit('grn_item_id', $request);
            if ($grn_id !== null && $grn_id >= 0) {
                $po_item = PurchaseOrderItem::with(['item', 'purchaseOrder.supplier'])
                    ->find($grn_id);
                if ($po_item) {
                    $outstanding = $po_item->outstanding_inv_qty;
                    $default_qty = max(0, $outstanding);
                    $cart['grn_items'][$grn_id] = [
                        'po_detail_item_id' => $po_item->id,
                        'stock_id' => $po_item->item->code ?? '',
                        'item_description' => $po_item->description ?? '',
                        'qty_recd' => $po_item->received_quantity,
                        'prev_quantity_inv' => $po_item->invoiced_quantity,
                        'this_quantity_inv' => $default_qty,
                        'unit_price' => $po_item->unit_price,
                        'chg_price' => $po_item->unit_price,
                        'order_price' => $po_item->unit_price,
                        'unit' => $po_item->item->unit_of_measure ?? 'each',
                        'line_total' => $default_qty * $po_item->unit_price,
                    ];
                }
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Handle InvGRNAll (add all outstanding items)
            if ($request->has('InvGRNAll')) {
                $supplier_id = $cart['supplier_id'];
                $outstanding_items = $this->getOutstandingGrnItems($supplier_id);
                foreach ($outstanding_items as $po_item) {
                    $id = $po_item->id;
                    $outstanding = $po_item->outstanding_inv_qty;
                    $cart['grn_items'][$id] = [
                        'po_detail_item_id' => $po_item->id,
                        'stock_id' => $po_item->item->code ?? '',
                        'item_description' => $po_item->description ?? '',
                        'qty_recd' => $po_item->received_quantity,
                        'prev_quantity_inv' => $po_item->invoiced_quantity,
                        'this_quantity_inv' => $outstanding,
                        'unit_price' => $po_item->unit_price,
                        'chg_price' => $po_item->unit_price,
                        'order_price' => $po_item->unit_price,
                        'unit' => $po_item->item->unit_of_measure ?? 'each',
                        'line_total' => $outstanding * $po_item->unit_price,
                    ];
                }
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Handle Delete GRN item from cart
            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id >= 0 && isset($cart['grn_items'][$delete_id])) {
                unset($cart['grn_items'][$delete_id]);
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Handle Add GL Code to Trans
            if ($request->has('AddGLCodeToTrans')) {
                $gl_code = $request->gl_code ?? '';
                $amount = (float)($request->gl_amount ?? 0);
                $memo = $request->gl_memo ?? '';
                $dim_id = $request->gl_dimension_id ?? 0;
                $dim2_id = $request->gl_dimension2_id ?? 0;
                if ($gl_code && $amount > 0) {
                    $cart['gl_items'][] = [
                        'gl_code' => $gl_code,
                        'gl_act_name' => $gl_code, // Could look up name
                        'amount' => $amount,
                        'memo' => $memo,
                        'dimension_id' => $dim_id,
                        'dimension2_id' => $dim2_id,
                    ];
                }
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Handle Delete GL item
            $delete_gl_id = $this->findSubmit('Delete2', $request);
            if ($delete_gl_id !== null && $delete_gl_id >= 0 && isset($cart['gl_items'][$delete_gl_id])) {
                array_splice($cart['gl_items'], $delete_gl_id, 1);
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Handle update (refresh)
            if ($request->has('update')) {
                // Update quantities/prices from POST for each GRN item in cart
                if (!empty($cart['grn_items'])) {
                    foreach ($cart['grn_items'] as $id => $grn_item) {
                        $qty_key = 'this_quantity_inv' . $id;
                        $price_key = 'ChgPrice' . $id;
                        if ($request->has($qty_key)) {
                            $cart['grn_items'][$id]['this_quantity_inv'] = max(0, (float)$request->$qty_key);
                        }
                        if ($request->has($price_key)) {
                            $cart['grn_items'][$id]['chg_price'] = max(0, (float)$request->$price_key);
                        }
                        $cart['grn_items'][$id]['line_total'] =
                            $cart['grn_items'][$id]['this_quantity_inv'] * $cart['grn_items'][$id]['chg_price'];
                    }
                }
                session(['supp_inv_cart' => $cart]);
                return redirect()->route('purchases.invoices.index');
            }

            // Handle Commit (PostInvoice)
            if ($request->has('PostInvoice')) {
                $validation_errors = [];

                if (!$cart['supplier_id']) {
                    $validation_errors[] = 'There is no supplier selected.';
                }
                if (empty($cart['grn_items']) && empty($cart['gl_items'])) {
                    $validation_errors[] = 'The invoice cannot be processed because there are no items or values on the invoice.';
                }
                if (!$cart['tran_date']) {
                    $validation_errors[] = 'The invoice date is in an incorrect format.';
                }
                if (!$cart['due_date']) {
                    $validation_errors[] = 'The due date is in an incorrect format.';
                }
                if (empty(trim($cart['supp_reference'] ?? ''))) {
                    $validation_errors[] = 'You must enter a supplier\'s invoice reference.';
                }
                if (!empty($cart['supp_reference']) && $cart['supplier_id']) {
                    $existing = SupplierInvoice::where('supplier_id', $cart['supplier_id'])
                        ->where('supp_reference', $cart['supp_reference'])
                        ->whereIn('status', ['approved', 'partial', 'paid', 'draft'])
                        ->count();
                    if ($existing > 0) {
                        $validation_errors[] = 'This invoice number has already been entered. It cannot be entered again.';
                    }
                }

                if (!empty($validation_errors)) {
                    foreach ($validation_errors as $err) {
                        session()->flash('error', $err);
                    }
                    session(['supp_inv_cart' => $cart]);
                    return redirect()->route('purchases.invoices.index');
                }

                // Calculate totals
                $subtotal = 0;
                foreach ($cart['grn_items'] as $item) {
                    $subtotal += $item['this_quantity_inv'] * $item['chg_price'];
                }
                foreach ($cart['gl_items'] as $item) {
                    $subtotal += $item['amount'];
                }
                $total = $subtotal;

                // Generate invoice number
                $inv_prefix = 'SI-' . date('Ymd') . '-';
                $last_inv = SupplierInvoice::where('invoice_number', 'like', $inv_prefix . '%')
                    ->orderBy('invoice_number', 'desc')->first();
                $seq = $last_inv ? (intval(substr($last_inv->invoice_number, -4)) + 1) : 1;
                $invoice_number = $inv_prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

                $supplier = Supplier::find($cart['supplier_id']);

                $invoice = SupplierInvoice::create([
                    'company_id' => 1,
                    'supplier_id' => $cart['supplier_id'],
                    'invoice_number' => $invoice_number,
                    'reference' => $cart['reference'] ?? '',
                    'supp_reference' => $cart['supp_reference'] ?? '',
                    'invoice_date' => $cart['tran_date'],
                    'due_date' => $cart['due_date'],
                    'location' => '',
                    'delivery_address' => '',
                    'currency' => $supplier->curr_code ?? 'USD',
                    'exchange_rate' => 1,
                    'subtotal' => $subtotal,
                    'tax_total' => 0,
                    'total_amount' => $total,
                    'alloc' => 0,
                    'dimension_id' => $cart['dimension_id'] ?? 0,
                    'dimension2_id' => $cart['dimension2_id'] ?? 0,
                    'comments' => $cart['comments'] ?? '',
                    'status' => 'approved',
                    'created_by' => auth()->id() ?? 1,
                ]);

                // Create invoice items for GRN items
                foreach ($cart['grn_items'] as $id => $item) {
                    $line_total = $item['this_quantity_inv'] * $item['chg_price'];
                    $item_model = Item::where('code', $item['stock_id'])->first();
                    SupplierInvoiceItem::create([
                        'supplier_invoice_id' => $invoice->id,
                        'item_id' => $item_model->id ?? null,
                        'stock_id' => $item['stock_id'],
                        'description' => $item['item_description'],
                        'quantity' => $item['this_quantity_inv'],
                        'unit_price' => $item['chg_price'],
                        'unit' => $item['unit'] ?? 'each',
                        'line_total' => $line_total,
                    ]);

                    // Update PO item invoiced quantity
                    $po_item = PurchaseOrderItem::find($item['po_detail_item_id']);
                    if ($po_item) {
                        $po_item->increment('invoiced_quantity', $item['this_quantity_inv']);
                    }
                }

                // Create invoice items for GL items
                foreach ($cart['gl_items'] as $item) {
                    SupplierInvoiceItem::create([
                        'supplier_invoice_id' => $invoice->id,
                        'item_id' => null,
                        'stock_id' => '',
                        'description' => $item['memo'] ?: ('GL: ' . $item['gl_code']),
                        'quantity' => 1,
                        'unit_price' => $item['amount'],
                        'unit' => '',
                        'line_total' => $item['amount'],
                        'gl_code' => $item['gl_code'],
                        'dimension_id' => $item['dimension_id'] ?? 0,
                        'dimension2_id' => $item['dimension2_id'] ?? 0,
                    ]);
                }

                session()->forget('supp_inv_cart');
                session()->flash('success', 'Supplier Invoice has been processed. #' . $invoice_number);
                return redirect()->route('purchases.invoices.index');
            }

            session(['supp_inv_cart' => $cart]);
            return redirect()->route('purchases.invoices.index');
        }

        // GET - generate reference
        if (!$cart['reference']) {
            $date_part = date('Ymd');
            $cart['reference'] = 'SI-' . $date_part . '-' . strtoupper(substr(uniqid(), -4));
            session(['supp_inv_cart' => $cart]);
        }

        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $dimensions = Dimension::orderBy('id')->get();
        $supplier = $cart['supplier_id'] ? Supplier::find($cart['supplier_id']) : null;

        // Get outstanding GRN items (received but not fully invoiced) for the selected supplier
        $outstanding_grn_items = [];
        if ($cart['supplier_id']) {
            $outstanding_grn_items = $this->getOutstandingGrnItems($cart['supplier_id']);
        }

        // Compute totals for display
        $grn_subtotal = 0;
        foreach ($cart['grn_items'] ?? [] as $item) {
            $item['line_total'] = $item['this_quantity_inv'] * $item['chg_price'];
            $grn_subtotal += $item['line_total'];
        }
        $gl_total = 0;
        foreach ($cart['gl_items'] ?? [] as $item) {
            $gl_total += $item['amount'];
        }
        $ov_amount = $grn_subtotal + $gl_total;

        return view('purchases.invoices.index', compact(
            'cart', 'suppliers', 'dimensions', 'supplier',
            'outstanding_grn_items', 'grn_subtotal', 'gl_total', 'ov_amount'
        ));
    }

    public function allocationIndex(Request $request): View|RedirectResponse
    {
        $supplier_id = $request->input('supplier_id', '');
        $from_date = $request->input('from_date', '');
        $to_date = $request->input('to_date', '');

        // Handle delete
        $delete_id = $request->input('delete_id', '');
        if ($delete_id) {
            $allocation = SupplierPaymentAllocation::with(['invoice'])->find($delete_id);
            if ($allocation) {
                $invoice = $allocation->invoice;
                if ($invoice) {
                    $invoice->decrement('alloc', $allocation->amount);
                }
                $allocation->delete();
                session()->flash('success', 'Allocation deleted successfully.');
            } else {
                session()->flash('error', 'Allocation not found.');
            }
            return redirect()->route('purchases.allocation.index', array_filter([
                'supplier_id' => $supplier_id,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ]));
        }

        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();

        $allocations = SupplierPaymentAllocation::with([
            'payment.supplier',
            'payment.bankAccount',
            'invoice.supplier',
        ])->orderBy('created_at', 'desc');

        if ($supplier_id) {
            $allocations->where(function ($q) use ($supplier_id) {
                $q->whereHas('payment', function ($q2) use ($supplier_id) {
                    $q2->where('supplier_id', $supplier_id);
                })->orWhereHas('invoice', function ($q2) use ($supplier_id) {
                    $q2->where('supplier_id', $supplier_id);
                });
            });
        }

        if ($from_date) {
            $allocations->whereDate('created_at', '>=', $from_date);
        }
        if ($to_date) {
            $allocations->whereDate('created_at', '<=', $to_date);
        }

        $allocations = $allocations->paginate(25)->withQueryString();

        return view('purchases.allocation.index', compact(
            'suppliers', 'supplier_id', 'from_date', 'to_date', 'allocations'
        ));
    }

    public function allocationView($id): View
    {
        $allocation = SupplierPaymentAllocation::with([
            'payment.supplier',
            'payment.bankAccount',
            'invoice.supplier',
            'invoice.items',
        ])->findOrFail($id);

        return view('purchases.allocation.view', compact('allocation'));
    }

    public function ordersInquiry(Request $request): View
    {
        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();
        $items = Item::where('is_active', true)->where('no_purchase', false)->orderBy('code')->get(['code', 'name']);

        $po_no = $request->input('po_no', '');
        $supplier_id = $request->input('supplier_id', '');
        $from_date = $request->input('from_date', '');
        $to_date = $request->input('to_date', '');
        $location = $request->input('location', '');
        $item_code = $request->input('item_code', '');
        $status = $request->input('status', '');
        $also_closed = $request->input('also_closed', '');

        $query = PurchaseOrder::with(['supplier'])
            ->orderBy('order_date', 'desc')
            ->orderBy('id', 'desc');

        if ($po_no !== '') {
            $query->where(function ($q) use ($po_no) {
                $q->where('order_number', 'LIKE', "%{$po_no}%")
                  ->orWhere('id', is_numeric($po_no) ? $po_no : 0);
            });
        } else {
            if ($from_date !== '') {
                $query->whereDate('order_date', '>=', $from_date);
            }
            if ($to_date !== '') {
                $query->whereDate('order_date', '<=', $to_date);
            }
            if ($location !== '') {
                $query->where('location', $location);
            }
            if ($item_code !== '') {
                $query->whereHas('items', function ($q) use ($item_code) {
                    $q->whereHas('item', function ($iq) use ($item_code) {
                        $iq->where('code', $item_code);
                    });
                });
            }
        }

        if ($supplier_id !== '') {
            $query->where('supplier_id', $supplier_id);
        }

        if ($also_closed !== '1') {
            $query->whereIn('status', ['pending', 'partial', 'received']);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(25)->withQueryString();

        return view('purchases.inquiries.orders', compact(
            'orders', 'suppliers', 'locations', 'items',
            'po_no', 'supplier_id', 'from_date', 'to_date',
            'location', 'item_code', 'status', 'also_closed'
        ));
    }

    public function supplierInquiry(Request $request): View
    {
        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();

        $supplier_id = $request->input('supplier_id', '');
        $filter_type = $request->input('filter_type', '');
        $from_date = $request->input('from_date', date('Y-m-d', strtotime('-30 days')));
        $to_date = $request->input('to_date', date('Y-m-d'));

        // Compute supplier aging summary
        $aging = null;
        $past1 = 30;
        $past2 = 60;

        if ($supplier_id) {
            $supplier = Supplier::find($supplier_id);
            $aging = $this->getSupplierAging($supplier_id, $to_date, $past1, $past2);
        }

        // Build combined transaction query
        $transactions = collect();

        if ($supplier_id) {
            // Get invoices and credit notes from supplier_invoices
            $inv_query = SupplierInvoice::with(['supplier'])
                ->where('supplier_id', $supplier_id)
                ->whereIn('status', ['approved', 'partial', 'paid']);

            if ($filter_type === 'invoice') {
                $inv_query->where('type', 'invoice');
            } elseif ($filter_type === 'credit_note') {
                $inv_query->where('type', 'credit_note');
            }

            if ($from_date && $filter_type !== '') {
                $inv_query->whereDate('invoice_date', '>=', $from_date);
            }
            if ($to_date && $filter_type !== '') {
                $inv_query->whereDate('invoice_date', '<=', $to_date);
            }

            $invoices = $inv_query->get()->map(function ($inv) {
                return [
                    'type' => $inv->type === 'credit_note' ? 'Credit Note' : 'Invoice',
                    'type_code' => $inv->type === 'credit_note' ? 'credit_note' : 'invoice',
                    'trans_no' => $inv->id,
                    'reference' => $inv->reference,
                    'supplier' => $inv->supplier->name ?? '',
                    'supp_reference' => $inv->supp_reference,
                    'date' => $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : '',
                    'due_date' => $inv->due_date ? $inv->due_date->format('Y-m-d') : '',
                    'currency' => $inv->currency,
                    'amount' => $inv->total_amount,
                    'balance' => $inv->outstanding_amount,
                    'alloc' => $inv->alloc,
                    'type_id' => $inv->type === 'credit_note' ? 2 : 1,
                ];
            });

            // Get payments from supplier_payments
            $pmt_query = SupplierPayment::with(['supplier', 'bankAccount'])
                ->where('supplier_id', $supplier_id)
                ->where('status', 'approved');

            if ($filter_type === 'payment') {
                // only payments
            } elseif ($filter_type !== '') {
                $pmt_query->whereRaw('1=0'); // no payments for other filters
            }

            if ($from_date && $filter_type !== '') {
                $pmt_query->whereDate('payment_date', '>=', $from_date);
            }
            if ($to_date && $filter_type !== '') {
                $pmt_query->whereDate('payment_date', '<=', $to_date);
            }

            $payments = $pmt_query->get()->map(function ($pmt) {
                return [
                    'type' => 'Payment',
                    'type_code' => 'payment',
                    'trans_no' => $pmt->id,
                    'reference' => $pmt->reference,
                    'supplier' => $pmt->supplier->name ?? '',
                    'supp_reference' => '',
                    'date' => $pmt->payment_date ? $pmt->payment_date->format('Y-m-d') : '',
                    'due_date' => '',
                    'currency' => $pmt->currency,
                    'amount' => -$pmt->amount,
                    'balance' => $pmt->unallocated_amount,
                    'alloc' => $pmt->amount - $pmt->unallocated_amount,
                    'type_id' => 3,
                ];
            });

            $transactions = $invoices->concat($payments);

            // Filter by overdue
            if ($filter_type === 'overdue') {
                $today = now()->toDateString();
                $transactions = $transactions->filter(function ($t) use ($today) {
                    return $t['due_date'] && $t['due_date'] < $today && abs($t['balance']) > 0;
                });
            }

            // Sort by date desc
            $transactions = $transactions->sortByDesc('date')->values();
        }

        // Paginate manually
        $page = $request->input('page', 1);
        $perPage = 25;
        $total = $transactions->count();
        $transactions_page = $transactions->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions_page, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('purchases.inquiries.transactions', compact(
            'suppliers', 'supplier_id', 'filter_type',
            'from_date', 'to_date', 'aging',
            'transactions', 'paginator'
        ));
    }

    public function allocationInquiry(Request $request): View
    {
        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();

        $supplier_id = $request->input('supplier_id', '');
        $filter_type = $request->input('filter_type', '');
        $from_date = $request->input('from_date', '');
        $to_date = $request->input('to_date', '');
        $show_settled = $request->input('show_settled', '');

        $transactions = collect();

        if ($supplier_id) {
            // Build invoice/credit note query
            $inv_query = SupplierInvoice::with(['supplier'])
                ->where('supplier_id', $supplier_id);

            // Filter by type
            if ($filter_type === '1' || $filter_type === '2') {
                $inv_query->where('type', 'invoice');
            } elseif ($filter_type === '3') {
                $inv_query->whereRaw('1=0'); // no invoices for payment filter
            } elseif ($filter_type === '4' || $filter_type === '5') {
                $inv_query->where('type', 'credit_note');
            }

            // Filter by status
            if ($show_settled) {
                $inv_query->whereIn('status', ['approved', 'partial', 'paid']);
            } else {
                $inv_query->whereIn('status', ['approved', 'partial']);
            }

            if ($from_date) {
                $inv_query->whereDate('invoice_date', '>=', $from_date);
            }
            if ($to_date) {
                $inv_query->whereDate('invoice_date', '<=', $to_date);
            }

            $today = now()->toDateString();

            $invoices = $inv_query->get()->map(function ($inv) use ($today, $filter_type) {
                $is_overdue = $inv->due_date && $inv->due_date->format('Y-m-d') < $today && $inv->outstanding_amount > 0;

                // Post-filter for overdue types
                if (($filter_type === '2' || $filter_type === '5') && !$is_overdue) {
                    return null;
                }

                $is_cn = $inv->type === 'credit_note';
                return [
                    'type' => $is_cn ? 'Credit Note' : 'Invoice',
                    'type_code' => $is_cn ? 'credit_note' : 'invoice',
                    'type_id' => $is_cn ? 2 : 1,
                    'trans_no' => $inv->id,
                    'reference' => $inv->reference,
                    'date' => $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : '',
                    'due_date' => $inv->due_date ? $inv->due_date->format('Y-m-d') : '',
                    'debit' => $is_cn ? 0 : $inv->total_amount,
                    'credit' => $is_cn ? $inv->total_amount : 0,
                    'amount' => $inv->total_amount,
                    'alloc' => $inv->alloc,
                    'balance' => $inv->outstanding_amount,
                    'is_overdue' => $is_overdue,
                    'supplier_id' => $inv->supplier_id,
                ];
            })->filter()->values();

            // Build payments query
            $pmt_query = SupplierPayment::with(['supplier'])
                ->where('supplier_id', $supplier_id)
                ->where('status', 'approved');

            if ($filter_type === '3') {
                // only payments, no additional filter needed
            } elseif ($filter_type !== '') {
                $pmt_query->whereRaw('1=0'); // only show payments when type is '3' or 'all'
            }

            if ($from_date) {
                $pmt_query->whereDate('payment_date', '>=', $from_date);
            }
            if ($to_date) {
                $pmt_query->whereDate('payment_date', '<=', $to_date);
            }

            $payments = $pmt_query->get()->map(function ($pmt) {
                $unallocated = $pmt->unallocated_amount;
                return [
                    'type' => 'Payment',
                    'type_code' => 'payment',
                    'type_id' => 3,
                    'trans_no' => $pmt->id,
                    'reference' => $pmt->reference,
                    'date' => $pmt->payment_date ? $pmt->payment_date->format('Y-m-d') : '',
                    'due_date' => '',
                    'debit' => 0,
                    'credit' => $pmt->amount,
                    'amount' => -$pmt->amount,
                    'alloc' => $pmt->amount - $unallocated,
                    'balance' => $unallocated,
                    'is_overdue' => false,
                    'supplier_id' => $pmt->supplier_id,
                ];
            });

            $transactions = $invoices->concat($payments);

            // Sort by date desc
            $transactions = $transactions->sortByDesc('date')->values();
        }

        // Paginate manually
        $page = $request->input('page', 1);
        $perPage = 25;
        $total = $transactions->count();
        $transactions_page = $transactions->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions_page, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('purchases.inquiries.allocation', compact(
            'suppliers', 'supplier_id', 'filter_type',
            'from_date', 'to_date', 'show_settled',
            'transactions', 'paginator'
        ));
    }

    private function getSupplierAging($supplier_id, $to_date, $past1, $past2)
    {
        $supplier = Supplier::find($supplier_id);
        if (!$supplier) return null;

        $to = $to_date ?: date('Y-m-d');

        // Get all invoices and credit notes
        $inv_trans = SupplierInvoice::where('supplier_id', $supplier_id)
            ->whereIn('status', ['approved', 'partial', 'paid'])
            ->whereDate('invoice_date', '<=', $to)
            ->get();

        $balance = 0;
        $due = 0;
        $overdue1 = 0;
        $overdue2 = 0;

        foreach ($inv_trans as $inv) {
            $amt = $inv->total_amount;
            $alloc = $inv->alloc;
            $outstanding = $amt - $alloc;

            if ($outstanding <= 0) continue;

            $balance += $outstanding;

            $due_date = $inv->due_date ? $inv->due_date->format('Y-m-d') : $inv->invoice_date->format('Y-m-d');
            $days_overdue = (strtotime($to) - strtotime($due_date)) / 86400;

            if ($days_overdue > 0) {
                $due += $outstanding;
            }
            if ($days_overdue > $past1) {
                $overdue1 += $outstanding;
            }
            if ($days_overdue > $past2) {
                $overdue2 += $outstanding;
            }
        }

        $current = $balance - $due;
        $due_30 = $due - $overdue1;
        $due_60 = $overdue1 - $overdue2;

        // Get payment terms
        $terms = '';
        if ($supplier->payment_terms) {
            $pt = PaymentTerm::where('terms_indicator', $supplier->payment_terms)->first();
            if ($pt) $terms = $pt->terms;
        }

        return (object)[
            'curr_code' => $supplier->curr_code ?? 'USD',
            'terms' => $terms,
            'current' => max(0, $current),
            'due_30' => max(0, $due_30),
            'due_60' => max(0, $due_60),
            'overdue2' => max(0, $overdue2),
            'balance' => $balance,
        ];
    }

    public function supplierCreditNote(Request $request): View|RedirectResponse
    {
        $cart = session('supp_cn_cart', $this->initSuppCnCart());

        if ($request->isMethod('POST')) {
            // Handle supplier change
            if ($request->has('supplier_id') && $request->supplier_id != ($cart['supplier_id'] ?? '')) {
                $cart['supplier_id'] = $request->supplier_id;
                $cart['grn_items'] = [];
                $cart['gl_items'] = [];
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $cart['curr_code'] = $supplier->curr_code ?? 'USD';
                    $cart['tax_included'] = $supplier->tax_included ?? false;
                    if ($supplier->payment_terms) {
                        $terms = PaymentTerm::where('terms_indicator', $supplier->payment_terms)->first();
                        if ($terms) {
                            $days = $terms->days_before_due ?? 0;
                            $cart['due_date'] = date('Y-m-d', strtotime('+' . $days . ' days'));
                        }
                    }
                }
                $cart['reference'] = $cart['tran_date'] = date('Y-m-d');
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            $cart['tran_date'] = $request->tran_date ?? date('Y-m-d');
            $cart['due_date'] = $request->due_date ?? $cart['due_date'] ?? date('Y-m-d');
            $cart['reference'] = $request->reference ?? '';
            $cart['supp_reference'] = $request->supp_reference ?? '';
            $cart['comments'] = $request->comments ?? '';
            $cart['dimension_id'] = $request->dimension_id ?? 0;
            $cart['dimension2_id'] = $request->dimension2_id ?? 0;

            // Handle Add GRN item
            $grn_id = $this->findSubmit('grn_item_id', $request);
            if ($grn_id !== null && $grn_id >= 0) {
                $po_item = PurchaseOrderItem::with(['item', 'purchaseOrder.supplier'])
                    ->find($grn_id);
                if ($po_item) {
                    $outstanding = $po_item->outstanding_credit_qty;
                    $default_qty = max(0, $outstanding);
                    $cart['grn_items'][$grn_id] = [
                        'po_detail_item_id' => $po_item->id,
                        'stock_id' => $po_item->item->code ?? '',
                        'item_description' => $po_item->description ?? '',
                        'qty_recd' => $po_item->received_quantity,
                        'prev_quantity_credited' => $po_item->credited_quantity,
                        'this_quantity_cn' => $default_qty,
                        'unit_price' => $po_item->unit_price,
                        'chg_price' => $po_item->unit_price,
                        'order_price' => $po_item->unit_price,
                        'unit' => $po_item->item->unit_of_measure ?? 'each',
                        'line_total' => $default_qty * $po_item->unit_price,
                    ];
                }
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            // Handle AddAll (add all creditable items)
            if ($request->has('AddAll')) {
                $supplier_id = $cart['supplier_id'];
                $creditable_items = $this->getCreditableGrnItems($supplier_id);
                foreach ($creditable_items as $po_item) {
                    $id = $po_item->id;
                    $outstanding = $po_item->outstanding_credit_qty;
                    $cart['grn_items'][$id] = [
                        'po_detail_item_id' => $po_item->id,
                        'stock_id' => $po_item->item->code ?? '',
                        'item_description' => $po_item->description ?? '',
                        'qty_recd' => $po_item->received_quantity,
                        'prev_quantity_credited' => $po_item->credited_quantity,
                        'this_quantity_cn' => $outstanding,
                        'unit_price' => $po_item->unit_price,
                        'chg_price' => $po_item->unit_price,
                        'order_price' => $po_item->unit_price,
                        'unit' => $po_item->item->unit_of_measure ?? 'each',
                        'line_total' => $outstanding * $po_item->unit_price,
                    ];
                }
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            // Handle Delete GRN item from cart
            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id >= 0 && isset($cart['grn_items'][$delete_id])) {
                unset($cart['grn_items'][$delete_id]);
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            // Handle Add GL Code
            if ($request->has('AddGLCode')) {
                $gl_code = $request->gl_code ?? '';
                $amount = (float)($request->gl_amount ?? 0);
                $memo = $request->gl_memo ?? '';
                $dim_id = $request->gl_dimension_id ?? 0;
                $dim2_id = $request->gl_dimension2_id ?? 0;
                if ($gl_code && $amount > 0) {
                    $cart['gl_items'][] = [
                        'gl_code' => $gl_code,
                        'gl_act_name' => $gl_code,
                        'amount' => $amount,
                        'memo' => $memo,
                        'dimension_id' => $dim_id,
                        'dimension2_id' => $dim2_id,
                    ];
                }
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            // Handle Delete GL item
            $delete_gl_id = $this->findSubmit('Delete2', $request);
            if ($delete_gl_id !== null && $delete_gl_id >= 0 && isset($cart['gl_items'][$delete_gl_id])) {
                array_splice($cart['gl_items'], $delete_gl_id, 1);
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            // Handle update (refresh) - update qty and price for each GRN item
            if ($request->has('update')) {
                if (!empty($cart['grn_items'])) {
                    foreach ($cart['grn_items'] as $id => $grn_item) {
                        $qty_key = 'this_quantity_cn' . $id;
                        $price_key = 'ChgPrice' . $id;
                        if ($request->has($qty_key)) {
                            $cart['grn_items'][$id]['this_quantity_cn'] = max(0, (float)$request->$qty_key);
                        }
                        if ($request->has($price_key)) {
                            $cart['grn_items'][$id]['chg_price'] = max(0, (float)$request->$price_key);
                        }
                        $cart['grn_items'][$id]['line_total'] =
                            $cart['grn_items'][$id]['this_quantity_cn'] * $cart['grn_items'][$id]['chg_price'];
                    }
                }
                session(['supp_cn_cart' => $cart]);
                return redirect()->route('purchases.credit-notes.index');
            }

            // Handle Process Credit Note
            if ($request->has('ProcessCreditNote')) {
                $validation_errors = [];

                if (!$cart['supplier_id']) {
                    $validation_errors[] = 'There is no supplier selected.';
                }
                if (empty($cart['grn_items']) && empty($cart['gl_items'])) {
                    $validation_errors[] = 'The credit note cannot be processed because there are no items or values.';
                }
                if (!$cart['tran_date']) {
                    $validation_errors[] = 'The credit note date is in an incorrect format.';
                }
                if (!$cart['due_date']) {
                    $validation_errors[] = 'The due date is in an incorrect format.';
                }
                if (empty(trim($cart['supp_reference'] ?? ''))) {
                    $validation_errors[] = 'You must enter a supplier\'s credit note reference.';
                }
                if (!empty($cart['supp_reference']) && $cart['supplier_id']) {
                    $existing = SupplierInvoice::where('supplier_id', $cart['supplier_id'])
                        ->where('supp_reference', $cart['supp_reference'])
                        ->where('type', 'credit_note')
                        ->whereIn('status', ['approved', 'partial', 'paid', 'draft'])
                        ->count();
                    if ($existing > 0) {
                        $validation_errors[] = 'This credit note number has already been entered. It cannot be entered again.';
                    }
                }

                if (!empty($validation_errors)) {
                    foreach ($validation_errors as $err) {
                        session()->flash('error', $err);
                    }
                    session(['supp_cn_cart' => $cart]);
                    return redirect()->route('purchases.credit-notes.index');
                }

                $subtotal = 0;
                foreach ($cart['grn_items'] as $item) {
                    $subtotal += $item['this_quantity_cn'] * $item['chg_price'];
                }
                foreach ($cart['gl_items'] as $item) {
                    $subtotal += $item['amount'];
                }
                $total = $subtotal;

                $cn_prefix = 'SC-' . date('Ymd') . '-';
                $last_cn = SupplierInvoice::where('type', 'credit_note')
                    ->where('invoice_number', 'like', $cn_prefix . '%')
                    ->orderBy('invoice_number', 'desc')->first();
                $seq = $last_cn ? (intval(substr($last_cn->invoice_number, -4)) + 1) : 1;
                $cn_number = $cn_prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

                $supplier = Supplier::find($cart['supplier_id']);

                $creditNote = SupplierInvoice::create([
                    'type' => 'credit_note',
                    'company_id' => 1,
                    'supplier_id' => $cart['supplier_id'],
                    'invoice_number' => $cn_number,
                    'reference' => $cart['reference'] ?? '',
                    'supp_reference' => $cart['supp_reference'] ?? '',
                    'invoice_date' => $cart['tran_date'],
                    'due_date' => $cart['due_date'],
                    'location' => '',
                    'delivery_address' => '',
                    'currency' => $supplier->curr_code ?? 'USD',
                    'exchange_rate' => 1,
                    'subtotal' => $subtotal,
                    'tax_total' => 0,
                    'total_amount' => $total,
                    'alloc' => 0,
                    'dimension_id' => $cart['dimension_id'] ?? 0,
                    'dimension2_id' => $cart['dimension2_id'] ?? 0,
                    'comments' => $cart['comments'] ?? '',
                    'status' => 'approved',
                    'created_by' => auth()->id() ?? 1,
                ]);

                foreach ($cart['grn_items'] as $id => $item) {
                    $line_total = $item['this_quantity_cn'] * $item['chg_price'];
                    $item_model = Item::where('code', $item['stock_id'])->first();
                    SupplierInvoiceItem::create([
                        'supplier_invoice_id' => $creditNote->id,
                        'item_id' => $item_model->id ?? null,
                        'stock_id' => $item['stock_id'],
                        'description' => $item['item_description'],
                        'quantity' => $item['this_quantity_cn'],
                        'unit_price' => $item['chg_price'],
                        'unit' => $item['unit'] ?? 'each',
                        'line_total' => $line_total,
                    ]);

                    $po_item = PurchaseOrderItem::find($item['po_detail_item_id']);
                    if ($po_item) {
                        $po_item->increment('credited_quantity', $item['this_quantity_cn']);
                    }
                }

                foreach ($cart['gl_items'] as $item) {
                    SupplierInvoiceItem::create([
                        'supplier_invoice_id' => $creditNote->id,
                        'item_id' => null,
                        'stock_id' => '',
                        'description' => $item['memo'] ?: ('GL: ' . $item['gl_code']),
                        'quantity' => 1,
                        'unit_price' => $item['amount'],
                        'unit' => '',
                        'line_total' => $item['amount'],
                        'gl_code' => $item['gl_code'],
                        'dimension_id' => $item['dimension_id'] ?? 0,
                        'dimension2_id' => $item['dimension2_id'] ?? 0,
                    ]);
                }

                session()->forget('supp_cn_cart');
                session()->flash('success', 'Supplier Credit Note has been processed. #' . $cn_number);
                return redirect()->route('purchases.credit-notes.index');
            }

            session(['supp_cn_cart' => $cart]);
            return redirect()->route('purchases.credit-notes.index');
        }

        // GET - generate reference
        if (!$cart['reference']) {
            $date_part = date('Ymd');
            $cart['reference'] = 'SC-' . $date_part . '-' . strtoupper(substr(uniqid(), -4));
            session(['supp_cn_cart' => $cart]);
        }

        $suppliers = Supplier::where('inactive', false)->orderBy('name')->get();
        $dimensions = Dimension::orderBy('id')->get();
        $supplier = $cart['supplier_id'] ? Supplier::find($cart['supplier_id']) : null;

        $creditable_grn_items = [];
        if ($cart['supplier_id']) {
            $creditable_grn_items = $this->getCreditableGrnItems($cart['supplier_id']);
        }

        $grn_subtotal = 0;
        foreach ($cart['grn_items'] ?? [] as $item) {
            $item['line_total'] = $item['this_quantity_cn'] * $item['chg_price'];
            $grn_subtotal += $item['line_total'];
        }
        $gl_total = 0;
        foreach ($cart['gl_items'] ?? [] as $item) {
            $gl_total += $item['amount'];
        }
        $ov_amount = $grn_subtotal + $gl_total;

        return view('purchases.credit-notes.index', compact(
            'cart', 'suppliers', 'dimensions', 'supplier',
            'creditable_grn_items', 'grn_subtotal', 'gl_total', 'ov_amount'
        ));
    }

    /**
     * Get outstanding GRN items that can be credited for a supplier.
     */
    private function getCreditableGrnItems($supplier_id)
    {
        return PurchaseOrderItem::with(['item', 'purchaseOrder'])
            ->whereHas('purchaseOrder', function ($q) use ($supplier_id) {
                $q->where('supplier_id', $supplier_id)
                  ->whereIn('status', ['received', 'partial']);
            })
            ->whereRaw('received_quantity > credited_quantity')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get outstanding GRN items (received but not fully invoiced) for a supplier.
     */
    private function getOutstandingGrnItems($supplier_id)
    {
        return PurchaseOrderItem::with(['item', 'purchaseOrder'])
            ->whereHas('purchaseOrder', function ($q) use ($supplier_id) {
                $q->where('supplier_id', $supplier_id)
                  ->whereIn('status', ['received', 'partial']);
            })
            ->whereRaw('received_quantity > invoiced_quantity')
            ->orderBy('id')
            ->get();
    }

    /**
     * Initialize a new empty supplier invoice cart.
     */
    private function initSuppInvCart(): array
    {
        return [
            'supplier_id' => '',
            'tran_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'reference' => '',
            'supp_reference' => '',
            'comments' => '',
            'dimension_id' => 0,
            'dimension2_id' => 0,
            'curr_code' => 'USD',
            'tax_included' => false,
            'grn_items' => [],
            'gl_items' => [],
        ];
    }

    /**
     * Initialize a new empty supplier credit note cart.
     */
    private function initSuppCnCart(): array
    {
        return [
            'supplier_id' => '',
            'tran_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'reference' => '',
            'supp_reference' => '',
            'comments' => '',
            'dimension_id' => 0,
            'dimension2_id' => 0,
            'curr_code' => 'USD',
            'tax_included' => false,
            'grn_items' => [],
            'gl_items' => [],
        ];
    }

    /**
     * Initialize a new empty invoice cart.
     */
    private function initInvoiceCart(): array
    {
        return [
            'supplier_id' => '',
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'reference' => '',
            'supp_reference' => '',
            'delivery_address' => '',
            'location' => '',
            'comments' => '',
            'dimension_id' => 0,
            'dimension2_id' => 0,
            'cash_account_id' => '',
            'curr_code' => 'USD',
            'tax_included' => false,
            'items' => [],
            'edit_line' => -1,
        ];
    }

    /**
     * Get purchase price for a supplier/item combination.
     */
    private function getPurchasePrice($supplier_id, $stock_id): float
    {
        // Try supplier-specific pricing from purch_data
        $result = \DB::table('purch_data')
            ->where('supplier_id', $supplier_id)
            ->where('stock_id', $stock_id)
            ->first();
        if ($result) {
            return $result->price / max($result->conversion_factor, 1);
        }
        return 0;
    }

    /**
     * Find a submit button named "Edit{id}" or "Delete{id}" in the request.
     */
    private function findSubmit(string $prefix, Request $request): ?int
    {
        foreach ($request->all() as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $id = substr($key, strlen($prefix));
                if (is_numeric($id)) {
                    return (int)$id;
                }
            }
        }
        return null;
    }
}
