@extends('layouts.pathologist')

@section('title', 'Medical Checklist')
@section('page-title', 'Medical Checklist')

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 rounded bg-green-100 text-green-800 border border-green-300 text-center font-semibold">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-300 text-center font-semibold">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-300">
        <h4 class="font-semibold mb-2">Validation Errors:</h4>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="bg-teal-900 text-white text-center py-3 rounded-t-lg mb-8">
            <h2 class="text-xl font-bold tracking-wide">MEDICAL CHECKLIST</h2>
        </div>

        @php
            // Determine the correct form action based on examination type
            if (isset($examinationType) && $examinationType === 'opd') {
                // For OPD, always use the generic store/update routes since there's no specific OPD store route
                $formAction = isset($medicalChecklist) && $medicalChecklist->id 
                    ? route('pathologist.medical-checklist.update', $medicalChecklist->id) 
                    : route('pathologist.medical-checklist.store');
            } else {
                // For pre-employment and annual physical
                $formAction = isset($medicalChecklist) && $medicalChecklist->id 
                    ? route('pathologist.medical-checklist.update', $medicalChecklist->id) 
                    : route('pathologist.medical-checklist.store');
            }
            
            // Preserve URL parameters in form action for non-OPD cases
            if (!isset($examinationType) || $examinationType !== 'opd') {
                if (request('pre_employment_record_id') || request('patient_id') || request('examination_type')) {
                    $params = [];
                    if (request('pre_employment_record_id')) $params['pre_employment_record_id'] = request('pre_employment_record_id');
                    if (request('patient_id')) $params['patient_id'] = request('patient_id');
                    if (request('examination_type')) $params['examination_type'] = request('examination_type');
                    $formAction .= '?' . http_build_query($params);
                }
            }
        @endphp
        
        <form action="{{ $formAction }}" method="POST" class="space-y-8">
            @csrf
            @if(isset($medicalChecklist) && $medicalChecklist->id)
                @method('PATCH')
            @endif
            
            <input type="hidden" name="examination_type" value="{{ isset($examinationType) ? ($examinationType === 'pre-employment' || $examinationType === 'pre_employment' ? 'pre_employment' : ($examinationType === 'opd' ? 'opd' : 'annual_physical')) : 'annual_physical' }}">
            
            {{-- Always include the IDs from request parameters if available --}}
            @if(request('pre_employment_record_id'))
                <input type="hidden" name="pre_employment_record_id" value="{{ request('pre_employment_record_id') }}">
            @elseif(isset($preEmploymentRecord) && $preEmploymentRecord)
                <input type="hidden" name="pre_employment_record_id" value="{{ $preEmploymentRecord->id }}">
            @endif
            
            @if(request('patient_id'))
                <input type="hidden" name="patient_id" value="{{ request('patient_id') }}">
            @elseif(isset($patient) && $patient)
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
                } elseif (isset($patient) && $patient) {
                    $generatedNumber = 'APEP-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
                } elseif (isset($preEmploymentRecord) && $preEmploymentRecord) {
                    $generatedNumber = 'PPEP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT);
                } elseif (isset($opdPatient) && $examinationType === 'opd') {
                    $generatedNumber = 'OPD-' . str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT);
                } else {
                    $generatedNumber = $number ?: old('number', '');
                }
            @endphp

            <!-- Patient Information -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1">Name</label>
                    <div class="text-lg font-semibold text-gray-800">
                        @if(isset($medicalChecklist) && $medicalChecklist->patient)
                            {{ $medicalChecklist->patient->full_name }}
                        @elseif(isset($patient) && $patient)
                            {{ $patient->full_name }}
                        @elseif(isset($preEmploymentRecord) && $preEmploymentRecord)
                            {{ $preEmploymentRecord->first_name }} {{ $preEmploymentRecord->last_name }}
                        @elseif(isset($opdPatient) && $examinationType === 'opd')
                            {{ $opdPatient->fname }} {{ $opdPatient->lname }}
                        @else
                            {{ $name ?: old('name', '') }}
                        @endif
                    </div>
                    <input type="hidden" name="name" value="@if(isset($medicalChecklist) && $medicalChecklist->patient){{ $medicalChecklist->patient->full_name }}@elseif(isset($patient) && $patient){{ $patient->full_name }}@elseif(isset($preEmploymentRecord) && $preEmploymentRecord){{ $preEmploymentRecord->first_name }} {{ $preEmploymentRecord->last_name }}@elseif(isset($opdPatient) && $examinationType === 'opd'){{ $opdPatient->fname }} {{ $opdPatient->lname }}@else{{ $name ?: old('name', '') }}@endif" />
                </div>
                
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1">Date</label>
                    @php($currentDate = old('date', $medicalChecklist->date ?? $date ?? now()->format('Y-m-d')))
                    <div class="text-lg font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($currentDate)->format('M d, Y') }}
                    </div>
                    <input type="hidden" name="date" value="{{ $currentDate }}" />
                </div>
                
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1">Age</label>
                    <div class="text-lg font-semibold text-gray-800">
                        @if(isset($medicalChecklist) && $medicalChecklist->patient)
                            {{ $medicalChecklist->patient->age }}
                        @elseif(isset($patient) && $patient)
                            {{ $patient->age }}
                        @elseif(isset($preEmploymentRecord) && $preEmploymentRecord)
                            {{ $preEmploymentRecord->age }}
                        @elseif(isset($opdPatient) && $examinationType === 'opd')
                            {{ $opdPatient->age }}
                        @else
                            {{ $age ?: old('age', '') }}
                        @endif
                    </div>
                    <input type="hidden" name="age" value="@if(isset($medicalChecklist) && $medicalChecklist->patient){{ $medicalChecklist->patient->age }}@elseif(isset($patient) && $patient){{ $patient->age }}@elseif(isset($preEmploymentRecord) && $preEmploymentRecord){{ $preEmploymentRecord->age }}@elseif(isset($opdPatient) && $examinationType === 'opd'){{ $opdPatient->age }}@else{{ $age ?: old('age', '') }}@endif" />
                </div>
                
                <div>
                    <label class="block text-xs font-semibold uppercase mb-1">Record Number</label>
                    <div class="text-lg font-semibold text-gray-800 font-mono">
                        {{ $generatedNumber ?: 'N/A' }}
                    </div>
                    <input type="hidden" name="number" value="{{ $generatedNumber }}" />
                </div>
            </div>

            <!-- Laboratory Examinations -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 text-center">
                    <i class="fas fa-clipboard-check mr-2 text-teal-600"></i>Laboratory Examinations
                </h3>
                
                <div class="space-y-4">
                    @foreach([
                        'chest_xray' => ['Chest X-Ray', 'fas fa-x-ray'],
                        'stool_exam' => ['Stool Exam', 'fas fa-vial'],
                        'urinalysis' => ['Urinalysis', 'fas fa-tint'],
                        'drug_test' => ['Drug Test', 'fas fa-pills'],
                        'blood_extraction' => ['Blood Extraction', 'fas fa-syringe'],
                        'ecg' => ['ElectroCardioGram (ECG)', 'fas fa-heartbeat'],
                        'physical_exam' => ['Physical Exam', 'fas fa-stethoscope'],
                    ] as $field => $examData)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                                    <i class="{{ $examData[1] }} text-teal-600 text-sm"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">{{ $loop->iteration }}. {{ $examData[0] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">Completed by:</span>
                                <input type="text" name="{{ $field }}_done_by"
                                       value="{{ old($field . '_done_by', isset($medicalChecklist) ? $medicalChecklist->{$field . '_done_by'} : '') }}"
                                       placeholder="Initials/Signature"
                                       @if($field === 'stool_exam' || $field === 'urinalysis') 
                                           class="form-input w-32 rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500" 
                                       @else 
                                           readonly class="form-input w-32 rounded-lg border-gray-300 text-sm bg-gray-50 text-gray-700" 
                                       @endif>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Optional Exam -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-plus-circle mr-2 text-teal-600"></i>Additional Examinations
                </h3>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Optional Examinations</label>
                    <input type="text" name="optional_exam" 
                           value="{{ old('optional_exam', $medicalChecklist->optional_exam ?? $optionalExam ?? 'Audiometry/Ishihara') }}" 
                           class="form-input w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" 
                           placeholder="Enter additional examinations if any" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ $examinationType === 'pre-employment' ? route('pathologist.pre-employment') : ($examinationType === 'opd' ? route('pathologist.opd') : route('pathologist.annual-physical')) }}" 
                   class="bg-gray-500 text-white px-8 py-3 rounded-lg shadow hover:bg-gray-600 transition-colors font-semibold tracking-wide">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
                <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg shadow hover:from-green-700 hover:to-green-800 transition-all font-semibold tracking-wide transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i>Submit Checklist
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Form validation and debugging
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['stool_exam_done_by', 'urinalysis_done_by'];
        let isValid = true;
        
        
        requiredFields.forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields (Stool Exam and Urinalysis).');
            return false;
        }
        
    });
</script>
@endsection