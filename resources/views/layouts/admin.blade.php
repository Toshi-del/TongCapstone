<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - RSS Citi Health Services')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #059669;
            --accent-color: #d97706;
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
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.3);
        }
        
        .stat-card {
            transition: all 0.2s ease;
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        
        .glass-morphism {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .gradient-text {
            color: var(--primary-color);
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
        
        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Ensure notifications dropdown is always on top */
        #notifications-dropdown {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        
    </style>
    @stack('styles')
</head>
<body class="h-full overflow-hidden">
    <div class="flex h-full">
        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm transition-all duration-500 opacity-0 pointer-events-none lg:hidden"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 sidebar-glass transform -translate-x-full transition-all duration-500 ease-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col custom-scrollbar">
            <!-- Sidebar header -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-gray-900 font-bold text-lg">RSS Citi Health</span>
                        <p class="text-gray-600 text-xs font-medium">Admin Portal</p>
                    </div>
                </div>
                <button id="sidebar-close" class="lg:hidden text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-2 rounded-xl transition-all duration-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto custom-scrollbar">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mb-4">Main Menu</div>
                
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt text-lg mr-4"></i>
                    <span>Dashboard</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.patients') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
                    <i class="fas fa-users text-lg mr-4"></i>
                    <span>Patients</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.appointments') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.appointments*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check text-lg mr-4"></i>
                    <span>Appointments</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.pre-employment') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.pre-employment*') ? 'active' : '' }}">
                    <i class="fas fa-file-medical text-lg mr-4"></i>
                    <span>Pre-Employment</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.tests') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.tests*') ? 'active' : '' }}">
                    <i class="fas fa-vial text-lg mr-4"></i>
                    <span>Medical Tests</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.medical-test-categories.index') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.medical-test-categories.*') ? 'active' : '' }}">
                    <i class="fas fa-list-alt text-lg mr-4"></i>
                    <span>Test Categories</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mt-8 mb-4">Communication</div>
                
                <a href="{{ route('admin.messages') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                    <i class="fas fa-comments text-lg mr-4"></i>
                    <span>Messages</span>
                    <div class="ml-auto flex items-center space-x-2">
                        <span id="message-count" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                        <i class="fas fa-chevron-right text-xs opacity-60"></i>
                    </div>
                </a>
                
                <a href="{{ route('admin.notifications') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                    <i class="fas fa-bell text-lg mr-4"></i>
                    <span>Notifications</span>
                    <div class="ml-auto flex items-center space-x-2">
                        <span id="notification-count" class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                        <i class="fas fa-chevron-right text-xs opacity-60"></i>
                    </div>
                </a>
                
                <a href="{{ route('admin.report') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.report*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line text-lg mr-4"></i>
                    <span>Reports</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mt-8 mb-4">Management</div>
                
                <a href="{{ route('admin.accounts-and-patients') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.accounts-and-patients') ? 'active' : '' }}">
                    <i class="fas fa-building text-lg mr-4"></i>
                    <span>Company Accounts</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.opd', ['filter' => request('filter','pending')]) }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.opd*') ? 'active' : '' }}">
                    <i class="fas fa-hospital text-lg mr-4"></i>
                    <span>OPD Entries</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.medical-staff') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.medical-staff*') ? 'active' : '' }}">
                    <i class="fas fa-user-md text-lg mr-4"></i>
                    <span>Medical Staff</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <a href="{{ route('admin.inventory.index') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                    <i class="fas fa-boxes text-lg mr-4"></i>
                    <span>Inventory Management</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
                
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-4 mt-8 mb-4">Content Management</div>
                
                <a href="{{ route('admin.page-contents.index') }}" class="nav-item flex items-center px-4 py-4 rounded-2xl font-medium {{ request()->routeIs('admin.page-contents*') ? 'active' : '' }}">
                    <i class="fas fa-edit text-lg mr-4"></i>
                    <span>Page Contents</span>
                    <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                </a>
            </nav>
            
            <!-- User profile section -->
            <div class="p-6 border-t border-gray-200">
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr(Auth::user()->fname ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-semibold text-sm truncate">
                                {{ Auth::user()->fname ?? 'Admin' }} {{ Auth::user()->lname ?? 'User' }}
                            </p>
                            <p class="text-gray-600 text-xs">System Administrator</p>
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
                            <p class="text-gray-600 text-sm font-medium">Welcome back, {{ Auth::user()->fname ?? 'Admin' }}!</p>
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
                                       class="bg-gray-50 border border-gray-200 pl-12 pr-4 py-3 rounded-2xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 w-80" 
                                       placeholder="Search patients, appointments..."
                                       autocomplete="off"
                                       id="header-search">
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notifications-toggle" class="bg-gray-50 hover:bg-gray-100 border border-gray-200 p-3 rounded-2xl text-gray-600 hover:text-gray-900 transition-all duration-300 relative">
                                <i class="fas fa-bell text-lg"></i>
                                <span id="header-notification-count" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hidden">0</span>
                            </button>
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
            
            // Navigation items are now ready without stagger animation
            
            // Notifications functionality
            initializeNotifications();
            
            // Messages functionality
            initializeMessages();
        });
        
        function initializeNotifications() {
            const notificationsToggle = document.getElementById('notifications-toggle');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const markAllReadBtn = document.getElementById('mark-all-read');
            
            // Toggle notifications dropdown
            if (notificationsToggle) {
                notificationsToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    if (notificationsDropdown.classList.contains('hidden')) {
                        // Position the dropdown relative to the button
                        const rect = notificationsToggle.getBoundingClientRect();
                        notificationsDropdown.style.top = (rect.bottom + 8) + 'px';
                        notificationsDropdown.style.right = (window.innerWidth - rect.right) + 'px';
                        notificationsDropdown.style.left = 'auto';
                        
                        notificationsDropdown.classList.remove('hidden');
                        loadNotifications();
                    } else {
                        notificationsDropdown.classList.add('hidden');
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationsDropdown.contains(e.target) && !notificationsToggle.contains(e.target)) {
                    notificationsDropdown.classList.add('hidden');
                }
            });
            
            // Mark all as read
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                    markAllNotificationsAsRead();
                });
            }
            
            // Load initial notification count
            loadNotificationCount();
            
            // Refresh notifications every 30 seconds
            setInterval(loadNotificationCount, 30000);
        }
        
        function loadNotificationCount() {
            fetch('/admin/notifications/count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationCount(data.count);
                })
                .catch(error => {
                    console.log('Error loading notification count:', error);
                });
        }
        
        function updateNotificationCount(count) {
            const headerCount = document.getElementById('header-notification-count');
            const sidebarCount = document.getElementById('notification-count');
            
            if (count > 0) {
                if (headerCount) {
                    headerCount.textContent = count > 99 ? '99+' : count;
                    headerCount.classList.remove('hidden');
                }
                if (sidebarCount) {
                    sidebarCount.textContent = count > 99 ? '99+' : count;
                    sidebarCount.classList.remove('hidden');
                }
            } else {
                if (headerCount) headerCount.classList.add('hidden');
                if (sidebarCount) sidebarCount.classList.add('hidden');
            }
        }
        
        function loadNotifications() {
            const notificationsList = document.getElementById('notifications-list');
            
            fetch('/admin/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        notificationsList.innerHTML = data.notifications.map(notification => `
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer notification-item ${notification.is_read ? 'opacity-75' : ''}" 
                                 data-id="${notification.id}" onclick="markAsRead(${notification.id})">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas ${getNotificationIcon(notification.type)} text-lg ${getNotificationColor(notification.priority)}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">${notification.title}</p>
                                        <p class="text-xs text-gray-600 line-clamp-2">${notification.message}</p>
                                        <p class="text-xs text-gray-400 mt-1">${notification.time_ago}</p>
                                    </div>
                                    ${!notification.is_read ? '<div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>' : ''}
                                </div>
                            </div>
                        `).join('');
                    } else {
                        notificationsList.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                <p>No notifications</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.log('Error loading notifications:', error);
                    notificationsList.innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                            <p>Error loading notifications</p>
                        </div>
                    `;
                });
        }
        
        function markAsRead(notificationId) {
            fetch(`/admin/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotificationCount();
                    loadNotifications();
                }
            })
            .catch(error => {
                console.log('Error marking notification as read:', error);
            });
        }
        
        function markAllNotificationsAsRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotificationCount();
                    loadNotifications();
                }
            })
            .catch(error => {
                console.log('Error marking all notifications as read:', error);
            });
        }
        
        function getNotificationIcon(type) {
            const icons = {
                'appointment_created': 'fa-calendar-plus',
                'pre_employment_created': 'fa-user-plus',
                'annual_physical_created': 'fa-heartbeat',
                'checklist_completed': 'fa-check-square',
                'lab_exam_completed': 'fa-flask',
                'xray_completed': 'fa-x-ray',
                'ecg_completed': 'fa-heartbeat',
                'pathologist_report_submitted': 'fa-microscope',
                'patient_registered': 'fa-user-check',
                'examination_updated': 'fa-edit',
                'specimen_collected': 'fa-vial',
                'xray_interpreted': 'fa-search',
                'system_alert': 'fa-exclamation-circle'
            };
            return icons[type] || 'fa-bell';
        }
        
        function getNotificationColor(priority) {
            const colors = {
                'high': 'text-red-500',
                'medium': 'text-yellow-500',
                'low': 'text-green-500'
            };
            return colors[priority] || 'text-gray-500';
        }
        
        function initializeMessages() {
            // Load initial message count
            loadMessageCount();
            
            // Refresh message count every 30 seconds
            setInterval(loadMessageCount, 30000);
        }
        
        function loadMessageCount() {
            fetch('/admin/messages/count')
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
        
        
    </script>
    @stack('scripts')

    <!-- Notifications Dropdown Portal (Outside of header to avoid z-index issues) -->
    <div id="notifications-dropdown" class="fixed w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 hidden" style="z-index: 999999;">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <a href="{{ route('admin.notifications') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
            </div>
        </div>
        <div id="notifications-list" class="max-h-96 overflow-y-auto custom-scrollbar">
            <div class="p-4 text-center text-gray-500">
                <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
        <div class="p-3 border-t border-gray-200 text-center">
            <button id="mark-all-read" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Mark All as Read</button>
        </div>
    </div>
</body>
</html> 