@extends('layouts.admin')

@section('title', 'OPD - RSS Citi Health Services')
@section('page-title', 'OPD')

@section('content')
<div class="space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center space-x-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center space-x-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="content-card rounded-xl p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Entries</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $entries->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-stethoscope text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="content-card rounded-xl p-6 border-l-4 border-amber-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Submitted to Admin</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $entries->where('status', 'sent_to_admin')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-paper-plane text-amber-600 text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="content-card rounded-xl p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Sent to OPD</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $entries->where('status', 'sent_to_patient')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-share text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="content-card rounded-xl p-6 border-l-4 border-emerald-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Approved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $entries->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-stethoscope text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">OPD Examinations</h3>
                        <p class="text-blue-100 text-sm">Manage OPD medical examinations submitted by doctors</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Filter Tabs -->
                    <div class="flex items-center space-x-2">
                        @php $statuses = ['sent_to_admin' => ['label' => 'Submitted', 'color' => 'yellow'], 'sent_to_patient' => ['label' => 'Sent to OPD', 'color' => 'blue'], 'approved' => ['label' => 'Approved', 'color' => 'green'], 'completed' => ['label' => 'Completed', 'color' => 'purple'], 'all' => ['label' => 'All', 'color' => 'gray']]; @endphp
                        @foreach($statuses as $key => $status)
                        <a href="{{ route('admin.opd', ['filter' => $key]) }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 {{ ($filter ?? 'sent_to_admin') === $key ? 'bg-white text-blue-600' : 'bg-white/10 text-white hover:bg-white/20' }}">
                            {{ $status['label'] }}
                        </a>
                        @endforeach
                    </div>
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-white/60 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" onkeyup="searchEntries()"
                               class="glass-morphism pl-12 pr-4 py-2 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/20 transition-all duration-200 w-64 text-sm border border-white/20" 
                               placeholder="Search entries...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OPD Entries Table -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full" id="opdTable">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">ID</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Patient Name</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Patient Email</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Examination Date</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Fitness Assessment</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Status</th>
                        <th class="text-left py-5 px-6 text-sm font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-blue-50 transition-all duration-200 border-l-4 border-transparent hover:border-blue-400 entry-card" data-entry-id="{{ $entry->id }}">
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg text-sm font-bold text-gray-700">
                                        {{ $entry->id }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $entry->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">OPD Patient</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="text-sm text-gray-900">{{ $entry->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <div class="text-sm text-gray-900">{{ $entry->date ? $entry->date->format('M d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                @php
                                    $assessment = $entry->fitness_assessment ?? 'Pending';
                                    $assessmentClass = '';
                                    if ($assessment === 'Fit to work') {
                                        $assessmentClass = 'bg-green-100 text-green-800';
                                    } elseif ($assessment === 'Not fit for work') {
                                        $assessmentClass = 'bg-red-100 text-red-800';
                                    } elseif ($assessment === 'For evaluation') {
                                        $assessmentClass = 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        $assessmentClass = 'bg-gray-100 text-gray-800';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $assessmentClass }}">
                                    {{ $assessment }}
                                </span>
                            </td>
                            <td class="py-5 px-6 border-r border-gray-100">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($entry->status === 'approved') bg-green-100 text-green-800
                                    @elseif($entry->status === 'sent_to_admin') bg-yellow-100 text-yellow-800
                                    @elseif($entry->status === 'sent_to_patient') bg-blue-100 text-blue-800
                                    @elseif($entry->status === 'completed') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $entry->status ?? 'unknown')) }}
                                </span>
                            </td>
                            <td class="py-5 px-6">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.view-opd-examination', $entry->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all duration-150 shadow-sm hover:shadow-md">
                                        <i class="fas fa-eye mr-1"></i>
                                        View
                                    </a>
                                    
                                    @if($entry->status === 'sent_to_admin')
                                        <form action="{{ route('admin.send-opd-results', $entry->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-all duration-150 shadow-sm hover:shadow-md">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Send to OPD
                                            </button>
                                        </form>
                                    @elseif($entry->status === 'sent_to_patient')
                                        <form action="{{ route('admin.approve-opd-examination', $entry->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-all duration-150 shadow-sm hover:shadow-md">
                                                <i class="fas fa-check mr-1"></i>
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center border-2 border-dashed border-gray-300">
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center border-4 border-blue-300">
                                        <i class="fas fa-stethoscope text-blue-500 text-3xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-semibold text-lg">No OPD examinations found</p>
                                        <p class="text-gray-500 text-sm mt-1">No examinations match the current filter criteria</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($entries, 'links'))
    <div class="flex justify-center">
        {{ $entries->links() }}
    </div>
    @endif
