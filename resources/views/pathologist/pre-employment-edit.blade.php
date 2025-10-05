@extends('layouts.pathologist')

@section('title', 'Edit Pre-Employment Examination')
@section('page-title', 'Edit Pre-Employment Examination')

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300 text-center font-semibold shadow-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-center font-semibold shadow-sm">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
        <h4 class="font-semibold mb-2">
            <i class="fas fa-exclamation-triangle mr-2"></i>Please correct the following errors:
        </h4>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-6xl mx-auto">
    <form action="{{ route('pathologist.pre-employment.update', $examination->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Employee Information Header -->
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Pre-Employment Examination</h2>
                    <p class="text-green-100">{{ $examination->name ?? $examination->preEmploymentRecord->full_name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold">{{ $examination->date->format('M d, Y') }}</p>
                    <p class="text-green-100">{{ $examination->company_name ?? $examination->preEmploymentRecord->company_name }}</p>
                </div>
            </div>
        </div>

        <!-- Pathologist Medical Tests -->
        @php
            $pathologistTests = $examination->preEmploymentRecord->pathologist_tests ?? collect();
            $groupedTests = $pathologistTests->groupBy('category_name');
        @endphp

        @if($pathologistTests->isNotEmpty())
            @foreach($groupedTests as $categoryName => $tests)
                @php
                    // Filter out tests that should be skipped (like Blood Chemistry Panel)
                    $visibleTests = $tests->filter(function($test) {
                        return stripos($test['test_name'], 'blood chemistry panel') === false;
                    });
                @endphp
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-flask mr-2 text-teal-600"></i>{{ $categoryName }} Results
                    </h3>
                    
                    @if($visibleTests->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($visibleTests as $test)
                                @php
                                    $fieldName = 'lab_report[' . strtolower(str_replace([' ', '-', '&'], '_', $test['test_name'])) . ']';
                                @endphp
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        {{ $test['test_name'] }}
                                        @if($test['is_package_component'] ?? false)
                                            <div class="text-xs text-blue-600 font-medium mt-1">
                                                <i class="fas fa-box mr-1"></i>From: {{ $test['package_name'] }}
                                                @if($test['package_price'] > 0)
                                                    ({{ $test['package_category'] ?? 'Package' }}: ₱{{ number_format($test['package_price'], 2) }})
                                                @endif
                                                @if(!empty($test['blood_chemistry_sources']))
                                                    @foreach($test['blood_chemistry_sources'] as $bcSource)
                                                        <br><i class="fas fa-flask mr-1"></i>{{ $bcSource['name'] }}
                                                        @if($bcSource['price'] > 0)
                                                            (Blood Chemistry: ₱{{ number_format($bcSource['price'], 2) }})
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($test['price'] > 0)
                                            <span class="text-xs text-emerald-600 font-medium">(₱{{ number_format($test['price'], 2) }})</span>
                                        @endif
                                    </label>
                                    @php
                                        $testSlug = strtolower(str_replace([' ', '-', '&'], '_', $test['test_name']));
                                        // Check both {test} and {test}_result for backward compatibility
                                        $currentResult = old($fieldName, $examination->lab_report[$testSlug . '_result'] ?? $examination->lab_report[$testSlug] ?? '');
                                        $isOthers = !in_array($currentResult, ['', 'Not available', 'Normal', 'Not normal']) && !empty($currentResult);
                                        $othersValue = $isOthers ? $currentResult : '';
                                        $selectValue = $isOthers ? 'Others' : ($currentResult ?: 'Not available');
                                    @endphp
                                    <select name="{{ $fieldName }}" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 result-dropdown"
                                            data-test-slug="{{ $testSlug }}">
                                        <option value="Not available" {{ $selectValue == 'Not available' ? 'selected' : '' }}>Not available</option>
                                        <option value="Normal" {{ $selectValue == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="Not normal" {{ $selectValue == 'Not normal' ? 'selected' : '' }}>Not normal</option>
                                        <option value="Others" {{ $selectValue == 'Others' ? 'selected' : '' }}>Others (specify)</option>
                                    </select>
                                    <input type="text" 
                                           name="{{ $fieldName }}_others" 
                                           value="{{ $othersValue }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 mt-2 others-input" 
                                           data-test-slug="{{ $testSlug }}"
                                           placeholder="Specify other result"
                                           style="{{ $selectValue == 'Others' ? '' : 'display: none;' }}">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State for Category with No Visible Tests -->
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-vial text-orange-500 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-2">No {{ $categoryName }} Tests Available</h4>
                            <p class="text-gray-500 text-sm max-w-md mx-auto">
                                No specific {{ strtolower($categoryName) }} tests have been assigned for this examination.
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <!-- Enhanced Empty State for No Pathologist Tests -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-flask text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">
                        <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>No Laboratory Tests Found
                    </h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        No specific pathologist tests have been assigned to this examination. You can still add general laboratory findings below.
                    </p>
                    
                    <!-- Fallback General Test Input -->
                    @if($examination->preEmploymentRecord->medicalTest)
                        <div class="bg-gradient-to-r from-teal-50 to-blue-50 rounded-xl p-6 border border-teal-200 max-w-lg mx-auto">
                            <h4 class="text-lg font-semibold text-teal-800 mb-4">
                                <i class="fas fa-clipboard-check mr-2"></i>General Laboratory Examination
                            </h4>
                            @php
                                $testName = $examination->preEmploymentRecord->medicalTest->name;
                                $fieldName = 'lab_report[' . strtolower(str_replace([' ', '-', '&'], '_', $testName)) . ']';
                                $testSlug = strtolower(str_replace([' ', '-', '&'], '_', $testName));
                                $currentResult = old($fieldName, $examination->lab_report[$testSlug . '_result'] ?? $examination->lab_report[$testSlug] ?? '');
                                $isOthers = !in_array($currentResult, ['', 'Not available', 'Normal', 'Not normal']) && !empty($currentResult);
                                $othersValue = $isOthers ? $currentResult : '';
                                $selectValue = $isOthers ? 'Others' : ($currentResult ?: 'Not available');
                            @endphp
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-teal-700 mb-2">{{ $testName }}</label>
                                    <select name="{{ $fieldName }}" 
                                            class="w-full px-4 py-3 border border-teal-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 result-dropdown bg-white"
                                            data-test-slug="{{ $testSlug }}">
                                        <option value="Not available" {{ $selectValue == 'Not available' ? 'selected' : '' }}>Not available</option>
                                        <option value="Normal" {{ $selectValue == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="Not normal" {{ $selectValue == 'Not normal' ? 'selected' : '' }}>Not normal</option>
                                        <option value="Others" {{ $selectValue == 'Others' ? 'selected' : '' }}>Others (specify)</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" 
                                           name="{{ $fieldName }}_others" 
                                           value="{{ $othersValue }}"
                                           class="w-full px-4 py-3 border border-teal-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 others-input" 
                                           data-test-slug="{{ $testSlug }}"
                                           placeholder="Specify other result or detailed findings"
                                           style="{{ $selectValue == 'Others' ? '' : 'display: none;' }}">
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No Medical Test at All -->
                        <div class="bg-red-50 rounded-xl p-6 border border-red-200 max-w-lg mx-auto">
                            <div class="flex items-center justify-center space-x-3 mb-4">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                <h4 class="text-lg font-semibold text-red-800">No Medical Test Assigned</h4>
                            </div>
                            <p class="text-red-700 text-sm">
                                This examination record does not have any medical tests assigned. Please contact the administrator to add the required tests.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Additional Examinations Section -->
        @if($examination->preEmploymentRecord->other_exams && !empty($examination->preEmploymentRecord->parsed_other_exams['additional_exams']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-clipboard-list mr-2 text-teal-600"></i>Additional Examinations
                </h3>
                
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Requested Additional Exams:</h4>
                    <p class="text-sm text-blue-700">{{ $examination->preEmploymentRecord->parsed_other_exams['additional_exams'] }}</p>
                </div>
                
                <div class="mt-4 space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Additional Examination Results</label>
                    <textarea name="lab_report[additional_exams_results]" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500"
                              placeholder="Enter results for additional examinations">{{ old('lab_report.additional_exams_results', $examination->lab_report['additional_exams_results'] ?? '') }}</textarea>
                </div>
            </div>
        @else
            <!-- Empty State for Additional Examinations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-clipboard-list mr-2 text-teal-600"></i>Additional Examinations
                    </h3>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        <i class="fas fa-info-circle mr-1"></i>Optional
                    </span>
                </div>
                
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-blue-500 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">No Additional Examinations Requested</h4>
                    <p class="text-gray-500 text-sm max-w-md mx-auto">
                        No additional examinations were requested for this patient.
                    </p>
                </div>
            </div>
        @endif

        <!-- Laboratory Examinations Report Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">
                <i class="fas fa-flask mr-2 text-teal-600"></i>Laboratory Examinations Report Summary
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">TEST</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">RESULT</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">FINDINGS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pathologistTests->isNotEmpty())
                            @foreach($pathologistTests as $test)
                                @php
                                    // Skip Blood Chemistry Panel - individual tests are shown separately
                                    if (stripos($test['test_name'], 'blood chemistry panel') !== false) {
                                        continue;
                                    }
                                    $testNameSlug = strtolower(str_replace([' ', '-', '&'], '_', $test['test_name']));
                                    $resultFieldName = 'lab_report[' . $testNameSlug . '_result]';
                                    $findingsFieldName = 'lab_report[' . $testNameSlug . '_findings]';
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">
                                        {{ $test['test_name'] }}
                                        @if($test['is_package_component'] ?? false)
                                            <div class="text-xs text-blue-600 mt-1">
                                                <i class="fas fa-box mr-1"></i>{{ $test['package_name'] }}
                                                @if($test['package_price'] > 0)
                                                    ({{ $test['package_category'] ?? 'Package' }}: ₱{{ number_format($test['package_price'], 2) }})
                                                @endif
                                                @if(!empty($test['blood_chemistry_sources']))
                                                    @foreach($test['blood_chemistry_sources'] as $bcSource)
                                                        <br><i class="fas fa-flask mr-1"></i>{{ $bcSource['name'] }}
                                                        @if($bcSource['price'] > 0)
                                                            (Blood Chemistry: ₱{{ number_format($bcSource['price'], 2) }})
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($test['price'] > 0)
                                            <div class="text-xs text-emerald-600">(₱{{ number_format($test['price'], 2) }})</div>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" 
                                               name="{{ $resultFieldName }}" 
                                               value="{{ old($resultFieldName, $examination->lab_report[$testNameSlug . '_result'] ?? '') }}"
                                               class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm bg-gray-50 table-result-display"
                                               data-test-slug="{{ $testNameSlug }}"
                                               readonly
                                               placeholder="Auto-filled from above">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" name="{{ $findingsFieldName }}" 
                                               value="{{ old($findingsFieldName, $examination->lab_report[$testNameSlug . '_findings'] ?? '') }}"
                                               class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm"
                                               placeholder="Enter findings">
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Enhanced Empty State for Laboratory Report Table -->
                            @if($examination->preEmploymentRecord->medicalTest)
                                @php
                                    $testName = $examination->preEmploymentRecord->medicalTest->name;
                                    $testNameSlug = strtolower(str_replace([' ', '-', '&'], '_', $testName));
                                    $resultFieldName = 'lab_report[' . $testNameSlug . '_result]';
                                    $findingsFieldName = 'lab_report[' . $testNameSlug . '_findings]';
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">{{ $testName }}</td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" 
                                               name="{{ $resultFieldName }}" 
                                               value="{{ old($resultFieldName, $examination->lab_report[$testNameSlug . '_result'] ?? '') }}"
                                               class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm bg-gray-50 table-result-display"
                                               data-test-slug="{{ $testNameSlug }}"
                                               readonly
                                               placeholder="Auto-filled from above">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" name="{{ $findingsFieldName }}" 
                                               value="{{ old($findingsFieldName, $examination->lab_report[$testNameSlug . '_findings'] ?? '') }}"
                                               class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm"
                                               placeholder="Enter findings">
                                    </td>
                                </tr>
                            @else
                                <!-- No Tests Available Row -->
                                <tr>
                                    <td colspan="3" class="border border-gray-300 px-4 py-8 text-center">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-flask text-gray-400 text-2xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-700 mb-1">No Laboratory Tests</h4>
                                                <p class="text-sm text-gray-500">No specific tests have been assigned to this examination.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endif
                        
                        <!-- Additional row for any extra findings -->
                        <tr>
                            <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">Additional Notes</td>
                            <td class="border border-gray-300 px-4 py-3" colspan="2">
                                <textarea name="lab_report[additional_notes]" rows="2"
                                          class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm resize-none"
                                          placeholder="Enter any additional notes or observations">{{ old('lab_report.additional_notes', $examination->lab_report['additional_notes'] ?? '') }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

     

        <!-- Status and Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="block text-sm font-semibold text-gray-700">Status:</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                        <option value="Pending" {{ old('status', $examination->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ old('status', $examination->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="sent_to_company" {{ old('status', $examination->status) == 'sent_to_company' ? 'selected' : '' }}>Sent to Company</option>
                    </select>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('pathologist.pre-employment') }}" 
                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-teal-700 transition-all font-semibold">
                        <i class="fas fa-save mr-2"></i>Update Examination
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // Result dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update table result display
        function updateTableResult(testSlug) {
            const dropdown = document.querySelector(`.result-dropdown[data-test-slug="${testSlug}"]`);
            const othersInput = document.querySelector(`.others-input[data-test-slug="${testSlug}"]`);
            const tableResultDisplay = document.querySelector(`.table-result-display[data-test-slug="${testSlug}"]`);
            
            if (dropdown && tableResultDisplay) {
                let resultValue = dropdown.value;
                
                // If "Others" is selected and there's a value in the others input, use that
                if (dropdown.value === 'Others' && othersInput && othersInput.value.trim()) {
                    resultValue = othersInput.value.trim();
                }
                
                tableResultDisplay.value = resultValue;
            }
        }
        
        // Handle result dropdown changes
        document.querySelectorAll('.result-dropdown').forEach(dropdown => {
            const testSlug = dropdown.getAttribute('data-test-slug');
            
            // Initial sync on page load
            updateTableResult(testSlug);
            
            dropdown.addEventListener('change', function() {
                const othersInput = document.querySelector(`.others-input[data-test-slug="${testSlug}"]`);
                
                if (this.value === 'Others') {
                    othersInput.style.display = 'block';
                    othersInput.focus();
                } else {
                    othersInput.style.display = 'none';
                    othersInput.value = '';
                }
                
                // Update table result
                updateTableResult(testSlug);
            });
        });
        
        // Handle others input changes
        document.querySelectorAll('.others-input').forEach(othersInput => {
            othersInput.addEventListener('input', function() {
                const testSlug = this.getAttribute('data-test-slug');
                updateTableResult(testSlug);
            });
        });
        
        // Handle form submission to merge Others input with dropdown value
        document.querySelector('form').addEventListener('submit', function(e) {
            document.querySelectorAll('.result-dropdown').forEach(dropdown => {
                if (dropdown.value === 'Others') {
                    const testSlug = dropdown.getAttribute('data-test-slug');
                    const othersInput = document.querySelector(`.others-input[data-test-slug="${testSlug}"]`);
                    if (othersInput && othersInput.value.trim()) {
                        // Create a hidden input with the others value as the main result
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = dropdown.name;
                        hiddenInput.value = othersInput.value.trim();
                        dropdown.parentNode.appendChild(hiddenInput);
                        dropdown.disabled = true; // Disable dropdown so it doesn't submit
                    }
                }
            });
        });
    });

    // Auto-save functionality
    let autoSaveTimeout;
    document.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // Implement auto-save functionality here
                console.log('Auto-saving examination data...');
            }, 2000);
        });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['status'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });

    // Real-time character count for textareas
    document.querySelectorAll('textarea').forEach(textarea => {
        const maxLength = 1000;
        const counter = document.createElement('div');
        counter.className = 'text-sm text-gray-500 mt-1';
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} characters remaining`;
            counter.className = `text-sm mt-1 ${remaining < 100 ? 'text-red-500' : 'text-gray-500'}`;
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });

    // Dynamic form sections
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.classList.toggle('hidden');
        }
    }
</script>
@endsection
