@extends('layouts.doctor')

@section('title', 'Edit Pre-Employment Examination')
@section('page-title', 'Edit Pre-Employment Examination')
@section('page-description', 'Update and manage employment medical screening results')

@section('content')
<div class="space-y-8">
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500">
        <div class="px-8 py-6 bg-gradient-to-r from-green-500 to-green-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-white font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="text-green-200 hover:text-white transition-colors duration-200" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-600">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-briefcase mr-3"></i>Pre-Employment Medical Examination
                    </h1>
                    <p class="text-blue-100">Employment medical screening and health assessment certificate</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-800 bg-opacity-50 rounded-lg px-4 py-2 border border-blue-500">
                        <p class="text-blue-200 text-sm font-medium">Exam ID</p>
                        <p class="text-white text-lg font-bold">#{{ $preEmployment->id }}</p>
                    </div>
                    <a href="{{ route('doctor.pre-employment.examination.show', $preEmployment->id) }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-x-ray mr-2"></i>View X-Ray Results
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Form Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Applicant Information Section -->
        @if($preEmployment->preEmploymentRecord)
        <div class="px-8 py-6 bg-gradient-to-r from-green-600 to-green-700 border-l-4 border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-user-tie mr-3"></i>Applicant Information
                    </h2>
                    <p class="text-green-100 mt-1">Job applicant details and company information</p>
                </div>
            </div>
        </div>
        
        <div class="p-8 bg-green-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                    <div class="text-lg font-bold text-blue-800">{{ $preEmployment->preEmploymentRecord->full_name ?? ($preEmployment->preEmploymentRecord->first_name . ' ' . $preEmployment->preEmploymentRecord->last_name) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Age</label>
                    <div class="text-lg font-bold text-green-800">{{ $preEmployment->preEmploymentRecord->age }} years</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-indigo-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Sex</label>
                    <div class="text-lg font-bold text-indigo-800">{{ $preEmployment->preEmploymentRecord->sex }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Company</label>
                    <div class="text-sm font-semibold text-yellow-800 truncate">{{ $preEmployment->preEmploymentRecord->company_name }}</div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Form Section -->
        <div class="p-8">
            <form action="{{ route('doctor.pre-employment.update', $preEmployment->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')
                
                <!-- Medical History Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
                        <div class="flex items-center">
                            <i class="fas fa-notes-medical text-white text-xl mr-3"></i>
                            <h3 class="text-lg font-bold text-white">Medical History Review</h3>
                        </div>
                    </div>
                    <div class="p-6 bg-green-50">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-hospital mr-2 text-green-600"></i>Illness / Hospitalization
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 min-h-[4rem] text-sm text-gray-700">
                                {{ $preEmployment->illness_history ?: 'No illness or hospitalization history recorded' }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-injured mr-2 text-orange-600"></i>Accidents / Operations
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 min-h-[4rem] text-sm text-gray-700">
                                {{ $preEmployment->accidents_operations ?: 'No accidents or surgical operations recorded' }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Past Medical History
                            </label>
                            <textarea name="past_medical_history" class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-h-[4rem] text-sm text-gray-700" placeholder="Enter past medical conditions">{{ old('past_medical_history', $preEmployment->past_medical_history) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-users mr-2 text-purple-600"></i>Family Medical History
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @php
                                $family = is_array($preEmployment->family_history) ? $preEmployment->family_history : [];
                                $options = ['asthma','arthritis','migraine','diabetes','heart_disease','tuberculosis','allergies','anemia','cancer','insanity','hypertension','epilepsy'];
                            @endphp
                            @foreach($options as $opt)
                                @php
                                    $isChecked = is_array($family) && in_array($opt, $family, true);
                                @endphp
                                <label class="inline-flex items-center p-3 rounded-lg border transition-colors duration-200 {{ $isChecked ? 'bg-green-100 border-green-300 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-600' }}">
                                    <input type="checkbox" name="family_history[]" value="{{ $opt }}" class="mr-3 text-green-600 focus:ring-green-500" {{ $isChecked ? 'checked' : '' }}>
                                    <span class="text-sm font-medium">{{ str_replace('_', ' ', ucwords($opt)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    </div>
                </div>
                <!-- Personal History Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
                        <div class="flex items-center">
                            <i class="fas fa-user-check text-white text-xl mr-3"></i>
                            <h3 class="text-lg font-bold text-white">Personal History & Habits</h3>
                        </div>
                    </div>
                    <div class="p-6 bg-blue-50">
                    
                    <div class="bg-white rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @php
                                // Ensure $habits is an array and properly decoded if it's a JSON string
                                $habits = [];
                                if (isset($preEmployment->personal_habits)) {
                                    if (is_string($preEmployment->personal_habits)) {
                                        $habits = json_decode($preEmployment->personal_habits, true) ?? [];
                                    } elseif (is_array($preEmployment->personal_habits)) {
                                        $habits = $preEmployment->personal_habits;
                                    }
                                }
                                
                                $habitOptions = [
                                    'alcohol' => ['icon' => 'fas fa-wine-bottle', 'color' => 'red'],
                                    'cigarettes' => ['icon' => 'fas fa-smoking', 'color' => 'orange'],
                                    'coffee_tea' => ['icon' => 'fas fa-coffee', 'color' => 'amber']
                                ];
                            @endphp
                            @foreach($habitOptions as $habit => $config)
                                @php
                                    $hasHabit = is_array($habits) && array_key_exists($habit, $habits) && $habits[$habit];
                                @endphp
                                <label class="flex items-center p-4 rounded-lg border transition-colors duration-200 {{ $hasHabit ? 'bg-blue-100 border-blue-300' : 'bg-gray-50 border-gray-200' }}">
                                    <input type="hidden" name="personal_habits[{{ $habit }}]" value="0">
                                    <input type="checkbox" name="personal_habits[{{ $habit }}]" value="1" class="hidden" {{ $hasHabit ? 'checked' : '' }}>
                                    <span class="flex items-center">
                                        <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-700 mr-3">{{ str_replace('_', ' ', ucwords($habit)) }}</span>
                                        <i class="fas {{ $hasHabit ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400' }}"></i>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    </div>
                </div>
                <!-- Physical Examination Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-yellow-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600">
                        <div class="flex items-center">
                            <i class="fas fa-stethoscope text-white text-xl mr-3"></i>
                            <h3 class="text-lg font-bold text-white">Physical Examination Results</h3>
                        </div>
                    </div>
                    <div class="p-6 bg-yellow-50">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @php 
                            $phys = $preEmployment->physical_exam ?? [];
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
                                    {{ $phys[$key] ?? 'Not recorded' }}
                                    @if(isset($phys[$key]) && $phys[$key])
                                        <span class="text-xs text-gray-500 ml-1">{{ $vital['unit'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>
                <!-- Skin Identification Marks Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-pink-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-pink-500 to-pink-600">
                        <div class="flex items-center">
                            <i class="fas fa-search text-white text-xl mr-3"></i>
                            <h3 class="text-lg font-bold text-white">Skin Identification Marks</h3>
                        </div>
                    </div>
                    <div class="p-6 bg-pink-50">
                    
                    <div class="bg-white rounded-lg p-4">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-700 min-h-[4rem]">
                            {{ $preEmployment->skin_marks ?: 'No identifying marks, scars, or tattoos recorded' }}
                        </div>
                    </div>
                    </div>
                </div>
                <!-- Visual Assessment & Findings Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-indigo-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600">
                        <div class="flex items-center">
                            <i class="fas fa-eye text-white text-xl mr-3"></i>
                            <h3 class="text-lg font-bold text-white">Visual Assessment & Findings</h3>
                        </div>
                    </div>
                    <div class="p-6 bg-indigo-50">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-glasses mr-2 text-blue-600"></i>Visual Acuity
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                {{ $preEmployment->visual ?: 'Visual acuity not tested' }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-palette mr-2 text-green-600"></i>Ishihara Test
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                {{ $preEmployment->ishihara_test ?: 'Color vision test not performed' }}
                            </div>
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
                        $lab = $preEmployment->lab_report ?? [];
                        
                        // Get pathologist tests that were actually requested for this patient
                        $pathologistTests = $preEmployment->preEmploymentRecord->pathologist_tests ?? collect();
                        
                        // Build dynamic lab fields based on requested tests - using same field names as pathologist
                        $labFields = [];
                        $additionalTests = [];
                        
                        foreach($pathologistTests as $test) {
                            $testName = $test['test_name'];
                            
                            // Skip Blood Chemistry Panel - we don't want to show this in the card view
                            if (stripos($testName, 'blood chemistry panel') !== false) {
                                continue;
                            }
                            
                            $testSlug = strtolower(str_replace([' ', '-', '&'], '_', $testName)); // Same as pathologist
                            $config = ['icon' => 'fas fa-flask', 'color' => 'teal'];
                            
                            // Set appropriate icons/colors based on test type
                            if (stripos($testName, 'complete blood count') !== false || stripos($testName, 'cbc') !== false) {
                                $config = ['icon' => 'fas fa-tint', 'color' => 'red'];
                            } elseif (stripos($testName, 'urinalysis') !== false) {
                                $config = ['icon' => 'fas fa-vial', 'color' => 'yellow'];
                            } elseif (stripos($testName, 'stool') !== false || stripos($testName, 'fecalysis') !== false) {
                                $config = ['icon' => 'fas fa-microscope', 'color' => 'brown'];
                            } elseif (stripos($testName, 'blood chemistry') !== false) {
                                $config = ['icon' => 'fas fa-heartbeat', 'color' => 'pink'];
                            } elseif (stripos($testName, 'sodium') !== false) {
                                $config = ['icon' => 'fas fa-atom', 'color' => 'blue'];
                            } elseif (stripos($testName, 'potassium') !== false) {
                                $config = ['icon' => 'fas fa-atom', 'color' => 'green'];
                            } elseif (stripos($testName, 'calcium') !== false) {
                                $config = ['icon' => 'fas fa-atom', 'color' => 'purple'];
                            } elseif (stripos($testName, 'hbsag') !== false || stripos($testName, 'hepatitis b') !== false) {
                                $config = ['icon' => 'fas fa-shield-virus', 'color' => 'orange'];
                                $additionalTests[$testSlug] = ['name' => $testName, 'config' => $config, 'slug' => $testSlug];
                                continue;
                            } elseif (stripos($testName, 'hepa a') !== false || stripos($testName, 'hepatitis a') !== false) {
                                $config = ['icon' => 'fas fa-virus', 'color' => 'purple'];
                                $additionalTests[$testSlug] = ['name' => $testName, 'config' => $config, 'slug' => $testSlug];
                                continue;
                            } else {
                                $config = ['icon' => 'fas fa-flask', 'color' => 'indigo'];
                            }
                            
                            $labFields[$testSlug] = $config;
                            $labFields[$testSlug]['display_name'] = $testName;
                            $labFields[$testSlug]['slug'] = $testSlug;
                        }
                        
                        // If no specific tests found, show basic tests
                        if (empty($labFields)) {
                            $labFields = [
                                'complete_blood_count__cbc_' => ['icon' => 'fas fa-tint', 'color' => 'red', 'display_name' => 'Complete Blood Count (CBC)', 'slug' => 'complete_blood_count__cbc_'],
                                'urinalysis' => ['icon' => 'fas fa-vial', 'color' => 'yellow', 'display_name' => 'Urinalysis', 'slug' => 'urinalysis'],
                                'fecalysis' => ['icon' => 'fas fa-microscope', 'color' => 'brown', 'display_name' => 'Stool Examination', 'slug' => 'fecalysis']
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
                                    @php
                                        $testSlug = $config['slug'];
                                        // Check both {test} and {test}_result for backward compatibility - same as pathologist
                                        $resultValue = data_get($lab, $testSlug . '_result', data_get($lab, $testSlug, ''));
                                    @endphp
                                    {{ $resultValue ?: 'Not available' }}
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
                                        @php
                                            $testSlug = $testInfo['slug'];
                                            // Check both {test} and {test}_result for backward compatibility - same as pathologist
                                            $resultValue = data_get($lab, $testSlug . '_result', data_get($lab, $testSlug, ''));
                                        @endphp
                                        {{ $resultValue ?: 'Not available' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

               

                <!-- Physical Findings Section -->
                <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-user-md text-blue-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-blue-800">Physical Examination Findings</h3>
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
                                    <input type="text" name="physical_findings[{{ $row }}][result]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" value="{{ old('physical_findings.'.$row.'.result', data_get($preEmployment->physical_findings, $row.'.result', '')) }}" placeholder="Enter result">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Findings</label>
                                    <input type="text" name="physical_findings[{{ $row }}][findings]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" value="{{ old('physical_findings.'.$row.'.findings', data_get($preEmployment->physical_findings, $row.'.findings', '')) }}" placeholder="Enter findings">
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
                        // Get pathologist tests that were actually requested for this patient
                        $pathologistTests = $preEmployment->preEmploymentRecord->pathologist_tests ?? collect();
                        
                        // Build dynamic lab fields based on requested tests
                        $labFields = [];
                        
                        // Always include Chest X-Ray as it's standard
                        $labFields[] = ['name' => 'Chest X-Ray', 'slug' => 'chest_x_ray', 'config' => ['icon' => 'fas fa-x-ray', 'color' => 'gray']];
                        
                        foreach($pathologistTests as $test) {
                            $testName = $test['test_name'];
                            
                            // Skip Blood Chemistry Panel - we don't want to show this in the card view
                            if (stripos($testName, 'blood chemistry panel') !== false) {
                                continue;
                            }
                            
                            $config = ['icon' => 'fas fa-flask', 'color' => 'teal'];
                            $slug = strtolower(str_replace([' ', '-', '&'], '_', $testName));
                            
                            // Set appropriate icons/colors and slugs based on test type
                            if (stripos($testName, 'complete blood count') !== false || stripos($testName, 'cbc') !== false) {
                                $config = ['icon' => 'fas fa-tint', 'color' => 'red'];
                                $slug = 'cbc';
                            } elseif (stripos($testName, 'urinalysis') !== false) {
                                $config = ['icon' => 'fas fa-vial', 'color' => 'yellow'];
                                $slug = 'urinalysis';
                            } elseif (stripos($testName, 'stool') !== false || stripos($testName, 'fecalysis') !== false) {
                                $config = ['icon' => 'fas fa-microscope', 'color' => 'brown'];
                                $slug = 'fecalysis';
                            } elseif (stripos($testName, 'hbsag') !== false || stripos($testName, 'hepatitis b') !== false) {
                                $config = ['icon' => 'fas fa-shield-virus', 'color' => 'purple'];
                                $slug = 'hbsag_screening';
                            } elseif (stripos($testName, 'hepa a') !== false || stripos($testName, 'hepatitis a') !== false) {
                                $config = ['icon' => 'fas fa-virus', 'color' => 'pink'];
                                $slug = 'hepa_a_igg_igm';
                            } else {
                                $config = ['icon' => 'fas fa-flask', 'color' => 'indigo'];
                            }
                            
                            $labFields[] = ['name' => $testName, 'slug' => $slug, 'config' => $config];
                        }
                        
                        // If no specific tests found besides X-ray, show basic tests
                        if (count($labFields) <= 1) {
                            $labFields = [
                                ['name' => 'Chest X-Ray', 'slug' => 'chest_x_ray', 'config' => ['icon' => 'fas fa-x-ray', 'color' => 'gray']],
                                ['name' => 'Urinalysis', 'slug' => 'urinalysis', 'config' => ['icon' => 'fas fa-vial', 'color' => 'yellow']],
                                ['name' => 'Fecalysis', 'slug' => 'fecalysis', 'config' => ['icon' => 'fas fa-microscope', 'color' => 'brown']],
                                ['name' => 'CBC', 'slug' => 'cbc', 'config' => ['icon' => 'fas fa-tint', 'color' => 'red']]
                            ];
                        }
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach($labFields as $labField)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div class="flex items-center">
                                    <i class="{{ $labField['config']['icon'] }} text-{{ $labField['config']['color'] }}-600 mr-3"></i>
                                    <span class="font-semibold text-gray-700">{{ $labField['name'] }}</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Result</label>
                                    @php
                                        $testSlug = $labField['slug'];
                                        
                                        // Special handling for Chest X-Ray - get from radiologist data
                                        if ($labField['name'] === 'Chest X-Ray') {
                                            $resultValue = '';
                                            if ($preEmployment->lab_findings && is_array($preEmployment->lab_findings) && isset($preEmployment->lab_findings['chest_xray'])) {
                                                $resultValue = $preEmployment->lab_findings['chest_xray']['result'] ?? '';
                                            }
                                        } else {
                                            // Check both {test} and {test}_result for backward compatibility - same as pathologist
                                            $resultValue = data_get($preEmployment->lab_report, $testSlug . '_result', data_get($preEmployment->lab_report, $testSlug, ''));
                                        }
                                    @endphp
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700">
                                        {{ $resultValue ?: 'Not available' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Findings</label>
                                    @php
                                        // Special handling for Chest X-Ray - get from radiologist data
                                        if ($labField['name'] === 'Chest X-Ray') {
                                            $findingsValue = '';
                                            if ($preEmployment->lab_findings && is_array($preEmployment->lab_findings) && isset($preEmployment->lab_findings['chest_xray'])) {
                                                $findingsValue = $preEmployment->lab_findings['chest_xray']['finding'] ?? '';
                                            }
                                        } else {
                                            $findingsValue = data_get($preEmployment->lab_report, $testSlug . '_findings', '');
                                        }
                                    @endphp
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm text-gray-700 min-h-[2.5rem]">
                                        @if($labField['name'] === 'Chest X-Ray' && $findingsValue)
                                            {{ $findingsValue === '—' ? 'Normal findings' : $findingsValue }}
                                        @else
                                            {{ $findingsValue ?: 'No findings' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Drug Test Form (DT Form 2) -->
                @php
                    $medicalTestName = $preEmployment->preEmploymentRecord->medicalTest->name ?? '';
                    $lowerTestName = strtolower($medicalTestName);
                    
                    // Check for drug test in medical test name
                    $requiresDrugTest = in_array($lowerTestName, [
                        'pre-employment with drug test',
                        'pre-employment with ecg and drug test',
                        'pre-employment with drug test and audio and ishihara',
                        'drug test only (bring valid i.d)'
                    ]) || str_contains($lowerTestName, 'drug test');
                @endphp
                
                @if($requiresDrugTest)
                @php
                    $drugTest = $preEmployment->drug_test ?? [];
                    $drugTestResults = $preEmployment->drugTestResults()->latest()->first();
                @endphp
                
                <x-drug-test-form 
                    exam-type="pre-employment"
                    :patient-data="[
                        'name' => $preEmployment->preEmploymentRecord->full_name ?? ($preEmployment->preEmploymentRecord->first_name . ' ' . $preEmployment->preEmploymentRecord->last_name),
                        'address' => $preEmployment->preEmploymentRecord->address ?? '',
                        'age' => $preEmployment->preEmploymentRecord->age ?? '',
                        'gender' => ucfirst($preEmployment->preEmploymentRecord->sex ?? '')
                    ]"
                    :existing-data="$drugTest"
                    :connected-result="$drugTestResults"
                    :is-edit="false" />
                @endif
                
                <!-- ECG Section (Only show if medical test includes ECG) -->
                @php
                    $hasEcgTest = false;
                    if ($preEmployment->preEmploymentRecord && $preEmployment->preEmploymentRecord->medicalTest) {
                        $medicalTestName = $preEmployment->preEmploymentRecord->medicalTest->name ?? '';
                        if (stripos($medicalTestName, 'ecg') !== false || 
                            stripos($medicalTestName, 'electrocardiogram') !== false ||
                            stripos($medicalTestName, 'Pre-Employment with ECG and Drug test') !== false ||
                            stripos($medicalTestName, 'Pre-Employment with Drug test and ECG') !== false) {
                            $hasEcgTest = true;
                        }
                    }
                @endphp
                
                @if($hasEcgTest)
                <!-- ECG Section -->
                <div class="bg-red-50 rounded-xl p-6 border-l-4 border-red-500">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-heartbeat text-red-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-red-800">Electrocardiogram (ECG)</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-chart-line text-red-600 mr-2"></i>ECG Results
                        </label>
                        <input type="text" name="ecg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" value="{{ old('ecg', $preEmployment->ecg ?? '') }}" placeholder="Enter ECG findings and interpretation">
                    </div>
                </div>
                @endif
                
                <!-- General Findings & Assessment Section -->
                <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-600">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-clipboard-check text-purple-600 text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-purple-900" style="font-family: 'Poppins', sans-serif;">General Findings & Medical Assessment</h3>
                    </div>
                    
                    @php
                        $lab = $preEmployment->lab_report ?? [];
                        $drugTest = $preEmployment->drug_test ?? [];
                        
                        
                        // Count drug test positive results
                        $drugPositiveCount = 0;
                        $methResult = $drugTest['methamphetamine_result'] ?? '';
                        $mariResult = $drugTest['marijuana_result'] ?? '';
                        
                        if (strtolower(trim($methResult)) === 'positive') {
                            $drugPositiveCount++;
                        }
                        if (strtolower(trim($mariResult)) === 'positive') {
                            $drugPositiveCount++;
                        }
                        
                        // Count "Not normal" results from medical tests (excluding drug test)
                        $medicalNotNormalCount = 0;
                        $medicalTests = ['chest_x_ray', 'urinalysis', 'fecalysis', 'cbc', 'hbsag_screening', 'hepa_a_igg_igm', 'others'];
                        
                        foreach($medicalTests as $test) {
                            $result = data_get($lab, $test, '');
                            if (strtolower($result) === 'not normal' || strtolower($result) === 'abnormal' || strtolower($result) === 'positive') {
                                $medicalNotNormalCount++;
                            }
                        }
                        
                        // Determine assessment based on drug test and medical test combination
                        // DRUG TEST - MEDICAL TEST
                        // All Negative, All normal - Fit to Work
                        // All Negative, 2 or more not normal - not fit
                        // 2 Negative, 1 Not Normal - For Eval
                        // 1 Positive, 1 Not Normal - Not Fit
                        // 1 Positive, 2 Not Normal - Not Fit
                        // 2 Positive, 2 Not Normal - Not Fit
                        // 1 Positive, All Normal - Not Fit
                        
                        if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0) {
                            // All Negative, All normal - Fit to Work
                            $assessment = 'Fit to work';
                            $assessmentColor = 'green';
                            $assessmentIcon = 'fas fa-check-circle';
                        } elseif ($drugPositiveCount == 0 && $medicalNotNormalCount >= 2) {
                            // All Negative, 2 or more not normal - not fit
                            $assessment = 'Not fit for work';
                            $assessmentColor = 'red';
                            $assessmentIcon = 'fas fa-times-circle';
                        } elseif ($drugPositiveCount == 0 && $medicalNotNormalCount == 1) {
                            // All Negative, 1 Not Normal - For Eval
                            $assessment = 'For evaluation';
                            $assessmentColor = 'yellow';
                            $assessmentIcon = 'fas fa-exclamation-triangle';
                        } elseif ($drugPositiveCount >= 1) {
                            // Any positive drug test result - Not Fit
                            $assessment = 'Not fit for work';
                            $assessmentColor = 'red';
                            $assessmentIcon = 'fas fa-times-circle';
                        } else {
                            // Fallback
                            $assessment = 'For evaluation';
                            $assessmentColor = 'yellow';
                            $assessmentIcon = 'fas fa-exclamation-triangle';
                        }
                        
                        // Total abnormal count for display
                        $totalAbnormalCount = $drugPositiveCount + $medicalNotNormalCount;
                    @endphp
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Automatic Medical Assessment -->
                        <div class="bg-white rounded-lg p-4 border-l-4 border-gray-500">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-stethoscope text-gray-600 mr-2"></i>Medical Assessment
                            </label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <span class="font-semibold text-gray-800">{{ $assessment }}</span>
                                </div>
                                <div class="text-xs text-gray-600 mt-1">
                                    Drug Test: {{ $drugPositiveCount }} positive result(s) | Medical Tests: {{ $medicalNotNormalCount }} abnormal result(s)
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
                                    <div class="pt-1 font-semibold">Applied Rule:</div>
                                    <div>
                                        @if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0)
                                            All Negative, All Normal → Fit to Work
                                        @elseif ($drugPositiveCount == 0 && $medicalNotNormalCount >= 2)
                                            All Negative, 2+ Abnormal → Not Fit
                                        @elseif ($drugPositiveCount == 0 && $medicalNotNormalCount == 1)
                                            All Negative, 1 Abnormal → For Evaluation
                                        @elseif ($drugPositiveCount >= 1)
                                            Any Positive Drug Test → Not Fit
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Physician Signature & Submit Section -->
                <div class="bg-gray-50 rounded-xl p-6 border-l-4 border-gray-500">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fas fa-user-md text-gray-600 mr-3"></i>Physician Authorization
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">Complete employment medical screening and authorize certificate</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div class="bg-white rounded-lg p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-signature text-gray-600 mr-2"></i>Physician's Signature
                            </label>
                            <div class="border-b-2 border-gray-300 pb-4">
                                <p class="text-xs text-gray-500">Digital signature will be applied upon submission</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-3"></i>
                                Save Medical Certificate
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openFullscreen(img) {
        // Create fullscreen modal
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; cursor: zoom-out;';
        
        // Create image element
        const fullImg = document.createElement('img');
        fullImg.src = img.src;
        fullImg.style.cssText = 'max-width: 90%; max-height: 90%; object-fit: contain;';
        
        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.style.cssText = 'position: absolute; top: 20px; right: 20px; background: white; color: black; border: none; padding: 10px 15px; border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 10000;';
        
        modal.appendChild(fullImg);
        modal.appendChild(closeBtn);
        document.body.appendChild(modal);
        
        // Close on click
        modal.onclick = function() {
            document.body.removeChild(modal);
        };
        
        closeBtn.onclick = function(e) {
            e.stopPropagation();
            document.body.removeChild(modal);
        };
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        });
    }
</script>
@endsection

