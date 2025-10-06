@extends('layouts.pathologist')

@section('title', 'Edit Annual Physical Examination')
@section('page-title', 'Edit Annual Physical Examination')

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
    <form action="{{ route('pathologist.annual-physical.update', $examination->id ?? $examination->patient_id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Patient Information Header -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Annual Physical Examination</h2>
                    <p class="text-gray-600">{{ $examination->name ?? $examination->patient->full_name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-800">{{ $examination->date ? $examination->date->format('M d, Y') : now()->format('M d, Y') }}</p>
                    <p class="text-gray-600">Patient ID: {{ $examination->patient_id }}</p>
                </div>
            </div>
        </div>

        @php
            $pathologistTests = $examination->patient->pathologist_tests ?? collect();
            $groupedTests = $pathologistTests->groupBy('category_name');
        @endphp

        @if($pathologistTests->isNotEmpty())
            @foreach($groupedTests as $categoryName => $tests)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        @if($categoryName == 'Hematology')
                            <i class="fas fa-microscope mr-2 text-teal-600"></i>Routine Examinations Results
                        @elseif($categoryName == 'Clinical Pathology')
                            <i class="fas fa-eye mr-2 text-teal-600"></i>Clinical Microscopy Results
                        @elseif($categoryName == 'Blood Chemistry')
                            <i class="fas fa-flask mr-2 text-teal-600"></i>Blood Chemistry Results
                        @else
                            <i class="fas fa-flask mr-2 text-teal-600"></i>{{ $categoryName }} Results
                        @endif
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($tests as $test)
                            @php
                                // Standardize field names for common tests
                                $testName = $test['test_name'];
                                $standardFieldName = '';
                                
                                if (stripos($testName, 'complete blood count') !== false || stripos($testName, 'cbc') !== false) {
                                    $standardFieldName = 'cbc';
                                } elseif (stripos($testName, 'urinalysis') !== false) {
                                    $standardFieldName = 'urinalysis';
                                } elseif (stripos($testName, 'stool') !== false || stripos($testName, 'fecalysis') !== false) {
                                    $standardFieldName = 'fecalysis';
                                } elseif (stripos($testName, 'blood chemistry') !== false) {
                                    $standardFieldName = 'blood_chemistry';
                                } elseif (stripos($testName, 'sodium') !== false) {
                                    $standardFieldName = 'sodium';
                                } elseif (stripos($testName, 'potassium') !== false) {
                                    $standardFieldName = 'potassium';
                                } elseif (stripos($testName, 'calcium') !== false) {
                                    $standardFieldName = 'ionized_calcium';
                                } else {
                                    $standardFieldName = strtolower(str_replace([' ', '-', '&', '(', ')'], '_', $testName));
                                }
                                
                                $fieldName = 'lab_report[' . $standardFieldName . ']';
                                $cleanFieldName = $standardFieldName;
                            @endphp
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ $test['test_name'] }}
                                        @if($test['price'] > 0)
                                            <span class="text-xs text-green-600 font-medium">(₱{{ number_format($test['price'], 2) }})</span>
                                        @endif
                                    </label>
                                    
                                    
                                    @php
                                        $currentMainValue = old(str_replace(['[', ']'], ['.', ''], $fieldName), $examination->lab_report[$cleanFieldName] ?? '');
                                        $mainOthersFieldName = 'lab_report[' . $standardFieldName . '_main_others]';
                                        $cleanMainOthersFieldName = $standardFieldName . '_main_others';
                                    @endphp
                                    <select name="{{ $fieldName }}" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 result-dropdown main-dropdown" 
                                            data-test-name="{{ $standardFieldName }}-main"
                                            data-summary-target="summary-{{ $standardFieldName }}">
                                        <option value="">Not available</option>
                                        <option value="Normal" {{ $currentMainValue == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="Not Normal" {{ $currentMainValue == 'Not Normal' ? 'selected' : '' }}>Not Normal</option>
                                        <option value="Others" {{ $currentMainValue == 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                    <div class="others-input mt-2" id="others-{{ $standardFieldName }}-main" style="{{ $currentMainValue == 'Others' ? 'display: block;' : 'display: none;' }}">
                                        <input type="text" 
                                               name="{{ $mainOthersFieldName }}" 
                                               value="{{ old(str_replace(['[', ']'], ['.', ''], $mainOthersFieldName), $examination->lab_report[$cleanMainOthersFieldName] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                               placeholder="Specify"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <!-- Fallback: Standard Laboratory Tests when no specific tests are detected -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-flask mr-2 text-teal-600"></i>Standard Laboratory Tests
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Complete Blood Count (CBC)</label>
                            @php
                                $cbcValue = old('lab_report.complete_blood_count_cbc', $examination->lab_report['complete_blood_count_cbc'] ?? '');
                            @endphp
                            <select name="lab_report[complete_blood_count_cbc]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 result-dropdown" 
                                    data-test-name="complete_blood_count_cbc-fallback">
                                <option value="">Not available</option>
                                <option value="Normal" {{ $cbcValue == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Not Normal" {{ $cbcValue == 'Not Normal' ? 'selected' : '' }}>Not Normal</option>
                                <option value="Others" {{ $cbcValue == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            <div class="others-input mt-2" id="others-complete_blood_count_cbc-fallback" style="{{ $cbcValue == 'Others' ? 'display: block;' : 'display: none;' }}">
                                <input type="text" 
                                       name="lab_report[complete_blood_count_cbc_others]" 
                                       value="{{ old('lab_report.complete_blood_count_cbc_others', $examination->lab_report['complete_blood_count_cbc_others'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                       placeholder="Specify">
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Urinalysis</label>
                            @php
                                $urinalysisValue = old('lab_report.urinalysis', $examination->lab_report['urinalysis'] ?? '');
                            @endphp
                            <select name="lab_report[urinalysis]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 result-dropdown" 
                                    data-test-name="urinalysis-fallback">
                                <option value="">Not available</option>
                                <option value="Normal" {{ $urinalysisValue == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Not Normal" {{ $urinalysisValue == 'Not Normal' ? 'selected' : '' }}>Not Normal</option>
                                <option value="Others" {{ $urinalysisValue == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            <div class="others-input mt-2" id="others-urinalysis-fallback" style="{{ $urinalysisValue == 'Others' ? 'display: block;' : 'display: none;' }}">
                                <input type="text" 
                                       name="lab_report[urinalysis_others]" 
                                       value="{{ old('lab_report.urinalysis_others', $examination->lab_report['urinalysis_others'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                       placeholder="Specify">
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Stool Examination</label>
                            @php
                                $stoolValue = old('lab_report.stool_examination', $examination->lab_report['stool_examination'] ?? '');
                            @endphp
                            <select name="lab_report[stool_examination]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 result-dropdown" 
                                    data-test-name="stool_examination-fallback">
                                <option value="">Not available</option>
                                <option value="Normal" {{ $stoolValue == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Not Normal" {{ $stoolValue == 'Not Normal' ? 'selected' : '' }}>Not Normal</option>
                                <option value="Others" {{ $stoolValue == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            <div class="others-input mt-2" id="others-stool_examination-fallback" style="{{ $stoolValue == 'Others' ? 'display: block;' : 'display: none;' }}">
                                <input type="text" 
                                       name="lab_report[stool_examination_others]" 
                                       value="{{ old('lab_report.stool_examination_others', $examination->lab_report['stool_examination_others'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                       placeholder="Specify">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Laboratory Examinations Report Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-clipboard-list mr-2 text-teal-600"></i>Laboratory Examinations Report Summary
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
                                    // Use the same standardization logic for summary table
                                    $testName = $test['test_name'];
                                    $standardFieldName = '';
                                    
                                    if (stripos($testName, 'complete blood count') !== false || stripos($testName, 'cbc') !== false) {
                                        $standardFieldName = 'cbc';
                                    } elseif (stripos($testName, 'urinalysis') !== false) {
                                        $standardFieldName = 'urinalysis';
                                    } elseif (stripos($testName, 'stool') !== false || stripos($testName, 'fecalysis') !== false) {
                                        $standardFieldName = 'fecalysis';
                                    } elseif (stripos($testName, 'blood chemistry') !== false) {
                                        $standardFieldName = 'blood_chemistry';
                                    } elseif (stripos($testName, 'sodium') !== false) {
                                        $standardFieldName = 'sodium';
                                    } elseif (stripos($testName, 'potassium') !== false) {
                                        $standardFieldName = 'potassium';
                                    } elseif (stripos($testName, 'calcium') !== false) {
                                        $standardFieldName = 'ionized_calcium';
                                    } else {
                                        $standardFieldName = strtolower(str_replace([' ', '-', '&', '(', ')'], '_', $testName));
                                    }
                                    
                                    $resultFieldName = 'lab_report[' . $standardFieldName . '_result]';
                                    $findingsFieldName = 'lab_report[' . $standardFieldName . '_findings]';
                                    $cleanResultFieldName = $standardFieldName . '_result';
                                    $cleanFindingsFieldName = $standardFieldName . '_findings';
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">
                                        <div class="font-semibold">{{ $test['test_name'] }}</div>
                                        <div class="text-xs text-blue-600 mt-1">
                                            @if($test['is_package_component'] ?? false)
                                                <div><i class="fas fa-box mr-1"></i>{{ $test['package_name'] }} ({{ $test['package_category'] ?? 'Package' }}: ₱{{ number_format($test['package_price'], 2) }})</div>
                                            @endif
                                            @if(!empty($test['blood_chemistry_sources']))
                                                @foreach($test['blood_chemistry_sources'] as $bcSource)
                                                    <div><i class="fas fa-flask mr-1"></i>{{ $bcSource['name'] }} (Blood Chemistry: ₱{{ number_format($bcSource['price'], 2) }})</div>
                                                @endforeach
                                            @endif
                                            @if($test['price'] > 0 && !($test['is_package_component'] ?? false))
                                                <div><i class="fas fa-tag mr-1"></i>Individual Test: ₱{{ number_format($test['price'], 2) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        @php
                                            $currentValue = old(str_replace(['[', ']'], ['.', ''], $resultFieldName), $examination->lab_report[$cleanResultFieldName] ?? '');
                                            $othersFieldName = 'lab_report[' . strtolower(str_replace([' ', '-', '&', '(', ')'], '_', $test['test_name'])) . '_others]';
                                            $cleanOthersFieldName = strtolower(str_replace([' ', '-', '&', '(', ')'], '_', $test['test_name'])) . '_others';
                                        @endphp
                                        <input type="text" 
                                               name="{{ $resultFieldName }}" 
                                               id="summary-{{ $standardFieldName }}"
                                               value="{{ $currentValue }}"
                                               class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-teal-500 focus:border-teal-500 text-sm summary-input bg-gray-100" 
                                               data-test-name="{{ $standardFieldName }}"
                                               readonly
                                               placeholder="Not available">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" name="{{ $findingsFieldName }}" 
                                               value="{{ old(str_replace(['[', ']'], ['.', ''], $findingsFieldName), $examination->lab_report[$cleanFindingsFieldName] ?? '') }}"
                                               class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm"
                                               placeholder="Enter findings">
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Fallback rows when no specific tests are detected -->
                            <tr>
                                <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">
                                    <div class="font-semibold">Complete Blood Count (CBC)</div>
                                    <div class="text-xs text-blue-600 mt-1">
                                        <div><i class="fas fa-flask mr-1"></i>Standard Laboratory Test</div>
                                    </div>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    @php
                                        $cbcResultValue = old('lab_report.cbc_result', $examination->lab_report['cbc_result'] ?? '');
                                    @endphp
                                    <input type="text" 
                                           name="lab_report[cbc_result]" 
                                           id="summary-cbc_result-table"
                                           value="{{ $cbcResultValue }}"
                                           class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-teal-500 focus:border-teal-500 text-sm summary-input bg-gray-100" 
                                           data-test-name="cbc_result-table"
                                           readonly
                                           placeholder="Not available">
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <input type="text" name="lab_report[cbc_findings]" 
                                           value="{{ old('lab_report.cbc_findings', $examination->lab_report['cbc_findings'] ?? '') }}"
                                           class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm"
                                           placeholder="Enter findings">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700">
                                    <div class="font-semibold">Urinalysis</div>
                                    <div class="text-xs text-blue-600 mt-1">
                                        <div><i class="fas fa-flask mr-1"></i>Standard Laboratory Test</div>
                                    </div>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    @php
                                        $urinalysisResultValue = old('lab_report.urinalysis_result', $examination->lab_report['urinalysis_result'] ?? '');
                                    @endphp
                                    <input type="text" 
                                           name="lab_report[urinalysis_result]" 
                                           id="summary-urinalysis_result-table"
                                           value="{{ $urinalysisResultValue }}"
                                           class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-teal-500 focus:border-teal-500 text-sm summary-input bg-gray-100" 
                                           data-test-name="urinalysis_result-table"
                                           readonly
                                           placeholder="Not available">
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <input type="text" name="lab_report[urinalysis_findings]" 
                                           value="{{ old('lab_report.urinalysis_findings', $examination->lab_report['urinalysis_findings'] ?? '') }}"
                                           class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none text-sm"
                                           placeholder="Enter findings">
                                </td>
                            </tr>
                        @endif
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
                        <option value="completed" {{ old('status', $examination->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="sent_to_company" {{ old('status', $examination->status) == 'sent_to_company' ? 'selected' : '' }}>Sent to Company</option>
                    </select>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('pathologist.annual-physical') }}" 
                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-teal-600 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-teal-700 hover:to-blue-700 transition-all font-semibold">
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
    // Handle dropdown changes to show/hide "Others" input fields and sync with summary
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to main dropdowns
        document.querySelectorAll('.main-dropdown').forEach(dropdown => {
            dropdown.addEventListener('change', function() {
                const testName = this.getAttribute('data-test-name');
                const summaryTarget = this.getAttribute('data-summary-target');
                const othersDiv = document.getElementById('others-' + testName);
                
                // Handle "Others" input field visibility
                if (othersDiv) {
                    if (this.value === 'Others') {
                        othersDiv.style.display = 'block';
                        // Focus on the input field
                        const input = othersDiv.querySelector('input');
                        if (input) {
                            setTimeout(() => input.focus(), 100);
                        }
                    } else {
                        othersDiv.style.display = 'none';
                        // Clear the input value when hiding
                        const input = othersDiv.querySelector('input');
                        if (input && this.value !== 'Others') {
                            input.value = '';
                        }
                    }
                }
                
                // Sync with summary table text input
                if (summaryTarget) {
                    const summaryInput = document.getElementById(summaryTarget);
                    if (summaryInput) {
                        // Update the summary text input value
                        if (this.value === 'Others') {
                            // If "Others" is selected, get the value from the "Others" input field
                            const othersInput = othersDiv ? othersDiv.querySelector('input') : null;
                            if (othersInput && othersInput.value.trim()) {
                                summaryInput.value = othersInput.value;
                            } else {
                                summaryInput.value = 'Others (specify)';
                            }
                        } else {
                            summaryInput.value = this.value || 'Not available';
                        }
                        
                        // Update visual state
                        summaryInput.readOnly = false;
                        summaryInput.classList.remove('bg-gray-100');
                        summaryInput.classList.add('bg-white');
                        
                        // Make it read-only again after a brief moment to show it's been updated
                        setTimeout(() => {
                            summaryInput.readOnly = true;
                            summaryInput.classList.remove('bg-white');
                            summaryInput.classList.add('bg-gray-50');
                        }, 100);
                    }
                }
            });
        });
        
        // Add event listeners to fallback dropdowns (for when no specific tests are detected)
        document.querySelectorAll('.result-dropdown:not(.main-dropdown):not(.summary-dropdown)').forEach(dropdown => {
            dropdown.addEventListener('change', function() {
                const testName = this.getAttribute('data-test-name');
                const othersDiv = document.getElementById('others-' + testName);
                
                if (othersDiv) {
                    if (this.value === 'Others') {
                        othersDiv.style.display = 'block';
                        // Focus on the input field
                        const input = othersDiv.querySelector('input');
                        if (input) {
                            setTimeout(() => input.focus(), 100);
                        }
                    } else {
                        othersDiv.style.display = 'none';
                        // Clear the input value when hiding
                        const input = othersDiv.querySelector('input');
                        if (input && this.value !== 'Others') {
                            input.value = '';
                        }
                    }
                }
            });
        });
        
        // Initialize summary table state on page load
        document.querySelectorAll('.main-dropdown').forEach(dropdown => {
            const summaryTarget = dropdown.getAttribute('data-summary-target');
            if (summaryTarget) {
                const summaryInput = document.getElementById(summaryTarget);
                if (summaryInput) {
                    if (dropdown.value) {
                        if (dropdown.value === 'Others') {
                            const testName = dropdown.getAttribute('data-test-name');
                            const othersDiv = document.getElementById('others-' + testName);
                            const othersInput = othersDiv ? othersDiv.querySelector('input') : null;
                            if (othersInput && othersInput.value.trim()) {
                                summaryInput.value = othersInput.value;
                            } else {
                                summaryInput.value = 'Others (specify)';
                            }
                        } else {
                            summaryInput.value = dropdown.value;
                        }
                        summaryInput.classList.remove('bg-gray-100');
                        summaryInput.classList.add('bg-gray-50');
                    }
                }
            }
        });
        
        // Add event listeners to "Others" input fields to update summary when they change
        document.querySelectorAll('.others-input input').forEach(input => {
            input.addEventListener('input', function() {
                // Find the corresponding main dropdown
                const othersDiv = this.closest('.others-input');
                if (othersDiv) {
                    const testNameMatch = othersDiv.id.match(/others-(.+)-main/);
                    if (testNameMatch) {
                        const testName = testNameMatch[1];
                        const summaryInput = document.getElementById('summary-' + testName);
                        if (summaryInput) {
                            summaryInput.value = this.value || 'Others (specify)';
                        }
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
        
        // Validate "Others" fields - if "Others" is selected, the text input should not be empty
        document.querySelectorAll('.result-dropdown').forEach(dropdown => {
            if (dropdown.value === 'Others') {
                const testName = dropdown.getAttribute('data-test-name');
                const othersDiv = document.getElementById('others-' + testName);
                if (othersDiv) {
                    const input = othersDiv.querySelector('input');
                    if (input && !input.value.trim()) {
                        input.classList.add('border-red-500');
                        isValid = false;
                    } else if (input) {
                        input.classList.remove('border-red-500');
                    }
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields. If you select "Others", please specify the details.');
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

    // Add visual feedback for "Others" input fields
    document.querySelectorAll('.others-input input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-teal-500');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-teal-500');
        });
    });
</script>
@endsection
