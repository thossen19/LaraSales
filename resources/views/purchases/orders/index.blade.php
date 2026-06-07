@extends('layouts.app')

@section('title', 'Purchase Orders - Sales ERP')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Purchase Orders</h2>
        <p class="mt-2 text-gray-600">Manage purchase orders and supplier relationships.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-900">Purchase Orders List</h3>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>New Purchase Order
            </button>
        </div>
        
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-file-invoice text-6xl mb-4"></i>
            <p class="text-lg">No purchase orders yet</p>
        </div>
    </div>
@endsection
