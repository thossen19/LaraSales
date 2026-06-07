<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Dimension;
use App\Models\Item;
use App\Models\Location;
use App\Models\PaymentTerm;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\TaxType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class FixedAssetsController extends Controller
{
    private function findSubmit(string $prefix, Request $request): ?string
    {
        foreach ($request->all() as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $id = substr($key, strlen($prefix));
                if ($id !== '') {
                    return $id;
                }
            }
        }
        return null;
    }

    public function index(Request $request): View
    {
        $msg = '';
        $error = '';
        $show_inactive = $request->has('show_inactive');
        $stock_id = $request->input('stock_id', '');
        $new_item = $stock_id === '';

        if ($request->isMethod('POST')) {
            $NewStockID = $request->input('NewStockID', '');
            $description = trim($request->input('description', ''));
            $long_description = trim($request->input('long_description', ''));
            $category_id = $request->input('category_id', '');
            $tax_type_id = $request->input('tax_type_id', '');
            $units = $request->input('units', 'each');
            $sales_account = $request->input('sales_account', '');
            $inventory_account = $request->input('inventory_account', '');
            $cogs_account = $request->input('cogs_account', '');
            $adjustment_account = $request->input('adjustment_account', '');
            $wip_account = $request->input('wip_account', '');
            $fa_class_id = (int)$request->input('fa_class_id', 0);
            $depreciation_method = $request->input('depreciation_method', 'S');
            $depreciation_rate = (float)$request->input('depreciation_rate', 0);
            $depreciation_factor = (float)$request->input('depreciation_factor', 1);
            $depreciation_start = $request->input('depreciation_start', null);

            if ($request->has('addupdate')) {
                if (strlen($description) == 0) {
                    $error = 'The item name must be entered.';
                } elseif (strlen($NewStockID) == 0) {
                    $error = 'The item code cannot be empty';
                } elseif (preg_match('/[\s\'+\"&]/', $NewStockID)) {
                    $error = 'The item code cannot contain any of the following characters - & + OR a space OR quotes';
                } else {
                    $exists = Item::where('code', $NewStockID)->where('mb_flag', 'F')->exists();
                    $is_new = $request->has('_new_item') || !$exists;
                    if (!$is_new && $stock_id !== $NewStockID) {
                        $error = 'Item code cannot be changed.';
                    } elseif ($is_new && $exists) {
                        $error = 'This item code is already assigned to a fixed asset.';
                    } else {
                        $data = [
                            'name' => $description,
                            'description' => $description,
                            'long_description' => $long_description,
                            'category' => $category_id,
                            'tax_type_id' => $tax_type_id ?: 0,
                            'unit_of_measure' => $units,
                            'mb_flag' => 'F',
                            'is_stock_item' => true,
                            'is_service' => false,
                            'is_active' => $request->has('is_active'),
                            'sales_account' => $sales_account,
                            'inventory_account' => $inventory_account,
                            'cogs_account' => $cogs_account,
                            'adjustment_account' => $adjustment_account,
                            'wip_account' => $wip_account ?: $inventory_account,
                            'fa_class_id' => $fa_class_id,
                            'depreciation_method' => $depreciation_method,
                            'depreciation_rate' => $depreciation_rate,
                            'depreciation_factor' => $depreciation_factor,
                            'depreciation_start' => $depreciation_start,
                        ];

                        if ($is_new) {
                            $data['code'] = $NewStockID;
                            $data['company_id'] = 1;
                            $data['purchase_cost'] = 0;
                            $data['material_cost'] = 0;
                            $data['cost_price'] = 0;
                            $data['purchase_price'] = 0;
                            $data['sale_price'] = 0;
                            Item::create($data);
                            $msg = 'A new fixed asset has been added.';
                            $stock_id = '';
                            $new_item = true;
                        } else {
                            Item::where('code', $stock_id)->update($data);
                            $msg = 'Fixed asset has been updated.';
                            $stock_id = $NewStockID;
                            $new_item = false;
                        }
                    }
                }
            }

            if ($request->has('delete')) {
                $used = DB::table('stock_moves')->where('stock_id', $stock_id)->exists();
                if ($used) {
                    $error = 'Cannot delete this fixed asset because transactions exist.';
                } else {
                    Item::where('code', $stock_id)->delete();
                    $msg = 'Selected fixed asset has been deleted.';
                    $stock_id = '';
                    $new_item = true;
                }
            }

            if ($request->has('cancel')) {
                $stock_id = '';
                $new_item = true;
            }
        }

        $fixed_assets = Item::where('mb_flag', 'F')
            ->when(!$show_inactive, fn($q) => $q->where('is_active', true))
            ->orderBy('code')
            ->get();

        $selected_asset = null;
        if ($stock_id) {
            $selected_asset = Item::where('code', $stock_id)->first();
            if ($selected_asset) {
                $new_item = false;
            }
        }

        $fa_classes = DB::table('stock_fa_class')->where('inactive', false)->orderBy('description')->get();
        $stock_categories = DB::table('stock_category')->where('dflt_mb_flag', 'F')->where('inactive', false)->orderBy('description')->get(['id', 'description']);
        $tax_types = TaxType::where('inactive', false)->orderBy('name')->get(['id', 'name']);
        $units = DB::table('item_units')->where('inactive', false)->orderBy('name')->get(['name']);
        $gl_accounts = Account::where('is_active', true)->orderBy('code')->get(['code', 'name']);
        $dimensions = Dimension::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $depreciation_methods = ['S' => 'Straight Line', 'D' => 'Diminishing Balance', 'O' => 'One Time', 'N' => 'No Depreciation'];

        return view('fixed-assets.index', compact(
            'msg', 'error', 'fixed_assets', 'show_inactive',
            'stock_id', 'selected_asset', 'new_item',
            'fa_classes', 'stock_categories', 'tax_types', 'units', 'gl_accounts', 'dimensions',
            'depreciation_methods'
        ));
    }

    public function locations(Request $request): View
    {
        $msg = '';
        $error = '';
        $selected_id = $request->input('selected_id', '');
        if ($selected_id === '') $selected_id = -1;
        $show_inactive = $request->has('show_inactive');
        $fixed_asset = true;

        if ($request->isMethod('POST')) {
            $edit_code = $this->findSubmit('Edit', $request);
            if ($edit_code !== null && $edit_code !== '') {
                $selected_id = $edit_code;
            }

            $delete_code = $this->findSubmit('Delete', $request);
            if ($delete_code !== null && $delete_code !== '') {
                $selected_id = $delete_code;
                if ($this->canDeleteLocation($selected_id)) {
                    Location::destroy($selected_id);
                    $msg = 'Selected location has been deleted';
                    $selected_id = -1;
                } else {
                    $error = 'Cannot delete this location because it is used by related records.';
                }
            }

            if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
                $loc_code = strtoupper(trim($request->input('loc_code', '')));
                $location_name = trim($request->input('location_name', ''));
                $contact = trim($request->input('contact', ''));
                $delivery_address = trim($request->input('delivery_address', ''));
                $phone = trim($request->input('phone', ''));
                $phone2 = trim($request->input('phone2', ''));
                $fax = trim($request->input('fax', ''));
                $email = trim($request->input('email', ''));

                if (strlen($loc_code) > 7 || empty($loc_code)) {
                    $error = 'The location code must be five characters or less long.';
                } elseif (strlen($location_name) == 0) {
                    $error = 'The location name must be entered.';
                } else {
                    if ($selected_id != -1) {
                        Location::where('loc_code', $selected_id)->update([
                            'location_name' => $location_name,
                            'delivery_address' => $delivery_address,
                            'contact' => $contact,
                            'phone' => $phone,
                            'phone2' => $phone2,
                            'fax' => $fax,
                            'email' => $email,
                            'fixed_asset' => true,
                        ]);
                        $msg = 'Selected location has been updated';
                        $selected_id = -1;
                    } else {
                        $exists = Location::where('loc_code', $loc_code)->exists();
                        if ($exists) {
                            $error = 'This location code is already in use.';
                        } else {
                            Location::create([
                                'loc_code' => $loc_code,
                                'location_name' => $location_name,
                                'delivery_address' => $delivery_address,
                                'contact' => $contact,
                                'phone' => $phone,
                                'phone2' => $phone2,
                                'fax' => $fax,
                                'email' => $email,
                                'inactive' => false,
                                'fixed_asset' => true,
                            ]);
                            $msg = 'New location has been added';
                            $selected_id = -1;
                        }
                    }
                }
            }

            if ($request->has('cancel')) {
                $selected_id = -1;
            }
        }

        $locations = Location::where('fixed_asset', true)
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('loc_code')
            ->get();

        $selected_location = null;
        if ($selected_id != -1) {
            $selected_location = Location::find($selected_id);
        }

        return view('fixed-assets.locations', compact(
            'msg', 'error', 'locations', 'selected_id', 'selected_location', 'show_inactive', 'fixed_asset'
        ));
    }

    public function categories(Request $request): View
    {
        $msg = '';
        $error = '';
        $selected_id = $request->input('selected_id', '');
        if ($selected_id === '') $selected_id = -1;
        $show_inactive = $request->has('show_inactive');

        if ($request->isMethod('POST')) {
            $edit_id_input = $this->findSubmit('Edit', $request);
            if ($edit_id_input !== null && $edit_id_input !== '') {
                $selected_id = (int)$edit_id_input;
            }

            $delete_id_input = $this->findSubmit('Delete', $request);
            if ($delete_id_input !== null && $delete_id_input !== '') {
                $selected_id = (int)$delete_id_input;
                $used = DB::table('items')->where('category', $selected_id)->exists();
                if ($used) {
                    $error = 'Cannot delete this item category because items have been created using this item category.';
                } else {
                    DB::table('stock_category')->where('id', $selected_id)->delete();
                    $msg = 'Selected item category has been deleted';
                }
                $selected_id = -1;
            }

            if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
                $description = trim($request->input('description', ''));
                $tax_type_id = $request->input('tax_type_id', '');
                $units = $request->input('units', 'each');
                $sales_account = $request->input('sales_account', '');
                $inventory_account = $request->input('inventory_account', '');
                $cogs_account = $request->input('cogs_account', '');
                $adjustment_account = $request->input('adjustment_account', '');
                $wip_account = $request->input('wip_account', '');

                if (strlen($description) == 0) {
                    $error = 'The item category description cannot be empty.';
                } else {
                    $data = [
                        'description' => $description,
                        'dflt_tax_type' => $tax_type_id ?: null,
                        'dflt_units' => $units,
                        'dflt_mb_flag' => 'F',
                        'dflt_sales_act' => $sales_account,
                        'dflt_inventory_act' => $inventory_account,
                        'dflt_cogs_act' => $cogs_account,
                        'dflt_adjustment_act' => $adjustment_account,
                        'dflt_wip_act' => $wip_account ?: $inventory_account,
                        'dflt_dim1' => 0,
                        'dflt_dim2' => 0,
                        'dflt_no_sale' => false,
                        'dflt_no_purchase' => $request->has('no_purchase'),
                    ];

                    if ($selected_id != -1) {
                        DB::table('stock_category')->where('id', $selected_id)->update($data);
                        $msg = 'Selected item category has been updated';
                        $selected_id = -1;
                    } else {
                        $data['inactive'] = false;
                        DB::table('stock_category')->insert($data);
                        $msg = 'New item category has been added';
                        $selected_id = -1;
                    }
                }
            }

            if ($request->has('cancel')) {
                $selected_id = -1;
            }
        }

        $categories = DB::table('stock_category')
            ->leftJoin('tax_types', 'stock_category.dflt_tax_type', '=', 'tax_types.id')
            ->where('dflt_mb_flag', 'F')
            ->when(!$show_inactive, fn($q) => $q->where('stock_category.inactive', false))
            ->orderBy('stock_category.description')
            ->select('stock_category.*', 'tax_types.name as tax_name')
            ->get();

        $selected_category = null;
        if ($selected_id != -1) {
            $selected_category = DB::table('stock_category')->where('id', $selected_id)->first();
        }

        $tax_types = TaxType::where('inactive', false)->orderBy('name')->get(['id', 'name']);
        $units = DB::table('item_units')->where('inactive', false)->orderBy('name')->get(['name']);
        $gl_accounts = Account::where('is_active', true)->orderBy('code')->get(['code', 'name']);
        $dimensions = Dimension::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $stock_types = ['B' => 'Buy', 'M' => 'Manufacture', 'D' => 'Service', 'S' => 'Sales Kit', 'F' => 'Fixed Asset'];

        return view('fixed-assets.categories', compact(
            'msg', 'error', 'categories', 'selected_id', 'selected_category',
            'show_inactive', 'tax_types', 'units', 'gl_accounts', 'dimensions', 'stock_types'
        ));
    }

    public function classes(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = -1;
        $show_inactive = $request->boolean('show_inactive');

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $fa_class_id = $request->input('fa_class_id');
            $depreciation_rate = (float) $request->input('depreciation_rate', 0);

            if (!is_numeric($fa_class_id) || $fa_class_id != (int)$fa_class_id) {
                $error = "Fixed asset class must be a valid integer.";
                $input_error = 1;
            } elseif ($depreciation_rate > 100) {
                $error = "The depreciation rate can't be greater than 100%";
                $input_error = 1;
            }

            if (!$input_error) {
                $data = [
                    'fa_class_id' => (int) $request->input('fa_class_id'),
                    'parent_id' => $request->input('parent_id') ?: null,
                    'description' => $request->input('description', ''),
                    'long_description' => $request->input('long_description', ''),
                    'depreciation_rate' => $depreciation_rate,
                ];

                if ($request->has('UPDATE_ITEM')) {
                    $id = $request->input('selected_id');
                    DB::table('stock_fa_class')->where('fa_class_id', $id)->update($data);
                    $msg = 'Selected fixed asset class has been updated';
                } else {
                    DB::table('stock_fa_class')->insert($data);
                    $msg = 'New fixed asset class has been added';
                }
            }
        } elseif ($delete_id !== null) {
            if (DB::table('items')->where('fa_class_id', $delete_id)->exists()) {
                $error = "Cannot delete this class because it is used by some fixed asset items.";
            } else {
                DB::table('stock_fa_class')->where('fa_class_id', $delete_id)->delete();
                $msg = 'Selected fixed asset class has been deleted';
            }
        } elseif ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->input('toggle_inactive');
            $class = DB::table('stock_fa_class')->where('fa_class_id', $toggle_id)->first();
            if ($class) {
                DB::table('stock_fa_class')->where('fa_class_id', $toggle_id)->update([
                    'inactive' => !$class->inactive,
                ]);
                $msg = $class->inactive ? 'Fixed asset class activated' : 'Fixed asset class deactivated';
            }
        }

        $classes = DB::table('stock_fa_class')
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('fa_class_id')
            ->get();

        $selected_class = null;
        if ($selected_id != -1) {
            $selected_class = DB::table('stock_fa_class')->where('fa_class_id', $selected_id)->first();
        }

        $all_classes = DB::table('stock_fa_class')->where('inactive', false)->orderBy('description')->get(['fa_class_id', 'description']);

        return view('fixed-assets.classes', compact(
            'msg', 'error', 'classes', 'selected_id', 'selected_class',
            'show_inactive', 'all_classes'
        ));
    }

    private function canDeleteLocation(string $loc_code): bool
    {
        $tables = [
            ['table' => 'stock_moves', 'column' => 'loc_code'],
            ['table' => 'work_orders', 'column' => 'loc_code'],
            ['table' => 'bom', 'column' => 'loc_code'],
        ];
        foreach ($tables as $t) {
            if (DB::table($t['table'])->where($t['column'], $loc_code)->exists()) {
                return false;
            }
        }
        return true;
    }

    public function purchase(Request $request): View|RedirectResponse
    {
        $cart = session('fa_purchase_cart', [
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
        ]);

        if ($request->isMethod('POST')) {
            // Supplier change
            if ($request->has('supplier_id') && $request->supplier_id != ($cart['supplier_id'] ?? '')) {
                $cart['supplier_id'] = $request->supplier_id;
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
                    $loc = Location::where('inactive', false)->where('fixed_asset', true)->first()
                        ?? Location::where('inactive', false)->first();
                    if ($loc) {
                        $cart['location'] = $loc->loc_code;
                        $cart['delivery_address'] = $loc->delivery_address ?? '';
                    }
                }
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['fa_purchase_cart' => $cart]);
                return redirect()->route('fixed-assets.purchase');
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

            // Edit line
            $edit_id = $this->findSubmit('Edit', $request);
            if ($edit_id !== null && $edit_id >= 0) {
                $cart['edit_line'] = $edit_id;
                if (isset($cart['items'][$edit_id])) {
                    $item = $cart['items'][$edit_id];
                    $cart['stock_id'] = $item['stock_id'];
                    $cart['qty'] = 1;
                    $cart['price'] = $item['price'];
                    $cart['item_description'] = $item['item_description'];
                }
                session(['fa_purchase_cart' => $cart]);
                return redirect()->route('fixed-assets.purchase');
            }

            // Delete line
            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id >= 0) {
                if (isset($cart['items'][$delete_id])) {
                    unset($cart['items'][$delete_id]);
                    $cart['items'] = array_values($cart['items']);
                }
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['fa_purchase_cart' => $cart]);
                return redirect()->route('fixed-assets.purchase');
            }

            // Auto-populate on item selection
            $has_specific_action = $request->has('EnterLine') || $request->has('UpdateLine')
                || $request->has('CancelUpdate') || $request->has('CancelOrder') || $request->has('Commit')
                || $this->findSubmit('Edit', $request) !== null || $this->findSubmit('Delete', $request) !== null;
            if ($request->has('stock_id') && !$has_specific_action) {
                $cart['stock_id'] = $request->stock_id;
                $item_info = Item::where('code', $request->stock_id)->where('mb_flag', 'F')->first();
                if ($item_info) {
                    $price = 0;
                    $purchData = DB::table('purch_data')
                        ->where('supplier_id', $cart['supplier_id'])
                        ->where('stock_id', $request->stock_id)->first();
                    if ($purchData) {
                        $price = $purchData->price / max($purchData->conversion_factor, 1);
                    }
                    $cart['price'] = $price ?: ($item_info->purchase_cost ?? 0);
                    $cart['qty'] = 1;
                    $cart['item_description'] = $item_info->name ?? '';
                }
                session(['fa_purchase_cart' => $cart]);
                return redirect()->route('fixed-assets.purchase');
            }

            // EnterLine / UpdateLine
            if ($request->has('EnterLine') || $request->has('UpdateLine')) {
                $stock_id = $request->stock_id ?? '';
                $qty = 1;
                $price = (float)($request->price ?? 0);
                $item_description = $request->item_description ?? '';

                if (!$stock_id) {
                    session()->flash('error', 'Please select an item.');
                } else {
                    $item_info = Item::where('code', $stock_id)->where('mb_flag', 'F')->first();
                    if (!$item_description) {
                        $item_description = $item_info ? ($item_info->name ?? '') : '';
                    }
                    $line_data = [
                        'stock_id' => $stock_id,
                        'item_description' => $item_description,
                        'quantity' => 1,
                        'price' => max(0, $price),
                        'unit' => $item_info->unit_of_measure ?? 'each',
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
                session(['fa_purchase_cart' => $cart]);
                return redirect()->route('fixed-assets.purchase');
            }

            // CancelUpdate
            if ($request->has('CancelUpdate')) {
                $cart['edit_line'] = -1;
                unset($cart['stock_id'], $cart['qty'], $cart['price'], $cart['item_description']);
                session(['fa_purchase_cart' => $cart]);
                return redirect()->route('fixed-assets.purchase');
            }

            // CancelOrder
            if ($request->has('CancelOrder')) {
                session()->forget('fa_purchase_cart');
                session()->flash('success', 'Fixed asset purchase invoice entry has been cancelled.');
                return redirect()->route('fixed-assets.purchase');
            }

            // Commit (Process Invoice)
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
                    session(['fa_purchase_cart' => $cart]);
                    return redirect()->route('fixed-assets.purchase');
                }

                $subtotal = 0;
                foreach ($cart['items'] as $item) {
                    $subtotal += $item['quantity'] * $item['price'];
                }
                $total = $subtotal;
                $supplier = Supplier::find($cart['supplier_id']);
                if ($supplier && !$supplier->tax_included) {
                    // For simplicity, tax_total stays 0; FA calculates from tax group
                }

                $invoice = SupplierInvoice::create([
                    'type' => 'invoice',
                    'company_id' => 1,
                    'supplier_id' => $cart['supplier_id'],
                    'invoice_number' => $cart['reference'] ?: ('FA-PI-' . date('YmdHis')),
                    'reference' => $cart['reference'] ?: '',
                    'supp_reference' => $cart['supp_reference'],
                    'invoice_date' => $cart['invoice_date'],
                    'due_date' => $cart['due_date'],
                    'location' => $cart['location'],
                    'delivery_address' => $cart['delivery_address'],
                    'currency' => $cart['curr_code'],
                    'exchange_rate' => 1,
                    'subtotal' => $subtotal,
                    'tax_total' => 0,
                    'total_amount' => $total,
                    'alloc' => 0,
                    'cash_account_id' => $cart['cash_account_id'] ?: null,
                    'dimension_id' => $cart['dimension_id'],
                    'dimension2_id' => $cart['dimension2_id'],
                    'comments' => $cart['comments'],
                    'status' => 'approved',
                    'created_by' => 1,
                ]);

                foreach ($cart['items'] as $li) {
                    $line_total = $li['quantity'] * $li['price'];
                    SupplierInvoiceItem::create([
                        'supplier_invoice_id' => $invoice->id,
                        'stock_id' => $li['stock_id'],
                        'description' => $li['item_description'],
                        'quantity' => 1,
                        'unit_price' => $li['price'],
                        'unit' => $li['unit'] ?? 'each',
                        'line_total' => $line_total,
                        'gl_code' => '',
                    ]);

                    // Update the fixed asset item's purchase_cost
                    Item::where('code', $li['stock_id'])->update([
                        'purchase_cost' => $li['price'],
                    ]);
                }

                session()->forget('fa_purchase_cart');
                session()->flash('success', 'Fixed Asset Purchase Invoice #' . $invoice->id . ' has been entered.');
                return redirect()->route('fixed-assets.purchase');
            }
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $fixed_assets = Item::where('mb_flag', 'F')->where('is_active', true)->orderBy('code')->get();
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();
        $dimensions = Dimension::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $cash_accounts = BankAccount::where('inactive', false)->orderBy('bank_name')->get(['id', 'bank_name', 'bank_account_name', 'bank_curr_code']);

        return view('fixed-assets.purchase', compact(
            'cart', 'suppliers', 'fixed_assets', 'locations', 'dimensions', 'cash_accounts'
        ));
    }
}
