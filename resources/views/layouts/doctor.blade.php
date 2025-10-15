<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Doctor Dashboard') - RCC Health Services</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @yield('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .nav-item-active {
            background: #1f2937;
            color: white;
            border-radius: 12px;
        }
        
        .nav-item {
            transition: all 0.2s ease;
            border-radius: 12px;
            margin: 2px 0;
        }
        
        .nav-item:hover {
            background: #f3f4f6;
        }
        
        .nav-item-active:hover {
            background: #1f2937;
        }
        
        .notification-badge {
            background: #10b981;
        }
        
        .sidebar-border {
            border-right: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Clean Minimal Sidebar -->
        <div class="w-80 bg-white sidebar-border flex flex-col">
            <!-- Header Section -->
            <div class="p-8 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hospital text-white text-lg"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">RSS</h1>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-6 py-8">
                <div class="space-y-1">
                    <a href="{{ route('doctor.dashboard') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('doctor.dashboard') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-th-large mr-3 text-lg"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('doctor.annual-physical') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('doctor.annual-physical*') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-file-medical mr-3 text-lg"></i>
                        <span class="font-medium">Annual Physical</span>
                    </a>
                    
                    <a href="{{ route('doctor.pre-employment') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('doctor.pre-employment*') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-briefcase mr-3 text-lg"></i>
                        <span class="font-medium">Pre-Employment</span>
                    </a>
                    
                    <a href="{{ route('doctor.opd') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('doctor.opd*') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-user-md mr-3 text-lg"></i>
                        <span class="font-medium">OPD Examinations</span>
                    </a>
                    
                    <a href="{{ route('medical-test-categories.index') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('medical-test-categories*') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-list-alt mr-3 text-lg"></i>
                        <span class="font-medium">Test Categories</span>
                    </a>
                    
                    <a href="{{ route('medical-tests.index') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('medical-tests*') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-vials mr-3 text-lg"></i>
                        <span class="font-medium">Medical Tests</span>
                    </a>
                    
                    <a href="{{ route('doctor.messages') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 {{ request()->routeIs('doctor.messages*') ? 'nav-item-active text-white' : '' }}">
                        <i class="fas fa-comments mr-3 text-lg"></i>
                        <span class="font-medium">Messages</span>
                        <span id="message-count" class="ml-auto notification-badge text-white text-xs px-2 py-1 rounded-full font-medium hidden">0</span>
                    </a>
                </div>
            </nav>
            
            <!-- Profile Section -->
            <div class="p-6 border-t border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">
                            {{ strtoupper(substr(Auth::user()->fname, 0, 1) . substr(Auth::user()->lname, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-900 font-semibold text-sm truncate">Dr. {{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                        <p class="text-gray-500 text-xs truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <button id="profileButton" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-cog text-gray-500 text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Simple Header -->
            <header class="bg-white border-b border-gray-100">
                <div class="px-8 py-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-gray-600 text-sm mt-1">@yield('page-description', 'Welcome to your medical dashboard')</p>
                    </div>
                    
                    <!-- Notifications Dropdown -->
                    <div class="relative">
                        <button id="notificationButton" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notification-badge" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold hidden">0</span>
                        </button>
                        
                        <!-- Notifications Dropdown Menu -->
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                    <button id="markAllRead" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Mark all as read</button>
                                </div>
                            </div>
                            
                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <!-- Notifications will be loaded here -->
                                <div class="p-8 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-sm">No notifications yet</p>
                                </div>
                            </div>
                            
                            <div class="p-3 border-t border-gray-100 text-center">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all notifications</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Clean Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-0 w-96 shadow-xl rounded-xl bg-white overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Profile</h3>
                    <button id="closeModal" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Profile Info -->
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white font-semibold text-lg">
                            {{ strtoupper(substr(Auth::user()->fname, 0, 1) . substr(Auth::user()->lname, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-900">Dr. {{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                        <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                            <i class="fas fa-user-md mr-1"></i>
                            Medical Doctor
                        </span>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-2">
                    <a href="{{ route('doctor.profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        <i class="fas fa-user-edit text-gray-500 mr-3"></i>
                        <span class="font-medium">Edit Profile</span>
                    </a>
                    
                    <div class="border-t border-gray-100 pt-2 mt-4">
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                <i class="fas fa-sign-out-alt text-red-500 mr-3"></i>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
    
    <script>
        // Profile Modal Functionality
        const profileButton = document.getElementById('profileButton');
        const profileModal = document.getElementById('profileModal');
        const closeModal = document.getElementById('closeModal');

        // Open modal
        profileButton.addEventListener('click', function() {
            profileModal.classList.remove('hidden');
        });

        // Close modal
        closeModal.addEventListener('click', function() {
            profileModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        profileModal.addEventListener('click', function(e) {
            if (e.target === profileModal) {
                profileModal.classList.add('hidden');
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !profileModal.classList.contains('hidden')) {
                profileModal.classList.add('hidden');
            }
        });

        // Notifications Dropdown Functionality
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        notificationButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });
        
        // Mark all as read functionality
        document.getElementById('markAllRead').addEventListener('click', function() {
            fetch('/doctor/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        });

        // Initialize messages functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeMessages();
            loadNotifications();
        });
        
        function initializeMessages() {
            // Load initial message count
            loadMessageCount();
            
            // Refresh message count every 30 seconds
            setInterval(loadMessageCount, 30000);
        }
        
        function loadMessageCount() {
            fetch('/doctor/messages/count')
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
            
            if (count > 0) {
                if (messageCount) {
                    messageCount.textContent = count > 99 ? '99+' : count;
                    messageCount.classList.remove('hidden');
                }
            } else {
                if (messageCount) messageCount.classList.add('hidden');
            }
        }
        
        // Load notifications
        function loadNotifications() {
            fetch('/doctor/notifications?limit=10')
                .then(response => response.json())
                .then(data => {
                    const notificationBadge = document.getElementById('notification-badge');
                    const notificationList = document.getElementById('notificationList');
                    const notifications = data.notifications;
                    
                    // Update badge count
                    fetch('/doctor/notifications/count')
                        .then(response => response.json())
                        .then(countData => {
                            if (countData.count > 0) {
                                notificationBadge.textContent = countData.count > 99 ? '99+' : countData.count;
                                notificationBadge.classList.remove('hidden');
                            } else {
                                notificationBadge.classList.add('hidden');
                            }
                        });
                    
                    if (notifications.length > 0) {
                        notificationList.innerHTML = notifications.map(notification => {
                            const iconColor = notification.is_read ? 'gray' : 'blue';
                            const bgColor = notification.is_read ? 'bg-gray-50' : 'bg-white';
                            
                            return `
                                <div class="p-4 hover:bg-gray-100 border-b border-gray-100 cursor-pointer transition-colors duration-200 ${bgColor}" 
                                     onclick="markNotificationRead(${notification.id})">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-${iconColor}-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas ${notification.icon} text-${iconColor}-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                            <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${notification.time_ago}</p>
                                        </div>
                                        ${!notification.is_read ? '<div class="w-2 h-2 bg-blue-600 rounded-full"></div>' : ''}
                                    </div>
                                </div>
                            `;
                        }).join('');
                    } else {
                        notificationList.innerHTML = `
                            <div class="p-8 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm">No notifications yet</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }
        
        // Mark notification as read
        function markNotificationRead(notificationId) {
            fetch(`/doctor/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
        
        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    </script>
    
    @stack('scripts')
</body>
</html> 