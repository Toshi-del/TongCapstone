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
        // Exclude records that have already been sent (have corresponding examination with sent status)
        $preEmploymentRecords = PreEmploymentRecord::where('created_by', $user->id)
            ->whereDoesntHave('preEmploymentExamination', function($query) {
                $query->whereIn('status', ['sent_to_company', 'sent_to_patient', 'sent_to_both']);
            })
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
        
        // Calculate pre-employment statistics (exclude sent records)
        $totalPreEmploymentCount = PreEmploymentRecord::where('created_by', $user->id)
            ->whereDoesntHave('preEmploymentExamination', function($query) {
                $query->whereIn('status', ['sent_to_company', 'sent_to_patient', 'sent_to_both']);
            })
            ->count();
        
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
        // Include sent_to_company, sent_to_patient, and sent_to_both for pre-employment
        // Match by company name (case-insensitive) OR by created_by user
        $sentPreEmploymentResults = \App\Models\PreEmploymentExamination::with('preEmploymentRecord')
            ->where(function($query) use ($user) {
                $query->whereRaw('LOWER(TRIM(company_name)) = ?', [strtolower(trim($user->company))])
                      ->orWhereHas('preEmploymentRecord', function($q) use ($user) {
                          $q->where('created_by', $user->id);
                      });
            })
            ->whereIn('status', ['sent_to_company', 'sent_to_patient', 'sent_to_both'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Log for debugging
        \Log::info('Company medical results query', [
            'user_company' => $user->company,
            'user_id' => $user->id,
            'sent_pre_employment_count' => $sentPreEmploymentResults->count()
        ]);
            
        $sentAnnualPhysicalResults = \App\Models\AnnualPhysicalExamination::with('patient.appointment')
            ->whereIn('status', ['sent_to_company', 'sent_to_both'])
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
        
        // Calculate total prices for sent results
        $totalPriceAnnualPhysical = $sentAnnualPhysicalResults->sum(function($exam) {
            return $exam->patient && $exam->patient->appointment ? $exam->patient->appointment->total_price : 0;
        });
        
        $totalPricePreEmployment = $sentPreEmploymentResults->sum(function($exam) {
            return $exam->preEmploymentRecord ? $exam->preEmploymentRecord->total_price : 0;
        });
        
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
            'totalPriceAnnualPhysical',
            'totalPricePreEmployment',
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
            ->where(function($query) use ($user) {
                $query->whereRaw('LOWER(TRIM(company_name)) = ?', [strtolower(trim($user->company))])
                      ->orWhereHas('preEmploymentRecord', function($q) use ($user) {
                          $q->where('created_by', $user->id);
                      });
            })
            ->whereIn('status', ['sent_to_company', 'sent_to_patient', 'sent_to_both'])
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
            ->whereIn('status', ['sent_to_company', 'sent_to_both'])
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
            ->where(function($query) use ($user) {
                $query->whereRaw('LOWER(TRIM(company_name)) = ?', [strtolower(trim($user->company))])
                      ->orWhereHas('preEmploymentRecord', function($q) use ($user) {
                          $q->where('created_by', $user->id);
                      });
            })
            ->whereIn('status', ['sent_to_company', 'sent_to_patient', 'sent_to_both'])
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
            ->whereIn('status', ['sent_to_company', 'sent_to_both'])
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
    public function fetchMessages(Request $request)
    {
        $userId = Auth::id();
        $otherUserId = $request->get('user_id');
        
        // If no specific user is selected, return empty messages
        if (!$otherUserId) {
            return response()->json(['messages' => []]);
        }
        
        // Mark messages to this user as delivered
        Message::whereNull('delivered_at')
            ->where('receiver_id', $userId)
            ->where('sender_id', $otherUserId)
            ->update(['delivered_at' => now()]);

        // Get messages between company user and the specific user
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
     * Get unread message count for the current company user.
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
        $currentUserId = Auth::id();
        
        $users = User::select('id', 'fname', 'lname', 'role', 'company')
            ->where('id', '!=', $currentUserId)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
        
        // Add last message information for each user
        $users = $users->map(function($user) use ($currentUserId) {
            // Get the last message between company user and this user
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
}
