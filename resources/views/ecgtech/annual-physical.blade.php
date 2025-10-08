@extends('layouts.ecgtech')

@section('title', 'Annual Physical ECG Patients')

@section('page-title', 'Annual Physical ECG Patients')
@section('page-description', 'Manage ECG examinations and cardiac monitoring for routine annual physical examinations')

@section('content')
@if(session('success'))
    <div class="mb-8 p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 flex items-center space-x-3">
        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
            <i class="fas fa-check text-white text-sm"></i>
        </div>
        <div>
            <p class="text-green-800 font-semibold">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<!-- ECG Status Tabs -->
<div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200 mb-8">
    @php
        $currentTab = request('ecg_status', 'needs_attention');
    @endphp
    
    <!-- Tab Navigation -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex space-x-1">
                <a href="{{ request()->fullUrlWithQuery(['ecg_status' => 'needs_attention']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'needs_attention' ? 'bg-green-600 text-white' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Needs Attention
                    @php
                        $needsAttentionCount = \App\Models\Patient::where('status', 'approved')
                            ->where(function($q) {
                                $q->where('age_adjusted', false)
                                  ->orWhereNull('age_adjusted');
                            })
                            ->whereDoesntHave('annualPhysicalExamination', function($q) {
                                $q->whereNotNull('ecg')
                                  ->where('ecg', '!=', '');
                            })
                            ->count();
                    @endphp
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'needs_attention' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $needsAttentionCount }}
                    </span>
                </a>
                
                <a href="{{ request()->fullUrlWithQuery(['ecg_status' => 'ecg_completed']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentTab === 'ecg_completed' ? 'bg-green-600 text-white' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                    <i class="fas fa-check-circle mr-2"></i>
                    ECG Completed
                    @php
                        $completedCount = \App\Models\Patient::where('status', 'approved')
                            ->where(function($q) {
                                $q->where('age_adjusted', false)
                                  ->orWhereNull('age_adjusted');
                            })
                            ->whereHas('annualPhysicalExamination', function($q) {
                                $q->whereNotNull('ecg')
                                  ->where('ecg', '!=', '');
                            })
                            ->count();
                    @endphp
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $currentTab === 'ecg_completed' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $completedCount }}
                    </span>
                </a>
            </div>
            
            <a href="{{ route('ecgtech.annual-physical') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-times mr-1"></i>Clear All Filters
            </a>
        </div>
    </div>

    <!-- Additional Filters -->
    <div class="p-6">
        <form method="GET" action="{{ route('ecgtech.annual-physical') }}" class="space-y-6">
            <!-- Preserve current tab -->
            <input type="hidden" name="ecg_status" value="{{ $currentTab }}">
            
            <!-- Preserve search query -->
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            
            <!-- Filter Row: Gender only -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Gender Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
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
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
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
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                Search: "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">×</a>
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

<!-- Enhanced Search and Filter Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-8">
    <div class="flex items-center space-x-3 mb-6">
        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-search text-white"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Search & Filter</h3>
            <p class="text-gray-600 text-sm">Find specific annual physical patients quickly</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Search Patients</label>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search by patient name, email address..." 
                       class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Age Range</label>
            <select id="ageFilter" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <option value="">All Ages</option>
                <option value="18-30">18-30 years</option>
                <option value="31-45">31-45 years</option>
                <option value="46-60">46-60 years</option>
                <option value="60+">60+ years</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
            <select id="sexFilter" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 mb-4">
                <option value="">All Genders</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            
            <button onclick="clearFilters()" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors duration-200 flex items-center justify-center space-x-2">
                <i class="fas fa-times"></i>
                <span>Clear Filters</span>
            </button>
        </div>
    </div>
</div>

<!-- Age Restriction Info Message -->
@php
    $ageAdjustedCount = \App\Models\Patient::where('status', 'approved')
        ->where('age_adjusted', true)
        ->count();
@endphp
@if($ageAdjustedCount > 0)
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
    <div class="flex items-start space-x-3">
        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-sm"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-medium text-blue-900 mb-1">Age-Based ECG Filtering</h4>
            <p class="text-sm text-blue-800">
                {{ $ageAdjustedCount }} patient{{ $ageAdjustedCount > 1 ? 's' : '' }} under 34 years old {{ $ageAdjustedCount > 1 ? 'are' : 'is' }} automatically excluded from this ECG list. 
                Their medical tests were adjusted from "Annual Medical with ECG and Drug Test" to "Annual Medical with Drug Test" only, 
                so they don't require ECG examinations.
            </p>
        </div>
    </div>
</div>
@endif

