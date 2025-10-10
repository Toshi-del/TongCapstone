@extends('layouts.opd')

@section('opd-content')
<div class="bg-gradient-to-b from-blue-50 to-white border border-blue-100 rounded-xl p-5 mb-6">
  <div class="flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-calendar-alt"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-gray-900">Reschedule Appointment</h2>
        <p class="text-gray-500 text-sm">Update your appointment date and time</p>
      </div>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('opd.show') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Appointments
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

@if ($errors->any())
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
    <div class="flex items-start">
      <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-3 mt-0.5">
        <i class="fa-solid fa-exclamation-triangle"></i>
      </div>
      <div>
        <h4 class="text-red-800 font-medium mb-2">Please fix the following errors:</h4>
        <ul class="text-red-700 text-sm list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
@endif

<!-- Current Appointment Details -->
<div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
  <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Appointment Details</h3>
  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
      <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
          <i class="fa-solid fa-calendar"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Current Date</p>
          <p class="font-semibold text-gray-900">
            @php
              try {
                echo \Carbon\Carbon::parse($appointmentDate)->format('F j, Y');
              } catch (\Exception $e) {
                echo $appointmentDate;
              }
            @endphp
          </p>
        </div>
      </div>
    </div>
    
    <div>
      <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
          <i class="fa-solid fa-clock"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Current Time</p>
          <p class="font-semibold text-gray-900">
            @php
              try {
                echo \Carbon\Carbon::createFromFormat('H:i', trim($appointmentTime))->format('g:i A');
              } catch (\Exception $e) {
                echo $appointmentTime;
              }
            @endphp
          </p>
        </div>
      </div>
    </div>
  </div>
  
  <div class="border-t border-gray-200 pt-4">
    <h4 class="font-medium text-gray-900 mb-3">Tests to be rescheduled:</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
      @foreach($appointments as $appointment)
        <div class="border border-gray-200 rounded-lg p-3">
          <h5 class="font-medium text-gray-900 text-sm">{{ $appointment->medical_test }}</h5>
          <p class="text-lg font-bold text-blue-600">₱{{ number_format($appointment->price, 2) }}</p>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Reschedule Form -->
<div class="bg-white border border-gray-200 rounded-xl p-6">
  <h3 class="text-lg font-semibold text-gray-900 mb-4">Select New Date and Time</h3>
  
  <form method="POST" action="{{ route('opd.reschedule') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="old_date" value="{{ $appointmentDate }}">
    <input type="hidden" name="old_time" value="{{ $appointmentTime }}">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="new_date" class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fa-solid fa-calendar mr-2"></i>New Date
        </label>
        <input type="date" 
               id="new_date" 
               name="new_date" 
               value="{{ old('new_date') }}"
               min="{{ date('Y-m-d') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('new_date') border-red-500 @enderror"
               required>
        @error('new_date')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
      
      <div>
        <label for="new_time" class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fa-solid fa-clock mr-2"></i>New Time
        </label>
        <select id="new_time" 
                name="new_time" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('new_time') border-red-500 @enderror"
                required>
          <option value="">Select a time</option>
          <option value="08:00" {{ old('new_time') == '08:00' ? 'selected' : '' }}>8:00 AM</option>
          <option value="09:00" {{ old('new_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
          <option value="10:00" {{ old('new_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
          <option value="11:00" {{ old('new_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
          <option value="13:00" {{ old('new_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
          <option value="14:00" {{ old('new_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
          <option value="15:00" {{ old('new_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
          <option value="16:00" {{ old('new_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
        </select>
        @error('new_time')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
      <div class="flex items-start">
        <div class="w-6 h-6 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mr-3 mt-0.5">
          <i class="fa-solid fa-exclamation-triangle text-xs"></i>
        </div>
        <div class="text-yellow-800 text-sm">
          <p class="font-medium mb-1">Important Notes:</p>
          <ul class="list-disc list-inside space-y-1">
            <li>Please reschedule at least 24 hours before your current appointment</li>
            <li>All tests in this appointment will be moved to the new date and time</li>
            <li>You may receive a confirmation email once the reschedule is processed</li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="flex gap-3 pt-4">
      <button type="submit" 
              class="inline-flex items-center px-6 py-3 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
        <i class="fa-solid fa-calendar-check mr-2"></i>
        Reschedule Appointment
      </button>
      
      <a href="{{ route('opd.show') }}" 
         class="inline-flex items-center px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
        <i class="fa-solid fa-times mr-2"></i>
        Cancel
      </a>
    </div>
  </form>
</div>

@endsection
