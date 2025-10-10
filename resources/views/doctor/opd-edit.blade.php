@extends('layouts.doctor')

@section('title', 'Edit OPD Examination - RSS Citi Health Services')
@section('page-title', 'Edit OPD Examination')
@section('page-description', 'Update and manage OPD medical examination results')

@section('content')
<div class="space-y-8">
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-4 bg-green-600">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <span class="text-white font-medium">{{ session('success') }}</span>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 bg-teal-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-user-md text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold mb-1">Edit OPD Examination</h1>
                        <p class="text-teal-100 text-sm">Out-Patient Department medical examination and evaluation</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 rounded-xl px-6 py-4 border border-white border-opacity-30">
                        <p class="text-teal-100 text-sm font-medium">Examination ID</p>
                        <p class="text-white text-2xl font-bold">#{{ $opdExamination->id }}</p>
                    </div>
                    <a href="{{ route('doctor.opd.examination.show', $opdExamination->id) }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-white border-opacity-30">
                        <i class="fas fa-eye mr-2"></i>View Results
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Form Container -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <!-- Patient Information Section -->
        <div class="px-8 py-6 bg-teal-600">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center border border-white border-opacity-30">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Patient Information</h2>
                    <p class="text-teal-100 text-sm">Basic patient details and contact information</p>
                </div>
            </div>
        </div>
        
        <div class="p-8 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 border-l-4 border-teal-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Patient Name</label>
                    <div class="text-lg font-bold text-gray-900">{{ $opdExamination->name ?? 'N/A' }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 border-l-4 border-green-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Date</label>
                    <div class="text-lg font-bold text-gray-900">{{ $opdExamination->date ? $opdExamination->date->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 border-l-4 border-blue-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Status</label>
                    <div class="text-lg font-bold text-gray-900">{{ ucfirst($opdExamination->status ?? 'pending') }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 border-l-4 border-orange-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Doctor</label>
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $opdExamination->user->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="p-8">
            <form action="{{ route('doctor.opd.update', $opdExamination->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')
                
                <!-- Medical History Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-green-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-notes-medical text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Medical History</h3>
                        </div>
                    </div>
                    <div class="p-6">
                    
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-hospital mr-2 text-green-600"></i>Illness / Hospitalization
                                </label>
                                <textarea name="illness_history" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Enter illness history or hospitalizations...">{{ old('illness_history', $opdExamination->illness_history) }}</textarea>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-injured mr-2 text-orange-600"></i>Accidents / Operations
                                </label>
                                <textarea name="accidents_operations" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Enter accidents or surgical operations...">{{ old('accidents_operations', $opdExamination->accidents_operations) }}</textarea>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Past Medical History
                                </label>
                                <textarea name="past_medical_history" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Enter past medical conditions...">{{ old('past_medical_history', $opdExamination->past_medical_history) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-4">
                                <i class="fas fa-users mr-2 text-purple-600"></i>Family Medical History
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @php
                                    $family = $opdExamination->family_history ?? [];
                                    $options = ['asthma','arthritis','migraine','diabetes','heart_disease','tuberculosis','allergies','anemia','cancer','insanity','hypertension','epilepsy'];
                                @endphp
                                @foreach($options as $opt)
                                    <label class="inline-flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-green-50 hover:border-green-300 cursor-pointer transition-colors duration-200">
                                        <input type="checkbox" name="family_history[]" value="{{ $opt }}" class="mr-3 text-green-600 focus:ring-green-500" {{ in_array($opt, $family ?? []) ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-gray-700">{{ str_replace('_', ' ', ucwords($opt)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Personal History Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Personal History & Habits</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @php
                                $habits = $opdExamination->personal_habits ?? [];
                                $habitOptions = [
                                    'alcohol' => ['icon' => 'fas fa-wine-bottle', 'color' => 'red'],
                                    'cigarettes' => ['icon' => 'fas fa-smoking', 'color' => 'orange'],
                                    'coffee_tea' => ['icon' => 'fas fa-coffee', 'color' => 'yellow']
                                ];
                            @endphp
                            @foreach($habitOptions as $habit => $config)
                                <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-colors duration-200">
                                    <input type="checkbox" name="personal_habits[]" value="{{ $habit }}" class="mr-4 text-blue-600 focus:ring-blue-500" {{ in_array($habit, $habits ?? []) ? 'checked' : '' }}>
                                    <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700">{{ str_replace('_', ' ', ucwords($habit)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Physical Examination Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-red-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-stethoscope text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Physical Examination</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @php 
                                $phys = $opdExamination->physical_exam ?? [];
                                $vitals = [
                                    'temp' => ['label' => 'Temperature', 'icon' => 'fas fa-thermometer-half', 'unit' => '°C', 'color' => 'red'],
                                    'height' => ['label' => 'Height', 'icon' => 'fas fa-ruler-vertical', 'unit' => 'cm', 'color' => 'blue'],
                                    'heart_rate' => ['label' => 'Heart Rate', 'icon' => 'fas fa-heartbeat', 'unit' => 'bpm', 'color' => 'pink'],
                                    'weight' => ['label' => 'Weight', 'icon' => 'fas fa-weight', 'unit' => 'kg', 'color' => 'green']
                                ];
                            @endphp
                            @foreach($vitals as $key => $vital)
                                <div class="bg-white rounded-lg p-4 border-l-4 border-{{ $vital['color'] }}-500">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="{{ $vital['icon'] }} text-{{ $vital['color'] }}-600 mr-2"></i>{{ $vital['label'] }}
                                    </label>
                                    <input type="text" name="physical_exam[{{ $key }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" value="{{ old('physical_exam.'.$key, data_get($phys, $key, '')) }}" placeholder="Enter {{ strtolower($vital['label']) }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Skin Identification Marks Section -->
                <div class="bg-pink-50 rounded-xl p-6 border-l-4 border-pink-600">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-search text-pink-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-pink-900" style="font-family: 'Poppins', sans-serif;">Skin Identification Marks</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <textarea name="skin_marks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm" placeholder="Enter any identifying marks, scars, or tattoos...">{{ old('skin_marks', $opdExamination->skin_marks) }}</textarea>
                    </div>
                </div>
                
                <!-- Visual & Findings Section -->
                <div class="bg-indigo-50 rounded-xl p-6 border-l-4 border-indigo-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-eye text-indigo-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-indigo-900" style="font-family: 'Poppins', sans-serif;">Visual Assessment</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-glasses mr-2 text-blue-600"></i>Visual Acuity
                            </label>
                            <input type="text" name="visual" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" value="{{ old('visual', $opdExamination->visual) }}" placeholder="Enter visual acuity results">
                        </div>
                        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-palette mr-2 text-green-600"></i>Ishihara Test
                            </label>
                            <input type="text" name="ishihara_test" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" value="{{ old('ishihara_test', $opdExamination->ishihara_test) }}" placeholder="Enter color vision test results">
                        </div>
                    </div>
                </div>

                <!-- Physical Findings Section -->
                <div class="bg-cyan-50 rounded-xl p-6 border-l-4 border-cyan-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-user-md text-cyan-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-cyan-900" style="font-family: 'Poppins', sans-serif;">Physical Examination Findings</h3>
                    </div>
                    
                    @php
                        $physicalRows = [
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
                    
                    <div class="space-y-4">
                        @foreach($physicalRows as $row => $config)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div class="flex items-center">
                                    <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-3"></i>
                                    <span class="font-semibold text-gray-700">{{ $config['label'] }}</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Result</label>
                                    @php
                                        $currentResult = old('physical_findings.'.$row.'.result', data_get($opdExamination->physical_findings, $row.'.result', ''));
                                    @endphp
                                    <select name="physical_findings[{{ $row }}][result]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm bg-white">
                                        <option value="">Select Result</option>
                                        <option value="Normal" {{ $currentResult === 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="Not Normal" {{ $currentResult === 'Not Normal' ? 'selected' : '' }}>Not Normal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Findings</label>
                                    <input type="text" name="physical_findings[{{ $row }}][findings]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" value="{{ old('physical_findings.'.$row.'.findings', data_get($opdExamination->physical_findings, $row.'.findings', '')) }}" placeholder="Enter findings">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Laboratory Test Results Section -->
                <div class="bg-lime-50 rounded-xl p-6 border-l-4 border-lime-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-microscope text-lime-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-lime-900" style="font-family: 'Poppins', sans-serif;">Laboratory Test Results</h3>
                        <span class="ml-3 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Completed by Pathologist</span>
                    </div>
                    
                    @if(isset($opdTests) && $opdTests->count() > 0)
                        <div class="space-y-4">
                            @foreach($opdTests as $test)
                                @php
                                    $testKey = strtolower(str_replace([' ', '-', '&', '.'], '_', $test->medical_test));
                                    $pathologistResult = $opdExamination->lab_report[$testKey] ?? '';
                                    $pathologistFindings = $opdExamination->lab_results[$testKey . '_findings'] ?? '';
                                    
                                    // Determine icon and color based on test name
                                    $icon = 'fas fa-flask';
                                    $color = 'blue';
                                    if (stripos($test->medical_test, 'x-ray') !== false || stripos($test->medical_test, 'xray') !== false) {
                                        $icon = 'fas fa-x-ray';
                                        $color = 'gray';
                                    } elseif (stripos($test->medical_test, 'urinalysis') !== false) {
                                        $icon = 'fas fa-vial';
                                        $color = 'yellow';
                                    } elseif (stripos($test->medical_test, 'fecalysis') !== false || stripos($test->medical_test, 'stool') !== false) {
                                        $icon = 'fas fa-microscope';
                                        $color = 'brown';
                                    } elseif (stripos($test->medical_test, 'cbc') !== false || stripos($test->medical_test, 'blood') !== false) {
                                        $icon = 'fas fa-tint';
                                        $color = 'red';
                                    } elseif (stripos($test->medical_test, 'ecg') !== false || stripos($test->medical_test, 'ekg') !== false) {
                                        $icon = 'fas fa-heartbeat';
                                        $color = 'red';
                                    }
                                @endphp
                                <div class="bg-white rounded-lg p-4 border border-gray-200 {{ $pathologistResult ? 'border-l-4 border-l-green-500' : '' }}">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                        <div class="flex items-center">
                                            <i class="{{ $icon }} text-{{ $color }}-600 mr-3"></i>
                                            <div>
                                                <span class="font-semibold text-gray-700">{{ $test->medical_test }}</span>
                                                @if(isset($test->is_standard) && $test->is_standard)
                                                    <span class="ml-2 text-xs text-blue-600 font-medium">
                                                        <i class="fas fa-star mr-1"></i>Standard
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Result</label>
                                            <div class="relative">
                                                <input type="text" 
                                                       value="{{ $pathologistResult ?: 'Pending pathologist review' }}" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm {{ $pathologistResult ? 'bg-green-50 text-green-800 font-medium' : 'bg-gray-50 text-gray-500' }}" 
                                                       readonly>
                                                @if($pathologistResult)
                                                    <div class="absolute right-2 top-2">
                                                        <i class="fas fa-check-circle text-green-500"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Pathologist Findings</label>
                                            <input type="text" 
                                                   value="{{ $pathologistFindings ?: 'No findings recorded' }}" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm {{ $pathologistFindings ? 'bg-blue-50 text-blue-800' : 'bg-gray-50 text-gray-500' }}" 
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-8 text-center border border-gray-200">
                            <div class="text-gray-500">
                                <i class="fas fa-info-circle text-3xl mb-3"></i>
                                <p class="text-lg font-medium">No laboratory tests found</p>
                                <p class="text-sm">Laboratory tests will appear here once processed by the pathologist.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Chest X-Ray Results Section -->
                <div class="bg-indigo-50 rounded-xl p-6 border-l-4 border-indigo-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-x-ray text-indigo-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-indigo-900" style="font-family: 'Poppins', sans-serif;">Chest X-Ray Results</h3>
                        <span class="ml-3 text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Completed by Radiologist</span>
                    </div>
                    
                    @php
                        // Get chest X-ray findings from radiologist
                        $chestXrayResult = '';
                        $chestXrayFindings = '';
                        $radiologistName = '';
                        $reviewDate = '';
                        $hasXrayImage = false;
                        $xrayImagePath = '';
                        
                        if ($opdExamination && isset($opdExamination->lab_findings['chest_xray'])) {
                            $cxr = $opdExamination->lab_findings['chest_xray'];
                            $chestXrayResult = $cxr['result'] ?? '';
                            $chestXrayFindings = $cxr['finding'] ?? '';
                            $radiologistName = $cxr['reviewed_by'] ?? '';
                            $reviewDate = $cxr['reviewed_at'] ?? '';
                        }
                        
                        // Get X-ray image from medical checklist
                        $medicalChecklist = \App\Models\MedicalChecklist::where('user_id', $opdExamination->user_id)
                            ->where('examination_type', 'opd')
                            ->whereNotNull('xray_image_path')
                            ->first();
                        
                        if ($medicalChecklist && $medicalChecklist->xray_image_path) {
                            $hasXrayImage = true;
                            $xrayImagePath = $medicalChecklist->xray_image_path;
                        }
                    @endphp
                    
                    @if($chestXrayResult || $hasXrayImage)
                        <div class="space-y-4">
                            <!-- X-Ray Image Display -->
                            @if($hasXrayImage)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
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
                            <div class="bg-white rounded-lg p-4 border border-gray-200 {{ $chestXrayResult ? 'border-l-4 border-l-indigo-500' : '' }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Radiologist Result</label>
                                        <div class="relative">
                                            <input type="text" 
                                                   value="{{ $chestXrayResult ?: 'Pending radiologist review' }}" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm {{ $chestXrayResult ? ($chestXrayResult === 'Normal' ? 'bg-green-50 text-green-800 font-medium' : 'bg-orange-50 text-orange-800 font-medium') : 'bg-gray-50 text-gray-500' }}" 
                                                   readonly>
                                            @if($chestXrayResult)
                                                <div class="absolute right-2 top-2">
                                                    <i class="fas fa-check-circle {{ $chestXrayResult === 'Normal' ? 'text-green-500' : 'text-orange-500' }}"></i>
                                                </div>
                                            @endif
                                        </div>
                                        @if($reviewDate)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Reviewed: {{ \Carbon\Carbon::parse($reviewDate)->format('M d, Y g:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Radiologist Findings</label>
                                        <textarea 
                                            value="{{ $chestXrayFindings ?: 'No findings recorded' }}" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm {{ $chestXrayFindings ? 'bg-blue-50 text-blue-800' : 'bg-gray-50 text-gray-500' }}" 
                                            rows="3"
                                            readonly>{{ $chestXrayFindings ?: 'No findings recorded' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-8 text-center border border-gray-200">
                            <div class="text-gray-500">
                                <i class="fas fa-x-ray text-3xl mb-3"></i>
                                <p class="text-lg font-medium">No chest X-ray results available</p>
                                <p class="text-sm">X-ray results will appear here once processed by the radiologist.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- ECG Section -->
                <div class="bg-red-50 rounded-xl p-6 border-l-4 border-red-600">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-heartbeat text-red-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-red-900" style="font-family: 'Poppins', sans-serif;">Electrocardiogram (ECG)</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-chart-line text-red-600 mr-2"></i>ECG Results
                        </label>
                        <textarea name="ecg" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Enter ECG results and interpretation...">{{ old('ecg', $opdExamination->ecg) }}</textarea>
                    </div>
                </div>
                
                <!-- General Findings Section -->
                <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-clipboard-check text-purple-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-purple-900" style="font-family: 'Poppins', sans-serif;">General Findings & Assessment</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-notes-medical text-purple-600 mr-2"></i>Clinical Findings
                        </label>
                        <textarea name="findings" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Enter general findings and clinical assessment...">{{ old('findings', $opdExamination->findings) }}</textarea>
                    </div>
                </div>
                
                <!-- Medical Assessment Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white" style="font-family: 'Poppins', sans-serif;">Medical Assessment</h3>
                        </div>
                    </div>
                    
                    @php
                        // Calculate fitness assessment
                        $assessmentData = $opdExamination->calculateFitnessAssessment();
                        
                        // Use calculated or stored fitness assessment data
                        $assessment = $opdExamination->fitness_assessment ?? $assessmentData['assessment'];
                        $drugPositiveCount = $opdExamination->drug_positive_count ?? $assessmentData['drug_positive_count'];
                        $medicalNotNormalCount = $opdExamination->medical_abnormal_count ?? $assessmentData['medical_abnormal_count'];
                        $physicalNotNormalCount = $opdExamination->physical_abnormal_count ?? $assessmentData['physical_abnormal_count'];
                        
                        // Set colors for display
                        if ($assessment === 'Fit to work') {
                            $assessmentColor = 'green';
                            $assessmentIcon = 'fas fa-check-circle';
                        } elseif ($assessment === 'Not fit for work') {
                            $assessmentColor = 'red';
                            $assessmentIcon = 'fas fa-times-circle';
                        } else {
                            $assessmentColor = 'yellow';
                            $assessmentIcon = 'fas fa-exclamation-triangle';
                        }
                    @endphp
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
                            <!-- Automatic Medical Assessment -->
                            <div class="bg-white rounded-lg p-4 border-l-4 border-gray-500">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-stethoscope text-gray-600 mr-2"></i>Medical Assessment
                                </label>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    <div class="flex items-center">
                                        <i class="{{ $assessmentIcon }} text-{{ $assessmentColor }}-600 mr-2"></i>
                                        <span class="font-semibold text-gray-800">{{ $assessment }}</span>
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        Drug Test: {{ $drugPositiveCount }} positive | Medical Tests: {{ $medicalNotNormalCount }} abnormal | Physical Exam: {{ $physicalNotNormalCount }} abnormal
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Assessment Logic Breakdown -->
                            <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Assessment Logic
                                </label>
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="text-xs text-blue-600 space-y-1">
                                        <div class="font-semibold">Current Results:</div>
                                        <div>• Drug Tests: {{ $drugPositiveCount == 0 ? 'All Negative' : $drugPositiveCount . ' Positive' }}</div>
                                        <div>• Medical Tests: {{ $medicalNotNormalCount == 0 ? 'All Normal' : $medicalNotNormalCount . ' Abnormal' }}</div>
                                        <div>• Physical Exam: {{ $physicalNotNormalCount == 0 ? 'All Normal' : $physicalNotNormalCount . ' Abnormal' }}</div>
                                        <div class="pt-1 font-semibold">Applied Rule:</div>
                                        <div>
                                            @if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0 && $physicalNotNormalCount == 0)
                                                All Negative, All Normal, No Physical Abnormalities → Fit to Work
                                            @elseif ($drugPositiveCount >= 1)
                                                Any Positive Drug Test → Not Fit for Work
                                            @elseif ($medicalNotNormalCount >= 2)
                                                2+ Medical Abnormal → Not Fit for Work
                                            @elseif ($physicalNotNormalCount >= 2)
                                                2+ Physical Abnormal → Not Fit for Work
                                            @elseif ($medicalNotNormalCount >= 1 && $physicalNotNormalCount >= 1)
                                                1+ Medical + 1+ Physical Abnormal → Not Fit for Work
                                            @elseif ($medicalNotNormalCount == 1)
                                                1 Medical Abnormal → For Evaluation
                                            @elseif ($physicalNotNormalCount == 1)
                                                1 Physical Abnormal → For Evaluation
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Physician Signature & Submit Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-teal-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-md text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">Physician Authorization</h3>
                                <p class="text-teal-100 text-sm">Complete examination and authorize results</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-signature text-teal-600 mr-2"></i>Physician's Signature
                                </label>
                                <div class="border-b-2 border-gray-300 pb-4">
                                    <p class="text-xs text-gray-500">Digital signature will be applied upon submission</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-teal-600  rounded-lg font-semibold hover:bg-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save mr-3"></i>
                                    Save Examination Results
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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

// Debug form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const formData = new FormData(form);
            const physicalFindings = {};
            
            // Collect physical findings data
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('physical_findings[')) {
                    console.log('Physical Findings Field:', key, '=', value);
                }
            }
            
            console.log('Form is being submitted...');
        });
    }
});
</script>
@endsection
