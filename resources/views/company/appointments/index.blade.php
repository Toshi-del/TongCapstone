@extends('layouts.company')

@section('title', 'Appointment Management - RSS Citi Health Services')
@section('page-title', 'Appointment Management')
@section('page-description', 'Schedule and manage medical examination appointments with an interactive calendar')

@section('content')
<div class="space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="content-card rounded-xl p-4 shadow-lg border border-emerald-200 bg-emerald-50">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="content-card rounded-xl p-4 shadow-lg border border-red-200 bg-red-50">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="content-card rounded-xl p-4 shadow-lg border border-blue-200 bg-blue-50">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-blue-800 font-medium">{{ session('info') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-blue-400 hover:text-blue-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="content-card rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">This Month</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $appointments->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="content-card rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $appointments->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
        </div>
        <div class="content-card rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Approved</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $appointments->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
            </div>
        </div>
        <div class="content-card rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Cancelled</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $appointments->where('status', 'cancelled')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="content-card rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Patients</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $appointments->sum(function($apt) { return $apt->patients ? $apt->patients->count() : 0; }) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Calendar -->
    <div class="content-card rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Calendar Header -->
        <div class="bg-blue-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Appointment Calendar</h2>
                        <p class="text-blue-100 text-sm">Click on any date to schedule an appointment</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="todayBtn" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 backdrop-blur-sm border border-white/20">
                        <i class="fas fa-calendar-day mr-2"></i>Today
                    </button>
                    <a href="{{ route('company.appointments.create') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                        <i class="fas fa-plus mr-2"></i>New Appointment
                    </a>
                </div>
            </div>
        </div>

        <!-- Calendar Navigation -->
        <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button id="prevMonth" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="nextMonth" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900" id="currentMonth">
                    {{ now()->format('F Y') }}
                </h3>
                
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-1 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span>Available</span>
                    </div>
                    <div class="flex items-center space-x-1 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                        <span>Scheduled</span>
                    </div>
                    <div class="flex items-center space-x-1 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        <span>Cancelled</span>
                    </div>
                    <div class="flex items-center space-x-1 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                        <span>Blocked/Closed</span>
                    </div>
                    <div class="flex items-center space-x-1 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span>Taken by Other Company</span>
                    </div>
                    <div class="flex items-center space-x-1 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        <span>Past</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="p-6">
            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 gap-2 mb-4">
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Sunday</div>
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Monday</div>
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Tuesday</div>
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Wednesday</div>
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Thursday</div>
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Friday</div>
                <div class="text-center text-sm font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">Saturday</div>
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-2" id="calendarGrid">
                <!-- Calendar days will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list-alt text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Upcoming Appointments</h3>
                    <p class="text-gray-600 text-sm">Manage and track scheduled medical examinations</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $appointments->count() }}</span> appointments
                </div>
                <a href="{{ route('company.appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-plus mr-2"></i>Schedule New
                </a>
            </div>
        </div>
        
        @forelse($appointments as $appointment)
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:bg-gray-100 transition-colors duration-200 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ \Carbon\Carbon::parse($appointment->formatted_date ?? now())->format('d') }}</div>
                            <div class="text-xs text-blue-500">{{ \Carbon\Carbon::parse($appointment->formatted_date ?? now())->format('M') }}</div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-lg">
                            @php
                                $firstCategory = $appointment->first_category;
                                $firstTest = $appointment->first_test;
                                $testCount = $appointment->selected_tests->count();
                            @endphp
                            {{ $firstCategory->name ?? 'Medical Examination' }}
                            @if($firstTest)
                                - {{ $firstTest->name }}
                                @if($testCount > 1)
                                    <span class="text-sm text-gray-500">(+{{ $testCount - 1 }} more)</span>
                                @endif
                            @endif
                        </h4>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-2">
                            <span class="flex items-center space-x-2">
                                <i class="fas fa-calendar text-blue-500"></i>
                                <span>{{ $appointment->formatted_date ?? 'Date not set' }}</span>
                            </span>
                            <span class="flex items-center space-x-2">
                                <i class="fas fa-clock text-emerald-500"></i>
                                <span>{{ $appointment->formatted_time_slot ?? 'Time not set' }}</span>
                            </span>
                            <span class="flex items-center space-x-2">
                                <i class="fas fa-users text-purple-500"></i>
                                <span>{{ $appointment->patients ? $appointment->patients->count() : 0 }} patients</span>
                            </span>
                            @if($appointment->total_price)
                            <span class="flex items-center space-x-2">
                                <i class="fas fa-peso-sign text-amber-500"></i>
                                <span>₱{{ number_format($appointment->total_price, 2) }}</span>
                            </span>
                            @endif
                        </div>
                        @if($appointment->status === 'cancelled' && $appointment->cancellation_info)
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-info-circle text-red-500 mt-0.5 text-sm"></i>
                                <p class="text-sm text-red-700">{{ $appointment->cancellation_info }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @php
                        $statusConfig = [
                            'pending' => ['bg-amber-100 text-amber-700', 'fas fa-clock', 'Pending'],
                            'approved' => ['bg-emerald-100 text-emerald-700', 'fas fa-check-circle', 'Approved'],
                            'scheduled' => ['bg-blue-100 text-blue-700', 'fas fa-calendar-check', 'Scheduled'],
                            'completed' => ['bg-green-100 text-green-700', 'fas fa-check-double', 'Completed'],
                            'cancelled' => ['bg-red-100 text-red-700', 'fas fa-times-circle', 'Cancelled'],
                        ];
                        $status = $appointment->status ?? 'scheduled';
                        $config = $statusConfig[$status] ?? $statusConfig['scheduled'];
                    @endphp
                    <span class="px-4 py-2 rounded-full text-sm font-medium {{ $config[0] }} flex items-center space-x-2">
                        <i class="{{ $config[1] }} text-xs"></i>
                        <span>{{ $config[2] }}</span>
                    </span>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('company.appointments.show', $appointment) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($appointment->status !== 'cancelled')
                        <a href="{{ route('company.appointments.edit', $appointment) }}" class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Appointment">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('company.appointments.destroy', $appointment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" onclick="return confirm('Are you sure you want to delete this appointment?')" title="Delete Appointment">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @else
                        <span class="p-2 text-gray-400 cursor-not-allowed rounded-lg" title="Cannot edit cancelled appointments">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span class="p-2 text-gray-400 cursor-not-allowed rounded-lg" title="Cannot delete cancelled appointments">
                            <i class="fas fa-trash"></i>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
            </div>
            <h4 class="text-xl font-medium text-gray-900 mb-2">No Appointments Scheduled</h4>
            <p class="text-gray-600 mb-6">Get started by scheduling your first medical examination appointment.</p>
            <a href="{{ route('company.appointments.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>
                Schedule First Appointment
            </a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthElement = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const todayBtn = document.getElementById('todayBtn');
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    // Current company's appointments data
    const appointments = @json($appointments->map(function($apt) {
        return [
            'date' => $apt->appointment_date ? $apt->appointment_date->format('Y-m-d') : null,
            'status' => $apt->status ?? 'scheduled'
        ];
    }));
    
    // All booked dates from all companies
    const allBookedDates = @json($allBookedDates ?? []);
    
    // Debug: Log the booked dates to console
    console.log('All booked dates:', allBookedDates);
    
    function hasAppointment(dateString) {
        return appointments.some(apt => apt.date === dateString);
    }
    
    function getAppointmentStatus(dateString) {
        const appointment = appointments.find(apt => apt.date === dateString);
        return appointment ? appointment.status : null;
    }
    
    function isDateBookedByOtherCompany(dateString) {
        const isBooked = allBookedDates.includes(dateString) && !hasAppointment(dateString);
        if (isBooked) {
            console.log('Date blocked by other company:', dateString);
        }
        return isBooked;
    }
    
    // Helper function to format date without timezone issues
    function formatDateString(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function generateCalendar(month, year) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];
        
        currentMonthElement.textContent = `${monthNames[month]} ${year}`;
        
        calendarGrid.innerHTML = '';
        
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            
            const dayElement = document.createElement('div');
            const dayNumber = date.getDate();
            const isCurrentMonth = date.getMonth() === month;
            const isToday = date.toDateString() === new Date().toDateString();
            const isPast = date < new Date().setHours(0, 0, 0, 0);
            
            // Block next 4 days from today (including today) and all Sundays
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const blockedUntil = new Date(today);
            blockedUntil.setDate(today.getDate() + 4);
            const isWithinBlockedDays = date >= today && date < blockedUntil;
            const isSunday = date.getDay() === 0; // Sunday = 0
            
            const dateString = formatDateString(date);
            const hasAppt = hasAppointment(dateString);
            const appointmentStatus = getAppointmentStatus(dateString);
            const isBookedByOtherCompany = isDateBookedByOtherCompany(dateString);
            
            const isBlocked = isWithinBlockedDays || isSunday || isBookedByOtherCompany;
            
            // Base styling
            let dayClasses = 'relative rounded-xl min-h-[100px] p-3 transition-all duration-200 cursor-pointer border-2 border-transparent';
            
            if (isCurrentMonth) {
                if (isPast) {
                    dayClasses += ' bg-gray-100 text-gray-400 cursor-not-allowed';
                } else if (isBlocked) {
                    dayClasses += ' bg-red-50 text-red-400 cursor-not-allowed border-red-200';
                } else if (isToday) {
                    dayClasses += ' bg-blue-500 text-white shadow-lg transform scale-105';
                } else if (hasAppt) {
                    if (appointmentStatus === 'cancelled') {
                        dayClasses += ' bg-orange-100 text-orange-800 border-orange-200 hover:bg-orange-200';
                    } else {
                        dayClasses += ' bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200';
                    }
                } else {
                    dayClasses += ' bg-white text-gray-900 hover:bg-blue-50 hover:border-blue-200 shadow-sm';
                }
            } else {
                dayClasses += ' bg-gray-50 text-gray-300';
            }
            
            dayElement.className = dayClasses;
            
            let dayContent = `
                <div class="flex justify-between items-start">
                    <span class="text-lg font-bold">${dayNumber}</span>
                    ${hasAppt ? '<div class="w-2 h-2 bg-emerald-500 rounded-full"></div>' : ''}
                </div>
            `;
            
            if (hasAppt && isCurrentMonth) {
                dayContent += `
                    <div class="mt-2">
                        <div class="text-xs bg-emerald-500 text-white px-2 py-1 rounded-full">
                            Scheduled
                        </div>
                    </div>
                `;
            }
            
            if (isToday) {
                dayContent += `
                    <div class="absolute bottom-2 left-2 right-2">
                        <div class="text-xs bg-white/20 text-white px-2 py-1 rounded-full text-center">
                            Today
                        </div>
                    </div>
                `;
            }
            
            if (isBlocked && isCurrentMonth && !isPast) {
                let blockedReason = '';
                if (isSunday) {
                    blockedReason = '<i class="fas fa-calendar-times mr-1"></i>Closed';
                } else if (isBookedByOtherCompany) {
                    blockedReason = '<i class="fas fa-building mr-1"></i>Taken';
                } else if (isWithinBlockedDays) {
                    blockedReason = '<i class="fas fa-ban mr-1"></i>Blocked';
                }
                
                dayContent += `
                    <div class="absolute bottom-2 left-2 right-2">
                        <div class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full text-center border border-red-200">
                            ${blockedReason}
                        </div>
                    </div>
                `;
            }
            
            dayElement.innerHTML = dayContent;
            
            // Add click event for future dates in current month (excluding blocked dates and scheduled appointments)
            if (!isPast && !isBlocked && !hasAppt && isCurrentMonth) {
                dayElement.addEventListener('click', function() {
                    // Add selection effect
                    document.querySelectorAll('.calendar-selected').forEach(el => {
                        el.classList.remove('calendar-selected', 'ring-4', 'ring-blue-300');
                    });
                    
                    dayElement.classList.add('calendar-selected', 'ring-4', 'ring-blue-300');
                    
                    // Format date for URL
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const selectedDate = `${year}-${month}-${day}`;
                    
                    // Navigate to create appointment with selected date
                    setTimeout(() => {
                        window.location.href = `{{ route('company.appointments.create') }}?date=${selectedDate}`;
                    }, 200);
                });
                
                // Add hover effect for available dates only
                dayElement.addEventListener('mouseenter', function() {
                    if (!hasAppt && !isToday && !isBlocked && !isPast) {
                        dayElement.classList.add('transform', 'scale-105', 'shadow-md');
                    }
                });
                
                dayElement.addEventListener('mouseleave', function() {
                    if (!hasAppt && !isToday && !isBlocked && !isPast) {
                        dayElement.classList.remove('transform', 'scale-105', 'shadow-md');
                    }
                });
            }
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    // Navigation event listeners
    prevMonthBtn.addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        generateCalendar(currentMonth, currentYear);
    });
    
    nextMonthBtn.addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        generateCalendar(currentMonth, currentYear);
    });
    
    todayBtn.addEventListener('click', function() {
        currentDate = new Date();
        currentMonth = currentDate.getMonth();
        currentYear = currentDate.getFullYear();
        generateCalendar(currentMonth, currentYear);
    });
    
    // Initialize calendar
    generateCalendar(currentMonth, currentYear);
});
</script>

<style>
    .calendar-selected {
        animation: pulse 0.5s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endpush
@endsection