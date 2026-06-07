@extends('layouts.app')

@section('title', 'Sales Quotations - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Sales Quotations</h2>
            <p class="mt-2 text-gray-600">Manage and track all sales quotations.</p>
        </div>

            <div class="bg-white shadow rounded-lg">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Quotations List</h3>
                    <a href="{{ route('sales.quotations.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Create Quotation
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Person</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quotations as $quotation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quotation->quotation_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quotation->quotation_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quotation->customer->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quotation->salesPerson?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quotation->formatted_total }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($quotation->status == 'draft')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                        @elseif($quotation->status == 'sent')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sent</span>
                                        @elseif($quotation->status == 'accepted')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepted</span>
                                        @elseif($quotation->status == 'rejected')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Expired</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('sales.quotations.show', $quotation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a href="{{ route('sales.quotations.edit', $quotation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('sales.quotations.destroy', $quotation) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-file-contract text-6xl mb-4"></i>
                                        <p class="text-lg">No quotations yet</p>
                                        <p class="text-sm mt-2">Create your first quotation to get started</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($quotations->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $quotations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