<!-- Enhanced Patients Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heartbeat text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Annual Physical ECG Patients</h2>
                    <p class="text-gray-600 text-sm mt-1">Routine cardiac health assessments and monitoring (34+ years only)</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-green-600" id="patientCount">{{ $patients->count() }}</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">ECG Required Patients</div>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full" id="patientsTable">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Age</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 patient-row" data-name="{{ strtolower($patient->first_name . ' ' . $patient->last_name) }}" data-email="{{ strtolower($patient->email) }}" data-age="{{ $patient->age }}" data-sex="{{ $patient->sex }}">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                    <p class="text-xs text-gray-500">Annual Physical Patient</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $patient->age }}</div>
                            <div class="text-xs text-gray-500">years old</div>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $patient->sex }}</td>
                        <td class="px-6 py-6 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $patient->email }}</div>
                            <div class="text-xs text-gray-500">Primary Contact</div>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                
                                <a href="{{ route('ecgtech.medical-checklist-page.annual-physical', $patient->id) }}" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors duration-200 group" title="ECG Checklist">
                                    <i class="fas fa-heartbeat mr-1 group-hover:scale-110 transition-transform"></i>
                                    ECG
                                </a>
                                <a href="{{ route('ecgtech.annual-physical.edit', $patient->id) }}" class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors duration-200 group" title="Edit ECG Data">
                                    <i class="fas fa-edit mr-1 group-hover:rotate-12 transition-transform"></i>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyState">
                        <td colspan="5" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-heartbeat text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No ECG patients found</p>
                                <p class="text-gray-400 text-sm">Only patients 34+ years requiring ECG examinations appear here</p>
                                <p class="text-gray-400 text-xs mt-2">Patients under 34 are automatically excluded (Drug Test only)</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Enhanced Pagination -->
    @if($patients->hasPages())
        <div class="px-8 py-6 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} results
                </div>
                <div class="pagination-wrapper">
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const ageFilter = document.getElementById('ageFilter');
    const sexFilter = document.getElementById('sexFilter');
    const patientCount = document.getElementById('patientCount');
    
    // Add loading states and animations
    searchInput.addEventListener('input', debounce(filterTable, 300));
    ageFilter.addEventListener('change', filterTable);
    sexFilter.addEventListener('change', filterTable);
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const ageFilterValue = ageFilter.value;
        const sexFilterValue = sexFilter.value;
        const rows = document.querySelectorAll('.patient-row');
        const emptyState = document.getElementById('emptyState');
        let visibleCount = 0;
        
        // Add loading animation
        patientCount.style.opacity = '0.5';
        
        rows.forEach((row, index) => {
            const name = row.dataset.name;
            const email = row.dataset.email;
            const age = parseInt(row.dataset.age);
            const sex = row.dataset.sex;
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesAge = !ageFilterValue || checkAgeRange(age, ageFilterValue);
            const matchesSex = !sexFilterValue || sex === sexFilterValue;
            
            if (matchesSearch && matchesAge && matchesSex) {
                row.style.display = '';
                row.style.animation = `fadeInUp 0.3s ease-out ${index * 0.05}s both`;
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update patient count with animation
        setTimeout(() => {
            patientCount.textContent = visibleCount;
            patientCount.style.opacity = '1';
        }, 150);
        
        // Show/hide empty state
        if (emptyState) {
            if (visibleCount === 0 && (searchTerm || ageFilterValue || sexFilterValue)) {
                emptyState.style.display = '';
                emptyState.innerHTML = `
                    <td colspan="5" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center space-y-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No patients match your search</p>
                            <p class="text-gray-400 text-sm">Try adjusting your search terms or filters</p>
                            <button onclick="clearFilters()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    </td>
                `;
            } else if (visibleCount === 0) {
                emptyState.style.display = '';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }
    
    function checkAgeRange(age, range) {
        switch(range) {
            case '18-30': return age >= 18 && age <= 30;
            case '31-45': return age >= 31 && age <= 45;
            case '46-60': return age >= 46 && age <= 60;
            case '60+': return age > 60;
            default: return true;
        }
    }
    
    function clearFilters() {
        searchInput.value = '';
        ageFilter.value = '';
        sexFilter.value = '';
        
        // Add clear animation
        searchInput.style.transform = 'scale(0.95)';
        ageFilter.style.transform = 'scale(0.95)';
        sexFilter.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            searchInput.style.transform = 'scale(1)';
            ageFilter.style.transform = 'scale(1)';
            sexFilter.style.transform = 'scale(1)';
        }, 150);
        
        filterTable();
    }
    
    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Add hover effects to action buttons
    const actionButtons = document.querySelectorAll('button, a');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear filters
        if (e.key === 'Escape') {
            clearFilters();
        }
    });
    
    // Add search input focus effects
    searchInput.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.boxShadow = '0 4px 12px rgba(34, 197, 94, 0.15)';
    });
    
    searchInput.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
        this.parentElement.style.boxShadow = 'none';
    });
    
    // Make clearFilters globally available
    window.clearFilters = clearFilters;
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .pagination-wrapper .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        space-x: 1rem;
    }
    
    .pagination-wrapper .pagination a,
    .pagination-wrapper .pagination span {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    
    .pagination-wrapper .pagination a:hover {
        background-color: #16a34a;
        color: white;
        transform: translateY(-1px);
    }
`;
document.head.appendChild(style);
</script>

@endsection
