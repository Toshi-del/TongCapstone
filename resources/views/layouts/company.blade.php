<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Company Portal') - RSS Citi Health Services</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Fallback for Font Awesome -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Font Awesome loaded
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-home';
            testIcon.style.display = 'none';
            document.body.appendChild(testIcon);
            
            const computedStyle = window.getComputedStyle(testIcon, ':before');
            if (computedStyle.content === 'none' || computedStyle.content === '') {
                console.warn('Font Awesome not loaded, trying alternative CDN');
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://use.fontawesome.com/releases/v6.5.2/css/all.css';
                document.head.appendChild(link);
            }
            document.body.removeChild(testIcon);
        });
    </script>
    
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #1e3a8a;
            --info-color: #0891b2;
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
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
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.5s;
        }
        
        .nav-item:hover::before {
            left: 100%;
        }
        
        .nav-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }
        
        .nav-item.active {
            background: #1e40af;
            color: white;
            border-left: 4px solid #3b82f6;
        }
        
        .nav-section {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
        
        .nav-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .nav-section-title {
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            padding: 0 1rem;
        }
        
        .notification-badge {
            background: #dc2626;
            animation: pulse 2s infinite;
        }
        
        .search-bar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .search-bar:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .profile-modal {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .modal-active {
            overflow: hidden;
        }
        
        .modal-active .glass-sidebar {
            filter: blur(2px);
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .mobile-menu-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        @media (max-width: 768px) {
            .glass-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .glass-sidebar.mobile-open {
                transform: translateX(0);
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-800 min-h-screen">
    @auth
    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 mobile-menu-overlay z-40 hidden md:hidden"></div>
    
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="w-72 glass-sidebar relative z-10 shadow-2xl flex flex-col h-full">
            <!-- Header -->
            <div class="p-8 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Company Portal</h1>
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
                        <a href="{{ route('company.dashboard') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('company.dashboard') ? 'active text-white' : '' }}">
                            <i class="fas fa-chart-line mr-4 text-lg"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('company.appointments.index') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('company.appointments.*') ? 'active text-white' : '' }}">
                            <i class="fas fa-calendar-check mr-4 text-lg"></i>
                            <span class="font-medium">Appointments</span>
                            <span class="ml-auto notification-badge w-2 h-2 bg-red-500 rounded-full"></span>
                        </a>
                        <a href="{{ route('company.pre-employment.index') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('company.pre-employment.*') ? 'active text-white' : '' }}">
                            <i class="fas fa-briefcase mr-4 text-lg"></i>
                            <span class="font-medium">Pre-Employment</span>
                        </a>
                        <a href="{{ route('company.medical-results') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('company.medical-results*') ? 'active text-white' : '' }}">
                            <i class="fas fa-file-medical mr-4 text-lg"></i>
                            <span class="font-medium">Medical Results</span>
                        </a>
                    </div>
                </div>

                <!-- Employee Management Section -->
                <div class="mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-3">Employee Management</h3>
                    <div class="space-y-1">
                        <a href="{{ route('company.account-invitations.index') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('company.account-invitations*') ? 'active text-white' : '' }}">
                            <i class="fas fa-user-plus mr-4 text-lg"></i>
                            <span class="font-medium">Account Invitations</span>
                        </a>
                    </div>
                </div>

                <!-- Communication Section -->
                <div class="mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-3">Communication</h3>
                    <div class="space-y-1">
                        <a href="{{ route('company.messages') }}" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 {{ request()->routeIs('company.messages') ? 'active text-white' : '' }}">
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
                        <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ substr(Auth::user()->full_name ?? 'C', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-semibold text-sm truncate">
                                {{ Auth::user()->full_name ?? 'Company Admin' }}
                            </p>
                            <p class="text-gray-600 text-xs">Company Administrator</p>
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
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Company Dashboard')</h1>
                            <p class="text-sm text-gray-600 mt-1">@yield('page-description', 'Manage your company\'s medical examinations and employee health records')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications Dropdown -->
                        <div class="relative">
                            <button id="notificationButton" class="relative p-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200">
                                <i class="fas fa-bell text-lg"></i>
                                <span id="notification-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold hidden">0</span>
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
                        
                        <!-- Current Time -->
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-medium text-gray-900" id="current-time"></div>
                            <div class="text-xs text-gray-500">Current Time</div>
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
            <div class="bg-blue-600 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Profile</h3>
                            <p class="text-blue-100 text-sm">Company Administrator</p>
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
                <div class="flex items-center space-x-4 mb-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->full_name ?? 'Company Admin' }}</h4>
                        <p class="text-sm text-gray-600">{{ Auth::user()->email ?? 'admin@company.com' }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Company Administrator</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-2">
                    <a href="{{ route('company.profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-blue-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-user-edit text-gray-500 group-hover:text-blue-600"></i>
                        </div>
                        <span class="font-medium">Edit Profile</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-blue-500"></i>
                    </a>
                </div>

                <!-- Logout Button -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition-all duration-200 font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endauth

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu functionality
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            
            if (mobileMenuBtn && sidebar && mobileOverlay) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                    mobileOverlay.classList.toggle('hidden');
                    document.body.classList.toggle('modal-active');
                });
                
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    mobileOverlay.classList.add('hidden');
                    document.body.classList.remove('modal-active');
                });
            }
            
            // Profile modal functionality
            const profileButton = document.getElementById('profileButton');
            const profileModal = document.getElementById('profileModal');
            const closeModal = document.getElementById('closeModal');
            
            if (profileButton && profileModal && closeModal) {
                profileButton.addEventListener('click', function() {
                    profileModal.classList.remove('hidden');
                    document.body.classList.add('modal-active');
                });
                
                closeModal.addEventListener('click', function() {
                    profileModal.classList.add('hidden');
                    document.body.classList.remove('modal-active');
                });
                
                profileModal.addEventListener('click', function(e) {
                    if (e.target === profileModal) {
                        profileModal.classList.add('hidden');
                        document.body.classList.remove('modal-active');
                    }
                });
            }
            
            // Update current time
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
                const timeElement = document.getElementById('current-time');
                if (timeElement) {
                    timeElement.textContent = timeString;
                }
            }
            
            // Update time immediately and then every minute
            updateTime();
            setInterval(updateTime, 60000);
            
            // Initialize messages functionality
            initializeMessages();
            
            // Initialize notifications functionality
            initializeNotifications();
            
            // Search functionality
            const searchInput = document.querySelector('.search-bar');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    // Add search functionality here if needed
                    console.log('Searching for:', query);
                });
            }
        });
        
        function initializeMessages() {
            // Load initial message count
            loadMessageCount();
            
            // Refresh message count every 30 seconds
            setInterval(loadMessageCount, 30000);
        }
        
        function loadMessageCount() {
            fetch('/company/messages/count')
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
        
        // Notifications functionality
        function initializeNotifications() {
            const notificationButton = document.getElementById('notificationButton');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const markAllRead = document.getElementById('markAllRead');
            
            if (notificationButton && notificationDropdown) {
                // Toggle dropdown
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
                
                // Mark all as read
                if (markAllRead) {
                    markAllRead.addEventListener('click', function() {
                        fetch('/company/notifications/mark-all-read', {
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
                }
            }
            
            // Load initial notifications
            loadNotifications();
            
            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);
        }
        
        function loadNotifications() {
            fetch('/company/notifications?limit=10')
                .then(response => response.json())
                .then(data => {
                    const notificationBadge = document.getElementById('notification-badge');
                    const notificationList = document.getElementById('notificationList');
                    const notifications = data.notifications;
                    
                    // Update badge count
                    fetch('/company/notifications/count')
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
        
        function markNotificationRead(notificationId) {
            fetch(`/company/notifications/${notificationId}/read`, {
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
    </script>
</body>
</html> 