</div>

<!-- Modals -->
<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Approve OPD Entry</h3>
                <button onclick="closeModal('approveModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Approve OPD entry for <span id="approvePatientName"></span>?</p>
                        <p class="text-sm text-gray-500">This will approve the appointment request.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancel</button>
                <button type="button" onclick="confirmApprove()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div id="declineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Decline OPD Entry</h3>
                <button onclick="closeModal('declineModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Decline OPD entry for <span id="declinePatientName"></span>?</p>
                        <p class="text-sm text-gray-500">This action will decline the appointment request.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('declineModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancel</button>
                <button type="button" onclick="confirmDecline()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">Decline</button>
            </div>
        </div>
    </div>
</div>

<!-- Mark Done Modal -->
<div id="markDoneModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Mark as Done</h3>
                <button onclick="closeModal('markDoneModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-double text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Mark OPD entry for <span id="markDonePatientName"></span> as done?</p>
                        <p class="text-sm text-gray-500">This will mark the appointment as completed.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('markDoneModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancel</button>
                <button type="button" onclick="confirmMarkDone()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">Mark Done</button>
            </div>
        </div>
    </div>
</div>

<!-- Send Results Modal -->
<div id="sendResultsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Send Results</h3>
                <button onclick="closeModal('sendResultsModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Send results to <span id="sendResultsPatientName"></span>?</p>
                        <p class="text-sm text-gray-500">Results will be sent to: <span id="sendResultsEmail" class="font-mono"></span></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('sendResultsModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancel</button>
                <button type="button" onclick="confirmSendResults()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">Send Results</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for current action
let currentEntryId = null;

// Modal functions
function openApproveModal(entryId, patientName) {
    currentEntryId = entryId;
    document.getElementById('approvePatientName').textContent = patientName;
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openDeclineModal(entryId, patientName) {
    currentEntryId = entryId;
    document.getElementById('declinePatientName').textContent = patientName;
    document.getElementById('declineModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openMarkDoneModal(entryId, patientName) {
    currentEntryId = entryId;
    document.getElementById('markDonePatientName').textContent = patientName;
    document.getElementById('markDoneModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openSendResultsModal(entryId, patientName, email) {
    currentEntryId = entryId;
    document.getElementById('sendResultsPatientName').textContent = patientName;
    document.getElementById('sendResultsEmail').textContent = email;
    document.getElementById('sendResultsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentEntryId = null;
}

// Action confirmations
function confirmApprove() {
    if (currentEntryId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/opd/${currentEntryId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDecline() {
    if (currentEntryId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/opd/${currentEntryId}/decline`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmMarkDone() {
    if (currentEntryId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/opd/${currentEntryId}/done`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmSendResults() {
    if (currentEntryId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/opd/${currentEntryId}/send-results`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Search functionality
function searchEntries() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#opdTable tbody tr.entry-card');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['approveModal', 'declineModal', 'markDoneModal', 'sendResultsModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            closeModal(modalId);
        }
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['approveModal', 'declineModal', 'markDoneModal', 'sendResultsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});
</script>

@endsection
