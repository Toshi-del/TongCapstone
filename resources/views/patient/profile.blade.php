@extends('layouts.patient')

@section('title', 'Profile')

@section('content')
        <!-- Profile Header -->
        <div class="content-card profile-gradient rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white/20 rounded-2xl flex items-center justify-center relative">
                        <span class="text-white text-3xl font-bold">
                            {{ substr(Auth::user()->fname, 0, 1) }}{{ substr(Auth::user()->lname, 0, 1) }}
                        </span>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-camera text-indigo-600 text-sm"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-2 text-white">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h1>
                        <p class="text-white/90 text-lg">Manage your personal information and preferences</p>
                        <div class="mt-3">
                            <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full border border-white/30">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right bg-white/10 rounded-2xl p-4">
                    <p class="text-white text-sm font-medium">Member Since</p>
                    <p class="text-2xl font-bold text-white">{{ Auth::user()->created_at->format('Y') }}</p>
                    <p class="text-white/80 text-sm">{{ Auth::user()->created_at->format('M d') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Profile Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Personal Information -->
            <div class="lg:col-span-2">
                <div class="content-card rounded-2xl p-8">
                    <div class="flex items-center space-x-3 mb-8">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-edit text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Personal Information</h2>
                            <p class="text-gray-600">Update your personal details and contact information</p>
                        </div>
                    </div>
            
                    <form method="POST" action="{{ route('patient.profile.update') }}" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name Fields -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Full Name</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-medium text-gray-700">
                                        <i class="fas fa-user text-indigo-500 mr-2"></i>First Name
                                    </label>
                                    <input type="text" name="fname" value="{{ Auth::user()->fname }}" 
                                           class="form-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter your first name">
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-medium text-gray-700">
                                        <i class="fas fa-user text-indigo-500 mr-2"></i>Last Name
                                    </label>
                                    <input type="text" name="lname" value="{{ Auth::user()->lname }}" 
                                           class="form-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter your last name">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="flex items-center text-sm font-medium text-gray-700">
                                        <i class="fas fa-user text-indigo-500 mr-2"></i>Middle Name
                                    </label>
                                    <input type="text" name="mname" value="{{ Auth::user()->mname }}" 
                                           class="form-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter your middle name (optional)">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-medium text-gray-700">
                                        <i class="fas fa-envelope text-indigo-500 mr-2"></i>Email Address
                                    </label>
                                    <input type="email" name="email" value="{{ Auth::user()->email }}" 
                                           class="form-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter your email address">
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-medium text-gray-700">
                                        <i class="fas fa-phone text-indigo-500 mr-2"></i>Phone Number
                                    </label>
                                    <input type="tel" name="phone" value="{{ Auth::user()->phone }}" 
                                           class="form-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                           placeholder="Enter your phone number">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Professional Information -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Professional Information</h3>
                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-medium text-gray-700">
                                    <i class="fas fa-building text-indigo-500 mr-2"></i>Company/Organization
                                </label>
                                <input type="text" name="company" value="{{ Auth::user()->company }}" 
                                       class="form-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                       placeholder="Enter your company or organization">
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium">
                                <i class="fas fa-times mr-2"></i>Cancel Changes
                            </button>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Profile Sidebar -->
            <div class="space-y-6">
                <!-- Account Summary -->
                <div class="content-card rounded-2xl p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-green-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Account Summary</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar text-blue-500"></i>
                                <span class="text-sm font-medium text-gray-700">Member Since</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-tag text-indigo-500"></i>
                                <span class="text-sm font-medium text-gray-700">Account Type</span>
                            </div>
                            <span class="text-sm font-semibold text-indigo-600">{{ ucfirst(Auth::user()->role) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span class="text-sm font-medium text-gray-700">Status</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">Active</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="content-card rounded-2xl p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bolt text-purple-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <button class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-xl transition-colors duration-200">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-key text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Change Password</p>
                                <p class="text-xs text-gray-500">Update your account password</p>
                            </div>
                        </button>
                        
                        <button class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-xl transition-colors duration-200">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-download text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Download Data</p>
                                <p class="text-xs text-gray-500">Export your medical records</p>
                            </div>
                        </button>
                        
                        <button class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-xl transition-colors duration-200">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bell text-orange-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Notifications</p>
                                <p class="text-xs text-gray-500">Manage your preferences</p>
                            </div>
                        </button>
                    </div>
                </div>
                
                <!-- Security Tips -->
                <div class="content-card rounded-2xl p-6 bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lightbulb text-yellow-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Security Tips</h3>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <p class="text-gray-700">Use a strong, unique password</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <p class="text-gray-700">Keep your contact info updated</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <p class="text-gray-700">Review your account regularly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Form input enhancements
            document.querySelectorAll('.form-input').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
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

            // Form validation feedback
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
                    submitBtn.disabled = true;
                });
            }

            // Quick action buttons
            document.querySelectorAll('button[class*="w-full flex items-center"]').forEach(button => {
                button.addEventListener('click', function() {
                    // Add visual feedback
                    this.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                    }, 200);
                });
            });

            console.log('Patient profile initialized with modern interactions');
        });
    </script>
@endsection 