@extends('layouts.app')

@section('title', 'Inventory Transactions - Sales ERP')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Inventory Transactions</h2>
        <p class="mt-2 text-gray-600">Track all inventory movements and adjustments.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction History</h3>
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-exchange-alt text-6xl mb-4"></i>
            <p class="text-lg">No transactions yet</p>
        </div>
    </div>
@endsection
