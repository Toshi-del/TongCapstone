@extends('layouts.plebo')

@section('title', 'Blood Collection Checklist')
@section('page-title', 'Medical Checklist')

@section('content')
<!-- Success/Error Messages -->
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

@if(session('error'))
<div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-center space-x-3">
    <div class="flex-shrink-0">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
    </div>
    <div>
        <p class="text-red-800 font-medium">{{ session('error') }}</p>
    </div>
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
        </div>
        <div>
            <h4 class="text-red-800 font-semibold mb-2">Please correct the following errors:</h4>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Medical Checklist Form -->
<div class="content-card rounded-2xl overflow-hidden">
    <!-- Form Header -->
    <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-vial text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Blood Collection Checklist</h1>
                    <p class="text-orange-100 text-sm">Medical examination and blood collection form</p>
                </div>
            </div>
            <div class="bg-white/20 px-4 py-2 rounded-full">
                <span class="text-white font-semibold">
                    @if($examinationType === 'pre-employment')
                        Pre-Employment
                    @elseif($examinationType === 'opd')
                        OPD Walk-in
                    @else
                        Annual Physical
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-8">
        <form action="{{ isset($medicalChecklist) && $medicalChecklist->id ? route('plebo.medical-checklist.update', $medicalChecklist->id) : route('plebo.medical-checklist.store') }}" method="POST" class="space-y-8">
            @csrf
            @if(isset($medicalChecklist) && $medicalChecklist->id)
                @method('PATCH')
            @endif
            <input type="hidden" name="examination_type" value="{{ $examinationType === 'pre-employment' ? 'pre_employment' : ($examinationType === 'opd' ? 'opd' : 'annual_physical') }}">
            @if(isset($preEmploymentRecord))
                <input type="hidden" name="pre_employment_record_id" value="{{ $preEmploymentRecord->id }}">
            @endif
            @if(isset($patient))
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            @endif
            @if(isset($opdPatient) && $examinationType === 'opd')
                <input type="hidden" name="user_id" value="{{ $opdPatient->id }}">
                @if(isset($opdExamination))
                    <input type="hidden" name="opd_examination_id" value="{{ $opdExamination->id }}">
                @endif
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
                } elseif (isset($opdPatient) && $examinationType === 'opd') {
                    $generatedNumber = 'OPD-' . str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT);
                } else {
                    $generatedNumber = old('number', $number ?? '');
                }
            @endphp

            <!-- Patient Information -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-orange-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Patient Information</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Patient Name</label>
                        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 text-gray-900 font-medium">
                            @if(isset($medicalChecklist) && $medicalChecklist->patient)
                                {{ $medicalChecklist->patient->full_name }}
                            @elseif(isset($patient))
                                {{ $patient->full_name }}
                            @elseif(isset($preEmploymentRecord))
                                {{ $preEmploymentRecord->first_name }} {{ $preEmploymentRecord->last_name }}
                            @elseif(isset($opdPatient))
                                {{ $opdPatient->fname }} {{ $opdPatient->lname }}
                            @else
                                {{ old('name', $medicalChecklist->name ?? $name ?? '') }}
                            @endif
                        </div>
                        <input type="hidden" name="name" value="@if(isset($medicalChecklist) && $medicalChecklist->patient){{ $medicalChecklist->patient->full_name }}@elseif(isset($patient)){{ $patient->full_name }}@elseif(isset($preEmploymentRecord)){{ $preEmploymentRecord->first_name }} {{ $preEmploymentRecord->last_name }}@elseif(isset($opdPatient)){{ $opdPatient->fname }} {{ $opdPatient->lname }}@else{{ old('name', $medicalChecklist->name ?? $name ?? '') }}@endif" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                        @php($currentDate = old('date', $medicalChecklist->date ?? $date ?? now()->format('Y-m-d')))
                        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 text-gray-900 font-medium">
                            {{ \Carbon\Carbon::parse($currentDate)->format('M d, Y') }}
                        </div>
                        <input type="hidden" name="date" value="{{ $currentDate }}" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Age</label>
                        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 text-gray-900 font-medium">
                            @if(isset($medicalChecklist) && $medicalChecklist->patient)
                                {{ $medicalChecklist->patient->age }} years
                            @elseif(isset($patient))
                                {{ $patient->age }} years
                            @elseif(isset($preEmploymentRecord))
                                {{ $preEmploymentRecord->age }} years
                            @elseif(isset($opdPatient))
                                {{ $opdPatient->age }} years
                            @else
                                {{ old('age', $medicalChecklist->age ?? $age ?? '') }} years
                            @endif
                        </div>
                        <input type="hidden" name="age" value="@if(isset($medicalChecklist) && $medicalChecklist->patient){{ $medicalChecklist->patient->age }}@elseif(isset($patient)){{ $patient->age }}@elseif(isset($preEmploymentRecord)){{ $preEmploymentRecord->age }}@elseif(isset($opdPatient)){{ $opdPatient->age }}@else{{ old('age', $medicalChecklist->age ?? $age ?? '') }}@endif" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Record Number</label>
                        <div class="bg-orange-50 rounded-xl border border-orange-200 px-4 py-3 text-orange-900 font-bold">
                            {{ $generatedNumber ?: 'N/A' }}
                        </div>
                        <input type="hidden" name="number" value="{{ $generatedNumber }}" />
                    </div>
                </div>
            </div>

            <!-- Medical Examinations Checklist -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-blue-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Medical Examinations</h3>
                </div>
                
                <div class="space-y-4">
                    @foreach([
                        'chest_xray' => ['name' => 'Chest X-Ray', 'icon' => 'fa-lungs'],
                        'stool_exam' => ['name' => 'Stool Examination', 'icon' => 'fa-vial'],
                        'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fa-flask'],
                        'drug_test' => ['name' => 'Drug Test', 'icon' => 'fa-pills'],
                        'blood_extraction' => ['name' => 'Blood Extraction', 'icon' => 'fa-tint'],
                        'ecg' => ['name' => 'ElectroCardioGram (ECG)', 'icon' => 'fa-heartbeat'],
                        'physical_exam' => ['name' => 'Physical Examination', 'icon' => 'fa-stethoscope'],
                    ] as $field => $exam)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-orange-300 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas {{ $exam['icon'] }} text-orange-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $exam['name'] }}</span>
                                        @if($field === 'blood_extraction')
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-star mr-1"></i>
                                                Phlebotomy
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <label class="text-sm font-medium text-gray-700">Completed by:</label>
                                    <input type="text" name="{{ $field }}_done_by"
                                           value="{{ old($field . '_done_by', $medicalChecklist->{$field . '_done_by'} ?? '') }}"
                                           placeholder="Initials/Signature"
                                           @if($field !== 'blood_extraction') 
                                               readonly disabled 
                                               class="w-32 px-3 py-2 rounded-lg border border-gray-300 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"
                                           @else 
                                               class="w-32 px-3 py-2 rounded-lg border border-orange-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white"
                                           @endif>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Optional Examinations -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-purple-600 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Optional Examinations</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Tests</label>
                    <input type="text" 
                           name="optional_exam" 
                           value="{{ old('optional_exam', $medicalChecklist->optional_exam ?? $optionalExam ?? 'Audiometry/Ishihara') }}" 
                           placeholder="Enter optional examinations (e.g., Audiometry/Ishihara)"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white" />
                    <p class="text-xs text-gray-500 mt-2">Specify any additional tests or examinations required</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <a href="{{ $examinationType === 'pre-employment' ? route('plebo.pre-employment') : ($examinationType === 'opd' ? route('plebo.opd') : route('plebo.annual-physical')) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors duration-200 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl transition-colors duration-200 font-semibold shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Save Checklist
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation and interactions
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const bloodExtractionInput = document.querySelector('input[name="blood_extraction_done_by"]');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Focus on blood extraction field when page loads
    if (bloodExtractionInput && !bloodExtractionInput.value) {
        setTimeout(() => {
            bloodExtractionInput.focus();
        }, 500);
    }
    
    // Add visual feedback for required field
    if (bloodExtractionInput) {
        bloodExtractionInput.addEventListener('input', function() {
            const parent = this.closest('.bg-gray-50');
            if (this.value.trim()) {
                parent.classList.remove('border-gray-200');
                parent.classList.add('border-green-300', 'bg-green-50');
            } else {
                parent.classList.remove('border-green-300', 'bg-green-50');
                parent.classList.add('border-gray-200');
            }
        });
    }
    
    // Form submission confirmation
    form.addEventListener('submit', function(e) {
        if (bloodExtractionInput && !bloodExtractionInput.value.trim()) {
            e.preventDefault();
            alert('Please complete the Blood Extraction field before submitting.');
            bloodExtractionInput.focus();
            return false;
        }
        
        // Show loading state
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        submitButton.disabled = true;
    });
    
    console.log('Medical checklist form initialized');
});
</script>
@endsection
