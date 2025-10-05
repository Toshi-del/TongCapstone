<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Patient Portal') - RSS Citi Health Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-indigo: #4f46e5;
            --primary-indigo-dark: #4338ca;
            --primary-indigo-light: #a5b4fc;
            --sidebar-bg: rgba(255, 255, 255, 0.98);
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
        
        .patient-gradient {
            background: linear-gradient(to right, #4f46e5, #4338ca) !important;
        }
        
        .profile-gradient {
            background: linear-gradient(to right, #4f46e5, #4338ca) !important;
        }
        
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            ring-color: var(--primary-indigo);
            border-color: var(--primary-indigo);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .nav-item {
            transition: all 0.3s ease;
            border-radius: 1rem;
        }
        
        .nav-item:hover {
            background-color: rgba(79, 70, 229, 0.1);
        }
        
        .nav-item.active {
            background-color: rgba(79, 70, 229, 0.15);
            color: var(--primary-indigo-dark);
            font-weight: 600;
        }
        
        .tab-button {
            transition: all 0.3s ease;
        }
        
        .tab-button.active {
            background: linear-gradient(to right, #4f46e5, #4338ca);
            color: white;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-indigo);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-indigo-dark);
        }
    </style>
</head>
<body class="bg-slate-50 font-poppins min-h-screen flex flex-col">
    <!-- Modern Header -->
    <header class="bg-white/95 backdrop-blur-sm shadow-sm border-b border-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-injured text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Patient Portal</h1>
                        <p class="text-indigo-600 text-sm font-medium">RSS Citi Health Services</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('patient.dashboard') }}" class="nav-item px-4 py-2 text-gray-700 {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large mr-2"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('patient.medical-results') }}" class="nav-item px-4 py-2 text-gray-700 {{ request()->routeIs('patient.medical-results*') ? 'active' : '' }}">
                        <i class="fas fa-file-medical mr-2"></i>
                        <span class="font-medium">Medical Results</span>
                    </a>
                    <a href="{{ route('patient.profile') }}" class="nav-item px-4 py-2 text-gray-700 {{ request()->routeIs('patient.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span class="font-medium">Profile</span>
                    </a>
                </nav>
                
                <!-- User Profile & Logout -->
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                            <p class="text-sm text-indigo-600">Patient</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                                {{ substr(Auth::user()->fname, 0, 1) }}{{ substr(Auth::user()->lname, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                    
                    <!-- Mobile menu button -->
                    <button id="mobileMenuButton" class="md:hidden p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} RSS Citi Health Services. All rights reserved.</p>
                <p class="mt-1">Your health information is secure and confidential.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        // Add mobile menu functionality if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle logic can be added here
        });
    </script>
</body>
</html>
