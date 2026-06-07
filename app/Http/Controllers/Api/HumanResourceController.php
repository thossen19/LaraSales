<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PayrollResource;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class HumanResourceController extends Controller
{
    // Employees
    public function employeesIndex(Request $request)
    {
        $query = Employee::with(['createdBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        if ($request->has('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->paginate(20);

        return EmployeeResource::collection($employees);
    }

    public function employeesStore(EmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'company_id' => $request->user()->company_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'hire_date' => $request->hire_date,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department' => $request->department,
                'position' => $request->position,
                'employment_type' => $request->employment_type,
                'salary' => $request->salary ?? 0,
                'hourly_rate' => $request->hourly_rate ?? 0,
                'bank_account' => $request->bank_account,
                'tax_id' => $request->tax_id,
                'social_security' => $request->social_security,
                'is_active' => $request->is_active ?? true,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            DB::commit();

            return new EmployeeResource($employee->load(['createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create employee: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function employeesShow(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new EmployeeResource($employee->load(['createdBy', 'payrolls']));
    }

    public function employeesUpdate(EmployeeRequest $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();

            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'hire_date' => $request->hire_date,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department' => $request->department,
                'position' => $request->position,
                'employment_type' => $request->employment_type,
                'salary' => $request->salary ?? $employee->salary,
                'hourly_rate' => $request->hourly_rate ?? $employee->hourly_rate,
                'bank_account' => $request->bank_account,
                'tax_id' => $request->tax_id,
                'social_security' => $request->social_security,
                'is_active' => $request->is_active ?? $employee->is_active,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return new EmployeeResource($employee->load(['createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update employee: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function terminate(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'termination_date' => 'required|date|after_or_equal:hire_date',
            'termination_reason' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $employee->update([
                'termination_date' => $request->termination_date,
                'termination_reason' => $request->termination_reason,
                'is_active' => false,
            ]);

            DB::commit();

            return new EmployeeResource($employee->load(['createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to terminate employee: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Payrolls
    public function payrollsIndex(Request $request)
    {
        $query = Payroll::with(['employee', 'createdBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('period_from')) {
            $query->whereDate('pay_period_start', '>=', $request->period_from);
        }

        if ($request->has('period_to')) {
            $query->whereDate('pay_period_end', '<=', $request->period_to);
        }

        $payrolls = $query->orderBy('pay_period_start', 'desc')->paginate(20);

        return PayrollResource::collection($payrolls);
    }

    public function payrollStore(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'payment_date' => 'required|date|after_or_equal:pay_period_end',
            'overtime_hours' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'tax_withheld' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($request->employee_id);
            
            // Calculate gross salary based on employment type
            $grossSalary = $employee->employment_type === 'hourly' 
                ? $employee->hourly_rate * 160 // Standard 160 hours per month
                : $employee->monthly_salary;

            $overtimePay = ($request->overtime_hours ?? 0) * ($employee->hourly_rate * 1.5);

            $payroll = Payroll::create([
                'company_id' => $request->user()->company_id,
                'employee_id' => $request->employee_id,
                'pay_period_start' => $request->pay_period_start,
                'pay_period_end' => $request->pay_period_end,
                'payment_date' => $request->payment_date,
                'gross_salary' => $grossSalary,
                'overtime_hours' => $request->overtime_hours ?? 0,
                'overtime_pay' => $overtimePay,
                'bonus' => $request->bonus ?? 0,
                'allowances' => $request->allowances ?? 0,
                'deductions' => $request->deductions ?? 0,
                'tax_withheld' => $request->tax_withheld ?? 0,
                'other_deductions' => $request->other_deductions ?? 0,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            DB::commit();

            return new PayrollResource($payroll->load(['employee', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create payroll: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function payrollShow(Request $request, Payroll $payroll)
    {
        if ($payroll->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new PayrollResource($payroll->load(['employee', 'createdBy']));
    }

    public function payrollProcess(Request $request, Payroll $payroll)
    {
        if ($payroll->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($payroll->status !== 'draft') {
            return response()->json(['message' => 'Only draft payrolls can be processed'], Response::HTTP_BAD_REQUEST);
        }

        $payroll->update(['status' => 'processed']);

        return new PayrollResource($payroll->load(['employee', 'createdBy']));
    }

    public function payrollPay(Request $request, Payroll $payroll)
    {
        if ($payroll->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($payroll->status !== 'processed') {
            return response()->json(['message' => 'Only processed payrolls can be marked as paid'], Response::HTTP_BAD_REQUEST);
        }

        $payroll->update(['status' => 'paid']);

        return new PayrollResource($payroll->load(['employee', 'createdBy']));
    }

    public function departments(Request $request)
    {
        $departments = Employee::where('company_id', $request->user()->company_id)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort();

        return response()->json($departments);
    }

    public function report(Request $request)
    {
        $query = Employee::where('company_id', $request->user()->company_id);

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        $employees = $query->with(['payrolls' => function ($query) use ($request) {
            if ($request->has('year')) {
                $query->whereYear('pay_period_start', $request->year);
            }
        }])->get();

        $summary = [
            'total_employees' => $employees->count(),
            'active_employees' => $employees->where('is_active', true)->count(),
            'total_payroll_cost' => $employees->sum('salary'),
            'by_department' => $employees->groupBy('department')->map(function ($deptEmployees) {
                return [
                    'count' => $deptEmployees->count(),
                    'total_salary' => $deptEmployees->sum('salary'),
                ];
            }),
            'by_employment_type' => $employees->groupBy('employment_type')->map(function ($typeEmployees) {
                return [
                    'count' => $typeEmployees->count(),
                    'total_salary' => $typeEmployees->sum('salary'),
                ];
            }),
        ];

        return response()->json([
            'summary' => $summary,
            'employees' => $employees,
        ]);
    }
}
