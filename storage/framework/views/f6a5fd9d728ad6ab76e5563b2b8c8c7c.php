<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Radiologic Technologist Dashboard - RSS Citi Health Services'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <style>
        :root {
            --primary-color: #0891b2;
            --secondary-color: #06b6d4;
            --accent-color: #0e7490;
            --info-color: #0369a1;
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --glass-bg: rgba(6, 182, 212, 0.05);
            --glass-border: rgba(6, 182, 212, 0.1);
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
            background: #0891b2;
            color: white;
            border-left: 4px solid #06b6d4;
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
            background: linear-gradient(90deg, #06b6d4, #0891b2);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .search-container:focus-within::after {
            width: 100%;
        }
    </style>
    
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body class="h-full bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-72 glass-sidebar relative z-10 shadow-2xl flex flex-col h-full">
            <!-- Header -->
            <div class="p-8 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-x-ray text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Radiologic Technologist</h1>
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
                        <a href="<?php echo e(route('radtech.dashboard')); ?>" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('radtech.dashboard') ? 'active text-white' : ''); ?>">
                            <i class="fas fa-th-large mr-4 text-lg"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- X-Ray Services Section -->
                <div class="mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-3">X-Ray Services</h3>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('radtech.pre-employment-xray')); ?>" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('radtech.pre-employment-xray*') ? 'active text-white' : ''); ?>">
                            <i class="fas fa-briefcase mr-4 text-lg"></i>
                            <span class="font-medium">Pre-Employment X-Ray</span>
                        </a>
                        <a href="<?php echo e(route('radtech.annual-physical-xray')); ?>" class="nav-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('radtech.annual-physical-xray*') ? 'active text-white' : ''); ?>">
                            <i class="fas fa-user-md mr-4 text-lg"></i>
                            <span class="font-medium">Annual Physical X-Ray</span>
                        </a>
                    </div>
                </div>

                <!-- Medical Imaging Section -->
               
            </nav>

            <!-- User Profile Section - Sticky Bottom -->
            <div class="flex-shrink-0 p-6 border-t border-gray-200 mt-auto">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            <?php echo e(substr(Auth::user()->fname ?? 'R', 0, 1)); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-semibold text-sm truncate">
                                <?php echo e(Auth::user()->fname ?? 'Radtech'); ?> <?php echo e(Auth::user()->lname ?? 'User'); ?>

                            </p>
                            <p class="text-gray-600 text-xs">Radiologic Technologist</p>
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
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                        <p class="text-sm text-gray-600 mt-1"><?php echo $__env->yieldContent('page-description', 'Radiologic Technologist Portal'); ?></p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="p-3 text-gray-600 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition-all duration-200">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full notification-badge"></span>
                            </button>
                        </div>
                        
                        <!-- X-Ray Queue -->
                        <div class="relative">
                            <button class="p-3 text-gray-600 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition-all duration-200">
                                <i class="fas fa-x-ray text-lg"></i>
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-cyan-500 rounded-full notification-badge"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-0 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fas fa-x-ray text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Profile</h3>
                            <p class="text-cyan-100 text-sm">Radiologic Technologist</p>
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
                <div class="flex items-center space-x-4 mb-8 p-4 bg-cyan-50 rounded-xl border border-cyan-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-x-ray text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900"><?php echo e(Auth::user()->fname); ?> <?php echo e(Auth::user()->lname); ?></h4>
                        <p class="text-sm text-gray-600"><?php echo e(Auth::user()->email); ?></p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 bg-cyan-100 text-cyan-700 text-xs font-medium rounded-full">Radiologic Technologist</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-2">
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-cyan-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-user-edit text-gray-500 group-hover:text-cyan-600"></i>
                        </div>
                        <span class="font-medium">Edit Profile</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-cyan-500"></i>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-cyan-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-cog text-gray-500 group-hover:text-cyan-600"></i>
                        </div>
                        <span class="font-medium">Settings</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-cyan-500"></i>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 rounded-xl transition-all duration-200 group">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-cyan-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i class="fas fa-question-circle text-gray-500 group-hover:text-cyan-600"></i>
                        </div>
                        <span class="font-medium">Help & Support</span>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-cyan-500"></i>
                    </a>
                    
                    <div class="border-t border-gray-200 my-4"></div>
                    
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="block">
                        <?php echo csrf_field(); ?>
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

    <?php echo $__env->yieldContent('scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    
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
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\rss_new-1\resources\views/layouts/radtech.blade.php ENDPATH**/ ?>