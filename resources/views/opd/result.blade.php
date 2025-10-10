@extends('layouts.opd')

@section('opd-content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex items-start justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 m-0">OPD Examination Results</h2>
                <p class="text-sm text-gray-500">Your medical examination results</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('opd.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </a>
                @if($hasResults)
                <button type="button" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition" onclick="window.print()">
                    <i class="fa-solid fa-print mr-2"></i> Print
                </button>
                @endif
            </div>
        </div>

        @if(!$hasResults)
        <!-- No Results Available -->
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-medical text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Results Available</h3>
            <p class="text-gray-500 mb-4">{{ $message }}</p>
            <a href="{{ route('opd.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
        @else
        <!-- Patient Information Header -->
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-blue-50">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-semibold text-gray-900 text-lg">{{ $patientName }}</div>
                    <div class="text-sm text-gray-600">Patient ID: OPD-{{ str_pad($latestExamination->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Examination Date</div>
                    <div class="font-semibold text-gray-900">{{ $examDate }}</div>
                </div>
            </div>
        </div>

        <!-- Examination Results -->
        <div class="p-6 space-y-6">
            <!-- Medical Assessment -->
            @if($latestExamination->fitness_assessment)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>
                    Medical Assessment
                </h3>
                @php
                    $assessment = $latestExamination->fitness_assessment;
                    if ($assessment === 'Fit to work') {
                        $assessmentClass = 'bg-green-100 text-green-800 border-green-200';
                        $assessmentIcon = 'fas fa-check-circle text-green-600';
                    } elseif ($assessment === 'Not fit for work') {
                        $assessmentClass = 'bg-red-100 text-red-800 border-red-200';
                        $assessmentIcon = 'fas fa-times-circle text-red-600';
                    } else {
                        $assessmentClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        $assessmentIcon = 'fas fa-exclamation-triangle text-yellow-600';
                    }
                @endphp
                <div class="flex items-center space-x-3 p-4 rounded-lg border {{ $assessmentClass }}">
                    <i class="{{ $assessmentIcon }} text-2xl"></i>
                    <div>
                        <div class="font-bold text-xl">{{ $assessment }}</div>
                        <div class="text-sm opacity-75">
                            Drug Tests: {{ $latestExamination->drug_positive_count ?? 0 }} positive | 
                            Medical Tests: {{ $latestExamination->medical_abnormal_count ?? 0 }} abnormal | 
                            Physical Exam: {{ $latestExamination->physical_abnormal_count ?? 0 }} abnormal
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Medical History -->
            @if($latestExamination->illness_history || $latestExamination->accidents_operations || $latestExamination->past_medical_history)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history text-green-600 mr-2"></i>
                    Medical History
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($latestExamination->illness_history)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Illness/Hospitalization</label>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700">{{ $latestExamination->illness_history }}</div>
                    </div>
                    @endif
                    @if($latestExamination->accidents_operations)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accidents/Operations</label>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700">{{ $latestExamination->accidents_operations }}</div>
                    </div>
                    @endif
                    @if($latestExamination->past_medical_history)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Past Medical History</label>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700">{{ $latestExamination->past_medical_history }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Family History -->
            @if($latestExamination->family_history && count($latestExamination->family_history) > 0)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Family History
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @php
                        $familyOptions = ['asthma','arthritis','migraine','diabetes','heart_disease','tuberculosis','allergies','anemia','cancer','insanity','hypertension','epilepsy'];
                    @endphp
                    @foreach($familyOptions as $condition)
                        @if(in_array($condition, $latestExamination->family_history))
                        <div class="flex items-center space-x-2 bg-red-50 p-2 rounded-lg border border-red-200">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                            <span class="text-sm font-medium text-red-800">{{ ucwords(str_replace('_', ' ', $condition)) }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Personal Habits -->
            @if($latestExamination->personal_habits && count($latestExamination->personal_habits) > 0)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user-check text-orange-600 mr-2"></i>
                    Personal Habits
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $habitOptions = [
                            'alcohol' => ['icon' => 'fas fa-wine-bottle', 'color' => 'red'],
                            'cigarettes' => ['icon' => 'fas fa-smoking', 'color' => 'orange'],
                            'drugs' => ['icon' => 'fas fa-pills', 'color' => 'purple']
                        ];
                    @endphp
                    @foreach($habitOptions as $habit => $config)
                        @if(isset($latestExamination->personal_habits[$habit]) && $latestExamination->personal_habits[$habit])
                        <div class="bg-{{ $config['color'] }}-50 p-4 rounded-lg border border-{{ $config['color'] }}-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600"></i>
                                <span class="font-medium text-{{ $config['color'] }}-900">{{ ucfirst($habit) }}</span>
                            </div>
                            <div class="text-sm text-{{ $config['color'] }}-800">{{ $latestExamination->personal_habits[$habit] }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Vital Signs -->
            @if($latestExamination->physical_exam)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                    Vital Signs
                </h3>
                @php
                    $vitals = $latestExamination->physical_exam ?? [];
                    $vitalSigns = [
                        'temp' => ['label' => 'Temperature', 'unit' => '°C', 'icon' => 'fas fa-thermometer-half', 'color' => 'red'],
                        'height' => ['label' => 'Height', 'unit' => 'cm', 'icon' => 'fas fa-ruler-vertical', 'color' => 'blue'],
                        'weight' => ['label' => 'Weight', 'unit' => 'kg', 'icon' => 'fas fa-weight', 'color' => 'green'],
                        'bp' => ['label' => 'Blood Pressure', 'unit' => 'mmHg', 'icon' => 'fas fa-heart', 'color' => 'purple'],
                        'pulse' => ['label' => 'Pulse Rate', 'unit' => 'bpm', 'icon' => 'fas fa-heartbeat', 'color' => 'pink'],
                        'resp' => ['label' => 'Respiratory Rate', 'unit' => '/min', 'icon' => 'fas fa-lungs', 'color' => 'teal']
                    ];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($vitalSigns as $key => $vital)
                        @if(isset($vitals[$key]) && $vitals[$key])
                        <div class="bg-{{ $vital['color'] }}-50 p-4 rounded-lg border border-{{ $vital['color'] }}-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-{{ $vital['color'] }}-100 rounded-lg flex items-center justify-center">
                                    <i class="{{ $vital['icon'] }} text-{{ $vital['color'] }}-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-{{ $vital['color'] }}-700">{{ $vital['label'] }}</p>
                                    <p class="text-lg font-bold text-{{ $vital['color'] }}-900">{{ $vitals[$key] }} {{ $vital['unit'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Physical Examination Findings -->
            @if($latestExamination->physical_findings)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                    Physical Examination Findings
                </h3>
                @php
                    $physicalFindings = $latestExamination->physical_findings ?? [];
                    $systems = [
                        'head_neck' => ['label' => 'Head & Neck', 'icon' => 'fas fa-head-side-mask', 'color' => 'blue'],
                        'chest_lungs' => ['label' => 'Chest & Lungs', 'icon' => 'fas fa-lungs', 'color' => 'teal'],
                        'heart' => ['label' => 'Heart', 'icon' => 'fas fa-heartbeat', 'color' => 'red'],
                        'abdomen' => ['label' => 'Abdomen', 'icon' => 'fas fa-circle', 'color' => 'yellow'],
                        'extremities' => ['label' => 'Extremities', 'icon' => 'fas fa-hand-paper', 'color' => 'green'],
                        'neurological' => ['label' => 'Neurological', 'icon' => 'fas fa-brain', 'color' => 'purple'],
                        'skin' => ['label' => 'Skin', 'icon' => 'fas fa-hand-holding-medical', 'color' => 'pink'],
                        'genitourinary' => ['label' => 'Genitourinary', 'icon' => 'fas fa-user-md', 'color' => 'indigo']
                    ];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($systems as $key => $system)
                        @if(isset($physicalFindings[$key]))
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-8 h-8 bg-{{ $system['color'] }}-100 rounded-lg flex items-center justify-center">
                                    <i class="{{ $system['icon'] }} text-{{ $system['color'] }}-600 text-sm"></i>
                                </div>
                                <h4 class="font-semibold text-gray-900">{{ $system['label'] }}</h4>
                                @if(isset($physicalFindings[$key]['result']))
                                <span class="px-2 py-1 text-xs rounded-full {{ $physicalFindings[$key]['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $physicalFindings[$key]['result'] }}
                                </span>
                                @endif
                            </div>
                            @if(isset($physicalFindings[$key]['findings']) && $physicalFindings[$key]['findings'])
                            <div class="text-sm text-gray-700">
                                <strong>Findings:</strong> {{ $physicalFindings[$key]['findings'] }}
                            </div>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Vision & Special Tests -->
            @if($latestExamination->visual || $latestExamination->ishihara_test || $latestExamination->skin_marks)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-eye text-blue-600 mr-2"></i>
                    Vision & Special Tests
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($latestExamination->visual)
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-glasses text-blue-600"></i>
                            <span class="font-medium text-blue-900">Visual Acuity</span>
                        </div>
                        <div class="text-sm text-blue-800">{{ $latestExamination->visual }}</div>
                    </div>
                    @endif
                    @if($latestExamination->ishihara_test)
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-palette text-green-600"></i>
                            <span class="font-medium text-green-900">Ishihara Test</span>
                        </div>
                        <div class="text-sm text-green-800">{{ $latestExamination->ishihara_test }}</div>
                    </div>
                    @endif
                    @if($latestExamination->skin_marks)
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-hand-holding-medical text-purple-600"></i>
                            <span class="font-medium text-purple-900">Skin Marks</span>
                        </div>
                        <div class="text-sm text-purple-800">{{ $latestExamination->skin_marks }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Laboratory Results -->
            @php
                // Combine lab_findings and lab_report data
                $labFindings = $latestExamination->lab_findings ?? [];
                $labReport = $latestExamination->lab_report ?? [];
                
                // Define all possible laboratory tests
                $labTests = [
                    'cbc' => ['name' => 'Complete Blood Count (CBC)', 'icon' => 'fas fa-tint', 'color' => 'red'],
                    'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-flask', 'color' => 'yellow'],
                    'fecalysis' => ['name' => 'Fecalysis', 'icon' => 'fas fa-microscope', 'color' => 'brown'],
                    'sodium' => ['name' => 'Sodium', 'icon' => 'fas fa-vial', 'color' => 'blue'],
                    'potassium' => ['name' => 'Potassium', 'icon' => 'fas fa-vial', 'color' => 'green'],
                    'creatinine' => ['name' => 'Creatinine', 'icon' => 'fas fa-vial', 'color' => 'purple'],
                    'bun' => ['name' => 'Blood Urea Nitrogen (BUN)', 'icon' => 'fas fa-vial', 'color' => 'indigo'],
                    'glucose' => ['name' => 'Glucose', 'icon' => 'fas fa-vial', 'color' => 'pink'],
                    'cholesterol' => ['name' => 'Cholesterol', 'icon' => 'fas fa-vial', 'color' => 'orange'],
                    'triglycerides' => ['name' => 'Triglycerides', 'icon' => 'fas fa-vial', 'color' => 'teal'],
                    'hdl' => ['name' => 'HDL Cholesterol', 'icon' => 'fas fa-vial', 'color' => 'cyan'],
                    'ldl' => ['name' => 'LDL Cholesterol', 'icon' => 'fas fa-vial', 'color' => 'lime'],
                    'hba1c' => ['name' => 'HbA1c', 'icon' => 'fas fa-vial', 'color' => 'amber'],
                    'psa' => ['name' => 'PSA', 'icon' => 'fas fa-vial', 'color' => 'emerald'],
                    'hepatitis_b' => ['name' => 'Hepatitis B', 'icon' => 'fas fa-virus', 'color' => 'red'],
                    'hepatitis_c' => ['name' => 'Hepatitis C', 'icon' => 'fas fa-virus', 'color' => 'orange'],
                    'hiv' => ['name' => 'HIV', 'icon' => 'fas fa-virus', 'color' => 'red'],
                    'syphilis' => ['name' => 'Syphilis', 'icon' => 'fas fa-virus', 'color' => 'purple'],
                ];
                
                $allLabResults = [];
                
                // Process lab_findings (current structure)
                foreach ($labFindings as $test => $data) {
                    if ($test !== 'chest_xray' && isset($data['result']) && $data['result']) {
                        $allLabResults[$test] = [
                            'result' => $data['result'],
                            'findings' => $data['finding'] ?? $data['findings'] ?? '',
                            'source' => 'lab_findings'
                        ];
                    }
                }
                
                // Process lab_report (legacy structure)
                foreach ($labReport as $test => $result) {
                    if ($test !== 'chest_xray' && $result && !isset($allLabResults[$test])) {
                        // Handle both direct result and _result suffix
                        $testKey = str_replace('_result', '', $test);
                        $allLabResults[$testKey] = [
                            'result' => $result,
                            'findings' => $labReport[$testKey . '_findings'] ?? '',
                            'source' => 'lab_report'
                        ];
                    }
                }
            @endphp

            @if(count($allLabResults) > 0)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-microscope text-lime-600 mr-2"></i>
                    Laboratory Test Results
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($allLabResults as $testKey => $data)
                        @php
                            $testInfo = $labTests[$testKey] ?? [
                                'name' => ucwords(str_replace('_', ' ', $testKey)),
                                'icon' => 'fas fa-vial',
                                'color' => 'gray'
                            ];
                            $isNormal = strtolower($data['result']) === 'normal';
                            $resultClass = $isNormal ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
                            $cardClass = $isNormal ? 'border-green-200' : 'border-red-200';
                        @endphp
                        <div class="bg-gray-50 p-4 rounded-lg border {{ $cardClass }}">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-8 h-8 bg-{{ $testInfo['color'] }}-100 rounded-lg flex items-center justify-center">
                                    <i class="{{ $testInfo['icon'] }} text-{{ $testInfo['color'] }}-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ $testInfo['name'] }}</h4>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full border {{ $resultClass }}">
                                            {{ $data['result'] }}
                                        </span>
                                        @if(!$isNormal)
                                            <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                                        @else
                                            <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($data['findings'])
                            <div class="text-xs text-gray-600 bg-white p-2 rounded border">
                                <strong>Findings:</strong> {{ $data['findings'] }}
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Summary -->
                @php
                    $normalCount = collect($allLabResults)->where('result', 'Normal')->count();
                    $abnormalCount = collect($allLabResults)->where('result', '!=', 'Normal')->count();
                    $totalCount = count($allLabResults);
                @endphp
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-blue-900">Laboratory Summary</h4>
                            <p class="text-sm text-blue-700">Total Tests: {{ $totalCount }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600">{{ $normalCount }}</div>
                                <div class="text-xs text-green-700">Normal</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-red-600">{{ $abnormalCount }}</div>
                                <div class="text-xs text-red-700">Abnormal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- X-Ray Results -->
            @php
                // Get X-ray results from radiologist
                $chestXrayResult = '';
                $chestXrayFindings = '';
                $radiologistName = '';
                $reviewDate = '';
                $hasXrayImage = false;
                $xrayImagePath = '';
                
                if ($latestExamination && isset($latestExamination->lab_findings['chest_xray'])) {
                    $cxr = $latestExamination->lab_findings['chest_xray'];
                    $chestXrayResult = $cxr['result'] ?? '';
                    $chestXrayFindings = $cxr['finding'] ?? '';
                    $radiologistName = $cxr['reviewed_by'] ?? '';
                    $reviewDate = $cxr['reviewed_at'] ?? '';
                }
                
                // Get X-ray image from medical checklist
                $medicalChecklist = \App\Models\MedicalChecklist::where('user_id', $latestExamination->user_id)
                    ->where('examination_type', 'opd')
                    ->whereNotNull('xray_image_path')
                    ->first();
                
                if ($medicalChecklist && $medicalChecklist->xray_image_path) {
                    $hasXrayImage = true;
                    $xrayImagePath = $medicalChecklist->xray_image_path;
                }
            @endphp

            @if($chestXrayResult || $hasXrayImage)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-x-ray text-indigo-600 mr-2"></i>
                    Chest X-Ray Results
                </h3>
                
                <div class="space-y-4">
                    <!-- X-Ray Image -->
                    @if($hasXrayImage)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-image text-indigo-600 mr-2"></i>
                                Chest X-Ray Image
                            </h4>
                            <span class="text-xs text-gray-500">
                                Taken by: {{ $medicalChecklist->chest_xray_done_by ?? 'RadTech' }}
                            </span>
                        </div>
                        <div class="relative bg-gray-900 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $xrayImagePath) }}" 
                                 alt="Chest X-Ray" 
                                 class="w-full h-64 object-contain cursor-pointer hover:scale-105 transition-transform duration-200"
                                 onclick="openXrayModal('{{ asset('storage/' . $xrayImagePath) }}')">
                            <div class="absolute top-2 right-2">
                                <button type="button" onclick="openXrayModal('{{ asset('storage/' . $xrayImagePath) }}')" 
                                        class="bg-black/50 hover:bg-black/70 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                    <i class="fas fa-expand mr-1"></i>Enlarge
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Radiologist Results -->
                    @if($chestXrayResult)
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Radiologist Result</label>
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-2 rounded-lg text-sm font-medium {{ $chestXrayResult === 'Normal' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-orange-100 text-orange-800 border border-orange-200' }}">
                                        {{ $chestXrayResult }}
                                    </span>
                                    <i class="fas fa-check-circle {{ $chestXrayResult === 'Normal' ? 'text-green-500' : 'text-orange-500' }}"></i>
                                </div>
                                @if($reviewDate)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Reviewed: {{ \Carbon\Carbon::parse($reviewDate)->format('M d, Y g:i A') }}
                                    </p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Radiologist Findings</label>
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="text-sm text-blue-800">{{ $chestXrayFindings ?: 'No findings recorded' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- ECG Results -->
            @if($latestExamination->ecg)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                    Electrocardiogram (ECG)
                </h3>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm text-red-800">{{ $latestExamination->ecg }}</div>
                </div>
            </div>
            @endif

            <!-- Clinical Findings -->
            @if($latestExamination->findings)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-notes-medical text-purple-600 mr-2"></i>
                    Clinical Findings
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-700">{{ $latestExamination->findings }}</div>
                </div>
            </div>
            @endif

            <!-- Examination History -->
            @if($opdExaminations->count() > 1)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history text-gray-600 mr-2"></i>
                    Previous Examinations
                </h3>
                <div class="space-y-3">
                    @foreach($opdExaminations->skip(1) as $exam)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900">{{ $exam->date ? $exam->date->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-sm text-gray-600">Status: {{ ucfirst(str_replace('_', ' ', $exam->status)) }}</div>
                        </div>
                        @if($exam->fitness_assessment)
                        <span class="px-2 py-1 text-xs rounded-full {{ $exam->fitness_assessment === 'Fit to work' ? 'bg-green-100 text-green-800' : ($exam->fitness_assessment === 'Not fit for work' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $exam->fitness_assessment }}
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- X-Ray Image Modal -->
<div id="xrayModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl max-h-full p-4">
        <button onclick="closeXrayModal()" 
                class="absolute top-4 right-4 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-all duration-200 z-10">
            <i class="fas fa-times text-xl"></i>
        </button>
        <img id="modalXrayImage" src="" alt="Chest X-Ray" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

<script>
function openXrayModal(imageSrc) {
    document.getElementById('modalXrayImage').src = imageSrc;
    document.getElementById('xrayModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeXrayModal() {
    document.getElementById('xrayModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('xrayModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeXrayModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeXrayModal();
    }
});
</script>
@endsection






