@extends('layouts.plebo')

@section('title', 'Phlebotomy Dashboard')

@section('page-title', 'Dashboard')

@section('content')
@if(session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center space-x-3">
        <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Pre-Employment Records -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-orange-500">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-md text-orange-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $preEmploymentCount }}</h3>
                    <p class="text-sm text-gray-600">Pre-Employment</p>
                </div>
            </div>
        </div>
        <p class="text-gray-600 text-sm mb-4">Blood collection for pre-employment medical exams</p>
        <a href="#pre-employment-section" class="inline-flex items-center text-orange-600 hover:text-orange-700 text-sm font-semibold transition-colors">
            View Records
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- Annual Physical Patients -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-medical text-emerald-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $patientCount }}</h3>
                    <p class="text-sm text-gray-600">Annual Physical</p>
                </div>
            </div>
        </div>
        <p class="text-gray-600 text-sm mb-4">Blood collection for annual physical examinations</p>
        <a href="#annual-physical-section" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 text-sm font-semibold transition-colors">
            View Patients
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- OPD Walk-ins -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-blue-500">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-walking text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $opdCount }}</h3>
                    <p class="text-sm text-gray-600">OPD Walk-ins</p>
                </div>
            </div>
        </div>
        <p class="text-gray-600 text-sm mb-4">Blood collection for outpatient department visits</p>
        <a href="#opd-section" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-semibold transition-colors">
            View Patients
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- Recent Activities -->
    <div class="content-card rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border-l-4 border-purple-500">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $appointmentCount }}</h3>
                    <p class="text-sm text-gray-600">Recent Activities</p>
                </div>
            </div>
        </div>
        <p class="text-gray-600 text-sm mb-4">Recent blood collection appointments and activities</p>
        <a href="#appointments-section" class="inline-flex items-center text-purple-600 hover:text-purple-700 text-sm font-semibold transition-colors">
            View Activities
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<!-- Pre-Employment Patients Section -->
<div id="pre-employment-section" class="content-card rounded-2xl mb-8 overflow-hidden">
    <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-md text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Pre-Employment Patients</h2>
                    <p class="text-orange-100 text-sm">Blood collection for employment medical exams</p>
                </div>
            </div>
            <div class="bg-white/20 px-3 py-1 rounded-full">
                <span class="text-white font-semibold">{{ $preEmploymentCount }} Records</span>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Details</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Company</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Medical Info</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($preEmployments as $preEmployment)
                    <tr class="hover:bg-orange-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-orange-600 font-bold text-sm">
                                        {{ substr($preEmployment->first_name, 0, 1) }}{{ substr($preEmployment->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $preEmployment->first_name }} {{ $preEmployment->last_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <p><span class="font-medium">Age:</span> {{ $preEmployment->age }}</p>
                                <p><span class="font-medium">Sex:</span> {{ $preEmployment->sex }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="text-gray-900 font-medium">{{ $preEmployment->company_name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <p class="font-medium">
                                    @if($preEmployment->medicalTestCategory)
                                        {{ $preEmployment->medicalTestCategory->name }}
                                    @else
                                        {{ $preEmployment->medical_exam_type ?? 'N/A' }}
                                    @endif
                                </p>
                                <p class="text-gray-500">
                                    @if($preEmployment->medicalTest)
                                        {{ $preEmployment->medicalTest->name }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = match($preEmployment->status) {
                                    'approved' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'fa-check-circle'],
                                    'declined' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'fa-times-circle'],
                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'fa-clock'],
                                    default => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-question-circle']
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['class'] }}">
                                <i class="fas {{ $statusConfig['icon'] }} mr-1"></i>
                                {{ ucfirst($preEmployment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('plebo.medical-checklist.pre-employment', $preEmployment->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors duration-200">
                                <i class="fas fa-vial mr-2"></i>
                                Blood Collection
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-md text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No pre-employment records found</p>
                                <p class="text-gray-400 text-sm">Pre-employment patients will appear here when available</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Annual Physical Patients Section -->
<div id="annual-physical-section" class="content-card rounded-2xl mb-8 overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-medical text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Annual Physical Patients</h2>
                    <p class="text-emerald-100 text-sm">Blood collection for annual physical examinations</p>
                </div>
            </div>
            <div class="bg-white/20 px-3 py-1 rounded-full">
                <span class="text-white font-semibold">{{ $patientCount }} Patients</span>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Details</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Medical Info</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($patients as $patient)
                    <tr class="hover:bg-emerald-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <span class="text-emerald-600 font-bold text-sm">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <p><span class="font-medium">Age:</span> {{ $patient->age }}</p>
                                <p><span class="font-medium">Sex:</span> {{ $patient->sex }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="text-gray-900 font-medium">{{ $patient->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <p class="font-medium">
                                    @if($patient->appointment && $patient->appointment->medicalTestCategory)
                                        {{ $patient->appointment->medicalTestCategory->name }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <p class="text-gray-500">
                                    @if($patient->medicalTests->isNotEmpty())
                                        {{ $patient->medicalTests->pluck('name')->implode(', ') }}
                                    @elseif($patient->appointment && $patient->appointment->selected_tests->isNotEmpty())
                                        {{ $patient->appointment->selected_tests->pluck('name')->implode(', ') }}
                                    @elseif($patient->appointment && $patient->appointment->medicalTest)
                                        {{ $patient->appointment->medicalTest->name }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = match($patient->status) {
                                    'approved' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'fa-check-circle'],
                                    'declined' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'fa-times-circle'],
                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'fa-clock'],
                                    default => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-question-circle']
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['class'] }}">
                                <i class="fas {{ $statusConfig['icon'] }} mr-1"></i>
                                {{ ucfirst($patient->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('plebo.medical-checklist.annual-physical', $patient->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition-colors duration-200">
                                <i class="fas fa-vial mr-2"></i>
                                Blood Collection
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-medical text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No annual physical patients found</p>
                                <p class="text-gray-400 text-sm">Annual physical patients will appear here when available</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- OPD Walk-in Patients Section -->
<div id="opd-section" class="content-card rounded-2xl mb-8 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-walking text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">OPD Walk-in Patients</h2>
                    <p class="text-blue-100 text-sm">Blood collection for outpatient department visits</p>
                </div>
            </div>
            <div class="bg-white/20 px-3 py-1 rounded-full">
                <span class="text-white font-semibold">{{ $opdCount }} Patients</span>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Details</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact Info</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($opdPatients as $patient)
                    <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-sm">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <p><span class="font-medium">Age:</span> {{ $patient->age }}</p>
                                <p><span class="font-medium">Sex:</span> {{ $patient->sex }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="text-gray-900 font-medium">{{ $patient->email }}</p>
                                <p class="text-gray-500">{{ $patient->phone }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $hasExamination = $patient->opdExamination;
                                $hasMedicalChecklist = \App\Models\MedicalChecklist::where('opd_examination_id', optional($patient->opdExamination)->id)->exists();
                                
                                if ($hasExamination && $hasMedicalChecklist) {
                                    $statusConfig = ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'fa-check-circle', 'text' => 'Completed'];
                                } elseif ($hasExamination || $hasMedicalChecklist) {
                                    $statusConfig = ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'fa-clock', 'text' => 'In Progress'];
                                } else {
                                    $statusConfig = ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-hourglass-start', 'text' => 'Pending'];
                                }
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['class'] }}">
                                <i class="fas {{ $statusConfig['icon'] }} mr-1"></i>
                                {{ $statusConfig['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('plebo.medical-checklist.opd', $patient->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors duration-200">
                                <i class="fas fa-vial mr-2"></i>
                                Blood Collection
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-walking text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No OPD patients found</p>
                                <p class="text-gray-400 text-sm">OPD walk-in patients will appear here when available</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
// Smooth scroll to sections when clicking stats cards
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Add highlight effect
                target.classList.add('ring-2', 'ring-orange-500', 'ring-opacity-50');
                setTimeout(() => {
                    target.classList.remove('ring-2', 'ring-orange-500', 'ring-opacity-50');
                }, 2000);
            }
        });
    });
    
    console.log('Plebo dashboard initialized');
});
</script>
@endsection


