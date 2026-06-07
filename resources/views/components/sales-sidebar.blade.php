<!-- Sales Module Sidebar -->
<div class="bg-white shadow-lg rounded-lg p-4">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Module</h3>
    
    <!-- Transactions -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-3 uppercase tracking-wider">Transactions</h4>
        <div class="space-y-1">
            <a href="{{ route('sales.quotations.create') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-file-alt mr-2"></i>Sales Quotation Entry
            </a>
            <a href="{{ route('sales.orders.create') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-file-invoice mr-2"></i>Sales Order Entry
            </a>
            <a href="{{ route('sales.delivery.direct') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-truck mr-2"></i>Direct Delivery
            </a>
            <a href="{{ route('sales.invoice.direct') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Direct Invoice
            </a>
            <a href="{{ route('sales.delivery.from-order') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-exchange-alt mr-2"></i>Delivery Against Sales Orders
            </a>
            <a href="{{ route('sales.invoice.from-delivery') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-file-invoice mr-2"></i>Invoice Against Sales Delivery
            </a>
            <a href="{{ route('sales.invoice.prepaid') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>Invoice Prepaid Orders
            </a>
            <a href="{{ route('sales.delivery.template') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-copy mr-2"></i>Template Delivery
            </a>
            <a href="{{ route('sales.invoice.template') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-file-alt mr-2"></i>Template Invoice
            </a>
            <a href="{{ route('sales.invoice.recurrent') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-sync mr-2"></i>Create and Print Recurrent Invoices
            </a>
        </div>
    </div>

    <!-- Payments & Credits -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-3 uppercase tracking-wider">Payments & Credits</h4>
        <div class="space-y-1">
            <a href="{{ route('sales.payments.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-money-bill-wave mr-2"></i>Customer Payments
            </a>
            <a href="{{ route('sales.credit-notes.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-receipt mr-2"></i>Customer Credit Notes
            </a>
            <a href="{{ route('sales.allocation.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-link mr-2"></i>Allocate Customer Payments or Credit Notes
            </a>
        </div>
    </div>

    <!-- Inquiries -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-3 uppercase tracking-wider">Inquiries</h4>
        <div class="space-y-1">
            <a href="{{ route('sales.inquiries.quotations') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-search mr-2"></i>Sales Quotation Inquiry
            </a>
            <a href="{{ route('sales.inquiries.orders') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-search mr-2"></i>Sales Order Inquiry
            </a>
            <a href="{{ route('sales.inquiries.transactions') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-search mr-2"></i>Customer Transaction Inquiry
            </a>
            <a href="{{ route('sales.inquiries.allocation') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-search mr-2"></i>Customer Allocation Inquiry
            </a>
        </div>
    </div>

    <!-- Reports -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-3 uppercase tracking-wider">Reports</h4>
        <div class="space-y-1">
            <a href="{{ route('sales.reports.customer') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-chart-bar mr-2"></i>Customer Reports
            </a>
            <a href="{{ route('sales.reports.sales') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-chart-line mr-2"></i>Sales Reports
            </a>
        </div>
    </div>

    <!-- Customer Management -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-3 uppercase tracking-wider">Customer Management</h4>
        <div class="space-y-1">
            <a href="{{ route('sales.customers.index') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-users mr-2"></i>Add and Manage Customers
            </a>
            <a href="{{ route('sales.customers.branches') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-code-branch mr-2"></i>Customer Branches
            </a>
        </div>
    </div>

    <!-- Setup -->
    <div class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-3 uppercase tracking-wider">Setup</h4>
        <div class="space-y-1">
            <a href="{{ route('sales.setup.groups') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-layer-group mr-2"></i>Sales Groups
            </a>
            <a href="{{ route('sales.setup.recurrent-invoices') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-sync mr-2"></i>Recurrent Invoices
            </a>
            <a href="{{ route('sales.setup.types') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-tags mr-2"></i>Sales Types
            </a>
            <a href="{{ route('sales.setup.persons') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-user-tie mr-2"></i>Sales Persons
            </a>
            <a href="{{ route('sales.setup.areas') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-map-marked-alt mr-2"></i>Sales Areas
            </a>
            <a href="{{ route('sales.setup.credit-status') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>Credit Status Setup
            </a>
        </div>
    </div>
</div>
