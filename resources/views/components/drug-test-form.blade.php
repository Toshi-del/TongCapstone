@props([
    'examType' => 'pre-employment', // 'pre-employment' or 'annual-physical'
    'patientData' => null,
    'existingData' => null,
    'connectedResult' => null,
    'isEdit' => false
])

@php
    $themeColors = [
        'pre-employment' => [
            'primary' => 'blue',
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-900',
            'accent' => 'text-blue-700',
            'ring' => 'focus:ring-blue-500',
            'button' => 'bg-blue-600 hover:bg-blue-700'
        ],
        'annual-physical' => [
            'primary' => 'purple',
            'bg' => 'bg-purple-50',
            'border' => 'border-purple-200',
            'text' => 'text-purple-900',
            'accent' => 'text-purple-700',
            'ring' => 'focus:ring-purple-500',
            'button' => 'bg-purple-600 hover:bg-purple-700'
        ]
    ];
    
    $colors = $themeColors[$examType];
    $drugTestData = $existingData ?? [];
@endphp

<!-- Drug Test Form Card -->
<div class="content-card rounded-xl p-8 shadow-lg border border-gray-200 bg-white">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-pills text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Drug Test Result (DT Form 2)</h3>
                <p class="text-gray-600">Urine drug screening examination form</p>
            </div>
        </div>
        <div class="text-right">
            <div class="text-gray-500 text-sm">Form Type</div>
            <div class="text-gray-900 font-bold">{{ ucwords(str_replace('-', ' ', $examType)) }}</div>
        </div>
    </div>

    @if($connectedResult && $isEdit)
    <!-- Connected Drug Test Information -->
    <div class="{{ $colors['bg'] }} rounded-xl p-6 {{ $colors['border'] }} mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-{{ $colors['primary'] }}-100 rounded-full flex items-center justify-center">
                <i class="fas fa-link text-{{ $colors['primary'] }}-600"></i>
            </div>
            <div>
                <h4 class="text-lg font-semibold {{ $colors['text'] }}">Connected Drug Test Record</h4>
                <p class="{{ $colors['accent'] }} text-sm">Linked drug test result (ID: {{ $connectedResult->id }})</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="font-medium {{ $colors['text'] }}">Test Date:</span>
                <div class="{{ $colors['accent'] }}">{{ $connectedResult->examination_datetime ? $connectedResult->examination_datetime->format('F j, Y \a\t g:i A') : 'N/A' }}</div>
            </div>
            <div>
                <span class="font-medium {{ $colors['text'] }}">Conducted By:</span>
                <div class="{{ $colors['accent'] }}">{{ $connectedResult->test_conducted_by ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="font-medium {{ $colors['text'] }}">Status:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">{{ ucfirst($connectedResult->status) }}</span>
            </div>
            <div>
                <span class="font-medium {{ $colors['text'] }}">Test Method:</span>
                <div class="{{ $colors['accent'] }}">{{ $connectedResult->test_method ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="space-y-8">
        <!-- Patient Information Section -->
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Patient Information</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Patient Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="drug_test[patient_name]" 
                           value="{{ old('drug_test.patient_name', $connectedResult->patient_name ?? $drugTestData['patient_name'] ?? ($patientData['name'] ?? '')) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors bg-gray-50 text-gray-700 cursor-not-allowed @error('drug_test.patient_name') border-red-500 ring-2 ring-red-200 @enderror" 
                           placeholder="Full name" readonly />
                    @error('drug_test.patient_name')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="drug_test[address]" 
                           value="{{ old('drug_test.address', $connectedResult->address ?? $drugTestData['address'] ?? ($patientData['address'] ?? '')) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif @error('drug_test.address') border-red-500 ring-2 ring-red-200 @enderror" 
                           placeholder="Patient address" @if(!$isEdit) readonly @else required @endif />
                    @error('drug_test.address')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Age <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="drug_test[age]" 
                           value="{{ old('drug_test.age', $connectedResult->age ?? $drugTestData['age'] ?? ($patientData['age'] ?? '')) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors bg-gray-50 text-gray-700 cursor-not-allowed @error('drug_test.age') border-red-500 ring-2 ring-red-200 @enderror" 
                           placeholder="Age" readonly />
                    @error('drug_test.age')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="drug_test[gender]" 
                           value="{{ old('drug_test.gender', $connectedResult->gender ?? $drugTestData['gender'] ?? ($patientData['gender'] ?? '')) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors bg-gray-50 text-gray-700 cursor-not-allowed @error('drug_test.gender') border-red-500 ring-2 ring-red-200 @enderror" 
                           placeholder="Gender" readonly />
                    @error('drug_test.gender')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Examination Details Section -->
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-gray-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Examination Details</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Date and Time of Examination <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="drug_test[examination_datetime]" 
                           value="{{ old('drug_test.examination_datetime', $connectedResult ? $connectedResult->examination_datetime?->format('Y-m-d\TH:i') : ($drugTestData['examination_datetime'] ?? now()->format('Y-m-d\TH:i'))) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif @error('drug_test.examination_datetime') border-red-500 ring-2 ring-red-200 @enderror" 
                           @if(!$isEdit) readonly @else required @endif />
                    @error('drug_test.examination_datetime')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Test Method <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="drug_test[test_method]" 
                           value="{{ old('drug_test.test_method', $connectedResult->test_method ?? $drugTestData['test_method'] ?? 'URINE TEST KIT') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif @error('drug_test.test_method') border-red-500 ring-2 ring-red-200 @enderror" 
                           @if(!$isEdit) readonly @else required @endif />
                    @error('drug_test.test_method')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>


                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Date of Last Intake of Substance
                    </label>
                    <input type="date" name="drug_test[last_intake_date]" 
                           value="{{ old('drug_test.last_intake_date', $connectedResult ? $connectedResult->last_intake_date?->format('Y-m-d') : ($drugTestData['last_intake_date'] ?? '')) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif @error('drug_test.last_intake_date') border-red-500 ring-2 ring-red-200 @enderror" 
                           @if(!$isEdit) readonly @endif />
                    @error('drug_test.last_intake_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Drug Test Results Section -->
        <div class="bg-white rounded-xl p-6 border-2 border-gray-200 shadow-sm">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-flask text-red-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Drug Test Results</h4>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border-2 border-gray-300 bg-white rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-6 py-4 text-left font-semibold text-gray-900 text-sm">Drug/Metabolites</th>
                            <th class="border border-gray-300 px-6 py-4 text-left font-semibold text-gray-900 text-sm">Result</th>
                            <th class="border border-gray-300 px-6 py-4 text-left font-semibold text-gray-900 text-sm">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="border border-gray-300 px-6 py-4 font-medium text-gray-900">METHAMPHETAMINE (Meth)</td>
                            <td class="border border-gray-300 px-6 py-4">
                                <select name="drug_test[methamphetamine_result]" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 bg-white transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif @error('drug_test.methamphetamine_result') border-red-500 ring-2 ring-red-200 @enderror" 
                                        @if(!$isEdit) disabled @else required @endif>
                                    <option value="">Select Result</option>
                                    <option value="Negative" {{ old('drug_test.methamphetamine_result', $connectedResult->methamphetamine_result ?? $drugTestData['methamphetamine_result'] ?? '') == 'Negative' ? 'selected' : '' }}>Negative</option>
                                    <option value="Positive" {{ old('drug_test.methamphetamine_result', $connectedResult->methamphetamine_result ?? $drugTestData['methamphetamine_result'] ?? '') == 'Positive' ? 'selected' : '' }}>Positive</option>
                                </select>
                                @error('drug_test.methamphetamine_result')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="border border-gray-300 px-6 py-4">
                                <input type="text" name="drug_test[methamphetamine_remarks]" 
                                       value="{{ old('drug_test.methamphetamine_remarks', $connectedResult->methamphetamine_remarks ?? $drugTestData['methamphetamine_remarks'] ?? '') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif" 
                                       placeholder="Optional remarks" @if(!$isEdit) readonly @endif />
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="border border-gray-300 px-6 py-4 font-medium text-gray-900">TETRAHYDROCANNABINOL (Marijuana)</td>
                            <td class="border border-gray-300 px-6 py-4">
                                <select name="drug_test[marijuana_result]" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 bg-white transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif @error('drug_test.marijuana_result') border-red-500 ring-2 ring-red-200 @enderror" 
                                        @if(!$isEdit) disabled @else required @endif>
                                    <option value="">Select Result</option>
                                    <option value="Negative" {{ old('drug_test.marijuana_result', $connectedResult->marijuana_result ?? $drugTestData['marijuana_result'] ?? '') == 'Negative' ? 'selected' : '' }}>Negative</option>
                                    <option value="Positive" {{ old('drug_test.marijuana_result', $connectedResult->marijuana_result ?? $drugTestData['marijuana_result'] ?? '') == 'Positive' ? 'selected' : '' }}>Positive</option>
                                </select>
                                @error('drug_test.marijuana_result')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="border border-gray-300 px-6 py-4">
                                <input type="text" name="drug_test[marijuana_remarks]" 
                                       value="{{ old('drug_test.marijuana_remarks', $connectedResult->marijuana_remarks ?? $drugTestData['marijuana_remarks'] ?? '') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg {{ $colors['ring'] }} focus:border-{{ $colors['primary'] }}-500 transition-colors @if(!$isEdit) bg-gray-50 text-gray-700 cursor-not-allowed @endif" 
                                       placeholder="Optional remarks" @if(!$isEdit) readonly @endif />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-signature text-gray-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Signatures</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="text-center">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        Test Conducted by:
                    </label>
                    <div class="border-b-2 border-gray-400 pb-4 mb-2 min-h-[3rem] flex items-end justify-center">
                        <p class="font-semibold text-gray-900 text-lg">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                    </div>
                    <p class="text-xs text-gray-500">Signature over Printed Name of Staff</p>
                </div>
                <div class="text-center">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        Conforme:
                    </label>
                    <div class="border-b-2 border-gray-400 pb-4 mb-2 min-h-[3rem]">
                        <!-- Empty space for patient signature -->
                    </div>
                    <p class="text-xs text-gray-500">Signature over Printed Name of Client</p>
                </div>
            </div>
        </div>
    </div>
</div>
