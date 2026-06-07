<?php

namespace App\Http\Controllers;

use App\Models\SalesGroup;
use App\Models\SalesType;
use App\Models\SalesPerson;
use App\Models\SalesArea;
use App\Models\CreditStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesSetupController extends Controller
{
    // Sales Groups (FA-style single-page CRUD)
    public function groupsIndex(Request $request): View|RedirectResponse
    {
        // Handle show_inactive toggle
        if ($request->has('show_inactive')) {
            session(['groups_show_inactive' => true]);
        } elseif ($request->has('show_inactive') === false && $request->isMethod('GET')) {
            // Only reset on explicit GET (checkbox unchecked submits without value)
        }

        if ($request->has('show_inactive') && !$request->has('selected_id')) {
            session(['groups_show_inactive' => true]);
        }
        if ($request->isMethod('GET') && !$request->has('show_inactive') && !$request->has('toggle_inactive')) {
            // Only reset from session on clean GET
        }
        if ($request->isMethod('POST') && !$request->has('show_inactive') && $request->has('Mode') === false) {
            session()->forget('groups_show_inactive');
        }

        $show_inactive = session('groups_show_inactive', false);
        $selected_id = $request->input('selected_id', 0);
        $mode = $request->input('Mode', '');
        $edit_group = null;
        $edit_description = '';

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->toggle_inactive;
            $group = SalesGroup::find($toggle_id);
            if ($group) {
                $group->status = $group->status === 'inactive' ? 'active' : 'inactive';
                $group->save();
                session()->flash('success', 'Sales group status updated.');
            }
            return redirect()->route('sales.setup.groups', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'RESET') {
            $selected_id = 0;
            $sav = $show_inactive;
            session()->forget('groups_selected_id');
            if ($sav) {
                session(['groups_show_inactive' => true]);
            } else {
                session()->forget('groups_show_inactive');
            }
            return redirect()->route('sales.setup.groups', $sav ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'ADD_ITEM') {
            $validated = $request->validate([
                'description' => 'required|string|max:100|unique:sales_groups,group_name',
            ]);

            SalesGroup::create(['group_name' => $request->description]);

            session()->flash('success', 'New sales group has been added');

            return redirect()->route('sales.setup.groups', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'UPDATE_ITEM') {
            $validated = $request->validate([
                'description' => 'required|string|max:100|unique:sales_groups,group_name,' . $selected_id,
            ]);

            $group = SalesGroup::findOrFail($selected_id);
            $group->update(['group_name' => $request->description]);

            session()->flash('success', 'Selected sales group has been updated');

            return redirect()->route('sales.setup.groups', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'Delete') {
            $group = SalesGroup::find($selected_id);
            if ($group) {
                $branch_count = \DB::table('customer_branches')->where('group_no', $selected_id)->count();
                if ($branch_count > 0) {
                    session()->flash('error', 'Cannot delete this group because customers have been created using this group.');
                } else {
                    $group->delete();
                    session()->flash('success', 'Selected sales group has been deleted');
                }
            }

            return redirect()->route('sales.setup.groups', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        // Edit mode - load group for editing
        if ($mode === 'Edit') {
            $edit_group = SalesGroup::find($selected_id);
            if ($edit_group) {
                $edit_description = $edit_group->group_name;
            }
        }

        // Query groups
        $groupsQuery = SalesGroup::query();
        if (!$show_inactive) {
            $groupsQuery->where('status', 'active');
        }
        $groups = $groupsQuery->orderBy('id')->get();

        return view('sales.setup.groups', compact(
            'groups', 'edit_group', 'edit_description', 'selected_id', 'show_inactive'
        ));
    }

    // Sales Types (FA-style single-page CRUD)
    public function typesIndex(Request $request): View|RedirectResponse
    {
        if ($request->has('show_inactive')) {
            session(['types_show_inactive' => true]);
        } elseif ($request->isMethod('GET') && !$request->has('toggle_inactive')) {
            // preserve existing session state
        }

        $show_inactive = session('types_show_inactive', false);
        $selected_id = $request->input('selected_id', 0);
        $mode = $request->input('Mode', '');
        $edit_type = null;
        $edit_type_name = '';
        $edit_factor = '1.0000';
        $edit_tax_included = false;

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->toggle_inactive;
            $type = SalesType::find($toggle_id);
            if ($type) {
                $type->status = $type->status === 'inactive' ? 'active' : 'inactive';
                $type->save();
                session()->flash('success', 'Sales type status updated.');
            }
            return redirect()->route('sales.setup.types', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'RESET') {
            $sav = $show_inactive;
            if ($sav) {
                session(['types_show_inactive' => true]);
            } else {
                session()->forget('types_show_inactive');
            }
            return redirect()->route('sales.setup.types', $sav ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'ADD_ITEM') {
            $validated = $request->validate([
                'type_name' => 'required|string|max:100|unique:sales_types,type_name',
                'factor' => 'required|numeric|min:0',
                'tax_included' => 'nullable|boolean',
            ]);

            SalesType::create([
                'type_name' => $request->type_name,
                'factor' => $request->factor ?? 1,
                'tax_included' => $request->boolean('tax_included'),
            ]);

            session()->flash('success', 'New sales type has been added');

            return redirect()->route('sales.setup.types', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'UPDATE_ITEM') {
            $validated = $request->validate([
                'type_name' => 'required|string|max:100|unique:sales_types,type_name,' . $selected_id,
                'factor' => 'required|numeric|min:0',
                'tax_included' => 'nullable|boolean',
            ]);

            $type = SalesType::findOrFail($selected_id);
            $type->update([
                'type_name' => $request->type_name,
                'factor' => $request->factor ?? 1,
                'tax_included' => $request->boolean('tax_included'),
            ]);

            session()->flash('success', 'Selected sales type has been updated');

            return redirect()->route('sales.setup.types', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'Delete') {
            $type = SalesType::find($selected_id);
            if ($type) {
                $trans_count = \DB::table('sales_orders')->where('sales_type_id', $selected_id)->count();
                $cust_count = \DB::table('customers')->where('sales_type_id', $selected_id)->count();
                if ($trans_count > 0) {
                    session()->flash('error', 'Cannot delete this sales type because customer transactions have been created using this sales type.');
                } elseif ($cust_count > 0) {
                    session()->flash('error', 'Cannot delete this sales type because customers are currently set up to use this sales type.');
                } else {
                    $type->delete();
                    session()->flash('success', 'Selected sales type has been deleted');
                }
            }
            return redirect()->route('sales.setup.types', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        // Edit mode
        if ($mode === 'Edit') {
            $edit_type = SalesType::find($selected_id);
            if ($edit_type) {
                $edit_type_name = $edit_type->type_name;
                $edit_factor = number_format($edit_type->factor ?? 1, 4);
                $edit_tax_included = $edit_type->tax_included ?? false;
            }
        }

        // Query types
        $typesQuery = SalesType::query();
        if (!$show_inactive) {
            $typesQuery->where('status', 'active');
        }
        $types = $typesQuery->orderBy('id')->get();

        // Find base sales type (first active type with factor 1.0000)
        $base_sales_type_id = SalesType::where('factor', 1.0000)->where('status', 'active')->value('id');
        if (!$base_sales_type_id) {
            $base_sales_type_id = SalesType::where('status', 'active')->orderBy('id')->value('id');
        }

        return view('sales.setup.types', compact(
            'types', 'edit_type', 'edit_type_name', 'edit_factor', 'edit_tax_included',
            'selected_id', 'show_inactive', 'base_sales_type_id'
        ));
    }

    // Sales Persons
    public function personsIndex(Request $request): View|RedirectResponse
    {
        if ($request->has('show_inactive')) {
            session(['persons_show_inactive' => true]);
        } elseif ($request->isMethod('GET') && !$request->has('toggle_inactive')) {
            // preserve existing session state
        }

        $show_inactive = session('persons_show_inactive', false);
        $selected_id = $request->input('selected_id', 0);
        $mode = $request->input('Mode', '');
        $edit_person = null;
        $edit_name = '';
        $edit_phone = '';
        $edit_fax = '';
        $edit_email = '';
        $edit_provision = '0.00';
        $edit_break_pt = '0.00';
        $edit_provision2 = '0.00';

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $person = SalesPerson::find($request->toggle_inactive);
            if ($person) {
                $person->status = $person->status === 'inactive' ? 'active' : 'inactive';
                $person->save();
                session()->flash('success', 'Sales person status updated.');
            }
            return redirect()->route('sales.setup.persons', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'RESET') {
            $sav = $show_inactive;
            if ($sav) {
                session(['persons_show_inactive' => true]);
            } else {
                session()->forget('persons_show_inactive');
            }
            return redirect()->route('sales.setup.persons', $sav ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'ADD_ITEM') {
            $validated = $request->validate([
                'salesman_name' => 'required|string|max:255',
                'salesman_phone' => 'nullable|string|max:50',
                'salesman_fax' => 'nullable|string|max:50',
                'salesman_email' => 'required|email|max:255|unique:sales_persons,email',
                'provision' => 'required|numeric|min:0|max:100',
                'break_pt' => 'required|numeric|min:0',
                'provision2' => 'required|numeric|min:0|max:100',
            ]);

            SalesPerson::create([
                'name' => $request->salesman_name,
                'phone' => $request->salesman_phone ?? '',
                'fax' => $request->salesman_fax ?? '',
                'email' => $request->salesman_email,
                'commission_rate' => $request->provision,
                'monthly_target' => $request->break_pt,
                'provision2' => $request->provision2,
            ]);

            session()->flash('success', 'New sales person data have been added');

            return redirect()->route('sales.setup.persons', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'UPDATE_ITEM') {
            $validated = $request->validate([
                'salesman_name' => 'required|string|max:255',
                'salesman_phone' => 'nullable|string|max:50',
                'salesman_fax' => 'nullable|string|max:50',
                'salesman_email' => 'required|email|max:255|unique:sales_persons,email,' . $selected_id,
                'provision' => 'required|numeric|min:0|max:100',
                'break_pt' => 'required|numeric|min:0',
                'provision2' => 'required|numeric|min:0|max:100',
            ]);

            $person = SalesPerson::findOrFail($selected_id);
            $person->update([
                'name' => $request->salesman_name,
                'phone' => $request->salesman_phone ?? '',
                'fax' => $request->salesman_fax ?? '',
                'email' => $request->salesman_email,
                'commission_rate' => $request->provision,
                'monthly_target' => $request->break_pt,
                'provision2' => $request->provision2,
            ]);

            session()->flash('success', 'Selected sales person data have been updated');

            return redirect()->route('sales.setup.persons', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'Delete') {
            $person = SalesPerson::find($selected_id);
            if ($person) {
                $branch_count = \DB::table('customer_branches')->where('salesman', $selected_id)->count();
                if ($branch_count > 0) {
                    session()->flash('error', 'Cannot delete this sales-person because branches are set up referring to this sales-person - first alter the branches concerned.');
                } else {
                    $person->delete();
                    session()->flash('success', 'Selected sales person data have been deleted');
                }
            }
            return redirect()->route('sales.setup.persons', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        // Edit mode
        if ($mode === 'Edit') {
            $edit_person = SalesPerson::find($selected_id);
            if ($edit_person) {
                $edit_name = $edit_person->name;
                $edit_phone = $edit_person->phone ?? '';
                $edit_fax = $edit_person->fax ?? '';
                $edit_email = $edit_person->email;
                $edit_provision = number_format($edit_person->commission_rate, 2);
                $edit_break_pt = number_format($edit_person->monthly_target, 2);
                $edit_provision2 = number_format($edit_person->provision2, 2);
            }
        }

        // Query persons
        $personsQuery = SalesPerson::query();
        if (!$show_inactive) {
            $personsQuery->where('status', 'active');
        }
        $persons = $personsQuery->orderBy('id')->get();

        return view('sales.setup.persons', compact(
            'persons', 'edit_person', 'edit_name', 'edit_phone', 'edit_fax', 'edit_email',
            'edit_provision', 'edit_break_pt', 'edit_provision2',
            'selected_id', 'show_inactive'
        ));
    }

    // Sales Areas (FA-style single-page CRUD)
    public function areasIndex(Request $request): View|RedirectResponse
    {
        if ($request->has('show_inactive')) {
            session(['areas_show_inactive' => true]);
        } elseif ($request->isMethod('GET') && !$request->has('toggle_inactive')) {
            // preserve existing session state
        }

        $show_inactive = session('areas_show_inactive', false);
        $selected_id = $request->input('selected_id', 0);
        $mode = $request->input('Mode', '');
        $edit_area = null;
        $edit_area_name = '';

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $area = SalesArea::find($request->toggle_inactive);
            if ($area) {
                $area->status = $area->status === 'inactive' ? 'active' : 'inactive';
                $area->save();
                session()->flash('success', 'Sales area status updated.');
            }
            return redirect()->route('sales.setup.areas', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'RESET') {
            $sav = $show_inactive;
            if ($sav) {
                session(['areas_show_inactive' => true]);
            } else {
                session()->forget('areas_show_inactive');
            }
            return redirect()->route('sales.setup.areas', $sav ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'ADD_ITEM') {
            $validated = $request->validate([
                'description' => 'required|string|max:100|unique:sales_areas,area_name',
            ]);

            SalesArea::create(['area_name' => $request->description]);

            session()->flash('success', 'New sales area has been added');

            return redirect()->route('sales.setup.areas', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'UPDATE_ITEM') {
            $validated = $request->validate([
                'description' => 'required|string|max:100|unique:sales_areas,area_name,' . $selected_id,
            ]);

            $area = SalesArea::findOrFail($selected_id);
            $area->update(['area_name' => $request->description]);

            session()->flash('success', 'Selected sales area has been updated');

            return redirect()->route('sales.setup.areas', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'Delete') {
            $area = SalesArea::find($selected_id);
            if ($area) {
                $branch_count = \DB::table('customer_branches')->where('area_id', $selected_id)->count();
                if ($branch_count > 0) {
                    session()->flash('error', 'Cannot delete this area because customer branches have been created using this area.');
                } else {
                    $area->delete();
                    session()->flash('success', 'Selected sales area has been deleted');
                }
            }
            return redirect()->route('sales.setup.areas', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        // Edit mode
        if ($mode === 'Edit') {
            $edit_area = SalesArea::find($selected_id);
            if ($edit_area) {
                $edit_area_name = $edit_area->area_name;
            }
        }

        // Query areas
        $areasQuery = SalesArea::query();
        if (!$show_inactive) {
            $areasQuery->where('status', 'active');
        }
        $areas = $areasQuery->orderBy('id')->get();

        return view('sales.setup.areas', compact(
            'areas', 'edit_area', 'edit_area_name',
            'selected_id', 'show_inactive'
        ));
    }

    // Credit Status (FA-style single-page CRUD)
    public function creditStatusIndex(Request $request): View|RedirectResponse
    {
        if ($request->has('show_inactive')) {
            session(['creditstatus_show_inactive' => true]);
        } elseif ($request->isMethod('GET') && !$request->has('toggle_inactive')) {
            // preserve existing session state
        }

        $show_inactive = session('creditstatus_show_inactive', false);
        $selected_id = $request->input('selected_id', 0);
        $mode = $request->input('Mode', '');
        $edit_status = null;
        $edit_reason_description = '';
        $edit_disallow_invoices = false;

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $status = CreditStatus::find($request->toggle_inactive);
            if ($status) {
                $status->status = $status->status === 'inactive' ? 'active' : 'inactive';
                $status->save();
                session()->flash('success', 'Credit status status updated.');
            }
            return redirect()->route('sales.setup.credit-status', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'RESET') {
            $sav = $show_inactive;
            if ($sav) {
                session(['creditstatus_show_inactive' => true]);
            } else {
                session()->forget('creditstatus_show_inactive');
            }
            return redirect()->route('sales.setup.credit-status', $sav ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'ADD_ITEM') {
            $validated = $request->validate([
                'reason_description' => 'required|string|max:100|unique:credit_statuses,status_name',
                'DisallowInvoices' => 'nullable|boolean',
            ]);

            CreditStatus::create([
                'status_name' => $request->reason_description,
                'dissallow_invoices' => $request->boolean('DisallowInvoices'),
            ]);

            session()->flash('success', 'New credit status has been added');

            return redirect()->route('sales.setup.credit-status', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'UPDATE_ITEM') {
            $validated = $request->validate([
                'reason_description' => 'required|string|max:100|unique:credit_statuses,status_name,' . $selected_id,
                'DisallowInvoices' => 'nullable|boolean',
            ]);

            $status = CreditStatus::findOrFail($selected_id);
            $status->update([
                'status_name' => $request->reason_description,
                'dissallow_invoices' => $request->boolean('DisallowInvoices'),
            ]);

            session()->flash('success', 'Selected credit status has been updated');

            return redirect()->route('sales.setup.credit-status', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        if ($mode === 'Delete') {
            $status = CreditStatus::find($selected_id);
            if ($status) {
                $cust_count = \DB::table('customers')->where('credit_status', $selected_id)->count();
                if ($cust_count > 0) {
                    session()->flash('error', 'Cannot delete this credit status because customer accounts have been created referring to it.');
                } else {
                    $status->delete();
                    session()->flash('success', 'Selected credit status has been deleted');
                }
            }
            return redirect()->route('sales.setup.credit-status', $show_inactive ? ['show_inactive' => '1'] : []);
        }

        // Edit mode
        if ($mode === 'Edit') {
            $edit_status = CreditStatus::find($selected_id);
            if ($edit_status) {
                $edit_reason_description = $edit_status->status_name;
                $edit_disallow_invoices = $edit_status->dissallow_invoices ?? false;
            }
        }

        // Query statuses
        $statusesQuery = CreditStatus::query();
        if (!$show_inactive) {
            $statusesQuery->where('status', 'active');
        }
        $statuses = $statusesQuery->orderBy('id')->get();

        return view('sales.setup.credit-status', compact(
            'statuses', 'edit_status', 'edit_reason_description', 'edit_disallow_invoices',
            'selected_id', 'show_inactive'
        ));
    }

    // Recurrent Invoices
    public function recurrentInvoicesIndex(): View
    {
        $statuses = CreditStatus::latest()->paginate(10);
        return view('sales.setup.recurrent-invoices', compact('statuses'));
    }
}
