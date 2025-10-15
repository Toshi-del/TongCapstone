<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Medical Technologist Dashboard - RSS Citi Health Services')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #059669;
            --secondary-color: #10b981;
            --accent-color: #065f46;
            --info-color: #0891b2;
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --glass-bg: rgba(16, 185, 129, 0.05);
            --glass-border: rgba(16, 185, 129, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #1f2937;
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-sidebar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-right: 2px solid #e5e7eb;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .nav-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .nav-item:hover::before {
            left: 100%;
        }
        
        .nav-item.active {
            background: #059669;
            color: white;
            border-left: 4px solid #10b981;
        }
        
        .nav-item:hover {
            background: #f3f4f6;
        }
        
        .modal-active {
            overflow: hidden;
        }
        
        .modal-active .glass-sidebar {
            filter: blur(2px);
        }
        
        .notification-badge {
            background: #dc2626;
            animation: pulse 2s infinite;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .search-container:focus-within::after {
            width: 100%;
        }
    </style>
    
    @yield('styles')
</head>
<body class="h-full bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-72 glass-sidebar relative z-10 shadow-2xl flex flex-col h-full">
            <!-- Header -->
            <div class="p-8 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-nurse text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Medical Technologist</h1>
                        <p class="text-gray-600 text-sm">RSS Citi Health Services</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-6 py-8 space-y-2 overflow-y-auto">
                <!-- Main Menu Section -->
                <div class="mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-3">Main Menu</h3>
                    <div class="space-y-1">
                        <a href="{{ route('nurse.dashboard') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('nurse.dashboard') ? 'active text-white' : '' }}">
                            <i class="fas fa-th-large mr-4 text-lg"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- Medical Services Section -->
                <div class="mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-3">Medical Services</h3>
                    <div class="space-y-1">
                        <a href="{{ route('nurse.pre-employment') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('nurse.pre-employment*') ? 'active text-white' : '' }}">
                            <i class="fas fa-user-md mr-4 text-lg"></i>
                            <span class="font-medium">Pre-Employment</span>
                        </a>
                        <a href="{{ route('nurse.annual-physical') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('nurse.annual-physical*') ? 'active text-white' : '' }}">
                            <i class="fas fa-file-medical mr-4 text-lg"></i>
                            <span class="font-medium">Annual Physical</span>
                        </a>
                        <a href="{{ route('nurse.opd') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('nurse.opd*') ? 'active text-white' : '' }}">
                            <i class="fas fa-walking mr-4 text-lg"></i>
                            <span class="font-medium">OPD Walk-ins</span>
                        </a>
                    </div>
                </div>

                <!-- Communication Section -->
                <div class="mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-3">Communication</h3>
                    <div class="space-y-1">
                        <a href="{{ route('nurse.messages') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('nurse.messages*') ? 'active text-white' : '' }}">
                            <i class="fas fa-comments mr-4 text-lg"></i>
                            <span class="font-medium">Messages</span>
                            <span id="message-count" class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Profile Section - Sticky Bottom -->
            <div class="flex-shrink-0 p-6 border-t border-gray-200 mt-auto">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ substr(Auth::user()->fname ?? 'M', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-semibold text-sm truncate">
                                {{ Auth::user()->fname ?? 'Medical' }} {{ Auth::user()->lname ?? 'Tech' }}
                            </p>
                            <p class="text-gray-600 text-xs">Medical Technologist</p>
                        </div>
                        <button id="profileButton" class="text-gray-600 hover:text-gray-900 bg-white hover:bg-gray-100 p-2 rounded-lg transition-all duration-300 border border-gray-200">
                            <i class="fas fa-user-cog text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            <!-- Header -->
            <header class="content-card shadow-lg border-b border-gray-200 relative z-20">
                <div class="flex items-center justify-between px-8 py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-sm text-gray-600 mt-1">@yield('page-description', 'Medical Technologist Portal')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Messages -->
                        <div class="relative">
                            <a href="{{ route('nurse.messages') }}" class="p-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all duration-200 inline-block">
                                <i class="fas fa-envelope text-lg"></i>
                                <span id="header-message-count" class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full notification-badge hidden"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fas fa-user-nurse text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Profile</h3>
                            <p class="text-emerald-100 text-sm">Medical Technologist</p>
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
                <div class="flex items-center space-x-4 mb-8 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-nurse text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-full">Medical Technologist</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-2">
                    <a href="{{ route('nurse.profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-emerald-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-user-edit text-gray-500 group-hover:text-emerald-600"></i>
                        </div>
                        <span class="font-medium">Edit Profile</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-emerald-500"></i>
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

    @yield('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Profile Modal Functionality
            const profileButton = document.getElementById('profileButton');
            const profileModal = document.getElementById('profileModal');
            const closeModal = document.getElementById('closeModal');
            // Open modal
            profileButton.addEventListener('click', function(e) {
                e.preventDefault();
                profileModal.classList.remove('hidden');
                document.body.classList.add('modal-active');
                
                // Add animation
                const modalContent = profileModal.querySelector('.relative');
                modalContent.style.animation = 'fadeInUp 0.3s ease-out';
            });

            // Close modal function
            function closeProfileModal() {
                const modalContent = profileModal.querySelector('.relative');
                modalContent.style.animation = 'fadeInUp 0.2s ease-in reverse';
                
                setTimeout(() => {
                    profileModal.classList.add('hidden');
                    document.body.classList.remove('modal-active');
                }, 200);
            }

            // Close modal events
            closeModal.addEventListener('click', closeProfileModal);

            // Close modal when clicking outside
            profileModal.addEventListener('click', function(e) {
                if (e.target === profileModal) {
                    closeProfileModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !profileModal.classList.contains('hidden')) {
                    closeProfileModal();
                }
            });

            // Navigation active state management
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.animation = 'slideIn 0.3s ease-out';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.animation = '';
                });
            });

            // Search functionality enhancement
            const searchInput = document.querySelector('input[type="text"]');
            if (searchInput) {
                searchInput.addEventListener('focus', function() {
                    this.parentElement.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.parentElement.style.transition = 'transform 0.2s ease';
                });
                
                searchInput.addEventListener('blur', function() {
                    this.parentElement.parentElement.style.transform = 'scale(1)';
                });
            }

            // Notification badges animation
            const badges = document.querySelectorAll('.notification-badge');
            badges.forEach(badge => {
                setInterval(() => {
                    badge.style.animation = 'pulse 1s ease-in-out';
                    setTimeout(() => {
                        badge.style.animation = '';
                    }, 1000);
                }, 3000);
            });

            // Enhanced hover effects for buttons
            const buttons = document.querySelectorAll('button, .nav-item');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    if (this.classList.contains('nav-item')) {
                        this.style.transform = 'translateX(4px)';
                    }
                });
                
                button.addEventListener('mouseleave', function() {
                    if (this.classList.contains('nav-item')) {
                        this.style.transform = 'translateX(0)';
                    }
                });
            });

            // Smooth scrolling for main content
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.style.scrollBehavior = 'smooth';
            }
            
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
            fetch('/nurse/messages/count')
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
</body>
</html> 