<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bom;
use App\Models\Item;
use App\Models\Location;
use App\Models\Refline;
use App\Models\WorkCentre;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends Controller
{
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

    public function workCentres(Request $request): View
    {
        $msg = '';
        $error = '';
        $selected_id = $request->input('selected_id', '');
        if ($selected_id === '') $selected_id = -1;

        if ($request->isMethod('POST')) {
            $edit_id = $this->findSubmit('Edit', $request);
            if ($edit_id !== null && $edit_id > 0) {
                $selected_id = $edit_id;
            }

            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id > 0) {
                $used_bom = \DB::table('bom')->where('workcentre_added', $delete_id)->exists();
                if ($used_bom) {
                    $error = 'Cannot delete this work centre because BOMs have been created referring to it.';
                } else {
                    WorkCentre::destroy($delete_id);
                    $msg = 'Selected work centre has been deleted.';
                    $selected_id = -1;
                }
            }

            if ($request->has('ADD_ITEM')) {
                $name = trim($request->input('name', ''));
                $description = trim($request->input('description', ''));
                if ($name === '') {
                    $error = 'The work centre name cannot be empty.';
                } else {
                    $exists = WorkCentre::where('name', $name)->exists();
                    if ($exists) {
                        $error = 'This work centre name is already in use.';
                    } else {
                        WorkCentre::create(['name' => $name, 'description' => $description]);
                        $msg = 'New work centre has been added.';
                        $selected_id = -1;
                    }
                }
            }

            if ($request->has('UPDATE_ITEM')) {
                $id = (int)$request->input('selected_id');
                $name = trim($request->input('name', ''));
                $description = trim($request->input('description', ''));
                if ($name === '') {
                    $error = 'The work centre name cannot be empty.';
                } else {
                    $dup = WorkCentre::where('name', $name)->where('id', '!=', $id)->exists();
                    if ($dup) {
                        $error = 'This work centre name is already in use.';
                    } else {
                        WorkCentre::find($id)?->update(['name' => $name, 'description' => $description]);
                        $msg = 'Selected work centre has been updated.';
                        $selected_id = -1;
                    }
                }
            }

            if ($request->has('delete_work_centre')) {
                $id = (int)$request->input('selected_id');
                $used_bom = \DB::table('bom')->where('workcentre_added', $id)->exists();
                if ($used_bom) {
                    $error = 'Cannot delete this work centre because BOMs have been created referring to it.';
                } else {
                    WorkCentre::destroy($id);
                    $msg = 'Selected work centre has been deleted.';
                    $selected_id = -1;
                }
            }

            if ($request->has('cancel')) {
                $selected_id = -1;
            }
        }

        $work_centres = WorkCentre::orderBy('name')->get();
        $selected_centre = $selected_id > 0 ? WorkCentre::find($selected_id) : null;

        return view('manufacturing.work-centers', compact(
            'msg', 'error', 'work_centres', 'selected_id', 'selected_centre'
        ));
    }

    public function bomEdit(Request $request): View
    {
        $msg = '';
        $error = '';
        $selected_id = $request->input('selected_id', '');
        if ($selected_id === '') $selected_id = -1;

        $stock_id = $request->input('stock_id', session('bom_stock_id', ''));

        if ($request->isMethod('POST')) {
            // Handle parent item selection change
            if ($request->has('stock_id') && $request->input('stock_id') !== $stock_id) {
                $stock_id = $request->input('stock_id');
                session(['bom_stock_id' => $stock_id]);
                $selected_id = -1;
            }

            // Handle new_stock_id (copy BOM) - must be checked before stock_id update
            $new_stock_id = $request->input('new_stock_id', '');
            if ($new_stock_id && $stock_id) {
                $bom_items = Bom::where('parent', $stock_id)->get();
                foreach ($bom_items as $bom) {
                    $exists = Bom::where('parent', $new_stock_id)
                        ->where('component', $bom->component)
                        ->where('workcentre_added', $bom->workcentre_added)
                        ->where('loc_code', $bom->loc_code)
                        ->exists();
                    if (!$exists) {
                        Bom::create([
                            'parent' => $new_stock_id,
                            'component' => $bom->component,
                            'workcentre_added' => $bom->workcentre_added,
                            'loc_code' => $bom->loc_code,
                            'quantity' => $bom->quantity,
                        ]);
                    }
                }
                $stock_id = $new_stock_id;
                session(['bom_stock_id' => $stock_id]);
                $selected_id = -1;
                $item = Item::where('code', $stock_id)->first();
                $msg = 'BOM copied to ' . ($item->name ?? $stock_id);
            }

            // Handle Edit button from table row
            $edit_id = $this->findSubmit('Edit', $request);
            if ($edit_id !== null && $edit_id > 0) {
                $selected_id = $edit_id;
            }

            // Handle Delete button from table row
            $delete_id = $this->findSubmit('Delete', $request);
            if ($delete_id !== null && $delete_id > 0) {
                Bom::destroy($delete_id);
                $msg = 'The component item has been deleted from this bom.';
                $selected_id = -1;
            }

            // Handle ADD_ITEM / UPDATE_ITEM (on_submit logic)
            if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
                $component = $request->input('component', '');
                $loc_code = $request->input('loc_code', '');
                $workcentre_added = (int)$request->input('workcentre_added', 0);
                $quantity = (float)$request->input('quantity', 0);

                if (!$component) {
                    $error = 'You must select a component.';
                } elseif ($quantity <= 0) {
                    $error = 'The quantity entered must be numeric and greater than zero.';
                } elseif ($stock_id === $component) {
                    $error = 'The selected component is a parent of the current item. Recursive BOMs are not allowed.';
                } else {
                    // Check for recursive BOM
                    if ($this->checkRecursiveBom($stock_id, $component)) {
                        $error = 'The selected component is a parent of the current item. Recursive BOMs are not allowed.';
                    } elseif ($request->has('UPDATE_ITEM')) {
                        $id = (int)$request->input('selected_id');
                        Bom::where('id', $id)->update([
                            'workcentre_added' => $workcentre_added,
                            'loc_code' => $loc_code,
                            'quantity' => $quantity,
                        ]);
                        $msg = 'Selected component has been updated.';
                        $selected_id = -1;
                    } else {
                        // Check if component already on BOM
                        $already = Bom::where('parent', $stock_id)
                            ->where('component', $component)
                            ->where('workcentre_added', $workcentre_added)
                            ->where('loc_code', $loc_code)
                            ->exists();
                        if ($already) {
                            $error = 'The selected component is already on this bom. You can modify its quantity but it cannot appear more than once on the same bom.';
                        } else {
                            Bom::create([
                                'parent' => $stock_id,
                                'component' => $component,
                                'workcentre_added' => $workcentre_added,
                                'loc_code' => $loc_code,
                                'quantity' => $quantity,
                            ]);
                            $msg = 'A new component part has been added to the bill of material for this item.';
                            $selected_id = -1;
                        }
                    }
                }
            }

            if ($request->has('cancel')) {
                $selected_id = -1;
            }
        }

        session(['bom_stock_id' => $stock_id]);

        // Manufactured items (mb_flag = 'M' for manufactured, 'B' for both)
        $manufactured_items = Item::where('is_active', true)
            ->whereIn('mb_flag', ['M', 'B'])
            ->orderBy('code')
            ->get(['code', 'name']);

        // BOM items for selected parent
        $bom_items = collect();
        if ($stock_id) {
            $bom_items = Bom::with(['componentItem', 'workCentre', 'location'])
                ->where('parent', $stock_id)
                ->orderBy('id')
                ->get()
                ->map(function ($b) {
                    return (object)[
                        'id' => $b->id,
                        'component' => $b->component,
                        'description' => $b->componentItem->name ?? '',
                        'location_name' => $b->location->location_name ?? '',
                        'WorkCentreDescription' => $b->workCentre->name ?? '',
                        'quantity' => $b->quantity,
                        'units' => $b->componentItem->unit_of_measure ?? 'each',
                    ];
                });
        }

        // Selected component for editing
        $selected_component = null;
        if ($selected_id > 0) {
            $selected_component = Bom::with(['componentItem'])->find($selected_id);
        }

        // Work centres dropdown
        $work_centres = WorkCentre::where('inactive', false)->orderBy('name')->get();
        $locations = Location::where('inactive', false)->orderBy('location_name')->get();

        // Component items (all items that could be components, excluding current parent)
        $component_items = Item::where('is_active', true)
            ->where('code', '!=', $stock_id ?: '')
            ->orderBy('code')
            ->get(['code', 'name']);

        return view('manufacturing.bom.index', compact(
            'msg', 'error', 'stock_id', 'bom_items', 'selected_id', 'selected_component',
            'manufactured_items', 'work_centres', 'locations', 'component_items'
        ));
    }

    public function workOrderEntry(Request $request): View
    {
        $msg = '';
        $error = '';
        $selected_id = $request->input('selected_id', $request->input('trans_no', ''));

        $wo_types = [0 => 'Assembly', 1 => 'Unassembly', 2 => 'Advanced Manufacture'];
        $wo_cost_types = ['Labour', 'Overhead', 'Materials'];

        // Get or generate reference
        $next_ref = $this->getNextWorkOrderRef();

        if ($request->isMethod('POST')) {
            if ($request->has('ADD_ITEM')) {
                $wo_ref = $request->input('wo_ref', $next_ref);
                $stock_id = $request->input('stock_id', '');
                $loc_code = $request->input('StockLocation', '');
                $type = (int)$request->input('type', 0);
                $quantity = (float)$request->input('quantity', 0);
                $date_ = $request->input('date_', date('Y-m-d'));
                $required_by = $request->input('RequDate', null);
                $labour = (float)$request->input('Labour', 0);
                $costs = (float)$request->input('Costs', 0);
                $cr_acc = $request->input('cr_acc', '');
                $cr_lab_acc = $request->input('cr_lab_acc', '');
                $memo = $request->input('memo_', '');

                $errors = [];
                if (!$stock_id) $errors[] = 'You must select an item to manufacture.';
                if ($quantity <= 0) $errors[] = 'The quantity entered is invalid or less than zero.';
                if (!$loc_code) $errors[] = 'You must select a destination location.';
                if ($type != 2 && !$this->hasBom($stock_id)) $errors[] = 'The selected item to manufacture does not have a BOM.';

                if (empty($errors)) {
                    $workOrder = WorkOrder::create([
                        'wo_ref' => $wo_ref,
                        'loc_code' => $loc_code,
                        'units_reqd' => $quantity,
                        'stock_id' => $stock_id,
                        'date_' => $date_,
                        'type' => $type,
                        'required_by' => $type == 2 ? $required_by : null,
                        'additional_costs' => $costs,
                        'labour_cost' => $labour,
                        'cr_acc' => $cr_acc,
                        'cr_lab_acc' => $cr_lab_acc,
                        'memo' => $memo,
                    ]);
                    $msg = 'Work order has been added. Reference: ' . $wo_ref;
                    $selected_id = $workOrder->id;
                } else {
                    $error = implode(' ', $errors);
                }
            }

            $update_id = $request->input('selected_id', 0);
            if ($request->has('UPDATE_ITEM') && $update_id) {
                $workOrder = WorkOrder::find($update_id);
                if ($workOrder && !$workOrder->closed) {
                    $workOrder->update([
                        'loc_code' => $request->input('StockLocation', $workOrder->loc_code),
                        'units_reqd' => (float)$request->input('quantity', $workOrder->units_reqd),
                        'date_' => $request->input('date_', $workOrder->date_),
                        'required_by' => $request->input('RequDate', $workOrder->required_by),
                        'additional_costs' => (float)$request->input('Costs', $workOrder->additional_costs),
                        'labour_cost' => (float)$request->input('Labour', $workOrder->labour_cost),
                        'cr_acc' => $request->input('cr_acc', $workOrder->cr_acc),
                        'cr_lab_acc' => $request->input('cr_lab_acc', $workOrder->cr_lab_acc),
                        'memo' => $request->input('memo_', $workOrder->memo),
                    ]);
                    $msg = 'Work order has been updated.';
                }
            }

            if ($request->has('delete') && $update_id) {
                $workOrder = WorkOrder::find($update_id);
                if ($workOrder && !$workOrder->released && !$workOrder->closed) {
                    $workOrder->delete();
                    $msg = 'Work order has been deleted.';
                    $selected_id = '';
                } else {
                    $error = 'This work order cannot be deleted because it has already been processed.';
                }
            }

            if ($request->has('close') && $update_id) {
                $workOrder = WorkOrder::find($update_id);
                if ($workOrder) {
                    $workOrder->update(['closed' => true]);
                    $msg = 'This work order has been closed. There can be no more issues against it.';
                }
            }
        }

        // Load existing work order for editing
        $workOrder = null;
        if ($selected_id) {
            $workOrder = WorkOrder::with(['item', 'location'])->find($selected_id);
        }

        $manufactured_items = Item::where('is_active', true)
            ->whereIn('mb_flag', ['M', 'B'])
            ->orderBy('code')
            ->get(['code', 'name']);

        $locations = Location::where('inactive', false)->orderBy('location_name')->get(['loc_code', 'location_name']);
        $gl_accounts = Account::where('is_active', true)->orderBy('code')->get(['code', 'name']);

        return view('manufacturing.work-order-entry', compact(
            'msg', 'error', 'workOrder', 'selected_id', 'wo_types', 'wo_cost_types',
            'manufactured_items', 'locations', 'gl_accounts', 'next_ref'
        ));
    }

    public function outstandingOrders(Request $request): View
    {
        $outstanding_only = $request->input('outstanding_only', $request->query('outstanding_only', 1));

        // Get filter inputs
        $OrderId = $request->input('OrderId', '');
        $OrderNumber = $request->input('OrderNumber', '');
        $StockLocation = $request->input('StockLocation', '');
        $SelectedStockItem = $request->input('SelectedStockItem', '');
        $OverdueOnly = $request->has('OverdueOnly');
        $OpenOnly = $request->has('OpenOnly');
        $search = $request->has('SearchOrders');

        // If search was submitted, remember filters in session
        if ($request->isMethod('POST') && $search) {
            session([
                'wo_search.OrderId' => $OrderId,
                'wo_search.OrderNumber' => $OrderNumber,
                'wo_search.StockLocation' => $StockLocation,
                'wo_search.SelectedStockItem' => $SelectedStockItem,
                'wo_search.OverdueOnly' => $OverdueOnly,
                'wo_search.OpenOnly' => $OpenOnly,
            ]);
        } elseif (!$search && !$request->isMethod('POST')) {
            // Restore from session on GET
            $OrderId = session('wo_search.OrderId', '');
            $OrderNumber = session('wo_search.OrderNumber', '');
            $StockLocation = session('wo_search.StockLocation', '');
            $SelectedStockItem = session('wo_search.SelectedStockItem', '');
            $OverdueOnly = session('wo_search.OverdueOnly', false);
            $OpenOnly = session('wo_search.OpenOnly', false);
        }

        // Build the query matching FA's get_sql_for_work_orders()
        $query = DB::table('work_orders as workorder')
            ->join('items as item', 'workorder.stock_id', '=', 'item.code')
            ->join('locations as location', 'workorder.loc_code', '=', 'location.loc_code')
            ->leftJoin('item_units as unit', 'item.unit_of_measure', '=', 'unit.name')
            ->select(
                'workorder.id',
                'workorder.wo_ref',
                'workorder.type',
                'location.location_name',
                'item.description',
                'workorder.units_reqd',
                'workorder.units_issued',
                'workorder.date_',
                'workorder.required_by',
                'workorder.released_date',
                'workorder.closed',
                'workorder.released',
                'workorder.stock_id',
                DB::raw('COALESCE(unit.decimals, 0) as decimals')
            );

        // Only open (not closed) filter
        if ($OpenOnly || $outstanding_only) {
            $query->where('workorder.closed', 0);
        }

        if ($StockLocation !== '') {
            $query->where('workorder.loc_code', $StockLocation);
        }

        if ($OrderId !== '') {
            $query->where('workorder.id', 'LIKE', "%{$OrderId}%");
        }

        if ($OrderNumber !== '') {
            $query->where('workorder.wo_ref', 'LIKE', "%{$OrderNumber}%");
        }

        if ($SelectedStockItem !== '') {
            $query->where('workorder.stock_id', $SelectedStockItem);
        }

        if ($OverdueOnly) {
            $today = date('Y-m-d');
            $query->where('workorder.required_by', '<', $today);
        }

        $query->orderBy('workorder.id', 'desc');

        $workOrders = $query->paginate(25);

        // Determine disabled state for Location/OverdueOnly/OpenOnly/StockItem
        $disableFilters = $OrderNumber !== '';

        // Work order types
        $wo_types = [0 => 'Assembly', 1 => 'Unassembly', 2 => 'Advanced Manufacture'];

        // Locations for filter dropdown
        $locations = Location::where('inactive', false)->orderBy('location_name')->get(['loc_code', 'location_name']);

        // Manufactured items for filter dropdown
        $manufactured_items = Item::where('is_active', true)
            ->whereIn('mb_flag', ['M', 'B'])
            ->orderBy('code')
            ->get(['code', 'name']);

        return view('manufacturing.outstanding-work-orders', compact(
            'workOrders', 'wo_types', 'outstanding_only',
            'OrderId', 'OrderNumber', 'StockLocation', 'SelectedStockItem',
            'OverdueOnly', 'OpenOnly', 'disableFilters',
            'locations', 'manufactured_items'
        ));
    }

    public function whereUsed(Request $request): View
    {
        $stock_id = $request->input('stock_id', $request->query('stock_id', ''));

        $items = Item::where('is_active', true)->orderBy('code')->get(['code', 'name']);

        $parents = collect();
        if ($stock_id) {
            $parents = DB::table('bom')
                ->join('items as parent_item', 'bom.parent', '=', 'parent_item.code')
                ->join('work_centres', 'bom.workcentre_added', '=', 'work_centres.id')
                ->join('locations', 'bom.loc_code', '=', 'locations.loc_code')
                ->where('bom.component', $stock_id)
                ->orderBy('bom.parent')
                ->select(
                    'bom.parent',
                    'parent_item.name as description',
                    'work_centres.name as WorkCentreName',
                    'locations.location_name',
                    'bom.quantity'
                )
                ->paginate(25);
        }

        return view('manufacturing.item-where-used', compact(
            'stock_id', 'items', 'parents'
        ));
    }

    public function workOrderInquiry(Request $request): View
    {
        $request->merge(['outstanding_only' => 0]);
        return $this->outstandingOrders($request);
    }

    public function costedBomInquiry(Request $request): View
    {
        $stock_id = $request->input('stock_id', $request->query('stock_id', ''));

        $manufactured_items = Item::where('is_active', true)
            ->whereIn('mb_flag', ['M', 'B'])
            ->orderBy('code')
            ->get(['code', 'name']);

        $bom_items = collect();
        $item = null;
        $total_cost = 0;

        if ($stock_id) {
            $item = Item::where('code', $stock_id)->first();

            $bom_items = DB::table('bom')
                ->join('items', 'bom.component', '=', 'items.code')
                ->join('work_centres', 'bom.workcentre_added', '=', 'work_centres.id')
                ->join('locations', 'bom.loc_code', '=', 'locations.loc_code')
                ->where('bom.parent', $stock_id)
                ->orderBy('bom.id')
                ->select(
                    'bom.component',
                    'items.name as description',
                    'work_centres.name as WorkCentreDescription',
                    'locations.location_name',
                    'bom.quantity',
                    'items.material_cost as ProductCost',
                    DB::raw('bom.quantity * items.material_cost as ComponentCost'),
                    'items.unit_of_measure'
                )
                ->get();

            $total_cost = $bom_items->sum('ComponentCost');
            if ($item) {
                if ($item->labour_cost) $total_cost += $item->labour_cost;
                if ($item->overhead_cost) $total_cost += $item->overhead_cost;
            }
        }

        return view('manufacturing.costed-bom-inquiry', compact(
            'stock_id', 'manufactured_items', 'bom_items', 'item', 'total_cost'
        ));
    }

    private function getNextWorkOrderRef(): string
    {
        $last = WorkOrder::orderBy('id', 'desc')->first();
        $nextNum = $last ? ((int)filter_var($last->wo_ref, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return 'WO-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    private function hasBom(string $stock_id): bool
    {
        return Bom::where('parent', $stock_id)->exists();
    }

    private function checkRecursiveBom($parent, $component): bool
    {
        if ($parent === $component) return true;
        $children = Bom::where('parent', $component)->pluck('component');
        foreach ($children as $child) {
            if ($this->checkRecursiveBom($parent, $child)) return true;
        }
        return false;
    }
}
