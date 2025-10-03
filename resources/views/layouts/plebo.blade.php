<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Phlebotomy Portal') - RSS Health Services Corp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #ea580c;
            --secondary-color: #fb923c;
            --accent-color: #c2410c;
            --info-color: #0891b2;  
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .sidebar-glass {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-right: 2px solid #e5e7eb;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .nav-item {
            transition: all 0.2s ease;
            color: #374151;
        }
        
        .nav-item:hover {
            background: #f3f4f6;
            backdrop-filter: blur(10px);
        }
        
        .nav-item.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.3);
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
    </style>
    @yield('styles')
</head>
<body class="h-full overflow-hidden">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 sidebar-glass transform -translate-x-full transition-all duration-500 ease-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col custom-scrollbar">
            <!-- Sidebar header -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-orange-600 flex items-center justify-center">
                        <i class="fas fa-vial text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-gray-900 font-bold text-lg">Phlebotomy Portal</span>
                        <p class="text-gray-600 text-xs font-medium">Blood Collection Services</p>
                    </div>
                </div>
                <button id="sidebar-close" class="lg:hidden text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-2 rounded-xl transition-all duration-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto custom-scrollbar">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mb-4">Main Menu</div>
                
                <a href="{{ route('plebo.dashboard') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('plebo.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt text-lg mr-4"></i>
                    <span>Dashboard</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mt-8 mb-4">Laboratory Services</div>
                
                <a href="{{ route('plebo.pre-employment') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('plebo.pre-employment') ? 'active' : '' }}">
                    <i class="fas fa-user-md text-lg mr-4"></i>
                    <span>Pre-Employment</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('plebo.annual-physical') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('plebo.annual-physical') ? 'active' : '' }}">
                    <i class="fas fa-file-medical text-lg mr-4"></i>
                    <span>Annual Physical</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('plebo.opd') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('plebo.opd') ? 'active' : '' }}">
                    <i class="fas fa-walking text-lg mr-4"></i>
                    <span>OPD Walk-ins</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mt-8 mb-4">Communication</div>
                
                <a href="{{ route('plebo.messages') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('plebo.messages*') ? 'active' : '' }}">
                    <i class="fas fa-comments text-lg mr-4"></i>
                    <span>Messages</span>
                    <div class="ml-auto flex items-center space-x-2">
                        <span id="message-count" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                        <i class="fas fa-chevron-right text-xs opacity-60"></i>
                    </div>
                </a>
            </nav>
            
            <!-- User profile section -->
            <div class="p-6 border-t border-gray-200">
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-orange-600 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr(Auth::user()->fname ?? 'P', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-semibold text-sm truncate">
                                {{ Auth::user()->fname ?? 'Phlebotomist' }} {{ Auth::user()->lname ?? 'User' }}
                            </p>
                            <p class="text-gray-600 text-xs">Medical Phlebotomist</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900 bg-white hover:bg-gray-100 p-2 rounded-xl transition-all duration-300 border border-gray-200">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm transition-all duration-500 opacity-0 pointer-events-none lg:hidden"></div>
        
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top header -->
            <header class="bg-white/95 backdrop-blur-xl border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-6 py-5">
                    <div class="flex items-center space-x-4">
                        <button id="sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-3 rounded-2xl transition-all duration-300">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                @yield('page-title', 'Dashboard')
                            </h1>
                            <p class="text-gray-600 text-sm font-medium">Welcome back, {{ Auth::user()->fname ?? 'Phlebotomist' }}!</p>
                        </div>
                    </div>
                    
                    <!-- Header actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Search bar -->
                        <div class="hidden md:block">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       class="bg-gray-50 border border-gray-200 pl-12 pr-4 py-3 rounded-2xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 w-80" 
                                       placeholder="Search patients, tests...">
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <button class="relative bg-gray-50 hover:bg-gray-100 border border-gray-200 p-3 rounded-2xl text-gray-600 hover:text-gray-900 transition-all duration-300">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs flex items-center justify-center text-white font-bold">3</span>
                        </button>
                        
                        <!-- Messages -->
                        <div class="relative">
                            <a href="{{ route('plebo.messages') }}" class="bg-gray-50 hover:bg-gray-100 border border-gray-200 p-3 rounded-2xl text-gray-600 hover:text-gray-900 transition-all duration-300 inline-block">
                                <i class="fas fa-envelope text-lg"></i>
                                <span id="header-message-count" class="absolute -top-1 -right-1 w-3 h-3 bg-orange-500 rounded-full notification-badge hidden"></span>
                            </a>
                        </div>
                        
                        <!-- Settings -->
                        <button class="bg-gray-50 hover:bg-gray-100 border border-gray-200 p-3 rounded-2xl text-gray-600 hover:text-gray-900 transition-all duration-300">
                            <i class="fas fa-cog text-lg"></i>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100');
                document.body.classList.add('overflow-hidden');
            }
            
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100');
                document.body.classList.remove('overflow-hidden');
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', openSidebar);
            }
            
            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }
            
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                }
            });
            
            // Initialize messages functionality
            initializeMessages();
        });
        
        function initializeMessages() {
            // Load initial message count
            loadMessageCount();
            
            // Refresh message count every 30 seconds
            setInterval(loadMessageCount, 30000);
        }
        
        function loadMessageCount() {
            fetch('/plebo/messages/count')
                .then(response => response.json())
                .then(data => {
                    updateMessageCount(data.count);
                })
                .catch(error => {
                    console.log('Error loading message count:', error);
                });
        }
        
        function updateMessageCount(count) {
            const messageCount = document.getElementById('message-count');
            const headerMessageCount = document.getElementById('header-message-count');
            
            if (count > 0) {
                if (messageCount) {
                    messageCount.textContent = count > 99 ? '99+' : count;
                    messageCount.classList.remove('hidden');
                }
                if (headerMessageCount) {
                    headerMessageCount.classList.remove('hidden');
                }
            } else {
                if (messageCount) messageCount.classList.add('hidden');
                if (headerMessageCount) headerMessageCount.classList.add('hidden');
            }
        }
    </script>
    @yield('scripts')
</body>
</html>


