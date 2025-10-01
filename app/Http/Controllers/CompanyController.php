<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\PreEmploymentRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class CompanyController extends Controller
{
    /**
     * Show the company dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get appointments for the current company user
        $appointments = Appointment::where('created_by', $user->id)
            ->with('patients')
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();
            
        // Get pre-employment records for the current company user
        $preEmploymentRecords = PreEmploymentRecord::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Calculate statistics
        $pendingAppointmentsCount = Appointment::where('created_by', $user->id)
            ->where('status', 'pending')
            ->count();
            
        $approvedAppointmentsCount = Appointment::where('created_by', $user->id)
            ->where('status', 'approved')
            ->count();
            
        $totalAppointmentsCount = Appointment::where('created_by', $user->id)->count();
        
        // Calculate pre-employment statistics
        $totalPreEmploymentCount = PreEmploymentRecord::where('created_by', $user->id)->count();
        
        return view('company.dashboard', compact(
            'appointments',
            'preEmploymentRecords',
            'pendingAppointmentsCount',
            'approvedAppointmentsCount',
            'totalAppointmentsCount',
            'totalPreEmploymentCount'
        ));
    }

    /**
     * Show the company settings page
     */
    public function settings()
    {
        return view('company.settings');
    }

    /**
     * Update company settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'employee_count' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return redirect()->route('company.settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * Show medical results page
     */
    public function medicalResults(Request $request)
    {
        $user = Auth::user();
        
        // Get annual physical examination results (from appointments)
        // Note: legacy column 'appointment_type' removed; filter by creator only
        $annualPhysicalResults = Appointment::where('created_by', $user->id)
            ->with(['patients' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        // Get pre-employment examination results
        $preEmploymentResults = PreEmploymentRecord::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get sent examination results from admin
        $sentPreEmploymentResults = \App\Models\PreEmploymentExamination::where('company_name', $user->company)
            ->where('status', 'sent_to_company')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $sentAnnualPhysicalResults = \App\Models\AnnualPhysicalExamination::where('status', 'sent_to_company')
            ->whereHas('patient.appointment', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Filter by status if requested
        $statusFilter = $request->get('status');
        if ($statusFilter) {
            if ($statusFilter === 'annual_physical') {
                $annualPhysicalResults = $annualPhysicalResults->filter(function($appointment) {
                    return $appointment->status === 'completed';
                });
            } elseif ($statusFilter === 'pre_employment') {
                $preEmploymentResults = $preEmploymentResults->filter(function($record) {
                    return $record->status === 'passed' || $record->status === 'failed';
                });
            } elseif ($statusFilter === 'sent_results') {
                // Show only sent results
                $annualPhysicalResults = collect();
                $preEmploymentResults = collect();
            }
        }
        
        // Calculate statistics
        $totalAnnualPhysical = $annualPhysicalResults->count();
        $completedAnnualPhysical = $annualPhysicalResults->where('status', 'completed')->count();
        $totalPreEmployment = $preEmploymentResults->count();
        $passedPreEmployment = $preEmploymentResults->where('status', 'passed')->count();
        $failedPreEmployment = $preEmploymentResults->where('status', 'failed')->count();
        
        // Calculate sent results statistics
        $totalSentAnnualPhysical = $sentAnnualPhysicalResults->count();
        $totalSentPreEmployment = $sentPreEmploymentResults->count();
        
        return view('company.medical-results', compact(
            'annualPhysicalResults',
            'preEmploymentResults',
            'sentAnnualPhysicalResults',
            'sentPreEmploymentResults',
            'totalAnnualPhysical',
            'completedAnnualPhysical',
            'totalPreEmployment',
            'passedPreEmployment',
            'failedPreEmployment',
            'totalSentAnnualPhysical',
            'totalSentPreEmployment',
            'statusFilter'
        ));
    }

    /**
     * View sent pre-employment examination details
     */
    public function viewSentPreEmployment($id)
    {
        $user = Auth::user();
        $examination = \App\Models\PreEmploymentExamination::with([
            'preEmploymentRecord.medicalTests',
            'preEmploymentRecord.medicalTestCategories',
            'preEmploymentRecord.preEmploymentMedicalTests.medicalTest',
            'preEmploymentRecord.preEmploymentMedicalTests.medicalTestCategory'
        ])
            ->where('id', $id)
            ->where('company_name', $user->company)
            ->where('status', 'sent_to_company')
            ->firstOrFail();
            
        return view('company.view-sent-pre-employment', compact('examination'));
    }

    /**
     * View sent annual physical examination details
     */
    public function viewSentAnnualPhysical($id)
    {
        $user = Auth::user();
        $examination = \App\Models\AnnualPhysicalExamination::with([
            'patient.appointment.medicalTestCategory',
            'patient.appointment.medicalTest'
        ])
            ->where('id', $id)
            ->where('status', 'sent_to_company')
            ->whereHas('patient.appointment', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->firstOrFail();
            
        return view('company.view-sent-annual-physical', compact('examination'));
    }

    /**
     * Download sent pre-employment examination results
     */
    public function downloadSentPreEmployment($id)
    {
        $user = Auth::user();
        $examination = \App\Models\PreEmploymentExamination::where('id', $id)
            ->where('company_name', $user->company)
            ->where('status', 'sent_to_company')
            ->firstOrFail();
            
        // For now, redirect to view page - can be enhanced to generate PDF
        return redirect()->route('company.view-sent-pre-employment', $id)
            ->with('info', 'Download functionality will be implemented to generate PDF reports.');
    }

    /**
     * Download sent annual physical examination results
     */
    public function downloadSentAnnualPhysical($id)
    {
        $user = Auth::user();
        $examination = \App\Models\AnnualPhysicalExamination::where('id', $id)
            ->where('status', 'sent_to_company')
            ->whereHas('patient.appointment', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->firstOrFail();
            
        // For now, redirect to view page - can be enhanced to generate PDF
        return redirect()->route('company.view-sent-annual-physical', $id)
            ->with('info', 'Download functionality will be implemented to generate PDF reports.');
    }

    /**
     * Show the company chat page
     */
    public function messages()
    {
        return view('company.messages');
    }

    /**
     * Fetch chat messages for the company user.
     */
    public function fetchMessages()
    {
        $userId = Auth::id();
        // Mark messages to this user as delivered
        Message::whereNull('delivered_at')
            ->where('receiver_id', $userId)
            ->update(['delivered_at' => now()]);

        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json($messages);
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
     * Send a chat message from company user to another user.
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
     * Fetch all users except the current user for chat user list
     */
    public function chatUsers()
    {
        $users = User::where('id', '!=', auth()->id())->get(['id', 'fname', 'lname', 'role', 'company']);
        return response()->json($users);
    }
}
