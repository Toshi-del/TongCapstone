@extends('layouts.doctor')

@section('title', 'OPD Examinations - RSS Citi Health Services')
@section('page-title', 'OPD Examinations')
@section('page-description', 'Manage and monitor OPD medical examinations')

@section('content')
<div class="space-y-8">
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-4 bg-green-600">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <span class="text-white font-medium">{{ session('success') }}</span>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 bg-teal-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-user-md text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold mb-1">OPD Examinations</h1>
                        <p class="text-teal-100 text-sm">Out-Patient Department medical examinations and patient management</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl px-6 py-4 border border-white border-opacity-30">
                    <p class="text-teal-100 text-sm font-medium">Total Examinations</p>
                    <p class="text-white text-2xl font-bold">{{ $opdExaminations->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 bg-teal-600">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center border border-white border-opacity-30">
                    <i class="fas fa-filter"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Examination Status</h2>
                    <p class="text-teal-100 text-sm">Filter examinations by their current status</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <a href="{{ route('doctor.opd', ['filter' => 'needs_attention']) }}" 
                   class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors duration-200 {{ request('filter', 'needs_attention') === 'needs_attention' ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Needs Attention
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ $needsAttentionCount }}</span>
                </a>
                <a href="{{ route('doctor.opd', ['filter' => 'submitted']) }}" 
                   class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors duration-200 {{ request('filter') === 'submitted' ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submitted
                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $submittedCount }}</span>
                </a>
                <a href="{{ route('doctor.opd', ['filter' => 'all']) }}" 
                   class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors duration-200 {{ request('filter') === 'all' ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-list mr-2"></i>
                    All Examinations
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">{{ $totalCount }}</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Examinations Section -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 bg-teal-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center border border-white border-opacity-30">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Examination Management</h2>
                        <p class="text-teal-100 text-sm">OPD examinations and their current status</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 border border-white border-opacity-30">
                    <p class="text-teal-100 text-xs font-medium">Current Filter</p>
                    <p class="text-white text-lg font-bold">{{ $opdExaminations->count() }}</p>
                </div>
            </div>
        </div>
        
        @if($opdExaminations->count() > 0)
        <div class="p-8">
            <div class="space-y-6">
                @foreach($opdExaminations as $examination)
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200">
                    <!-- Examination Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            @php
                                $name = $examination->name ?? 'Unknown Patient';
                                $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr($name, strpos($name, ' ') + 1, 1) : substr($name, 1, 1)));
                                $colors = ['bg-teal-500', 'bg-blue-500', 'bg-indigo-500', 'bg-green-500', 'bg-purple-500'];
                                $colorIndex = crc32($examination->id) % count($colors);
                            @endphp
                            <div class="w-14 h-14 {{ $colors[$colorIndex] }} rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-lg">{{ $initials }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $name }}</h3>
                                <p class="text-gray-500 text-sm">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $examination->date ? $examination->date->format('M d, Y') : 'Date not set' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                <i class="fas fa-user-md mr-1"></i>OPD Examination
                            </span>
                            @php
                                $status = $examination->status ?? 'pending';
                                $statusConfig = [
                                    'pending' => ['bg-yellow-100 text-yellow-800', 'fas fa-clock', 'Pending'],
                                    'collection_completed' => ['bg-blue-100 text-blue-800', 'fas fa-vial', 'Collection Done'],
                                    'completed' => ['bg-green-100 text-green-800', 'fas fa-check', 'Completed'],
                                    'sent_to_admin' => ['bg-purple-100 text-purple-800', 'fas fa-paper-plane', 'Submitted'],
                                    'approved' => ['bg-green-100 text-green-800', 'fas fa-check-circle', 'Approved']
                                ];
                                $config = $statusConfig[$status] ?? $statusConfig['pending'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $config[0] }}">
                                <i class="{{ $config[1] }} mr-1"></i>{{ $config[2] }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Examination Information -->
                    <div class="bg-gradient-to-r from-teal-50 to-blue-50 rounded-lg p-4 mb-6 border border-teal-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-notes-medical text-teal-600"></i>
                                </div>
                                <span class="text-sm font-bold text-teal-900">Examination Details</span>
                            </div>
                            <span class="px-2 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-semibold">
                                ID: #{{ $examination->id }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="bg-white rounded-lg p-3 border border-teal-100">
                                <div class="flex items-center text-xs text-gray-600 mb-1">
                                    <i class="fas fa-user-md text-teal-500 mr-1.5"></i>
                                    <span>Examined By</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $examination->user->name ?? 'Not assigned' }}
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-teal-100">
                                <div class="flex items-center text-xs text-gray-600 mb-1">
                                    <i class="fas fa-calendar-check text-teal-500 mr-1.5"></i>
                                    <span>Last Updated</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $examination->updated_at->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-teal-100">
                                <div class="flex items-center text-xs text-gray-600 mb-1">
                                    <i class="fas fa-clipboard-check text-teal-500 mr-1.5"></i>
                                    <span>Status</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between space-x-3 mb-4">
                        @if(in_array($status, ['pending', 'collection_completed', 'completed']))
                        <!-- Submit to Admin -->
                        <form action="{{ route('doctor.opd.submit', $examination->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center" 
                                    title="Submit to Admin">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit to Admin
                            </button>
                        </form>
                        @else
                        <div class="flex-1 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-medium flex items-center justify-center cursor-not-allowed">
                            <i class="fas fa-check-circle mr-2"></i>
                            Already Submitted
                        </div>
                        @endif
                        
                        <!-- Edit Examination -->
                        <a href="{{ route('doctor.opd.edit', $examination->id) }}" 
                           class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-200 text-sm font-medium flex items-center justify-center" 
                           title="Edit Examination">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Examination
                        </a>
                        
                        <!-- View Details -->
                        <a href="{{ route('doctor.opd.examination.show', $examination->id) }}" 
                           class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium flex items-center justify-center" 
                           title="View Details">
                            <i class="fas fa-eye mr-2"></i>
                            View Details
                        </a>
                    </div>
                    
                    <!-- Examination Status Footer -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                <span>Created: {{ $examination->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(in_array($status, ['sent_to_admin', 'approved']))
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-green-600 font-medium text-sm">Submitted</span>
                                @elseif($status === 'completed')
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-blue-600 font-medium text-sm">Ready to Submit</span>
                                @else
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    <span class="text-yellow-600 font-medium text-sm">In Progress</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $opdExaminations->links() }}
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="p-16 text-center">
            <div class="w-24 h-24 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-md text-teal-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No OPD Examinations Found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                @if(request('filter') === 'submitted')
                    No submitted examinations found. Examinations will appear here after you submit them to admin.
                @elseif(request('filter') === 'all')
                    No OPD examinations found in the system.
                @else
                    No examinations need your attention at this time. Check back later for new examinations.
                @endif
            </p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('doctor.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
                @if(request('filter') !== 'all')
                <a href="{{ route('doctor.opd', ['filter' => 'all']) }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-list mr-2"></i>
                    View All
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
