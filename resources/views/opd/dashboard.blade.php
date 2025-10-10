@extends('layouts.opd')

@section('opd-content')
<div class="bg-gradient-to-b from-blue-50 to-white border border-blue-100 rounded-xl p-5 mb-6">
  <div class="flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-user"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-gray-900">Welcome, {{ Auth::user()->fname ?? 'OPD' }}!</h2>
        <p class="text-gray-500 text-sm">Walk‑in patient portal</p>
      </div>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('opd.medical-test-categories') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
        <i class="fa-solid fa-compass mr-2"></i> Start browsing
      </a>
      <a href="{{ route('opd.incoming-tests') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-blue-600 text-blue-700 text-sm hover:bg-blue-50 transition">
        <i class="fa-solid fa-inbox mr-2"></i> View Incoming
      </a>
      
    </div>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

  <a href="{{ route('opd.result') }}" class="block group">
    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition h-full">
      <div class="flex items-center gap-3">
        <div class="w-14 h-14 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
          <i class="fa-solid fa-file-medical"></i>
        </div>
        <div class="flex-1">
          <p class="text-gray-500 text-xs">Preview</p>
          <h3 class="text-base font-semibold text-gray-900">Result Template</h3>
          <p class="text-gray-500 text-sm">UI-only sample of result card</p>
        </div>
        <div>
          <span class="inline-flex items-center px-3 py-1 rounded-lg border text-sm text-gray-700 border-gray-300"><i class="fa-solid fa-file-lines mr-2"></i> Open</span>
        </div>
      </div>
    </div>
  </a>

  <a href="{{ route('opd.create') }}" class="block group">
    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition h-full">
      <div class="flex items-center gap-3">
        <div class="w-14 h-14 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
          <i class="fa-solid fa-plus"></i>
        </div>
        <div class="flex-1">
          <p class="text-gray-500 text-xs">Action</p>
          <h3 class="text-base font-semibold text-gray-900">Create New</h3>
          <p class="text-gray-500 text-sm">Add new medical test or appointment</p>
        </div>
        <div>
          <span class="inline-flex items-center px-3 py-1 rounded-lg border text-sm text-green-700 border-green-300 bg-green-50"><i class="fa-solid fa-plus mr-2"></i> Create</span>
        </div>
      </div>
    </div>
  </a>

  <a href="{{ route('opd.show') }}" class="block group">
    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition h-full">
      <div class="flex items-center gap-3">
        <div class="w-14 h-14 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div class="flex-1">
          <p class="text-gray-500 text-xs">View</p>
          <h3 class="text-base font-semibold text-gray-900">My Appointments</h3>
          <p class="text-gray-500 text-sm">View your booked tests and appointments</p>
        </div>
        <div>
          <span class="inline-flex items-center px-3 py-1 rounded-lg border text-sm text-purple-700 border-purple-300 bg-purple-50"><i class="fa-solid fa-eye mr-2"></i> View</span>
        </div>
      </div>
    </div>
  </a>

 
@endsection





  
