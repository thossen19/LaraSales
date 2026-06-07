<!-- Header Component -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="h-8 w-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Sales ERP</h1>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('dashboard') }}" 
                   class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <i class="fas fa-tachometer-alt text-gray-400 mr-2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" 
                   class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <i class="fas fa-cog text-gray-400"></i>
                    <span>Modules</span>
                </a>
                <a href="#" 
                   class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <i class="fas fa-chart-bar text-gray-400"></i>
                    <span>Reports</span>
                </a>
                <a href="#" 
                   class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <i class="fas fa-cogs text-gray-400"></i>
                    <span>Setup</span>
                </a>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <button class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition duration-150 ease-in-out">
                    <i class="fas fa-bell"></i>
                </button>

                <!-- User Menu -->
                <div class="relative">
                    <button onclick="toggleUserMenu()" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <img class="h-8 w-8 rounded-full" src="https://picsum.photos/seed/user/40/40.jpg" alt="">
                        <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">John Doe</p>
                            <p class="text-xs text-gray-500">john.doe@example.com</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}" onsubmit="handleLogout(event)">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('hidden');
}

function handleLogout(event) {
    // Clear the auth token from localStorage
    localStorage.removeItem('auth_token');
    
    // Show loading state
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging out...';
    submitButton.disabled = true;
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('userMenu');
    const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
    
    if (!userButton && !userMenu.contains(event.target)) {
        userMenu.classList.add('hidden');
    }
});
</script>
