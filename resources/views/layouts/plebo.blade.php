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
                        <button id="profileButton" class="text-gray-600 hover:text-gray-900 bg-white hover:bg-gray-100 p-2 rounded-xl transition-all duration-300 border border-gray-200">
                            <i class="fas fa-user-cog text-lg"></i>
                        </button>
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
                        <!-- Messages -->
                        <div class="relative">
                            <a href="{{ route('plebo.messages') }}" class="bg-gray-50 hover:bg-gray-100 border border-gray-200 p-3 rounded-2xl text-gray-600 hover:text-gray-900 transition-all duration-300 inline-block">
                                <i class="fas fa-envelope text-lg"></i>
                                <span id="header-message-count" class="absolute -top-1 -right-1 w-3 h-3 bg-orange-500 rounded-full notification-badge hidden"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fas fa-vial text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Profile</h3>
                            <p class="text-orange-100 text-sm">Medical Phlebotomist</p>
                        </div>
                    </div>
                    <button id="closeModal" class="text-white/70 hover:text-white transition-colors p-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-8">
                <!-- Profile Info -->
                <div class="flex items-center space-x-4 mb-8 p-4 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-vial text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">Medical Phlebotomist</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-2">
                    <a href="{{ route('plebo.profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-orange-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-user-edit text-gray-500 group-hover:text-orange-600"></i>
                        </div>
                        <span class="font-medium">Edit Profile</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-orange-500"></i>
                    </a>
                    
                    <div class="border-t border-gray-200 my-4"></div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 group">
                            <div class="w-10 h-10 bg-red-50 group-hover:bg-red-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                <i class="fas fa-sign-out-alt text-red-500"></i>
                            </div>
                            <span class="font-medium">Logout</span>
                            <i class="fas fa-chevron-right ml-auto text-red-400"></i>
                        </button>
                    </form>
                </div>
            </div>
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
            
            // Profile Modal Functionality
            const profileButton = document.getElementById('profileButton');
            const profileModal = document.getElementById('profileModal');
            const closeModal = document.getElementById('closeModal');
            
            // Open modal
            if (profileButton) {
                profileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileModal.classList.remove('hidden');
                    
                    // Add animation
                    const modalContent = profileModal.querySelector('.relative');
                    modalContent.style.animation = 'fadeInUp 0.3s ease-out';
                });
            }

            // Close modal function
            function closeProfileModal() {
                const modalContent = profileModal.querySelector('.relative');
                modalContent.style.animation = 'fadeInUp 0.2s ease-in reverse';
                
                setTimeout(() => {
                    profileModal.classList.add('hidden');
                }, 200);
            }

            // Close modal events
            if (closeModal) {
                closeModal.addEventListener('click', closeProfileModal);
            }

            // Close modal when clicking outside
            if (profileModal) {
                profileModal.addEventListener('click', function(e) {
                    if (e.target === profileModal) {
                        closeProfileModal();
                    }
                });
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && profileModal && !profileModal.classList.contains('hidden')) {
                    closeProfileModal();
                }
            });
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


