@extends('layouts.admin')

@section('title', 'Notifications - RSS Citi Health Services')
@section('page-title', 'Notifications')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-bell text-blue-600 mr-3"></i>
                    System Notifications
                </h2>
                <p class="text-gray-600 mt-1">Monitor all medical workflow activities across departments</p>
            </div>
            <div class="flex items-center space-x-3">
                <button id="refresh-notifications" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow-sm">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                <button id="mark-all-read-btn" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow-sm">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark All Read
                </button>
            </div>
        </div>
    </div>

    <!-- Modern Tab Navigation -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Tab Pills Container - Organized in Two Rows -->
        <div class="p-6 pb-0">
            <div class="space-y-3 mb-6">
                <!-- Row 1: Primary & System Tabs -->
                <div class="flex flex-wrap gap-3 justify-center">
                    <button class="notification-tab-pill {{ request()->get('status') === 'unread' ? '' : (request()->get('type') ? '' : 'active') }}" data-tab="all">
                        <i class="fas fa-list-ul"></i>
                        All Notifications
                        <span class="count-badge bg-gray-500">{{ $counts['all'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ request()->get('status') === 'unread' ? 'active' : '' }}" data-tab="unread">
                        <i class="fas fa-envelope"></i>
                        Unread
                        <span class="count-badge bg-red-500">{{ $counts['unread'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('appointment_created', explode(',', request()->get('type', ''))) || in_array('pre_employment_created', explode(',', request()->get('type', ''))) || in_array('patient_registered', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="company">
                        <i class="fas fa-building"></i>
                        Company Actions
                        <span class="count-badge bg-blue-500">{{ $counts['company'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('checklist_completed', explode(',', request()->get('type', ''))) || in_array('annual_physical_created', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="nurse">
                        <i class="fas fa-user-nurse"></i>
                        Nurse/Medtech
                        <span class="count-badge bg-emerald-500">{{ $counts['nurse'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('pathologist_report_submitted', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="pathologist">
                        <i class="fas fa-microscope"></i>
                        Pathologist
                        <span class="count-badge bg-purple-500">{{ $counts['pathologist'] }}</span>
                    </button>
                </div>
                
                <!-- Row 2: Medical Specialists -->
                <div class="flex flex-wrap gap-3 justify-center">
                    <button class="notification-tab-pill {{ in_array('xray_completed', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="radtech">
                        <i class="fas fa-x-ray"></i>
                        Radtech
                        <span class="count-badge bg-indigo-500">{{ $counts['radtech'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('xray_interpreted', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="radiologist">
                        <i class="fas fa-search"></i>
                        Radiologist
                        <span class="count-badge bg-cyan-500">{{ $counts['radiologist'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('ecg_completed', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="ecgtech">
                        <i class="fas fa-heartbeat"></i>
                        ECG Tech
                        <span class="count-badge bg-orange-500">{{ $counts['ecgtech'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('specimen_collected', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="plebo">
                        <i class="fas fa-vial"></i>
                        Phlebotomist
                        <span class="count-badge bg-red-500">{{ $counts['plebo'] }}</span>
                    </button>
                    <button class="notification-tab-pill {{ in_array('examination_updated', explode(',', request()->get('type', ''))) ? 'active' : '' }}" data-tab="doctor">
                        <i class="fas fa-user-md"></i>
                        Doctor
                        <span class="count-badge bg-violet-500">{{ $counts['doctor'] }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-flag mr-1 text-gray-500"></i>
                        Priority:
                    </label>
                    <select name="priority" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                        <option value="">All Priorities</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🔴 High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                    </select>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-calendar mr-1 text-gray-500"></i>
                        Date Range:
                    </label>
                    <select name="date_range" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-sm font-semibold transition-all duration-300 shadow-sm">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
                
                @if(request()->hasAny(['priority', 'date_range', 'status', 'type']))
                    <a href="{{ route('admin.notifications') }}" class="text-gray-600 hover:text-gray-800 text-sm font-semibold flex items-center">
                        <i class="fas fa-times mr-1"></i>
                        Clear Filters
                    </a>
                @endif
            </form>
        </div>

        <!-- Enhanced Notifications List -->
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="p-6 hover:bg-gray-50 transition-all duration-300 {{ !$notification->is_read ? 'bg-blue-50/50 border-l-4 border-l-blue-500' : '' }}">
                    <div class="flex items-start space-x-4">
                        <!-- Enhanced Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm {{ $notification->priority_color === 'red' ? 'bg-red-100 text-red-600 border border-red-200' : ($notification->priority_color === 'yellow' ? 'bg-yellow-100 text-yellow-600 border border-yellow-200' : 'bg-green-100 text-green-600 border border-green-200') }}">
                                <i class="fas {{ $notification->type_icon }} text-lg"></i>
                            </div>
                        </div>
                        
                        <!-- Enhanced Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                                        @if(!$notification->is_read)
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @endif
                                    </div>
                                    <!-- Structured Notification Content -->
                                    <div class="mb-3">
                                        @php
                                            // Parse notification message for better display
                                            $message = $notification->message;
                                            $structuredData = [];
                                            
                                            // Check for different notification types and extract structured data
                                            if (str_contains($message, 'pre-employment record(s)')) {
                                                // Pre-employment notification
                                                preg_match("/Company '([^']+)' has created (\d+) new pre-employment record\(s\)\./", $message, $matches);
                                                if ($matches) {
                                                    $structuredData['type'] = 'pre_employment';
                                                    $structuredData['company'] = $matches[1];
                                                    $structuredData['count'] = $matches[2];
                                                }
                                                
                                                // Extract tests - improved pattern to handle periods in test names
                                                if (preg_match("/Tests: (.+?)(?:\. Total value:|$)/", $message, $testMatches)) {
                                                    $structuredData['tests'] = trim($testMatches[1]);
                                                }
                                                
                                                // Extract total value
                                                if (preg_match("/Total value: ([^.]+)/", $message, $valueMatches)) {
                                                    $structuredData['total_value'] = $valueMatches[1];
                                                }
                                            } elseif (str_contains($message, 'appointment') && str_contains($message, 'scheduled')) {
                                                // Appointment scheduled notification
                                                $structuredData['type'] = 'appointment_scheduled';
                                                if (preg_match("/Patient '([^']+)'/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                                if (preg_match("/scheduled for (.+?) on (.+?) at (.+?)\./", $message, $appointmentMatches)) {
                                                    $structuredData['service'] = $appointmentMatches[1];
                                                    $structuredData['date'] = $appointmentMatches[2];
                                                    $structuredData['time'] = $appointmentMatches[3];
                                                }
                                            } elseif (str_contains($message, 'specimen collected')) {
                                                // Specimen collection notification
                                                $structuredData['type'] = 'specimen';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                                if (preg_match("/Tests: (.+)/", $message, $testMatches)) {
                                                    $structuredData['tests'] = $testMatches[1];
                                                }
                                            } elseif (str_contains($message, 'X-ray') && str_contains($message, 'completed')) {
                                                // X-ray completed notification
                                                $structuredData['type'] = 'xray_completed';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                                if (preg_match("/Type: (.+)/", $message, $typeMatches)) {
                                                    $structuredData['xray_type'] = $typeMatches[1];
                                                }
                                            } elseif (str_contains($message, 'X-ray') && str_contains($message, 'interpreted')) {
                                                // X-ray interpreted notification
                                                $structuredData['type'] = 'xray_interpreted';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                            } elseif (str_contains($message, 'ECG') && str_contains($message, 'completed')) {
                                                // ECG completed notification
                                                $structuredData['type'] = 'ecg_completed';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                            } elseif (str_contains($message, 'pathologist') && str_contains($message, 'report')) {
                                                // Pathologist report notification
                                                $structuredData['type'] = 'pathologist_report';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                                if (preg_match("/Tests: (.+)/", $message, $testMatches)) {
                                                    $structuredData['tests'] = $testMatches[1];
                                                }
                                            } elseif (str_contains($message, 'checklist completed')) {
                                                // Checklist completed notification
                                                $structuredData['type'] = 'checklist_completed';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                            } elseif (str_contains($message, 'examination') && str_contains($message, 'updated')) {
                                                // Doctor examination notification
                                                $structuredData['type'] = 'examination_updated';
                                                if (preg_match("/Patient: ([^,]+)/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                            } elseif (str_contains($message, 'patient registered')) {
                                                // Patient registration notification
                                                $structuredData['type'] = 'patient_registered';
                                                if (preg_match("/Patient '([^']+)'/", $message, $patientMatches)) {
                                                    $structuredData['patient'] = $patientMatches[1];
                                                }
                                                if (preg_match("/Company: ([^.]+)/", $message, $companyMatches)) {
                                                    $structuredData['company'] = $companyMatches[1];
                                                }
                                            } else {
                                                // Default/other notifications
                                                $structuredData['type'] = 'default';
                                            }
                                        @endphp

                                        @if(isset($structuredData['type']) && $structuredData['type'] === 'pre_employment')
                                            <!-- Pre-Employment Structured Display -->
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-building text-blue-600"></i>
                                                    <span class="font-semibold text-blue-900">Company:</span>
                                                    <span class="text-blue-800">{{ $structuredData['company'] }}</span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-file-medical text-blue-600"></i>
                                                    <span class="font-semibold text-blue-900">Records Created:</span>
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        {{ $structuredData['count'] }} new pre-employment record(s)
                                                    </span>
                                                </div>
                                                
                                                @if(isset($structuredData['tests']))
                                                    <div class="space-y-2">
                                                        <div class="flex items-start space-x-2">
                                                            <i class="fas fa-list-check text-blue-600 mt-1"></i>
                                                            <span class="font-semibold text-blue-900">Tests Included:</span>
                                                        </div>
                                                        <div class="ml-6 bg-white border border-blue-200 rounded-md p-3">
                                                            @php
                                                                $tests = explode(', ', $structuredData['tests']);
                                                            @endphp
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($tests as $test)
                                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-sm">
                                                                        {{ trim($test) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($structuredData['total_value']))
                                                    <div class="flex items-center space-x-2 pt-2 border-t border-blue-200">
                                                        <i class="fas fa-peso-sign text-green-600"></i>
                                                        <span class="font-semibold text-green-900">Total Value:</span>
                                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold">
                                                            {{ $structuredData['total_value'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'appointment_scheduled')
                                            <!-- Appointment Scheduled Structured Display -->
                                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-green-600"></i>
                                                        <span class="font-semibold text-green-900">Patient:</span>
                                                        <span class="text-green-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($structuredData['service']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-stethoscope text-green-600"></i>
                                                        <span class="font-semibold text-green-900">Service:</span>
                                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                                                            {{ $structuredData['service'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($structuredData['date']) && isset($structuredData['time']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-calendar-check text-green-600"></i>
                                                        <span class="font-semibold text-green-900">Scheduled:</span>
                                                        <span class="text-green-800">{{ $structuredData['date'] }} at {{ $structuredData['time'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(!isset($structuredData['patient']) && !isset($structuredData['service']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-calendar-check text-green-600 mt-1"></i>
                                                        <div class="text-green-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'patient_registered')
                                            <!-- Patient Registration Structured Display -->
                                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user-plus text-emerald-600"></i>
                                                        <span class="font-semibold text-emerald-900">New Patient:</span>
                                                        <span class="text-emerald-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($structuredData['company']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-building text-emerald-600"></i>
                                                        <span class="font-semibold text-emerald-900">Company:</span>
                                                        <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full text-sm font-medium">
                                                            {{ $structuredData['company'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                @if(!isset($structuredData['patient']) && !isset($structuredData['company']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-user-plus text-emerald-600 mt-1"></i>
                                                        <div class="text-emerald-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'checklist_completed')
                                            <!-- Checklist Completed Structured Display -->
                                            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-teal-600"></i>
                                                        <span class="font-semibold text-teal-900">Patient:</span>
                                                        <span class="text-teal-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-check-circle text-teal-600"></i>
                                                    <span class="font-semibold text-teal-900">Status:</span>
                                                    <span class="bg-teal-100 text-teal-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        Checklist Completed
                                                    </span>
                                                </div>
                                                
                                                @if(!isset($structuredData['patient']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-check-circle text-teal-600 mt-1"></i>
                                                        <div class="text-teal-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'specimen')
                                            <!-- Specimen Collection Structured Display -->
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-red-600"></i>
                                                        <span class="font-semibold text-red-900">Patient:</span>
                                                        <span class="text-red-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-vial text-red-600"></i>
                                                    <span class="font-semibold text-red-900">Status:</span>
                                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        Specimen Collected
                                                    </span>
                                                </div>
                                                
                                                @if(isset($structuredData['tests']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-list-check text-red-600 mt-1"></i>
                                                        <span class="font-semibold text-red-900">Tests:</span>
                                                        <span class="text-red-800">{{ $structuredData['tests'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(!isset($structuredData['patient']) && !isset($structuredData['tests']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-vial text-red-600 mt-1"></i>
                                                        <div class="text-red-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'xray_completed')
                                            <!-- X-ray Completed Structured Display -->
                                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-indigo-600"></i>
                                                        <span class="font-semibold text-indigo-900">Patient:</span>
                                                        <span class="text-indigo-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-x-ray text-indigo-600"></i>
                                                    <span class="font-semibold text-indigo-900">Status:</span>
                                                    <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        X-ray Completed
                                                    </span>
                                                </div>
                                                
                                                @if(isset($structuredData['xray_type']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-image text-indigo-600"></i>
                                                        <span class="font-semibold text-indigo-900">Type:</span>
                                                        <span class="text-indigo-800">{{ $structuredData['xray_type'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(!isset($structuredData['patient']) && !isset($structuredData['xray_type']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-x-ray text-indigo-600 mt-1"></i>
                                                        <div class="text-indigo-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'xray_interpreted')
                                            <!-- X-ray Interpreted Structured Display -->
                                            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-cyan-600"></i>
                                                        <span class="font-semibold text-cyan-900">Patient:</span>
                                                        <span class="text-cyan-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-search text-cyan-600"></i>
                                                    <span class="font-semibold text-cyan-900">Status:</span>
                                                    <span class="bg-cyan-100 text-cyan-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        X-ray Interpreted
                                                    </span>
                                                </div>
                                                
                                                @if(!isset($structuredData['patient']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-search text-cyan-600 mt-1"></i>
                                                        <div class="text-cyan-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'ecg_completed')
                                            <!-- ECG Completed Structured Display -->
                                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-orange-600"></i>
                                                        <span class="font-semibold text-orange-900">Patient:</span>
                                                        <span class="text-orange-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-heartbeat text-orange-600"></i>
                                                    <span class="font-semibold text-orange-900">Status:</span>
                                                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        ECG Completed
                                                    </span>
                                                </div>
                                                
                                                @if(!isset($structuredData['patient']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-heartbeat text-orange-600 mt-1"></i>
                                                        <div class="text-orange-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'pathologist_report')
                                            <!-- Pathologist Report Structured Display -->
                                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-purple-600"></i>
                                                        <span class="font-semibold text-purple-900">Patient:</span>
                                                        <span class="text-purple-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-microscope text-purple-600"></i>
                                                    <span class="font-semibold text-purple-900">Status:</span>
                                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        Pathologist Report Submitted
                                                    </span>
                                                </div>
                                                
                                                @if(isset($structuredData['tests']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-list-check text-purple-600 mt-1"></i>
                                                        <span class="font-semibold text-purple-900">Tests:</span>
                                                        <span class="text-purple-800">{{ $structuredData['tests'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(!isset($structuredData['patient']) && !isset($structuredData['tests']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-microscope text-purple-600 mt-1"></i>
                                                        <div class="text-purple-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(isset($structuredData['type']) && $structuredData['type'] === 'examination_updated')
                                            <!-- Doctor Examination Structured Display -->
                                            <div class="bg-violet-50 border border-violet-200 rounded-lg p-4 space-y-3">
                                                @if(isset($structuredData['patient']))
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-violet-600"></i>
                                                        <span class="font-semibold text-violet-900">Patient:</span>
                                                        <span class="text-violet-800">{{ $structuredData['patient'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-user-md text-violet-600"></i>
                                                    <span class="font-semibold text-violet-900">Status:</span>
                                                    <span class="bg-violet-100 text-violet-800 px-2 py-1 rounded-full text-sm font-medium">
                                                        Examination Updated
                                                    </span>
                                                </div>
                                                
                                                @if(!isset($structuredData['patient']))
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-user-md text-violet-600 mt-1"></i>
                                                        <div class="text-violet-800">{{ $notification->message }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <!-- Default Structured Display -->
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-start space-x-2">
                                                    <i class="fas fa-info-circle text-gray-600 mt-1"></i>
                                                    <div class="text-gray-800 leading-relaxed">
                                                        {{ $notification->message }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Enhanced Metadata -->
                                    <div class="flex items-center space-x-4 text-sm">
                                        <span class="flex items-center text-gray-500">
                                            <i class="fas fa-clock mr-1.5"></i>
                                            {{ $notification->time_ago }}
                                        </span>
                                        @if($notification->triggered_by_name)
                                            <span class="flex items-center text-gray-500">
                                                <i class="fas fa-user mr-1.5"></i>
                                                {{ $notification->triggered_by_name }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $notification->priority === 'high' ? 'bg-red-100 text-red-800 border border-red-200' : ($notification->priority === 'medium' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-green-100 text-green-800 border border-green-200') }}">
                                            {{ $notification->priority === 'high' ? '🔴' : ($notification->priority === 'medium' ? '🟡' : '🟢') }}
                                            {{ ucfirst($notification->priority) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Enhanced Actions -->
                                <div class="flex items-center space-x-2 ml-4">
                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})" class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 border border-blue-200">
                                            <i class="fas fa-check mr-1"></i>
                                            Mark Read
                                        </button>
                                    @endif
                                    <button onclick="deleteNotification({{ $notification->id }})" class="bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 border border-red-200">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center">
                    <div class="w-20 h-20 mx-auto bg-gray-100 rounded-2xl flex items-center justify-center mb-6 border border-gray-200">
                        <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">No notifications found</h3>
                    <p class="text-gray-600 max-w-md mx-auto">You're all caught up! New notifications will appear here when there's activity across the medical workflow.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<style>
/* Modern Tab Pills */
.notification-tab-pill {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
    background-color: #ffffff;
    color: #6b7280;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    cursor: pointer;
    text-decoration: none;
}

.notification-tab-pill:hover {
    background-color: #f9fafb;
    color: #1f2937;
    border-color: #d1d5db;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.notification-tab-pill.active {
    background-color: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.notification-tab-pill.active:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
    transform: translateY(-1px);
}

.notification-tab-pill .count-badge {
    margin-left: 8px;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
    color: #ffffff;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-tab-pill.active .count-badge {
    background-color: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.notification-tab-pill i {
    margin-right: 6px;
}

/* Enhanced animations */
.notification-tab-pill:active {
    transform: translateY(-2px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .notification-tab-pill {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .notification-tab-pill i {
        margin-right: 6px;
    }
    
    .notification-tab-pill .count-badge {
        margin-left: 6px;
        padding: 1px 6px;
        font-size: 11px;
        min-width: 18px;
        height: 18px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced tab switching functionality
    const tabs = document.querySelectorAll('.notification-tab-pill');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabType = this.dataset.tab;
            
            // Update active tab with smooth animation
            tabs.forEach(t => {
                t.classList.remove('active');
                t.style.transform = 'translateY(0)';
            });
            this.classList.add('active');
            
            // Add click animation
            this.style.transform = 'translateY(-2px)';
            setTimeout(() => {
                this.style.transform = 'translateY(-1px)';
            }, 150);
            
            // Update URL with tab filter and navigate
            const url = new URL(window.location);
            if (tabType === 'all') {
                url.searchParams.delete('type');
                url.searchParams.delete('status');
            } else if (tabType === 'unread') {
                url.searchParams.set('status', 'unread');
                url.searchParams.delete('type');
            } else {
                url.searchParams.delete('status');
                const typeMap = {
                    'company': 'appointment_created,pre_employment_created,patient_registered',
                    'nurse': 'checklist_completed,annual_physical_created',
                    'pathologist': 'pathologist_report_submitted',
                    'radtech': 'xray_completed',
                    'radiologist': 'xray_interpreted',
                    'ecgtech': 'ecg_completed',
                    'plebo': 'specimen_collected',
                    'doctor': 'examination_updated'
                };
                if (typeMap[tabType]) {
                    url.searchParams.set('type', typeMap[tabType]);
                }
            }
            
            window.location.href = url.toString();
        });
    });
    
    // Refresh notifications
    document.getElementById('refresh-notifications').addEventListener('click', function() {
        window.location.reload();
    });
    
    // Mark all as read
    document.getElementById('mark-all-read-btn').addEventListener('click', function() {
        markAllNotificationsAsRead();
    });
});

function markAsRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/admin/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
        });
    }
}

function markAllNotificationsAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }
}
</script>
@endsection
