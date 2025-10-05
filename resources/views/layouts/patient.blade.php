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
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-user-injured text-blue-600 text-2xl mr-3"></i>
                        <span class="text-xl font-bold text-gray-900">Patient Portal</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('patient.dashboard') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('patient.dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('patient.medical-results') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('patient.medical-results*') ? 'text-blue-600 bg-blue-50' : '' }}">
                            <i class="fas fa-file-medical mr-2"></i>Medical Results
                        </a>
                        <a href="{{ route('patient.profile') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('patient.profile') ? 'text-blue-600 bg-blue-50' : '' }}">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
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
