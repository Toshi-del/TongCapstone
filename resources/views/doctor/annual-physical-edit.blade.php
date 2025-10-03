@extends('layouts.doctor')

@section('title', 'Edit Annual Physical Examination - RSS Citi Health Services')
@section('page-title', 'Edit Annual Physical Examination')
@section('page-description', 'Update and manage comprehensive health examination results')

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
        <div class="px-8 py-6 bg-purple-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-file-medical text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">Edit Annual Physical Examination</h1>
                        <p class="text-purple-100 text-sm">Comprehensive health assessment and medical evaluation</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl px-6 py-4 border border-white border-opacity-30">
                    <p class="text-purple-100 text-sm font-medium">Patient ID</p>
                    <p class="text-white text-2xl font-bold">#{{ $annualPhysical->id }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Form Container -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <!-- Patient Information Section -->
        @if($annualPhysical->patient)
        <div class="px-8 py-6 bg-purple-600">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center border border-white border-opacity-30">
                    <i class="fas fa-user-injured text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Patient Information</h2>
                    <p class="text-purple-100 text-sm">Basic patient details and contact information</p>
                </div>
            </div>
        </div>
        
        <div class="p-8 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 border-l-4 border-purple-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Patient Name</label>
                    <div class="text-lg font-bold text-gray-900">{{ $annualPhysical->patient->full_name }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 border-l-4 border-green-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Age</label>
                    <div class="text-lg font-bold text-gray-900">{{ $annualPhysical->patient->age }} years</div>
                </div>
                <div class="bg-white rounded-xl p-6 border-l-4 border-blue-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Sex</label>
                    <div class="text-lg font-bold text-gray-900">{{ $annualPhysical->patient->sex }}</div>
                </div>
                <div class="bg-white rounded-xl p-6 border-l-4 border-orange-500 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Email</label>
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $annualPhysical->patient->email }}</div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Form Section -->
        <div class="p-8">
            <form action="{{ route('doctor.annual-physical.update', $annualPhysical->id) }}" method="POST" class="space-y-8">
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
                                <textarea name="illness_history" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Enter illness history or hospitalizations...">{{ old('illness_history', $annualPhysical->illness_history) }}</textarea>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-injured mr-2 text-orange-600"></i>Accidents / Operations
                                </label>
                                <textarea name="accidents_operations" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Enter accidents or surgical operations...">{{ old('accidents_operations', $annualPhysical->accidents_operations) }}</textarea>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Past Medical History
                                </label>
                                <textarea name="past_medical_history" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Enter past medical conditions...">{{ old('past_medical_history', $annualPhysical->past_medical_history) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-4">
                                <i class="fas fa-users mr-2 text-purple-600"></i>Family Medical History
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @php
                                    $family = $annualPhysical->family_history ?? [];
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
                                $habits = $annualPhysical->personal_habits ?? [];
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
                    <div class="px-6 py-4 bg-orange-600">
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
                                $phys = $annualPhysical->physical_exam ?? [];
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
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                        {{ data_get($phys, $key, 'Not recorded') }}
                                        @if(data_get($phys, $key))
                                            <span class="text-xs text-gray-500 ml-1">{{ $vital['unit'] }}</span>
                                        @endif
                                    </div>
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
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-700 min-h-[4rem]">
                            {{ $annualPhysical->skin_marks ?: 'No identifying marks, scars, or tattoos recorded' }}
                        </div>
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
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                {{ $annualPhysical->visual ?: 'Visual acuity not tested' }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-palette mr-2 text-green-600"></i>Ishihara Test
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                {{ $annualPhysical->ishihara_test ?: 'Color vision test not performed' }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Laboratory Examination Report Section -->
                <div class="bg-teal-50 rounded-xl p-6 border-l-4 border-teal-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-flask text-teal-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-teal-900" style="font-family: 'Poppins', sans-serif;">Laboratory Examination Report</h3>
                    </div>
                    
                    @php
                        $lab = $annualPhysical->lab_report ?? [];
                        
                        // Get pathologist tests that were actually requested for this patient
                        $pathologistTests = $annualPhysical->patient->pathologist_tests ?? collect();
                        $requestedTests = collect();
                        
                        // Build dynamic lab fields based on requested tests
                        $labFields = [];
                        $additionalTests = [];
                        
                        foreach($pathologistTests as $test) {
                            $testName = $test['test_name'];
                            $standardFieldName = '';
                            $config = ['icon' => 'fas fa-flask', 'color' => 'teal'];
                            
                            // Standardize field names and set appropriate icons/colors
                            if (stripos($testName, 'complete blood count') !== false || stripos($testName, 'cbc') !== false) {
                                $standardFieldName = 'cbc';
                                $config = ['icon' => 'fas fa-tint', 'color' => 'red'];
                            } elseif (stripos($testName, 'urinalysis') !== false) {
                                $standardFieldName = 'urinalysis';
                                $config = ['icon' => 'fas fa-vial', 'color' => 'yellow'];
                            } elseif (stripos($testName, 'stool') !== false || stripos($testName, 'fecalysis') !== false) {
                                $standardFieldName = 'fecalysis';
                                $config = ['icon' => 'fas fa-microscope', 'color' => 'brown'];
                            } elseif (stripos($testName, 'blood chemistry') !== false) {
                                $standardFieldName = 'blood_chemistry';
                                $config = ['icon' => 'fas fa-heartbeat', 'color' => 'pink'];
                            } elseif (stripos($testName, 'sodium') !== false) {
                                $standardFieldName = 'sodium';
                                $config = ['icon' => 'fas fa-atom', 'color' => 'blue'];
                            } elseif (stripos($testName, 'potassium') !== false) {
                                $standardFieldName = 'potassium';
                                $config = ['icon' => 'fas fa-atom', 'color' => 'green'];
                            } elseif (stripos($testName, 'calcium') !== false) {
                                $standardFieldName = 'ionized_calcium';
                                $config = ['icon' => 'fas fa-atom', 'color' => 'purple'];
                            } elseif (stripos($testName, 'hbsag') !== false || stripos($testName, 'hepatitis b') !== false) {
                                $standardFieldName = 'hbsag_screening';
                                $config = ['icon' => 'fas fa-shield-virus', 'color' => 'orange'];
                                $additionalTests[$standardFieldName] = ['name' => $testName, 'config' => $config];
                                continue;
                            } elseif (stripos($testName, 'hepa a') !== false || stripos($testName, 'hepatitis a') !== false) {
                                $standardFieldName = 'hepa_a_igg_igm';
                                $config = ['icon' => 'fas fa-virus', 'color' => 'purple'];
                                $additionalTests[$standardFieldName] = ['name' => $testName, 'config' => $config];
                                continue;
                            } else {
                                $standardFieldName = strtolower(str_replace([' ', '-', '&', '(', ')'], '_', $testName));
                                $config = ['icon' => 'fas fa-flask', 'color' => 'indigo'];
                            }
                            
                            if ($standardFieldName) {
                                $labFields[$standardFieldName] = $config;
                                $labFields[$standardFieldName]['display_name'] = $testName;
                            }
                        }
                        
                        // If no specific tests found, show basic tests
                        if (empty($labFields)) {
                            $labFields = [
                                'cbc' => ['icon' => 'fas fa-tint', 'color' => 'red', 'display_name' => 'Complete Blood Count (CBC)'],
                                'urinalysis' => ['icon' => 'fas fa-vial', 'color' => 'yellow', 'display_name' => 'Urinalysis'],
                                'fecalysis' => ['icon' => 'fas fa-microscope', 'color' => 'brown', 'display_name' => 'Fecalysis']
                            ];
                        }
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        @foreach($labFields as $field => $config)
                            <div class="bg-white rounded-lg p-4 border-l-4 border-{{ $config['color'] }}-500">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-2"></i>{{ $config['display_name'] ?? str_replace('_', ' ', ucwords($field)) }}
                                </label>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                    {{ data_get($lab, $field, 'Not available') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Additional Laboratory Tests (Only show if requested) -->
                    @if(!empty($additionalTests))
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">
                            <i class="fas fa-plus-square text-teal-600 mr-2"></i>Additional Laboratory Tests
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($additionalTests as $field => $testInfo)
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-{{ $testInfo['config']['color'] }}-500">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="{{ $testInfo['config']['icon'] }} text-{{ $testInfo['config']['color'] }}-600 mr-2"></i>{{ $testInfo['name'] }}
                                    </label>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                        {{ data_get($lab, $field, 'Not available') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Physical Findings Section -->
                <div class="bg-cyan-50 rounded-xl p-6 border-l-4 border-cyan-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-user-md text-cyan-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-cyan-900" style="font-family: 'Poppins', sans-serif;">Physical Examination Findings</h3>
                    </div>
                    
                    @php
                        $physicalRows = [
                            'Neck' => ['icon' => 'fas fa-head-side-cough', 'color' => 'blue'],
                            'Chest-Breast Axilla' => ['icon' => 'fas fa-lungs', 'color' => 'green'],
                            'Lungs' => ['icon' => 'fas fa-lungs-virus', 'color' => 'teal'],
                            'Heart' => ['icon' => 'fas fa-heartbeat', 'color' => 'red'],
                            'Abdomen' => ['icon' => 'fas fa-stomach', 'color' => 'orange'],
                            'Extremities' => ['icon' => 'fas fa-hand-paper', 'color' => 'purple'],
                            'Anus-Rectum' => ['icon' => 'fas fa-circle', 'color' => 'gray'],
                            'GUT' => ['icon' => 'fas fa-pills', 'color' => 'yellow'],
                            'Inguinal / Genital' => ['icon' => 'fas fa-user-circle', 'color' => 'pink']
                        ];
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach($physicalRows as $row => $config)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div class="flex items-center">
                                    <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-3"></i>
                                    <span class="font-semibold text-gray-700">{{ $row }}</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Result</label>
                                    <input type="text" name="physical_findings[{{ $row }}][result]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" value="{{ old('physical_findings.'.$row.'.result', data_get($annualPhysical->physical_findings, $row.'.result', '')) }}" placeholder="Enter result">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Findings</label>
                                    <input type="text" name="physical_findings[{{ $row }}][findings]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" value="{{ old('physical_findings.'.$row.'.findings', data_get($annualPhysical->physical_findings, $row.'.findings', '')) }}" placeholder="Enter findings">
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
                    </div>
                    
                    @php
                        $labRows = [
                            'Chest X-Ray' => ['icon' => 'fas fa-x-ray', 'color' => 'gray'],
                            'Urinalysis' => ['icon' => 'fas fa-vial', 'color' => 'yellow'],
                            'Fecalysis' => ['icon' => 'fas fa-microscope', 'color' => 'brown'],
                            'CBC' => ['icon' => 'fas fa-tint', 'color' => 'red'],
                            'HBsAg Screening' => ['icon' => 'fas fa-shield-virus', 'color' => 'purple'],
                            'HEPA A IGG & IGM' => ['icon' => 'fas fa-virus', 'color' => 'pink'],
                            'Others' => ['icon' => 'fas fa-plus-circle', 'color' => 'indigo']
                        ];
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach($labRows as $row => $config)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div class="flex items-center">
                                    <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-3"></i>
                                    <span class="font-semibold text-gray-700">{{ $row }}</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Result</label>
                                    @php
                                        $testKey = strtolower(str_replace([' ', '-', '&'], ['_', '_', '_'], $row));
                                        $testKey = str_replace('chest_x_ray', 'xray', $testKey);
                                        $testKey = str_replace('hepa_a_igg___igm', 'hepa_a_igg_igm', $testKey);
                                        
                                        // Special handling for drug test - use actual drug test data
                                        if ($testKey === 'drug_test') {
                                            $drugTestData = $annualPhysical->drug_test ?? [];
                                            $methResult = $drugTestData['methamphetamine'] ?? '';
                                            $marijuanaResult = $drugTestData['marijuana'] ?? '';
                                            
                                            // Count positive results
                                            $positiveCount = 0;
                                            if (strtolower($methResult) === 'positive') $positiveCount++;
                                            if (strtolower($marijuanaResult) === 'positive') $positiveCount++;
                                            
                                            // Determine overall result
                                            if ($positiveCount >= 2) {
                                                $resultValue = 'Both Positive';
                                            } elseif ($positiveCount === 1) {
                                                $resultValue = '1 Positive';
                                            } elseif ($methResult && $marijuanaResult) {
                                                $resultValue = 'Both Negative';
                                            } else {
                                                $resultValue = 'Not available';
                                            }
                                        } else {
                                            $resultValue = data_get($annualPhysical->lab_report, $testKey, '');
                                        }
                                    @endphp
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                        {{ $resultValue ?: 'Not available' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Findings</label>
                                    @php
                                        // Special handling for drug test findings
                                        if ($testKey === 'drug_test') {
                                            $drugTestData = $annualPhysical->drug_test ?? [];
                                            $methResult = $drugTestData['methamphetamine'] ?? '';
                                            $marijuanaResult = $drugTestData['marijuana'] ?? '';
                                            
                                            $findings = [];
                                            if ($methResult) {
                                                $findings[] = "Methamphetamine: " . ucfirst($methResult);
                                            }
                                            if ($marijuanaResult) {
                                                $findings[] = "Marijuana: " . ucfirst($marijuanaResult);
                                            }
                                            
                                            $findingsValue = !empty($findings) ? implode(', ', $findings) : 'No findings';
                                        } else {
                                            $findingsKey = $testKey . '_findings';
                                            $findingsValue = data_get($annualPhysical->lab_report, $findingsKey, '');
                                        }
                                    @endphp
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700 min-h-[2.5rem]">
                                        {{ $findingsValue ?: 'No findings' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- ECG Section (Only show if medical test includes ECG) -->
                @php
                    $hasEcgTest = false;
                    if ($annualPhysical->patient && $annualPhysical->patient->appointment) {
                        $selectedTests = $annualPhysical->patient->appointment->selected_tests ?? collect();
                        foreach ($selectedTests as $test) {
                            $testName = $test->name ?? '';
                            if (stripos($testName, 'ecg') !== false || 
                                stripos($testName, 'electrocardiogram') !== false ||
                                stripos($testName, 'Annual Medical with Drug Test and ECG') !== false ||
                                stripos($testName, 'Annual Medical Examination with Drug Test and ECG') !== false) {
                                $hasEcgTest = true;
                                break;
                            }
                        }
                    }
                @endphp
                
                @if($hasEcgTest)
                <div class="bg-red-50 rounded-xl p-6 border-l-4 border-red-600">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-heartbeat text-red-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-red-900" style="font-family: 'Poppins', sans-serif;">Electrocardiogram (ECG)</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-chart-line text-red-600 mr-2"></i>ECG Results
                        </label>
                        <input type="text" name="ecg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" value="{{ old('ecg', $annualPhysical->ecg ?? '') }}" placeholder="Enter ECG findings and interpretation">
                    </div>
                </div>
                @endif
                
                <!-- Drug Test Form (DT Form 2) -->
                @php
                    $medicalTestName = '';
                    if ($annualPhysical->patient && $annualPhysical->patient->appointment && $annualPhysical->patient->appointment->medicalTest) {
                        $medicalTestName = strtolower($annualPhysical->patient->appointment->medicalTest->name);
                    }
                    
                    // Check if this examination requires a drug test
                    $requiresDrugTest = in_array($medicalTestName, [
                        'annual medical with drug test',
                        'annual medical with drug test and ecg',
                        'annual medical examination with drug test',
                        'annual medical examination with drug test and ecg'
                    ]) || str_contains($medicalTestName, 'drug test');
                @endphp
                
                @if($requiresDrugTest)
                @php
                    $drugTest = $annualPhysical->drug_test ?? [];
                    $drugTestResults = $annualPhysical->drugTestResults()->latest()->first();
                @endphp
                
                <x-drug-test-form 
                    exam-type="annual-physical"
                    :patient-data="[
                        'name' => $annualPhysical->patient->first_name . ' ' . $annualPhysical->patient->last_name,
                        'address' => $annualPhysical->patient->address ?? '',
                        'age' => $annualPhysical->patient->age ?? '',
                        'gender' => ucfirst($annualPhysical->patient->sex ?? '')
                    ]"
                    :existing-data="$drugTest"
                    :connected-result="$drugTestResults"
                    :is-edit="false" />
                @endif
                
                <!-- General Findings & Assessment Section -->
                <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-clipboard-check text-purple-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-purple-900" style="font-family: 'Poppins', sans-serif;">General Findings & Medical Assessment</h3>
                    </div>
                    
                    @php
                        $lab = $annualPhysical->lab_report ?? [];
                        
                        // Count "Not normal" results from lab tests
                        $notNormalCount = 0;
                        $labTests = ['xray', 'urinalysis', 'fecalysis', 'cbc', 'hbsag_screening', 'hepa_a_igg_igm', 'others'];
                        
                        foreach($labTests as $test) {
                            $result = data_get($lab, $test, '');
                            if (strtolower($result) === 'not normal' || strtolower($result) === 'abnormal' || strtolower($result) === 'positive') {
                                $notNormalCount++;
                            }
                        }
                        
                        // Determine assessment based on lab results
                        if ($notNormalCount >= 2) {
                            $assessment = 'Not fit for work';
                            $assessmentColor = 'red';
                            $assessmentIcon = 'fas fa-times-circle';
                        } elseif ($notNormalCount === 1) {
                            $assessment = 'For evaluation';
                            $assessmentColor = 'yellow';
                            $assessmentIcon = 'fas fa-exclamation-triangle';
                        } else {
                            $assessment = 'Fit to work';
                            $assessmentColor = 'green';
                            $assessmentIcon = 'fas fa-check-circle';
                        }
                    @endphp
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        
                        <!-- Automatic Medical Assessment -->
                        <div class="bg-white rounded-lg p-4 border-l-4 border-{{ $assessmentColor }}-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-stethoscope text-{{ $assessmentColor }}-600 mr-2"></i>Medical Assessment
                            </label>
                            <div class="bg-{{ $assessmentColor }}-50 p-3 rounded-lg border border-{{ $assessmentColor }}-200">
                                <div class="flex items-center">
                                    <i class="{{ $assessmentIcon }} text-{{ $assessmentColor }}-600 mr-2"></i>
                                    <span class="font-semibold text-{{ $assessmentColor }}-800">{{ $assessment }}</span>
                                </div>
                                <div class="text-xs text-{{ $assessmentColor }}-600 mt-1">
                                    Based on {{ $notNormalCount }} abnormal lab result(s)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Physician Signature & Submit Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-purple-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-md text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Physician Authorization</h3>
                                <p class="text-purple-100 text-sm">Complete examination and authorize results</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-signature text-purple-600 mr-2"></i>Physician's Signature
                                </label>
                                <div class="border-b-2 border-gray-300 pb-4">
                                    <p class="text-xs text-gray-500">Digital signature will be applied upon submission</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
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
@endsection

