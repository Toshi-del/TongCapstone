@extends('layouts.pathologist')

@section('title', 'Edit OPD Examination')
@section('page-title', 'Edit OPD Examination')

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
    <form action="{{ route('pathologist.opd.update', $examination->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Patient Information Header -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">OPD Walk-in Examination</h2>
                    <p class="text-gray-600">{{ $examination->name ?? ($opdPatient->fname . ' ' . $opdPatient->lname) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-800">{{ $examination->date ? \Carbon\Carbon::parse($examination->date)->format('M d, Y') : now()->format('M d, Y') }}</p>
                    <p class="text-gray-600">Patient ID: OPD-{{ str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <!-- Laboratory Examination Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">
                <i class="fas fa-flask mr-2 text-teal-600"></i>Laboratory Examination Report
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @if($opdTests->count() > 0)
                    @foreach($opdTests as $test)
                        @php
                            $testKey = strtolower(str_replace([' ', '-', '&', '.'], '_', $test->medical_test));
                        @endphp
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                {{ $test->medical_test }}
                                @if(isset($test->is_standard) && $test->is_standard)
                                    <span class="text-xs text-blue-600 font-medium ml-2">
                                        <i class="fas fa-star mr-1"></i>Standard Lab Test
                                    </span>
                                @endif
                            </label>
                            <select name="lab_report[{{ $testKey }}]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 lab-test-select"
                                    data-test-key="{{ $testKey }}">
                                <option value="">Select Result</option>
                                <option value="Normal" {{ old('lab_report.' . $testKey, $examination->lab_report[$testKey] ?? '') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Not Normal" {{ old('lab_report.' . $testKey, $examination->lab_report[$testKey] ?? '') === 'Not Normal' ? 'selected' : '' }}>Not Normal</option>
                            </select>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-3 text-center py-8">
                        <div class="text-gray-500">
                            <i class="fas fa-info-circle text-2xl mb-2"></i>
                            <p>No medical tests found for this OPD patient.</p>
                            <p class="text-sm">Please ensure the patient has approved medical tests in the system.</p>
                        </div>
                    </div>
                @endif
            </div>


            <!-- Laboratory Examinations Report Table -->
            @if($opdTests->count() > 0)
                <h4 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-table mr-2 text-teal-600"></i>Laboratory Examinations Report
                </h4>
                
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b border-gray-300">TEST</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b border-gray-300">RESULT</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b border-gray-300">FINDINGS</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($opdTests as $index => $test)
                                @php
                                    $testKey = strtolower(str_replace([' ', '-', '&', '.'], '_', $test->medical_test));
                                @endphp
                                <tr class="{{ $index < $opdTests->count() - 1 ? 'border-b border-gray-200' : '' }}">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $test->medical_test }}</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="lab_results[{{ $testKey }}_result]" 
                                               id="result_{{ $testKey }}"
                                               value="{{ old('lab_results.' . $testKey . '_result', $examination->lab_results[$testKey . '_result'] ?? '') }}"
                                               class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-teal-500 focus:border-teal-500 bg-gray-50"
                                               placeholder="Auto-populated from above"
                                               readonly>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="lab_results[{{ $testKey }}_findings]" 
                                               value="{{ old('lab_results.' . $testKey . '_findings', $examination->lab_results[$testKey . '_findings'] ?? '') }}"
                                               class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-teal-500 focus:border-teal-500"
                                               placeholder="Enter findings">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        

        <!-- Status and Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Examination Status</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                        <option value="pending" {{ old('status', $examination->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status', $examination->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="sent_to_company" {{ old('status', $examination->status) === 'sent_to_company' ? 'selected' : '' }}>Sent to Company</option>
                    </select>
                </div>
                
                <div class="flex space-x-4">
                    <a href="{{ route('pathologist.opd') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-semibold">
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown changes to auto-populate result fields
    const labTestSelects = document.querySelectorAll('.lab-test-select');
    
    labTestSelects.forEach(function(select) {
        // Set initial values on page load
        updateResultField(select);
        
        // Handle changes
        select.addEventListener('change', function() {
            updateResultField(this);
        });
    });
    
    function updateResultField(selectElement) {
        const testKey = selectElement.getAttribute('data-test-key');
        const resultField = document.getElementById('result_' + testKey);
        
        if (resultField) {
            const selectedValue = selectElement.value;
            resultField.value = selectedValue;
            
            // Visual feedback for the result field
            if (selectedValue === 'Normal') {
                resultField.className = resultField.className.replace(/bg-red-50|bg-yellow-50/, 'bg-green-50');
            } else if (selectedValue === 'Not Normal') {
                resultField.className = resultField.className.replace(/bg-green-50|bg-yellow-50/, 'bg-red-50');
            } else {
                resultField.className = resultField.className.replace(/bg-green-50|bg-red-50/, 'bg-gray-50');
            }
        }
    }
});
</script>
@endsection
