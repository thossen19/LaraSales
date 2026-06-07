@extends('layouts.app')

@section('title', 'Sales Reports - Sales ERP')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Sales Reports</h2>
        <p class="mt-2 text-gray-600">View and analyze your sales performance and trends.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Overview</h3>
            <div class="h-64 flex items-center justify-center text-gray-500">
                <i class="fas fa-chart-line text-4xl mb-2"></i>
                <p>Sales chart will appear here</p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Products</h3>
            <div class="h-64 flex items-center justify-center text-gray-500">
                <i class="fas fa-box text-4xl mb-2"></i>
                <p>Product rankings will appear here</p>
            </div>
        </div>
    </div>
@endsection
