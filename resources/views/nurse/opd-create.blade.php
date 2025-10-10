@extends('layouts.nurse')

@section('title', 'Create OPD Examination - RSS Citi Health Services')
@section('page-title', 'Create OPD Examination')
@section('page-description', 'New outpatient department consultation and medical examination')

@section('content')
<div class="space-y-8">
    <!-- Validation Errors -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold mb-2">Please complete all required fields:</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-circle text-xs text-red-500"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Create OPD Examination</h2>
                        <p class="text-amber-100 text-sm">New outpatient consultation and medical assessment</p>
                    </div>
                </div>
                <a href="{{ route('nurse.opd') }}" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors backdrop-blur-sm border border-white/20 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to OPD List
                </a>
            </div>
        </div>
    </div>

    <!-- Patient Information Card -->
    <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-amber-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Patient Information</h3>
                <p class="text-gray-600 text-sm">Walk-in patient details for examination</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Patient Name</label>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                        <span class="text-amber-600 font-semibold text-sm">
                            {{ substr($opdPatient->fname, 0, 1) }}{{ substr($opdPatient->lname, 0, 1) }}
                        </span>
                    </div>
                    <div class="text-lg font-semibold text-gray-900">{{ $opdPatient->fname }} {{ $opdPatient->lname }}</div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Age</label>
                <div class="text-lg font-semibold text-gray-900">{{ $opdPatient->age }} years old</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Email</label>
                <div class="text-sm font-medium text-gray-900">{{ $opdPatient->email }}</div>
            </div>
        </div>
    </div>

    <!-- OPD Examination Form -->
    <form action="{{ route('nurse.opd.store') }}" method="POST" class="space-y-8">
        @csrf
        <input type="hidden" name="user_id" value="{{ $opdPatient->id }}">
        
        <!-- Drug Test Selection Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pills text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Drug Test Requirement</h3>
                    <p class="text-gray-600 text-sm">Specify if drug testing is required for this OPD examination</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center space-x-4">
                    <input type="checkbox" id="requires_drug_test" name="requires_drug_test" value="1" 
                           class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500 focus:ring-2" 
                           onchange="toggleDrugTestForm()" {{ old('requires_drug_test') ? 'checked' : '' }}>
                    <label for="requires_drug_test" class="text-sm font-semibold text-gray-700">
                        This OPD examination requires drug testing
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2 ml-9">Check this box if the patient needs to undergo drug testing as part of their OPD examination</p>
            </div>
        </div>
        
        <!-- Medical History Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Medical History</h3>
                    <p class="text-gray-600 text-sm">Patient's previous medical conditions and history</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-2">
                    <label for="illness_history" class="block text-sm font-semibold text-gray-700">Illness History</label>
                    <textarea name="illness_history" id="illness_history" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors" 
                              placeholder="Enter patient's illness history, chronic conditions, or recurring health issues...">{{ old('illness_history') }}</textarea>
                    @error('illness_history')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="accidents_operations" class="block text-sm font-semibold text-gray-700">Accidents/Operations</label>
                    <textarea name="accidents_operations" id="accidents_operations" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors" 
                              placeholder="Enter any accidents, surgeries, or operations the patient has had...">{{ old('accidents_operations') }}</textarea>
                    @error('accidents_operations')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="past_medical_history" class="block text-sm font-semibold text-gray-700">Past Medical History</label>
                    <textarea name="past_medical_history" id="past_medical_history" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors" 
                              placeholder="Enter comprehensive past medical history, family history, and relevant medical background...">{{ old('past_medical_history') }}</textarea>
                    @error('past_medical_history')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Physical Examination Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Physical Examination <span class="text-red-500">*</span></h3>
                    <p class="text-gray-600 text-sm">Vital signs and basic physical measurements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="space-y-2">
                    <label for="temp" class="block text-sm font-semibold text-gray-700">
                        Temperature <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[temp]" id="temp" value="{{ old('physical_exam.temp') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('physical_exam.temp') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 36.5°C" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-thermometer-half text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.temp')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="height" class="block text-sm font-semibold text-gray-700">
                        Height <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[height]" id="height" value="{{ old('physical_exam.height') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('physical_exam.height') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 170 cm" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-ruler-vertical text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.height')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="weight" class="block text-sm font-semibold text-gray-700">
                        Weight <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[weight]" id="weight" value="{{ old('physical_exam.weight') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('physical_exam.weight') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 70 kg" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-weight text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.weight')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="heart_rate" class="block text-sm font-semibold text-gray-700">
                        Heart Rate <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[heart_rate]" id="heart_rate" value="{{ old('physical_exam.heart_rate') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('physical_exam.heart_rate') border-red-500 ring-2 ring-red-200 @enderror" 
                               placeholder="e.g., 72 bpm" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-heartbeat text-gray-400"></i>
                        </div>
                    </div>
                    @error('physical_exam.heart_rate')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Drug Test Form Card -->
        <div id="drug_test_form" class="content-card rounded-xl p-8 shadow-lg border border-gray-200" style="display: none;">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pills text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Drug Test Result (DT Form 2)</h3>
                    <p class="text-gray-600 text-sm">Urine drug screening examination form</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Patient Information Section -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Patient Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="drug_test[patient_name]" 
                                   value="{{ old('drug_test.patient_name', $opdPatient->fname . ' ' . $opdPatient->lname) }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.patient_name') border-red-500 ring-2 ring-red-200 @enderror" 
                                   placeholder="Full name" />
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
                            <input type="text" name="drug_test[address]" value="{{ old('drug_test.address') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.address') border-red-500 ring-2 ring-red-200 @enderror" 
                                   placeholder="Patient address" />
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
                                   value="{{ old('drug_test.age', $opdPatient->age ?? '') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.age') border-red-500 ring-2 ring-red-200 @enderror" 
                                   placeholder="Age" />
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
                            <select name="drug_test[gender]" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.gender') border-red-500 ring-2 ring-red-200 @enderror">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('drug_test.gender', ucfirst($opdPatient->sex ?? '')) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('drug_test.gender', ucfirst($opdPatient->sex ?? '')) == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
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
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Examination Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Date and Time of Examination <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="drug_test[examination_datetime]" 
                                   value="{{ old('drug_test.examination_datetime', now()->format('Y-m-d\TH:i')) }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.examination_datetime') border-red-500 ring-2 ring-red-200 @enderror" />
                            @error('drug_test.examination_datetime')
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
                                   value="{{ old('drug_test.last_intake_date') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.last_intake_date') border-red-500 ring-2 ring-red-200 @enderror" />
                            @error('drug_test.last_intake_date')
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
                                   value="{{ old('drug_test.test_method', 'URINE TEST KIT') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors @error('drug_test.test_method') border-red-500 ring-2 ring-red-200 @enderror" />
                            @error('drug_test.test_method')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Drug Test Results Section -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Drug Test Results</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-900">Drug/Metabolites</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-900">Result</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-900">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-3 font-medium">METHAMPHETAMINE (Shabu)</td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <select name="drug_test[methamphetamine_result]" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('drug_test.methamphetamine_result') border-red-500 ring-2 ring-red-200 @enderror">
                                            <option value="">Select Result</option>
                                            <option value="Negative" {{ old('drug_test.methamphetamine_result') == 'Negative' ? 'selected' : '' }}>Negative</option>
                                            <option value="Positive" {{ old('drug_test.methamphetamine_result') == 'Positive' ? 'selected' : '' }}>Positive</option>
                                        </select>
                                        @error('drug_test.methamphetamine_result')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" name="drug_test[methamphetamine_remarks]" 
                                               value="{{ old('drug_test.methamphetamine_remarks') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                                               placeholder="Optional remarks" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-3 font-medium">TETRAHYDROCANNABINOL (Marijuana)</td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <select name="drug_test[marijuana_result]" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('drug_test.marijuana_result') border-red-500 ring-2 ring-red-200 @enderror">
                                            <option value="">Select Result</option>
                                            <option value="Negative" {{ old('drug_test.marijuana_result') == 'Negative' ? 'selected' : '' }}>Negative</option>
                                            <option value="Positive" {{ old('drug_test.marijuana_result') == 'Positive' ? 'selected' : '' }}>Positive</option>
                                        </select>
                                        @error('drug_test.marijuana_result')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <input type="text" name="drug_test[marijuana_remarks]" 
                                               value="{{ old('drug_test.marijuana_remarks') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                                               placeholder="Optional remarks" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Signatures Section -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Signatures</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Test Conducted by:
                            </label>
                            <div class="border-b-2 border-gray-400 pb-2 mb-2">
                                <p class="text-center font-medium">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                            </div>
                            <p class="text-xs text-gray-500 text-center">Signature over Printed Name of Staff</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Conforme:
                            </label>
                            <div class="border-b-2 border-gray-400 pb-2 mb-2 h-8">
                                <!-- Empty space for patient signature -->
                            </div>
                            <p class="text-xs text-gray-500 text-center">Signature over Printed Name of Client</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                <!-- Clinical Findings Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Clinical Findings <span class="text-red-500">*</span></h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="skin_marks" class="block text-sm font-medium text-gray-700 mb-2">Skin Marks/Tattoos <span class="text-red-500">*</span></label>
                            <textarea name="skin_marks" id="skin_marks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe any skin marks, tattoos, or scars..." required>{{ old('skin_marks') }}</textarea>
                            @error('skin_marks')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="visual" class="block text-sm font-medium text-gray-700 mb-2">Visual Acuity <span class="text-red-500">*</span></label>
                            <textarea name="visual" id="visual" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter visual acuity results..." required>{{ old('visual') }}</textarea>
                            @error('visual')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ishihara_test" class="block text-sm font-medium text-gray-700 mb-2">Ishihara Test <span class="text-red-500">*</span></label>
                            <textarea name="ishihara_test" id="ishihara_test" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Ishihara test results..." required>{{ old('ishihara_test') }}</textarea>
                            @error('ishihara_test')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="final_findings" class="block text-sm font-medium text-gray-700 mb-2">General Findings <span class="text-red-500">*</span></label>
                            <textarea name="final_findings" id="final_findings" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter general findings..." required>{{ old('final_findings') }}</textarea>
                            @error('final_findings')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('nurse.opd') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Examination
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
    // Define function globally to ensure it's available immediately
    window.toggleDrugTestForm = function() {
        const checkbox = document.getElementById('requires_drug_test');
        const drugTestForm = document.getElementById('drug_test_form');
        
        if (!checkbox || !drugTestForm) {
            console.log('Required elements not found for drug test toggle');
            return;
        }
        
        const drugTestInputs = drugTestForm.querySelectorAll('input[required], select[required]');
        
        if (checkbox.checked) {
            drugTestForm.style.display = 'block';
            // Add required attribute to drug test fields when form is shown
            drugTestInputs.forEach(input => {
                if (input.name.includes('drug_test[') && 
                    (input.name.includes('patient_name') || 
                     input.name.includes('address') || 
                     input.name.includes('age') || 
                     input.name.includes('gender') || 
                     input.name.includes('examination_datetime') || 
                     input.name.includes('test_method') || 
                     input.name.includes('methamphetamine_result') || 
                     input.name.includes('marijuana_result'))) {
                    input.setAttribute('required', 'required');
                }
            });
        } else {
            drugTestForm.style.display = 'none';
            // Remove required attribute when form is hidden
            drugTestInputs.forEach(input => {
                input.removeAttribute('required');
            });
        }
    };

    // Check if drug test was previously selected (for form validation errors)
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('requires_drug_test');
        if (checkbox && checkbox.checked) {
            toggleDrugTestForm();
        }
    });
</script>
@endsection
