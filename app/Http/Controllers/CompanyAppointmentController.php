<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\MedicalTest;
use App\Models\MedicalTestCategory;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CompanyAppointmentController extends Controller
{
    public function index()
    {
        // Auto-cancel expired appointments that are still pending
        $today = Carbon::today();
        $expiredAppointments = Appointment::where('created_by', Auth::id())
            ->where('appointment_date', '<', $today)
            ->where('status', 'pending')
            ->get();
        
        $cancelledCount = 0;
        foreach ($expiredAppointments as $expiredAppointment) {
            $expiredAppointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Automatically cancelled - appointment date has passed',
                'cancelled_at' => now()
            ]);
            $cancelledCount++;
        }
        
        // Add flash message if appointments were cancelled
        if ($cancelledCount > 0) {
            session()->flash('info', "Automatically cancelled {$cancelledCount} expired appointment(s) that had passed today's date.");
        }
        
        $appointments = Appointment::with(['patients'])->where('created_by', Auth::id())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();
        
        // Get all booked dates (from all companies) to prevent double booking
        $allBookedDates = Appointment::whereNotNull('appointment_date')
            ->pluck('appointment_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->toArray();
        
        return view('company.appointments.index', compact('appointments', 'allBookedDates'));
    }

    public function create(Request $request)
    {
        // Check if date parameter is provided
        if (!$request->has('date')) {
            return redirect()->route('company.appointments.index')
                ->with('error', 'Please select a date from the calendar.');
        }

        // Get medical tests grouped by category (deduplicated)
        $medicalTestCategories = MedicalTestCategory::with(['medicalTests' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order')->distinct();
        }])->where('is_active', true)->orderBy('sort_order')->distinct()->get();

        $timeSlots = [
            '8:00 AM', '8:30 AM', '9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM',
            '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', '1:00 PM', '1:30 PM',
            '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM', '4:00 PM'
        ];
        
        // Get booked dates for frontend validation
        $bookedDates = Appointment::pluck('appointment_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
        
        // Calculate minimum date (4 days from today)
        $minDate = Carbon::now()->addDays(4)->format('Y-m-d');

        return view('company.appointments.create', compact('medicalTestCategories', 'timeSlots', 'bookedDates', 'minDate'));
    }

    public function store(Request $request)
    {
        // Calculate minimum date (4 days from today)
        $minDate = Carbon::now()->addDays(4)->format('Y-m-d');
        
        $request->validate([
            'appointment_date' => 'nullable|date|after_or_equal:' . $minDate,
            'time_slot' => 'required|string',
            'medical_test_categories_id' => 'required|string',
            'medical_test_id' => 'required|string',
            'notes' => 'nullable|string',
            'excel_file' => 'nullable|file|mimes:xlsx,xls',
        ], [
            'appointment_date.after_or_equal' => 'Appointments must be scheduled at least 4 days in advance.',
        ]);

        // Decode JSON arrays from frontend
        $categoryIds = array_map('intval', json_decode($request->medical_test_categories_id, true) ?: []);
        $testIds = array_map('intval', json_decode($request->medical_test_id, true) ?: []);

        // Validate that we have at least one selection
        if (empty($categoryIds) || empty($testIds)) {
            return back()->withInput()->withErrors([
                'medical_test_categories_id' => 'Please select at least one medical test.',
                'medical_test_id' => 'Please select at least one medical test.'
            ]);
        }

        // Validate that arrays have same length (one test per category)
        if (count($categoryIds) !== count($testIds)) {
            return back()->withInput()->withErrors([
                'medical_test_categories_id' => 'Invalid test selection. Please refresh and try again.',
                'medical_test_id' => 'Invalid test selection. Please refresh and try again.'
            ]);
        }

        // DEBUG: Log the category validation
        \Log::info("Category validation debug", [
            "submitted_categories" => $categoryIds,
            "category_count" => count($categoryIds)
        ]);
        
        // Validate that all category IDs exist (check unique categories only)
        $uniqueCategoryIds = array_unique($categoryIds);
        $validCategoryIds = array_map('intval', MedicalTestCategory::whereIn('id', $uniqueCategoryIds)->pluck('id')->toArray());
        \Log::info("Category validation results", [
            "valid_categories" => $validCategoryIds,
            "valid_count" => count($validCategoryIds),
            "unique_submitted_count" => count($uniqueCategoryIds),
            "counts_match" => count($validCategoryIds) === count($uniqueCategoryIds)
        ]);
        if (count($validCategoryIds) !== count($uniqueCategoryIds)) {
            return back()->withInput()->withErrors([
                'medical_test_categories_id' => 'One or more selected categories are invalid.'
            ]);
        }

        // Validate that all test IDs exist
        $validTestIds = array_map('intval', MedicalTest::whereIn('id', $testIds)->pluck('id')->toArray());
        if (count($validTestIds) !== count($testIds)) {
            return back()->withInput()->withErrors([
                'medical_test_id' => 'One or more selected tests are invalid.'
            ]);
        }

        try {
            $appointmentDate = $request->appointment_date ?? $request->query('date');
            if (empty($appointmentDate)) {
                return back()->withInput()->with('error', 'Appointment date is required. Please select a date.');
            }
            
            // Ensure the date is in the correct format
            $appointmentDate = Carbon::parse($appointmentDate)->format('Y-m-d');
            
            // Check if the selected date is a Sunday (Saturday = 6, Sunday = 0)
            $dayOfWeek = Carbon::parse($appointmentDate)->dayOfWeek;
            if ($dayOfWeek == 0) {
                return back()->withInput()->with('error', 'Appointments cannot be scheduled on Sundays. Please select Monday-Saturday.');
            }

            // Check if any company has already booked this date (no double booking per day)
            $existingAppointment = Appointment::where('appointment_date', $appointmentDate)
                ->first();

            if ($existingAppointment) {
                return back()->withInput()->with('error', 'This date is not available. Another company has already booked an appointment on this date. Please choose a different date.');
            }

            // Validate that each test belongs to its corresponding category and calculate total price
            $totalPrice = 0;
            for ($i = 0; $i < count($testIds); $i++) {
                $selectedTest = MedicalTest::find($testIds[$i]);
                if (!$selectedTest || (int) $selectedTest->medical_test_category_id !== (int) $categoryIds[$i]) {
                    return back()->withInput()->withErrors(['medical_test_id' => 'One or more selected tests do not belong to their chosen categories.']);
                }
                $totalPrice += $selectedTest->price ?? 0;
            }

            // Store all selected category and test IDs as JSON strings
            $appointment = Appointment::create([
                'appointment_date' => $appointmentDate,
                'time_slot' => $request->time_slot,
                'medical_test_categories_id' => json_encode($categoryIds),
                'medical_test_id' => json_encode($testIds),
                'total_price' => $totalPrice,
                'notes' => $request->notes,
                'patients_data' => [], // Will be populated when Excel is processed
                'excel_file_path' => null, // Will be set if file is uploaded
                'created_by' => Auth::id(),
                'status' => 'pending',
            ]);

            // Handle Excel file upload and patient creation
            if ($request->hasFile('excel_file')) {
                $file = $request->file('excel_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('appointments', $fileName, 'public');
                
                // Update appointment with file path
                $appointment->update(['excel_file_path' => $filePath]);
                
                // Process Excel file and create patients
                $this->processExcelFile($file, $appointment);
                
                \Log::info('Excel file processed for appointment', [
                    'appointment_id' => $appointment->id,
                    'file_path' => $filePath,
                    'patients_count' => $appointment->patients()->count()
                ]);
            }

            // Create notification for admin
            $companyUser = Auth::user();
            $patientCount = $appointment->patients()->count();
            $testNames = [];
            
            // Get test names for notification
            for ($i = 0; $i < count($testIds); $i++) {
                $test = MedicalTest::find($testIds[$i]);
                if ($test) {
                    $testNames[] = $test->name;
                }
            }
            
            Notification::createForAdmin(
                'appointment_created',
                'New Appointment Created',
                "Company '{$companyUser->company}' has created a new appointment for " . Carbon::parse($appointmentDate)->format('M d, Y') . " with {$patientCount} patient(s). Tests: " . implode(', ', $testNames),
                [
                    'appointment_id' => $appointment->id,
                    'company_name' => $companyUser->company,
                    'appointment_date' => $appointmentDate,
                    'patient_count' => $patientCount,
                    'tests' => $testNames,
                    'total_price' => $totalPrice
                ],
                'medium',
                $companyUser,
                $appointment
            );

            return redirect()->route('company.appointments.index')
                ->with('success', 'Appointment created successfully.');

        } catch (\Exception $e) {
            \Log::error('Error creating appointment', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Error creating appointment: ' . $e->getMessage());
        }
    }

    private function processExcelFile($file, $appointment)
    {
        try {
            \Log::info('Starting Excel file processing', [
                'appointment_id' => $appointment->id,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize()
            ]);

            // Get the company name from the appointment creator
            $companyUser = \App\Models\User::find($appointment->created_by);
            $companyName = $companyUser ? $companyUser->company : null;
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            \Log::info('Excel file loaded', [
                'appointment_id' => $appointment->id,
                'total_rows' => count($rows)
            ]);
            
            // Skip header row
            array_shift($rows);
            
            $processedCount = 0;
            $skippedCount = 0;
            
            foreach ($rows as $index => $row) {
                \Log::info('Processing row', [
                    'appointment_id' => $appointment->id,
                    'row_index' => $index,
                    'row_data' => $row
                ]);
                
                if (empty($row[0]) && empty($row[1])) {
                    \Log::info('Skipping empty row', ['row_index' => $index]);
                    continue; // Skip empty rows
                }
                
                // Validate required fields
                if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                    \Log::info('Skipping invalid row - missing required fields', [
                        'row_index' => $index,
                        'row_data' => $row
                    ]);
                    continue; // Skip invalid rows
                }
                
                $firstName = trim($row[0]);
                $lastName = trim($row[1]);
                $email = !empty($row[4]) ? trim($row[4]) : null;
                $address = !empty($row[6]) ? trim($row[6]) : null;
                
                // Check for duplicate patients (same first name, last name, and email) for this specific appointment
                $existingPatientForAppointment = Patient::where('first_name', $firstName)
                    ->where('last_name', $lastName)
                    ->where('appointment_id', $appointment->id);
                
                if ($email) {
                    $existingPatientForAppointment = $existingPatientForAppointment->where('email', $email);
                } else {
                    $existingPatientForAppointment = $existingPatientForAppointment->whereNull('email');
                }
                
                $existingPatientForAppointment = $existingPatientForAppointment->first();
                
                if (!$existingPatientForAppointment) {
                    // Check if patient exists globally (across all appointments)
                    $globalExistingPatient = Patient::where('first_name', $firstName)
                        ->where('last_name', $lastName);
                    
                    if ($email) {
                        $globalExistingPatient = $globalExistingPatient->where('email', $email);
                    } else {
                        $globalExistingPatient = $globalExistingPatient->whereNull('email');
                    }
                    
                    $globalExistingPatient = $globalExistingPatient->first();
                    
                    if (!$globalExistingPatient) {
                        // Create new patient record
                        Patient::create([
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'age' => (int) $row[2],
                            'sex' => trim($row[3]),
                            'email' => $email,
                            'phone' => !empty($row[5]) ? trim($row[5]) : null,
                            'address' => $address,
                            'appointment_id' => $appointment->id,
                            'company_name' => $companyName,
                        ]);
                        $processedCount++;
                    } else {
                        // Patient exists globally but not for this appointment, create a new record for this appointment
                        Patient::create([
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'age' => (int) $row[2],
                            'sex' => trim($row[3]),
                            'email' => $email,
                            'phone' => !empty($row[5]) ? trim($row[5]) : null,
                            'address' => $address,
                            'appointment_id' => $appointment->id,
                            'company_name' => $companyName,
                        ]);
                        $processedCount++;
                    }
                } else {
                    // Patient already exists for this appointment, skip
                    $skippedCount++;
                }
            }
            
            // Get total patient count for this appointment
            $totalPatients = Patient::where('appointment_id', $appointment->id)->count();
            
            // Update appointment with patient data
            $appointment->update([
                'patients_data' => [
                    'count' => $totalPatients,
                    'processed' => $processedCount,
                    'skipped' => $skippedCount
                ]
            ]);
            
            \Log::info('Excel processing completed', [
                'appointment_id' => $appointment->id,
                'total_patients' => $totalPatients,
                'processed' => $processedCount,
                'skipped' => $skippedCount
            ]);
            
        } catch (\Exception $e) {
            throw new \Exception('Error processing Excel file: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $appointment = Appointment::with(['patients'])->where('created_by', Auth::id())
            ->findOrFail($id);
        
        return view('company.appointments.show', compact('appointment'));
    }

    public function edit($id)
    {
        $appointment = Appointment::where('created_by', Auth::id())
            ->findOrFail($id);
        
        // Get medical tests grouped by category (deduplicated)
        $medicalTestCategories = MedicalTestCategory::with(['medicalTests' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order')->distinct();
        }])->where('is_active', true)->orderBy('sort_order')->distinct()->get();

        $timeSlots = [
            '8:00 AM', '8:30 AM', '9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM',
            '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', '1:00 PM', '1:30 PM',
            '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM', '4:00 PM'
        ];
        
        // Get booked dates for frontend validation (excluding current appointment)
        $bookedDates = Appointment::where('id', '!=', $id)
            ->pluck('appointment_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();
        
        // For editing, we only restrict past dates, not the 4-day advance rule
        $minDate = Carbon::today()->format('Y-m-d');

        return view('company.appointments.edit', compact('appointment', 'medicalTestCategories', 'timeSlots', 'bookedDates', 'minDate'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::where('created_by', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'appointment_date' => 'required|date',
            'time_slot' => 'required|string',
            'medical_test_categories_id' => 'required|string',
            'medical_test_id' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Decode JSON arrays from frontend
        $categoryIds = array_map('intval', json_decode($request->medical_test_categories_id, true) ?: []);
        $testIds = array_map('intval', json_decode($request->medical_test_id, true) ?: []);

        // Validate that we have at least one selection
        if (empty($categoryIds) || empty($testIds)) {
            return back()->withInput()->withErrors([
                'medical_test_categories_id' => 'Please select at least one medical test.',
                'medical_test_id' => 'Please select at least one medical test.'
            ]);
        }

        // Validate that arrays have same length (one test per category)
        if (count($categoryIds) !== count($testIds)) {
            return back()->withInput()->withErrors([
                'medical_test_categories_id' => 'Invalid test selection. Please refresh and try again.',
                'medical_test_id' => 'Invalid test selection. Please refresh and try again.'
            ]);
        }

        // DEBUG: Log the category validation
        \Log::info("Category validation debug", [
            "submitted_categories" => $categoryIds,
            "category_count" => count($categoryIds)
        ]);
        
        // Validate that all category IDs exist (check unique categories only)
        $uniqueCategoryIds = array_unique($categoryIds);
        $validCategoryIds = array_map('intval', MedicalTestCategory::whereIn('id', $uniqueCategoryIds)->pluck('id')->toArray());
        \Log::info("Category validation results", [
            "valid_categories" => $validCategoryIds,
            "valid_count" => count($validCategoryIds),
            "unique_submitted_count" => count($uniqueCategoryIds),
            "counts_match" => count($validCategoryIds) === count($uniqueCategoryIds)
        ]);
        if (count($validCategoryIds) !== count($uniqueCategoryIds)) {
            return back()->withInput()->withErrors([
                'medical_test_categories_id' => 'One or more selected categories are invalid.'
            ]);
        }

        // Validate that all test IDs exist
        $validTestIds = array_map('intval', MedicalTest::whereIn('id', $testIds)->pluck('id')->toArray());
        if (count($validTestIds) !== count($testIds)) {
            return back()->withInput()->withErrors([
                'medical_test_id' => 'One or more selected tests are invalid.'
            ]);
        }

        // For existing appointments, we don't enforce the 4-day advance rule
        // since they were already created with proper advance notice
        // Only validate that the date is not in the past
        $today = Carbon::today()->format('Y-m-d');
        if ($request->appointment_date < $today) {
            return back()->withInput()->with('error', 'Appointments cannot be scheduled for past dates.');
        }
        
        // Check if the selected date is a Sunday (Saturday = 6, Sunday = 0)
        $dayOfWeek = Carbon::parse($request->appointment_date)->dayOfWeek;
        if ($dayOfWeek == 0) {
            return back()->withInput()->with('error', 'Appointments cannot be scheduled on Sundays. Please select Monday-Saturday.');
        }
        
        // Check if any company has already booked this date (no double booking per day, excluding current appointment)
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingAppointment) {
            return back()->withInput()->with('error', 'This date is not available. Another company has already booked an appointment on this date. Please choose a different date.');
        }

        // Validate that each test belongs to its corresponding category and calculate total price
        $totalPrice = 0;
        for ($i = 0; $i < count($testIds); $i++) {
            $selectedTest = MedicalTest::find($testIds[$i]);
            if (!$selectedTest || (int) $selectedTest->medical_test_category_id !== (int) $categoryIds[$i]) {
                return back()->withInput()->withErrors(['medical_test_id' => 'One or more selected tests do not belong to their chosen categories.']);
            }
            $totalPrice += $selectedTest->price ?? 0;
        }

        // Store all selected category and test IDs as JSON strings
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'time_slot' => $request->time_slot,
            'medical_test_categories_id' => json_encode($categoryIds),
            'medical_test_id' => json_encode($testIds),
            'total_price' => $totalPrice,
            'notes' => $request->notes,
        ]);

        return redirect()->route('company.appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy($id)
    {
        $appointment = Appointment::where('created_by', Auth::id())
            ->findOrFail($id);
        
        $appointment->delete();

        return redirect()->route('company.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
