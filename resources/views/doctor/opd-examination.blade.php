@extends('layouts.doctor')

@section('title', 'OPD Examination')
@section('page-title', 'OPD Examination')
@section('page-description', 'View and manage OPD medical examination')

@section('content')
<div class="space-y-8">
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-teal-600">
        <div class="px-8 py-6 bg-gradient-to-r from-teal-600 to-teal-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-user-md mr-3"></i>OPD Medical Examination
                    </h1>
                    <p class="text-teal-100">Out-Patient Department medical assessment and evaluation</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-teal-800 bg-opacity-50 rounded-lg px-4 py-2 border border-teal-500">
                        <p class="text-teal-200 text-sm font-medium">Exam ID</p>
                        <p class="text-white text-lg font-bold">#{{ $examination->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Patient Information Section -->
        <div class="px-8 py-6 bg-gradient-to-r from-green-600 to-green-700 border-l-4 border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-user mr-3"></i>Patient Information
                    </h2>
                    <p class="text-green-100 mt-1">Patient details and examination information</p>
                </div>
            </div>
        </div>
        
        <div class="p-8 bg-green-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Patient Name</label>
                    <div class="text-lg font-bold text-blue-800">{{ $examination->name ?? 'N/A' }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Date</label>
                    <div class="text-lg font-bold text-green-800">{{ $examination->date ? $examination->date->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-indigo-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Status</label>
                    <div class="text-lg font-bold text-indigo-800">{{ ucfirst($examination->status ?? 'pending') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500 hover:shadow-md transition-shadow duration-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Doctor</label>
                    <div class="text-sm font-semibold text-yellow-800 truncate">{{ $examination->user->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Examination Details Section -->
        <div class="p-8">
            <!-- Examination Status -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Examination Status</h3>
                    </div>
                </div>
                <div class="p-6 bg-green-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Examination Date</label>
                            <p class="text-gray-900">{{ $examination->date ? $examination->date->format('F j, Y') : 'N/A' }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $examination->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($examination->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($examination->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($examination->status ?? 'pending') }}
                            </span>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Examined By</label>
                            <p class="text-gray-900">{{ $examination->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History Section -->
            @if($examination->illness_history || $examination->accidents_operations || $examination->past_medical_history || $examination->family_history)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
                    <div class="flex items-center">
                        <i class="fas fa-notes-medical text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Medical History</h3>
                    </div>
                </div>
                <div class="p-6 bg-blue-50">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        @if($examination->illness_history)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hospital mr-2 text-green-600"></i>Illness / Hospitalization
                            </label>
                            <div class="text-sm text-gray-900">{{ $examination->illness_history }}</div>
                        </div>
                        @endif
                        @if($examination->accidents_operations)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-injured mr-2 text-orange-600"></i>Accidents / Operations
                            </label>
                            <div class="text-sm text-gray-900">{{ $examination->accidents_operations }}</div>
                        </div>
                        @endif
                        @if($examination->past_medical_history)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Past Medical History
                            </label>
                            <div class="text-sm text-gray-900">{{ $examination->past_medical_history }}</div>
                        </div>
                        @endif
                    </div>
                    
                    @if($examination->family_history && is_array($examination->family_history) && count($examination->family_history) > 0)
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            <i class="fas fa-users mr-2 text-purple-600"></i>Family Medical History
                        </label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($examination->family_history as $condition)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                    {{ str_replace('_', ' ', ucwords($condition)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Personal Habits Section -->
            @if($examination->personal_habits && is_array($examination->personal_habits) && count($examination->personal_habits) > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-orange-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600">
                    <div class="flex items-center">
                        <i class="fas fa-user-check text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Personal Habits</h3>
                    </div>
                </div>
                <div class="p-6 bg-orange-50">
                    <div class="flex flex-wrap gap-3">
                        @foreach($examination->personal_habits as $habit)
                            @php
                                $habitConfig = [
                                    'alcohol' => ['icon' => 'fas fa-wine-bottle', 'color' => 'red'],
                                    'cigarettes' => ['icon' => 'fas fa-smoking', 'color' => 'orange'],
                                    'coffee_tea' => ['icon' => 'fas fa-coffee', 'color' => 'yellow']
                                ];
                                $config = $habitConfig[$habit] ?? ['icon' => 'fas fa-circle', 'color' => 'gray'];
                            @endphp
                            <div class="flex items-center bg-white rounded-lg px-4 py-2 border border-gray-200">
                                <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">{{ str_replace('_', ' ', ucwords($habit)) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Physical Examination Section -->
            @if($examination->physical_exam)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-red-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600">
                    <div class="flex items-center">
                        <i class="fas fa-stethoscope text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Physical Examination</h3>
                    </div>
                </div>
                <div class="p-6 bg-red-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @php 
                            $phys = $examination->physical_exam ?? [];
                            $vitals = [
                                'temp' => ['label' => 'Temperature', 'icon' => 'fas fa-thermometer-half', 'unit' => '°C', 'color' => 'red'],
                                'height' => ['label' => 'Height', 'icon' => 'fas fa-ruler-vertical', 'unit' => 'cm', 'color' => 'blue'],
                                'heart_rate' => ['label' => 'Heart Rate', 'icon' => 'fas fa-heartbeat', 'unit' => 'bpm', 'color' => 'pink'],
                                'weight' => ['label' => 'Weight', 'icon' => 'fas fa-weight', 'unit' => 'kg', 'color' => 'green']
                            ];
                        @endphp
                        @foreach($vitals as $key => $vital)
                            @if(isset($phys[$key]) && $phys[$key])
                            <div class="bg-white rounded-lg p-4 border-l-4 border-{{ $vital['color'] }}-500">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="{{ $vital['icon'] }} text-{{ $vital['color'] }}-600 mr-2"></i>{{ $vital['label'] }}
                                </label>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $phys[$key] }}
                                    <span class="text-sm text-gray-500 font-normal">{{ $vital['unit'] }}</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Visual Assessment Section -->
            @if($examination->visual || $examination->ishihara_test)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-indigo-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <div class="flex items-center">
                        <i class="fas fa-eye text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Visual Assessment</h3>
                    </div>
                </div>
                <div class="p-6 bg-indigo-50">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @if($examination->visual)
                        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-glasses mr-2 text-blue-600"></i>Visual Acuity
                            </label>
                            <div class="text-sm text-gray-900">{{ $examination->visual }}</div>
                        </div>
                        @endif
                        @if($examination->ishihara_test)
                        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-palette mr-2 text-green-600"></i>Ishihara Test
                            </label>
                            <div class="text-sm text-gray-900">{{ $examination->ishihara_test }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Skin Marks Section -->
            @if($examination->skin_marks)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-pink-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-pink-500 to-pink-600">
                    <div class="flex items-center">
                        <i class="fas fa-search text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Skin Identification Marks</h3>
                    </div>
                </div>
                <div class="p-6 bg-pink-50">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="text-sm text-gray-900">{{ $examination->skin_marks }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Physical Findings Section -->
            @if($examination->physical_findings)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-cyan-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-cyan-500 to-cyan-600">
                    <div class="flex items-center">
                        <i class="fas fa-user-md text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Physical Examination Findings</h3>
                    </div>
                </div>
                <div class="p-6 bg-cyan-50">
                    @if(is_array($examination->physical_findings) || is_object($examination->physical_findings))
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Examination Area</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Result</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">Findings</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($examination->physical_findings as $area => $finding)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                {{ $area }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if(isset($finding['result']))
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $finding['result'] === 'Normal' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                                        <i class="fas {{ $finding['result'] === 'Normal' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-1 text-xs"></i>
                                                        {{ $finding['result'] }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500 text-xs">Not specified</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $finding['findings'] ?? 'No findings recorded' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-sm text-gray-900">{!! nl2br(e($examination->physical_findings)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Laboratory Findings Section -->
            @if($examination->lab_findings)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-lime-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-lime-500 to-lime-600">
                    <div class="flex items-center">
                        <i class="fas fa-microscope text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Laboratory Test Results</h3>
                    </div>
                </div>
                <div class="p-6 bg-lime-50">
                    @if(is_array($examination->lab_findings) || is_object($examination->lab_findings))
                        <div class="space-y-4">
                            @foreach($examination->lab_findings as $test => $result)
                                @if(is_array($result) && (isset($result['result']) || isset($result['findings'])))
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                        <div class="flex items-center">
                                            <i class="fas fa-flask text-lime-600 mr-3"></i>
                                            <span class="font-semibold text-gray-700">{{ ucwords(str_replace('_', ' ', $test)) }}</span>
                                        </div>
                                        <div>
                                            @if(isset($result['result']))
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $result['result'] === 'Normal' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                                    <i class="fas {{ $result['result'] === 'Normal' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-1 text-xs"></i>
                                                    {{ $result['result'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            @if(isset($result['findings']))
                                                <div class="text-sm text-gray-700">{{ $result['findings'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-sm text-gray-900">{!! nl2br(e($examination->lab_findings)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- ECG Section -->
            @if($examination->ecg)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-red-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600">
                    <div class="flex items-center">
                        <i class="fas fa-heartbeat text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">Electrocardiogram (ECG)</h3>
                    </div>
                </div>
                <div class="p-6 bg-red-50">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-chart-line text-red-600 mr-2"></i>ECG Results
                        </label>
                        <div class="text-sm text-gray-900">{!! nl2br(e($examination->ecg)) !!}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- General Findings Section -->
            @if($examination->findings)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-purple-500 mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check text-white text-xl mr-3"></i>
                        <h3 class="text-lg font-bold text-white">General Findings & Assessment</h3>
                    </div>
                </div>
                <div class="p-6 bg-purple-50">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-notes-medical text-purple-600 mr-2"></i>Clinical Findings
                        </label>
                        <div class="text-sm text-gray-900 prose max-w-none">
                            @if(is_string($examination->findings))
                                {!! nl2br(e($examination->findings)) !!}
                            @elseif(is_array($examination->findings) || is_object($examination->findings))
                                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($examination->findings, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $examination->findings }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 sm:space-x-4 mt-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('doctor.opd') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="{{ route('doctor.opd.submit', $examination->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>Submit to Admin
                        </button>
                    </form>
                    <a href="{{ route('doctor.opd.edit', $examination->id) }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit Examination
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
