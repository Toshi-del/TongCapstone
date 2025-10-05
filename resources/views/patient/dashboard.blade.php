@extends('layouts.patient')

@section('title', 'Dashboard')

@section('content')
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
                    <a href="#" class="nav-item px-4 py-2 text-gray-700 active">
                        <i class="fas fa-th-large mr-2"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('patient.profile') }}" class="nav-item px-4 py-2 text-gray-700">
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
        
        <!-- Welcome Section -->
        <div class="content-card patient-gradient rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-user-injured text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-2 text-white">Welcome, {{ Auth::user()->fname }}!</h1>
                        <p class="text-white/90 text-lg">Manage your health records and appointments</p>
                        <div class="mt-3">
                            <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full border border-white/30">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right bg-white/10 rounded-2xl p-4">
                    <p class="text-2xl font-bold text-white">{{ now()->format('M d, Y') }}</p>
                    <p class="text-white/80">{{ now()->format('l') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Patient Information -->
        <div class="content-card rounded-2xl p-6 mb-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-id-card text-indigo-600 text-lg"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Patient Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-envelope text-indigo-600"></i>
                        <label class="text-sm font-medium text-gray-600">Email Address</label>
                    </div>
                    <p class="font-semibold text-gray-800">{{ Auth::user()->email }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-phone text-indigo-600"></i>
                        <label class="text-sm font-medium text-gray-600">Phone Number</label>
                    </div>
                    <p class="font-semibold text-gray-800">{{ Auth::user()->phone ?? 'Not provided' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-building text-indigo-600"></i>
                        <label class="text-sm font-medium text-gray-600">Company</label>
                    </div>
                    <p class="font-semibold text-gray-800">{{ Auth::user()->company ?? 'Not specified' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Medical Records Tabs -->
        <div class="content-card rounded-2xl overflow-hidden mb-8">
            <!-- Tab Headers -->
            <div class="flex border-b border-gray-200">
                <button class="tab-button flex-1 px-6 py-4 text-left font-semibold active" id="tests-tab" onclick="openTab('tests')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-clipboard-list text-lg"></i>
                        <div>
                            <p class="font-bold">Appointments & Tests</p>
                            <p class="text-sm opacity-80">View your scheduled appointments and medical tests</p>
                        </div>
                    </div>
                </button>
                <button class="tab-button flex-1 px-6 py-4 text-left font-semibold" id="results-tab" onclick="openTab('results')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-medical text-lg"></i>
                        <div>
                            <p class="font-bold">Medical Results</p>
                            <p class="text-sm opacity-80">Access your examination results and reports</p>
                        </div>
                    </div>
                </button>
            </div>
            
            <!-- Tab Content -->
            <div class="p-6">
                <!-- Tests Tab -->
                <div id="tests" class="tab-content">
                    <!-- Header with Stats and Action -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Quick Stats -->
                        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 border border-blue-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-calendar-check text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-blue-700">{{ Auth::user()->patientAppointments()->count() }}</p>
                                        <p class="text-sm text-blue-600 font-medium">Total Appointments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-4 border border-green-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-check-circle text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-green-700">{{ Auth::user()->patientAppointments()->where('status', 'approved')->count() }}</p>
                                        <p class="text-sm text-green-600 font-medium">Approved</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-4 border border-yellow-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-clock text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-yellow-700">{{ Auth::user()->patientAppointments()->where('status', 'pending')->count() }}</p>
                                        <p class="text-sm text-yellow-600 font-medium">Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Request New Appointment Card -->
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                            <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                            <div class="relative z-10">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                                    <i class="fas fa-plus text-white text-xl"></i>
                                </div>
                                <h4 class="text-lg font-bold mb-2">Need an Appointment?</h4>
                                <p class="text-white/90 text-sm mb-4">Schedule your next medical examination</p>
                                <button class="w-full bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 text-white font-semibold py-2 px-4 rounded-xl transition-all duration-200 hover:scale-105">
                                    Request Now
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Appointments Section -->
                    <div class="mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-blue-600 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-800">Your Appointments</h4>
                                    <p class="text-sm text-gray-600">Manage your scheduled medical appointments</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="px-4 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                <button class="px-4 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-sort mr-2"></i>Sort
                                </button>
                            </div>
                        </div>
                        
                        <!-- Appointments Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse(Auth::user()->patientAppointments()->orderBy('created_at', 'desc')->get() as $appointment)
                            <div class="bg-white rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 overflow-hidden">
                                <!-- Status Header -->
                                <div class="px-6 py-4 {{ $appointment->status == 'approved' ? 'bg-gradient-to-r from-green-500 to-green-600' : ($appointment->status == 'pending' ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-red-500 to-red-600') }}">
                                    <div class="flex items-center justify-between text-white">
                                        <div class="flex items-center space-x-2">
                                            @if($appointment->status == 'approved')
                                                <i class="fas fa-check-circle"></i>
                                                <span class="font-semibold">Approved</span>
                                            @elseif($appointment->status == 'pending')
                                                <i class="fas fa-clock"></i>
                                                <span class="font-semibold">Pending Review</span>
                                            @else
                                                <i class="fas fa-times-circle"></i>
                                                <span class="font-semibold">Declined</span>
                                            @endif
                                        </div>
                                        <i class="fas fa-calendar-alt text-white/80"></i>
                                    </div>
                                </div>
                                
                                <!-- Appointment Details -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <h5 class="text-lg font-bold text-gray-800 mb-2">{{ $appointment->appointment_type ?? 'General Checkup' }}</h5>
                                        <div class="flex items-center space-x-2 text-gray-600 mb-2">
                                            <i class="fas fa-calendar text-blue-500"></i>
                                            <span class="font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <i class="fas fa-clock text-blue-500"></i>
                                            <span class="font-medium">{{ $appointment->time_slot }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t border-gray-100 pt-4">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-500">Requested on</span>
                                            <span class="font-medium text-gray-700">{{ $appointment->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($appointment->status == 'approved')
                                    <div class="mt-4">
                                        <button class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full">
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-12 text-center border-2 border-dashed border-gray-300">
                                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-calendar-plus text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Appointments Yet</h3>
                                    <p class="text-gray-500 mb-6">You haven't scheduled any appointments. Start by requesting your first appointment.</p>
                                    <button class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 hover:scale-105 shadow-lg">
                                        <i class="fas fa-plus mr-2"></i>Schedule Your First Appointment
                                    </button>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pre-Employment Records Section -->
                    <div class="mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-briefcase text-orange-600 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-800">Pre-Employment Records</h4>
                                    <p class="text-sm text-gray-600">Track your employment medical examinations</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="px-4 py-2 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-download mr-2"></i>Export
                                </button>
                                <button class="px-4 py-2 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </div>
                        
                        <!-- Pre-Employment Records Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @forelse(Auth::user()->preEmploymentRecords()->orderBy('created_at', 'desc')->get() as $record)
                            <div class="bg-white rounded-2xl border border-gray-200 hover:border-orange-300 hover:shadow-lg transition-all duration-300 overflow-hidden">
                                <!-- Status Header -->
                                <div class="px-6 py-4 {{ $record->status == 'completed' ? 'bg-gradient-to-r from-green-500 to-green-600' : ($record->status == 'in_progress' ? 'bg-gradient-to-r from-blue-500 to-blue-600' : ($record->status == 'pending' ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-gray-500 to-gray-600')) }}">
                                    <div class="flex items-center justify-between text-white">
                                        <div class="flex items-center space-x-2">
                                            @if($record->status == 'completed')
                                                <i class="fas fa-check-circle"></i>
                                                <span class="font-semibold">Completed</span>
                                            @elseif($record->status == 'in_progress')
                                                <i class="fas fa-spinner"></i>
                                                <span class="font-semibold">In Progress</span>
                                            @elseif($record->status == 'pending')
                                                <i class="fas fa-clock"></i>
                                                <span class="font-semibold">Pending</span>
                                            @else
                                                <i class="fas fa-question-circle"></i>
                                                <span class="font-semibold">{{ ucfirst($record->status ?? 'Unknown') }}</span>
                                            @endif
                                        </div>
                                        <i class="fas fa-briefcase text-white/80"></i>
                                    </div>
                                </div>
                                
                                <!-- Record Details -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <h5 class="text-lg font-bold text-gray-800 mb-2">{{ $record->full_name }}</h5>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div class="flex items-center space-x-2 text-gray-600">
                                                <i class="fas fa-user text-orange-500"></i>
                                                <span class="font-medium">{{ $record->age }} years, {{ $record->sex }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2 text-gray-600">
                                                <i class="fas fa-stethoscope text-orange-500"></i>
                                                <span class="font-medium">{{ $record->medical_exam_type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($record->company_name)
                                    <div class="mb-4 p-3 bg-orange-50 rounded-xl border border-orange-200">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-building text-orange-600"></i>
                                            <span class="font-semibold text-orange-800">{{ $record->company_name }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="border-t border-gray-100 pt-4">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-500">Created on</span>
                                            <span class="font-medium text-gray-700">{{ $record->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($record->status == 'completed')
                                    <div class="mt-4 flex space-x-2">
                                        <button class="flex-1 bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>View Report
                                        </button>
                                        <button class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-download mr-2"></i>Download
                                        </button>
                                    </div>
                                    @elseif($record->status == 'in_progress')
                                    <div class="mt-4">
                                        <button class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-clock mr-2"></i>Track Progress
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full">
                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-12 text-center border-2 border-dashed border-orange-300">
                                    <div class="w-20 h-20 bg-orange-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-briefcase text-orange-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-orange-700 mb-2">No Employment Records</h3>
                                    <p class="text-orange-600 mb-6">You don't have any pre-employment medical records yet.</p>
                                    <button class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 hover:scale-105 shadow-lg">
                                        <i class="fas fa-plus mr-2"></i>Request Employment Exam
                                    </button>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Results Tab -->
                <div id="results" class="tab-content hidden">
                    <!-- Quick Access to Full Results Page -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 mb-8 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-file-medical text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-1">View All Medical Results</h3>
                                    <p class="text-white/90">Access your complete medical examination results sent by RSS Citi Health Services</p>
                                </div>
                            </div>
                            <a href="{{ route('patient.medical-results') }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 hover:scale-105 flex items-center space-x-2">
                                <i class="fas fa-external-link-alt"></i>
                                <span>View Results</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Results Header -->
                    <div class="mb-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Medical Results Overview</h3>
                                <p class="text-gray-600">Quick overview of your examination results and reports</p>
                            </div>
                            <div class="flex items-center space-x-3 mt-4 md:mt-0">
                                <div class="relative">
                                    <input type="text" id="searchResults" class="w-64 px-4 py-2.5 pl-10 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white" placeholder="Search results...">
                                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                                </div>
                                <button class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl font-semibold transition-colors duration-200">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                            </div>
                        </div>
                        
                        <!-- Results Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-4 border border-emerald-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-file-medical text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-emerald-700">{{ Auth::user()->preEmploymentExaminations()->count() }}</p>
                                        <p class="text-sm text-emerald-600 font-medium">Pre-Employment</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 border border-blue-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-heart-pulse text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-blue-700">{{ Auth::user()->annualPhysicalExaminations()->count() }}</p>
                                        <p class="text-sm text-blue-600 font-medium">Annual Physical</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-4 border border-purple-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-flask text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-purple-700">{{ Auth::user()->preEmploymentExaminations()->whereNotNull('lab_findings')->count() + Auth::user()->annualPhysicalExaminations()->whereNotNull('lab_findings')->count() }}</p>
                                        <p class="text-sm text-purple-600 font-medium">Lab Results</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-2xl p-4 border border-teal-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-teal-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-heartbeat text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-teal-700">{{ Auth::user()->preEmploymentExaminations()->whereNotNull('ecg')->count() + Auth::user()->annualPhysicalExaminations()->whereNotNull('ecg')->count() }}</p>
                                        <p class="text-sm text-teal-600 font-medium">ECG Reports</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pre-Employment Examination Results -->
                    <div class="mb-12">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-medical text-emerald-600 text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-800">Pre-Employment Results</h4>
                                <p class="text-sm text-gray-600">Your employment medical examination results</p>
                            </div>
                        </div>
                        
                        <!-- Pre-Employment Results Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @forelse(Auth::user()->preEmploymentExaminations()->orderBy('created_at', 'desc')->get() as $exam)
                            <div class="bg-white rounded-2xl border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 overflow-hidden">
                                <!-- Status Header -->
                                <div class="px-6 py-4 {{ $exam->status == 'completed' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' : ($exam->status == 'in_progress' ? 'bg-gradient-to-r from-blue-500 to-blue-600' : ($exam->status == 'pending' ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-gray-500 to-gray-600')) }}">
                                    <div class="flex items-center justify-between text-white">
                                        <div class="flex items-center space-x-2">
                                            @if($exam->status == 'completed')
                                                <i class="fas fa-check-circle"></i>
                                                <span class="font-semibold">Results Ready</span>
                                            @elseif($exam->status == 'in_progress')
                                                <i class="fas fa-spinner"></i>
                                                <span class="font-semibold">Processing</span>
                                            @elseif($exam->status == 'pending')
                                                <i class="fas fa-clock"></i>
                                                <span class="font-semibold">Pending</span>
                                            @else
                                                <i class="fas fa-question-circle"></i>
                                                <span class="font-semibold">{{ ucfirst($exam->status ?? 'Unknown') }}</span>
                                            @endif
                                        </div>
                                        <i class="fas fa-file-medical text-white/80"></i>
                                    </div>
                                </div>
                                
                                <!-- Exam Details -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <div class="flex items-center space-x-2 text-gray-600 mb-3">
                                            <i class="fas fa-calendar text-emerald-500"></i>
                                            <span class="font-medium">{{ $exam->date ? $exam->date->format('l, M d, Y') : 'Date not set' }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Results Summary -->
                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                        <div class="text-center p-3 {{ ($exam->physical_findings && count($exam->physical_findings) > 0) ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} rounded-xl">
                                            <i class="fas fa-stethoscope {{ ($exam->physical_findings && count($exam->physical_findings) > 0) ? 'text-green-600' : 'text-gray-400' }} text-lg mb-1"></i>
                                            <p class="text-xs font-medium {{ ($exam->physical_findings && count($exam->physical_findings) > 0) ? 'text-green-700' : 'text-gray-500' }}">Physical</p>
                                        </div>
                                        <div class="text-center p-3 {{ ($exam->lab_findings && count($exam->lab_findings) > 0) ? 'bg-purple-50 border border-purple-200' : 'bg-gray-50 border border-gray-200' }} rounded-xl">
                                            <i class="fas fa-flask {{ ($exam->lab_findings && count($exam->lab_findings) > 0) ? 'text-purple-600' : 'text-gray-400' }} text-lg mb-1"></i>
                                            <p class="text-xs font-medium {{ ($exam->lab_findings && count($exam->lab_findings) > 0) ? 'text-purple-700' : 'text-gray-500' }}">Lab Tests</p>
                                        </div>
                                        <div class="text-center p-3 {{ $exam->ecg ? 'bg-teal-50 border border-teal-200' : 'bg-gray-50 border border-gray-200' }} rounded-xl">
                                            <i class="fas fa-heartbeat {{ $exam->ecg ? 'text-teal-600' : 'text-gray-400' }} text-lg mb-1"></i>
                                            <p class="text-xs font-medium {{ $exam->ecg ? 'text-teal-700' : 'text-gray-500' }}">ECG</p>
                                        </div>
                                    </div>
                                    
                                    @if($exam->status == 'completed')
                                    <div class="flex space-x-2">
                                        <button onclick="viewExamDetails('pre-employment', {{ $exam->id }})" class="flex-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>View Results
                                        </button>
                                        <button class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-download mr-2"></i>Download
                                        </button>
                                    </div>
                                    @elseif($exam->status == 'in_progress')
                                    <div>
                                        <button class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-xl transition-colors duration-200">
                                            <i class="fas fa-clock mr-2"></i>Processing Results
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full">
                                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-12 text-center border-2 border-dashed border-emerald-300">
                                    <div class="w-20 h-20 bg-emerald-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-file-medical text-emerald-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-emerald-700 mb-2">No Pre-Employment Results</h3>
                                    <p class="text-emerald-600">Your pre-employment examination results will appear here once available.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Annual Physical Examination Results -->
                    <div class="mb-8">
                        <h6 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="bi bi-heart-pulse me-2"></i>Annual Physical Examination Results
                        </h6>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-sm">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider">Physical Findings</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider">Lab Findings</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider">ECG</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse(Auth::user()->annualPhysicalExaminations()->orderBy('created_at', 'desc')->get() as $exam)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm">{{ $exam->date ? $exam->date->format('M d, Y') : 'N/A' }}</td>
                                        <td class="py-3 px-4">
                                            @if($exam->status == 'completed')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                            @elseif($exam->status == 'pending')
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($exam->status == 'in_progress')
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In Progress</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ ucfirst($exam->status ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            @if($exam->physical_findings && count($exam->physical_findings) > 0)
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Available</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Not Available</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            @if($exam->lab_findings && count($exam->lab_findings) > 0)
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Available</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Not Available</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            @if($exam->ecg)
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Available</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Not Available</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <button class="text-primary-600 hover:text-primary-800 font-medium" onclick="viewExamDetails('annual-physical', {{ $exam->id }})">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-gray-500">No annual physical examination results found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    </main>
    
    <!-- Modern Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-injured text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-gray-800">RSS Citi Health Services</span>
                    </div>
                    <p class="text-gray-600 text-sm">&copy; {{ date('Y') }} RSS Citi Health Services. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-gray-100 hover:bg-indigo-100 rounded-lg flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-all duration-200">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-100 hover:bg-indigo-100 rounded-lg flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-all duration-200">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-100 hover:bg-indigo-100 rounded-lg flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-all duration-200">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-100 hover:bg-indigo-100 rounded-lg flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-all duration-200">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            window.openTab = function(tabName) {
                // Hide all tab content
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show the selected tab content
                document.getElementById(tabName).classList.remove('hidden');
                
                // Update active tab styling
                const tabs = document.querySelectorAll('.tab-button');
                tabs.forEach(tab => {
                    tab.classList.remove('active');
                });
                
                document.getElementById(tabName + '-tab').classList.add('active');
            }

            // Enhanced navigation interactions
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = '';
                    }
                });
            });

            // Auto-hide success messages
            setTimeout(function() {
                const alerts = document.querySelectorAll('.bg-green-50');
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

            // Search functionality for results
            const searchInput = document.getElementById('searchResults');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#results tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // View exam details function
            window.viewExamDetails = function(type, examId) {
                alert(`Viewing ${type} examination details for ID: ${examId}`);
                // Implement modal or redirect functionality here
            }

            console.log('Patient dashboard initialized with modern interactions');
        });
    </script>
@endsection 