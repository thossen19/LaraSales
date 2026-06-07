@extends('layouts.app')

@section('title', 'Customer Details - Sales ERP')

@section('content')
    <div class="flex gap-6">
        @include('components.sales-sidebar')
        
        <div class="flex-1">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Customer Details</h2>
                <p class="mt-2 text-gray-600">View and manage customer information.</p>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $customer->name }}</h3>
                            <p class="text-sm text-gray-500">Customer Code: {{ $customer->customer_code }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('sales.customers.edit', $customer) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                <i class="fas fa-edit mr-2"></i>Edit Customer
                            </a>
                            <a href="{{ route('sales.customers.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                <i class="fas fa-arrow-left mr-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Customer Code:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->customer_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Contact Person:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->contact_person }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Phone:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->phone }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->email ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Fax:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->fax ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Tax ID:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->tax_id ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <!-- Address Information -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Address Information</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Address:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->address }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">City:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->city }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">State:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->state }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Postal Code:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->postal_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Country:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->country }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <!-- Financial Information -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Financial Information</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Credit Limit:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->formatted_credit_limit }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Payment Terms:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->payment_terms }} days</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Sales Group:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->salesGroup?->group_name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Sales Person:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->salesPerson?->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Credit Status:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->creditStatus?->status_name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <!-- Status and Notes -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Status & Notes</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                    <dd class="text-sm">
                                        @if($customer->status == 'active')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @elseif($customer->status == 'inactive')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Hold</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="mt-4">
                                    <dt class="text-sm font-medium text-gray-500">Notes:</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $customer->notes ?? 'No notes available' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <!-- Customer Branches -->
                    @if($customer->branches->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Customer Branches</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($customer->branches as $branch)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->branch_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->contact_person }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->phone }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->address }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->city }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
