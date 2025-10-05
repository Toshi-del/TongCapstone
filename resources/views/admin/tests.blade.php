@extends('layouts.admin')

@section('title', 'Tests - RSS Citi Health Services')
@section('page-title', 'Tests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Success Message -->
        @if(session('success'))
        <div id="successMessage" class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-green-800">Success!</h3>
                                <p class="text-green-700 mt-1">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="closeSuccessMessage()" class="text-green-400 hover:text-green-600 transition-colors">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <div class="w-full bg-green-200 rounded-full h-1">
                                <div id="progressBar" class="bg-green-500 h-1 rounded-full transition-all duration-100" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Pre-Employment Examinations Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-blue-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-briefcase text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Pre-Employment Examinations</h2>
                        <p class="text-blue-100 text-sm mt-1">Medical examinations for employment candidates</p>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Name</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-building text-gray-400"></i>
                                    <span>Company</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Date</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    <span>Status</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-cogs text-gray-400"></i>
                                    <span>Action</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($preEmploymentResults as $exam)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold">
                                            {{ $exam->id }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $exam->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-building text-gray-400 text-sm"></i>
                                        <span class="text-sm text-gray-700 font-medium">{{ $exam->company_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    @php
                                        $status = $exam->status ?? 'Pending';
                                    @endphp
                                    @if($status === 'Completed')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                            Completed
                                        </span>
                                    @elseif($status === 'In Progress')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            In Progress
                                        </span>
                                    @elseif($status === 'Sent')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="fas fa-paper-plane mr-1.5 text-xs"></i>
                                            Sent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.view-pre-employment-results', $exam->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                            <i class="fas fa-eye mr-2 text-xs"></i>
                                            View
                                        </a>
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="toggleDropdown('pre-employment-{{ $exam->id }}')" 
                                                    class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                                <i class="fas fa-paper-plane mr-2 text-xs"></i>
                                                Send
                                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                            </button>
                                            <div id="pre-employment-{{ $exam->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                <div class="py-1">
                                                    <form action="{{ route('admin.examinations.pre-employment.send', $exam->id) }}" method="POST" class="block">
                                                        @csrf
                                                        <input type="hidden" name="send_to" value="company">
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-building mr-2 text-blue-600"></i>
                                                            Send to Company
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.examinations.pre-employment.send', $exam->id) }}" method="POST" class="block">
                                                        @csrf
                                                        <input type="hidden" name="send_to" value="patient">
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-user mr-2 text-green-600"></i>
                                                            Send to Patient
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-briefcase text-gray-400 text-2xl"></i>
                                        </div>
                                        <div class="text-gray-500 text-sm">No pre-employment examinations found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Annual Physical Examinations Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-emerald-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Annual Physical Examinations</h2>
                        <p class="text-emerald-100 text-sm mt-1">Yearly health checkups and medical assessments</p>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Name</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Date</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    <span>Status</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-cogs text-gray-400"></i>
                                    <span>Action</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($annualPhysicalResults as $exam)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-semibold">
                                            {{ $exam->id }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $exam->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-r border-gray-100">
                                    @php
                                        $status = $exam->status ?? 'Pending';
                                    @endphp
                                    @if($status === 'Completed')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                            Completed
                                        </span>
                                    @elseif($status === 'In Progress')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            In Progress
                                        </span>
                                    @elseif($status === 'Sent')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="fas fa-paper-plane mr-1.5 text-xs"></i>
                                            Sent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.view-annual-physical-results', $exam->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                            <i class="fas fa-eye mr-2 text-xs"></i>
                                            View
                                        </a>
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="toggleDropdown('annual-physical-{{ $exam->id }}')" 
                                                    class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-all duration-150 shadow-md hover:shadow-lg">
                                                <i class="fas fa-paper-plane mr-2 text-xs"></i>
                                                Send
                                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                            </button>
                                            <div id="annual-physical-{{ $exam->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                <div class="py-1">
                                                    <form action="{{ route('admin.examinations.annual-physical.send', $exam->id) }}" method="POST" class="block">
                                                        @csrf
                                                        <input type="hidden" name="send_to" value="company">
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-building mr-2 text-blue-600"></i>
                                                            Send to Company
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.examinations.annual-physical.send', $exam->id) }}" method="POST" class="block">
                                                        @csrf
                                                        <input type="hidden" name="send_to" value="patient">
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-user mr-2 text-green-600"></i>
                                                            Send to Patient
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-heartbeat text-gray-400 text-2xl"></i>
                                        </div>
                                        <div class="text-gray-500 text-sm">No annual physical examinations found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide success message after 5 seconds with progress bar
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('successMessage');
    const progressBar = document.getElementById('progressBar');
    
    if (successMessage && progressBar) {
        let width = 100;
        const interval = setInterval(() => {
            width -= 2;
            progressBar.style.width = width + '%';
            
            if (width <= 0) {
                clearInterval(interval);
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 300);
            }
        }, 100); // 5 seconds total (100ms * 50 iterations)
    }
});

function closeSuccessMessage() {
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        successMessage.style.opacity = '0';
        successMessage.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 300);
    }
}

function toggleDropdown(dropdownId) {
    // Close all other dropdowns first
    document.querySelectorAll('[id^="pre-employment-"], [id^="annual-physical-"]').forEach(dropdown => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Toggle the clicked dropdown
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const isDropdownButton = event.target.closest('button[onclick^="toggleDropdown"]');
    const isDropdownContent = event.target.closest('[id^="pre-employment-"], [id^="annual-physical-"]');
    
    if (!isDropdownButton && !isDropdownContent) {
        document.querySelectorAll('[id^="pre-employment-"], [id^="annual-physical-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});
</script>
@endsection
