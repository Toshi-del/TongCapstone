@extends('layouts.opd')

@section('opd-content')
<div class="bg-gradient-to-b from-green-50 to-white border border-green-100 rounded-xl p-5 mb-6">
  <div class="flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-gray-900">My Appointments</h2>
        <p class="text-gray-500 text-sm">View your booked medical tests and appointments</p>
      </div>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('opd.create') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
        <i class="fa-solid fa-plus mr-2"></i> Book New Test
      </a>

        @csrf
        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700 transition">
          <i class="fa-solid fa-trash mr-2"></i> Clean Invalid
        </button>
      </form>
      <a href="{{ route('opd.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
      </a>
    </div>
  </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
  <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
    <div class="flex items-center">
      <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-check"></i>
      </div>
      <div class="text-green-800">{{ session('success') }}</div>
    </div>
  </div>
@endif

@if(session('error'))
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
    <div class="flex items-center">
      <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-exclamation-triangle"></i>
      </div>
      <div class="text-red-800">{{ session('error') }}</div>
    </div>
  </div>
@endif

@if(session('info'))
  <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
    <div class="flex items-center">
      <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-info-circle"></i>
      </div>
      <div class="text-blue-800">{{ session('info') }}</div>
    </div>
  </div>
@endif

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
        <i class="fa-solid fa-calendar-days"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Total Appointments</p>
        <p class="text-2xl font-bold text-gray-900">{{ $total_appointments }}</p>
      </div>
    </div>
  </div>
  
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
        <i class="fa-solid fa-flask"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Total Tests</p>
        <p class="text-2xl font-bold text-gray-900">{{ $total_tests }}</p>
      </div>
    </div>
  </div>
  
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
        <i class="fa-solid fa-peso-sign"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Total Amount</p>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($total_amount, 2) }}</p>
      </div>
    </div>
  </div>
  
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
        <i class="fa-solid fa-clock"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Upcoming</p>
        <p class="text-2xl font-bold text-gray-900">{{ $upcoming_appointments }}</p>
      </div>
    </div>
  </div>
</div>

@if($groupedAppointments->count() > 0)
  <!-- Filter and Search -->
  <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-search text-gray-400"></i>
          </div>
          <input type="text" id="searchAppointments" placeholder="Search appointments..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
        </div>
      </div>
      <div class="flex gap-2">
        <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
          <option value="">All Status</option>
          <option value="upcoming">Upcoming</option>
          <option value="past">Past</option>
        </select>
        <select id="sortBy" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
          <option value="date_desc">Newest First</option>
          <option value="date_asc">Oldest First</option>
          <option value="amount_desc">Highest Amount</option>
          <option value="amount_asc">Lowest Amount</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Appointments List -->
  <div class="space-y-4" id="appointmentsList">
    @foreach($groupedAppointments as $key => $appointmentGroup)
      @php
        $firstAppointment = $appointmentGroup->first();
        $appointmentDate = $firstAppointment->appointment_date;
        $appointmentTime = $firstAppointment->appointment_time;
        $totalPrice = $appointmentGroup->sum('price');
        $testCount = $appointmentGroup->count();
        $isUpcoming = $appointmentDate >= now()->toDateString();
      @endphp
      
      <div class="appointment-card bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-200" 
           data-date="{{ $appointmentDate }}" 
           data-amount="{{ $totalPrice }}"
           data-status="{{ $isUpcoming ? 'upcoming' : 'past' }}">
        
        <!-- Appointment Header -->
        <div class="bg-gradient-to-r {{ $isUpcoming ? 'from-green-50 to-emerald-50' : 'from-gray-50 to-slate-50' }} border-b border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl {{ $isUpcoming ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center">
                <i class="fa-solid {{ $isUpcoming ? 'fa-calendar-plus' : 'fa-calendar-check' }}"></i>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  @if($appointmentDate)
                    @php
                      try {
                        echo \Carbon\Carbon::parse($appointmentDate)->format('F j, Y');
                      } catch (\Exception $e) {
                        echo $appointmentDate;
                      }
                    @endphp
                  @else
                    Date not set
                  @endif
                </h3>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                  <span><i class="fa-solid fa-clock mr-1"></i> 
                    @if($appointmentTime)
                      @php
                        try {
                          echo \Carbon\Carbon::createFromFormat('H:i', trim($appointmentTime))->format('g:i A');
                        } catch (\Exception $e) {
                          echo $appointmentTime;
                        }
                      @endphp
                    @else
                      Time not set
                    @endif
                  </span>
                  <span><i class="fa-solid fa-flask mr-1"></i> {{ $testCount }} test{{ $testCount > 1 ? 's' : '' }}</span>
                  <span><i class="fa-solid fa-peso-sign mr-1"></i> ₱{{ number_format($totalPrice, 2) }}</span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              @if($isUpcoming)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  <i class="fa-solid fa-calendar-plus mr-1"></i> Upcoming
                </span>
              @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                  <i class="fa-solid fa-check mr-1"></i> Completed
                </span>
              @endif
              <button class="toggle-details text-gray-400 hover:text-gray-600 transition-colors" data-target="details-{{ $loop->index }}">
                <i class="fa-solid fa-chevron-down"></i>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Appointment Details -->
        <div class="appointment-details p-4 hidden" id="details-{{ $loop->index }}">
          <h4 class="font-semibold text-gray-900 mb-3">Test Details:</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($appointmentGroup as $appointment)
              <div class="border border-gray-200 rounded-lg p-3">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h5 class="font-medium text-gray-900 text-sm mb-1">{{ $appointment->medical_test }}</h5>
                    <p class="text-xs text-gray-500 mb-2">
                      Booked: 
                      @php
                        try {
                          echo \Carbon\Carbon::parse($appointment->created_at)->format('M j, Y g:i A');
                        } catch (\Exception $e) {
                          echo $appointment->created_at;
                        }
                      @endphp
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-lg font-bold text-blue-600">₱{{ number_format($appointment->price, 2) }}</div>
                  </div>
                </div>
                
                @if($appointment->price <= 500)
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Basic
                  </span>
                @elseif($appointment->price <= 1000)
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Standard
                  </span>
                @else
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Premium
                  </span>
                @endif
              </div>
            @endforeach
          </div>
          
          <!-- Appointment Actions -->
          <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-600">
              <strong>Customer:</strong> {{ $firstAppointment->customer_name }}<br>
              <strong>Email:</strong> {{ $firstAppointment->customer_email }}
            </div>
            <div class="flex gap-2">
              @if($isUpcoming)
                <a href="{{ route('opd.reschedule.show', ['date' => $appointmentDate, 'time' => $appointmentTime]) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
                  <i class="fa-solid fa-edit mr-2"></i> Reschedule
                </a>
                <form method="POST" action="{{ route('opd.cancel') }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')">
                  @csrf
                  <input type="hidden" name="appointment_date" value="{{ $appointmentDate }}">
                  <input type="hidden" name="appointment_time" value="{{ $appointmentTime }}">
                  <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg border border-red-600 text-red-600 text-sm hover:bg-red-50 transition">
                    <i class="fa-solid fa-times mr-2"></i> Cancel
                  </button>
                </form>
              @else
                <form method="POST" action="{{ route('opd.download-results') }}" class="inline">
                  @csrf
                  <input type="hidden" name="appointment_date" value="{{ $appointmentDate }}">
                  <input type="hidden" name="appointment_time" value="{{ $appointmentTime }}">
                  <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-green-600 text-white text-sm hover:bg-green-700 transition">
                    <i class="fa-solid fa-download mr-2"></i> Download Results
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Pagination would go here if needed -->
  
