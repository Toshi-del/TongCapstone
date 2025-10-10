<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\AppointmentConfirmation;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\MedicalTest;
use App\Models\OpdTest;

class OpdController extends Controller
{
    public function dashboard()
    {
        $incoming = session('opd_incoming_tests', []);
        $total = collect($incoming)->sum(function($item){ return (float)($item['price'] ?? 0); });
        return view('opd.dashboard', compact('incoming', 'total'));
    }

    public function medicalTestCategories()
    {
        $categories = \App\Models\MedicalTestCategory::with('medicalTests')
            ->orderByRaw("CASE WHEN LOWER(name) LIKE '%pre-employment%' OR LOWER(name) LIKE '%pre employment%' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(15);
        return view('opd.medical-test-categories', compact('categories'));
    }

    public function medicalTests()
    {
        $tests = \App\Models\MedicalTest::with('category')->orderBy('name')->paginate(15);
        return view('opd.medical-tests', compact('tests'));
    }

    public function incomingTests()
    {
        $incoming = session('opd_incoming_tests', []);
        $total = collect($incoming)->sum(function($item){ return (float)($item['price'] ?? 0); });
        return view('opd.incoming-tests', compact('incoming', 'total'));
    }

    public function addIncomingTest($testId)
    {
        $test = \App\Models\MedicalTest::with('category')->findOrFail($testId);
        $incoming = session('opd_incoming_tests', []);

        $appointmentData = [
            'customer_name' => request('customer_name'),
            'customer_email' => request('customer_email'),
            'appointment_date' => request('appointment_date'),
            'appointment_time' => request('appointment_time'),
        ];

        $testDetails = [
            'id' => $test->id,
            'name' => $test->name,
            'category' => optional($test->category)->name,
            'price' => (float)($test->price ?? 0),
        ];

        $incoming[$test->id] = array_merge($testDetails, $appointmentData);

        session(['opd_incoming_tests' => $incoming]);

        // Persist to OPD table
        DB::table('opd_tests')->insert([
            'customer_name' => $appointmentData['customer_name'],
            'customer_email' => $appointmentData['customer_email'],
            'medical_test' => $test->name,
            'appointment_date' => $appointmentData['appointment_date'] ?: null,
            'appointment_time' => $appointmentData['appointment_time'] ?: null,
            'price' => $testDetails['price'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email confirmation if customer email is provided
        if (!empty($appointmentData['customer_email']) && filter_var($appointmentData['customer_email'], FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($appointmentData['customer_email'])
                    ->send(new AppointmentConfirmation($appointmentData, $testDetails));
                
                return back()->with('success', 'Added to Incoming Tests and confirmation email sent!');
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error('Failed to send appointment confirmation email: ' . $e->getMessage());
                return back()->with('success', 'Added to Incoming Tests (email could not be sent)');
            }
        }

        return back()->with('success', 'Added to Incoming Tests');
    }

    public function removeIncomingTest($testId)
    {
        $incoming = session('opd_incoming_tests', []);
        unset($incoming[$testId]);
        session(['opd_incoming_tests' => $incoming]);
        return back()->with('success', 'Removed from Incoming Tests');
    }

    public function result()
    {
        $user = auth()->user();
        
        // Get OPD examination results that have been sent to this user
        $opdExaminations = \App\Models\OpdExamination::where('user_id', $user->id)
            ->whereIn('status', ['sent_to_patient', 'approved'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get the latest examination for display
        $latestExamination = $opdExaminations->first();
        
        // If no examination results available, show message
        if (!$latestExamination) {
            return view('opd.result', [
                'opdExaminations' => collect(),
                'latestExamination' => null,
                'hasResults' => false,
                'message' => 'No examination results available yet. Please check back later.'
            ]);
        }
        
        return view('opd.result', [
            'opdExaminations' => $opdExaminations,
            'latestExamination' => $latestExamination,
            'hasResults' => true,
            'patientName' => $latestExamination->name ?? $user->name,
            'examDate' => $latestExamination->date ? $latestExamination->date->format('M d, Y') : null
        ]);
    }

    /**
     * Show the create page for new medical tests and appointments
     */
    public function create()
    {
        $categories = \App\Models\MedicalTestCategory::with(['medicalTests' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
        
        return view('opd.create', compact('categories'));
    }

    /**
     * Show booked appointments and test details
     */
    public function show()
    {
        $user = auth()->user();
        
        // Get appointments and statistics using the model
        $appointments = OpdTest::forCustomer($user->email)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group appointments by date and time for better display
        $groupedAppointments = OpdTest::getGroupedAppointments($user->email);
        
        // Get statistics using the model
        $statistics = OpdTest::getStatistics($user->email);
        
        return view('opd.show', compact(
            'appointments', 
            'groupedAppointments'
        ) + $statistics);
    }

    /**
     * Book appointment with selected tests
     */
    public function bookAppointment(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Booking request data:', $request->all());
        
        // Debug: Check database connection and table
        \Log::info('Database connection:', ['connection' => DB::connection()->getName()]);
        \Log::info('Database name:', ['database' => DB::connection()->getDatabaseName()]);
        
        // Check if table exists and current record count
        try {
            $tableExists = DB::getSchemaBuilder()->hasTable('opd_tests');
            $currentCount = DB::table('opd_tests')->count();
            \Log::info('Table info:', ['exists' => $tableExists, 'current_count' => $currentCount]);
        } catch (\Exception $e) {
            \Log::error('Table check failed:', ['error' => $e->getMessage()]);
        }
        
        // Handle JSON string in selected_tests if needed
        $selectedTests = $request->selected_tests;
        if (is_string($selectedTests)) {
            $selectedTests = json_decode($selectedTests, true);
        }
        
        // Update request with decoded tests
        $request->merge(['selected_tests' => $selectedTests]);
        
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
            'selected_tests' => 'required|array|min:1',
            'selected_tests.*.id' => 'required',
            'selected_tests.*.name' => 'required|string',
            'selected_tests.*.price' => 'required|numeric|min:0'
        ]);

        try {
            // Combine all selected tests into one record
            $testNames = collect($request->selected_tests)->pluck('name')->toArray();
            $totalPrice = collect($request->selected_tests)->sum('price');
            $combinedTestNames = implode(', ', $testNames);
            
            \Log::info('Creating combined OPD appointment:', [
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'combined_tests' => $combinedTestNames,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'total_price' => $totalPrice,
            ]);
            
            try {
                // Create one record with all tests combined
                $opdTest = OpdTest::create([
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'medical_test' => $combinedTestNames,
                    'appointment_date' => $request->appointment_date,
                    'appointment_time' => $request->appointment_time,
                    'price' => $totalPrice,
                ]);
                
                \Log::info('Combined appointment created successfully:', ['id' => $opdTest->id]);
                $successCount = 1;
                $errors = [];
                
            } catch (\Exception $e) {
                \Log::error('Failed to create combined appointment:', ['error' => $e->getMessage()]);
                $errors[] = "Failed to book appointment: " . $e->getMessage();
                $successCount = 0;
            }

            if ($successCount > 0) {
                $testCount = count($request->selected_tests);
                return response()->json([
                    'success' => true,
                    'message' => "Successfully booked appointment with {$testCount} medical test(s) for {$request->appointment_date} at {$request->appointment_time}",
                    'booked_count' => $successCount,
                    'test_count' => $testCount,
                    'errors' => $errors
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to book any tests',
                    'errors' => $errors
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the OPD account creation form
     */
    public function showCreateAccount()
    {
        return view('opd.create-account');
    }

    /**
     * Handle OPD account creation with default role 'opd'
     */
    public function createAccount(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'birthday' => 'required|date',
            'company' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Additional duplicate prevention checks
        $firstName = trim($request->fname);
        $lastName = trim($request->lname);
        $email = trim($request->email);
        $phone = trim($request->phone);

        // Check for existing user with same name and email combination
        $existingUser = User::where('fname', $firstName)
            ->where('lname', $lastName)
            ->where('email', $email)
            ->first();

        if ($existingUser) {
            return back()->withErrors([
                'email' => 'A user with the same name and email already exists. Please use a different email or contact support if this is an error.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Check for existing user with same name and phone combination
        $existingUserByPhone = User::where('fname', $firstName)
            ->where('lname', $lastName)
            ->where('phone', $phone)
            ->first();

        if ($existingUserByPhone) {
            return back()->withErrors([
                'phone' => 'A user with the same name and phone number already exists. Please use a different phone number or contact support if this is an error.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Calculate age
        $birthday = Carbon::parse($request->birthday);
        $age = $birthday->age;

        $user = User::create([
            'fname' => $firstName,
            'lname' => $lastName,
            'mname' => $request->mname,
            'email' => $email,
            'phone' => $phone,
            'birthday' => $request->birthday,
            'age' => $age,
            'company' => $request->company,
            'role' => 'opd', // Default role for OPD accounts
            'password' => Hash::make($request->password),
            'created_by' => auth()->id(), // Track who created this account
        ]);

        // Create notification for admin when OPD patient registers
        Notification::createForAdmin(
            'patient_registered',
            'New OPD Patient Registered',
            "New OPD walk-in patient {$user->full_name} has registered for medical examination. Age: {$age}, Company: " . ($request->company ?: 'Individual'),
            [
                'patient_id' => $user->id,
                'patient_name' => $user->full_name,
                'patient_age' => $age,
                'patient_company' => $request->company,
                'registration_type' => 'opd_walkin',
                'email' => $email,
                'phone' => $phone
            ],
            'medium',
            null, // System generated
            $user
        );

        return redirect()->route('opd.dashboard')->with('success', 'OPD account created successfully for ' . $user->full_name . '!');
    }

    /**
     * Show reschedule form for an appointment group
     */
    public function showReschedule(Request $request)
    {
        $user = auth()->user();
        $appointmentDate = $request->get('date');
        $appointmentTime = $request->get('time');
        
        // Get appointments for this date/time combination
        $appointments = OpdTest::forCustomer($user->email)
            ->forDate($appointmentDate)
            ->forTime($appointmentTime)
            ->get();
            
        if ($appointments->isEmpty()) {
            return redirect()->route('opd.show')->with('error', 'Appointment not found.');
        }
        
        return view('opd.reschedule', compact('appointments', 'appointmentDate', 'appointmentTime'));
    }

    /**
     * Reschedule appointments
     */
    public function reschedule(Request $request)
    {
        $request->validate([
            'old_date' => 'required|date',
            'old_time' => 'required|string',
            'new_date' => 'required|date|after_or_equal:today',
            'new_time' => 'required|string',
        ]);

        $user = auth()->user();
        
        try {
            // Update all appointments for this date/time combination
            $updated = OpdTest::forCustomer($user->email)
                ->forDate($request->old_date)
                ->forTime($request->old_time)
                ->update([
                    'appointment_date' => $request->new_date,
                    'appointment_time' => $request->new_time,
                ]);

            if ($updated > 0) {
                return redirect()->route('opd.show')->with('success', "Successfully rescheduled {$updated} test(s) to {$request->new_date} at {$request->new_time}");
            } else {
                return redirect()->route('opd.show')->with('error', 'No appointments found to reschedule.');
            }
        } catch (\Exception $e) {
            return redirect()->route('opd.show')->with('error', 'Failed to reschedule appointments: ' . $e->getMessage());
        }
    }

    /**
     * Cancel appointments
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
        ]);

        $user = auth()->user();
        
        try {
            // Delete all appointments for this date/time combination
            $deleted = OpdTest::forCustomer($user->email)
                ->forDate($request->appointment_date)
                ->forTime($request->appointment_time)
                ->delete();

            if ($deleted > 0) {
                return redirect()->route('opd.show')->with('success', "Successfully cancelled {$deleted} test(s) for {$request->appointment_date} at {$request->appointment_time}");
            } else {
                return redirect()->route('opd.show')->with('error', 'No appointments found to cancel.');
            }
        } catch (\Exception $e) {
            return redirect()->route('opd.show')->with('error', 'Failed to cancel appointments: ' . $e->getMessage());
        }
    }

    /**
     * Download results for completed appointments (placeholder)
     */
    public function downloadResults(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
        ]);

        $user = auth()->user();
        
        // Get appointments for this date/time combination
        $appointments = OpdTest::forCustomer($user->email)
            ->forDate($request->appointment_date)
            ->forTime($request->appointment_time)
            ->get();
            
        if ($appointments->isEmpty()) {
            return redirect()->route('opd.show')->with('error', 'Appointment not found.');
        }
        
        // For now, redirect back with a message since results system isn't implemented yet
        return redirect()->route('opd.show')->with('info', 'Results download feature is coming soon. Please contact the clinic for your test results.');
    }

    /**
     * Clean up invalid appointments (those without proper date/time)
     */
    public function cleanupInvalidAppointments()
    {
        $user = auth()->user();
        
        try {
            // Delete appointments where date or time is null/empty
            $deleted = OpdTest::forCustomer($user->email)
                ->where(function($query) {
                    $query->whereNull('appointment_date')
                          ->orWhereNull('appointment_time')
                          ->orWhere('appointment_date', '')
                          ->orWhere('appointment_time', '');
                })
                ->delete();

            if ($deleted > 0) {
                return redirect()->route('opd.show')->with('success', "Successfully removed {$deleted} invalid appointment(s).");
            } else {
                return redirect()->route('opd.show')->with('info', 'No invalid appointments found to remove.');
            }
        } catch (\Exception $e) {
            return redirect()->route('opd.show')->with('error', 'Failed to cleanup appointments: ' . $e->getMessage());
        }
    }
}






