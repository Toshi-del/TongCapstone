@extends('layouts.admin')

@section('title', 'View OPD Examination - RSS Citi Health Services')
@section('page-title', 'View OPD Examination')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-stethoscope text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">OPD Examination Details</h1>
                            <p class="text-blue-100 text-sm mt-1">Review and manage OPD examination results</p>
                        </div>
                    </div>
                    <div class="bg-white/20 rounded-xl px-6 py-4 border border-white/30">
                        <p class="text-blue-100 text-sm font-medium">Examination ID</p>
                        <p class="text-white text-2xl font-bold">#{{ $examination->id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-teal-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Patient Information</h2>
                        <p class="text-teal-100 text-sm mt-1">Basic patient details and examination data</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl p-6 border-l-4 border-teal-500 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Patient Name</label>
                        <div class="text-lg font-bold text-gray-900">{{ $examination->name ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-l-4 border-green-500 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Email</label>
                        <div class="text-lg font-bold text-gray-900">{{ $examination->user->email ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-l-4 border-blue-500 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Examination Date</label>
                        <div class="text-lg font-bold text-gray-900">{{ $examination->date ? $examination->date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-l-4 border-orange-500 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Status</label>
                        <div class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $examination->status ?? 'pending')) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Assessment -->
        @if($examination->fitness_assessment)
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gray-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Medical Assessment</h2>
                        <p class="text-gray-100 text-sm mt-1">Fitness assessment and examination results</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Assessment Result -->
                    <div class="bg-white rounded-lg p-6 border-l-4 border-gray-500 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-stethoscope text-gray-600 mr-2"></i>Fitness Assessment
                        </label>
                        @php
                            $assessment = $examination->fitness_assessment;
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
                            <i class="{{ $assessmentIcon }} text-xl"></i>
                            <span class="font-bold text-lg">{{ $assessment }}</span>
                        </div>
                        <div class="text-xs text-gray-600 mt-3">
                            Drug Tests: {{ $examination->drug_positive_count ?? 0 }} positive | 
                            Medical Tests: {{ $examination->medical_abnormal_count ?? 0 }} abnormal | 
                            Physical Exam: {{ $examination->physical_abnormal_count ?? 0 }} abnormal
                        </div>
                    </div>
                    
                    <!-- Assessment Breakdown -->
                    <div class="bg-white rounded-lg p-6 border-l-4 border-blue-500 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>Assessment Details
                        </label>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex justify-between">
                                <span>Drug Test Results:</span>
                                <span class="font-medium">{{ ($examination->drug_positive_count ?? 0) == 0 ? 'All Negative' : ($examination->drug_positive_count ?? 0) . ' Positive' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Medical Test Results:</span>
                                <span class="font-medium">{{ ($examination->medical_abnormal_count ?? 0) == 0 ? 'All Normal' : ($examination->medical_abnormal_count ?? 0) . ' Abnormal' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Physical Examination:</span>
                                <span class="font-medium">{{ ($examination->physical_abnormal_count ?? 0) == 0 ? 'All Normal' : ($examination->physical_abnormal_count ?? 0) . ' Abnormal' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Examination Results -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-purple-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-medical text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Examination Results</h2>
                        <p class="text-purple-100 text-sm mt-1">Detailed medical examination findings</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <!-- Medical History -->
                @if($examination->illness_history || $examination->accidents_operations || $examination->past_medical_history)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Medical History</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($examination->illness_history)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Illness/Hospitalization</label>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700">{{ $examination->illness_history }}</div>
                        </div>
                        @endif
                        @if($examination->accidents_operations)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accidents/Operations</label>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700">{{ $examination->accidents_operations }}</div>
                        </div>
                        @endif
                        @if($examination->past_medical_history)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Past Medical History</label>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700">{{ $examination->past_medical_history }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Vital Signs -->
                @if($examination->physical_exam)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-heartbeat text-red-600 mr-2"></i>Vital Signs
                    </h3>
                    @php
                        $vitals = $examination->physical_exam ?? [];
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
                @if($examination->physical_findings)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-stethoscope text-blue-600 mr-2"></i>Physical Examination Findings
                    </h3>
                    @php
                        $physicalFindings = $examination->physical_findings ?? [];
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
                @if($examination->visual || $examination->ishihara_test || $examination->skin_marks)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-eye text-blue-600 mr-2"></i>Vision & Special Tests
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($examination->visual)
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-glasses text-blue-600"></i>
                                <span class="font-medium text-blue-900">Visual Acuity</span>
                            </div>
                            <div class="text-sm text-blue-800">{{ $examination->visual }}</div>
                        </div>
                        @endif
                        @if($examination->ishihara_test)
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-palette text-green-600"></i>
                                <span class="font-medium text-green-900">Ishihara Test</span>
                            </div>
                            <div class="text-sm text-green-800">{{ $examination->ishihara_test }}</div>
                        </div>
                        @endif
                        @if($examination->skin_marks)
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-hand-holding-medical text-purple-600"></i>
                                <span class="font-medium text-purple-900">Skin Marks</span>
                            </div>
                            <div class="text-sm text-purple-800">{{ $examination->skin_marks }}</div>
                        </div>
                        @endif
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
                    
                    if ($examination && isset($examination->lab_findings['chest_xray'])) {
                        $cxr = $examination->lab_findings['chest_xray'];
                        $chestXrayResult = $cxr['result'] ?? '';
                        $chestXrayFindings = $cxr['finding'] ?? '';
                        $radiologistName = $cxr['reviewed_by'] ?? '';
                        $reviewDate = $cxr['reviewed_at'] ?? '';
                    }
                    
                    // Get X-ray image from medical checklist
                    $medicalChecklist = \App\Models\MedicalChecklist::where('user_id', $examination->user_id)
                        ->where('examination_type', 'opd')
                        ->whereNotNull('xray_image_path')
                        ->first();
                    
                    if ($medicalChecklist && $medicalChecklist->xray_image_path) {
                        $hasXrayImage = true;
                        $xrayImagePath = $medicalChecklist->xray_image_path;
                    }
                @endphp

                @if($chestXrayResult || $hasXrayImage)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-x-ray text-indigo-600 mr-2"></i>Chest X-Ray Results
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
                @if($examination->ecg)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-heartbeat text-red-600 mr-2"></i>Electrocardiogram (ECG)
                    </h3>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <div class="text-sm text-red-800">{{ $examination->ecg }}</div>
                    </div>
                </div>
                @endif

                <!-- Laboratory Results -->
                @php
                    // Combine lab_findings and lab_report data
                    $labFindings = $examination->lab_findings ?? [];
                    $labReport = $examination->lab_report ?? [];
                    
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
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-microscope text-lime-600 mr-2"></i>Laboratory Test Results
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
                                <div class="text-xs text-gray-400 mt-2">
                                    Source: {{ ucwords(str_replace('_', ' ', $data['source'])) }}
                                </div>
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

                <!-- Clinical Findings -->
                @if($examination->findings)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Clinical Findings</h3>
                    <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700">{{ $examination->findings }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Actions</h3>
                        <p class="text-gray-600 text-sm">Manage examination status and send results</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.opd') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                        
                        @if($examination->status === 'sent_to_admin')
                        <form action="{{ route('admin.send-opd-results', $examination->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send to OPD
                            </button>
                        </form>
                        @elseif($examination->status === 'sent_to_patient')
                        <form action="{{ route('admin.approve-opd-examination', $examination->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-check mr-2"></i>
                                Approve Examination
                            </button>
                        </form>
                        @elseif($examination->status === 'approved')
                        <div class="inline-flex items-center px-6 py-3 bg-emerald-100 text-emerald-800 rounded-lg font-medium border border-emerald-200">
                            <i class="fas fa-check-circle mr-2"></i>
                            Examination Approved
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