@else
  <!-- Empty State -->
  <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <i class="fa-solid fa-calendar-xmark text-3xl text-gray-400"></i>
    </div>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Appointments Yet</h3>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
      You haven't booked any medical tests yet. Start by browsing our available tests and booking your first appointment.
    </p>
    <a href="{{ route('opd.create') }}" class="inline-flex items-center px-6 py-3 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
      <i class="fa-solid fa-plus mr-2"></i> Book Your First Test
    </a>
  </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchAppointments');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    
    function filterAndSortAppointments() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const selectedStatus = statusFilter?.value || '';
        const sortOption = sortBy?.value || 'date_desc';
        
        let appointments = Array.from(document.querySelectorAll('.appointment-card'));
        
        // Filter appointments
        appointments.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            const cardStatus = card.dataset.status;
            
            let showCard = true;
            
            // Search filter
            if (searchTerm && !cardText.includes(searchTerm)) {
                showCard = false;
            }
            
            // Status filter
            if (selectedStatus && cardStatus !== selectedStatus) {
                showCard = false;
            }
            
            card.style.display = showCard ? 'block' : 'none';
        });
        
        // Sort appointments
        const visibleAppointments = appointments.filter(card => card.style.display !== 'none');
        
        visibleAppointments.sort((a, b) => {
            const dateA = new Date(a.dataset.date);
            const dateB = new Date(b.dataset.date);
            const amountA = parseFloat(a.dataset.amount);
            const amountB = parseFloat(b.dataset.amount);
            
            switch(sortOption) {
                case 'date_asc':
                    return dateA - dateB;
                case 'date_desc':
                    return dateB - dateA;
                case 'amount_asc':
                    return amountA - amountB;
                case 'amount_desc':
                    return amountB - amountA;
                default:
                    return dateB - dateA;
            }
        });
        
        // Reorder DOM elements
        const container = document.getElementById('appointmentsList');
        if (container) {
            visibleAppointments.forEach(card => {
                container.appendChild(card);
            });
        }
    }
    
    // Event listeners
    if (searchInput) searchInput.addEventListener('input', filterAndSortAppointments);
    if (statusFilter) statusFilter.addEventListener('change', filterAndSortAppointments);
    if (sortBy) sortBy.addEventListener('change', filterAndSortAppointments);
    
    // Toggle appointment details
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const details = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });
    });
});
</script>

@endsection
