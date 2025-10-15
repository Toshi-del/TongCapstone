@extends('layouts.nurse')

@section('title', 'Annual Physical Examinations - RSS Citi Health Services')
@section('page-title', 'Annual Physical Examinations')
@section('page-description', 'Yearly health checkups and comprehensive medical assessments')

@section('content')
<div class="space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif


    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Annual Physical Examinations</h2>
                        <p class="text-purple-100 text-sm">Yearly comprehensive health assessments and medical checkups</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('nurse.annual-physical') }}" class="flex items-center space-x-3">
                        <!-- Preserve current filter -->
                        @if(request('exam_status'))
                            <input type="hidden" name="exam_status" value="{{ request('exam_status') }}">
                        @endif
                        
                        <!-- Search Bar -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-white/60 text-sm"></i>
                            </div>
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="glass-morphism pl-12 pr-4 py-2 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/20 transition-all duration-200 w-72 text-sm border border-white/20" 
                                   placeholder="Search by name, email...">
                        </div>
                        
                        <!-- Search Button -->
                        <button type="submit" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 border border-white/20 backdrop-blur-sm">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </form>
                    
                    <div class="text-right">
                        <div class="text-white/90 text-sm">Total Patients</div>
                        <div class="text-white font-bold text-2xl">{{ $patients->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Status Tabs -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        @php
            $currentTab = request('exam_status', 'needs_attention');
        @endphp
        
        <!-- Tab Navigation -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-1">
                    <a href="{{ request()->fullUrlWithQuery(['exam_status' => 'needs_attention']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'needs_attention' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Needs Attention
                        @php
                            $needsAttentionCount = \App\Models\Patient::where('status', 'approved')
                                ->whereHas('appointment', function($q) {
                                    $q->where('status', 'approved');
                                })
                                ->where(function($mainQuery) {
                                    $mainQuery->whereDoesntHave('annualPhysicalExamination')
                                    ->orWhereHas('annualPhysicalExamination', function($q) {
                                        $q->where(function($subQuery) {
                                            $subQuery->where(function($sq) {
                                                $sq->whereNull('physical_exam')->orWhere('physical_exam', '[]')->orWhere('physical_exam', '{}');
                                            })
                                            ->where(function($sq) {
                                                $sq->whereNull('lab_report')->orWhere('lab_report', '[]')->orWhere('lab_report', '{}');
                                            })
                                            ->where(function($sq) {
                                                $sq->whereNull('findings')->orWhere('findings', '');
                                            })
                                            ->where(function($sq) {
                                                $sq->whereNull('illness_history')->orWhere('illness_history', '');
                                            });
                                        });
                                    });
                                })
                                ->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'needs_attention' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $needsAttentionCount }}
                        </span>
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['exam_status' => 'exam_completed']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'exam_completed' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        <i class="fas fa-check-circle mr-2"></i>
                        Completed
                        @php
                            $completedCount = \App\Models\Patient::where('status', 'approved')
                                ->whereHas('appointment', function($q) {
                                    $q->where('status', 'approved');
                                })
                                ->whereHas('annualPhysicalExamination', function($q) {
                                    $q->where(function($subQuery) {
                                        $subQuery->where(function($sq) {
                                            $sq->whereNotNull('physical_exam')->where('physical_exam', '!=', '[]')->where('physical_exam', '!=', '{}');
                                        })
                                        ->orWhere(function($sq) {
                                            $sq->whereNotNull('lab_report')->where('lab_report', '!=', '[]')->where('lab_report', '!=', '{}');
                                        })
                                        ->orWhere(function($sq) {
                                            $sq->whereNotNull('findings')->where('findings', '!=', '');
                                        })
                                        ->orWhere(function($sq) {
                                            $sq->whereNotNull('illness_history')->where('illness_history', '!=', '');
                                        });
                                    });
                                })
                                ->count();
                        @endphp
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'exam_completed' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $completedCount }}
                        </span>
                    </a>
                </div>
                
                <a href="{{ route('nurse.annual-physical') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                    <i class="fas fa-times mr-1"></i>Clear All Filters
                </a>
            </div>
        </div>

        <!-- Additional Filters -->
        <div class="p-6">
            <form method="GET" action="{{ route('nurse.annual-physical') }}" class="space-y-6">
                <!-- Preserve current tab -->
                <input type="hidden" name="exam_status" value="{{ $currentTab }}">
                
                <!-- Preserve search query -->
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                <!-- Filter Row: Gender only -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Gender Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                            <option value="">All Genders</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Placeholder -->
                    <div></div>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ request()->fullUrlWithQuery(['gender' => null, 'search' => null]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Reset Filters
                        </a>
                    </div>
                    
                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['gender', 'search']))
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Active filters:</span>
                            @if(request('search'))
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-emerald-600 hover:text-emerald-800">×</a>
                                </span>
                            @endif
                            @if(request('gender'))
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                    Gender: {{ ucfirst(request('gender')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['gender' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">×</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $totalPatients = $patients->count();
            $completedCount = 0;
            $pendingCount = 0;
            $scheduledCount = $totalPatients;
            
            foreach($patients as $patient) {
                $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::where('patient_id', $patient->id)->first();
                $medicalChecklist = \App\Models\MedicalChecklist::where('patient_id', $patient->id)
                    ->whereIn('examination_type', ['annual-physical', 'annual_physical'])
                    ->first();
                
                // Check if examination is actually completed (has meaningful lab results)
                $isExamCompleted = $annualPhysicalExam && 
                    $annualPhysicalExam->lab_report && 
                    !empty($annualPhysicalExam->lab_report) && 
                    $annualPhysicalExam->lab_report != '[]' && 
                    $annualPhysicalExam->lab_report != '{}' &&
                    (is_array($annualPhysicalExam->lab_report) ? count(array_filter($annualPhysicalExam->lab_report)) > 0 : true);
                    
                if($isExamCompleted) {
                    $completedCount++;
                } else {
                    $pendingCount++;
                }
            }
        @endphp
        
        <div class="content-card rounded-xl p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Patients</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalPatients }}</p>
                    <p class="text-xs text-purple-600 mt-1">Scheduled checkups</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="content-card rounded-xl p-6 border-l-4 border-emerald-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $completedCount }}</p>
                    <p class="text-xs text-emerald-600 mt-1">Examinations done</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="content-card rounded-xl p-6 border-l-4 border-amber-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    <p class="text-xs text-amber-600 mt-1">Awaiting examination</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="content-card rounded-xl p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Completion Rate</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalPatients > 0 ? round(($completedCount / $totalPatients) * 100) : 0 }}%</p>
                    <p class="text-xs text-blue-600 mt-1">Overall progress</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Annual Physical Patients Table -->
    <div class="content-card rounded-xl shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-table text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Annual Physical Patients</h3>
                        <p class="text-purple-100 text-sm">Comprehensive yearly health examination records</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-0">
            @if($patients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Demographics</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contact Info</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Appointment</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($patients as $patient)
                                @php
                                    $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::where('patient_id', $patient->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                    
                                    // Find checklist by patient_id only
                                    $medicalChecklist = \App\Models\MedicalChecklist::where('patient_id', $patient->id)
                                        ->whereIn('examination_type', ['annual-physical', 'annual_physical'])
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                    
                                    // Check if examination form has been created and has data
                                    $isExamCompleted = $annualPhysicalExam && (
                                        // Has physical exam data
                                        (!empty($annualPhysicalExam->physical_exam) && $annualPhysicalExam->physical_exam != '[]' && $annualPhysicalExam->physical_exam != '{}') ||
                                        // Has lab report data
                                        (!empty($annualPhysicalExam->lab_report) && $annualPhysicalExam->lab_report != '[]' && $annualPhysicalExam->lab_report != '{}') ||
                                        // Has findings
                                        !empty($annualPhysicalExam->findings) ||
                                        // Has illness history
                                        !empty($annualPhysicalExam->illness_history)
                                    );
                                    
                                    // Status is "Completed" when examination form has been filled
                                    $isCompleted = $isExamCompleted;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                                <span class="text-purple-600 font-semibold text-sm">
                                                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                                <p class="text-xs text-gray-500">Patient ID: #{{ $patient->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-medium">{{ $patient->age }} years old</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($patient->sex) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-medium">{{ $patient->email }}</p>
                                            <p class="text-xs text-gray-500">{{ $patient->phone ?? 'No phone' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-medium">Appointment #{{ $patient->appointment_id }}</p>
                                            <p class="text-xs text-gray-500">Annual physical checkup</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($isCompleted)
                                            <span class="px-3 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i>Completed
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <!-- Edit Examination (if exists) -->
                                            @if($annualPhysicalExam)
                                                <a href="{{ route('nurse.annual-physical.edit', $annualPhysicalExam->id) }}" 
                                                   class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors" 
                                                   title="Edit Examination">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            <!-- Create Examination (if checklist completed) -->
                                            @if(!$annualPhysicalExam)
                                                @if($medicalChecklist && !empty($medicalChecklist->physical_exam_done_by))
                                                    <a href="{{ route('nurse.annual-physical.create', ['patient_id' => $patient->id]) }}" 
                                                       class="p-2 text-emerald-600 hover:text-emerald-900 hover:bg-emerald-50 rounded-lg transition-colors" 
                                                       title="Create Examination">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                @else
                                                    <button class="p-2 text-gray-400 cursor-not-allowed rounded-lg" 
                                                            title="Complete medical checklist first" 
                                                            disabled>
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            <!-- Medical Checklist -->
                                            <a href="{{ route('nurse.medical-checklist.annual-physical', $patient->id) }}" 
                                               class="p-2 text-purple-600 hover:text-purple-900 hover:bg-purple-50 rounded-lg transition-colors" 
                                               title="Medical Checklist">
                                                <i class="fas fa-clipboard-list"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Annual Physical Patients</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">There are no patients scheduled for annual physical examinations. New appointments will appear here once scheduled.</p>
                    <button class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        <i class="fas fa-calendar-plus mr-2"></i>Schedule Appointment
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add smooth animations to content cards
    document.addEventListener('DOMContentLoaded', function() {
        const contentCards = document.querySelectorAll('.content-card');
        contentCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });

        // Auto-hide alert messages after 5 seconds
        const alerts = document.querySelectorAll('[class*="bg-emerald-50"], [class*="bg-red-50"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });

        // Add confirmation for send to doctor actions
        const sendForms = document.querySelectorAll('form[action*="send-to-doctor"]');
        sendForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to send this examination to the doctor?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
    }
</style>
@endpush
@endsection
