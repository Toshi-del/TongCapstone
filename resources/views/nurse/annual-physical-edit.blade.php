@extends('layouts.nurse')

@section('title', 'Edit Annual Physical Examination - RSS Citi Health Services')
@section('page-title', 'Edit Annual Physical Examination')
@section('page-description', 'Update yearly health assessment and comprehensive medical checkup')

@section('content')
<div class="space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-edit text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Edit Annual Physical Examination</h2>
                        <p class="text-purple-100 text-sm">Update comprehensive yearly health assessment</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/90 text-sm">Examination ID</div>
                    <div class="text-white font-bold text-lg">#{{ $annualPhysical->id }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information Card -->
    @if($annualPhysical->patient)
    <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-purple-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Patient Information</h3>
                <p class="text-gray-600 text-sm">Patient details for this examination</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Patient Name</label>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-semibold text-sm">
                            {{ substr($annualPhysical->patient->first_name ?? 'P', 0, 1) }}{{ substr($annualPhysical->patient->last_name ?? 'T', 0, 1) }}
                        </span>
                    </div>
                    <div class="text-lg font-semibold text-gray-900">{{ $annualPhysical->patient->full_name ?? $annualPhysical->patient->first_name . ' ' . $annualPhysical->patient->last_name }}</div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Age</label>
                <div class="text-lg font-semibold text-gray-900">{{ $annualPhysical->patient->age }} years old</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Gender</label>
                <div class="text-lg font-semibold text-gray-900">{{ ucfirst($annualPhysical->patient->sex) }}</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Email</label>
                <div class="text-sm font-medium text-gray-900">{{ $annualPhysical->patient->email }}</div>
            </div>
        </div>
    </div>
    @endif
    <!-- Edit Form -->
    <form action="{{ route('nurse.annual-physical.update', $annualPhysical->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PATCH')
        
        <!-- Physical Examination Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Physical Examination</h3>
                    <p class="text-gray-600 text-sm">Update vital signs and basic physical measurements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php $phys = old('physical_exam', $annualPhysical->physical_exam ?? []); @endphp
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Temperature</label>
                    <div class="relative">
                        <input type="text" name="physical_exam[temp]" value="{{ $phys['temp'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                               placeholder="e.g., 36.5°C" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-thermometer-half text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Height</label>
                    <div class="relative">
                        <input type="text" name="physical_exam[height]" value="{{ $phys['height'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                               placeholder="e.g., 170 cm" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-ruler-vertical text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Heart Rate</label>
                    <div class="relative">
                        <input type="text" name="physical_exam[heart_rate]" value="{{ $phys['heart_rate'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                               placeholder="e.g., 72 bpm" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-heartbeat text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Weight</label>
                    <div class="relative">
                        <input type="text" name="physical_exam[weight]" value="{{ $phys['weight'] ?? '' }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                               placeholder="e.g., 65 kg" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-weight text-gray-400"></i>
                        </div>
                    </div>
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
                    <p class="text-gray-600 text-sm">Update notable skin marks, scars, or identifying features</p>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Skin Marks Description</label>
                <textarea name="skin_marks" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                          placeholder="Describe any visible skin marks, scars, tattoos, or identifying features...">{{ old('skin_marks', $annualPhysical->skin_marks) }}</textarea>
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
                    <p class="text-gray-600 text-sm">Update visual acuity, color vision, and examination findings</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Visual Acuity</label>
                    <div class="relative">
                        <input type="text" name="visual" value="{{ old('visual', $annualPhysical->visual) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                               placeholder="e.g., 20/20" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-eye text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Ishihara Test</label>
                    <div class="relative">
                        <input type="text" name="ishihara_test" value="{{ old('ishihara_test', $annualPhysical->ishihara_test) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                               placeholder="e.g., Normal" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-palette text-gray-400"></i>
                        </div>
                    </div>
                </div>

               
            </div>
        </div>

        @php
            // Check if this annual physical examination requires drug test
            $medicalTestName = $annualPhysical->patient->appointment->medicalTest->name ?? '';
            $hasDrugTest = in_array(strtolower($medicalTestName), [
                'annual medical with drug test',
                'annual medical with drug test and ecg',
                'annual medical examination with drug test',
                'annual medical examination with drug test and ecg'
            ]);
            
            // Get existing drug test data and connected drug test results
            $drugTest = $annualPhysical->drug_test ?? [];
            $drugTestResults = $annualPhysical->drugTestResults()->latest()->first();
        @endphp

        @if($hasDrugTest)
        <!-- Drug Test Form Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pills text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Drug Test Result (DT Form 2)</h3>
                    <p class="text-gray-600 text-sm">Urine drug screening examination form</p>
                </div>
            </div>

            @if($drugTestResults)
            <!-- Connected Drug Test Information -->
            <div class="bg-purple-50 rounded-xl p-6 border border-purple-200 mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-link text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-purple-900">Connected Drug Test Record</h4>
                        <p class="text-purple-700 text-sm">This examination has a linked drug test result (ID: {{ $drugTestResults->id }})</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-purple-800">Test Date:</span>
                        <span class="text-purple-700">{{ $drugTestResults->examination_datetime ? $drugTestResults->examination_datetime->format('F j, Y \a\t g:i A') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-purple-800">Conducted By:</span>
                        <span class="text-purple-700">{{ $drugTestResults->test_conducted_by ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-purple-800">Status:</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">{{ ucfirst($drugTestResults->status) }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Drug Test Results Section -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Drug Test Results</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-900">Drug/Metabolites</th>
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-900">Result</th>
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-900">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-3 font-medium">METHAMPHETAMINE (Meth)</td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <select name="drug_test[methamphetamine_result]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('drug_test.methamphetamine_result') border-red-500 ring-2 ring-red-200 @enderror">
                                        <option value="">Select Result</option>
                                        <option value="Negative" {{ old('drug_test.methamphetamine_result', $drugTestResults->methamphetamine_result ?? $drugTest['methamphetamine'] ?? '') == 'Negative' ? 'selected' : '' }}>Negative</option>
                                        <option value="Positive" {{ old('drug_test.methamphetamine_result', $drugTestResults->methamphetamine_result ?? $drugTest['methamphetamine'] ?? '') == 'Positive' ? 'selected' : '' }}>Positive</option>
                                    </select>
                                    @error('drug_test.methamphetamine_result')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <input type="text" name="drug_test[methamphetamine_remarks]" 
                                           value="{{ old('drug_test.methamphetamine_remarks', $drugTestResults->methamphetamine_remarks ?? $drugTest['methamphetamine_remarks'] ?? '') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                                           placeholder="Optional remarks" />
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-3 font-medium">TETRAHYDROCANNABINOL (Marijuana)</td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <select name="drug_test[marijuana_result]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('drug_test.marijuana_result') border-red-500 ring-2 ring-red-200 @enderror">
                                        <option value="">Select Result</option>
                                        <option value="Negative" {{ old('drug_test.marijuana_result', $drugTestResults->marijuana_result ?? $drugTest['marijuana'] ?? '') == 'Negative' ? 'selected' : '' }}>Negative</option>
                                        <option value="Positive" {{ old('drug_test.marijuana_result', $drugTestResults->marijuana_result ?? $drugTest['marijuana'] ?? '') == 'Positive' ? 'selected' : '' }}>Positive</option>
                                    </select>
                                    @error('drug_test.marijuana_result')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <input type="text" name="drug_test[marijuana_remarks]" 
                                           value="{{ old('drug_test.marijuana_remarks', $drugTestResults->marijuana_remarks ?? $drugTest['marijuana_remarks'] ?? '') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                                           placeholder="Optional remarks" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Signatures</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Test Conducted by:
                        </label>
                        <div class="border-b-2 border-gray-400 pb-2 mb-2">
                            <p class="text-center font-medium">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                        </div>
                        <p class="text-xs text-gray-500 text-center">Signature over Printed Name of Staff</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Conforme:
                        </label>
                        <div class="border-b-2 border-gray-400 pb-2 mb-2 h-8">
                            <!-- Empty space for patient signature -->
                        </div>
                        <p class="text-xs text-gray-500 text-center">Signature over Printed Name of Client</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-signature text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Medical Technologist Signature</h3>
                    <p class="text-gray-600 text-sm">Examination updated by: {{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
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
                        <p class="text-sm font-medium text-gray-700">Date Updated</p>
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
                <i class="fas fa-save mr-2"></i>Update Examination
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
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('border-gray-300');
                    this.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-200');
                } else {
                    this.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-200');
                    this.classList.add('border-gray-300');
                }
            });
        });

        // Form submission confirmation
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to update this annual physical examination?')) {
                e.preventDefault();
            }
        });

        // Auto-hide success messages after 5 seconds
        const alerts = document.querySelectorAll('[class*="bg-emerald-50"], [class*="bg-red-50"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
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
