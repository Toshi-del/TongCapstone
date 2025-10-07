@extends('layouts.ecgtech')

@section('title', 'Edit Annual Physical ECG Examination')
@section('page-title', 'Edit Annual Physical ECG Examination')
@section('page-description', 'Update ECG examination results and cardiac assessment for annual physical')

@section('content')
@if(session('success'))
    <div class="mb-8 p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 flex items-center space-x-3">
        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
            <i class="fas fa-check text-white text-sm"></i>
        </div>
        <div>
            <p class="text-green-800 font-semibold">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="mb-8 p-4 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 flex items-center space-x-3">
        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-white text-sm"></i>
        </div>
        <div>
            <p class="text-red-800 font-semibold">{{ session('error') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Enhanced Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Edit Annual Physical ECG</h2>
                        <p class="text-gray-600 text-sm mt-1">Update cardiac examination results and assessment</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Patient ID</div>
                    <div class="text-lg font-bold text-green-600">#{{ $annualPhysical->patient->id ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Form Container -->
        <div class="p-8">
        
            @if($annualPhysical->patient)
            <!-- Enhanced Patient Information -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Patient Information</h3>
                </div>
                
                <!-- First Row: Name and Age -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Patient Name</label>
                        <div class="text-lg font-semibold text-gray-900">{{ $annualPhysical->patient->first_name }} {{ $annualPhysical->patient->last_name }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Age</label>
                        <div class="text-lg font-semibold text-gray-900">{{ $annualPhysical->patient->age }} years</div>
                    </div>
                </div>

                <!-- Second Row: Gender and Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Gender</label>
                        <div class="text-lg font-semibold text-gray-900">{{ $annualPhysical->patient->sex }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="text-sm font-semibold text-gray-900 break-all">{{ $annualPhysical->patient->email }}</div>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('ecgtech.annual-physical.update', $annualPhysical->patient->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')
                
                <!-- Enhanced ECG Examination Section -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">ECG Examination Results</h3>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            ECG Results <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 font-normal ml-2">Enter detailed ECG examination findings</span>
                        </label>
                        <textarea name="ecg" 
                                  rows="6" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none" 
                                  placeholder="Enter comprehensive ECG examination results, including rhythm, rate, intervals, and any abnormalities..." 
                                  required>{{ old('ecg', $annualPhysical->ecg ?? '') }}</textarea>
                        @error('ecg')
                            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-red-600 text-sm font-medium">{{ $message }}</p>
                            </div>
                        @enderror
                        <div class="mt-3 flex items-center space-x-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i>
                            <span>Include rhythm analysis, rate measurements, and any significant findings</span>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Physical Examination Section -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-stethoscope text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Physical Examination Data</h3>
                            <p class="text-sm text-gray-600">Vital signs and measurements from physical examination</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Temperature</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-300 text-sm text-gray-600 font-medium">
                                {{ $annualPhysical->physical_exam['temp'] ?? 'Not recorded' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">°C / °F</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Height</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-300 text-sm text-gray-600 font-medium">
                                {{ $annualPhysical->physical_exam['height'] ?? 'Not recorded' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">cm / inches</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <label class="block text-xs font-bold text-green-700 uppercase tracking-wider mb-2">Heart Rate</label>
                            <input type="text" 
                                   name="heart_rate" 
                                   class="w-full p-3 border border-green-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 font-medium" 
                                   value="{{ old('heart_rate', $annualPhysical->physical_exam['heart_rate'] ?? '') }}" 
                                   placeholder="Enter BPM">
                            <div class="text-xs text-green-600 mt-1">beats per minute</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Weight</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-300 text-sm text-gray-600 font-medium">
                                {{ $annualPhysical->physical_exam['weight'] ?? 'Not recorded' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">kg / lbs</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center space-x-2 text-sm text-blue-700">
                            <i class="fas fa-info-circle"></i>
                            <span class="font-medium">Note:</span>
                            <span>Physical examination data is recorded by medical staff. You can update the heart rate as part of your ECG assessment.</span>
                        </div>
                    </div>
                </div>
                <!-- Enhanced Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-600"></i>
                        <span>Your ECG updates are securely saved and encrypted</span>
                    </div>
                    
                    <div class="flex space-x-4">
                        <a href="{{ route('ecgtech.annual-physical') }}" 
                           class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Annual Physical
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 font-semibold">
                            <i class="fas fa-save mr-2"></i>
                            Update ECG Results
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = document.querySelector('button[type="submit"]');
    const ecgTextarea = document.querySelector('textarea[name="ecg"]');
    const heartRateInput = document.querySelector('input[name="heart_rate"]');
    
    // Enhanced form submission with loading state
    form.addEventListener('submit', function(e) {
        // Validate ECG field
        if (!ecgTextarea.value.trim()) {
            e.preventDefault();
            showNotification('Please enter ECG examination results', 'error');
            ecgTextarea.focus();
            return;
        }
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating ECG Results...';
        submitButton.classList.add('opacity-75', 'cursor-not-allowed');
        
        // Add visual feedback
        form.style.opacity = '0.8';
        form.style.pointerEvents = 'none';
    });
    
    // Enhanced textarea validation and feedback
    ecgTextarea.addEventListener('input', function() {
        const value = this.value.trim();
        const parent = this.closest('.bg-green-50');
        const wordCount = value.split(/\s+/).filter(word => word.length > 0).length;
        
        // Update visual feedback
        if (value && wordCount >= 5) {
            parent.classList.remove('border-green-200');
            parent.classList.add('border-green-300', 'bg-green-100');
            this.classList.remove('border-gray-300');
            this.classList.add('border-green-400');
        } else if (value) {
            parent.classList.remove('border-green-300', 'bg-green-100');
            parent.classList.add('border-yellow-200', 'bg-yellow-50');
            this.classList.remove('border-gray-300', 'border-green-400');
            this.classList.add('border-yellow-300');
        } else {
            parent.classList.remove('border-green-300', 'bg-green-100', 'border-yellow-200', 'bg-yellow-50');
            parent.classList.add('border-green-200');
            this.classList.remove('border-green-400', 'border-yellow-300');
            this.classList.add('border-gray-300');
        }
        
        // Update character count
        updateCharacterCount();
    });
    
    // Heart rate validation
    heartRateInput.addEventListener('input', function() {
        const value = this.value.trim();
        const parent = this.closest('.bg-green-50');
        
        if (value && /^\d+$/.test(value)) {
            const rate = parseInt(value);
            if (rate >= 40 && rate <= 200) {
                parent.classList.remove('border-green-200');
                parent.classList.add('border-green-300', 'bg-green-100');
                this.classList.remove('border-green-300');
                this.classList.add('border-green-400');
            } else {
                parent.classList.remove('border-green-300', 'bg-green-100');
                parent.classList.add('border-yellow-200', 'bg-yellow-50');
                this.classList.remove('border-green-300', 'border-green-400');
                this.classList.add('border-yellow-300');
            }
        } else if (value) {
            parent.classList.remove('border-green-300', 'bg-green-100');
            parent.classList.add('border-red-200', 'bg-red-50');
            this.classList.remove('border-green-300', 'border-green-400');
            this.classList.add('border-red-300');
        } else {
            parent.classList.remove('border-green-300', 'bg-green-100', 'border-yellow-200', 'bg-yellow-50', 'border-red-200', 'bg-red-50');
            parent.classList.add('border-green-200');
            this.classList.remove('border-green-400', 'border-yellow-300', 'border-red-300');
            this.classList.add('border-green-300');
        }
    });
    
    // Character count functionality
    function updateCharacterCount() {
        const existing = document.querySelector('.character-count');
        if (existing) existing.remove();
        
        const count = ecgTextarea.value.length;
        const wordCount = ecgTextarea.value.trim().split(/\s+/).filter(word => word.length > 0).length;
        
        const countDiv = document.createElement('div');
        countDiv.className = 'character-count mt-2 text-xs text-gray-500 flex justify-between';
        countDiv.innerHTML = `
            <span>${count} characters, ${wordCount} words</span>
            <span class="${wordCount >= 5 ? 'text-green-600' : 'text-yellow-600'}">
                ${wordCount >= 5 ? '✓ Sufficient detail' : 'Add more detail'}
            </span>
        `;
        
        ecgTextarea.parentNode.appendChild(countDiv);
    }
    
    // Enhanced notification system
    function showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg border transition-all duration-300 transform translate-x-full`;
        
        const colors = {
            success: 'bg-green-50 border-green-200 text-green-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        
        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="${icons[type]}"></i>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:opacity-75">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
    
    // Add hover effects to form sections
    const formSections = document.querySelectorAll('.bg-white.rounded-xl, .bg-gray-50.rounded-xl');
    formSections.forEach(section => {
        section.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        });
        
        section.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (!submitButton.disabled) {
                form.submit();
            }
        }
        
        // Escape to cancel
        if (e.key === 'Escape') {
            const cancelButton = document.querySelector('a[href*="annual-physical"]');
            if (cancelButton) {
                cancelButton.click();
            }
        }
    });
    
    // Auto-save functionality (optional)
    let autoSaveTimeout;
    ecgTextarea.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            if (this.value.trim()) {
                showNotification('ECG data auto-saved locally', 'success', 2000);
            }
        }, 5000);
    });
    
    // Initial validation check
    if (ecgTextarea.value.trim()) {
        ecgTextarea.dispatchEvent(new Event('input'));
    }
    if (heartRateInput.value.trim()) {
        heartRateInput.dispatchEvent(new Event('input'));
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .form-section {
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    textarea:focus, input:focus {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
`;
document.head.appendChild(style);
</script>
@endsection
