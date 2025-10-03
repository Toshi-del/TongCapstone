<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\PreEmploymentRecord;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Mail\RegistrationInvitation;
use Illuminate\Support\Facades\Mail;
use App\Services\MedicalTestRoutingService;
use App\Models\AppointmentTestAssignment;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        // Get basic statistics
        $totalPatients = Patient::count();
        $approvedAppointments = Appointment::where('status', 'approved')->count();
        $testsToday = Appointment::whereDate('appointment_date', Carbon::today())
            ->where('status', 'approved')
            ->count();
        $totalPreEmployment = PreEmploymentRecord::count();
        
        // Get appointment statistics
        $appointmentStats = $this->getAppointmentStatistics();
        
        // Get pre-employment statistics
        $preEmploymentStats = $this->getPreEmploymentStatistics();
        
        // Get recent patients with appointments (last 5 records) and the creating company
        $patients = Patient::with([
            'appointment' => function($query) {
                $query->orderBy('appointment_date', 'desc');
            },
            'appointment.creator'
        ])
        ->whereHas('appointment')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
        
        // Get recent pre-employment records (last 5 records)
        $preEmployments = PreEmploymentRecord::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Generate chart data (provide up to 365 days so UI can slice to weekly/monthly/yearly)
        $preEmploymentChartData = $this->generatePreEmploymentChartData();
        $annualPhysicalChartData = $this->generateAnnualPhysicalChartData();

        // Get all company users
        $companyUsers = User::where('role', 'company')->orderBy('company')->get();
        $companyData = [];
        foreach ($companyUsers as $company) {
            // Patients: via appointments created by this company user
            $appointmentIds = Appointment::where('created_by', $company->id)->pluck('id');
            $patients = Patient::whereIn('appointment_id', $appointmentIds)->get();
            // Pre-employment records: created_by = company user id
            $preEmployments = PreEmploymentRecord::where('created_by', $company->id)->get();
            $companyData[] = [
                'company' => $company,
                'patients' => $patients,
                'preEmployments' => $preEmployments,
            ];
        }

        return view('admin.dashboard', compact(
            'totalPatients',
            'approvedAppointments', 
            'testsToday',
            'totalPreEmployment',
            'patients',
            'preEmployments',
            'preEmploymentChartData',
            'annualPhysicalChartData',
            'appointmentStats',
            'preEmploymentStats',
            'companyData' // <-- pass to view
        ));
    }
    
    /**
     * Get appointment statistics for weekly, monthly, and yearly
     */
    private function getAppointmentStatistics()
    {
        $now = Carbon::now();
        
        // Weekly statistics (last 7 days)
        $weeklyStats = [
            'total' => Appointment::whereBetween('created_at', [$now->copy()->subDays(7), $now])->count(),
            'approved' => Appointment::whereBetween('created_at', [$now->copy()->subDays(7), $now])
                ->where('status', 'approved')->count(),
            'pending' => Appointment::whereBetween('created_at', [$now->copy()->subDays(7), $now])
                ->where('status', 'pending')->count(),
            'cancelled' => Appointment::whereBetween('created_at', [$now->copy()->subDays(7), $now])
                ->where('status', 'cancelled')->count(),
        ];
        
        // Monthly statistics (last 30 days)
        $monthlyStats = [
            'total' => Appointment::whereBetween('created_at', [$now->copy()->subDays(30), $now])->count(),
            'approved' => Appointment::whereBetween('created_at', [$now->copy()->subDays(30), $now])
                ->where('status', 'approved')->count(),
            'pending' => Appointment::whereBetween('created_at', [$now->copy()->subDays(30), $now])
                ->where('status', 'pending')->count(),
            'cancelled' => Appointment::whereBetween('created_at', [$now->copy()->subDays(30), $now])
                ->where('status', 'cancelled')->count(),
        ];
        
        // Yearly statistics (last 365 days)
        $yearlyStats = [
            'total' => Appointment::whereBetween('created_at', [$now->copy()->subDays(365), $now])->count(),
            'approved' => Appointment::whereBetween('created_at', [$now->copy()->subDays(365), $now])
                ->where('status', 'approved')->count(),
            'pending' => Appointment::whereBetween('created_at', [$now->copy()->subDays(365), $now])
                ->where('status', 'pending')->count(),
            'cancelled' => Appointment::whereBetween('created_at', [$now->copy()->subDays(365), $now])
                ->where('status', 'cancelled')->count(),
        ];
        
        return [
            'weekly' => $weeklyStats,
            'monthly' => $monthlyStats,
            'yearly' => $yearlyStats
        ];
    }
    
    /**
     * Get pre-employment statistics for weekly, monthly, and yearly
     */
    private function getPreEmploymentStatistics()
    {
        $now = Carbon::now();
        
        // Weekly statistics (last 7 days)
        $weeklyStats = [
            'total' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(7), $now])->count(),
            'passed' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(7), $now])
                ->where('status', 'passed')->count(),
            'failed' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(7), $now])
                ->where('status', 'failed')->count(),
            'pending' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(7), $now])
                ->where('status', 'pending')->count(),
        ];
        
        // Monthly statistics (last 30 days)
        $monthlyStats = [
            'total' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(30), $now])->count(),
            'passed' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(30), $now])
                ->where('status', 'passed')->count(),
            'failed' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(30), $now])
                ->where('status', 'failed')->count(),
            'pending' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(30), $now])
                ->where('status', 'pending')->count(),
        ];
        
        // Yearly statistics (last 365 days)
        $yearlyStats = [
            'total' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(365), $now])->count(),
            'passed' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(365), $now])
                ->where('status', 'passed')->count(),
            'failed' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(365), $now])
                ->where('status', 'failed')->count(),
            'pending' => PreEmploymentRecord::whereBetween('created_at', [$now->copy()->subDays(365), $now])
                ->where('status', 'pending')->count(),
        ];
        
        return [
            'weekly' => $weeklyStats,
            'monthly' => $monthlyStats,
            'yearly' => $yearlyStats
        ];
    }
    
    /**
     * Generate pre-employment chart data for the last 365 days
     */
    private function generatePreEmploymentChartData()
    {
        $data = [];
        $startDate = Carbon::now()->subDays(365);
        
        for ($i = 0; $i < 365; $i++) {
            $date = $startDate->copy()->addDays($i);
            $count = PreEmploymentRecord::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }
        
        return $data;
    }

    /**
     * Generate annual physical examination chart data for the last 365 days
     */
    private function generateAnnualPhysicalChartData()
    {
        $data = [];
        $startDate = Carbon::now()->subDays(365);

        for ($i = 0; $i < 365; $i++) {
            $date = $startDate->copy()->addDays($i);
            $count = Appointment::whereDate('appointment_date', $date)
                ->whereNotNull('medical_test_id')
                ->count();

            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }

        return $data;
    }
    
    /**
     * Show patients page
     */
    public function patients()
    {
        $patients = Patient::with(['appointment.creator', 'annualPhysicalExamination'])->paginate(15);
        return view('admin.patients', compact('patients'));
    }
    
    /**
     * Show appointments page
     */
    public function appointments()
    {
        $appointments = Appointment::with([
            'patients', 
            'medicalTestCategory', 
            'medicalTest', 
            'creator'
        ])->paginate(15);
        return view('admin.appointments', compact('appointments'));
    }
    
    /**
     * Show pre-employment page
     */
    public function preEmployment(Request $request)
    {
        $filter = $request->get('filter', 'pending');
        
        $query = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest', 'creator']);
        
        // Apply filter tab logic
        switch ($filter) {
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'approved':
                $query->where('status', 'Approved')->where('registration_link_sent', false);
                break;
            case 'declined':
                $query->where('status', 'Declined');
                break;
            case 'approved_with_link':
                $query->where('status', 'Approved')->where('registration_link_sent', true);
                break;
            default:
                $query->where('status', 'pending');
        }
        
        // Apply additional filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
            });
        }
        
        if ($request->filled('company')) {
            $query->where('company_name', $request->get('company'));
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->filled('link_status')) {
            $linkStatus = $request->get('link_status');
            if ($linkStatus === 'sent') {
                $query->where('registration_link_sent', true);
            } elseif ($linkStatus === 'not_sent') {
                $query->where('registration_link_sent', false);
            }
        }
        
        if ($request->filled('date_range')) {
            $dateRange = $request->get('date_range');
            $now = now();
            
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [$now->startOfMonth(), $now->endOfMonth()]);
                    break;
            }
        }
        
        // Order by most recent first
        $query->orderBy('created_at', 'desc');
        
        $preEmployments = $query->paginate(15);
        return view('admin.pre-employment', compact('preEmployments', 'filter'));
    }
    
    /**
     * Show pre-employment details page
     */
    public function preEmploymentDetails($id)
    {
        $preEmployment = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest', 'creator'])
            ->findOrFail($id);
        
        return view('admin.pre-employment-details', compact('preEmployment'));
    }
    
    /**
     * Show appointment details page
     */
    public function appointmentDetails($id)
    {
        $appointment = Appointment::with(['medicalTestCategory', 'medicalTest', 'creator', 'patients'])
            ->findOrFail($id);
        
        return view('admin.appointment-details', compact('appointment'));
    }
    
    /**
     * Show tests page
     */
    public function tests()
    {
        // Only show examinations that have been submitted by doctors to admin
        // Pre-Employment: Doctor submits with status 'Approved' -> Admin can send to company
        // Annual Physical: Doctor submits with status 'sent_to_admin' -> Admin can send to company
        
        $preEmploymentResults = \App\Models\PreEmploymentExamination::where('status', 'Approved')->get();
        $annualPhysicalResults = \App\Models\AnnualPhysicalExamination::where('status', 'sent_to_admin')->get();
        
        return view('admin.tests', compact('preEmploymentResults', 'annualPhysicalResults'));
    }

    /**
     * View pre-employment examination details before sending
     */
    public function viewPreEmploymentExamination($id)
    {
        $examination = \App\Models\PreEmploymentExamination::findOrFail($id);
        return view('admin.view-pre-employment-examination', compact('examination'));
    }

    /**
     * View annual physical examination details before sending
     */
    public function viewAnnualPhysicalExamination($id)
    {
        $examination = \App\Models\AnnualPhysicalExamination::findOrFail($id);
        return view('admin.view-annual-physical-examination', compact('examination'));
    }

    /**
     * Send pre-employment examination to company
     */
    public function sendPreEmploymentExamination($id)
    {
        $examination = \App\Models\PreEmploymentExamination::findOrFail($id);
        
        // Here you would implement the actual sending logic
        // For now, we'll just mark it as sent and redirect back
        $examination->update(['status' => 'sent_to_company']);
        
        return redirect()->route('admin.tests')
            ->with('success', 'Pre-employment examination sent to ' . $examination->company_name . ' successfully.');
    }

    /**
     * Send annual physical examination to company
     */
    public function sendAnnualPhysicalExamination($id)
    {
        $examination = \App\Models\AnnualPhysicalExamination::findOrFail($id);
        
        // Get the company name from the associated patient's appointment
        $patient = \App\Models\Patient::find($examination->patient_id);
        $appointment = $patient ? \App\Models\Appointment::find($patient->appointment_id) : null;
        $companyName = $appointment ? \App\Models\User::find($appointment->created_by)->company : 'Unknown Company';
        
        // Here you would implement the actual sending logic
        // For now, we'll just mark it as sent and redirect back
        $examination->update(['status' => 'sent_to_company']);
        
        return redirect()->route('admin.tests')
            ->with('success', 'Annual physical examination sent to ' . $companyName . ' successfully.');
    }
    
    /**
     * Medical Staff Management: index
     */
    public function medicalStaff(Request $request)
    {
        $query = \App\Models\User::query();
        // Exclude company accounts from staff management
        $allowedRoles = ['doctor','nurse','plebo','pathologist','ecgtech','radtech','radiologist'];
        $query->whereIn('role', $allowedRoles);
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }
        // Do not filter by status; users table has no status column
        $staff = $query->orderBy('lname')->paginate(15);
        $roles = [
            'doctor' => 'Doctor',
            'nurse' => 'Nurse (Medtech)',
            'plebo' => 'Plebo',
            'pathologist' => 'Pathologist',
            'ecgtech' => 'ECG Tech',
            'radtech' => 'Radtech',
            'radiologist' => 'Radiologist',
        ];
        return view('admin.medical-staff', compact('staff', 'roles'));
    }

    /**
     * Store medical staff
     */
    public function storeMedicalStaff(Request $request)
    {
        $data = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'company' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        $data['password'] = bcrypt($data['password']);
        \App\Models\User::create($data);
        return redirect()->route('admin.medical-staff')->with('success', 'Staff created.');
    }

    /**
     * Update medical staff
     */
    public function updateMedicalStaff(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $data = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'company' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('admin.medical-staff')->with('success', 'Staff updated.');
    }

    /**
     * Delete medical staff
     */
    public function destroyMedicalStaff($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.medical-staff')->with('success', 'Staff deleted.');
    }
    
    /**
     * Show messages page
     */
    public function messages()
    {
        return view('admin.messages');
    }
    
    /**
     * Show the admin report page with examination data and company linkage
     */
    public function report()
    {
        // Pre-Employment Examinations with user and company info
        $preEmploymentExams = PreEmploymentExamination::with(['user' => function($q) {
            $q->select('id', 'fname', 'lname', 'company', 'role');
        }])->get();

        // Annual Physical Examinations with user info
        $annualPhysicalExams = AnnualPhysicalExamination::with(['user' => function($q) {
            $q->select('id', 'fname', 'lname', 'company', 'role');
        }])->get();

        // Patient company account linkage: patient.appointment_id > appointment.created_by
        $patients = \App\Models\Patient::with(['appointment.creator' => function($q) {
            $q->select('id', 'fname', 'lname', 'company', 'role');
        }])->get();

        // Debug information
        \Log::info('Admin Report Data:', [
            'pre_employment_count' => $preEmploymentExams->count(),
            'annual_physical_count' => $annualPhysicalExams->count(),
            'patients_count' => $patients->count(),
        ]);

        return view('admin.report', compact('preEmploymentExams', 'annualPhysicalExams', 'patients'));
    }

    /**
     * Show all company accounts and their patients and pre-employment records (admin view)
     */
    public function companyAccountsAndPatients()
    {
        $companyUsers = User::where('role', 'company')->orderBy('company')->get();
        $pendingCompanyUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        
        $companyData = [];
        foreach ($companyUsers as $company) {
            $appointmentIds = Appointment::where('created_by', $company->id)->pluck('id');
            $patients = Patient::whereIn('appointment_id', $appointmentIds)->get();
            $preEmployments = PreEmploymentRecord::where('created_by', $company->id)->get();
            $companyData[] = [
                'company' => $company,
                'patients' => $patients,
                'preEmployments' => $preEmployments,
            ];
        }
        return view('admin.accounts-and-patients', compact('companyData', 'pendingCompanyUsers'));
    }

    /**
     * Approve a pending company account
     */
    public function approveCompanyAccount($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'This account is not pending approval.');
        }

        $user->update([
            'role' => 'company',
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Company account approved successfully! User can now login with company privileges.');
    }

    /**
     * Reject a pending company account
     */
    public function rejectCompanyAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'This account is not pending approval.');
        }

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => $request->get('reason', 'Account rejected by administrator'),
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Company account rejected.');
    }

    /**
     * Update a company account
     */
    public function updateCompanyAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role !== 'company') {
            return redirect()->back()->with('error', 'This is not a company account.');
        }

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'company' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $user->id,
        ]);

        $user->update([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'company' => $request->company,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Company account updated successfully.');
    }

    /**
     * Fetch chat messages for the admin.
     */
    public function fetchMessages(Request $request)
    {
        $userId = Auth::id();
        $otherUserId = $request->get('user_id');
        
        // If no specific user is selected, return empty messages
        if (!$otherUserId) {
            return response()->json(['messages' => []]);
        }
        
        // Mark messages to this user as delivered if not set
        Message::whereNull('delivered_at')
            ->where('receiver_id', $userId)
            ->where('sender_id', $otherUserId)
            ->update(['delivered_at' => now()]);

        // Get messages between admin and the specific user
        $messages = Message::where(function($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $userId)->where('receiver_id', $otherUserId);
            })
            ->orWhere(function($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $otherUserId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json(['messages' => $messages]);
    }

    /**
     * Mark messages from a specific sender as read by the current user.
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
        ]);
        $userId = Auth::id();
        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['status' => 'ok']);
    }

    /**
     * Get unread message count for the current admin user.
     */
    public function getUnreadMessageCount()
    {
        $userId = Auth::id();
        $count = Message::where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Send a chat message from admin to another user.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        return response()->json($message, 201);
    }

    /**
     * Get all users for admin chat interface.
     */
    public function chatUsers()
    {
        $currentUserId = Auth::id();
        
        $users = User::select('id', 'fname', 'lname', 'role', 'company')
            ->where('id', '!=', $currentUserId) // Exclude current admin user
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
        
        // Add last message information for each user
        $users = $users->map(function($user) use ($currentUserId) {
            // Get the last message between admin and this user
            $lastMessage = Message::where(function($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $currentUserId)->where('receiver_id', $user->id);
                })
                ->orWhere(function($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $currentUserId);
                })
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Convert company enum values to strings for frontend compatibility
            $user->company = $user->company ? (string) $user->company : null;
            
            // Add last message info
            if ($lastMessage) {
                $user->last_message = \Str::limit($lastMessage->message, 50);
                $user->last_message_time = $lastMessage->created_at->diffForHumans();
            } else {
                $user->last_message = 'No messages yet';
                $user->last_message_time = '';
            }
            
            return $user;
        });
        
        // Sort by last message time (most recent first)
        $users = $users->sortByDesc(function($user) use ($currentUserId) {
            $lastMessage = Message::where(function($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $currentUserId)->where('receiver_id', $user->id);
                })
                ->orWhere(function($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $currentUserId);
                })
                ->orderBy('created_at', 'desc')
                ->first();
            
            return $lastMessage ? $lastMessage->created_at : '1970-01-01';
        })->values();
        
        return response()->json($users);
    }

    /**
     * Approve an appointment
     */
    public function approveAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'approved';
        $appointment->save();
        
        // Update all patients with this appointment_id
        \App\Models\Patient::where('appointment_id', $appointment->id)
            ->update(['status' => 'approved']);
        
        // Route medical tests to appropriate staff
        $routingService = new MedicalTestRoutingService();
        $assignments = $routingService->routeTestsForAppointment($appointment);
        
        $message = 'Appointment and patients approved successfully.';
        if (!empty($assignments)) {
            $assignmentCount = count($assignments);
            $staffRoles = array_unique(array_column($assignments, 'staff_role'));
            $staffList = implode(', ', array_map(function($role) use ($routingService) {
                return $routingService->getStaffRoleDisplayName($role);
            }, $staffRoles));
            
            $message .= " {$assignmentCount} test assignments created for: {$staffList}.";
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Decline an appointment
     */
    public function declineAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'declined';
        $appointment->save();
        return redirect()->back()->with('success', 'Appointment declined successfully.');
    }

    /**
     * Approve a pre-employment record
     */
    public function approvePreEmployment($id)
    {
        $record = PreEmploymentRecord::findOrFail($id);
        $record->status = 'Approved';
        $record->save();
        return redirect()->back()->with('success', 'Pre-employment record approved.');
    }

    /**
     * Decline a pre-employment record
     */
    public function declinePreEmployment(Request $request, $id)
    {
        $record = PreEmploymentRecord::findOrFail($id);
        $record->status = 'Declined';
        $record->decline_reason = $request->input('reason');
        $record->save();
        return redirect()->back()->with('success', 'Pre-employment record declined.');
    }

    /**
     * Send registration email for passed pre-employment records
     */
    public function sendRegistrationEmail($id)
    {
        $record = PreEmploymentRecord::findOrFail($id);
        if ($record->status !== 'Approved') {
            return redirect()->back()->with('error', 'Only passed pre-employment records can receive registration emails.');
        }
        if (empty($record->email)) {
            return redirect()->back()->with('error', 'No email address found for this record.');
        }
        try {
            Mail::to($record->email)->send(new RegistrationInvitation(
                $record->email,
                $record->full_name ?? ($record->first_name . ' ' . $record->last_name),
                $record->id
            ));
            // Mark registration link as sent
            $record->update(['registration_link_sent' => true]);
            return redirect()->back()->with('success', 'Registration email sent successfully to ' . $record->email);
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Email could not be sent. Error: ' . $e->getMessage());
        }
    }

    /**
     * Send registration emails to all passed/approved pre-employment records
     */
    public function sendAllRegistrationEmails(Request $request)
    {
        $sent = 0;
        $failed = [];
        $records = PreEmploymentRecord::where('status', 'approved')
            ->whereNotNull('email')
            ->get();
        foreach ($records as $record) {
            try {
                Mail::to($record->email)->send(new RegistrationInvitation(
                    $record->email,
                    $record->full_name ?? ($record->first_name . ' ' . $record->last_name),
                    $record->id
                ));
                // Mark registration link as sent
                $record->update(['registration_link_sent' => true]);
                $sent++;
            } catch (\Exception $e) {
                \Log::error('Bulk email failed for ' . $record->email . ': ' . $e->getMessage());
                $failed[] = $record->email;
            }
        }
        $message = $sent . ' registration email(s) sent successfully.';
        if (count($failed) > 0) {
            $message .= ' Failed to send to: ' . implode(', ', $failed);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Get HTML email template for registration
     */
    private function getRegistrationEmailTemplate($record, $registrationLink)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Pre-Employment Registration</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Congratulations!</h1>
                    <h2>Pre-Employment Medical Examination Passed</h2>
                </div>
                <div class="content">
                    <p>Dear <strong>' . htmlspecialchars($record->first_name . ' ' . $record->last_name) . '</strong>,</p>
                    
                    <p>We are pleased to inform you that your pre-employment medical examination has been <strong>PASSED</strong>.</p>
                    
                    <p><strong>Examination Details:</strong></p>
                    <ul>
                        <li><strong>Medical Exam Type:</strong> ' . htmlspecialchars($record->medical_exam_type) . '</li>
                        <li><strong>Blood Tests:</strong> ' . (is_array($record->blood_tests) ? implode(', ', $record->blood_tests) : $record->blood_tests) . '</li>
                        <li><strong>Other Exams:</strong> ' . htmlspecialchars($record->other_exams ?? 'N/A') . '</li>
                        <li><strong>Company:</strong> ' . htmlspecialchars($record->company_name ?? 'N/A') . '</li>
                    </ul>
                    
                    <p>To complete your registration and access your medical records, please click the button below:</p>
                    
                    <div style="text-align: center;">
                        <a href="' . $registrationLink . '" class="button">Complete Registration</a>
                    </div>
                    
                    <p><strong>Important Notes:</strong></p>
                    <ul>
                        <li>This link is valid for 7 days</li>
                        <li>Please complete your registration to access your medical dashboard</li>
                        <li>Keep your login credentials secure</li>
                    </ul>
                    
                    <p>If you have any questions, please contact our support team.</p>
                    
                    <p>Best regards,<br>
                    <strong>RSS Citi Health Services Team</strong></p>
                </div>
                <div class="footer">
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; ' . date('Y') . ' RSS Citi Health Services. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Get plain text email template for registration
     */
    private function getRegistrationEmailTextTemplate($record, $registrationLink)
    {
        return "Congratulations!\n\n" .
               "Your pre-employment medical examination has been PASSED.\n\n" .
               "Examination Details:\n" .
               "- Medical Exam Type: " . $record->medical_exam_type . "\n" .
               "- Blood Tests: " . (is_array($record->blood_tests) ? implode(', ', $record->blood_tests) : $record->blood_tests) . "\n" .
               "- Other Exams: " . ($record->other_exams ?? 'N/A') . "\n" .
               "- Company: " . ($record->company_name ?? 'N/A') . "\n\n" .
               "To complete your registration, visit: " . $registrationLink . "\n\n" .
               "Best regards,\nRSS Citi Health Services Team";
    }

    /**
     * Delete a company account and all related data (appointments, patients, pre-employment records)
     */
    public function deleteCompany($id)
    {
        $company = User::where('role', 'company')->findOrFail($id);
        // Delete related appointments and their patients
        $appointments = Appointment::where('created_by', $company->id)->get();
        foreach ($appointments as $appointment) {
            Patient::where('appointment_id', $appointment->id)->delete();
            $appointment->delete();
        }
        // Delete related pre-employment records
        PreEmploymentRecord::where('created_by', $company->id)->delete();
        // Delete the company user
        $company->delete();
        return redirect()->back()->with('success', 'Company account and all related data deleted successfully.');
    }

    /**
     * OPD admin listing with status filters.
     */
    public function opd(Request $request)
    {
        $filter = $request->get('filter', 'pending');
        $query = DB::table('opd_tests');
        switch ($filter) {
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'approved':
                $query->where('status', 'approved');
                break;
            case 'declined':
                $query->where('status', 'declined');
                break;
            case 'done':
                $query->where('status', 'done');
                break;
            case 'opd':
            default:
                // show all
                break;
        }
        $entries = $query->orderByDesc('created_at')->paginate(15);
        return view('admin.opd', compact('entries', 'filter'));
    }

    /** Approve OPD entry */
    public function approveOpd($id)
    {
        DB::table('opd_tests')->where('id', $id)->update(['status' => 'approved', 'updated_at' => now()]);
        return redirect()->back()->with('success', 'OPD entry approved.');
    }

    /** Decline OPD entry */
    public function declineOpd($id)
    {
        DB::table('opd_tests')->where('id', $id)->update(['status' => 'declined', 'updated_at' => now()]);
        return redirect()->back()->with('success', 'OPD entry declined.');
    }

    /** Mark OPD entry as done */
    public function markOpdDone($id)
    {
        DB::table('opd_tests')->where('id', $id)->update(['status' => 'done', 'updated_at' => now()]);
        return redirect()->back()->with('success', 'OPD entry marked as done.');
    }

    /** Send OPD results (placeholder) */
    public function sendOpdResults($id)
    {
        $entry = DB::table('opd_tests')->find($id);
        if (!$entry) {
            return redirect()->back()->with('error', 'OPD entry not found.');
        }
        return redirect()->back()->with('success', 'Results sent to ' . ($entry->customer_email ?? 'patient') . '.');
    }

    /**
     * Show test assignments page
     */
    public function testAssignments(Request $request)
    {
        $staffRole = $request->get('staff_role', 'all');
        $status = $request->get('status', 'all');
        
        $query = AppointmentTestAssignment::with(['appointment.creator', 'medicalTest', 'assignedToUser']);
        
        if ($staffRole !== 'all') {
            $query->where('staff_role', $staffRole);
        }
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $assignments = $query->orderBy('assigned_at', 'desc')->paginate(15);
        
        // Get summary statistics
        $routingService = new MedicalTestRoutingService();
        $stats = [
            'total' => AppointmentTestAssignment::count(),
            'pending' => AppointmentTestAssignment::where('status', 'pending')->count(),
            'in_progress' => AppointmentTestAssignment::where('status', 'in_progress')->count(),
            'completed' => AppointmentTestAssignment::where('status', 'completed')->count(),
            'by_staff_role' => AppointmentTestAssignment::select('staff_role', DB::raw('count(*) as count'))
                ->groupBy('staff_role')
                ->pluck('count', 'staff_role')
                ->toArray(),
        ];
        
        // Staff roles for filter dropdown
        $staffRoles = [
            'doctor' => 'Doctor',
            'nurse' => 'Nurse',
            'phlebotomist' => 'Phlebotomist',
            'pathologist' => 'Pathologist',
            'radiologist' => 'Radiologist',
            'radtech' => 'Radiology Technician',
            'ecg_tech' => 'ECG Technician',
            'med_tech' => 'Medical Technologist',
        ];
        
        return view('admin.test-assignments', compact('assignments', 'stats', 'staffRoles', 'staffRole', 'status', 'routingService'));
    }

    /**
     * Show test assignment details
     */
    public function showTestAssignment($id)
    {
        $assignment = AppointmentTestAssignment::with([
            'appointment.creator',
            'appointment.medicalTestCategory', 
            'medicalTest',
            'assignedToUser'
        ])->findOrFail($id);
        
        $routingService = new MedicalTestRoutingService();
        $summary = $routingService->getTestAssignmentsSummary($assignment->appointment);
        
        return view('admin.test-assignment-details', compact('assignment', 'summary', 'routingService'));
    }

    /**
     * Update test assignment status
     */
    public function updateTestAssignmentStatus(Request $request, $id)
    {
        $assignment = AppointmentTestAssignment::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'results' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $assignment->update([
            'status' => $request->status,
            'results' => $request->results ? json_encode($request->results) : null,
            'special_notes' => $request->notes,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);
        
        return redirect()->back()->with('success', 'Test assignment updated successfully.');
    }

    /**
     * Get billing information for pre-employment examination
     */
    public function getPreEmploymentBilling($id)
    {
        try {
            $examination = PreEmploymentExamination::with(['preEmploymentRecord.medicalTest', 'preEmploymentRecord.creator'])
                ->findOrFail($id);
            
            $record = $examination->preEmploymentRecord;
            
            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pre-employment record not found'
                ]);
            }
            
            // Get patient name
            $patientName = $examination->name ?: ($record->first_name . ' ' . $record->last_name);
            
            // Get company name
            $companyName = $examination->company_name ?: $record->company_name;
            
            // Get test name and price
            $testName = $record->medicalTest ? $record->medicalTest->name : 'Unknown Test';
            $totalAmount = $record->total_price ?: 0;
            
            return response()->json([
                'success' => true,
                'patient_name' => $patientName,
                'company_name' => $companyName,
                'test_name' => $testName,
                'total_amount' => $totalAmount,
                'examination_date' => $examination->date ? $examination->date->format('M d, Y') : 'N/A'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting pre-employment billing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load billing information'
            ]);
        }
    }

    /**
     * Get billing information for annual physical examination
     */
    public function getAnnualPhysicalBilling($id)
    {
        try {
            $examination = AnnualPhysicalExamination::with(['patient.appointment.medicalTest', 'patient.appointment.creator'])
                ->findOrFail($id);
            
            $patient = $examination->patient;
            $appointment = $patient ? $patient->appointment : null;
            
            if (!$patient || !$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient or appointment not found'
                ]);
            }
            
            // Get patient name
            $patientName = $examination->name ?: $patient->full_name;
            
            // Get company name from appointment creator
            $companyName = $appointment->creator ? $appointment->creator->company : 'Unknown Company';
            
            // Get test name and calculate total amount
            $testName = $appointment->medicalTest ? $appointment->medicalTest->name : 'Unknown Test';
            $totalAmount = $appointment->calculateTotalPrice();
            
            return response()->json([
                'success' => true,
                'patient_name' => $patientName,
                'company_name' => $companyName,
                'test_name' => $testName,
                'total_amount' => $totalAmount,
                'examination_date' => $examination->date ? $examination->date->format('M d, Y') : 'N/A'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting annual physical billing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load billing information'
            ]);
        }
    }

    /**
     * Send pre-employment examination to company with billing confirmation
     */
    public function sendPreEmploymentExaminationWithBilling($id)
    {
        try {
            $examination = PreEmploymentExamination::with(['preEmploymentRecord'])
                ->findOrFail($id);
            
            if ($examination->status === 'sent_to_company') {
                return response()->json([
                    'success' => false,
                    'message' => 'Examination has already been sent to company'
                ]);
            }
            
            // Update examination status
            $examination->update(['status' => 'sent_to_company']);
            
            // Log the billing transaction
            \Log::info('Pre-employment examination sent to company', [
                'examination_id' => $examination->id,
                'patient_name' => $examination->name,
                'company_name' => $examination->company_name,
                'total_amount' => $examination->preEmploymentRecord ? $examination->preEmploymentRecord->total_price : 0,
                'sent_by' => Auth::id(),
                'sent_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pre-employment examination sent to company successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error sending pre-employment examination: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send examination to company'
            ]);
        }
    }

    /**
     * Send annual physical examination to company with billing confirmation
     */
    public function sendAnnualPhysicalExaminationWithBilling($id)
    {
        try {
            $examination = AnnualPhysicalExamination::with(['patient.appointment'])
                ->findOrFail($id);
            
            if ($examination->status === 'sent_to_company') {
                return response()->json([
                    'success' => false,
                    'message' => 'Examination has already been sent to company'
                ]);
            }
            
            // Update examination status
            $examination->update(['status' => 'sent_to_company']);
            
            // Get company name and total amount for logging
            $patient = $examination->patient;
            $appointment = $patient ? $patient->appointment : null;
            $companyName = $appointment && $appointment->creator ? $appointment->creator->company : 'Unknown Company';
            $totalAmount = $appointment ? $appointment->calculateTotalPrice() : 0;
            
            // Log the billing transaction
            \Log::info('Annual physical examination sent to company', [
                'examination_id' => $examination->id,
                'patient_name' => $examination->name,
                'company_name' => $companyName,
                'total_amount' => $totalAmount,
                'sent_by' => Auth::id(),
                'sent_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Annual physical examination sent to company successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error sending annual physical examination: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send examination to company'
            ]);
        }
    }


    /**
     * Bulk approve pre-employment records
     */
    public function bulkApprovePreEmployment(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'No records selected for approval.');
            }

            $updated = PreEmploymentRecord::whereIn('id', $ids)
                ->where('status', '!=', 'Approved')
                ->update(['status' => 'Approved']);

            return redirect()->back()->with('success', "Successfully approved {$updated} pre-employment record(s).");
        } catch (\Exception $e) {
            \Log::error('Error bulk approving pre-employment records: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve selected records.');
        }
    }

    /**
     * Bulk decline pre-employment records
     */
    public function bulkDeclinePreEmployment(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $reason = $request->input('reason', '');
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'No records selected for declining.');
            }

            $updated = PreEmploymentRecord::whereIn('id', $ids)
                ->where('status', '!=', 'Declined')
                ->update([
                    'status' => 'Declined',
                    'decline_reason' => $reason
                ]);

            return redirect()->back()->with('success', "Successfully declined {$updated} pre-employment record(s).");
        } catch (\Exception $e) {
            \Log::error('Error bulk declining pre-employment records: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to decline selected records.');
        }
    }

    /**
     * Bulk send registration links
     */
    public function bulkSendRegistrationLinks(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'No records selected for sending links.');
            }

            $updated = PreEmploymentRecord::whereIn('id', $ids)
                ->where('status', 'Approved')
                ->where('registration_link_sent', false)
                ->update(['registration_link_sent' => true]);

            // Here you would typically send the actual emails
            // For now, we'll just mark them as sent

            return redirect()->back()->with('success', "Successfully sent registration links to {$updated} approved record(s).");
        } catch (\Exception $e) {
            \Log::error('Error bulk sending registration links: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send registration links.');
        }
    }

    /**
     * Bulk delete pre-employment records
     */
    public function bulkDeletePreEmployment(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'No records selected for deletion.');
            }

            $deleted = PreEmploymentRecord::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', "Successfully deleted {$deleted} pre-employment record(s).");
        } catch (\Exception $e) {
            \Log::error('Error bulk deleting pre-employment records: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete selected records.');
        }
    }

}
