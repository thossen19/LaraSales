<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use App\Models\Setting;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HrController extends Controller
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

    public function documentTypes(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = null;
        $show_inactive = $request->boolean('show_inactive');

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->input('toggle_inactive');
            $dt = DB::table('document_types')->where('type_id', $toggle_id)->first();
            if ($dt) {
                DB::table('document_types')->where('type_id', $toggle_id)->update([
                    'inactive' => !$dt->inactive,
                ]);
                $msg = $dt->inactive ? 'Document type activated' : 'Document type deactivated';
            }
        }

        // Handle ADD/UPDATE
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $name = trim($request->input('name', ''));
            $days = $request->input('days', '');
            $update_id = $request->input('selected_id');

            if (strlen($name) == 0) {
                $error = 'The document type description cannot be empty.';
                $input_error = 1;
            } elseif (!is_numeric($days)) {
                $error = 'Days before expiry must be a number.';
                $input_error = 1;
            }

            if (!$input_error) {
                $data = [
                    'type_name' => $name,
                    'notify_before' => (int)$days,
                ];

                if ($request->has('UPDATE_ITEM') && $update_id) {
                    DB::table('document_types')->where('type_id', $update_id)->update($data);
                    $msg = 'Selected document type has been updated';
                } else {
                    DB::table('document_types')->insert($data);
                    $msg = 'New document type item has been added';
                }

                $selected_id = null;
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            if (DB::table('employee_docs')->where('type_id', $delete_id)->exists()) {
                $error = 'This document category cannot be deleted.';
            } else {
                DB::table('document_types')->where('type_id', $delete_id)->delete();
                $msg = 'Selected document category has been deleted';
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        $types = DB::table('document_types')
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('type_id')
            ->get();

        $selected_type = null;
        if ($selected_id) {
            $selected_type = DB::table('document_types')->where('type_id', $selected_id)->first();
        }

        return view('hr.document-types', compact(
            'msg', 'error', 'types', 'selected_id', 'selected_type', 'show_inactive'
        ));
    }

    public function departments(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = null;
        $show_inactive = $request->boolean('show_inactive');

        $company_id = 1;

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        $USE_DEPT_ACC = Setting::getSetting('payroll_dept_based', $company_id, false);

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->input('toggle_inactive');
            $dept = DB::table('departments')->where('dept_id', $toggle_id)->first();
            if ($dept) {
                DB::table('departments')->where('dept_id', $toggle_id)->update([
                    'inactive' => !$dept->inactive,
                ]);
                $msg = $dept->inactive ? 'Selected department has been activated' : 'Selected department has been deactivated';
            }
        }

        // Handle ADD/UPDATE
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $name = trim($request->input('name', ''));
            $basic_acc = $request->input('basic_acc', '');
            $update_id = $request->input('selected_id');

            if (strlen($name) == 0) {
                $error = 'The Department name cannot be empty.';
                $input_error = 1;
            } elseif ($USE_DEPT_ACC && empty($basic_acc)) {
                $error = 'Please select basic account';
                $input_error = 1;
            } elseif ($USE_DEPT_ACC && !empty($basic_acc)) {
                $is_expense = Account::where('company_id', $company_id)
                    ->where('code', $basic_acc)
                    ->expenses()
                    ->exists();
                if (!$is_expense) {
                    $error = 'Salary Basic Account must be an expenses account.';
                    $input_error = 1;
                }
            }

            if (!$input_error) {
                $data = [
                    'dept_name' => $name,
                    'basic_account' => $USE_DEPT_ACC ? $basic_acc : null,
                ];

                if ($request->has('UPDATE_ITEM') && $update_id) {
                    DB::table('departments')->where('dept_id', $update_id)->update($data);
                    $msg = 'Selected department has been updated';
                } else {
                    DB::table('departments')->insert($data);
                    $msg = 'New department has been added';
                }

                $selected_id = null;
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            $dept = DB::table('departments')->where('dept_id', $delete_id)->first();
            if ($dept && DB::table('employees')->where('department', $dept->dept_name)->exists()) {
                $error = 'The Department cannot be deleted.';
            } else {
                DB::table('departments')->where('dept_id', $delete_id)->delete();
                $msg = 'Selected department has been deleted';
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        $departments = DB::table('departments')
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('dept_id')
            ->get();

        $selected_department = null;
        if ($selected_id) {
            $selected_department = DB::table('departments')->where('dept_id', $selected_id)->first();
        }

        $all_accounts = Account::where('company_id', $company_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['code', 'name', 'account_type']);

        return view('hr.departments', compact(
            'msg', 'error', 'departments', 'selected_id', 'selected_department',
            'show_inactive', 'USE_DEPT_ACC', 'all_accounts'
        ));
    }

    public function defaultSettings(Request $request): View
    {
        $msg = null;
        $error = null;
        $company_id = 1;

        $keyFields = [
            'payroll_payable_act', 'payroll_deductleave_act', 'payroll_overtime_act',
            'payroll_month_work_days', 'payroll_work_hours', 'weekend_day',
            'payroll_dept_based', 'payroll_grades',
        ];

        // Handle form submission
        if ($request->isMethod('POST') && $request->has('submit')) {
            $input_error = 0;

            $month_days = $request->input('payroll_month_work_days', '');
            $work_hours = $request->input('payroll_work_hours', '');
            $grades = $request->input('payroll_grades', '');

            if (!is_numeric($month_days) || $month_days < 0 || $month_days > 31 || strlen($month_days) == 0) {
                $error = 'The number of month working days must be between 0 and 31.';
                $input_error = 1;
            } elseif (!is_numeric($work_hours) || $work_hours < 0 || $work_hours > 24 || strlen($work_hours) == 0) {
                $error = 'The number of working hours must be between 0 and 24.';
                $input_error = 1;
            } elseif (is_numeric($grades)) {
                // Check max grade used by employees (grade_id column not present yet, pass through)
                $max_grade = DB::table('employees')->where('is_active', true)->max('grade_id');
                if ($max_grade && $grades < $max_grade) {
                    $error = sprintf('Grade %s is being used by employees, cannot select a lower grade', $max_grade);
                    $input_error = 1;
                }
            }

            if (!$input_error) {
                foreach ($keyFields as $key) {
                    if ($key === 'payroll_dept_based') {
                        $value = $request->has($key) ? '1' : '0';
                        Setting::setSetting($key, $value, $company_id, 'boolean', 'hr');
                    } else {
                        $value = $request->input($key, '');
                        Setting::setSetting($key, $value, $company_id, 'string', 'hr');
                    }
                }

                $msg = 'The Payroll setup has been updated.';
            }
        }

        // Load existing settings
        $prefs = [];
        foreach ($keyFields as $key) {
            $prefs[$key] = Setting::getSetting($key, $company_id, '');
        }

        // Ensure defaults for empty values
        if ($prefs['weekend_day'] === '' || $prefs['weekend_day'] === null) {
            $prefs['weekend_day'] = 7;
            Setting::setSetting('weekend_day', '7', $company_id, 'string', 'hr');
        }
        if ($prefs['payroll_month_work_days'] === '') {
            $prefs['payroll_month_work_days'] = 22;
        }
        if ($prefs['payroll_work_hours'] === '') {
            $prefs['payroll_work_hours'] = 8;
        }
        if ($prefs['payroll_grades'] === '') {
            $prefs['payroll_grades'] = 1;
        }

        $weekdays = [
            1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
        ];

        $all_accounts = Account::where('company_id', $company_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['code', 'name', 'account_type']);

        $max_grade_number = 20;

        return view('hr.default-settings', compact(
            'msg', 'error', 'prefs', 'weekdays', 'all_accounts', 'max_grade_number'
        ));
    }

    public function jobPositions(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = -1;
        $show_inactive = $request->boolean('show_inactive');

        $company_id = 1;

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        $USE_DEPT_ACC = Setting::getSetting('payroll_dept_based', $company_id, false);

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->input('toggle_inactive');
            $pos = DB::table('positions')->where('position_id', $toggle_id)->first();
            if ($pos) {
                DB::table('positions')->where('position_id', $toggle_id)->update([
                    'inactive' => !$pos->inactive,
                ]);
                $msg = $pos->inactive ? 'Selected job position has been activated' : 'Selected job position has been deactivated';
            }
        }

        // Handle ADD/UPDATE
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $name = trim($request->input('name', ''));
            $account_id = $request->input('AccountId', '');
            $amount = $request->input('amount', '');
            $pay_basis = $request->input('payBasis', 0);
            $update_id = $request->input('selected_id', -1);

            if (empty($name)) {
                $error = 'Name field cannot be empty.';
                $input_error = 1;
            } elseif (!is_numeric($amount) || $amount < 0) {
                $error = 'Amount field value must be a positive number.';
                $input_error = 1;
            } elseif (!empty($account_id) && !$USE_DEPT_ACC) {
                $is_expense = Account::where('company_id', $company_id)
                    ->where('code', $account_id)
                    ->expenses()
                    ->exists();
                if (!$is_expense) {
                    $error = 'Salary Basic Account must be an expenses account.';
                    $input_error = 1;
                }
            }

            if ($input_error) {
                if ($update_id && $update_id !== -1) {
                    $selected_id = $update_id;
                }
            } else {
                DB::beginTransaction();

                try {
                    if ($update_id && $update_id !== -1) {
                        DB::table('positions')->where('position_id', $update_id)->update([
                            'position_name' => $name,
                            'pay_basis' => $pay_basis,
                        ]);
                        $added_position = $update_id;
                    } else {
                        $added_position = DB::table('positions')->insertGetId([
                            'position_name' => $name,
                            'pay_basis' => $pay_basis,
                        ]);
                    }

                    // Set basic salary in salary_structure
                    $salary_data = [
                        'date' => now()->toDateString(),
                        'position_id' => $added_position,
                        'grade_id' => 0,
                        'pay_rule_id' => $account_id ?? '',
                        'pay_amount' => (float)$amount,
                        'type' => 1,
                        'is_basic' => true,
                    ];

                    $existing = DB::table('salary_structure')
                        ->where('position_id', $added_position)
                        ->where('grade_id', 0)
                        ->first();

                    if ($existing) {
                        DB::table('salary_structure')
                            ->where('position_id', $added_position)
                            ->where('grade_id', 0)
                            ->update([
                                'pay_rule_id' => $account_id ?? '',
                                'pay_amount' => (float)$amount,
                            ]);
                    } else {
                        DB::table('salary_structure')->insert($salary_data);
                    }

                    DB::commit();

                    if ($update_id && $update_id !== -1) {
                        $msg = 'Selected job position has been updated';
                    } else {
                        $msg = 'New job position has been added';
                    }

                    $selected_id = -1;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $error = 'An error occurred while saving the job position.';
                }
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            $pos = DB::table('positions')->where('position_id', $delete_id)->first();
            if ($pos && DB::table('employees')->where('position', $pos->position_name)->exists()) {
                $error = 'This job position cannot be deleted.';
            } else {
                DB::table('salary_structure')->where('position_id', $delete_id)->delete();
                DB::table('positions')->where('position_id', $delete_id)->delete();
                $msg = 'Selected job position has been deleted';
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        $positions = DB::table('positions')
            ->leftJoin('salary_structure', function ($join) {
                $join->on('positions.position_id', '=', 'salary_structure.position_id')
                    ->where('salary_structure.grade_id', '=', 0)
                    ->where('salary_structure.is_basic', '=', true);
            })
            ->when(!$show_inactive, fn($q) => $q->where('positions.inactive', false))
            ->orderBy('positions.position_id')
            ->select('positions.*', 'salary_structure.pay_amount', 'salary_structure.pay_rule_id')
            ->get();

        $selected_position = null;
        if ($selected_id !== -1) {
            $selected_position = DB::table('positions')
                ->leftJoin('salary_structure', function ($join) {
                    $join->on('positions.position_id', '=', 'salary_structure.position_id')
                        ->where('salary_structure.grade_id', '=', 0)
                        ->where('salary_structure.is_basic', '=', true);
                })
                ->where('positions.position_id', $selected_id)
                ->select('positions.*', 'salary_structure.pay_amount', 'salary_structure.pay_rule_id')
                ->first();
        }

        $all_accounts = Account::where('company_id', $company_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['code', 'name', 'account_type']);

        return view('hr.job-positions', compact(
            'msg', 'error', 'positions', 'selected_id', 'selected_position',
            'show_inactive', 'USE_DEPT_ACC', 'all_accounts'
        ));
    }

    public function grades(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = -1;
        $company_id = 1;

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        $grades_no = (int)Setting::getSetting('payroll_grades', $company_id, 0);
        $max_grade_number = 20;
        $position_count = DB::table('positions')->count();

        // Handle ADD_ITEM / UPDATE_ITEM
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $position_id = $request->input('position_id');
            $update_id = $request->input('selected_id', -1);

            if (empty($position_id)) {
                $error = 'Please select a Job Position.';
                $input_error = 1;
            }

            if (!$input_error) {
                $basic_acc = DB::table('salary_structure')
                    ->where('position_id', $position_id)
                    ->where('grade_id', 0)
                    ->value('pay_rule_id');

                DB::beginTransaction();
                try {
                    for ($i = 1; $i <= $max_grade_number; $i++) {
                        $amt_str = $request->input('amt_'.$i, '');
                        $amt = is_numeric($amt_str) ? (float)$amt_str : 0;

                        if ($amt < 0) {
                            $error = 'Pay amount cannot be a negative number';
                            $input_error = 1;
                            break;
                        }

                        $position = DB::table('positions')
                            ->leftJoin('salary_structure', function ($join) {
                                $join->on('positions.position_id', '=', 'salary_structure.position_id')
                                    ->where('salary_structure.grade_id', '=', 0)
                                    ->where('salary_structure.is_basic', '=', true);
                            })
                            ->where('positions.position_id', $position_id)
                            ->select('positions.*', 'salary_structure.pay_amount')
                            ->first();

                        $base_amt = $position->pay_amount ?? 0;
                        $final_amt = $amt > 0 ? $amt : $base_amt;

                        $existing = DB::table('grade_table')
                            ->where('grade_id', $i)
                            ->where('position_id', $position_id)
                            ->first();

                        if ($existing) {
                            DB::table('grade_table')
                                ->where('grade_id', $i)
                                ->where('position_id', $position_id)
                                ->update(['amount' => $final_amt]);

                            if (DB::table('salary_structure')
                                ->where('position_id', $position_id)
                                ->where('grade_id', $i)
                                ->exists()) {
                                DB::table('salary_structure')
                                    ->where('position_id', $position_id)
                                    ->where('grade_id', $i)
                                    ->update([
                                        'pay_rule_id' => $basic_acc ?? '',
                                        'pay_amount' => $final_amt,
                                    ]);
                            } else {
                                DB::table('salary_structure')->insert([
                                    'date' => now()->toDateString(),
                                    'position_id' => $position_id,
                                    'grade_id' => $i,
                                    'pay_rule_id' => $basic_acc ?? '',
                                    'pay_amount' => $final_amt,
                                    'type' => 1,
                                    'is_basic' => true,
                                ]);
                            }
                        } else {
                            DB::table('grade_table')->insert([
                                'grade_id' => $i,
                                'position_id' => $position_id,
                                'amount' => $final_amt,
                            ]);

                            DB::table('salary_structure')->insert([
                                'date' => now()->toDateString(),
                                'position_id' => $position_id,
                                'grade_id' => $i,
                                'pay_rule_id' => $basic_acc ?? '',
                                'pay_amount' => $final_amt,
                                'type' => 1,
                                'is_basic' => true,
                            ]);
                        }
                    }

                    if (!$input_error) {
                        DB::commit();
                        $msg = 'Grade table has been updated';
                        $selected_id = -1;
                    } else {
                        DB::rollBack();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $error = 'An error occurred while saving the grade table.';
                }
            }

            if ($input_error && $position_id) {
                $selected_id = $update_id !== -1 ? $update_id : -1;
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            if (DB::table('employees')->where('position_id', $delete_id)->exists()) {
                $error = 'Grade table for selected job position cannot be deleted.';
            } else {
                DB::table('grade_table')->where('position_id', $delete_id)->delete();
                DB::table('salary_structure')->where('position_id', $delete_id)->where('grade_id', '>', 0)->delete();
                $msg = 'Grade table of selected job position has been deleted';
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        // Get all positions with their basic amount
        $positions = DB::table('positions')
            ->leftJoin('salary_structure', function ($join) {
                $join->on('positions.position_id', '=', 'salary_structure.position_id')
                    ->where('salary_structure.grade_id', '=', 0)
                    ->where('salary_structure.is_basic', '=', true);
            })
            ->orderBy('positions.position_id')
            ->select('positions.*', 'salary_structure.pay_amount')
            ->get();

        // Get grade amounts for table display
        $grade_amounts = [];
        foreach ($positions as $pos) {
            $grades = DB::table('grade_table')
                ->where('position_id', $pos->position_id)
                ->get();
            foreach ($grades as $g) {
                $grade_amounts[$pos->position_id][$g->grade_id] = $g->amount;
            }
        }

        // Get selected position data for the edit form
        $selected_position = null;
        $selected_position_grades = [];
        if ($selected_id !== -1) {
            $selected_position = DB::table('positions')
                ->leftJoin('salary_structure', function ($join) {
                    $join->on('positions.position_id', '=', 'salary_structure.position_id')
                        ->where('salary_structure.grade_id', '=', 0)
                        ->where('salary_structure.is_basic', '=', true);
                })
                ->where('positions.position_id', $selected_id)
                ->select('positions.*', 'salary_structure.pay_amount')
                ->first();

            $grades = DB::table('grade_table')
                ->where('position_id', $selected_id)
                ->get();
            foreach ($grades as $g) {
                $selected_position_grades[$g->grade_id] = $g->amount;
            }
        }

        // All positions for dropdown
        $all_positions = DB::table('positions')
            ->orderBy('position_id')
            ->get(['position_id', 'position_name']);

        return view('hr.grades', compact(
            'msg', 'error', 'positions', 'grade_amounts', 'grades_no', 'max_grade_number',
            'selected_id', 'selected_position', 'selected_position_grades',
            'all_positions', 'position_count'
        ));
    }

    public function payElements(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = '';
        $company_id = 1;

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        // Handle ADD_ITEM / UPDATE_ITEM
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $element_name = trim($request->input('element_name', ''));
            $account_id = $request->input('AccountId', '');
            $update_id = $request->input('selected_id', '');

            if (empty($element_name)) {
                $error = 'Element Name cannot be empty.';
                $input_error = 1;
            } elseif (empty($update_id) && DB::table('pay_element')->where('account_code', $account_id)->exists()) {
                $error = 'Selected account has already exists.';
                $input_error = 1;
            }

            if ($input_error) {
                if ($update_id) {
                    $selected_id = $update_id;
                }
            } else {
                if ($update_id) {
                    DB::table('pay_element')->where('element_id', $update_id)->update([
                        'element_name' => $element_name,
                    ]);
                    $msg = 'The selected pay element has been updated.';
                } else {
                    DB::table('pay_element')->insert([
                        'element_name' => $element_name,
                        'account_code' => $account_id,
                    ]);
                    $msg = 'Pay element has been added.';
                }

                $selected_id = '';
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            $elem = DB::table('pay_element')->where('element_id', $delete_id)->first();
            if ($elem) {
                $used = DB::table('salary_structure')->where('pay_rule_id', $elem->account_code)->exists();
                if ($used) {
                    $error = 'Cannot delete this account because payroll rules have been created using it.';
                } else {
                    DB::table('pay_element')->where('element_id', $delete_id)->delete();
                    $msg = 'Selected account has been deleted';
                }
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        $elements = DB::table('pay_element')
            ->leftJoin('accounts', 'pay_element.account_code', '=', 'accounts.code')
            ->orderBy('pay_element.element_id')
            ->select('pay_element.*', 'accounts.name as account_name')
            ->get();

        $selected_element = null;
        if ($selected_id) {
            $selected_element = DB::table('pay_element')
                ->leftJoin('accounts', 'pay_element.account_code', '=', 'accounts.code')
                ->where('pay_element.element_id', $selected_id)
                ->select('pay_element.*', 'accounts.name as account_name')
                ->first();
        }

        $all_accounts = Account::where('company_id', $company_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['code', 'name', 'account_type']);

        return view('hr.pay-elements', compact(
            'msg', 'error', 'elements', 'selected_id', 'selected_element',
            'all_accounts'
        ));
    }

    public function payElementsAllocation(Request $request): View
    {
        $msg = null;
        $error = null;
        $company_id = 1;

        $position_id = $request->input('PositionId', '');
        $show_inactive = $request->boolean('show_inactive');
        $position_count = DB::table('positions')->count();

        // Handle Save/Update
        if ($request->has('submit')) {
            if ($position_id) {
                $payrule = [];

                foreach ($request->all() as $key => $val) {
                    if (strpos($key, 'Payroll') === 0) {
                        $a = substr($key, 7);
                        if ($val == 1 || DB::table('salary_structure')
                            ->where('position_id', $position_id)
                            ->where('pay_rule_id', $a)
                            ->exists()) {
                            $payrule[] = $a;
                        }
                    }
                }

                $exists = DB::table('payroll_structure')->where('position_id', $position_id)->exists();

                if ($exists && count($payrule) > 0) {
                    DB::table('payroll_structure')->where('position_id', $position_id)->update([
                        'payroll_rule' => implode(';', $payrule),
                    ]);
                } elseif (count($payrule) == 0) {
                    DB::table('payroll_structure')->where('position_id', $position_id)->delete();
                } elseif (!$exists && count($payrule) > 0) {
                    DB::table('payroll_structure')->insert([
                        'position_id' => $position_id,
                        'payroll_rule' => implode(';', $payrule),
                    ]);
                }

                $msg = 'Accounts have been updated, some accounts might not have been deleted because Salary Structure using them.';
            } else {
                $error = 'Select Job Position first.';
            }
        }

        // Handle Delete
        if ($request->has('delete')) {
            if ($position_id) {
                DB::table('payroll_structure')->where('position_id', $position_id)->delete();
                $msg = 'Selected payroll rules have been deleted.';
                $position_id = '';
            }
        }

        // Get selected position's existing rules
        $existing_rules = [];
        if ($position_id) {
            $ps = DB::table('payroll_structure')->where('position_id', $position_id)->first();
            if ($ps) {
                $existing_rules = explode(';', $ps->payroll_rule);
            }
        }

        // Get all pay elements
        $rules = DB::table('pay_element')
            ->leftJoin('accounts', 'pay_element.account_code', '=', 'accounts.code')
            ->orderBy('pay_element.element_id')
            ->select('pay_element.*', 'accounts.name as account_name')
            ->get();

        // Get all positions for dropdown
        $positions = DB::table('positions')
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('position_id')
            ->get(['position_id', 'position_name']);

        $has_rules = $position_id && DB::table('payroll_structure')->where('position_id', $position_id)->exists();

        return view('hr.pay-elements-allocation', compact(
            'msg', 'error', 'positions', 'position_id', 'show_inactive',
            'rules', 'existing_rules', 'has_rules', 'position_count'
        ));
    }

    public function employees(Request $request): View
    {
        $msg = null;
        $error = null;
        $company_id = 1;
        $tab = $request->input('_tabs_sel', 'list');

        // Handle employee name/ID click from list - set session EmpId
        $emp_clicked = false;
        foreach ($request->all() as $key => $value) {
            if (is_numeric($key) && $value == 1) {
                session(['EmpId' => $key]);
                $tab = 'add';
                $emp_clicked = true;
            }
        }

        // Switching to add tab without clicking an employee → clear session for empty form
        if ($tab == 'add' && !$emp_clicked && !$request->has('addupdate') && !$request->has('delete')) {
            session()->forget('EmpId');
        }

        $cur_id = session('EmpId', '');

        // Handle image upload
        $upload_error = null;
        if ($request->hasFile('pic') && $request->file('pic')->isValid()) {
            $file = $request->file('pic');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $upload_error = 'Only graphics files can be uploaded';
            } elseif ($file->getSize() > 2048 * 1024) {
                $upload_error = 'The file size is over the maximum allowed (2MB).';
            } else {
                $img_name = $cur_id ? $cur_id : 'new_' . time();
                $file->storeAs('public/employee_photos', $img_name . '.' . $ext);
            }
        }

        // Handle Delete Image checkbox
        $del_image = $request->boolean('del_image');

        // Handle Save (addupdate)
        if ($request->has('addupdate')) {
            $input_error = 0;

            $first_name = trim($request->input('emp_first_name', ''));
            $last_name = trim($request->input('emp_last_name', ''));
            $gender = $request->input('gender', 1);
            $address = $request->input('emp_address', '');
            $mobile = $request->input('emp_mobile', '');
            $email = $request->input('emp_email', '');
            $birth_date = $request->input('emp_birthdate', '');
            $national_id = $request->input('national_id', '');
            $passport = $request->input('passport', '');
            $bank_account = $request->input('bank_account', '');
            $tax_number = $request->input('tax_number', '');
            $notes = $request->input('emp_notes', '');
            $hire_date = $request->input('emp_hiredate', '');
            $department_id = $request->input('department_id', '');
            $position_id = $request->input('position_id', '');
            $grade_id = $request->input('grade_id', '');
            $personal_salary = $request->boolean('personal_salary');
            $release_date = $request->input('emp_releasedate', '');
            $inactive = $request->boolean('inactive');

            if (empty($first_name)) {
                $error = 'The employee first name must be entered.';
                $input_error = 1;
            } elseif (empty($last_name)) {
                $error = 'Employee last name must be entered.';
                $input_error = 1;
            } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email.';
                $input_error = 1;
            } elseif (empty($birth_date) || !strtotime($birth_date)) {
                $error = 'Invalid birth date.';
                $input_error = 1;
            } elseif (!empty($hire_date) && !strtotime($hire_date)) {
                $error = 'Invalid hire date.';
                $input_error = 1;
            } elseif (!empty($hire_date) && !empty($birth_date) && strtotime($hire_date) < strtotime($birth_date)) {
                $error = 'Hire date can not be before Birth date.';
                $input_error = 1;
            } elseif ($personal_salary && (!is_numeric($request->input('basic_amt')) || $request->input('basic_amt') <= 0)) {
                $error = 'Basic salary amount must be a positive number';
                $input_error = 1;
            } elseif ($personal_salary && empty($position_id)) {
                $error = "Staff's Job Position must be selected to use Personal Salary Structure";
                $input_error = 1;
            } elseif ($inactive && (empty($release_date) || !strtotime($release_date))) {
                $error = 'Invalid release date.';
                $input_error = 1;
            }

            if (!$input_error) {
                $emp_data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'gender' => $gender,
                    'address' => $address,
                    'phone' => $mobile,
                    'email' => $email,
                    'birth_date' => $birth_date ? date('Y-m-d', strtotime($birth_date)) : null,
                    'national_id' => $national_id,
                    'passport' => $passport,
                    'bank_account' => $bank_account,
                    'tax_id' => $tax_number,
                    'notes' => $notes,
                    'hire_date' => $hire_date ? date('Y-m-d', strtotime($hire_date)) : null,
                    'department_id' => $department_id ?: null,
                    'position_id' => $position_id ?: null,
                    'grade_id' => $grade_id ?: null,
                    'personal_salary' => $personal_salary,
                    'termination_date' => $release_date ? date('Y-m-d', strtotime($release_date)) : null,
                    'is_active' => !$inactive,
                ];

                if ($cur_id) {
                    DB::table('employees')->where('id', $cur_id)->update($emp_data);
                } else {
                    $emp_data['company_id'] = $company_id;
                    $emp_data['employee_number'] = 'EMP-' . strtoupper(substr(uniqid(), -8));
                    $emp_data['position'] = DB::table('positions')->where('position_id', $position_id)->value('position_name') ?? '';
                    $emp_data['employment_type'] = 'full_time';
                    $emp_data['created_by'] = auth()->id() ?? 1;
                    $cur_id = DB::table('employees')->insertGetId($emp_data);
                    session(['EmpId' => $cur_id]);
                }

                // Handle personal salary structure
                if ($personal_salary) {
                    $basic = DB::table('positions')
                        ->leftJoin('salary_structure', function ($j) {
                            $j->on('positions.position_id', '=', 'salary_structure.position_id')
                                ->where('salary_structure.grade_id', '=', 0)
                                ->where('salary_structure.is_basic', '=', true);
                        })
                        ->where('positions.position_id', $position_id)
                        ->select('salary_structure.pay_rule_id')
                        ->first();

                    $basic_acc = $basic->pay_rule_id ?? '';
                    $basic_amt = (float)$request->input('basic_amt', 0);

                    // Delete existing personal salary structure for this employee
                    DB::table('personal_salary_structure')->where('emp_id', $cur_id)->delete();

                    // Insert basic salary
                    DB::table('personal_salary_structure')->insert([
                        'date' => now()->toDateString(),
                        'emp_id' => $cur_id,
                        'pay_rule_id' => $basic_acc,
                        'pay_amount' => $basic_amt,
                        'type' => 1,
                        'is_basic' => true,
                    ]);

                    // Insert other pay elements
                    foreach ($request->all() as $key => $val) {
                        if (strpos($key, 'amt_') === 0) {
                            $rule_id = substr($key, 4);
                            $amt_val = (float)$val;
                            if ($amt_val != 0) {
                                DB::table('personal_salary_structure')->insert([
                                    'date' => now()->toDateString(),
                                    'emp_id' => $cur_id,
                                    'pay_rule_id' => $rule_id,
                                    'pay_amount' => $amt_val,
                                    'type' => $amt_val > 0 ? 1 : 0,
                                    'is_basic' => false,
                                ]);
                            }
                        }
                    }
                }

                // Handle image deletion
                if ($del_image) {
                    Storage::delete('public/employee_photos/' . $cur_id . '.jpg');
                }

                $msg = $cur_id ? 'Employee details has been updated.' : 'A new employee has been added.';
            }
        }

        // Handle Delete
        if ($request->has('delete')) {
            $emp = DB::table('employees')->where('id', $cur_id)->first();
            if ($emp && $emp->hire_date && $emp->hire_date != '0000-00-00') {
                $error = 'Employed person cannot be deleted.';
            } else {
                DB::table('personal_salary_structure')->where('emp_id', $cur_id)->delete();
                DB::table('employees')->where('id', $cur_id)->delete();
                $msg = 'Employee details has been deleted.';
                session()->forget('EmpId');
                $cur_id = '';
                $tab = 'add';
            }
        }

        // Fetch employee list
        $search_string = $request->input('string', '');
        $dept_filter = $request->input('DeptId', '');
        $position_filter = $request->input('position', '');
        $grade_filter = $request->input('grade', '');
        $show_inactive = $request->boolean('show_inactive');

        $employees_query = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.dept_id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.position_id')
            ->when(!$show_inactive, fn($q) => $q->where('employees.is_active', true))
            ->when($dept_filter, fn($q) => $q->where('employees.department_id', $dept_filter))
            ->when($position_filter, fn($q) => $q->where('employees.position_id', $position_filter))
            ->when($grade_filter !== '' && $grade_filter !== null, fn($q) => $q->where('employees.grade_id', $grade_filter))
            ->when($search_string, fn($q) => $q->where(function ($q) use ($search_string) {
                $q->where('employees.first_name', 'like', "%{$search_string}%")
                  ->orWhere('employees.last_name', 'like', "%{$search_string}%")
                  ->orWhere('employees.email', 'like', "%{$search_string}%")
                  ->orWhere('employees.phone', 'like', "%{$search_string}%");
            }))
            ->orderBy('employees.id')
            ->select('employees.*', 'departments.dept_name', 'positions.position_name');

        $employees = $employees_query->paginate(50);

        // Current employee for editing
        $employee = null;
        if ($cur_id) {
            $employee = DB::table('employees')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.dept_id')
                ->leftJoin('positions', 'employees.position_id', '=', 'positions.position_id')
                ->where('employees.id', $cur_id)
                ->select('employees.*', 'departments.dept_name', 'positions.position_name')
                ->first();
        }

        // Load personal salary structure
        $personal_salaries = collect();
        if ($cur_id) {
            $personal_salaries = DB::table('personal_salary_structure')
                ->where('emp_id', $cur_id)
                ->get()
                ->keyBy('pay_rule_id');
        }

        // Reference data
        $departments = DB::table('departments')
            ->orderBy('dept_id')
            ->get(['dept_id', 'dept_name']);

        $positions_list = DB::table('positions')
            ->orderBy('position_id')
            ->get(['position_id', 'position_name']);

        $grades_no = (int)Setting::getSetting('payroll_grades', $company_id, 0);

        $pay_elements = DB::table('pay_element')
            ->leftJoin('accounts', 'pay_element.account_code', '=', 'accounts.code')
            ->orderBy('pay_element.element_id')
            ->select('pay_element.*', 'accounts.name as account_name')
            ->get();

        $avatar_path = 'storage/employee_photos';

        return view('hr.employees.index', compact(
            'msg', 'error', 'tab', 'cur_id', 'employee',
            'employees', 'search_string', 'dept_filter', 'position_filter', 'grade_filter',
            'show_inactive', 'departments', 'positions_list', 'grades_no',
            'pay_elements', 'personal_salaries', 'avatar_path', 'upload_error', 'del_image'
        ));
    }

    public function overtimeRates(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = null;
        $show_inactive = $request->boolean('show_inactive');

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->input('toggle_inactive');
            $ot = DB::table('overtime')->where('overtime_id', $toggle_id)->first();
            if ($ot) {
                DB::table('overtime')->where('overtime_id', $toggle_id)->update([
                    'inactive' => !$ot->inactive,
                ]);
                $msg = $ot->inactive ? 'Overtime activated' : 'Overtime deactivated';
            }
        }

        // Handle ADD/UPDATE
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $rate = trim($request->input('rate', ''));
            $name = trim($request->input('name', ''));
            $update_id = $request->input('selected_id');

            if (empty($rate)) {
                $error = 'The overtime rate cannot be empty.';
                $input_error = 1;
            } elseif (!is_numeric($rate)) {
                $error = 'Overtime rate must be a number.';
                $input_error = 1;
            } elseif (empty($name)) {
                $error = 'The overtime name cannot be empty.';
                $input_error = 1;
            }

            if (!$input_error) {
                $data = [
                    'overtime_name' => $name,
                    'overtime_rate' => (float)$rate,
                ];

                if ($request->has('UPDATE_ITEM') && $update_id) {
                    DB::table('overtime')->where('overtime_id', $update_id)->update($data);
                    $msg = 'Selected overtime has been updated';
                } else {
                    DB::table('overtime')->insert($data);
                    $msg = 'New overtime item has been added';
                }

                $selected_id = null;
            }

            if ($input_error && $update_id) {
                $selected_id = $update_id;
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            if (DB::table('attendance')->where('overtime_id', $delete_id)->exists()) {
                $error = 'This overtime cannot be deleted.';
            } else {
                DB::table('overtime')->where('overtime_id', $delete_id)->delete();
                $msg = 'Selected overtime item has been deleted';
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        $rates = DB::table('overtime')
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('overtime_id')
            ->get();

        $selected_rate = null;
        if ($selected_id) {
            $selected_rate = DB::table('overtime')->where('overtime_id', $selected_id)->first();
        }

        return view('hr.overtime', compact(
            'msg', 'error', 'rates', 'selected_id', 'selected_rate', 'show_inactive'
        ));
    }

    public function leaveTypes(Request $request): View
    {
        $msg = null;
        $error = null;
        $selected_id = -1;
        $show_inactive = $request->boolean('show_inactive');

        $edit_id = $this->findSubmit('Edit', $request);
        $delete_id = $this->findSubmit('Delete', $request);

        // Handle toggle inactive
        if ($request->has('toggle_inactive')) {
            $toggle_id = $request->input('toggle_inactive');
            $lt = DB::table('leave_type')->where('leave_id', $toggle_id)->first();
            if ($lt) {
                DB::table('leave_type')->where('leave_id', $toggle_id)->update([
                    'inactive' => !$lt->inactive,
                ]);
                $msg = $lt->inactive ? 'Leave type activated' : 'Leave type deactivated';
            }
        }

        // Handle ADD/UPDATE
        if ($request->has('ADD_ITEM') || $request->has('UPDATE_ITEM')) {
            $input_error = 0;
            $pay_rate = $request->input('pay_rate', '');
            $leave_name = trim($request->input('leave_name', ''));
            $leave_code = trim($request->input('leave_code', ''));
            $update_id = $request->input('selected_id', -1);

            if (!is_numeric($pay_rate)) {
                $error = 'Salary rate must be a number.';
                $input_error = 1;
            } elseif (empty($leave_name)) {
                $error = 'The Leave type name cannot be empty.';
                $input_error = 1;
            } elseif (empty($leave_code) || !preg_match('/^[a-zA-Z]+$/', $leave_code)) {
                $error = 'The Leave type code cannot be empty and only allows alphabet letters.';
                $input_error = 1;
            }

            if (!$input_error) {
                $data = [
                    'leave_name' => $leave_name,
                    'leave_code' => $leave_code,
                    'pay_rate' => (float)$pay_rate,
                ];

                if ($request->has('UPDATE_ITEM') && $update_id && $update_id !== -1) {
                    DB::table('leave_type')->where('leave_id', $update_id)->update($data);
                    $msg = 'Selected leave type has been updated';
                } else {
                    DB::table('leave_type')->insert($data);
                    $msg = 'New leave type has been added';
                }

                $selected_id = -1;
            }

            if ($input_error && $update_id && $update_id !== -1) {
                $selected_id = $update_id;
            }
        }

        // Handle Delete
        if ($delete_id !== null) {
            if (DB::table('leave')->where('leave_id', $delete_id)->exists()) {
                $error = 'This leave type cannot be deleted.';
            } else {
                DB::table('leave_type')->where('leave_id', $delete_id)->delete();
                $msg = 'Selected leave type has been deleted';
            }
        }

        // Handle Edit
        if ($edit_id !== null) {
            $selected_id = $edit_id;
        }

        $leave_types = DB::table('leave_type')
            ->when(!$show_inactive, fn($q) => $q->where('inactive', false))
            ->orderBy('leave_id')
            ->get();

        $selected_leave_type = null;
        if ($selected_id !== -1) {
            $selected_leave_type = DB::table('leave_type')->where('leave_id', $selected_id)->first();
        }

        return view('hr.leave-types', compact(
            'msg', 'error', 'leave_types', 'selected_id', 'selected_leave_type', 'show_inactive'
        ));
    }

    public function salaryStructure(Request $request): View
    {
        $msg = null;
        $error = null;
        $company_id = 1;

        $position_id = $request->input('position_id', '');
        $active_tab = (int)$request->input('_tabs_sel', 0);
        $grade_id = $active_tab;

        // Handle Save/Update
        if ($request->has('submit')) {
            if (!$position_id) {
                $error = 'Select job position';
            } else {
                $input_error = false;
                foreach ($request->all() as $key => $val) {
                    if (str_starts_with($key, 'Account')) {
                        $code = $val;
                        $debit = (float)$request->input('Debit' . $code, 0);
                        $credit = (float)$request->input('Credit' . $code, 0);
                        if ($debit > 0 && $credit > 0) {
                            $error = 'Only one amount(Earning or Deduction) is allowed per rule';
                            $input_error = true;
                            break;
                        }
                    }
                }

                if (!$input_error) {
                    $payroll_rules = [];
                    foreach ($request->all() as $key => $val) {
                        if (str_starts_with($key, 'Account')) {
                            $code = $val;
                            $debit = (float)$request->input('Debit' . $code, 0);
                            $credit = (float)$request->input('Credit' . $code, 0);

                            if ($debit > 0) {
                                $type = 1;
                                $amount = $debit;
                            } else {
                                $type = 0;
                                $amount = $credit;
                            }

                            if ($amount > 0) {
                                $payroll_rules[] = [
                                    'position_id' => $position_id,
                                    'grade_id' => $grade_id,
                                    'pay_rule_id' => $code,
                                    'pay_amount' => $amount,
                                    'type' => $type,
                                ];
                            }
                        }
                    }

                    DB::table('salary_structure')
                        ->where('position_id', $position_id)
                        ->where('grade_id', $grade_id)
                        ->delete();

                    foreach ($payroll_rules as $rule) {
                        DB::table('salary_structure')->insert($rule + [
                            'date' => now()->toDateString(),
                            'is_basic' => false,
                        ]);
                    }

                    $msg = 'Salary structure has been updated.';
                }
            }
        }

        // Handle Delete
        if ($request->has('delete')) {
            if ($position_id) {
                DB::table('salary_structure')->where('position_id', $position_id)->delete();
                $msg = 'Selected structure has been deleted.';
                $position_id = '';
                $grade_id = 0;
                $active_tab = 0;
            }
        }

        // Get positions
        $positions = DB::table('positions')
            ->orderBy('position_name')
            ->get(['position_id', 'position_name', 'pay_basis']);

        $grades_count = (int)Setting::getSetting('payroll_grades', $company_id, 0);

        // Get position details
        $pay_basis = 0;
        if ($position_id) {
            $pos = DB::table('positions')->where('position_id', $position_id)->first();
            if ($pos) {
                $pay_basis = $pos->pay_basis;
            }
        }

        // Get payroll structure (allocated pay elements) for this position
        $existing_rules = [];
        $elements = collect();
        if ($position_id) {
            $ps = DB::table('payroll_structure')->where('position_id', $position_id)->first();
            if ($ps && $ps->payroll_rule) {
                $existing_rules = explode(';', $ps->payroll_rule);
            }
        }

        // Get pay elements details
        if (!empty($existing_rules)) {
            $elements = DB::table('pay_element')
                ->leftJoin('accounts', 'pay_element.account_code', '=', 'accounts.code')
                ->whereIn('pay_element.account_code', $existing_rules)
                ->orderBy('pay_element.element_id')
                ->select('pay_element.*', 'accounts.name as account_name')
                ->get();
        }

        // Get basic salary
        $basic_salary = null;
        if ($position_id) {
            $basic_salary = DB::table('salary_structure')
                ->where('position_id', $position_id)
                ->where('grade_id', 0)
                ->where('is_basic', true)
                ->first();
        }

        // Get existing salary structure for this position+grade
        $existing_salary = collect();
        if ($position_id) {
            $existing_salary = DB::table('salary_structure')
                ->where('position_id', $position_id)
                ->where('grade_id', $grade_id)
                ->get()
                ->keyBy('pay_rule_id');
        }

        // Check if grade exists
        $grade_exists = $grade_id == 0;
        if ($grade_id > 0 && $position_id) {
            $grade_exists = DB::table('grade_table')
                ->where('position_id', $position_id)
                ->where('grade_id', $grade_id)
                ->exists();
        }

        $has_positions = DB::table('positions')->count() > 0;
        $has_elements = !empty($existing_rules);
        $has_existing_data = DB::table('salary_structure')
            ->where('position_id', $position_id)
            ->where('grade_id', $grade_id)
            ->exists();

        return view('hr.salary-structure', compact(
            'msg', 'error', 'position_id', 'positions', 'grades_count',
            'active_tab', 'grade_id', 'pay_basis', 'elements',
            'basic_salary', 'existing_salary', 'grade_exists',
            'has_positions', 'has_elements', 'has_existing_data'
        ));
    }

    public function attendance(Request $request): View
    {
        $msg = null;
        $error = null;
        $company_id = 1;

        $from_date = $request->input('from_date', date('d/m/Y'));
        $to_date = $request->input('to_date', date('d/m/Y'));
        $dept_id = $request->input('DeptId', '');

        $Work_hours = Setting::getSetting('work_hours', $company_id, 8);
        $weekend_day = (int)Setting::getSetting('weekend_day', $company_id, 0);

        $employees = DB::table('employees')
            ->select('id', 'first_name', 'last_name')
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS name")
            ->when($dept_id, fn($q) => $q->where('department_id', $dept_id))
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $overtimes = DB::table('overtime')
            ->where('inactive', false)
            ->orderBy('overtime_id')
            ->get();

        $leave_types = DB::table('leave_type')
            ->where('inactive', false)
            ->orderBy('leave_id')
            ->get();

        $departments = DB::table('departments')
            ->orderBy('dept_id')
            ->get(['dept_id', 'dept_name']);

        $has_employee = DB::table('employees')->where('is_active', true)->exists();

        $emp_ids = $employees->pluck('id')->toArray();

        if ($request->has('bulk')) {
            foreach ($emp_ids as $eid) {
                if ($request->integer((string)$eid) == 1)
                    $request->merge([$eid . '-0' => $Work_hours]);
                else
                    $request->merge([$eid . '-0' => '']);
            }
        }

        if ($request->has('addatt')) {
            if (!$this->canProcessAttendance($request, $employees, $overtimes)) {
                $error = session('_att_error');
            } else {
                $att_items = 0;
                foreach ($emp_ids as $eid) {
                    $reg = $request->input($eid . '-0', '');
                    $lev = $request->input($eid . '-leave', '');

                    if ($reg && $this->checkPaidInRange($eid, $from_date, $to_date)) {
                        $error = 'The selected date range includes a date that has been approved, please select another date range.';
                        break;
                    } elseif (!empty($lev)) {
                        $leave_rate = DB::table('leave_type')->where('leave_id', $lev)->value('pay_rate') ?? 0;
                        $att_items++;
                        $this->writeAttendanceRange($eid, 0, 0, $leave_rate, $from_date, $to_date, $lev);
                    } else {
                        if (strlen($reg) > 0)
                            $att_items++;
                        $this->writeAttendanceRange($eid, 0, $this->timeToFloat($reg), 1, $from_date, $to_date);
                        foreach ($overtimes as $ot) {
                            $v = $request->input($eid . '-' . $ot->overtime_id, '');
                            if (strlen($v) > 0)
                                $att_items++;
                            $this->writeAttendanceRange($eid, $ot->overtime_id, $this->timeToFloat($v), $ot->overtime_rate, $from_date, $to_date);
                        }
                    }
                }
                if (!$error)
                    $msg = $att_items > 0 ? 'Attendance has been saved.' : 'Nothing added';
            }
        }

        return view('hr.attendance', compact(
            'msg', 'error', 'from_date', 'to_date', 'dept_id',
            'employees', 'overtimes', 'leave_types', 'departments',
            'has_employee'
        ));
    }

    private function canProcessAttendance(Request $request, $employees, $overtimes): bool
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $from_date) || !strtotime(str_replace('/', '-', $from_date))) {
            session()->flash('_att_error', 'The entered date is invalid.');
            return false;
        }
        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $to_date) || !strtotime(str_replace('/', '-', $to_date))) {
            session()->flash('_att_error', 'The entered date is invalid.');
            return false;
        }
        if (strtotime(str_replace('/', '-', $from_date)) > strtotime('today 23:59:59')) {
            session()->flash('_att_error', 'Cannot make attendance for the date in the future.');
            return false;
        }
        if (strtotime(str_replace('/', '-', $to_date)) > strtotime('today 23:59:59')) {
            session()->flash('_att_error', 'Cannot make attendance for the date in the future.');
            return false;
        }

        $err = 'Attendance input data must be greater than 0, less than 24 hours and formatted in <b>HH:MM</b> or <b>Integer</b>, example - 02:25 , 2:25, 8, 23:59 ...';
        foreach ($employees as $emp) {
            $reg = $request->input($emp->id . '-0', '');
            $lev = $request->input($emp->id . '-leave', '');
            if (strlen($reg) != 0 && empty($lev)) {
                if (!preg_match('/^(?(?=\d{2})(?:2[0-3]|[01][0-9])|[0-9]):[0-5][0-9]$/', $reg)
                    && (!is_numeric($reg) || $reg >= 24 || $reg <= 0)) {
                    session()->flash('_att_error', $err);
                    return false;
                }
            }
            if (empty($lev)) {
                foreach ($overtimes as $ot) {
                    $v = $request->input($emp->id . '-' . $ot->overtime_id, '');
                    if (strlen($v) != 0) {
                        if (!preg_match('/^(?(?=\d{2})(?:2[0-3]|[01][0-9])|[0-9]):[0-5][0-9]$/', $v)
                            && (!is_numeric($v) || $v >= 24 || $v <= 0)) {
                            session()->flash('_att_error', $err);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    private function writeAttendanceRange($emp_id, $time_type, $value, $rate, $from, $to, $leave_id = false)
    {
        $from_ts = strtotime(str_replace('/', '-', $from));
        $to_ts = strtotime(str_replace('/', '-', $to));

        $begin = new DateTime(date('Y-m-d', $from_ts));
        $end = (new DateTime(date('Y-m-d', $to_ts)))->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($begin, $interval, $end);

        $weekend_day = (int)Setting::getSetting('weekend_day', 1, 0);

        foreach ($period as $dt) {
            $day = $dt->format('Y-m-d');
            if ($weekend_day == 0 || $dt->format('N') != $weekend_day) {
                if ($leave_id) {
                    DB::table('employee_attendance')->where('emp_id', $emp_id)->where('att_date', $day)->delete();
                    DB::table('employee_leave')->updateOrInsert(
                        ['emp_id' => $emp_id, 'date' => $day],
                        ['leave_id' => $leave_id, 'pay_rate' => $rate]
                    );
                } else {
                    DB::table('employee_leave')->where('emp_id', $emp_id)->where('date', $day)->delete();
                    DB::table('employee_attendance')->updateOrInsert(
                        ['emp_id' => $emp_id, 'overtime_id' => $time_type, 'att_date' => $day],
                        ['hours' => $value, 'rate' => $rate]
                    );
                }
            }
        }
    }

    private function checkPaidInRange($emp_id, $from, $to): bool
    {
        if (!Schema::hasTable('payslip'))
            return false;

        $from_ts = strtotime(str_replace('/', '-', $from));
        $to_ts = strtotime(str_replace('/', '-', $to));

        $begin = new DateTime(date('Y-m-d', $from_ts));
        $end = (new DateTime(date('Y-m-d', $to_ts)))->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $day = $dt->format('Y-m-d');
            if (DB::table('payslip')->where('emp_id', $emp_id)->where('from_date', '<=', $day)->where('to_date', '>=', $day)->exists())
                return true;
        }
        return false;
    }

    public function payslipEntry(Request $request): View|RedirectResponse
    {
        $company_id = 1;
        $msg = session('_payslip_msg');
        $error = null;
        $added_trans_no = null;
        $added_payslip_no = null;

        if ($request->has('AddedID')) {
            $trans_no = $request->input('AddedID');
            $payslip = DB::table('payslip')->where('trans_no', $trans_no)->first();
            if ($payslip) {
                $msg = 'Payslip #' . $payslip->payslip_no . ' has been entered';
                $added_trans_no = $trans_no;
                $added_payslip_no = $payslip->payslip_no;
            }
        }

        $payslip_cart = session('payslip_items');
        if (!$payslip_cart) {
            $next_no = DB::table('payslip')->max('payslip_no') + 1 ?: 1;
            $payslip_cart = [
                'order_id'     => 0,
                'reference'    => '',
                'tran_date'    => date('Y-m-d'),
                'from_date'    => '',
                'to_date'      => '',
                'person_id'    => '',
                'payslip_no'   => $next_no,
                'memo_'        => '',
                'gl_items'     => [],
                'pay_basis'    => '',
                'leaves'       => 0,
                'deductable_leaves' => 0,
                'work_days'    => 0,
                'payable_amt'  => 0,
                'salary_amt'   => 0,
                'allowance'    => [],
                'overtime_amt' => 0,
                'empty_payment'=> false,
            ];
            session(['payslip_items' => $payslip_cart]);
        }

        $employees = DB::table('employees')
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name']);

        $employees_list = $employees->mapWithKeys(fn($e) => [$e->id => $e->first_name . ' ' . $e->last_name])->toArray();

        $accounts = DB::table('accounts')
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $use_dimension = 0;
        $dimensions = [];

        // Generate Payslip
        if ($request->has('GeneratePayslip')) {
            $error = $this->validatePayslipGeneration($request, $employees);
            if (!$error) {
                $this->generatePayslipGlItems($payslip_cart, $request);
                $payslip_cart['person_id'] = $request->input('person_id');
                $payslip_cart['from_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
                $payslip_cart['to_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
                $payslip_cart['tran_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('date_'))->format('Y-m-d');
                $payslip_cart['reference'] = $request->input('ref');
                $payslip_cart['memo_'] = $request->input('memo_', '');
                session(['payslip_items' => $payslip_cart]);
            }
        }

        // Process
        if ($request->has('Process')) {
            $input_date = $request->input('date_');
            if ($input_date && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input_date)) {
                $payslip_cart['tran_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input_date)->format('Y-m-d');
            }
            $input_from = $request->input('from_date');
            if ($input_from && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input_from)) {
                $payslip_cart['from_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input_from)->format('Y-m-d');
            }
            $input_to = $request->input('to_date');
            if ($input_to && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input_to)) {
                $payslip_cart['to_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input_to)->format('Y-m-d');
            }
            $payslip_cart['reference'] = $request->input('ref', $payslip_cart['reference']);
            $payslip_cart['memo_'] = $request->input('memo_', $payslip_cart['memo_']);
            $payslip_cart['person_id'] = $request->input('person_id', $payslip_cart['person_id']);

            if (count($payslip_cart['gl_items']) < 1) {
                $error = 'You must enter at least one journal line.';
            } else {
                $total = array_sum(array_column($payslip_cart['gl_items'], 'amount'));
                if (abs($total) > 0.001) {
                    $error = 'The journal must balance (debits equal to credits) before it can be processed.';
                } elseif (empty($payslip_cart['person_id'])) {
                    $error = 'Employee not selected';
                } elseif ($payslip_cart['empty_payment']) {
                    $error = 'Employee cannot getting paid for non-working period.';
                } else {
                    // Write journal entry
                    $entry_date = $payslip_cart['tran_date'] ?: now()->format('Y-m-d');
                    $entry_number = $payslip_cart['reference'] ?: 'PS-' . date('Ymd') . '-' . uniqid();
                    $jeId = DB::table('journal_entries')->insertGetId([
                        'company_id' => $company_id,
                        'entry_number' => $entry_number,
                        'entry_date' => $entry_date,
                        'reference_type' => 'payslip',
                        'reference_id' => null,
                        'description' => 'Payslip - ' . ($employees_list[$payslip_cart['person_id']] ?? ''),
                        'total_debit' => array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $payslip_cart['gl_items'])),
                        'total_credit' => array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $payslip_cart['gl_items'])),
                        'is_posted' => true,
                        'posted_at' => now(),
                        'posted_by' => auth()->id() ?? 1,
                        'created_by' => auth()->id() ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    foreach ($payslip_cart['gl_items'] as $item) {
                        $account = DB::table('accounts')->where('code', $item['code_id'])->first();
                        if ($account) {
                            $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                            $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                            DB::table('journal_entry_lines')->insert([
                                'journal_entry_id' => $jeId,
                                'account_id' => $account->id,
                                'description' => $item['memo'] ?? '',
                                'debit_amount' => $debit,
                                'credit_amount' => $credit,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    // Write payslip record
                    $payslip_no = DB::table('payslip')->max('payslip_no') + 1 ?: 1;
                    DB::table('payslip')->insert([
                        'payslip_no' => $payslip_no,
                        'trans_no' => $jeId,
                        'emp_id' => $payslip_cart['person_id'],
                        'generated_date' => now()->format('Y-m-d'),
                        'from_date' => $payslip_cart['from_date'],
                        'to_date' => $payslip_cart['to_date'],
                        'leaves' => $payslip_cart['leaves'],
                        'deductable_leaves' => $payslip_cart['deductable_leaves'],
                        'payable_amount' => $payslip_cart['payable_amt'],
                        'salary_amount' => $payslip_cart['salary_amt'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Write payslip_details
                    foreach ($payslip_cart['allowance'] as $acc_code => $amt) {
                        DB::table('payslip_details')->insert([
                            'payslip_no' => $payslip_no,
                            'account_code' => $acc_code,
                            'amount' => $amt,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    session(['payslip_items' => null]);
                    return redirect()->route('hr.payslips', ['AddedID' => $jeId]);
                }
            }
        }

        // Cancel Order
        if ($request->has('CancelOrder')) {
            $next_no = DB::table('payslip')->max('payslip_no') + 1 ?: 1;
            $payslip_cart['gl_items'] = [];
            $payslip_cart['leaves'] = 0;
            $payslip_cart['deductable_leaves'] = 0;
            $payslip_cart['work_days'] = 0;
            $payslip_cart['payable_amt'] = 0;
            $payslip_cart['salary_amt'] = 0;
            $payslip_cart['allowance'] = [];
            $payslip_cart['overtime_amt'] = 0;
            $payslip_cart['empty_payment'] = false;
            $payslip_cart['payslip_no'] = $next_no;
            $payslip_cart['reference'] = '';
            $payslip_cart['from_date'] = '';
            $payslip_cart['to_date'] = '';
            $payslip_cart['person_id'] = '';
            $payslip_cart['tran_date'] = date('Y-m-d');
            $payslip_cart['memo_'] = '';
            session(['payslip_items' => $payslip_cart]);
        }

        // GL item CRUD
        $cart = &$payslip_cart;

        $delete_id = request('Delete');
        if ($delete_id !== null && isset($cart['gl_items'][$delete_id])) {
            unset($cart['gl_items'][$delete_id]);
            $cart['gl_items'] = array_values($cart['gl_items']);
        }

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
                    'description' => '',
                    'amount' => $amount,
                    'memo' => request('LineMemo', ''),
                ];
            }
        }

        if (request('UpdateItem')) {
            $edit_idx = session('payslip_edit_index');
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
                        'description' => '',
                        'amount' => $amount,
                        'memo' => request('LineMemo', ''),
                    ];
                }
                session(['payslip_edit_index' => null]);
            }
        }

        if (request('CancelItemChanges')) {
            session(['payslip_edit_index' => null]);
        }

        $edit_id = request('Edit');
        if ($edit_id !== null && isset($cart['gl_items'][$edit_id])) {
            session(['payslip_edit_index' => $edit_id]);
        }
        $edit_index = session('payslip_edit_index');

        session(['payslip_items' => $cart]);

        $total_debit = array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $cart['gl_items'] ?? []));
        $total_credit = array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $cart['gl_items'] ?? []));

        $next_payslip_no = $cart['payslip_no'];
        $selected_emp = $cart['person_id'] ? $employees_list[$cart['person_id']] ?? '' : '';
        $pay_basis_label = $cart['pay_basis'] === 0 ? 'Monthly salary' : ($cart['pay_basis'] === 1 ? 'Daily wages' : '');

        $overtimes = DB::table('overtime')->where('inactive', false)->orderBy('overtime_id')->get();
        $leave_types = DB::table('leave_type')->where('inactive', false)->orderBy('leave_id')->get();

        $info = [];
        if (!empty($cart['person_id'])) {
            $emp_id = $cart['person_id'];
            $from = $cart['from_date'];
            $to = $cart['to_date'];

            if ($from && $to) {
                $from_fmt = str_replace('/', '-', $from);
                $to_fmt = str_replace('/', '-', $to);

                $workdays = DB::table('employee_attendance')
                    ->where('emp_id', $emp_id)
                    ->where('overtime_id', 0)
                    ->whereBetween('att_date', [date('Y-m-d', strtotime($from_fmt)), date('Y-m-d', strtotime($to_fmt))])
                    ->count();

                $Work_hours = Setting::getSetting('work_hours', $company_id, 8);
                $leave_hours = DB::table('employee_attendance')
                    ->where('emp_id', $emp_id)
                    ->where('overtime_id', 0)
                    ->where('hours', '<', 8)
                    ->whereBetween('att_date', [date('Y-m-d', strtotime($from_fmt)), date('Y-m-d', strtotime($to_fmt))])
                    ->get()
                    ->sum(fn($r) => $Work_hours - $r->hours);

                $info['work_days'] = $workdays;
                $info['leave_hours'] = $leave_hours;

                // Overtime totals
                $ot_totals = [];
                foreach ($overtimes as $ot) {
                    $total_ot = DB::table('employee_attendance')
                        ->where('emp_id', $emp_id)
                        ->where('overtime_id', $ot->overtime_id)
                        ->whereBetween('att_date', [date('Y-m-d', strtotime($from_fmt)), date('Y-m-d', strtotime($to_fmt))])
                        ->sum('hours');
                    $ot_totals[$ot->overtime_id] = $total_ot;
                }
                $info['ot_totals'] = $ot_totals;

                // Leave type counts
                $leave_counts = [];
                foreach ($leave_types as $lt) {
                    $count = DB::table('employee_leave')
                        ->where('emp_id', $emp_id)
                        ->where('leave_id', $lt->leave_id)
                        ->whereBetween('date', [date('Y-m-d', strtotime($from_fmt)), date('Y-m-d', strtotime($to_fmt))])
                        ->count();
                    $leave_counts[$lt->leave_id] = $count;
                }
                $info['leave_counts'] = $leave_counts;
            }
        }

        return view('hr.payslips', compact(
            'msg', 'error', 'cart', 'edit_index', 'accounts',
            'use_dimension', 'dimensions', 'employees', 'employees_list',
            'total_debit', 'total_credit', 'next_payslip_no', 'selected_emp',
            'pay_basis_label', 'overtimes', 'leave_types', 'info',
            'added_trans_no', 'added_payslip_no'
        ));
    }

    private function validatePayslipGeneration(Request $request, $employees): ?string
    {
        $person_id = $request->input('person_id');
        if (!$person_id) {
            return 'Employee not selected';
        }

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $from_date) || !strtotime(str_replace('/', '-', $from_date)))
            return 'The entered date is invalid.';
        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $to_date) || !strtotime(str_replace('/', '-', $to_date)))
            return 'The entered date is invalid.';

        // Check if already paid for any date in range
        $from_sql = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
        $to_sql = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));

        $existing = DB::table('payslip')
            ->where('emp_id', $person_id)
            ->where('from_date', '<=', $to_sql)
            ->where('to_date', '>=', $from_sql)
            ->exists();
        if ($existing) {
            return 'Selected period contains a period that has already been paid for this person';
        }

        if (strtotime(str_replace('/', '-', $from_date)) > strtotime(str_replace('/', '-', $to_date)))
            return 'End date cannot be before the start date';
        if (strtotime(str_replace('/', '-', $from_date)) > strtotime('today 23:59:59'))
            return 'Cannot pay for the date in the future.';
        if (strtotime(str_replace('/', '-', $to_date)) > strtotime('today 23:59:59'))
            return 'Cannot pay for the date in the future.';

        // Check employee hired
        $emp = DB::table('employees')->where('id', $person_id)->first();
        if (!$emp) return 'Employee not found';
        $hire_date = $emp->hire_date ?? $emp->created_at ?? null;
        if ($hire_date && $from_sql < date('Y-m-d', strtotime($hire_date)))
            return 'Cannot pay for the date before hired date';

        // Check position
        if (empty($emp->position_id))
            return 'Selected Employee does not have a Job Position, please define it first.';

        // Check salary structure exists
        $has_structure = false;
        if (!empty($emp->personal_salary)) {
            $has_structure = DB::table('personal_salary_structure')
                ->where('emp_id', $person_id)->exists();
        } else {
            $has_structure = DB::table('salary_structure')
                ->where('position_id', $emp->position_id)
                ->where('grade_id', $emp->grade_id ?? 0)
                ->exists();
        }
        if (!$has_structure)
            return "The Employee's Job Position does not have a structure, please define Salary Structure";

        return null;
    }

    private function generatePayslipGlItems(array &$cart, Request $request): void
    {
        $company_id = 1;
        $emp_id = $request->input('person_id');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $from_sql = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
        $to_sql = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));

        $emp = DB::table('employees')->where('id', $emp_id)->first();
        $position = DB::table('positions')->where('position_id', $emp->position_id)->first();

        $Work_days = Setting::getSetting('month_work_days', $company_id, 26);
        $Work_hours = Setting::getSetting('work_hours', $company_id, 8);
        $Payable_act = Setting::getSetting('payroll_payable_act', $company_id, '');
        $Deduct_act = Setting::getSetting('deductleave_act', $company_id, '');
        $Overtime_act = Setting::getSetting('overtime_act', $company_id, '');
        $USE_DEPT_ACC = Setting::getSetting('dept_based', $company_id, false);

        $cart['gl_items'] = [];
        $cart['empty_payment'] = false;

        $workdays = DB::table('employee_attendance')
            ->where('emp_id', $emp_id)
            ->where('overtime_id', 0)
            ->whereBetween('att_date', [$from_sql, $to_sql])
            ->count();

        // Get basic salary
        $emp_salary = $this->getEmpBasicSalary($emp_id);
        $salary_amount = $emp_salary['pay_amount'];
        $salary_basic_acc = $emp_salary['pay_rule_id'];

        // Get department
        $emp_dept = $emp->department_id;
        $dept = $emp_dept ? DB::table('departments')->where('dept_id', $emp_dept)->first() : null;
        $dept_basic_acc = $dept ? $dept->basic_account : '';

        if ($position && $position->pay_basis == 1) // DAILY_WAGE
            $Work_days = 1;

        $deductableleaves = ($position && $position->pay_basis == 0) ? ($Work_days - $workdays) : 0; // MONTHLY_SALARY
        $leave_hours = DB::table('employee_attendance')
            ->where('emp_id', $emp_id)
            ->where('overtime_id', 0)
            ->where('hours', '<', 8)
            ->whereBetween('att_date', [$from_sql, $to_sql])
            ->get()
            ->sum(fn($r) => $Work_hours - $r->hours);

        $leave_hours_amount = ($salary_amount / $Work_days) / $Work_hours * $leave_hours;
        $leave_deduct_amount = (($salary_amount / $Work_days) * $deductableleaves) + $leave_hours_amount;

        // Paid leaves adjustment
        $paid_leaves = DB::table('employee_leave')
            ->where('emp_id', $emp_id)
            ->whereBetween('date', [$from_sql, $to_sql])
            ->get();

        foreach ($paid_leaves as $row) {
            $row_leave_amt = ($salary_amount / $Work_days) * ($row->pay_rate / 100);
            $leave_deduct_amount -= $row_leave_amt;
        }

        // Get salary rules
        $salary_rules = $this->getEmpSalaryRules($emp_id);

        if (count($salary_rules) > 0) {
            $totalCredit = 0;
            $totalDebit = 0;
            $allowance = [];

            if (!empty($USE_DEPT_ACC) && empty($emp_dept)) {
                session()->flash('_payslip_error', 'This employee does not belong to any department');
                return;
            }
            if (!empty($USE_DEPT_ACC) && empty($dept_basic_acc)) {
                session()->flash('_payslip_error', 'Basic account has not been set for department: ' . ($dept->dept_name ?? ''));
                return;
            }
            if (empty($USE_DEPT_ACC) && empty($salary_basic_acc)) {
                session()->flash('_payslip_error', 'Basic account has not been set for job position: ' . ($position->position_name ?? ''));
                return;
            }

            // Basic salary GL line
            $basic_gl_account = !empty($USE_DEPT_ACC) && !empty($dept_basic_acc) ? $dept_basic_acc : $salary_basic_acc;
            $cart['gl_items'][] = [
                'code_id' => $basic_gl_account,
                'description' => '',
                'amount' => $salary_amount,
                'memo' => '',
            ];

            $pay_basis = $position->pay_basis ?? 0;

            foreach ($salary_rules as $rule) {
                if ($pay_basis == 0) { // MONTHLY_SALARY
                    if ((!empty($USE_DEPT_ACC) && $rule['is_basic'] != 1) || empty($USE_DEPT_ACC)) {
                        $amt = ($rule['type'] == 0) ? -$rule['pay_amount'] : $rule['pay_amount'];
                        $cart['gl_items'][] = [
                            'code_id' => $rule['pay_rule_id'],
                            'description' => '',
                            'amount' => $amt,
                            'memo' => '',
                        ];
                    }
                } else {
                    if ($workdays != 0) {
                        if ((!empty($USE_DEPT_ACC) && $rule['is_basic'] != 1) || empty($USE_DEPT_ACC)) {
                            $amt = ($rule['type'] == 0) ? -$rule['pay_amount'] * $workdays : $rule['pay_amount'] * $workdays;
                            $cart['gl_items'][] = [
                                'code_id' => $rule['pay_rule_id'],
                                'description' => '',
                                'amount' => $amt,
                                'memo' => '',
                            ];
                        }
                    }
                }

                if ($workdays != 0) {
                    if ($rule['type'] == 0)
                        $totalCredit += $rule['pay_amount'];
                    else
                        $totalDebit += ($pay_basis == 0) ? $rule['pay_amount'] : $rule['pay_amount'] * $workdays;
                }

                if ($rule['is_basic'] != 1) {
                    if ($rule['type'] == 1) { // DEBIT
                        $alw_leave_hours_amt = ($rule['pay_amount'] / $Work_days / $Work_hours) * $leave_hours;
                        if ($pay_basis == 0)
                            $alw_amt = ($rule['pay_amount'] / $Work_days) * $workdays - $alw_leave_hours_amt;
                        else
                            $alw_amt = $rule['pay_amount'] - $alw_leave_hours_amt;
                        $leave_deduct_amount += ($rule['pay_amount'] - $alw_amt);
                    } else { // CREDIT
                        $alw_leave_hours_amt = -($rule['pay_amount'] / $Work_days / $Work_hours) * $leave_hours;
                        if ($pay_basis == 0)
                            $alw_amt = -(-($rule['pay_amount'] / $Work_days) * $workdays - $alw_leave_hours_amt);
                        else
                            $alw_amt = -($rule['pay_amount'] - $alw_leave_hours_amt);
                        $leave_deduct_amount -= ($rule['pay_amount'] - $alw_amt);
                    }
                    $allowance[$rule['pay_rule_id']] = $alw_amt ?? 0;
                }
            }
            $cart['allowance'] = $allowance;
        }

        // Overtime calculation
        $overtime_amount = 0;
        $overtimes = DB::table('overtime')->where('inactive', false)->get();
        foreach ($overtimes as $ot) {
            $ot_rows = DB::table('employee_attendance')
                ->where('emp_id', $emp_id)
                ->where('overtime_id', $ot->overtime_id)
                ->whereBetween('att_date', [$from_sql, $to_sql])
                ->get();
            foreach ($ot_rows as $ot_row) {
                $overtime_amount += ($salary_amount / $Work_days) / $Work_hours * $ot_row->rate * $ot_row->hours;
            }
        }

        if ($overtime_amount != 0) {
            if (!empty($Overtime_act) && empty($USE_DEPT_ACC))
                $cart['gl_items'][] = ['code_id' => $Overtime_act, 'description' => '', 'amount' => $overtime_amount, 'memo' => ''];
            elseif (!empty($USE_DEPT_ACC))
                $this->updateFirstGlItem($cart, $basic_gl_account, $salary_amount + $overtime_amount);
            else
                $this->updateFirstGlItem($cart, $salary_basic_acc, $salary_amount + $overtime_amount);
        }

        $totalDebit = ($totalDebit ?? 0) + $overtime_amount;

        // Leave deduction
        if ($leave_deduct_amount != 0) {
            if (!empty($Deduct_act) && empty($USE_DEPT_ACC))
                $cart['gl_items'][] = ['code_id' => $Deduct_act, 'description' => '', 'amount' => -$leave_deduct_amount, 'memo' => ''];
            elseif (!empty($USE_DEPT_ACC))
                $cart['gl_items'][] = ['code_id' => $dept_basic_acc, 'description' => '', 'amount' => -$leave_deduct_amount, 'memo' => ''];
            else
                $cart['gl_items'][] = ['code_id' => $salary_basic_acc, 'description' => '', 'amount' => -$leave_deduct_amount, 'memo' => ''];
            $totalCredit = ($totalCredit ?? 0) + $leave_deduct_amount;
        }

        $payable_amount = ($totalCredit ?? 0) - ($totalDebit ?? 0) - $salary_amount;

        if ($payable_amount != 0)
            $cart['gl_items'][] = ['code_id' => $Payable_act, 'description' => '', 'amount' => $payable_amount, 'memo' => ''];

        $cart['payable_amt'] = abs($payable_amount);
        $cart['overtime_amt'] = abs($overtime_amount);
        $cart['leaves'] = $leave_hours;
        $cart['deductable_leaves'] = $deductableleaves;
        $cart['work_days'] = $workdays;
        $cart['pay_basis'] = $pay_basis;
        $cart['salary_amt'] = $salary_amount;

        if ($workdays == 0 && $overtime_amount == 0 && $payable_amount == 0) {
            $cart['gl_items'] = [];
            $cart['empty_payment'] = true;
        }
    }

    private function updateFirstGlItem(array &$cart, string $code_id, float $new_amount): void
    {
        foreach ($cart['gl_items'] as $idx => $item) {
            if ($item['code_id'] === $code_id) {
                $cart['gl_items'][$idx]['amount'] = $new_amount;
                return;
            }
        }
    }

    private function getEmpBasicSalary(int $emp_id): array
    {
        $emp = DB::table('employees')->where('id', $emp_id)->first();
        if (!$emp) return ['pay_amount' => 0, 'pay_rule_id' => ''];

        if (!empty($emp->personal_salary)) {
            $basic = DB::table('personal_salary_structure')
                ->where('emp_id', $emp_id)
                ->where('is_basic', true)
                ->first();
            if ($basic && $basic->pay_amount > 0)
                return ['pay_amount' => $basic->pay_amount, 'pay_rule_id' => $basic->pay_rule_id];
            return ['pay_amount' => $emp->personal_salary, 'pay_rule_id' => $basic->pay_rule_id ?? ''];
        }

        $position_id = $emp->position_id;
        $grade_id = $emp->grade_id ?? 0;
        $basic = DB::table('salary_structure')
            ->where('position_id', $position_id)
            ->where(function ($q) use ($grade_id) {
                $q->where('grade_id', $grade_id)->orWhere('grade_id', 0);
            })
            ->where('is_basic', true)
            ->first();
        if ($basic)
            return ['pay_amount' => $basic->pay_amount, 'pay_rule_id' => $basic->pay_rule_id];

        $position = DB::table('positions')->where('position_id', $position_id)->first();
        return ['pay_amount' => $position->pay_amount ?? 0, 'pay_rule_id' => ''];
    }

    private function getEmpSalaryRules(int $emp_id): array
    {
        $emp = DB::table('employees')->where('id', $emp_id)->first();
        if (!$emp) return [];

        if (!empty($emp->personal_salary)) {
            return DB::table('personal_salary_structure')
                ->where('emp_id', $emp_id)
                ->get()
                ->map(fn($r) => [
                    'pay_rule_id' => $r->pay_rule_id,
                    'pay_amount' => $r->pay_amount,
                    'type' => $r->type,
                    'is_basic' => $r->is_basic,
                ])
                ->toArray();
        }

        return DB::table('salary_structure')
            ->where('position_id', $emp->position_id)
            ->where('grade_id', $emp->grade_id ?? 0)
            ->get()
            ->map(fn($r) => [
                'pay_rule_id' => $r->pay_rule_id,
                'pay_amount' => $r->pay_amount,
                'type' => $r->type,
                'is_basic' => $r->is_basic,
            ])
            ->toArray();
    }

    public function documentExpiration(Request $request): View
    {
        $msg = null;
        $error = null;
        $company_id = 1;
        $Mode = '';

        if (!DB::table('document_types')->where('inactive', false)->exists()) {
            $error = 'There are no <b>Document Types</b> defined in the system';
        }

        $view_mode = $request->input('View', '');

        // Handle file view/download
        $view_id = $request->input('vw', $this->findSubmit('view', $request));
        if ($view_id !== null) {
            $doc = DB::table('employee_docs')->where('id', $view_id)->first();
            if ($doc && $doc->filename) {
                $path = storage_path('app/attachments/' . $doc->unique_name);
                if (file_exists($path)) {
                    $type = $doc->filetype ?: 'application/octet-stream';
                    header('Content-type: ' . $type);
                    header('Content-Length: ' . $doc->filesize);
                    header('Content-Disposition: inline');
                    echo file_get_contents($path);
                    exit;
                }
            }
        }

        $download_id = $request->input('dl', $this->findSubmit('download', $request));
        if ($download_id !== null) {
            $doc = DB::table('employee_docs')->where('id', $download_id)->first();
            if ($doc && $doc->filename) {
                $path = storage_path('app/attachments/' . $doc->unique_name);
                if (file_exists($path)) {
                    $type = $doc->filetype ?: 'application/octet-stream';
                    header('Content-type: ' . $type);
                    header('Content-Length: ' . $doc->filesize);
                    header('Content-Disposition: attachment; filename=' . $doc->filename);
                    echo file_get_contents($path);
                    exit;
                }
            }
        }

        // Handle EmpId/DocId from URL
        $emp_id = $request->input('emp_id', $request->input('EmpId', ''));
        $selected_id = $request->input('DocId', $request->input('selected_id', ''));

        if ($request->has('DocId') || $request->has('selected_id')) {
            $Mode = $request->has('DocId') ? 'Edit' : '';
        }

        // Handle delete
        $delete_id = $this->findSubmit('Delete', $request);
        if ($delete_id !== null) {
            $doc = DB::table('employee_docs')->where('id', $delete_id)->first();
            if ($doc) {
                $path = storage_path('app/attachments/' . $doc->unique_name);
                if ($doc->filename && file_exists($path)) {
                    unlink($path);
                }
                DB::table('employee_docs')->where('id', $delete_id)->delete();
                $msg = 'Attachment has been deleted.';
            }
            $selected_id = -1;
        }

        // Handle Edit click (entry mode only)
        $edit_click = $this->findSubmit('Edit', $request);
        if ($edit_click !== null) {
            $selected_id = $edit_click;
        }

        // Handle add/update
        $is_add = $request->has('ADD_ITEM') || ($request->has('process') && in_array($request->input('selected_id'), ['', '-1', null]));
        $is_update = $request->has('UPDATE_ITEM') || ($request->has('process') && !in_array($request->input('selected_id'), ['', '-1', null]));

        if ($is_add || $is_update) {
            $input_error = 0;
            $emp_id = $request->input('emp_id');
            $type_id = $request->input('type_id');
            $doc_title = $request->input('doc_title');
            $issue_date = $request->input('issue_date');
            $expiry_date = $request->input('expiry_date');
            $alert = $request->boolean('alert');

            if (empty($emp_id)) {
                $error = 'Select an employee.';
                $input_error = 1;
            } elseif (empty($type_id)) {
                $error = 'Select a document type.';
                $input_error = 1;
            } elseif (empty(trim($doc_title))) {
                $error = 'The document description cannot be empty.';
                $input_error = 1;
            } elseif ($issue_date && $expiry_date && strtotime(str_replace('/', '-', $issue_date)) > strtotime(str_replace('/', '-', $expiry_date))) {
                $error = 'Issue date cannot be after expiry date.';
                $input_error = 1;
            }

            if (!$input_error && $is_add && !$request->hasFile('filename')) {
                $error = 'Select attachment file.';
                $input_error = 1;
            }

            if (!$input_error && $is_add && $request->hasFile('filename')) {
                $file = $request->file('filename');
                if (!$file->isValid()) {
                    $error = $file->getError() == UPLOAD_ERR_INI_SIZE
                        ? 'The file size is over the maximum allowed.'
                        : 'Select attachment file.';
                    $input_error = 1;
                }
            }

            if (!$input_error) {
                $dir = storage_path('app/attachments');
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $original_name = '';
                $filesize = 0;
                $filetype = '';
                $unique_name = '';

                if ($request->hasFile('filename') && $request->file('filename')->isValid()) {
                    $file = $request->file('filename');
                    $original_name = $file->getClientOriginalName();
                    $filesize = $file->getSize();
                    $filetype = $file->getClientMimeType();
                    $unique_name = str_replace('.', '', uniqid('doc_', true)) . '_' . time() . '.' . $file->getClientOriginalExtension();

                    if ($is_update) {
                        $old_doc = DB::table('employee_docs')->where('id', $selected_id)->first();
                        if ($old_doc && $old_doc->filename && file_exists($dir . '/' . $old_doc->unique_name)) {
                            unlink($dir . '/' . $old_doc->unique_name);
                        }
                    }

                    $file->move($dir, $unique_name);
                } elseif ($is_update) {
                    $old_doc = DB::table('employee_docs')->where('id', $selected_id)->first();
                    $unique_name = $old_doc->unique_name;
                }

                $data = [
                    'emp_id' => $emp_id,
                    'type_id' => $type_id,
                    'description' => $doc_title,
                    'issue_date' => date('Y-m-d', strtotime(str_replace('/', '-', $issue_date))),
                    'expiry_date' => date('Y-m-d', strtotime(str_replace('/', '-', $expiry_date))),
                    'alert' => $alert,
                    'filename' => $original_name ?: ($is_update && isset($old_doc) ? $old_doc->filename : ''),
                    'unique_name' => $unique_name,
                    'filesize' => $filesize ?: ($is_update && isset($old_doc) ? $old_doc->filesize : 0),
                    'filetype' => $filetype ?: ($is_update && isset($old_doc) ? $old_doc->filetype : ''),
                ];

                if ($is_add) {
                    DB::table('employee_docs')->insert($data);
                    $msg = 'Attachment has been inserted.';
                } else {
                    DB::table('employee_docs')->where('id', $selected_id)->update($data);
                    $msg = 'Attachment has been updated.';
                }

                $selected_id = -1;
                $Mode = 'RESET';
            }
        }

        // Build query for documents list matching FA's get_sql_for_employee_documents
        $str = $request->input('string');
        $filter_emp = $request->input('emp_id');
        $filter_type = $request->input('type_id');
        $alert_on = $request->input('alert');
        $no_alert = $request->input('no_alert');
        $expired_from = $request->input('expired_from');
        $expired_to = $request->input('expired_to');
        $issue_from = $request->input('issue_from');
        $issue_to = $request->input('issue_to');

        $query = DB::table('employee_docs')
            ->join('employees', 'employee_docs.emp_id', '=', 'employees.id')
            ->join('document_types', 'employee_docs.type_id', '=', 'document_types.type_id')
            ->select(
                'employee_docs.id', 'employee_docs.type_id', 'employee_docs.description',
                'employee_docs.issue_date', 'employee_docs.expiry_date', 'employee_docs.alert',
                'employee_docs.filename', 'employee_docs.filesize', 'employee_docs.filetype',
                'employee_docs.emp_id', 'employee_docs.unique_name',
                'document_types.type_name', 'document_types.notify_before',
                DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as emp_name")
            );

        if (!empty($view_mode)) {
            // Inquiry mode: advanced filters matching FA get_sql_for_employee_documents
            $has_date_range = !empty($expired_from) || !empty($expired_to) || !empty($issue_from) || !empty($issue_to);

            if (!$has_date_range) {
                $query->where('employee_docs.emp_id', $emp_id ?: 0);
            } else {
                if (!empty($issue_from) && !empty($issue_to)) {
                    $query->whereBetween('employee_docs.issue_date', [
                        date('Y-m-d', strtotime(str_replace('/', '-', $issue_from))),
                        date('Y-m-d', strtotime(str_replace('/', '-', $issue_to)))
                    ]);
                }
                if (!empty($expired_from) && !empty($expired_to)) {
                    $query->whereBetween('employee_docs.expiry_date', [
                        date('Y-m-d', strtotime(str_replace('/', '-', $expired_from))),
                        date('Y-m-d', strtotime(str_replace('/', '-', $expired_to)))
                    ]);
                }
            }

            if (!empty($filter_emp))
                $query->where('employee_docs.emp_id', $filter_emp);
            if (!empty($filter_type))
                $query->where('employee_docs.type_id', $filter_type);
            if (!empty($alert_on) && !empty($no_alert)) {
                // both checked = no filter
            } elseif (!empty($no_alert)) {
                $query->where('employee_docs.alert', false);
            } elseif (!empty($alert_on)) {
                $query->where('employee_docs.alert', true);
            }
            if (!empty($str))
                $query->where(function ($q) use ($str) {
                    $q->where('employee_docs.description', 'like', "%$str%")
                      ->orWhere('employee_docs.filename', 'like', "%$str%")
                      ->orWhere('employee_docs.id', 'like', "%$str%");
                });
        } else {
            // Entry mode: simple employee filter
            $query->where('employee_docs.emp_id', $emp_id ?: 0);
        }

        $query->orderBy('employee_docs.id');
        $documents = $query->get();

        // Get data for edit form
        $edit_doc = null;
        if ($selected_id && $selected_id != -1) {
            $edit_doc = DB::table('employee_docs')->where('id', $selected_id)->first();
        }

        $employees = DB::table('employees')->where('is_active', true)->orderBy('id')->get(['id', 'first_name', 'last_name']);
        $doc_types = DB::table('document_types')->where('inactive', false)->orderBy('type_id')->get();

        $has_doc_types = DB::table('document_types')->where('inactive', false)->exists();

        return view('hr.document-expiration', compact(
            'msg', 'error', 'documents', 'employees', 'doc_types',
            'emp_id', 'selected_id', 'edit_doc', 'view_mode',
            'has_doc_types'
        ));
    }

    private function timeToFloat($time): float
    {
        if (strpos($time, ':') !== false) {
            sscanf($time, "%d:%d", $hours, $minutes);
            return $hours + $minutes / 60;
        }
        return (float)$time;
    }

    public function timesheet(Request $request): View
    {
        $company_id = 1;
        $msg = null;
        $error = null;

        $dept_id = $request->input('DeptId', '');
        $emp_id = $request->input('EmpId', '');
        $from_date = $request->input('FromDate', date('Y-m-d', strtotime('-7 days')));
        $to_date = $request->input('ToDate', date('Y-m-d'));
        $ot_id = $request->input('OvertimeId', '');
        $search = $request->has('Search');
        $lev_data = [];

        $weekend_day = (int)Setting::getSetting('weekend_day', $company_id, 0);
        if (empty($weekend_day)) $weekend_day = 7;

        $departments = DB::table('departments')
            ->orderBy('dept_id')
            ->get(['dept_id', 'dept_name']);

        $employees_filter = DB::table('employees')
            ->where('is_active', true)
            ->when($dept_id, fn($q) => $q->where('department_id', $dept_id))
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name']);

        $overtimes = DB::table('overtime')
            ->where('inactive', false)
            ->orderBy('overtime_id')
            ->get();

        // Build day columns
        $day_columns = [];
        $day_headers = [];
        if ($search && $from_date && $to_date) {
            $begin = new DateTime(date('Y-m-d', strtotime($from_date)));
            $end = (new DateTime(date('Y-m-d', strtotime($to_date))))->modify('+1 day');
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                $is_weekend = $dt->format('N') == $weekend_day;
                $day_columns[] = [
                    'date' => $dt->format('Y-m-d'),
                    'day' => $dt->format('d'),
                    'month' => $dt->format('m'),
                    'is_weekend' => $is_weekend,
                ];
                $day_headers[] = $dt->format('d');
            }
        }

        // Get attendance data
        $employees = collect();
        $att_data = [];

        if ($search && $from_date && $to_date) {
            $emp_query = DB::table('employees')
                ->where('is_active', true)
                ->when($dept_id, fn($q) => $q->where('department_id', $dept_id))
                ->when($emp_id, fn($q) => $q->where('id', $emp_id))
                ->orderBy('id');

            $employees = $emp_query->paginate(50);

            // For each employee, get attendance and leave data for each day
            $emp_ids = $employees->pluck('id')->toArray();

            if (!empty($emp_ids)) {
                $all_attendance = DB::table('employee_attendance')
                    ->whereIn('emp_id', $emp_ids)
                    ->whereBetween('att_date', [date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
                    ->when($ot_id !== '', fn($q) => $q->where('overtime_id', $ot_id))
                    ->get();

                $all_leaves = DB::table('employee_leave')
                    ->join('leave_type', 'employee_leave.leave_id', '=', 'leave_type.leave_id')
                    ->whereIn('emp_id', $emp_ids)
                    ->whereBetween('date', [date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
                    ->select('employee_leave.*', 'leave_type.leave_code', 'leave_type.pay_rate as leave_pay_rate')
                    ->get();

                foreach ($all_attendance as $att) {
                    $att_data[$att->emp_id][$att->att_date] = $att;
                }

                foreach ($all_leaves as $lev) {
                    $lev_data[$lev->emp_id][$lev->date] = $lev;
                }
            }
        }

        return view('hr.timesheet', compact(
            'msg', 'error', 'departments', 'employees_filter', 'overtimes',
            'dept_id', 'emp_id', 'from_date', 'to_date', 'ot_id', 'search',
            'day_columns', 'employees', 'att_data', 'lev_data', 'weekend_day'
        ));
    }

    public function employeeTransactionInquiry(Request $request): View
    {
        $company_id = 1;
        $msg = null;
        $error = null;

        $ref = $request->input('Ref', '');
        $memo = $request->input('Memo', '');
        $from_date = $request->input('FromDate', date('Y-m-d', strtotime('-30 days')));
        $to_date = $request->input('ToDate', date('Y-m-d'));
        $dept_id = $request->input('DeptId', '');
        $emp_id = $request->input('EmpId', '');
        $only_unpaid = $request->boolean('OnlyUnpaid');

        $departments = DB::table('departments')
            ->orderBy('dept_id')
            ->get(['dept_id', 'dept_name']);

        $employees_filter = DB::table('employees')
            ->where('is_active', true)
            ->when($dept_id, fn($q) => $q->where('department_id', $dept_id))
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name']);

        $transactions = collect();
        $has_searched = $request->has('Search');

        if ($has_searched) {
            $from_sql = DB::connection()->getPdo()->quote(date('Y-m-d', strtotime($from_date)));
            $to_sql = DB::connection()->getPdo()->quote(date('Y-m-d', strtotime($to_date)));

            // Build raw UNION SQL matching FA's get_sql_for_payslips
            $union_sql = "SELECT P.trans_date, P.trans_no, P.type, P.emp_id, P.emp_name, P.payslip_no, P.from_date, P.to_date, P.amount FROM (
                SELECT p.generated_date AS trans_date, p.trans_no, 0 AS type, e.id AS emp_id, CONCAT(e.first_name, ' ', e.last_name) AS emp_name, p.payslip_no, p.from_date, p.to_date, p.payable_amount AS amount
                FROM payslip p
                JOIN employees e ON p.emp_id = e.id
                UNION
                SELECT t.pay_date AS trans_date, t.trans_no, 1 AS type, e2.id AS emp_id, CONCAT(e2.first_name, ' ', e2.last_name) AS emp_name, t.payslip_no, p2.from_date, p2.to_date, t.pay_amount AS amount
                FROM employee_trans t
                JOIN payslip p2 ON t.payslip_no = p2.payslip_no
                JOIN employees e2 ON p2.emp_id = e2.id
                WHERE t.payslip_no != 0
                UNION
                SELECT t2.pay_date AS trans_date, t2.trans_no, 1 AS type, e3.id AS emp_id, CONCAT(e3.first_name, ' ', e3.last_name) AS emp_name, 0 AS payslip_no, NULL AS from_date, NULL AS to_date, t2.pay_amount AS amount
                FROM employee_trans t2
                JOIN employee_advance av ON t2.id = av.emp_trans_no
                JOIN employees e3 ON av.emp_id = e3.id
            ) P
            WHERE P.trans_date BETWEEN {$from_sql} AND {$to_sql}";

            // Department filter
            if ($dept_id) {
                $dept_esc = (int)$dept_id;
                $union_sql .= " AND P.emp_id IN (SELECT id FROM employees WHERE department_id = {$dept_esc})";
            }
            // Employee filter
            if ($emp_id) {
                $emp_esc = (int)$emp_id;
                $union_sql .= " AND P.emp_id = {$emp_esc}";
            }
            // Reference filter
            if (!empty($ref)) {
                $ref_esc = DB::connection()->getPdo()->quote('%' . $ref . '%');
                $union_sql .= " AND (CAST(P.payslip_no AS CHAR) LIKE {$ref_esc} OR CAST(P.trans_no AS CHAR) LIKE {$ref_esc})";
            }
            // Memo filter
            if (!empty($memo)) {
                $memo_esc = DB::connection()->getPdo()->quote('%' . $memo . '%');
                $union_sql .= " AND ((P.type = 0 AND P.trans_no IN (SELECT id FROM journal_entries WHERE description LIKE {$memo_esc})) OR (P.type = 1 AND P.trans_no IN (SELECT id FROM employee_trans WHERE memo_ LIKE {$memo_esc})))";
            }

            // Unpaid filter: payslip rows with no matching employee_trans payment advice
            if ($only_unpaid) {
                $union_sql .= " AND P.payslip_no != 0 AND P.type = 0 AND NOT EXISTS (
                    SELECT 1 FROM employee_trans et WHERE et.payslip_no = P.payslip_no AND et.payslip_no != 0
                )";
            }

            $union_sql .= " ORDER BY P.trans_date DESC";

            $rows = DB::select($union_sql);
            $transactions = collect($rows);
        }

        return view('hr.inquiries.transactions', compact(
            'msg', 'error', 'departments', 'employees_filter',
            'ref', 'memo', 'from_date', 'to_date', 'dept_id', 'emp_id',
            'only_unpaid', 'transactions', 'has_searched'
        ));
    }

    public function employeeAdvance(Request $request): View|RedirectResponse
    {
        $company_id = 1;
        $msg = null;
        $error = null;

        $advance_cart = session('advance_items');
        if (!$advance_cart) {
            $advance_cart = [
                'order_id'     => 0,
                'reference'    => '',
                'tran_date'    => date('Y-m-d'),
                'person_id'    => '',
                'bank_account' => '',
                'memo_'        => '',
                'gl_items'     => [],
                'advance_amount' => 0,
                'total_payments' => 0,
            ];
            session(['advance_items' => $advance_cart]);
        }

        $cart = &$advance_cart;

        $employees = DB::table('employees')
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name']);

        $employees_list = $employees->mapWithKeys(fn($e) => [$e->id => $e->first_name . ' ' . $e->last_name])->toArray();

        $bank_accounts = DB::table('bank_accounts')
            ->where('inactive', false)
            ->orderBy('bank_account_name')
            ->get(['id', 'bank_account_name', 'bank_curr_code']);

        $accounts = DB::table('accounts')
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $Payable_act = Setting::getSetting('payroll_payable_act', $company_id, '');

        // Handle employee change — clear gl_items
        if ($request->has('person_id') && $request->input('person_id') != $cart['person_id']) {
            $cart['gl_items'] = [];
            $cart['advance_amount'] = 0;
        }

        // Handle Generate Payment Items
        if ($request->has('update_advances')) {
            $advance_amount = (float)$request->input('advance_amount', 0);
            $emp_id = $request->input('person_id', $cart['person_id']);
            $cart['bank_account'] = $request->input('bank_account', $cart['bank_account']);

            if ($advance_amount <= 0) {
                $error = 'Pay amount have to be positive number.';
            } elseif (!$emp_id) {
                $error = 'You have to select an employee.';
            } elseif (empty($Payable_act)) {
                $error = 'Payroll Payable account is not set in Payroll Default Settings.';
            } else {
                $cart['gl_items'] = [];
                $cart['gl_items'][] = [
                    'code_id' => $Payable_act,
                    'description' => '',
                    'amount' => $advance_amount,
                    'memo' => '',
                ];
                $cart['person_id'] = $emp_id;
                $cart['advance_amount'] = $advance_amount;
            }
        }

        // GL item CRUD
        $delete_id = $request->input('Delete');
        if ($delete_id !== null && isset($cart['gl_items'][$delete_id])) {
            unset($cart['gl_items'][$delete_id]);
            $cart['gl_items'] = array_values($cart['gl_items']);
        }

        if ($request->has('AddItem')) {
            $code_id = $request->input('code_id', '');
            $amtDebit = (float)$request->input('AmountDebit', 0);
            $amtCredit = (float)$request->input('AmountCredit', 0);

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
                    'description' => '',
                    'amount' => $amount,
                    'memo' => $request->input('LineMemo', ''),
                ];
            }
        }

        if ($request->has('UpdateItem')) {
            $edit_idx = session('advance_edit_index');
            if ($edit_idx !== null && isset($cart['gl_items'][$edit_idx])) {
                $code_id = $request->input('code_id', '');
                $amtDebit = (float)$request->input('AmountDebit', 0);
                $amtCredit = (float)$request->input('AmountCredit', 0);

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
                        'description' => '',
                        'amount' => $amount,
                        'memo' => $request->input('LineMemo', ''),
                    ];
                }
                session(['advance_edit_index' => null]);
            }
        }

        if ($request->has('CancelItemChanges')) {
            session(['advance_edit_index' => null]);
        }

        $edit_id = $request->input('Edit');
        if ($edit_id !== null && isset($cart['gl_items'][$edit_id])) {
            session(['advance_edit_index' => $edit_id]);
        }
        $edit_index = session('advance_edit_index');

        // Process
        if ($request->has('Process')) {
            $input_date = $request->input('date_');
            if ($input_date && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input_date)) {
                $cart['tran_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input_date)->format('Y-m-d');
            }
            $cart['reference'] = $request->input('ref', $cart['reference']);
            $cart['memo_'] = $request->input('memo_', $cart['memo_']);
            $cart['person_id'] = $request->input('person_id', $cart['person_id']);
            $cart['bank_account'] = $request->input('bank_account', $cart['bank_account']);
            $amount_field = (float)$request->input('amount', 0);

            if (count($cart['gl_items']) < 1) {
                $error = 'You must enter at least one payment line.';
            } elseif (empty($cart['person_id'])) {
                $error = 'You have to select an employee.';
            } elseif (!$cart['tran_date']) {
                $error = 'The entered date is invalid.';
            } else {
                $total = array_sum(array_column($cart['gl_items'], 'amount'));
                $payable_amt = abs($total);

                // Collect allocations from previous advances
                $allocs = [];
                foreach ($request->all() as $k => $v) {
                    if (strlen($k) > 6 && substr($k, 0, 6) == 'amount' && $v > 0) {
                        $allocs[substr($k, 6)] = (float)$v;
                    }
                }

                if ($amount_field >= $payable_amt && count($allocs) > 0) {
                    // Fully allocated — just record allocation, no bank payment
                    DB::beginTransaction();
                    try {
                        $etId = DB::table('employee_trans')->insertGetId([
                            'trans_no' => 0,
                            'trans_type' => 1,
                            'payslip_no' => null,
                            'pay_date' => $cart['tran_date'],
                            'to_the_order_of' => $cart['person_id'],
                            'pay_amount' => 0,
                            'bank_account' => $cart['bank_account'],
                            'ref' => $cart['reference'],
                            'memo_' => $cart['memo_'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        DB::table('employee_advance')->insert([
                            'emp_trans_no' => $etId,
                            'emp_id' => $cart['person_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        foreach ($allocs as $trans_to => $alloc_amt) {
                            DB::table('employee_advance_allocation')->insert([
                                'trans_no_from' => $etId,
                                'trans_no_to' => $trans_to,
                                'amount' => $alloc_amt,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        DB::commit();
                        $msg = 'Employee advances have been allocated, no bank payment has been made.';

                        $cart['gl_items'] = [];
                        $cart['advance_amount'] = 0;
                        session(['advance_items' => $cart]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error = 'Error processing allocation: ' . $e->getMessage();
                    }
                } else {
                    // Write journal entry for the bank payment
                    DB::beginTransaction();
                    try {
                        // Adjust GL items if there's a partial allocation
                        $old_amt = $payable_amt;
                        if ($amount_field > 0 && $amount_field < $payable_amt) {
                            $this_alloc = $amount_field;
                            $cart['gl_items'] = [];
                            $cart['gl_items'][] = [
                                'code_id' => $Payable_act,
                                'description' => '',
                                'amount' => $old_amt - $this_alloc,
                                'memo' => '',
                            ];
                            $payable_amt = $old_amt - $this_alloc;
                        }

                        $entry_date = $cart['tran_date'] ?: now()->format('Y-m-d');
                        $entry_number = $cart['reference'] ?: 'ADV-' . date('Ymd') . '-' . uniqid();
                        $jeId = DB::table('journal_entries')->insertGetId([
                            'company_id' => $company_id,
                            'entry_number' => $entry_number,
                            'entry_date' => $entry_date,
                            'reference_type' => 'employee_advance',
                            'reference_id' => null,
                            'description' => 'Employee Advance - ' . ($employees_list[$cart['person_id']] ?? ''),
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
                            $account = DB::table('accounts')->where('code', $item['code_id'])->first();
                            if ($account) {
                                $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                                $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                                DB::table('journal_entry_lines')->insert([
                                    'journal_entry_id' => $jeId,
                                    'account_id' => $account->id,
                                    'description' => $item['memo'] ?? '',
                                    'debit_amount' => $debit,
                                    'credit_amount' => $credit,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        // Write employee_trans
                        $etId = DB::table('employee_trans')->insertGetId([
                            'trans_no' => $jeId,
                            'trans_type' => 1,
                            'payslip_no' => null,
                            'pay_date' => $entry_date,
                            'to_the_order_of' => $cart['person_id'],
                            'pay_amount' => $payable_amt,
                            'bank_account' => $cart['bank_account'],
                            'ref' => $cart['reference'],
                            'memo_' => $cart['memo_'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Link to employee_advance
                        DB::table('employee_advance')->insert([
                            'emp_trans_no' => $etId,
                            'emp_id' => $cart['person_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Write allocations if any
                        if (count($allocs) > 0) {
                            foreach ($allocs as $trans_to => $alloc_amt) {
                                DB::table('employee_advance_allocation')->insert([
                                    'trans_no_from' => $etId,
                                    'trans_no_to' => $trans_to,
                                    'amount' => $alloc_amt,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        DB::commit();

                        session(['advance_items' => null]);
                        return redirect()->route('hr.employee-advances', ['AddedID' => $jeId]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error = 'Error processing advance: ' . $e->getMessage();
                    }
                }
            }
        }

        // Handle success display
        if ($request->has('AddedID')) {
            $trans_no = $request->input('AddedID');
            $msg = 'Employee Advance #' . $trans_no . ' has been entered';
        }

        // Cancel
        if ($request->has('CancelOrder')) {
            session()->forget('advance_items');
            $advance_cart = [
                'order_id' => 0,
                'reference' => '',
                'tran_date' => date('Y-m-d'),
                'person_id' => '',
                'bank_account' => '',
                'memo_' => '',
                'gl_items' => [],
                'advance_amount' => 0,
                'total_payments' => 0,
            ];
            session(['advance_items' => $advance_cart]);
            $cart = &$advance_cart;
        }

        // Get employee's previous unallocated advances
        $advances = collect();
        $total_allocated = 0;
        if (!empty($cart['person_id'])) {
            $advances = DB::table('employee_trans as t')
                ->leftJoin('employee_advance as a', 't.id', '=', 'a.emp_trans_no')
                ->leftJoin('employee_advance_allocation as al', 't.id', '=', 'al.trans_no_to')
                ->where('a.emp_id', $cart['person_id'])
                ->groupBy('t.id', 't.trans_no', 't.trans_type', 't.pay_date', 't.pay_amount', 't.to_the_order_of', 't.ref')
                ->selectRaw('t.id, t.trans_no, t.trans_type, t.pay_date, t.pay_amount, COALESCE(SUM(al.amount), 0) as amt_allocated, (t.pay_amount - COALESCE(SUM(al.amount), 0)) as remain')
                ->having('remain', '>', 0)
                ->get();

            if ($advances->isNotEmpty()) {
                // Get previous allocation amounts from request
                foreach ($advances as $adv) {
                    $alloc = (float)$request->input('amount' . $adv->id, 0);
                    $total_allocated += $alloc;
                }
            }
        }

        $total_debit = array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $cart['gl_items'] ?? []));
        $total_credit = array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $cart['gl_items'] ?? []));

        session(['advance_items' => $cart]);

        return view('hr.employee-advances', compact(
            'msg', 'error', 'cart', 'edit_index', 'accounts',
            'employees', 'employees_list', 'bank_accounts',
            'total_debit', 'total_credit', 'advances', 'total_allocated',
            'Payable_act'
        ));
    }

    public function paymentAdvice(Request $request): View|RedirectResponse
    {
        $company_id = 1;
        $msg = null;
        $error = null;

        $pay_cart = session('payment_advice_cart');
        if (!$pay_cart) {
            $pay_cart = [
                'bank_account_id' => '',
                'pay_date' => date('Y-m-d'),
                'ref' => '',
                'memo_' => '',
                'person_id' => '',
                'person_name' => '',
                'pay_amount' => 0,
                'payslip_no' => 0,
                'gl_items' => [],
                'total_payments' => 0,
            ];
            session(['payment_advice_cart' => $pay_cart]);
        }

        $cart = &$pay_cart;

        $bank_accounts = DB::table('bank_accounts')
            ->where('inactive', false)
            ->orderBy('bank_account_name')
            ->get(['id', 'bank_account_name', 'bank_curr_code']);

        $accounts = DB::table('accounts')
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $Payable_act = Setting::getSetting('payroll_payable_act', $company_id, '');

        // Unpaid payslips (no employee_trans payment advice yet)
        $unpaid_payslips = DB::table('payslip as p')
            ->join('employees as e', 'p.emp_id', '=', 'e.id')
            ->leftJoin('employee_trans as t', function ($j) {
                $j->on('t.payslip_no', '=', 'p.payslip_no')
                  ->where('t.payslip_no', '!=', 0);
            })
            ->whereNull('t.id')
            ->where('e.is_active', true)
            ->selectRaw('p.payslip_no, p.generated_date, p.from_date, p.to_date, p.payable_amount, e.id AS emp_id, CONCAT(e.first_name, \' \', e.last_name) AS emp_name')
            ->orderBy('p.payslip_no')
            ->get();

        // Handle select payslip
        if ($request->has('SelectPayslip')) {
            $payslip_no = (int)$request->input('payslip_no', 0);
            $related = $unpaid_payslips->firstWhere('payslip_no', $payslip_no);
            if ($related) {
                $cart['payslip_no'] = $payslip_no;
                $cart['person_id'] = $related->emp_id;
                $cart['person_name'] = $related->emp_name;
                $cart['pay_amount'] = (float)$related->payable_amount;
            }
        }

        // Handle generate GL items
        if ($request->has('GenerateGl')) {
            $cart['bank_account_id'] = $request->input('bank_account_id', $cart['bank_account_id']);

            if (empty($cart['bank_account_id'])) {
                $error = 'You have to select a bank account.';
            } elseif ($cart['payslip_no'] <= 0) {
                $error = 'You have to select a payslip.';
            } elseif (empty($Payable_act)) {
                $error = 'Payroll Payable account is not set in Payroll Default Settings.';
            } else {
                $bank_acct = $bank_accounts->firstWhere('id', $cart['bank_account_id']);
                $cart['gl_items'] = [];
                // Debit: payroll payable (amount goes from payable to bank)
                // In FA employee_payment.php: DR = payroll_payable_act, CR = bank_account
                // The amount is the payable amount being paid
                $cart['gl_items'][] = [
                    'code_id' => $Payable_act,
                    'description' => '',
                    'amount' => -$cart['pay_amount'], // Credit (decrease payable)
                    'memo' => $cart['memo_'],
                ];
                // We need a debit entry too - the bank account
                // But the bank account debit will be added when we know the bank's GL code
                // For now just add the payable credit
            }
        }

        // GL item CRUD
        $delete_id = $request->input('Delete');
        if ($delete_id !== null && isset($cart['gl_items'][$delete_id])) {
            unset($cart['gl_items'][$delete_id]);
            $cart['gl_items'] = array_values($cart['gl_items']);
        }

        if ($request->has('AddItem')) {
            $code_id = $request->input('code_id', '');
            $amtDebit = (float)$request->input('AmountDebit', 0);
            $amtCredit = (float)$request->input('AmountCredit', 0);

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
                    'description' => '',
                    'amount' => $amount,
                    'memo' => $request->input('LineMemo', ''),
                ];
            }
        }

        if ($request->has('UpdateItem')) {
            $edit_idx = session('pay_edit_index');
            if ($edit_idx !== null && isset($cart['gl_items'][$edit_idx])) {
                $code_id = $request->input('code_id', '');
                $amtDebit = (float)$request->input('AmountDebit', 0);
                $amtCredit = (float)$request->input('AmountCredit', 0);

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
                        'description' => '',
                        'amount' => $amount,
                        'memo' => $request->input('LineMemo', ''),
                    ];
                }
                session(['pay_edit_index' => null]);
            }
        }

        if ($request->has('CancelItemChanges')) {
            session(['pay_edit_index' => null]);
        }

        $edit_id = $request->input('Edit');
        if ($edit_id !== null && isset($cart['gl_items'][$edit_id])) {
            session(['pay_edit_index' => $edit_id]);
        }
        $edit_index = session('pay_edit_index');

        // Process
        if ($request->has('Process')) {
            $input_date = $request->input('date_');
            if ($input_date && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input_date)) {
                $cart['pay_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input_date)->format('Y-m-d');
            }
            $cart['ref'] = $request->input('ref', $cart['ref']);
            $cart['memo_'] = $request->input('memo_', $cart['memo_']);
            $cart['bank_account_id'] = $request->input('bank_account_id', $cart['bank_account_id']);

            if (count($cart['gl_items']) < 1) {
                $error = 'You must enter at least one payment line.';
            } elseif ($cart['payslip_no'] <= 0) {
                $error = 'You have to select a payslip.';
            } elseif (empty($cart['bank_account_id'])) {
                $error = 'You have to select a bank account.';
            } elseif (!$cart['pay_date']) {
                $error = 'The entered date is invalid.';
            } else {
                $total_debit = array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $cart['gl_items']));
                $total_credit = array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $cart['gl_items']));

                if (abs($total_debit - $total_credit) > 0.01) {
                    $error = 'The journal entry is not balanced (Debit: ' . number_format($total_debit, 2) . ' Credit: ' . number_format($total_credit, 2) . ')';
                } else {
                    DB::beginTransaction();
                    try {
                        // Collect allocations from previous advances
                        $allocs = [];
                        foreach ($request->all() as $k => $v) {
                            if (strlen($k) > 6 && substr($k, 0, 6) == 'amount' && $v > 0) {
                                $allocs[substr($k, 6)] = (float)$v;
                            }
                        }

                        // Create journal entry
                        $pay_date = $cart['pay_date'] ?: now()->format('Y-m-d');
                        $entry_number = $cart['ref'] ?: 'PAY-' . date('Ymd') . '-' . uniqid();
                        $jeId = DB::table('journal_entries')->insertGetId([
                            'company_id' => $company_id,
                            'entry_number' => $entry_number,
                            'entry_date' => $pay_date,
                            'reference_type' => 'employee_payment',
                            'reference_id' => null,
                            'description' => 'Payment Advice - ' . $cart['person_name'] . ' - Payslip #' . $cart['payslip_no'],
                            'total_debit' => $total_debit,
                            'total_credit' => $total_credit,
                            'is_posted' => true,
                            'posted_at' => now(),
                            'posted_by' => auth()->id() ?? 1,
                            'created_by' => auth()->id() ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        foreach ($cart['gl_items'] as $item) {
                            $account = DB::table('accounts')->where('code', $item['code_id'])->first();
                            if ($account) {
                                $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                                $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                                DB::table('journal_entry_lines')->insert([
                                    'journal_entry_id' => $jeId,
                                    'account_id' => $account->id,
                                    'description' => $item['memo'] ?? '',
                                    'debit_amount' => $debit,
                                    'credit_amount' => $credit,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        // Create bank trans record
                        DB::table('bank_trans')->insert([
                            'ref_type' => 'journal',
                            'reference_id' => $jeId,
                            'bank_account_id' => $cart['bank_account_id'],
                            'trans_date' => $pay_date,
                            'reference' => $cart['ref'],
                            'amount' => $cart['pay_amount'],
                            'memo' => $cart['memo_'] ?: 'Payment Advice - Payslip #' . $cart['payslip_no'],
                            'created_by' => auth()->id() ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create employee_trans (payment advice)
                        $etId = DB::table('employee_trans')->insertGetId([
                            'trans_no' => $jeId,
                            'trans_type' => 1,
                            'payslip_no' => $cart['payslip_no'],
                            'pay_date' => $pay_date,
                            'to_the_order_of' => $cart['person_id'],
                            'pay_amount' => $cart['pay_amount'],
                            'bank_account' => $cart['bank_account_id'],
                            'ref' => $cart['ref'],
                            'memo_' => $cart['memo_'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Write allocations if any
                        if (count($allocs) > 0) {
                            foreach ($allocs as $trans_to => $alloc_amt) {
                                DB::table('employee_advance_allocation')->insert([
                                    'trans_no_from' => $etId,
                                    'trans_no_to' => $trans_to,
                                    'amount' => $alloc_amt,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        DB::commit();

                        session(['payment_advice_cart' => null]);
                        return redirect()->route('hr.payment-advice', ['AddedID' => $jeId]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error = 'Error processing payment: ' . $e->getMessage();
                    }
                }
            }
        }

        // Handle success display
        if ($request->has('AddedID')) {
            $trans_no = $request->input('AddedID');
            $msg = 'Payment Advice #' . $trans_no . ' has been entered';
        }

        // Cancel
        if ($request->has('CancelOrder')) {
            session()->forget('payment_advice_cart');
            $pay_cart = [
                'bank_account_id' => '',
                'pay_date' => date('Y-m-d'),
                'ref' => '',
                'memo_' => '',
                'person_id' => '',
                'person_name' => '',
                'pay_amount' => 0,
                'payslip_no' => 0,
                'gl_items' => [],
                'total_payments' => 0,
            ];
            session(['payment_advice_cart' => $pay_cart]);
            $cart = &$pay_cart;
        }

        // Get employee's previous unallocated advances for this employee
        $advances = collect();
        $total_allocated = 0;
        if (!empty($cart['person_id'])) {
            $advances = DB::table('employee_trans as t')
                ->leftJoin('employee_advance as a', 't.id', '=', 'a.emp_trans_no')
                ->leftJoin('employee_advance_allocation as al', 't.id', '=', 'al.trans_no_to')
                ->where('a.emp_id', $cart['person_id'])
                ->groupBy('t.id', 't.trans_no', 't.trans_type', 't.pay_date', 't.pay_amount', 't.to_the_order_of', 't.ref')
                ->selectRaw('t.id, t.trans_no, t.trans_type, t.pay_date, t.pay_amount, COALESCE(SUM(al.amount), 0) as amt_allocated, (t.pay_amount - COALESCE(SUM(al.amount), 0)) as remain')
                ->having('remain', '>', 0)
                ->get();

            if ($advances->isNotEmpty()) {
                foreach ($advances as $adv) {
                    $alloc = (float)$request->input('amount' . $adv->id, 0);
                    $total_allocated += $alloc;
                }
            }
        }

        $total_debit = array_sum(array_map(fn($i) => $i['amount'] > 0 ? $i['amount'] : 0, $cart['gl_items'] ?? []));
        $total_credit = array_sum(array_map(fn($i) => $i['amount'] < 0 ? -$i['amount'] : 0, $cart['gl_items'] ?? []));

        session(['payment_advice_cart' => $cart]);

        return view('hr.payment-advice', compact(
            'msg', 'error', 'cart', 'edit_index', 'accounts',
            'bank_accounts', 'unpaid_payslips',
            'total_debit', 'total_credit', 'advances', 'total_allocated',
            'Payable_act'
        ));
    }
}
