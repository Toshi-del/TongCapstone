@extends('layouts.doctor')

@section('title', 'Medical Checklist - RSS Citi Health Services')
@section('page-title', 'Medical Checklist')
@section('page-description', 'Medical examination checklist and completion tracking')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-6 flex items-center space-x-4 shadow-lg">
            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-semibold text-lg">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 flex items-center space-x-4 shadow-lg">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-semibold text-lg">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-xl border-2 border-gray-200">
        <div class="bg-indigo-800 px-10 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-indigo-700 rounded-xl flex items-center justify-center backdrop-blur-sm border-2 border-white/20">
                        <i class="fas fa-clipboard-check text-white text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-white">Medical Checklist</h2>
                        <p class="text-violet-100 text-base mt-1">Medical examination completion tracking and verification</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/90 text-base">Examination Type</div>
                    <div class="text-white font-bold text-xl">{{ ucfirst($examinationType ?? 'General') }}</div>
                </div>
            </div>
        </div>
    </div>

        <form action="{{ isset($medicalChecklist) && $medicalChecklist->id ? route('doctor.medical-checklist.update', $medicalChecklist->id) : route('doctor.medical-checklist.store') }}" method="POST" class="space-y-8">
            @csrf
            @if(isset($medicalChecklist) && $medicalChecklist->id)
                @method('PATCH')
            @endif
            <input type="hidden" name="examination_type" value="{{ $examinationType }}">
            @if(isset($preEmploymentRecord))
                <input type="hidden" name="pre_employment_record_id" value="{{ $preEmploymentRecord->id }}">
            @endif
            @if(isset($patient))
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            @endif
            @if(isset($annualPhysicalExamination))
                <input type="hidden" name="annual_physical_examination_id" value="{{ $annualPhysicalExamination->id }}">
            @endif

            @php
                // Precompute generated number once for reuse
                $generatedNumber = null;
                if (isset($medicalChecklist) && ($medicalChecklist->number ?? null)) {
                    $generatedNumber = $medicalChecklist->number;
                } elseif (isset($patient)) {
                    $generatedNumber = 'APEP-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
                } elseif (isset($preEmploymentRecord)) {
                    $generatedNumber = 'PPEP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT);
                } else {
                    $generatedNumber = old('number', $number ?? '');
                }
            @endphp

            <!-- Patient Information Card -->
            <div class="content-card rounded-xl shadow-xl border-2 border-gray-200 p-8">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-md text-violet-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Patient Information</h3>
                        <p class="text-gray-600 text-base">Medical examination details and patient data</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="space-y-3">
                        <label class="block text-sm font-bold uppercase tracking-wide text-gray-700">Patient Name</label>
                        @php
                            $currentName = old('name', $medicalChecklist->name ?? $name ?? 'N/A');
                        @endphp
                        <div class="w-full rounded-xl border-2 border-gray-300 bg-gray-50 px-5 py-4 text-gray-800 font-medium text-base shadow-inner">
                            {{ $currentName }}
                        </div>
                        <input type="hidden" name="name" value="{{ $currentName }}" />
                    </div>
                    
                    <div class="space-y-3">
                        <label class="block text-sm font-bold uppercase tracking-wide text-gray-700">Examination Date</label>
                        @php
                            $currentDate = old('date', $medicalChecklist->date ?? $date ?? now()->format('Y-m-d'));
                        @endphp
                        <div class="w-full rounded-xl border-2 border-gray-300 bg-gray-50 px-5 py-4 text-gray-800 font-medium text-base shadow-inner">
                            {{ \Carbon\Carbon::parse($currentDate)->format('F j, Y') }}
                        </div>
                        <input type="hidden" name="date" value="{{ $currentDate }}" />
                    </div>
                    
                    <div class="space-y-3">
                        <label class="block text-sm font-bold uppercase tracking-wide text-gray-700">Patient Age</label>
                        @php
                            $currentAge = old('age', $medicalChecklist->age ?? $age ?? 0);
                        @endphp
                        <div class="w-full rounded-xl border-2 border-gray-300 bg-gray-50 px-5 py-4 text-gray-800 font-medium text-base shadow-inner">
                            {{ $currentAge }}
                        </div>
                        <input type="hidden" name="age" value="{{ $currentAge }}" />
                    </div>
                    
                    <div class="space-y-3">
                        <label class="block text-sm font-bold uppercase tracking-wide text-gray-700">Checklist Number</label>
                        <div class="w-full rounded-xl border-2 border-violet-300 bg-violet-50 px-5 py-4 text-violet-800 font-bold text-base shadow-inner">
                            {{ $generatedNumber ?: 'N/A' }}
                        </div>
                        <input type="hidden" name="number" value="{{ $generatedNumber }}" />
                    </div>
                </div>
            </div>

            <!-- Medical Examinations Checklist Card -->
            <div class="content-card rounded-xl shadow-xl border-2 border-gray-200 p-8">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Medical Examinations Checklist</h3>
                        <p class="text-gray-600 text-base">Track completion status and responsible medical staff</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    @foreach([
                        'chest_xray' => ['name' => 'Chest X-Ray', 'icon' => 'fas fa-lungs', 'color' => 'blue'],
                        'stool_exam' => ['name' => 'Stool Examination', 'icon' => 'fas fa-vial', 'color' => 'amber'],
                        'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-flask', 'color' => 'yellow'],
                        'drug_test' => ['name' => 'Drug Test', 'icon' => 'fas fa-pills', 'color' => 'red'],
                        'blood_extraction' => ['name' => 'Blood Extraction', 'icon' => 'fas fa-tint', 'color' => 'red'],
                        'ecg' => ['name' => 'ElectroCardioGram (ECG)', 'icon' => 'fas fa-heartbeat', 'color' => 'pink'],
                        'physical_exam' => ['name' => 'Physical Examination', 'icon' => 'fas fa-stethoscope', 'color' => 'emerald'],
                    ] as $field => $exam)
                        <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-{{ $exam['color'] }}-100 rounded-lg flex items-center justify-center">
                                        <i class="{{ $exam['icon'] }} text-{{ $exam['color'] }}-600"></i>
                                    </div>
                                    <div>
                                        <span class="text-lg font-bold text-gray-900">{{ $loop->iteration }}. {{ $exam['name'] }}</span>
                                        <div class="text-sm text-gray-600 mt-1">Medical examination requirement</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-6">
                                    @php
                                        $completedBy = null;
                                        if (isset($medicalChecklist) && $medicalChecklist) {
                                            // Get the value directly from the object
                                            $doneByField = $field . '_done_by';
                                            $completedField = $field . '_completed';
                                            
                                            // Try to get the value
                                            if (isset($medicalChecklist->$doneByField) && !empty($medicalChecklist->$doneByField)) {
                                                $completedBy = $medicalChecklist->$doneByField;
                                            } elseif (isset($medicalChecklist->$completedField) && !empty($medicalChecklist->$completedField)) {
                                                $completedBy = $medicalChecklist->$completedField;
                                            }
                                        }
                                        
                                        if (!$completedBy) {
                                            $completedBy = old($field . '_done_by') ?? old($field . '_completed');
                                        }
                                        
                                        $borderClass = $completedBy ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50';
                                    @endphp
                                    <div class="text-right">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Completed by:</label>
                                        <div class="w-48 px-4 py-3 rounded-xl border-2 {{ $borderClass }} text-gray-900 font-medium text-center shadow-sm">
                                            @if($completedBy)
                                                <i class="fas fa-check-circle text-green-600 mr-2"></i>{{ $completedBy }}
                                            @else
                                                <i class="fas fa-clock text-gray-400 mr-2"></i>Not completed
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Optional Examinations Card -->
            <div class="content-card rounded-xl shadow-xl border-2 border-gray-200 p-8">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Optional Examinations & Doctor Authorization</h3>
                        <p class="text-gray-600 text-base">Additional medical tests and physician authorization</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-eye text-purple-600"></i>
                            </div>
                            <label class="text-lg font-bold text-gray-900">Additional Tests</label>
                        </div>
                        
                        <div class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 bg-gray-50 text-gray-900 font-medium text-base shadow-inner">
                            {{ $medicalChecklist->optional_exam ?? $optionalExam ?? 'Audiometry/Ishihara' }}
                        </div>
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Common optional tests:</strong> Audiometry, Ishihara Color Vision Test, Spirometry, Vision Screening</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl border-2 border-violet-200 p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-signature text-violet-600"></i>
                            </div>
                            <label class="text-lg font-bold text-gray-900">Doctor's Authorization</label>
                        </div>
                        
                        <input type="text" 
                               name="doctor_signature" 
                               value="{{ old('doctor_signature', $medicalChecklist->doctor_signature ?? $doctorSignature ?? '') }}" 
                               placeholder="Enter doctor's signature or initials"
                               class="w-full px-6 py-4 rounded-xl border-2 border-violet-300 bg-white text-gray-900 font-medium text-base focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors shadow-sm" />
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Authorization:</strong> Physician's signature for checklist completion and medical clearance</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6">
                <a href="{{ route('doctor.dashboard') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors shadow-lg border-2 border-gray-300">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Back to Dashboard
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all duration-200 shadow-xl border-2 border-violet-500 transform hover:scale-105">
                    <i class="fas fa-save mr-3"></i>
                    {{ isset($medicalChecklist) && $medicalChecklist->id ? 'Update Checklist' : 'Submit Checklist' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
