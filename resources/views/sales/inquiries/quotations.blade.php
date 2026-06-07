@extends('layouts.app')

@section('title', 'Search All Sales Quotations - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Search All Sales Quotations</h2>
            <p class="mt-2 text-gray-600">Search and view sales quotations with advanced filtering options.</p>
        </div>

        <form method="GET" action="{{ route('sales.inquiries.quotations') }}">
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-filter mr-2"></i>Search Filters</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">#:</label>
                            <input type="text" name="OrderNumber" value="{{ request('OrderNumber') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ref:</label>
                            <input type="text" name="OrderReference" value="{{ request('OrderReference') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quotation date / Valid until:</label>
                            <div class="flex items-center gap-2 mt-1">
                                <label class="inline-flex items-center text-xs text-gray-700">
                                    <input type="radio" name="by_delivery" value="0" {{ !request('by_delivery') ? 'checked' : '' }} class="mr-1"> Quotation date
                                </label>
                                <label class="inline-flex items-center text-xs text-gray-700">
                                    <input type="radio" name="by_delivery" value="1" {{ request('by_delivery') == '1' ? 'checked' : '' }} class="mr-1"> Valid until
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">From:</label>
                            <input type="date" name="OrdersAfterDate" value="{{ request('OrdersAfterDate') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">To:</label>
                            <input type="date" name="OrdersToDate" value="{{ request('OrdersToDate') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Location:</label>
                            <select name="StockLocation" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Locations</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc }}" {{ request('StockLocation') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Item:</label>
                            <select name="SelectStockFromList" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Items</option>
                                @foreach($items as $it)
                                    <option value="{{ $it->code }}" {{ request('SelectStockFromList') == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Customer:</label>
                            <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Customers</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-4">
                            <label class="inline-flex items-center text-xs text-gray-700">
                                <input type="checkbox" name="show_all" value="1" {{ request('show_all') ? 'checked' : '' }} class="mr-1"> Show All
                            </label>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('sales.inquiries.quotations') }}" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </a>
                        </div>
                    </div>
                    <input type="hidden" name="order_view_mode" value="Quotations">
                    <input type="hidden" name="type" value="32">
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Sales Quotations</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-lg font-medium text-gray-900">Quotations Found: <span class="text-indigo-600 font-bold">{{ $quotations->total() }}</span></span>
                            </div>
                            <div class="text-right text-sm text-gray-600">
                                @if($quotations->total() > 0)
                                    Showing {{ $quotations->firstItem() }}-{{ $quotations->lastItem() }} of {{ $quotations->total() }} results
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quote #</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ref</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Branch</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cust Order Ref</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quote Date</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valid Until</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Delivery To</th>
                                    <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Quote Total</th>
                                    <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Currency</th>
                                    <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($quotations as $q)
                                    @php $isOverdue = $q->expiry_date && $q->expiry_date->isPast(); @endphp
                                    <tr class="{{ $isOverdue ? 'bg-red-50' : 'hover:bg-gray-50' }} transition">
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('sales.quotations.show', $q) }}" class="text-indigo-600 hover:text-indigo-900">{{ $q->quotation_number ?? '#' . $q->id }}</a>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $q->reference ?? '-' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-900">{{ $q->customer->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $q->customer?->branches?->first()?->branch_name ?? '-' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $q->cust_ref ?? '-' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $q->quotation_date ? $q->quotation_date->format('d/m/Y') : '-' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $q->expiry_date ? $q->expiry_date->format('d/m/Y') : '-' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700 max-w-[120px] truncate" title="{{ $q->deliver_to ?? '' }}">{{ $q->deliver_to ?? '-' }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-right font-medium text-gray-900">{{ number_format($q->total_amount + ($q->freight_cost ?? 0), 2) }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-center text-gray-700">USD</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-0.5">
                                                <a href="{{ route('sales.quotations.edit', $q) }}" class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                                                <a href="{{ route('sales.orders.create', ['quotation_id' => $q->id]) }}" class="p-1.5 text-green-600 hover:text-green-900 hover:bg-green-50 rounded transition" title="Sales Order"><i class="fas fa-file-invoice text-xs"></i></a>
                                                <a href="{{ route('sales.quotations.show', $q) }}" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="Print" onclick="window.open(this.href+'?print=1', '_blank'); return false;"><i class="fas fa-print text-xs"></i></a>
                                                <form action="{{ route('sales.quotations.destroy', $q) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-3 py-12 text-center text-gray-500">
                                            <i class="fas fa-file-invoice text-5xl mb-3 text-gray-300"></i>
                                            <p class="text-base font-medium text-gray-400">No quotations found</p>
                                            <p class="text-sm mt-1">Try adjusting your search filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($quotations->hasPages())
                        <div class="mt-6">
                            {{ $quotations->appends(request()->query())->links() }}
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <a href="{{ route('sales.inquiries.quotations', request()->except('order_view_mode', 'type')) }}" class="text-sm text-gray-500 hover:text-gray-700">
                            <i class="fas fa-sync-alt mr-1"></i>Update
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Quotations</p>
                            <p class="text-lg font-medium text-gray-900">{{ number_format($totalQuotations) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Accepted</p>
                            <p class="text-lg font-medium text-gray-900">{{ number_format($acceptedCount) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Pending</p>
                            <p class="text-lg font-medium text-gray-900">{{ number_format($pendingCount) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Value</p>
                            <p class="text-lg font-medium text-gray-900">${{ number_format($totalValue, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pagination { display: flex; justify-content: center; gap: 4px; flex-wrap: wrap; }
        .pagination .page-item { list-style: none; }
        .pagination .page-link { display: block; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; font-size: 14px; text-decoration: none; transition: all 0.15s; }
        .pagination .page-link:hover { background-color: #f3f4f6; }
        .pagination .active .page-link { background-color: #4f46e5; border-color: #4f46e5; color: white; }
        .pagination .disabled .page-link { color: #9ca3af; pointer-events: none; background-color: #f9fafb; }
        .pagination svg { width: 16px; height: 16px; }
        tr.bg-red-50 td { color: #dc2626; }
        tr.bg-red-50 a { color: #dc2626; }
        tr.bg-red-50 a:hover { color: #b91c1c; }
    </style>
    @endpush
@endsection