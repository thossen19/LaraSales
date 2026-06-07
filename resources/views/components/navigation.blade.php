<!-- Navigation Menu -->
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="h-8 w-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900">{{ config('app.name', 'Sales ERP') }}</h1>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('dashboard') }}" 
                   class="{{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Dashboard
                </a>
                
                <!-- Sales Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('sales.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Sales
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-48 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('sales.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sales Orders</a>
                        <a href="{{ route('sales.customers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Customers</a>
                        <a href="{{ route('sales.reports') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reports</a>
                    </div>
                </div>

                <!-- Purchases Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('purchases.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-truck mr-2"></i>
                        Purchases
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('purchases.orders.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Purchase Order Entry</a>
                        <a href="{{ route('purchases.orders.outstanding') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Outstanding Purchase Orders</a>
                        <a href="{{ route('purchases.grn.direct') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Direct GRN</a>
                        <a href="{{ route('purchases.invoice.direct') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Direct Supplier Invoice</a>
                        <a href="{{ route('purchases.payments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payments to Suppliers</a>
                        <a href="{{ route('purchases.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier Invoices</a>
                        <a href="{{ route('purchases.credit-notes.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier Credit Notes</a>
                        <a href="{{ route('purchases.allocation.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Allocate Payments/Credit Notes</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('purchases.inquiries.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Purchase Orders Inquiry</a>
                        <a href="{{ route('purchases.inquiries.transactions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier Transaction Inquiry</a>
                        <a href="{{ route('purchases.inquiries.allocation') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier Allocation Inquiry</a>
                        <a href="{{ route('purchases.reports.supplier') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier and Purchasing Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('purchases.suppliers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Suppliers</a>
                    </div>
                </div>

                <!-- Inventory Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('inventory.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-boxes mr-2"></i>
                        Items & Inventory
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('inventory.transfers') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Location Transfers</a>
                        <a href="{{ route('inventory.adjust') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Adjustments</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('inventory.inquiries.movements') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Item Movements</a>
                        <a href="{{ route('inventory.inquiries.status') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Item Status</a>
                        <a href="{{ route('inventory.reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('inventory.items.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Items</a>
                        <a href="{{ route('inventory.items.foreign-codes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Foreign Item Codes</a>
                        <a href="{{ route('inventory.items.sales-kits') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sales Kits</a>
                        <a href="{{ route('inventory.items.categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item Categories</a>
                        <a href="{{ route('inventory.locations') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Locations</a>
                        <a href="{{ route('inventory.units-of-measure') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Units of Measure</a>
                        <a href="{{ route('inventory.reorder-levels') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reorder Levels</a>
                        <a href="{{ route('inventory.items.import-csv') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Import CSV Items</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('inventory.pricing.sales') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sales Pricing</a>
                        <a href="{{ route('inventory.pricing.purchasing') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Purchasing Pricing</a>
                        <a href="{{ route('inventory.pricing.standard-costs') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Standard Costs</a>
                    </div>
                </div>

                <!-- Fixed Assets Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('fixed-assets.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-building mr-2"></i>
                        Fixed Assets
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('fixed-assets.purchase') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Purchase</a>
                        <a href="{{ route('fixed-assets.transfers') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Location Transfers</a>
                        <a href="{{ route('fixed-assets.disposal') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Disposal</a>
                        <a href="{{ route('fixed-assets.sale') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Sale</a>
                        <a href="{{ route('fixed-assets.depreciation') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Process Depreciation</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('fixed-assets.inquiries.movements') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Movements</a>
                        <a href="{{ route('fixed-assets.inquiries.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Inquiry</a>
                        <a href="{{ route('fixed-assets.reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('fixed-assets.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets</a>
                        <a href="{{ route('fixed-assets.locations') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Locations</a>
                        <a href="{{ route('fixed-assets.categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Categories</a>
                        <a href="{{ route('fixed-assets.classes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fixed Assets Classes</a>
                    </div>
                </div>

                <!-- Dimensions Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('dimensions.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-cube mr-2"></i>
                        Dimensions
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-48 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('dimensions.entry') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dimension Entry</a>
                        <a href="{{ route('dimensions.outstanding') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Outstanding Dimensions</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('dimensions.inquiries.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dimension Inquiry</a>
                        <a href="{{ route('dimensions.reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dimension Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('dimensions.tags') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dimension Tags</a>
                    </div>
                </div>

                <!-- Banking Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('banking.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-university mr-2"></i>
                        Banking & GL
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('banking.payments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payments</a>
                        <a href="{{ route('banking.deposits') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Deposits</a>
                        <a href="{{ route('banking.transfers') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Bank Account Transfers</a>
                        <a href="{{ route('banking.journal.entry') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Journal Entry</a>
                        <a href="{{ route('banking.budget-entry') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Budget Entry</a>
                        <a href="{{ route('banking.reconcile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reconcile Bank Account</a>
                        <a href="{{ route('banking.accruals') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Revenue / Costs Accruals</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('banking.inquiries.journal') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Journal Inquiry</a>
                        <a href="{{ route('banking.inquiries.gl') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">GL Inquiry</a>
                        <a href="{{ route('banking.inquiries.bank-account') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Bank Account Inquiry</a>
                        <a href="{{ route('banking.inquiries.tax') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tax Inquiry</a>
                        <a href="{{ route('banking.inquiries.tax-cash') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tax Inquiry (Cash Basis)</a>
                        <a href="{{ route('banking.reports.trial-balance') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Trial Balance</a>
                        <a href="{{ route('banking.reports.balance-sheet') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Balance Sheet Drilldown</a>
                        <a href="{{ route('banking.reports.profit-loss') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profit and Loss Drilldown</a>
                        <a href="{{ route('banking.reports.banking') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Banking Reports</a>
                        <a href="{{ route('banking.reports.gl') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">General Ledger Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('banking.accounts') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Bank Accounts</a>
                        <a href="{{ route('banking.quick-entries') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Quick Entries</a>
                        <a href="{{ route('banking.account-tags') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Tags</a>
                        <a href="{{ route('banking.currencies') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Currencies</a>
                        <a href="{{ route('banking.exchange-rates') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Exchange Rates</a>
                        <a href="{{ route('banking.gl-accounts') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">GL Accounts</a>
                        <a href="{{ route('banking.gl-groups') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">GL Account Groups</a>
                        <a href="{{ route('banking.gl-classes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">GL Account Classes</a>
                        <a href="{{ route('banking.closing') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Closing GL Transactions</a>
                        <a href="{{ route('banking.revaluation') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Revaluation of Currency Accounts</a>
                        <a href="{{ route('banking.journal.import') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Import Multiple Journal Entries</a>
                    </div>
                </div>

                <!-- Manufacturing Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('manufacturing.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-industry mr-2"></i>
                        Manufacturing
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('manufacturing.work-order-entry') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Work Order Entry</a>
                        <a href="{{ route('manufacturing.outstanding-work-orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Outstanding Work Orders</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('manufacturing.costed-bom-inquiry') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Costed Bill Of Material Inquiry</a>
                        <a href="{{ route('manufacturing.item-where-used') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory Item Where Used Inquiry</a>
                        <a href="{{ route('manufacturing.work-order-inquiry') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Work Order Inquiry</a>
                        <a href="{{ route('manufacturing.reports') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manufacturing Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('manufacturing.bom.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Bills Of Material</a>
                        <a href="{{ route('manufacturing.work-centers') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Work Centres</a>
                    </div>
                </div>

                <!-- HR Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->routeIs('hr.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-users mr-2"></i>
                        HR
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('hr.attendance') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Attendance</a>
                        <a href="{{ route('hr.payslips') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payslip Entry</a>
                        <a href="{{ route('hr.document-expiration') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Document Expiration</a>
                        <a href="{{ route('hr.payment-advice') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Advice</a>
                        <a href="{{ route('hr.employee-advances') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Employee Advances</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('hr.timesheet') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Timesheet</a>
                        <a href="{{ route('hr.inquiries.transactions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Employee Transaction Inquiry</a>
                        <a href="{{ route('hr.inquiries.documents') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Employee Document Inquiry</a>
                        <a href="{{ route('hr.reports.employee') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Employee Reports</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('hr.employees.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Employees</a>
                        <a href="{{ route('hr.document-types') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Document Types</a>
                        <a href="{{ route('hr.departments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Departments</a>
                        <a href="{{ route('hr.overtime') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage Overtime</a>
                        <a href="{{ route('hr.leave-types') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Leave Types</a>
                        <a href="{{ route('hr.default-settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Default Settings</a>
                        <a href="{{ route('hr.job-positions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Job Positions</a>
                        <a href="{{ route('hr.grades') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage Grades</a>
                        <a href="{{ route('hr.pay-elements') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pay Elements</a>
                        <a href="{{ route('hr.pay-elements-allocation') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pay Elements Allocation</a>
                        <a href="{{ route('hr.salary-structure') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Salary Structure</a>
                    </div>
                </div>

                <!-- Setup Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="{{ request()->route('setup.*') ? 'text-indigo-600' : 'text-gray-700' }} hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium flex items-center transition duration-150 ease-in-out">
                        <i class="fas fa-cogs mr-2"></i>
                        Setup
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white py-1">
                        <a href="{{ route('setup.company') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Company Setup</a>
                        <a href="{{ route('setup.users') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">User Accounts Setup</a>
                        <a href="{{ route('setup.access') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Access Setup</a>
                        <a href="{{ route('setup.display') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Display Setup</a>
                        <a href="{{ route('setup.transaction-references') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Transaction References</a>
                        <a href="{{ route('setup.taxes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Taxes</a>
                        <a href="{{ route('setup.tax-groups') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tax Groups</a>
                        <a href="{{ route('setup.item-tax-types') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item Tax Types</a>
                        <a href="{{ route('setup.system-gl') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">System and General GL Setup</a>
                        <a href="{{ route('setup.fiscal-years') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fiscal Years</a>
                        <a href="{{ route('setup.print-profiles') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Print Profiles</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('setup.payment-terms') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Terms</a>
                        <a href="{{ route('setup.shipping-company') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Shipping Company</a>
                        <a href="{{ route('setup.points-of-sale') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Points of Sale</a>
                        <a href="{{ route('setup.printers') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Printers</a>
                        <a href="{{ route('setup.contact-categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Contact Categories</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('setup.void-transaction') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Void a Transaction</a>
                        <a href="{{ route('setup.view-print-transactions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View or Print Transactions</a>
                        <a href="{{ route('setup.attach-documents') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Attach Documents</a>
                        <a href="{{ route('setup.system-diagnostics') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">System Diagnostics</a>
                        <a href="{{ route('setup.backup') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Backup and Restore</a>
                        <a href="{{ route('setup.companies') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create/Update Companies</a>
                    </div>
                </div>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition duration-150 ease-in-out">
                    <i class="fas fa-bell"></i>
                </button>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }" @click.away="open = !open">
                    <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <img class="h-8 w-8 rounded-full" src="https://picsum.photos/seed/user/40/40.jpg" alt="{{ Auth::user()->name }}">
                        <span class="ml-2 text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white py-1 z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="https://picsum.photos/seed/user/40/40.jpg" alt="{{ Auth::user()->name }}">
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->getRoleNames()->first() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
