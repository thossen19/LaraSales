<!-- Sidebar -->
<aside class="w-64 bg-gray-800 text-white min-h-screen flex-shrink-0">
    <!-- Logo -->
    <div class="flex items-center h-16 px-6">
        <div class="h-8 w-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
            <i class="fas fa-chart-line text-white text-sm"></i>
        </div>
        <h1 class="text-lg font-bold text-white">Sales ERP</h1>
    </div>

    <!-- Navigation -->
    <nav class="mt-6">
        <div class="px-4 space-y-2">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                <i class="fas fa-tachometer-alt text-gray-300 mr-3"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Submenu: Sales -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleSalesMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Sales Module</span>
                    <i id="salesMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="salesMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('sales.quotations.create') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.quotations.create') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-alt text-gray-400 mr-3"></i>
                        <span>Sales Quotation Entry</span>
                    </a>
                    <a href="{{ route('sales.orders.create') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.orders.create') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-invoice text-gray-400 mr-3"></i>
                        <span>Sales Order Entry</span>
                    </a>
                    <a href="{{ route('sales.delivery.direct') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.delivery.direct') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-truck text-gray-400 mr-3"></i>
                        <span>Direct Delivery</span>
                    </a>
                    <a href="{{ route('sales.invoice.direct') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.invoice.direct') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-invoice-dollar text-gray-400 mr-3"></i>
                        <span>Direct Invoice</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('sales.delivery.from-order') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.delivery.from-order') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-exchange-alt text-gray-400 mr-3"></i>
                        <span>Delivery Against Sales Orders</span>
                    </a>
                    <a href="{{ route('sales.invoice.from-delivery') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.invoice.from-delivery') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-invoice text-gray-400 mr-3"></i>
                        <span>Invoice Against Sales Delivery</span>
                    </a>
                    <a href="{{ route('sales.invoice.prepaid') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.invoice.prepaid') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-credit-card text-gray-400 mr-3"></i>
                        <span>Invoice Prepaid Orders</span>
                    </a>
                    <a href="{{ route('sales.delivery.template') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.delivery.template') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-copy text-gray-400 mr-3"></i>
                        <span>Template Delivery</span>
                    </a>
                    <a href="{{ route('sales.invoice.template') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.invoice.template') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-alt text-gray-400 mr-3"></i>
                        <span>Template Invoice</span>
                    </a>
                    <a href="{{ route('sales.invoice.recurrent') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.invoice.recurrent') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-sync text-gray-400 mr-3"></i>
                        <span>Create and Print Recurrent Invoices</span>
                    </a>
                    <a href="{{ route('sales.payments.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.payments.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-money-bill-wave text-gray-400 mr-3"></i>
                        <span>Customer Payments</span>
                    </a>
                    <a href="{{ route('sales.credit-notes.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.credit-notes.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-receipt text-gray-400 mr-3"></i>
                        <span>Customer Credit Notes</span>
                    </a>
                    <a href="{{ route('sales.allocation.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.allocation.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-link text-gray-400 mr-3"></i>
                        <span>Allocate Customer Payments or Credit Notes</span>
                    </a>
                    <a href="{{ route('sales.inquiries.quotations') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.inquiries.quotations') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Sales Quotation Inquiry</span>
                    </a>
                    <a href="{{ route('sales.inquiries.orders') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.inquiries.orders') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Sales Order Inquiry</span>
                    </a>
                    <a href="{{ route('sales.inquiries.transactions') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.inquiries.transactions') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Customer Transaction Inquiry</span>
                    </a>
                    <a href="{{ route('sales.inquiries.allocation') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.inquiries.allocation') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Customer Allocation Inquiry</span>
                    </a>
                    <a href="{{ route('sales.reports.customer') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.reports.customer') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-users text-gray-400 mr-3"></i>
                        <span>Customer and Sales Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('sales.customers.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.customers.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-users text-gray-400 mr-3"></i>
                        <span>Add and Manage Customers</span>
                    </a>
                    <a href="{{ route('sales.customers.branches') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.customers.branches') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-code-branch text-gray-400 mr-3"></i>
                        <span>Customer Branches</span>
                    </a>
                    <a href="{{ route('sales.setup.groups') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.setup.groups') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-layer-group text-gray-400 mr-3"></i>
                        <span>Sales Groups</span>
                    </a>
                    <a href="{{ route('sales.setup.recurrent-invoices') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.setup.recurrent-invoices') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-sync text-gray-400 mr-3"></i>
                        <span>Recurrent Invoices</span>
                    </a>
                    <a href="{{ route('sales.setup.types') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.setup.types') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-tag text-gray-400 mr-3"></i>
                        <span>Sales Types</span>
                    </a>
                    <a href="{{ route('sales.setup.persons') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.setup.persons') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                        <span>Sales Persons</span>
                    </a>
                    <a href="{{ route('sales.setup.areas') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.setup.areas') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-map-marked-alt text-gray-400 mr-3"></i>
                        <span>Sales Areas</span>
                    </a>
                    <a href="{{ route('sales.setup.credit-status') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.setup.credit-status') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-credit-card text-gray-400 mr-3"></i>
                        <span>Credit Status Setup</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Purchases -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="togglePurchasesMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Purchases Module</span>
                    <i id="purchasesMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="purchasesMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('purchases.orders.create') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.orders.create') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-invoice text-gray-400 mr-3"></i>
                        <span>Purchase Order Entry</span>
                    </a>
                    <a href="{{ route('purchases.orders.outstanding') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.orders.outstanding') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-clipboard-list text-gray-400 mr-3"></i>
                        <span>Outstanding Purchase Orders Maintenance</span>
                    </a>
                    <a href="{{ route('purchases.grn.direct') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.grn.direct') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-boxes text-gray-400 mr-3"></i>
                        <span>Direct GRN</span>
                    </a>
                    <a href="{{ route('purchases.invoice.direct') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.invoice.direct') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-invoice-dollar text-gray-400 mr-3"></i>
                        <span>Direct Supplier Invoice</span>
                    </a>
                    <a href="{{ route('purchases.payments.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.payments.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-money-bill-wave text-gray-400 mr-3"></i>
                        <span>Payments to Suppliers</span>
                    </a>
                    <a href="{{ route('purchases.invoices.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.invoices.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-invoice text-gray-400 mr-3"></i>
                        <span>Supplier Invoices</span>
                    </a>
                    <a href="{{ route('purchases.credit-notes.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.credit-notes.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-receipt text-gray-400 mr-3"></i>
                        <span>Supplier Credit Notes</span>
                    </a>
                    <a href="{{ route('purchases.allocation.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.allocation.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-link text-gray-400 mr-3"></i>
                        <span>Allocate Supplier Payments or Credit Notes</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('purchases.inquiries.orders') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.inquiries.orders') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Purchase Orders Inquiry</span>
                    </a>
                    <a href="{{ route('purchases.inquiries.transactions') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.inquiries.transactions') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Supplier Transaction Inquiry</span>
                    </a>
                    <a href="{{ route('purchases.inquiries.allocation') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.inquiries.allocation') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Supplier Allocation Inquiry</span>
                    </a>
                    <a href="{{ route('purchases.reports.supplier') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.reports.supplier') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-chart-bar text-gray-400 mr-3"></i>
                        <span>Supplier and Purchasing Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('purchases.suppliers.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchases.suppliers.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-truck text-gray-400 mr-3"></i>
                        <span>Suppliers</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Inventory -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleInventoryMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Items and Inventory</span>
                    <i id="inventoryMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="inventoryMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.transfers') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.transfers') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-exchange-alt text-gray-400 mr-3"></i>
                        <span>Inventory Location Transfers</span>
                    </a>
                    <a href="{{ route('inventory.adjust') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.adjust') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-adjust text-gray-400 mr-3"></i>
                        <span>Inventory Adjustments</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.inquiries.movements') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.inquiries.movements') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-arrows-alt text-gray-400 mr-3"></i>
                        <span>Inventory Item Movements</span>
                    </a>
                    <a href="{{ route('inventory.inquiries.status') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.inquiries.status') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-info-circle text-gray-400 mr-3"></i>
                        <span>Inventory Item Status</span>
                    </a>
                    <a href="{{ route('inventory.reports.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.reports.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-chart-bar text-gray-400 mr-3"></i>
                        <span>Inventory Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.items.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.items.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-box text-gray-400 mr-3"></i>
                        <span>Items</span>
                    </a>
                    <a href="{{ route('inventory.items.foreign-codes') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.items.foreign-codes') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-barcode text-gray-400 mr-3"></i>
                        <span>Foreign Item Codes</span>
                    </a>
                    <a href="{{ route('inventory.items.sales-kits') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.items.sales-kits') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-cubes text-gray-400 mr-3"></i>
                        <span>Sales Kits</span>
                    </a>
                    <a href="{{ route('inventory.items.categories') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.items.categories') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-tags text-gray-400 mr-3"></i>
                        <span>Item Categories</span>
                    </a>
                    <a href="{{ route('inventory.locations') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.locations') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-warehouse text-gray-400 mr-3"></i>
                        <span>Inventory Locations</span>
                    </a>
                    <a href="{{ route('inventory.units-of-measure') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.units-of-measure') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-ruler text-gray-400 mr-3"></i>
                        <span>Units of Measure</span>
                    </a>
                    <a href="{{ route('inventory.reorder-levels') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.reorder-levels') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-thermometer-half text-gray-400 mr-3"></i>
                        <span>Reorder Levels</span>
                    </a>
                    <a href="{{ route('inventory.items.import-csv') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.items.import-csv') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-file-csv text-gray-400 mr-3"></i>
                        <span>Import CSV Items</span>
                    </a>
                </div>

                <!-- Pricing and Costs -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Pricing and Costs</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.pricing.sales') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.pricing.sales') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-tag text-gray-400 mr-3"></i>
                        <span>Sales Pricing</span>
                    </a>
                    <a href="{{ route('inventory.pricing.purchasing') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.pricing.purchasing') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-shopping-cart text-gray-400 mr-3"></i>
                        <span>Purchasing Pricing</span>
                    </a>
                    <a href="{{ route('inventory.pricing.standard-costs') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.pricing.standard-costs') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-calculator text-gray-400 mr-3"></i>
                        <span>Standard Costs</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Fixed Assets -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleFixedAssetsMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Fixed Assets Module</span>
                    <i id="fixedAssetsMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="fixedAssetsMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('fixed-assets.purchase') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.purchase') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-cart-plus text-gray-400 mr-3"></i>
                        <span>Fixed Assets Purchase</span>
                    </a>
                    <a href="{{ route('fixed-assets.transfers') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.transfers') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-exchange-alt text-gray-400 mr-3"></i>
                        <span>Fixed Assets Location Transfers</span>
                    </a>
                    <a href="{{ route('fixed-assets.disposal') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.disposal') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-trash-alt text-gray-400 mr-3"></i>
                        <span>Fixed Assets Disposal</span>
                    </a>
                    <a href="{{ route('fixed-assets.sale') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.sale') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-hand-holding-usd text-gray-400 mr-3"></i>
                        <span>Fixed Assets Sale</span>
                    </a>
                    <a href="{{ route('fixed-assets.depreciation') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.depreciation') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-calculator text-gray-400 mr-3"></i>
                        <span>Process Depreciation</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('fixed-assets.inquiries.movements') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.inquiries.movements') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-arrows-alt text-gray-400 mr-3"></i>
                        <span>Fixed Assets Movements</span>
                    </a>
                    <a href="{{ route('fixed-assets.inquiries.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.inquiries.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Fixed Assets Inquiry</span>
                    </a>
                    <a href="{{ route('fixed-assets.reports.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.reports.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-chart-bar text-gray-400 mr-3"></i>
                        <span>Fixed Assets Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('fixed-assets.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-building text-gray-400 mr-3"></i>
                        <span>Fixed Assets</span>
                    </a>
                    <a href="{{ route('fixed-assets.locations') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.locations') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-3"></i>
                        <span>Fixed Assets Locations</span>
                    </a>
                    <a href="{{ route('fixed-assets.categories') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.categories') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-tags text-gray-400 mr-3"></i>
                        <span>Fixed Assets Categories</span>
                    </a>
                    <a href="{{ route('fixed-assets.classes') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('fixed-assets.classes') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-sitemap text-gray-400 mr-3"></i>
                        <span>Fixed Assets Classes</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Dimensions -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleDimensionsMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Dimensions Module</span>
                    <i id="dimensionsMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="dimensionsMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('dimensions.entry') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dimensions.entry') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-cube text-gray-400 mr-3"></i>
                        <span>Dimension Entry</span>
                    </a>
                    <a href="{{ route('dimensions.outstanding') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dimensions.outstanding') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-clipboard-list text-gray-400 mr-3"></i>
                        <span>Outstanding Dimensions</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('dimensions.inquiries.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dimensions.inquiries.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-search text-gray-400 mr-3"></i>
                        <span>Dimension Inquiry</span>
                    </a>
                    <a href="{{ route('dimensions.reports.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dimensions.reports.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-chart-bar text-gray-400 mr-3"></i>
                        <span>Dimension Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('dimensions.tags') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dimensions.tags') ? 'bg-gray-900' : 'hover:bg-gray-700' }} transition duration-150 ease-in-out">
                        <i class="fas fa-tags text-gray-400 mr-3"></i>
                        <span>Dimension Tags</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Banking and General Ledger -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleBankingMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Banking and General Ledger</span>
                    <i id="bankingMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="bankingMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('banking.payments') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.payments') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-money-bill-wave text-gray-400 mr-3"></i><span>Payments</span>
                    </a>
                    <a href="{{ route('banking.deposits') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.deposits') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-piggy-bank text-gray-400 mr-3"></i><span>Deposits</span>
                    </a>
                    <a href="{{ route('banking.transfers') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.transfers') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-exchange-alt text-gray-400 mr-3"></i><span>Bank Account Transfers</span>
                    </a>
                    <a href="{{ route('banking.journal.entry') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.journal.entry') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-book text-gray-400 mr-3"></i><span>Journal Entry</span>
                    </a>
                    <a href="{{ route('banking.budget-entry') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.budget-entry') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-calculator text-gray-400 mr-3"></i><span>Budget Entry</span>
                    </a>
                    <a href="{{ route('banking.reconcile') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.reconcile') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-check-double text-gray-400 mr-3"></i><span>Reconcile Bank Account</span>
                    </a>
                    <a href="{{ route('banking.accruals') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.accruals') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-clock text-gray-400 mr-3"></i><span>Revenue / Costs Accruals</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('banking.inquiries.journal') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.inquiries.journal') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-search text-gray-400 mr-3"></i><span>Journal Inquiry</span>
                    </a>
                    <a href="{{ route('banking.inquiries.gl') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.inquiries.gl') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-book-open text-gray-400 mr-3"></i><span>GL Inquiry</span>
                    </a>
                    <a href="{{ route('banking.inquiries.bank-account') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.inquiries.bank-account') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-university text-gray-400 mr-3"></i><span>Bank Account Inquiry</span>
                    </a>
                    <a href="{{ route('banking.inquiries.tax') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.inquiries.tax') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-invoice-dollar text-gray-400 mr-3"></i><span>Tax Inquiry</span>
                    </a>
                    <a href="{{ route('banking.inquiries.tax-cash') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.inquiries.tax-cash') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-cash-register text-gray-400 mr-3"></i><span>Tax Inquiry (Cash Basis)</span>
                    </a>
                    <a href="{{ route('banking.reports.trial-balance') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.reports.trial-balance') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-balance-scale text-gray-400 mr-3"></i><span>Trial Balance</span>
                    </a>
                    <a href="{{ route('banking.reports.balance-sheet') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.reports.balance-sheet') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-invoice text-gray-400 mr-3"></i><span>Balance Sheet Drilldown</span>
                    </a>
                    <a href="{{ route('banking.reports.profit-loss') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.reports.profit-loss') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-chart-line text-gray-400 mr-3"></i><span>Profit and Loss Drilldown</span>
                    </a>
                    <a href="{{ route('banking.reports.banking') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.reports.banking') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-chart-pie text-gray-400 mr-3"></i><span>Banking Reports</span>
                    </a>
                    <a href="{{ route('banking.reports.gl') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.reports.gl') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-book text-gray-400 mr-3"></i><span>General Ledger Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('banking.accounts') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.accounts') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-university text-gray-400 mr-3"></i><span>Bank Accounts</span>
                    </a>
                    <a href="{{ route('banking.quick-entries') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.quick-entries') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-bolt text-gray-400 mr-3"></i><span>Quick Entries</span>
                    </a>
                    <a href="{{ route('banking.account-tags') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.account-tags') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-tags text-gray-400 mr-3"></i><span>Account Tags</span>
                    </a>
                    <a href="{{ route('banking.currencies') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.currencies') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-money-bill text-gray-400 mr-3"></i><span>Currencies</span>
                    </a>
                    <a href="{{ route('banking.exchange-rates') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.exchange-rates') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-dollar-sign text-gray-400 mr-3"></i><span>Exchange Rates</span>
                    </a>
                    <a href="{{ route('banking.gl-accounts') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.gl-accounts') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-book text-gray-400 mr-3"></i><span>GL Accounts</span>
                    </a>
                    <a href="{{ route('banking.gl-groups') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.gl-groups') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-layer-group text-gray-400 mr-3"></i><span>GL Account Groups</span>
                    </a>
                    <a href="{{ route('banking.gl-classes') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.gl-classes') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-sitemap text-gray-400 mr-3"></i><span>GL Account Classes</span>
                    </a>
                    <a href="{{ route('banking.closing') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.closing') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-lock text-gray-400 mr-3"></i><span>Closing GL Transactions</span>
                    </a>
                    <a href="{{ route('banking.revaluation') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.revaluation') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-sync text-gray-400 mr-3"></i><span>Revaluation of Currency Accounts</span>
                    </a>
                    <a href="{{ route('banking.journal.import') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('banking.journal.import') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-import text-gray-400 mr-3"></i><span>Import Multiple Journal Entries</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Manufacturing -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleManufacturingMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Manufacturing Module</span>
                    <i id="manufacturingMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="manufacturingMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('manufacturing.work-order-entry') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.work-order-entry') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-clipboard-list text-gray-400 mr-3"></i><span>Work Order Entry</span>
                    </a>
                    <a href="{{ route('manufacturing.outstanding-work-orders') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.outstanding-work-orders') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-tasks text-gray-400 mr-3"></i><span>Outstanding Work Orders</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('manufacturing.costed-bom-inquiry') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.costed-bom-inquiry') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-dollar-sign text-gray-400 mr-3"></i><span>Costed Bill Of Material Inquiry</span>
                    </a>
                    <a href="{{ route('manufacturing.item-where-used') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.item-where-used') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-search text-gray-400 mr-3"></i><span>Inventory Item Where Used Inquiry</span>
                    </a>
                    <a href="{{ route('manufacturing.work-order-inquiry') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.work-order-inquiry') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-eye text-gray-400 mr-3"></i><span>Work Order Inquiry</span>
                    </a>
                    <a href="{{ route('manufacturing.reports') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.reports') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-chart-bar text-gray-400 mr-3"></i><span>Manufacturing Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('manufacturing.bom.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.bom.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-sitemap text-gray-400 mr-3"></i><span>Bills Of Material</span>
                    </a>
                    <a href="{{ route('manufacturing.work-centers') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('manufacturing.work-centers') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-cogs text-gray-400 mr-3"></i><span>Work Centres</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: HR -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleHRMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Human Resources Module</span>
                    <i id="hrMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="hrMenuContent" class="space-y-1">
                <!-- Transactions -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('hr.attendance') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.attendance') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-clipboard-check text-gray-400 mr-3"></i><span>Attendance</span>
                    </a>
                    <a href="{{ route('hr.payslips') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.payslips') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-invoice-dollar text-gray-400 mr-3"></i><span>Payslip Entry</span>
                    </a>
                    <a href="{{ route('hr.document-expiration') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.document-expiration') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-times text-gray-400 mr-3"></i><span>Document Expiration</span>
                    </a>
                    <a href="{{ route('hr.payment-advice') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.payment-advice') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-money-bill text-gray-400 mr-3"></i><span>Payment Advice</span>
                    </a>
                    <a href="{{ route('hr.employee-advances') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.employee-advances') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-hand-holding-usd text-gray-400 mr-3"></i><span>Employee Advances</span>
                    </a>
                </div>

                <!-- Inquiries and Reports -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries and Reports</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('hr.timesheet') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.timesheet') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-clock text-gray-400 mr-3"></i><span>Timesheet</span>
                    </a>
                    <a href="{{ route('hr.inquiries.transactions') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.inquiries.transactions') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-search text-gray-400 mr-3"></i><span>Employee Transaction Inquiry</span>
                    </a>
                    <a href="{{ route('hr.inquiries.documents') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.inquiries.documents') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-alt text-gray-400 mr-3"></i><span>Employee Document Inquiry</span>
                    </a>
                    <a href="{{ route('hr.reports.employee') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.reports.employee') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-chart-bar text-gray-400 mr-3"></i><span>Employee Reports</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('hr.employees.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.employees.index') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-users text-gray-400 mr-3"></i><span>Employees</span>
                    </a>
                    <a href="{{ route('hr.document-types') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.document-types') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file text-gray-400 mr-3"></i><span>Document Types</span>
                    </a>
                    <a href="{{ route('hr.departments') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.departments') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-sitemap text-gray-400 mr-3"></i><span>Departments</span>
                    </a>
                    <a href="{{ route('hr.overtime') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.overtime') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-clock text-gray-400 mr-3"></i><span>Manage Overtime</span>
                    </a>
                    <a href="{{ route('hr.leave-types') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.leave-types') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-plane text-gray-400 mr-3"></i><span>Leave Types</span>
                    </a>
                    <a href="{{ route('hr.default-settings') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.default-settings') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-cogs text-gray-400 mr-3"></i><span>Default Settings</span>
                    </a>
                    <a href="{{ route('hr.job-positions') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.job-positions') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-briefcase text-gray-400 mr-3"></i><span>Job Positions</span>
                    </a>
                    <a href="{{ route('hr.grades') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.grades') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-layer-group text-gray-400 mr-3"></i><span>Manage Grades</span>
                    </a>
                    <a href="{{ route('hr.pay-elements') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.pay-elements') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-coins text-gray-400 mr-3"></i><span>Pay Elements</span>
                    </a>
                    <a href="{{ route('hr.pay-elements-allocation') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.pay-elements-allocation') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-people-arrows text-gray-400 mr-3"></i><span>Pay Elements Allocation</span>
                    </a>
                    <a href="{{ route('hr.salary-structure') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hr.salary-structure') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-sitemap text-gray-400 mr-3"></i><span>Salary Structure</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Submenu: Setup -->
        <div class="mt-2 space-y-1">
            <div class="px-3">
                <button onclick="toggleSetupMenu()" class="w-full flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition duration-150 ease-in-out">
                    <span>Setup Module</span>
                    <i id="setupMenuIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-150"></i>
                </button>
            </div>
            <div id="setupMenuContent" class="space-y-1">
                <!-- Company Setup -->
                <div class="px-3">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Company Setup</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('setup.company') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.company') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-building text-gray-400 mr-3"></i><span>Company Setup</span>
                    </a>
                    <a href="{{ route('setup.users') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.users') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-users-cog text-gray-400 mr-3"></i><span>User Accounts Setup</span>
                    </a>
                    <a href="{{ route('setup.access') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.access') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-shield-alt text-gray-400 mr-3"></i><span>Access Setup</span>
                    </a>
                    <a href="{{ route('setup.display') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.display') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-desktop text-gray-400 mr-3"></i><span>Display Setup</span>
                    </a>
                    <a href="{{ route('setup.transaction-references') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.transaction-references') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-hashtag text-gray-400 mr-3"></i><span>Transaction References</span>
                    </a>
                    <a href="{{ route('setup.taxes') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.taxes') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-invoice-dollar text-gray-400 mr-3"></i><span>Taxes</span>
                    </a>
                    <a href="{{ route('setup.tax-groups') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.tax-groups') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-layer-group text-gray-400 mr-3"></i><span>Tax Groups</span>
                    </a>
                    <a href="{{ route('setup.item-tax-types') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.item-tax-types') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-tag text-gray-400 mr-3"></i><span>Item Tax Types</span>
                    </a>
                    <a href="{{ route('setup.system-gl') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.system-gl') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-cog text-gray-400 mr-3"></i><span>System and General GL Setup</span>
                    </a>
                    <a href="{{ route('setup.fiscal-years') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.fiscal-years') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-alt text-gray-400 mr-3"></i><span>Fiscal Years</span>
                    </a>
                    <a href="{{ route('setup.print-profiles') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.print-profiles') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-print text-gray-400 mr-3"></i><span>Print Profiles</span>
                    </a>
                </div>

                <!-- Miscellaneous -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Miscellaneous</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('setup.payment-terms') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.payment-terms') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-handshake text-gray-400 mr-3"></i><span>Payment Terms</span>
                    </a>
                    <a href="{{ route('setup.shipping-company') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.shipping-company') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-shipping-fast text-gray-400 mr-3"></i><span>Shipping Company</span>
                    </a>
                    <a href="{{ route('setup.points-of-sale') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.points-of-sale') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-cash-register text-gray-400 mr-3"></i><span>Points of Sale</span>
                    </a>
                    <a href="{{ route('setup.printers') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.printers') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-print text-gray-400 mr-3"></i><span>Printers</span>
                    </a>
                    <a href="{{ route('setup.contact-categories') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.contact-categories') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-address-book text-gray-400 mr-3"></i><span>Contact Categories</span>
                    </a>
                </div>

                <!-- Maintenance -->
                <div class="mt-4">
                    <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider">Maintenance</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('setup.void-transaction') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.void-transaction') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-ban text-gray-400 mr-3"></i><span>Void a Transaction</span>
                    </a>
                    <a href="{{ route('setup.view-print-transactions') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.view-print-transactions') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-file-alt text-gray-400 mr-3"></i><span>View or Print Transactions</span>
                    </a>
                    <a href="{{ route('setup.attach-documents') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.attach-documents') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-paperclip text-gray-400 mr-3"></i><span>Attach Documents</span>
                    </a>
                    <a href="{{ route('setup.system-diagnostics') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.system-diagnostics') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-stethoscope text-gray-400 mr-3"></i><span>System Diagnostics</span>
                    </a>
                    <a href="{{ route('setup.backup') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.backup') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-download text-gray-400 mr-3"></i><span>Backup and Restore</span>
                    </a>
                    <a href="{{ route('setup.companies') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('setup.companies') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-building text-gray-400 mr-3"></i><span>Create/Update Companies</span>
                    </a>
                </div>
            </div>
        </div>

    </nav>
</aside>

<script>
function toggleSalesMenu() {
    const menuContent = document.getElementById('salesMenuContent');
    const menuIcon = document.getElementById('salesMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('salesMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('salesMenuExpanded', 'false');
    }
}

function togglePurchasesMenu() {
    const menuContent = document.getElementById('purchasesMenuContent');
    const menuIcon = document.getElementById('purchasesMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('purchasesMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('purchasesMenuExpanded', 'false');
    }
}

function toggleInventoryMenu() {
    const menuContent = document.getElementById('inventoryMenuContent');
    const menuIcon = document.getElementById('inventoryMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('inventoryMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('inventoryMenuExpanded', 'false');
    }
}

function toggleFixedAssetsMenu() {
    const menuContent = document.getElementById('fixedAssetsMenuContent');
    const menuIcon = document.getElementById('fixedAssetsMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('fixedAssetsMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('fixedAssetsMenuExpanded', 'false');
    }
}

function toggleDimensionsMenu() {
    const menuContent = document.getElementById('dimensionsMenuContent');
    const menuIcon = document.getElementById('dimensionsMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('dimensionsMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('dimensionsMenuExpanded', 'false');
    }
}

function toggleBankingMenu() {
    const menuContent = document.getElementById('bankingMenuContent');
    const menuIcon = document.getElementById('bankingMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('bankingMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('bankingMenuExpanded', 'false');
    }
}

function toggleHRMenu() {
    const menuContent = document.getElementById('hrMenuContent');
    const menuIcon = document.getElementById('hrMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('hrMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('hrMenuExpanded', 'false');
    }
}

function toggleSetupMenu() {
    const menuContent = document.getElementById('setupMenuContent');
    const menuIcon = document.getElementById('setupMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('setupMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('setupMenuExpanded', 'false');
    }
}

function toggleManufacturingMenu() {
    const menuContent = document.getElementById('manufacturingMenuContent');
    const menuIcon = document.getElementById('manufacturingMenuIcon');
    
    if (menuContent.classList.contains('hidden')) {
        menuContent.classList.remove('hidden');
        menuIcon.classList.remove('fa-chevron-right');
        menuIcon.classList.add('fa-chevron-down');
        localStorage.setItem('manufacturingMenuExpanded', 'true');
    } else {
        menuContent.classList.add('hidden');
        menuIcon.classList.remove('fa-chevron-down');
        menuIcon.classList.add('fa-chevron-right');
        localStorage.setItem('manufacturingMenuExpanded', 'false');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Sales menu state
    const salesContent = document.getElementById('salesMenuContent');
    const salesIcon = document.getElementById('salesMenuIcon');
    const salesSaved = localStorage.getItem('salesMenuExpanded');
    const isSalesRoute = window.location.pathname.includes('/sales/');
    
    if (salesSaved === 'true' || isSalesRoute) {
        salesContent.classList.remove('hidden');
        salesIcon.classList.remove('fa-chevron-right');
        salesIcon.classList.add('fa-chevron-down');
    } else {
        salesContent.classList.add('hidden');
        salesIcon.classList.remove('fa-chevron-down');
        salesIcon.classList.add('fa-chevron-right');
    }

    // Purchases menu state
    const purchasesContent = document.getElementById('purchasesMenuContent');
    const purchasesIcon = document.getElementById('purchasesMenuIcon');
    const purchasesSaved = localStorage.getItem('purchasesMenuExpanded');
    const isPurchasesRoute = window.location.pathname.includes('/purchases/');
    
    if (purchasesSaved === 'true' || isPurchasesRoute) {
        purchasesContent.classList.remove('hidden');
        purchasesIcon.classList.remove('fa-chevron-right');
        purchasesIcon.classList.add('fa-chevron-down');
    } else {
        purchasesContent.classList.add('hidden');
        purchasesIcon.classList.remove('fa-chevron-down');
        purchasesIcon.classList.add('fa-chevron-right');
    }

    // Inventory menu state
    const inventoryContent = document.getElementById('inventoryMenuContent');
    const inventoryIcon = document.getElementById('inventoryMenuIcon');
    const inventorySaved = localStorage.getItem('inventoryMenuExpanded');
    const isInventoryRoute = window.location.pathname.includes('/inventory/');
    
    if (inventorySaved === 'true' || isInventoryRoute) {
        inventoryContent.classList.remove('hidden');
        inventoryIcon.classList.remove('fa-chevron-right');
        inventoryIcon.classList.add('fa-chevron-down');
    } else {
        inventoryContent.classList.add('hidden');
        inventoryIcon.classList.remove('fa-chevron-down');
        inventoryIcon.classList.add('fa-chevron-right');
    }

    // Fixed Assets menu state
    const fixedAssetsContent = document.getElementById('fixedAssetsMenuContent');
    const fixedAssetsIcon = document.getElementById('fixedAssetsMenuIcon');
    const fixedAssetsSaved = localStorage.getItem('fixedAssetsMenuExpanded');
    const isFixedAssetsRoute = window.location.pathname.includes('/fixed-assets/');
    
    if (fixedAssetsSaved === 'true' || isFixedAssetsRoute) {
        fixedAssetsContent.classList.remove('hidden');
        fixedAssetsIcon.classList.remove('fa-chevron-right');
        fixedAssetsIcon.classList.add('fa-chevron-down');
    } else {
        fixedAssetsContent.classList.add('hidden');
        fixedAssetsIcon.classList.remove('fa-chevron-down');
        fixedAssetsIcon.classList.add('fa-chevron-right');
    }

    // Dimensions menu state
    const dimensionsContent = document.getElementById('dimensionsMenuContent');
    const dimensionsIcon = document.getElementById('dimensionsMenuIcon');
    const dimensionsSaved = localStorage.getItem('dimensionsMenuExpanded');
    const isDimensionsRoute = window.location.pathname.includes('/dimensions/');
    
    if (dimensionsSaved === 'true' || isDimensionsRoute) {
        dimensionsContent.classList.remove('hidden');
        dimensionsIcon.classList.remove('fa-chevron-right');
        dimensionsIcon.classList.add('fa-chevron-down');
    } else {
        dimensionsContent.classList.add('hidden');
        dimensionsIcon.classList.remove('fa-chevron-down');
        dimensionsIcon.classList.add('fa-chevron-right');
    }

    // Banking menu state
    const bankingContent = document.getElementById('bankingMenuContent');
    const bankingIcon = document.getElementById('bankingMenuIcon');
    const bankingSaved = localStorage.getItem('bankingMenuExpanded');
    const isBankingRoute = window.location.pathname.includes('/banking/');
    
    if (bankingSaved === 'true' || isBankingRoute) {
        bankingContent.classList.remove('hidden');
        bankingIcon.classList.remove('fa-chevron-right');
        bankingIcon.classList.add('fa-chevron-down');
    } else {
        bankingContent.classList.add('hidden');
        bankingIcon.classList.remove('fa-chevron-down');
        bankingIcon.classList.add('fa-chevron-right');
    }

    // HR menu state
    const hrContent = document.getElementById('hrMenuContent');
    const hrIcon = document.getElementById('hrMenuIcon');
    const hrSaved = localStorage.getItem('hrMenuExpanded');
    const isHRRoute = window.location.pathname.includes('/hr/');
    
    if (hrSaved === 'true' || isHRRoute) {
        hrContent.classList.remove('hidden');
        hrIcon.classList.remove('fa-chevron-right');
        hrIcon.classList.add('fa-chevron-down');
    } else {
        hrContent.classList.add('hidden');
        hrIcon.classList.remove('fa-chevron-down');
        hrIcon.classList.add('fa-chevron-right');
    }

    // Setup menu state
    const setupContent = document.getElementById('setupMenuContent');
    const setupIcon = document.getElementById('setupMenuIcon');
    const setupSaved = localStorage.getItem('setupMenuExpanded');
    const isSetupRoute = window.location.pathname.includes('/setup/');
    
    if (setupSaved === 'true' || isSetupRoute) {
        setupContent.classList.remove('hidden');
        setupIcon.classList.remove('fa-chevron-right');
        setupIcon.classList.add('fa-chevron-down');
    } else {
        setupContent.classList.add('hidden');
        setupIcon.classList.remove('fa-chevron-down');
        setupIcon.classList.add('fa-chevron-right');
    }

    // Manufacturing menu state
    const manufacturingContent = document.getElementById('manufacturingMenuContent');
    const manufacturingIcon = document.getElementById('manufacturingMenuIcon');
    const manufacturingSaved = localStorage.getItem('manufacturingMenuExpanded');
    const isManufacturingRoute = window.location.pathname.includes('/manufacturing/');
    
    if (manufacturingSaved === 'true' || isManufacturingRoute) {
        manufacturingContent.classList.remove('hidden');
        manufacturingIcon.classList.remove('fa-chevron-right');
        manufacturingIcon.classList.add('fa-chevron-down');
    } else {
        manufacturingContent.classList.add('hidden');
        manufacturingIcon.classList.remove('fa-chevron-down');
        manufacturingIcon.classList.add('fa-chevron-right');
    }
});
</script>
