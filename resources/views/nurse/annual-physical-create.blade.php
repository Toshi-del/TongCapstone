@extends('layouts.nurse')

@section('title', 'Create Annual Physical Examination - RSS Citi Health Services')
@section('page-title', 'Create Annual Physical Examination')
@section('page-description', 'New yearly health assessment and comprehensive medical checkup')

@section('content')
<div class="space-y-8">
    <!-- Validation Errors -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold mb-2">Please complete all required fields:</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-circle text-xs text-red-500"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Create Annual Physical Examination</h2>
                        <p class="text-purple-100 text-sm">Comprehensive yearly health assessment form</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/90 text-sm">Form Type</div>
                    <div class="text-white font-bold text-lg">Annual Physical</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information Card -->
    @if($patient)
    <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-purple-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Patient Information</h3>
                <p class="text-gray-600 text-sm">Basic patient details for examination</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Patient Name</label>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-semibold text-sm">
                            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                        </span>
                    </div>
                    <div class="text-lg font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Age</label>
                <div class="text-lg font-semibold text-gray-900">{{ $patient->age }} years old</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Gender</label>
                <div class="text-lg font-semibold text-gray-900">{{ ucfirst($patient->sex) }}</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Email</label>
                <div class="text-sm font-medium text-gray-900">{{ $patient->email }}</div>
            </div>
        </div>
    </div>
    @endif
    <!-- Examination Form -->
    <form action="{{ route('nurse.annual-physical.store') }}" method="POST" class="space-y-8">
        @csrf
        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
        
        <!-- Physical Examination Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Physical Examination</h3>
                    <p class="text-gray-600 text-sm">Vital signs and basic physical measurements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php $phys = old('physical_exam', []); @endphp
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Temperature <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[temp]" value="{{ $phys['temp'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('physical_exam.temp') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 36.5°C" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-thermometer-half text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.temp')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Height <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[height]" value="{{ $phys['height'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('physical_exam.height') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 170 cm" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-ruler-vertical text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.height')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Heart Rate <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[heart_rate]" value="{{ $phys['heart_rate'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('physical_exam.heart_rate') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 72 bpm" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-heartbeat text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.heart_rate')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Weight <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[weight]" value="{{ $phys['weight'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('physical_exam.weight') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 65 kg" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-weight text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.weight')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Skin Identification Marks Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Skin Identification Marks</h3>
                    <p class="text-gray-600 text-sm">Notable skin marks, scars, or identifying features</p>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                    Skin Marks Description <span class="text-red-500">*</span>
                </label>
                <textarea name="skin_marks" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('skin_marks') border-red-500 ring-2 ring-red-200 @enderror" 
                          placeholder="Describe any visible skin marks, scars, tattoos, or identifying features..." required>{{ old('skin_marks') }}</textarea>
                @error('skin_marks')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
            </div>
        </div>
        
        <!-- Vision and Tests Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Vision and Medical Tests</h3>
                    <p class="text-gray-600 text-sm">Visual acuity, color vision, and examination findings</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Visual Acuity <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="visual" value="{{ old('visual') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('visual') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 20/20" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-eye text-gray-400"></i>
                        </div>
                    </div>
                    @error('visual')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                @php
                    $medicalTestName = $patient->appointment->medicalTest->name ?? '';
                    $isAnnualMedicalExam = in_array(strtolower($medicalTestName), [
                        'annual medical examination',
                        'annual medical examination with drug test',
                        'annual medical examination with drug test and ecg'
                    ]);
                    $hasDrugTest = in_array(strtolower($medicalTestName), [
                        'annual medical examination with drug test',
                        'annual medical examination with drug test and ecg',
                        'annual medical with drug test',
                        'annual medical with drug test and ecg'
                    ]);
                @endphp
                
                @if(!$isAnnualMedicalExam)
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Ishihara Test <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="ishihara_test" value="{{ old('ishihara_test') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('ishihara_test') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., Normal" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-palette text-gray-400"></i>
                        </div>
                    </div>
                    @error('ishihara_test')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                @else
                <!-- Ishihara test hidden for Annual Medical Examinations -->
                <input type="hidden" name="ishihara_test" value="N/A - Not required for Annual Medical Examination" />
                @endif

              
                    @error('findings')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        @if($hasDrugTest)
        <!-- Drug Test Form Component -->
        <x-drug-test-form 
            exam-type="annual-physical"
            :patient-data="[
                'name' => $patient->first_name . ' ' . $patient->last_name,
                'address' => $patient->address ?? '',
                'age' => $patient->age ?? '',
                'gender' => ucfirst($patient->sex ?? '')
            ]"
            :is-edit="true" />
        @endif

        <!-- Signature Section -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-signature text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Medical Technologist Signature</h3>
                    <p class="text-gray-600 text-sm">Examination completed by: {{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Medical Technologist</p>
                        <div class="border-b-2 border-gray-400 w-64 mt-3 mb-2"></div>
                        <p class="text-xs text-gray-500">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Date</p>
                        <div class="border-b-2 border-gray-400 w-32 mt-3 mb-2"></div>
                        <p class="text-xs text-gray-500">{{ now()->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-end pt-8 border-t border-gray-200 space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('nurse.annual-physical') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" 
                    class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-semibold shadow-lg">
                <i class="fas fa-save mr-2"></i>Create Examination
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations to content cards
        const contentCards = document.querySelectorAll('.content-card');
        contentCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });

        // Form validation enhancement
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                } else {
                    this.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                    this.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-200');
                }
            });

            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'ring-red-200');
                    this.classList.add('border-emerald-500', 'ring-emerald-200');
                }
            });
        });

        // Form submission confirmation
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('input[required], textarea[required]');
            let hasErrors = false;
            
            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    hasErrors = true;
                    field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('Please fill in all required fields before submitting.');
                return;
            }
            
            if (!confirm('Are you sure you want to create this annual physical examination?')) {
                e.preventDefault();
            }
        });
    });
</script>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
    }
</style>
@endpush
@endsection
