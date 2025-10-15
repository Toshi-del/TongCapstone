<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pathologist Dashboard') - RSS Health Services Corp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @yield('styles')
    <style>
        :root {
            --primary-teal: #0d9488;
            --primary-teal-dark: #0f766e;
            --primary-teal-light: #5eead4;
            --sidebar-bg: rgba(255, 255, 255, 0.98);
            --sidebar-border: rgba(15, 118, 110, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        
        .content-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .content-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Allow gradient backgrounds to override white background */
        .bg-gradient-to-r.content-card,
        .bg-gradient-to-l.content-card,
        .bg-gradient-to-t.content-card,
        .bg-gradient-to-b.content-card {
            background: inherit !important;
        }
        
        /* Specific welcome section styling */
        .welcome-gradient {
            background: linear-gradient(to right, #0d9488, #0f766e) !important;
        }
        
        .sidebar-glass {
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            border-right: 2px solid rgba(13, 148, 136, 0.2);
            box-shadow: 2px 0 10px rgba(13, 148, 136, 0.1);
        }
        
        .nav-item {
            transition: all 0.3s ease;
            border-radius: 1rem;
            margin: 0.25rem 0.75rem;
        }
        
        .nav-item:hover {
            background-color: rgba(13, 148, 136, 0.1);
        }
        
        .nav-item.active {
            background-color: rgba(13, 148, 136, 0.15);
            color: var(--primary-teal-dark);
            font-weight: 600;
            border-left: 3px solid var(--primary-teal);
        }
        
        .notification-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .header-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(15, 118, 110, 0.2);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .search-focus:focus {
            ring-color: var(--primary-teal);
            border-color: var(--primary-teal);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-teal);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-teal-dark);
        }
    </style>
</head>
<body class="bg-slate-50 font-poppins">
    <div class="flex h-screen">
        <!-- Modern Sidebar -->
        <div class="w-64 sidebar-glass flex-shrink-0 shadow-xl relative">
            <!-- Brand Header -->
            <div class="p-6 border-b border-teal-100">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-microscope text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Pathologist</h1>
                        <p class="text-teal-600 text-sm font-medium">Laboratory Portal</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <!-- Main Menu -->
                    <div class="px-3 mb-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Main Menu</h3>
                    </div>
                    
                    <a href="{{ route('pathologist.dashboard') }}" 
                       class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('pathologist.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large mr-3 text-lg"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </div>
                
                <!-- Laboratory Services -->
                <div class="mt-8 px-3">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Laboratory Services</h3>
                    <div class="space-y-1">
                        <a href="{{ route('pathologist.annual-physical') }}" 
                           class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('pathologist.annual-physical*') ? 'active' : '' }}">
                            <i class="fas fa-file-medical mr-3 text-lg"></i>
                            <span class="font-medium">Annual Physical</span>
                        </a>
                        
                        <a href="{{ route('pathologist.pre-employment') }}" 
                           class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('pathologist.pre-employment*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase mr-3 text-lg"></i>
                            <span class="font-medium">Pre-Employment</span>
                        </a>
                        
                        <a href="{{ route('pathologist.opd') }}" 
                           class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('pathologist.opd*') ? 'active' : '' }}">
                            <i class="fas fa-walking mr-3 text-lg"></i>
                            <span class="font-medium">OPD Walk-ins</span>
                        </a>
                    </div>
                </div>
                
                <!-- Communication -->
                <div class="mt-8 px-3">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Communication</h3>
                    <div class="space-y-1">
                        <a href="{{ route('pathologist.messages') }}" 
                           class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('pathologist.messages*') ? 'active' : '' }}">
                            <i class="fas fa-comments mr-3 text-lg"></i>
                            <span class="font-medium">Messages</span>
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 notification-pulse">3</span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- Profile Section -->
            <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-teal-100">
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                                {{ substr(Auth::user()->fname, 0, 1) }}{{ substr(Auth::user()->lname, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">Dr. {{ Auth::user()->fname }}</p>
                            <p class="text-xs text-teal-600">Pathologist</p>
                        </div>
                        <button id="profileButton" class="p-2 text-gray-600 hover:text-gray-900 bg-white hover:bg-gray-100 rounded-lg transition-colors duration-200" title="Profile Settings">
                            <i class="fas fa-user-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Modern Header -->
            <header class="header-glass shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Laboratory Overview')</h1>
                        <p class="text-sm text-gray-600 mt-1">@yield('page-description', 'Pathologist Portal')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Messages -->
                        <div class="relative">
                            <a href="{{ route('pathologist.messages') }}" class="p-3 text-gray-600 hover:text-teal-600 hover:bg-teal-50 rounded-xl transition-all duration-200 inline-block">
                                <i class="fas fa-envelope text-lg"></i>
                                <span id="header-message-count" class="absolute -top-1 -right-1 w-3 h-3 bg-teal-500 rounded-full notification-badge hidden"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fas fa-microscope text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Profile</h3>
                            <p class="text-teal-100 text-sm">Medical Pathologist</p>
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
                <div class="flex items-center space-x-4 mb-8 p-4 bg-teal-50 rounded-xl border border-teal-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-microscope text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900">Dr. {{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs font-medium rounded-full">Medical Pathologist</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-2">
                    <a href="{{ route('pathologist.profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-teal-50 hover:text-teal-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-teal-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-user-edit text-gray-500 group-hover:text-teal-600"></i>
                        </div>
                        <span class="font-medium">Edit Profile</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-teal-500"></i>
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
        // Enhanced pathologist layout functionality
        document.addEventListener('DOMContentLoaded', function() {
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

            // Enhanced navigation interactions (removed problematic transforms)
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = 'rgba(13, 148, 136, 0.1)';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = '';
                    }
                });
            });

            // Quick action button interactions (removed scale transforms)
            document.querySelectorAll('button[title]').forEach(button => {
                button.addEventListener('click', function() {
                    // Add visual feedback without transforms
                    this.style.opacity = '0.8';
                    setTimeout(() => {
                        this.style.opacity = '1';
                    }, 150);
                });
            });

            // Auto-hide success/error messages with enhanced animation
            setTimeout(function() {
                const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                });
            }, 5000);

            // Notification pulse animation (removed scale transforms)
            const notifications = document.querySelectorAll('.notification-pulse');
            notifications.forEach(notification => {
                setInterval(() => {
                    notification.style.opacity = '0.7';
                    setTimeout(() => {
                        notification.style.opacity = '1';
                    }, 200);
                }, 3000);
            });

            console.log('Pathologist layout initialized with enhanced interactions');
        });
    </script>
</body>
</html>