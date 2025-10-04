{{ ... }}

@section('title', 'Annual Physical Examination Results')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-white">Annual Physical Medical Report</h1>
                            <p class="text-blue-100 text-sm">Patient: {{ $examination->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Completed & Sent
                        </span>
                        <a href="{{ route('company.medical-results', ['status' => 'sent_results']) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg text-sm font-medium hover:bg-white/30 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Message -->
        @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <span class="text-blue-800 font-medium">{{ session('info') }}</span>
            </div>
        </div>
        @endif

        <!-- Patient Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Patient Information -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-900">Patient Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Patient Name</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->name }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-green-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Patient ID</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->patient_id }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Examination Date</label>
                            <div class="text-base font-semibold text-gray-900">
                                @if($examination->date)
                                    @php
                                        $examDate = \Carbon\Carbon::parse($examination->date);
                                    @endphp
                                    {{ $examDate->format('F j, Y \a\t g:i A') }}
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-orange-400">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Report Sent</label>
                            <div class="text-base font-semibold text-gray-900">{{ $examination->updated_at->format('F j, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-green-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-900">Billing Summary</h2>
                    </div>
                </div>
                <div class="p-6">
                    @php
                        $totalAmount = 0;
                        $selectedTests = [];
                        
                        // Get tests from the appointment
                        if ($examination->patient && $examination->patient->appointment) {
                            $appointment = $examination->patient->appointment;
                            if ($appointment->medicalTest) {
                                $selectedTests[] = [
                                    'category' => $appointment->medicalTestCategory->name ?? 'Annual Physical',
                                    'test' => $appointment->medicalTest->name,
                                    'price' => $appointment->medicalTest->price ?? 0
                                ];
                                $totalAmount += $appointment->medicalTest->price ?? 0;
                            }
                            
                            // Use stored total_price if available and different
                            if ($appointment->total_price > 0) {
                                $totalAmount = $appointment->total_price;
                            }
                        }
                    @endphp
                    
                    @if(!empty($selectedTests))
                        @foreach($selectedTests as $test)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $test['test'] }}</p>
                                <p class="text-sm text-gray-500">{{ $test['category'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₱{{ number_format($test['price'], 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-base font-medium text-gray-900">Total Amount</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-green-600">₱{{ number_format($totalAmount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-receipt text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No billing information available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200 rounded-t-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-purple-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Patient Medical History</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-virus text-red-600 mr-2"></i>
                            <label class="block text-sm font-semibold text-red-900">Past Illness & Diseases</label>
                        </div>
                        <div class="text-sm text-red-800">{{ $examination->illness_history ?: 'No past illness recorded' }}</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 border-l-4 border-orange-400">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-ambulance text-orange-600 mr-2"></i>
                            <label class="block text-sm font-semibold text-orange-900">Accidents & Operations</label>
                        </div>
                        <div class="text-sm text-orange-800">{{ $examination->accidents_operations ?: 'None reported' }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-400">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-notes-medical text-yellow-600 mr-2"></i>
                            <label class="block text-sm font-semibold text-yellow-900">General Medical History</label>
                        </div>
                        <div class="text-sm text-yellow-800">{{ $examination->past_medical_history ?: 'No major medical issues' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family History & Personal Habits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200 rounded-t-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Family History & Personal Habits</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Family History -->
                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-dna text-blue-600 mr-2"></i>
                            <h3 class="text-base font-semibold text-blue-900">Family Medical History</h3>
                        </div>
                        @if($examination->family_history && count($examination->family_history) > 0)
                            <div class="space-y-2">
                                @foreach($examination->family_history as $condition)
                                    <span class="inline-block px-3 py-1 bg-blue-200 text-blue-900 rounded-full text-sm font-medium mr-2 mb-2">
                                        {{ ucwords(str_replace('_', ' ', $condition)) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center text-blue-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <p class="text-sm">No family history recorded</p>
                            </div>
                        @endif
                    </div>

                    <!-- Personal Habits -->
                    <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-400">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-leaf text-green-600 mr-2"></i>
                            <h3 class="text-base font-semibold text-green-900">Personal Habits & Lifestyle</h3>
                        </div>
                        @if($examination->personal_habits && count($examination->personal_habits) > 0)
                            <div class="space-y-2">
                                @foreach($examination->personal_habits as $habit)
                                    <span class="inline-block px-3 py-1 bg-green-200 text-green-900 rounded-full text-sm font-medium mr-2 mb-2">
                                        {{ ucwords(str_replace('_', ' ', $habit)) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <p class="text-sm">No personal habits recorded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Collapsible Medical Report -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <button onclick="toggleMedicalReport()" class="flex items-center justify-between w-full text-left">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-stethoscope text-teal-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-900">Detailed Medical Report</h2>
                    </div>
                    <i id="medicalReportIcon" class="fas fa-chevron-down text-gray-400 transition-transform duration-200"></i>
                </button>
            </div>
            <div id="medicalReportContent" class="hidden">
                <!-- Physical Examination -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Physical Examination</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visual Acuity</label>
                            <div class="text-sm text-gray-900">{{ $examination->visual ?: 'Not tested' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ishihara Test</label>
                            <div class="text-sm text-gray-900">{{ $examination->ishihara_test ?: 'Not tested' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">ECG</label>
                            <div class="text-sm text-gray-900">{{ $examination->ecg ?: 'Not performed' }}</div>
                        </div>
                    </div>
                    @if($examination->skin_marks)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skin Marks/Tattoos</label>
                        <div class="text-sm text-gray-900">{{ $examination->skin_marks }}</div>
                    </div>
                    @endif
                </div>

                <!-- Physical Findings -->
                @if($examination->physical_findings && count($examination->physical_findings) > 0)
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Physical Findings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($examination->physical_findings as $area => $finding)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $area }}</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $finding['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $finding['result'] }}
                                </span>
                            </div>
                            @if(isset($finding['findings']) && $finding['findings'])
                                <p class="text-sm text-gray-700">{{ $finding['findings'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Laboratory Findings -->
                @if($examination->lab_findings && count($examination->lab_findings) > 0)
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Laboratory Results</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($examination->lab_findings as $test => $result)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $test }}</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $result['result'] === 'Normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $result['result'] }}
                                </span>
                            </div>
                            @if(isset($result['findings']) && $result['findings'])
                                <p class="text-sm text-gray-700">{{ $result['findings'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Final Findings -->
                @if($examination->findings)
                <div class="p-6">
                    <h3 class="text-base font-medium text-gray-900 mb-4">Doctor's Findings</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-900">{{ $examination->findings }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Report Footer -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-certificate text-gray-600 text-sm"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">Report Information</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Generated By</label>
                        <div class="text-sm text-gray-900">RSS Citi Health Services</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Date</label>
                        <div class="text-sm text-gray-900">{{ $examination->updated_at->format('F j, Y g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMedicalReport() {
    const content = document.getElementById('medicalReportContent');
    const icon = document.getElementById('medicalReportIcon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection
