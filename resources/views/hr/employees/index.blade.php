@extends('layouts.app')
@section('title', 'Manage Employees')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Employees</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif
@if($upload_error)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">{{ $upload_error }}</div>
@endif

<form method="POST" action="{{ route('hr.employees.index') }}" enctype="multipart/form-data">
@csrf

<div class="bg-white shadow rounded-lg mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex">
            <button type="submit" name="_tabs_sel" value="list"
                class="px-6 py-3 text-sm font-medium {{ $tab == 'list' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                Employees List
            </button>
            <button type="submit" name="_tabs_sel" value="add"
                class="px-6 py-3 text-sm font-medium {{ $tab == 'add' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                Add/Edit Employee
            </button>
        </nav>
    </div>

    <div class="p-6">
        @if($tab == 'list')
        <div class="mb-4 flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Enter Search String:</label>
                <input type="text" name="string" value="{{ $search_string }}" placeholder="Enter fragment or leave empty"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">&nbsp;</label>
                <select name="DeptId"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All departments</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->dept_id }}" {{ $dept_filter == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">&nbsp;</label>
                <select name="position"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Positions</option>
                    @foreach($positions_list as $p)
                    <option value="{{ $p->position_id }}" {{ $position_filter == $p->position_id ? 'selected' : '' }}>{{ $p->position_name }}</option>
                    @endforeach
                </select>
            </div>
            @if($grades_no > 0)
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">&nbsp;</label>
                <select name="grade"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Grades</option>
                    @for($i = 1; $i <= $grades_no; $i++)
                    <option value="{{ $i }}" {{ $grade_filter == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            @endif
            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Show resigned:</span>
                </label>
            </div>
            <div>
                <button type="submit" name="Search" value="1"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Search
                </button>
            </div>
        </div>

        @if($employees->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mobile</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Birth</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hired Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employees as $e)
                    <tr class="hover:bg-gray-50 {{ !$e->is_active ? 'text-gray-400' : '' }}">
                        <td class="px-4 py-2 text-sm">
                            <button type="submit" name="{{ $e->id }}" value="1" class="text-indigo-600 hover:text-indigo-900">{{ $e->id }}</button>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <button type="submit" name="{{ $e->id }}" value="1" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                {{ $e->first_name }} {{ $e->last_name }}
                            </button>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            @if($e->gender == 0) Female @elseif($e->gender == 1) Male @else Other @endif
                        </td>
                        <td class="px-4 py-2 text-sm">{{ $e->phone }}</td>
                        <td class="px-4 py-2 text-sm">{{ $e->email }}</td>
                        <td class="px-4 py-2 text-sm">{{ $e->birth_date ? date('d/m/Y', strtotime($e->birth_date)) : '' }}</td>
                        <td class="px-4 py-2 text-sm">{{ $e->hire_date && $e->hire_date != '0000-00-00' ? date('d/m/Y', strtotime($e->hire_date)) : 'Not hired' }}</td>
                        <td class="px-4 py-2 text-sm">{{ $e->dept_name ?? ($e->department ?? 'Not selected') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $employees->appends(request()->all())->links() }}
        </div>
        @else
        <div class="text-center py-8 text-gray-500">No employee defined.</div>
        @endif

        @else {{-- tab == 'add' --}}

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Section 1: Image + Personal Info --}}
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image File:</label>
                    <input type="file" name="pic" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div class="mb-4">
                    @php
                        $img_path = $cur_id ? 'storage/employee_photos/' . $cur_id . '.jpg' : '';
                    @endphp
                    @if($cur_id && file_exists(public_path($img_path)))
                    <img src="{{ asset($img_path) }}" alt="{{ $cur_id }}.jpg" height="100" class="mb-2">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="del_image" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Delete Image:</span>
                    </label>
                    @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(($employee->first_name ?? 'N') . '+' . ($employee->last_name ?? 'A')) }}&size=100&background=6366f1&color=fff" alt="avatar" height="100">
                    @endif
                </div>

                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Personal Information</h3>

                @if($cur_id)
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Employee Id:</label>
                    <span class="text-sm text-gray-900">{{ $cur_id }}</span>
                </div>
                @endif

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name:</label>
                    <input type="text" name="emp_first_name" value="{{ old('emp_first_name', $employee->first_name ?? '') }}" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name:</label>
                    <input type="text" name="emp_last_name" value="{{ old('emp_last_name', $employee->last_name ?? '') }}" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender:</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="0" {{ old('gender', $employee->gender ?? '1') == '0' ? 'checked' : '' }}
                                class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Female</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="1" {{ old('gender', $employee->gender ?? '1') == '1' ? 'checked' : '' }}
                                class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Male</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="2" {{ old('gender', $employee->gender ?? '1') == '2' ? 'checked' : '' }}
                                class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Other</span>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
                    <textarea name="emp_address" rows="5" cols="31"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('emp_address', $employee->address ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile:</label>
                    <input type="text" name="emp_mobile" value="{{ old('emp_mobile', $employee->phone ?? '') }}" maxlength="30" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">e-Mail:</label>
                    <input type="email" name="emp_email" value="{{ old('emp_email', $employee->email ?? '') }}" maxlength="100" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date:</label>
                    <input type="text" name="emp_birthdate" value="{{ old('emp_birthdate', $employee && $employee->birth_date ? date('d/m/Y', strtotime($employee->birth_date)) : '') }}" placeholder="dd/mm/yyyy"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Section 2: Personal Info (cont) + Job Info --}}
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Personal Information</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">National ID:</label>
                    <input type="text" name="national_id" value="{{ old('national_id', $employee->national_id ?? '') }}" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passport:</label>
                    <input type="text" name="passport" value="{{ old('passport', $employee->passport ?? '') }}" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name/Account:</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account', $employee->bank_account ?? '') }}" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax ID Number:</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $employee->tax_id ?? '') }}" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200 mt-6">Job Information</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes:</label>
                    <textarea name="emp_notes" rows="5" cols="31"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('emp_notes', $employee->notes ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hire Date:</label>
                    <input type="text" name="emp_hiredate" value="{{ old('emp_hiredate', $employee && $employee->hire_date && $employee->hire_date != '0000-00-00' ? date('d/m/Y', strtotime($employee->hire_date)) : '') }}" placeholder="dd/mm/yyyy"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department:</label>
                    @if($cur_id && $employee && $employee->hire_date && $employee->hire_date != '0000-00-00')
                    <select name="department_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not selected</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}" {{ old('department_id', $employee->department_id ?? '') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                        @endforeach
                    </select>
                    @elseif($cur_id)
                    <span class="text-sm text-gray-500">Set hire date first</span>
                    <input type="hidden" name="department_id" value="">
                    @else
                    <select name="department_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not selected</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}" {{ old('department_id', $employee->department_id ?? '') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Position:</label>
                    <select name="position_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not selected</option>
                        @foreach($positions_list as $p)
                        <option value="{{ $p->position_id }}" {{ old('position_id', $employee->position_id ?? '') == $p->position_id ? 'selected' : '' }}>{{ $p->position_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Grade:</label>
                    <select name="grade_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Basic</option>
                        @for($i = 1; $i <= $grades_no; $i++)
                        <option value="{{ $i }}" {{ old('grade_id', $employee->grade_id ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                @if($cur_id)
                <div class="mb-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="inactive" value="1" {{ old('inactive', $employee && !$employee->is_active ? true : false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Resigned:</span>
                    </label>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Release Date:</label>
                    <input type="text" name="emp_releasedate" value="{{ old('emp_releasedate', $employee && $employee->termination_date && $employee->termination_date != '0000-00-00' ? date('d/m/Y', strtotime($employee->termination_date)) : '') }}" placeholder="dd/mm/yyyy"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @else
                <input type="hidden" name="inactive" value="0">
                <input type="hidden" name="emp_releasedate" value="">
                @endif
            </div>

            {{-- Section 3: Pay Elements --}}
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Pay Elements</h3>

                <div class="text-xs text-gray-500 mb-3 italic" title="Enter negative amount for deduction, positive for earning">(?)</div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Use Personal Salary Structure:</label>
                    <select name="personal_salary"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="0" {{ old('personal_salary', $employee->personal_salary ?? false) ? '' : 'selected' }}>No</option>
                        <option value="1" {{ old('personal_salary', $employee->personal_salary ?? false) ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                @php
                    $basic_sal = $personal_salaries->first(function($v) { return $v->is_basic; });
                    $basic_amt_val = $basic_sal ? $basic_sal->pay_amount : 0;
                @endphp
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary Amount:</label>
                    <input type="text" name="basic_amt" value="{{ old('basic_amt', number_format($basic_amt_val, 2)) }}"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                @foreach($pay_elements as $el)
                @php
                    $amt_val = old('amt_' . $el->account_code, isset($personal_salaries[$el->account_code]) ? $personal_salaries[$el->account_code]->pay_amount : 0);
                @endphp
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $el->element_name }}:</label>
                    <input type="text" name="amt_{{ $el->account_code }}" value="{{ old('amt_' . $el->account_code, number_format($amt_val, 2)) }}"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 flex items-center gap-4">
            @if($cur_id)
            <button type="submit" name="addupdate" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update Employee
            </button>
            <a href="{{ route('hr.employees.index') }}?tab=list"
                class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">
                Cancel
            </a>
            <button type="submit" name="delete" value="1"
                class="px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50 transition duration-150"
                onclick="return confirm('Delete this employee?')">
                Delete Employee
            </button>
            @else
            <button type="submit" name="addupdate" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Add New Employee Details
            </button>
            @endif
        </div>
        @endif
    </div>
</div>

</form>
@endsection
