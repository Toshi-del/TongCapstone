@extends('layouts.admin')

@section('title', 'Pre-Employment - RSS Citi Health Services')
@section('page-title', 'Pre-Employment Management')

@section('content')
<div class="space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="content-card rounded-lg p-4 bg-green-50 border border-green-200">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="content-card rounded-lg p-4 bg-red-50 border border-red-200">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-user-tie text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Pre-Employment Management</h3>
                        <p class="text-cyan-100 text-sm">Manage pre-employment test records and send registration links</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.pre-employment') }}" class="flex items-center space-x-3">
                        <!-- Preserve current filter -->
                        @if(request('filter'))
                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                        @endif
                        @if(request('company'))
                            <input type="hidden" name="company" value="{{ request('company') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
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
                    
                    <!-- Export Button -->
                    <button class="bg-white/10 hover:bg-white/20 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 border border-white/20 backdrop-blur-sm">
                        <i class="fas fa-download text-sm"></i>
                        <span>Export</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-filter mr-2 text-gray-600"></i>Filters
                </h3>
                <button type="button" onclick="clearAllFilters()" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                    <i class="fas fa-times mr-1"></i>Clear All
                </button>
            </div>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.pre-employment') }}" class="space-y-4">
                <!-- Preserve current filter tab -->
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Company Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                        <select name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                            <option value="">All Companies</option>
                            @php
                                $companies = \App\Models\PreEmploymentRecord::whereNotNull('company_name')
                                    ->distinct()
                                    ->pluck('company_name')
                                    ->sort();
                            @endphp
                            @foreach($companies as $company)
                                <option value="{{ $company }}" {{ request('company') === $company ? 'selected' : '' }}>
                                    {{ $company }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Declined" {{ request('status') === 'Declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>
                    
                    <!-- Registration Link Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registration Link</label>
                        <select name="link_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                            <option value="">All</option>
                            <option value="sent" {{ request('link_status') === 'sent' ? 'selected' : '' }}>Link Sent</option>
                            <option value="not_sent" {{ request('link_status') === 'not_sent' ? 'selected' : '' }}>Link Not Sent</option>
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select name="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium transition-all duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.pre-employment') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-200">
                            <i class="fas fa-refresh mr-2"></i>
                            Reset
                        </a>
                    </div>
                    
                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['company', 'status', 'link_status', 'date_range', 'search']))
                        <div class="flex items-center space-x-2 text-sm">
                            <span class="text-gray-500">Active filters:</span>
                            @if(request('company'))
                                <span class="inline-flex items-center px-2 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs">
                                    Company: {{ request('company') }}
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="inline-flex items-center px-2 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs">
                                    Status: {{ request('status') }}
                                </span>
                            @endif
                            @if(request('link_status'))
                                <span class="inline-flex items-center px-2 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs">
                                    Link: {{ request('link_status') === 'sent' ? 'Sent' : 'Not Sent' }}
                                </span>
                            @endif
                            @if(request('date_range'))
                                <span class="inline-flex items-center px-2 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs">
                                    Date: {{ ucfirst(request('date_range')) }}
                                </span>
                            @endif
                            @if(request('search'))
                                <span class="inline-flex items-center px-2 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs">
                                    Search: {{ request('search') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-1">
                <a href="{{ route('admin.pre-employment', ['filter' => 'pending']) }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100' }}">
                    <i class="fas fa-clock mr-2 text-xs"></i>
                    Pending
                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 bg-yellow-500 text-white rounded-full text-xs font-bold">
                        {{ \App\Models\PreEmploymentRecord::where('status', 'pending')->count() }}
                    </span>
                </a>
                <a href="{{ route('admin.pre-employment', ['filter' => 'approved']) }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'approved' ? 'bg-green-100 text-green-800 border border-green-200' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100' }}">
                    <i class="fas fa-check-circle mr-2 text-xs"></i>
                    Approved
                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 bg-green-500 text-white rounded-full text-xs font-bold">
                        {{ \App\Models\PreEmploymentRecord::where('status', 'Approved')->where('registration_link_sent', false)->count() }}
                    </span>
                </a>
                <a href="{{ route('admin.pre-employment', ['filter' => 'declined']) }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'declined' ? 'bg-red-100 text-red-800 border border-red-200' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100' }}">
                    <i class="fas fa-times-circle mr-2 text-xs"></i>
                    Declined
                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white rounded-full text-xs font-bold">
                        {{ \App\Models\PreEmploymentRecord::where('status', 'Declined')->count() }}
                    </span>
                </a>
                <a href="{{ route('admin.pre-employment', ['filter' => 'approved_with_link']) }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'approved_with_link' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100' }}">
                    <i class="fas fa-envelope mr-2 text-xs"></i>
                    Link Sent
                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 bg-blue-500 text-white rounded-full text-xs font-bold">
                        {{ \App\Models\PreEmploymentRecord::where('status', 'Approved')->where('registration_link_sent', true)->count() }}
                    </span>
                </a>
            </div>
        </div>

        @if($filter === 'approved')
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-envelope-open text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">Bulk Registration Links</h4>
                            <p class="text-sm text-gray-600">Send registration links to all approved candidates at once</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Approved Records</div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ \App\Models\PreEmploymentRecord::where('status', 'Approved')->where('registration_link_sent', false)->count() }}
                            </div>
                        </div>
                        <button type="button" 
                                onclick="openBulkSendEmailModal()" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium transition-all duration-200 shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send All Registration Links
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200 hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-square text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Bulk Actions</h3>
                        <p class="text-indigo-100 text-sm">
                            <span id="selectedCount">0</span> record(s) selected
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="openBulkApproveModal()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-check mr-2"></i>
                        Approve Selected
                    </button>
                    <button type="button" onclick="openBulkDeclineModal()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Decline Selected
                    </button>
                    <button type="button" onclick="openBulkSendLinksModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Links
                    </button>
                    <button type="button" onclick="openBulkDeleteModal()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Selected
                    </button>
                    <button type="button" onclick="clearSelection()" class="inline-flex items-center px-3 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pre-Employment Table -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full" id="preEmploymentTable">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-5 px-4 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 text-cyan-600 bg-gray-100 border-gray-300 rounded focus:ring-cyan-500 focus:ring-2">
                                <label for="selectAll" class="sr-only">Select all</label>
                            </div>
                        </th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">ID</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Candidate Name</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Company</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Email</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Medical Examination</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Price</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Status</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Registration Link</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($preEmployments as $preEmployment)
                        <tr class="hover:bg-cyan-50 transition-all duration-200 border-l-4 border-transparent hover:border-cyan-400">
                            <td class="py-5 px-4 border-r border-gray-100">
                                <div class="flex items-center">
                                    <input type="checkbox" name="selected_records[]" value="{{ $preEmployment->id }}" onchange="updateSelection()" class="record-checkbox w-4 h-4 text-cyan-600 bg-gray-100 border-gray-300 rounded focus:ring-cyan-500 focus:ring-2">
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg text-sm font-bold text-gray-700">
                                        {{ $preEmployment->id }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-md">
                                        <span class="text-white font-bold text-sm">
                                            {{ substr($preEmployment->first_name ?? $preEmployment->full_name ?? 'N', 0, 1) }}{{ substr($preEmployment->last_name ?? 'A', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $preEmployment->full_name ?? ($preEmployment->first_name . ' ' . $preEmployment->last_name) }}
                                        </div>
                                        <div class="text-xs text-gray-500">Record ID: #{{ $preEmployment->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-building text-gray-400 text-xs"></i>
                                    <span class="text-sm text-gray-700">{{ $preEmployment->company_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                    <span class="text-sm text-gray-700">{{ $preEmployment->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                                    <div class="text-sm font-medium text-amber-800">
                                        @if($preEmployment->medicalTestCategory)
                                            {{ $preEmployment->medicalTestCategory->name }}
                                            @if($preEmployment->medicalTest)
                                                <div class="text-xs text-amber-600 mt-1">
                                                    {{ $preEmployment->medicalTest->name }}
                                                </div>
                                            @endif
                                        @else
                                            {{ $preEmployment->medical_exam_type ?? 'General Examination' }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @if($preEmployment->age_adjusted && $preEmployment->original_price > $preEmployment->total_price)
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2 bg-amber-50 px-2 py-1 rounded border border-amber-200">
                                            <i class="fas fa-info-circle text-amber-600 text-xs"></i>
                                            <span class="text-xs text-amber-700 font-medium">Age Adjusted</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500 line-through">₱{{ number_format($preEmployment->original_price, 2) }}</span>
                                            <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                        </div>
                                        <div class="flex items-center space-x-2 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                                            <i class="fas fa-peso-sign text-green-500 text-xs"></i>
                                            <span class="text-sm font-semibold text-green-700">
                                                {{ number_format((float)($preEmployment->total_price ?? 0), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                                        <i class="fas fa-peso-sign text-green-500 text-xs"></i>
                                        <span class="text-sm font-semibold text-green-700">
                                            {{ number_format((float)($preEmployment->total_price ?? 0), 2) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @php
                                    $status = $preEmployment->status ?? 'Pending';
                                @endphp
                                @if($status === 'Approved')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                        Approved
                                    </span>
                                @elseif($status === 'Declined')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle mr-1.5 text-xs"></i>
                                        Declined
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-clock mr-1.5 text-xs"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @if($preEmployment->registration_link_sent)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                        Sent
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                        <i class="fas fa-times-circle mr-1.5 text-xs"></i>
                                        Not Sent
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 px-6">
                                <div class="flex items-center space-x-1">
                                    @if($preEmployment->status !== 'Approved')
                                        <button type="button" 
                                                class="w-8 h-8 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-all duration-150 border border-green-200 flex items-center justify-center"
                                                onclick="openPreEmploymentApproveModal({{ $preEmployment->id }})"
                                                title="Approve">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    @endif
                                    @if($preEmployment->status !== 'Declined')
                                        <button type="button" 
                                                class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all duration-150 border border-red-200 flex items-center justify-center"
                                                onclick="openPreEmploymentDeclineModal({{ $preEmployment->id }})"
                                                title="Decline">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    @endif
                                    @if($preEmployment->status === 'Approved' && !$preEmployment->registration_link_sent)
                                        <button type="button" 
                                                class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all duration-150 border border-blue-200 flex items-center justify-center"
                                                onclick="openPreEmploymentSendEmailModal({{ $preEmployment->id }}, '{{ $preEmployment->email ?? 'this candidate' }}')"
                                                title="Send Registration Link">
                                            <i class="fas fa-envelope text-xs"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.pre-employment.details', $preEmployment->id) }}" 
                                       class="w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-150 border border-gray-200 flex items-center justify-center"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-16 text-center border-2 border-dashed border-gray-300">
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="w-20 h-20 bg-gradient-to-br from-cyan-100 to-cyan-200 rounded-full flex items-center justify-center border-4 border-cyan-300">
                                        <i class="fas fa-user-tie text-cyan-500 text-3xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-semibold text-lg">No pre-employment records found</p>
                                        <p class="text-gray-500 text-sm mt-1">Records will appear here when candidates submit applications</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($preEmployments, 'links'))
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $preEmployments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Pre-Employment Approve Modal -->
<div id="preEmploymentApproveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-green-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Approve Pre-Employment Record</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Approval</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to approve this pre-employment record? This will allow the candidate to proceed with registration.
                    </p>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-green-800">
                    <i class="fas fa-info-circle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closePreEmploymentApproveModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmPreEmploymentApprove()" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-check mr-2"></i>
                    Approve Record
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pre-Employment Decline Modal -->
<div id="preEmploymentDeclineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-red-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Decline Pre-Employment Record</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Decline</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to decline this pre-employment record? The candidate will be notified of the rejection.
                    </p>
                </div>
            </div>
            <div class="mb-6">
                <label for="preEmploymentDeclineReason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for declining (optional)
                </label>
                <textarea id="preEmploymentDeclineReason" 
                          rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                          placeholder="Provide a reason for declining this record..."></textarea>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-red-800">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closePreEmploymentDeclineModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmPreEmploymentDecline()" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Decline Record
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pre-Employment Send Email Modal -->
<div id="preEmploymentSendEmailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-blue-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Send Registration Link</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Send Registration Email</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Send registration link to <span id="candidateEmail" class="font-medium text-blue-600"></span>? This will allow them to complete their registration process.
                    </p>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-blue-800">
                    <i class="fas fa-info-circle text-sm"></i>
                    <span class="text-sm font-medium">Email will be sent immediately</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closePreEmploymentSendEmailModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmPreEmploymentSendEmail()" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-envelope mr-2"></i>
                    Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Send Email Modal -->
<div id="bulkSendEmailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 transform transition-all duration-300">
        <div class="bg-indigo-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Send All Registration Links</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Bulk Email Confirmation</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        You are about to send registration links to <span id="bulkEmailCount" class="font-bold text-indigo-600">0</span> approved candidates. This action will notify all candidates simultaneously.
                    </p>
                </div>
            </div>
            
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-indigo-600 text-sm mt-0.5"></i>
                    <div class="text-sm text-indigo-800">
                        <div class="font-medium mb-1">What will happen:</div>
                        <ul class="space-y-1 text-xs">
                            <li>• Registration emails will be sent to all approved candidates</li>
                            <li>• Candidates will receive unique registration links</li>
                            <li>• Email status will be updated automatically</li>
                            <li>• This action cannot be undone</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-yellow-800">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-sm font-medium">Please ensure all candidate information is correct before proceeding</span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeBulkSendEmailModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmBulkSendEmail()" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send All Links
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div id="bulkApproveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-green-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Bulk Approve Records</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Bulk Approval</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to approve <span id="bulkApproveCount" class="font-bold text-green-600">0</span> selected record(s)? This will allow the candidates to proceed with registration.
                    </p>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-green-800">
                    <i class="fas fa-info-circle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeBulkApproveModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmBulkApprove()" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-check mr-2"></i>
                    Approve Records
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Decline Modal -->
<div id="bulkDeclineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-red-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Bulk Decline Records</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Bulk Decline</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to decline <span id="bulkDeclineCount" class="font-bold text-red-600">0</span> selected record(s)? The candidates will be notified of the rejection.
                    </p>
                </div>
            </div>
            <div class="mb-6">
                <label for="bulkDeclineReason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for declining (optional)
                </label>
                <textarea id="bulkDeclineReason" 
                          rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                          placeholder="Provide a reason for declining these records..."></textarea>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-red-800">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-sm font-medium">This action cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeBulkDeclineModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmBulkDecline()" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Decline Records
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Send Links Modal -->
<div id="bulkSendLinksModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-blue-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Bulk Send Registration Links</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Send Registration Links</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Send registration links to <span id="bulkSendLinksCount" class="font-bold text-blue-600">0</span> selected approved record(s)? This will allow them to complete their registration process.
                    </p>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-blue-800">
                    <i class="fas fa-info-circle text-sm"></i>
                    <span class="text-sm font-medium">Emails will be sent immediately</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeBulkSendLinksModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmBulkSendLinks()" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-envelope mr-2"></i>
                    Send Links
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
        <div class="bg-gray-600 px-6 py-4 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trash text-white text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Bulk Delete Records</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Confirm Permanent Deletion</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Are you sure you want to permanently delete <span id="bulkDeleteCount" class="font-bold text-red-600">0</span> selected record(s)? This action cannot be undone and all data will be lost.
                    </p>
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 text-red-800">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-sm font-medium">This action is permanent and cannot be undone</span>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeBulkDeleteModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-150 border border-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        onclick="confirmBulkDelete()" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-150 shadow-md">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Records
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Hidden Forms for Actions -->
<form id="preEmploymentApproveForm" action="" method="POST" style="display: none;">
    @csrf
</form>

<form id="preEmploymentDeclineForm" action="" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="reason" id="preEmploymentDeclineReasonInput">
</form>

<form id="preEmploymentSendEmailForm" action="" method="POST" style="display: none;">
    @csrf
</form>

<form id="bulkSendEmailForm" action="{{ route('admin.pre-employment.send-all-emails') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
let currentPreEmploymentId = null;

function openPreEmploymentApproveModal(preEmploymentId) {
    currentPreEmploymentId = preEmploymentId;
    document.getElementById('preEmploymentApproveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreEmploymentApproveModal() {
    document.getElementById('preEmploymentApproveModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentPreEmploymentId = null;
}

function confirmPreEmploymentApprove() {
    if (currentPreEmploymentId) {
        const form = document.getElementById('preEmploymentApproveForm');
        form.action = `/admin/pre-employment/${currentPreEmploymentId}/approve`;
        form.submit();
    }
}

function openPreEmploymentDeclineModal(preEmploymentId) {
    currentPreEmploymentId = preEmploymentId;
    document.getElementById('preEmploymentDeclineModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreEmploymentDeclineModal() {
    document.getElementById('preEmploymentDeclineModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentPreEmploymentId = null;
    document.getElementById('preEmploymentDeclineReason').value = '';
}

function confirmPreEmploymentDecline() {
    if (currentPreEmploymentId) {
        const reason = document.getElementById('preEmploymentDeclineReason').value;
        document.getElementById('preEmploymentDeclineReasonInput').value = reason;
        
        const form = document.getElementById('preEmploymentDeclineForm');
        form.action = `/admin/pre-employment/${currentPreEmploymentId}/decline`;
        form.submit();
    }
}

function openPreEmploymentSendEmailModal(preEmploymentId, email) {
    currentPreEmploymentId = preEmploymentId;
    document.getElementById('candidateEmail').textContent = email;
    document.getElementById('preEmploymentSendEmailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreEmploymentSendEmailModal() {
    document.getElementById('preEmploymentSendEmailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentPreEmploymentId = null;
}

function confirmPreEmploymentSendEmail() {
    if (currentPreEmploymentId) {
        const form = document.getElementById('preEmploymentSendEmailForm');
        form.action = `/admin/pre-employment/${currentPreEmploymentId}/send-email`;
        form.submit();
    }
}


function openBulkSendEmailModal() {
    const approvedCount = {{ \App\Models\PreEmploymentRecord::where('status', 'Approved')->where('registration_link_sent', false)->count() }};
    document.getElementById('bulkEmailCount').textContent = approvedCount;
    document.getElementById('bulkSendEmailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBulkSendEmailModal() {
    document.getElementById('bulkSendEmailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmBulkSendEmail() {
    const form = document.getElementById('bulkSendEmailForm');
    form.submit();
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['preEmploymentApproveModal', 'preEmploymentDeclineModal', 'preEmploymentSendEmailModal', 'bulkSendEmailModal', 'bulkApproveModal', 'bulkDeclineModal', 'bulkSendLinksModal', 'bulkDeleteModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (modalId === 'preEmploymentDeclineModal') {
                document.getElementById('preEmploymentDeclineReason').value = '';
            }
            if (modalId === 'bulkDeclineModal') {
                document.getElementById('bulkDeclineReason').value = '';
            }
            currentPreEmploymentId = null;
        }
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['preEmploymentApproveModal', 'preEmploymentDeclineModal', 'preEmploymentSendEmailModal', 'bulkSendEmailModal', 'bulkApproveModal', 'bulkDeclineModal', 'bulkSendLinksModal', 'bulkDeleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                if (modalId === 'preEmploymentDeclineModal') {
                    document.getElementById('preEmploymentDeclineReason').value = '';
                }
                if (modalId === 'bulkDeclineModal') {
                    document.getElementById('bulkDeclineReason').value = '';
                }
                currentPreEmploymentId = null;
            }
        });
    }
});

// Bulk Operations and Filter Functions
function clearAllFilters() {
    window.location.href = '{{ route("admin.pre-employment") }}';
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.record-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.record-checkbox');
    const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
    const selectAll = document.getElementById('selectAll');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    
    // Update select all checkbox state
    if (checkedBoxes.length === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
    }
    
    // Show/hide bulk actions bar
    if (checkedBoxes.length > 0) {
        bulkActionsBar.classList.remove('hidden');
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActionsBar.classList.add('hidden');
    }
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.record-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectAll.checked = false;
    selectAll.indeterminate = false;
    
    updateSelection();
}

function getSelectedIds() {
    const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
    return Array.from(checkedBoxes).map(checkbox => checkbox.value);
}

// Bulk Modal Functions
function openBulkApproveModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one record to approve.');
        return;
    }
    
    document.getElementById('bulkApproveCount').textContent = selectedIds.length;
    document.getElementById('bulkApproveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBulkApproveModal() {
    document.getElementById('bulkApproveModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmBulkApprove() {
    const selectedIds = getSelectedIds();
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.pre-employment.bulk-approve") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function openBulkDeclineModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one record to decline.');
        return;
    }
    
    document.getElementById('bulkDeclineCount').textContent = selectedIds.length;
    document.getElementById('bulkDeclineReason').value = '';
    document.getElementById('bulkDeclineModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBulkDeclineModal() {
    document.getElementById('bulkDeclineModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('bulkDeclineReason').value = '';
}

function confirmBulkDecline() {
    const selectedIds = getSelectedIds();
    const reason = document.getElementById('bulkDeclineReason').value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.pre-employment.bulk-decline") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const reasonInput = document.createElement('input');
    reasonInput.type = 'hidden';
    reasonInput.name = 'reason';
    reasonInput.value = reason;
    form.appendChild(reasonInput);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function openBulkSendLinksModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one approved record to send links.');
        return;
    }
    
    document.getElementById('bulkSendLinksCount').textContent = selectedIds.length;
    document.getElementById('bulkSendLinksModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBulkSendLinksModal() {
    document.getElementById('bulkSendLinksModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmBulkSendLinks() {
    const selectedIds = getSelectedIds();
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.pre-employment.bulk-send-links") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function openBulkDeleteModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one record to delete.');
        return;
    }
    
    document.getElementById('bulkDeleteCount').textContent = selectedIds.length;
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmBulkDelete() {
    const selectedIds = getSelectedIds();
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.pre-employment.bulk-delete") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

