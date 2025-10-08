<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\MedicalChecklist;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use App\Models\OpdExamination;
use App\Models\User;
use App\Models\Message;
use App\Models\MedicalTest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EcgtechController extends Controller
{
    /**
     * Show the ECG tech dashboard
     */
    public function dashboard()
    {
        // Get pre-employment records not yet submitted - only those with ECG and Drug test or packages A-E (excluding age-adjusted)
        $preEmployments = PreEmploymentRecord::where('status', 'approved')
            ->where(function($q) {
                $q->where('age_adjusted', false)
                  ->orWhereNull('age_adjusted');
            })
            ->whereHas('medicalTest', function ($q) {
                $q->where('name', 'Pre-Employment with ECG and Drug test')
                  ->orWhereIn('name', ['Package A', 'Package B', 'Package C', 'Package D', 'Package E']);
            })
            ->whereDoesntHave('preEmploymentExamination', function ($q) {
                $q->whereIn('status', ['completed', 'Approved', 'sent_to_company']);
            })
            ->latest()->take(5)->get();
            
        $preEmploymentCount = PreEmploymentRecord::where('status', 'approved')
            ->where(function($q) {
                $q->where('age_adjusted', false)
                  ->orWhereNull('age_adjusted');
            })
            ->whereHas('medicalTest', function ($q) {
                $q->where('name', 'Pre-Employment with ECG and Drug test')
                  ->orWhereIn('name', ['Package A', 'Package B', 'Package C', 'Package D', 'Package E']);
            })
            ->count();

        // Get patients for annual physical with ECG and Drug test or packages A-E (excluding age-adjusted)
        $patients = Patient::where('status', 'approved')
            ->where(function($q) {
                $q->where('age_adjusted', false)
                  ->orWhereNull('age_adjusted');
            })
            ->whereHas('appointment', function ($q) {
                $q->where('status', 'approved')
                  ->whereHas('medicalTest', function ($testQuery) {
                      $testQuery->where('name', 'Annual Medical with ECG and Drug test')
                               ->orWhereIn('name', ['Package A', 'Package B', 'Package C', 'Package D', 'Package E']);
                  });
            })
            ->whereDoesntHave('annualPhysicalExamination', function ($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->latest()->take(5)->get();

        $patientCount = Patient::where('status', 'approved')
            ->where(function($q) {
                $q->where('age_adjusted', false)
                  ->orWhereNull('age_adjusted');
            })
            ->whereHas('appointment', function ($q) {
                $q->where('status', 'approved')
                  ->whereHas('medicalTest', function ($testQuery) {
                      $testQuery->where('name', 'Annual Medical with ECG and Drug test')
                               ->orWhereIn('name', ['Package A', 'Package B', 'Package C', 'Package D', 'Package E']);
                  });
            })
            ->count();

        // Get OPD walk-in patients (users with 'opd' role)

        $opdPatients = User::where('role', 'opd')->latest()->take(5)->get();
        $opdCount = User::where('role', 'opd')->count();

        // Get ECG reports (simulated data for now)
        $ecgReports = collect([
            (object)['patient_name' => 'John Doe', 'date' => now()->format('Y-m-d'), 'ecg_result' => 'Normal', 'status' => 'completed'],
            (object)['patient_name' => 'Jane Smith', 'date' => now()->subDay()->format('Y-m-d'), 'ecg_result' => 'Abnormal', 'status' => 'completed'],
            (object)['patient_name' => 'Bob Johnson', 'date' => now()->subDays(2)->format('Y-m-d'), 'ecg_result' => 'Normal', 'status' => 'completed'],
        ]);
        $ecgReportCount = $ecgReports->count();

        return view('ecgtech.dashboard', compact(
            'preEmployments',
            'preEmploymentCount',
            'patients',
            'patientCount',
            'opdPatients',
            'opdCount',
            'ecgReports',
            'ecgReportCount'
        ));
    }

    /**
     * Show pre-employment records for ECG tech
     */
    public function preEmployment(Request $request)
    {
        $query = PreEmploymentRecord::where('status', 'approved')
            ->where(function($q) {
                $q->where('age_adjusted', false)
                  ->orWhereNull('age_adjusted');
            });

        // Get ECG status filter (default to 'needs_attention')
        $ecgStatus = $request->filled('ecg_status') ? $request->ecg_status : 'needs_attention';

        // Apply ECG status filtering
        if ($ecgStatus === 'needs_attention') {
            // Records without ECG completion in preEmploymentExamination
            $query->whereDoesntHave('preEmploymentExamination', function($q) {
                $q->whereNotNull('ecg')
                  ->where('ecg', '!=', '');
            });
        } elseif ($ecgStatus === 'ecg_completed') {
            // Records with ECG completion in preEmploymentExamination
            $query->whereHas('preEmploymentExamination', function($q) {
                $q->whereNotNull('ecg')
                  ->where('ecg', '!=', '');
            });
        }

        // Apply additional filters
        if ($request->filled('company')) {
            $query->where('company_name', $request->company);
        }

        if ($request->filled('gender')) {
            $query->where('sex', $request->gender);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('company_name', 'like', "%{$searchTerm}%");
            });
        }

        $preEmployments = $query->with(['medicalTest', 'preEmploymentExamination'])
            ->latest()
            ->paginate(15);
        
        return view('ecgtech.pre-employment', compact('preEmployments'));
    }

    /**
     * Show the annual physical patients list
     */
    public function annualPhysical(Request $request)
    {
        $query = Patient::where('status', 'approved')
            ->where(function($q) {
                $q->where('age_adjusted', false)
                  ->orWhereNull('age_adjusted');
            });

        // Get ECG status filter (default to 'needs_attention')
        $ecgStatus = $request->filled('ecg_status') ? $request->ecg_status : 'needs_attention';

        // Apply ECG status filtering
        if ($ecgStatus === 'needs_attention') {
            // Patients without ECG completion in annualPhysicalExamination
            $query->whereDoesntHave('annualPhysicalExamination', function($q) {
                $q->whereNotNull('ecg')
                  ->where('ecg', '!=', '');
            });
        } elseif ($ecgStatus === 'ecg_completed') {
            // Patients with ECG completion in annualPhysicalExamination
            $query->whereHas('annualPhysicalExamination', function($q) {
                $q->whereNotNull('ecg')
                  ->where('ecg', '!=', '');
            });
        }

        // Apply additional filters
        if ($request->filled('gender')) {
            $query->where('sex', $request->gender);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $patients = $query->with(['appointment.medicalTest', 'annualPhysicalExamination'])
            ->latest()
            ->paginate(15);
        
        return view('ecgtech.annual-physical', compact('patients'));
    }

    /**
     * Show OPD walk-in patients for ECG tech
     * 
     * @return \Illuminate\View\View
     */
    public function opd(Request $request)
    {
        $query = User::where('role', 'opd');

        // Get ECG status filter (default to 'needs_attention')
        $ecgStatus = $request->filled('ecg_status') ? $request->ecg_status : 'needs_attention';

        // Apply ECG status filtering
        if ($ecgStatus === 'needs_attention') {
            // Users without ECG completion in opdExamination
            $query->whereDoesntHave('opdExamination', function($q) {
                $q->whereNotNull('ecg')
                  ->where('ecg', '!=', '');
            });
        } elseif ($ecgStatus === 'ecg_completed') {
            // Users with ECG completion in opdExamination
            $query->whereHas('opdExamination', function($q) {
                $q->whereNotNull('ecg')
                  ->where('ecg', '!=', '');
            });
        }

        // Apply additional filters
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $opdPatients = $query->with(['opdExamination'])
            ->latest()
            ->paginate(15);
        
        return view('ecgtech.opd', compact('opdPatients'));
    }


    /**
     * Show medical checklist for pre-employment
     */
    public function showMedicalChecklistPreEmployment($recordId)
    {
        $record = PreEmploymentRecord::findOrFail($recordId);
        $medicalChecklist = MedicalChecklist::where('pre_employment_record_id', $recordId)->first();
        $examinationType = 'pre-employment';
        $number = 'PEM-' . str_pad($record->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? ''));
        $age = $record->age ?? null;
        $date = now()->format('Y-m-d');

        return view('ecgtech.medical-checklist', compact('medicalChecklist', 'record', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist for annual physical
     */
    public function showMedicalChecklistAnnualPhysical($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        // Find the most recent examination record for this patient
        $annualPhysicalExamination = \App\Models\AnnualPhysicalExamination::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Try to find checklist linked to this examination
        $medicalChecklist = null;
        if ($annualPhysicalExamination) {
            $medicalChecklist = MedicalChecklist::where('annual_physical_examination_id', $annualPhysicalExamination->id)
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->first();
        }
        
        // Fallback: check by patient_id for unlinked checklists
        if (!$medicalChecklist) {
            $medicalChecklist = MedicalChecklist::where('patient_id', $patientId)
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->whereNull('annual_physical_examination_id')
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        $examinationType = 'annual-physical';
        $number = 'PAT-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $age = $patient->age ?? null;
        $date = now()->format('Y-m-d');

        return view('ecgtech.medical-checklist', compact('medicalChecklist', 'patient', 'annualPhysicalExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist for OPD
     */
    public function showMedicalChecklistOpd($userId)
    {
        $opdPatient = User::where('role', 'opd')->findOrFail($userId);
        $opdExamination = OpdExamination::where('user_id', $userId)->first();
        $medicalChecklist = MedicalChecklist::where('opd_examination_id', $opdExamination->id ?? 0)->first();
        $examinationType = 'opd';
        $number = 'OPD-' . str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? ''));
        $age = $opdPatient->age ?? null;
        $date = now()->format('Y-m-d');
        
        return view('ecgtech.medical-checklist', compact('medicalChecklist', 'opdPatient', 'opdExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist page for pre-employment
     */
    public function showMedicalChecklistPagePreEmployment($recordId)
    {
        $record = PreEmploymentRecord::findOrFail($recordId);
        $medicalChecklist = MedicalChecklist::where('pre_employment_record_id', $recordId)->first();
        $examinationType = 'pre-employment';
        $number = 'PEM-' . str_pad($record->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? ''));
        $age = $record->age ?? null;
        $date = now()->format('Y-m-d');

        return view('ecgtech.medical-checklist-page', compact('medicalChecklist', 'record', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist page for annual physical
     */
    public function showMedicalChecklistPageAnnualPhysical($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $record = $patient; // For consistency with the blade template
        
        // Find the most recent examination record for this patient
        $annualPhysicalExamination = \App\Models\AnnualPhysicalExamination::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Try to find checklist linked to this examination
        $medicalChecklist = null;
        if ($annualPhysicalExamination) {
            $medicalChecklist = MedicalChecklist::where('annual_physical_examination_id', $annualPhysicalExamination->id)
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->first();
        }
        
        // Fallback: check by patient_id for unlinked checklists
        if (!$medicalChecklist) {
            $medicalChecklist = MedicalChecklist::where('patient_id', $patientId)
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->whereNull('annual_physical_examination_id')
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        $examinationType = 'annual-physical';
        $number = 'PAT-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $age = $patient->age ?? null;
        $date = now()->format('Y-m-d');

        return view('ecgtech.medical-checklist-page', compact('medicalChecklist', 'patient', 'record', 'annualPhysicalExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Store medical checklist
     */
    public function storeMedicalChecklist(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'pre_employment_record_id' => 'nullable|exists:pre_employment_records,id',
            'opd_examination_id' => 'nullable|integer',
            'examination_type' => 'required|in:annual-physical,pre-employment,opd',
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:120',
            'date' => 'required|date',
            'number' => 'nullable|string|max:255',
            
            
            // Done by fields (ECG Tech can only fill ECG)
            'chest_xray_done_by' => 'nullable|string|max:255',
            'stool_exam_done_by' => 'nullable|string|max:255',
            'urinalysis_done_by' => 'nullable|string|max:255',
            'drug_test_done_by' => 'nullable|string|max:255',
            'blood_extraction_done_by' => 'nullable|string|max:255',
            'ecg_done_by' => 'required|string|max:255',
            'physical_exam_done_by' => 'nullable|string|max:255',
            
            // Optional and doctor signature (read-only for ECG Tech)
            'optional_exam' => 'nullable|string|max:255',
            'doctor_signature' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();

        // For annual physical: find or create examination and link it
        if ($data['examination_type'] === 'annual-physical' && $data['patient_id']) {
            $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::where('patient_id', $data['patient_id'])
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$annualPhysicalExam) {
                $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::create([
                    'patient_id' => $data['patient_id'],
                    'status' => 'Pending'
                ]);
            }
            
            $data['annual_physical_examination_id'] = $annualPhysicalExam->id;
        }

        // Prepare the search criteria based on examination type
        $searchCriteria = [];
        if ($data['examination_type'] === 'annual-physical' && !empty($data['annual_physical_examination_id'])) {
            $searchCriteria['annual_physical_examination_id'] = $data['annual_physical_examination_id'];
        } elseif ($data['examination_type'] === 'pre-employment' && $data['pre_employment_record_id']) {
            $searchCriteria['pre_employment_record_id'] = $data['pre_employment_record_id'];
        } elseif ($data['examination_type'] === 'opd' && $data['opd_examination_id']) {
            $searchCriteria['opd_examination_id'] = $data['opd_examination_id'];
        }

        MedicalChecklist::updateOrCreate(
            $searchCriteria,
            $data
        );

        // Redirect back to the previous page (dashboard or list page)
        $redirectUrl = url()->previous();
        
        // If coming from a specific route, redirect to the appropriate list
        if (str_contains($redirectUrl, 'pre-employment')) {
            return redirect()->route('ecgtech.pre-employment')->with('success', 'ECG checklist saved successfully.');
        } elseif (str_contains($redirectUrl, 'annual-physical')) {
            return redirect()->route('ecgtech.annual-physical')->with('success', 'ECG checklist saved successfully.');
        } elseif (str_contains($redirectUrl, 'opd')) {
            return redirect()->route('ecgtech.opd')->with('success', 'ECG checklist saved successfully.');
        } else {
            return redirect()->route('ecgtech.dashboard')->with('success', 'ECG checklist saved successfully.');
        }
    }

    /**
     * Update medical checklist
     */
    public function updateMedicalChecklist(Request $request, $id)
    {
        $medicalChecklist = MedicalChecklist::findOrFail($id);
        
        $data = $request->validate([
            'ecg_result' => 'required|string|max:500',
            'ecg_findings' => 'nullable|string|max:1000',
            'rhythm' => 'nullable|string|max:255',
            'rate' => 'nullable|string|max:255',
            'pr_interval' => 'nullable|string|max:255',
            'qrs_duration' => 'nullable|string|max:255',
            'qt_interval' => 'nullable|string|max:255',
            'axis' => 'nullable|string|max:255',
            'interpretation' => 'nullable|string|max:1000',
        ]);

        $medicalChecklist->update($data);

        // Create notification for admin when ECG is completed
        $ecgtech = Auth::user();
        $patientName = $medicalChecklist->name;
        $examinationType = $medicalChecklist->pre_employment_record_id ? 'Pre-Employment' : 
                          ($medicalChecklist->patient_id ? 'Annual Physical' : 'OPD');
        
        Notification::createForAdmin(
            'ecg_completed',
            'ECG Examination Completed',
            "ECG Tech {$ecgtech->name} has completed ECG examination for {$patientName} ({$examinationType}).",
            [
                'checklist_id' => $medicalChecklist->id,
                'patient_name' => $patientName,
                'ecgtech_name' => $ecgtech->name,
                'examination_type' => strtolower(str_replace('-', '_', $examinationType)),
                'ecg_result' => $data['ecg_result'],
                'has_findings' => !empty($data['ecg_findings'])
            ],
            'medium',
            $ecgtech,
            $medicalChecklist
        );

        return redirect()->back()->with('success', 'ECG checklist updated successfully.');
    }

    /**
     * Show messages view
     */
    public function messages()
    {
        return view('ecgtech.messages');
    }

    /**
     * Simple test method
     */
    public function testContacts()
    {
        return response()->json(['message' => 'Test successful', 'user_id' => Auth::id()]);
    }

    /**
     * Get users that ecgtech can chat with (admin and doctor only)
     */
    public function chatUsers()
    {
        $currentUserId = Auth::id();
        
        $users = User::whereIn('role', ['admin', 'doctor'])
            ->where('id', '!=', $currentUserId)
            ->select('id', 'fname', 'lname', 'role', 'company')
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
        
        // Add last message information for each user
        $users = $users->map(function($user) use ($currentUserId) {
            // Get the last message between ecgtech user and this user
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
            
            // Add unread message count
            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $currentUserId)
                ->whereNull('read_at')
                ->count();
            $user->unread_messages = $unreadCount;
            
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
     * Fetch messages for the current ecgtech
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

        // Get messages between ecgtech user and the specific user
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
     * Send message
     */
    public function sendMessage(Request $request)
    {
        try {
            // Get data from request (works for both JSON and form data)
            $receiverId = $request->input('receiver_id');
            $messageText = $request->input('message');
            
            if (!$receiverId) {
                return response()->json(['error' => 'receiver_id is required'], 400);
            }
            
            if (!$messageText) {
                return response()->json(['error' => 'message is required'], 400);
            }
            
            if (strlen($messageText) > 1000) {
                return response()->json(['error' => 'message is too long'], 400);
            }
            
            // Validate that the receiver exists
            $receiver = User::find($receiverId);
            if (!$receiver) {
                return response()->json(['error' => 'Invalid receiver_id'], 400);
            }

            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'message' => $messageText
            ]);

            return response()->json($message);
        } catch (\Exception $e) {
            \Log::error('Error in sendMessage: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        try {
            // Get sender_id from request (works for both JSON and form data)
            $senderId = $request->input('sender_id');
            
            if (!$senderId) {
                return response()->json(['error' => 'sender_id is required'], 400);
            }
            
            // Validate that the sender exists
            $sender = User::find($senderId);
            if (!$sender) {
                return response()->json(['error' => 'Invalid sender_id'], 400);
            }

            Message::where('sender_id', $senderId)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error in markAsRead: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to mark messages as read: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing pre-employment examination
     */
    public function editPreEmployment($id)
    {
        try {
            // Find the pre-employment record
            $preEmploymentRecord = \App\Models\PreEmploymentRecord::findOrFail($id);
            
            // Find or create the examination record
            $preEmployment = $preEmploymentRecord->preEmploymentExamination;
            if (!$preEmployment) {
                $preEmployment = $preEmploymentRecord->preEmploymentExamination()->create([
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);
            }
            
            return view('ecgtech.pre-employment-edit', compact('preEmployment'));
        } catch (\Exception $e) {
            \Log::error('Error in editPreEmployment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load pre-employment examination for editing.');
        }
    }

    /**
     * Show the form for editing annual physical examination
     */
    public function editAnnualPhysical($id)
    {
        try {
            // Find the patient
            $patient = \App\Models\Patient::findOrFail($id);
            
            // Find or create the examination record
            $annualPhysical = $patient->annualPhysicalExamination;
            if (!$annualPhysical) {
                $annualPhysical = $patient->annualPhysicalExamination()->create([
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);
            }
            
            return view('ecgtech.annual-physical-edit', compact('annualPhysical'));
        } catch (\Exception $e) {
            \Log::error('Error in editAnnualPhysical: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load annual physical examination for editing.');
        }
    }

    /**
     * Update pre-employment examination ECG results
     */
    public function updatePreEmployment(Request $request, $id)
    {
        try {
            $request->validate([
                'ecg' => 'required|string|max:1000',
                'heart_rate' => 'nullable|string|max:255',
            ]);

            // Find the pre-employment record
            $preEmploymentRecord = \App\Models\PreEmploymentRecord::findOrFail($id);
            
            // Find or create the examination record
            $preEmployment = $preEmploymentRecord->preEmploymentExamination;
            if (!$preEmployment) {
                $preEmployment = $preEmploymentRecord->preEmploymentExamination()->create([
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);
            }
            
            // Get existing physical_exam data or create new array
            $physicalExam = $preEmployment->physical_exam ?? [];
            if ($request->heart_rate) {
                $physicalExam['heart_rate'] = $request->heart_rate;
            }
            
            $preEmployment->update([
                'ecg' => $request->ecg,
                'ecg_date' => now()->toDateString(),
                'ecg_technician' => Auth::user()->fname . ' ' . Auth::user()->lname,
                'physical_exam' => $physicalExam,
                'status' => 'completed', // Update status to completed
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            // Create notification for admin when ECG examination is completed
            $ecgtech = Auth::user();
            $patientName = $preEmploymentRecord->full_name;
            
            Notification::createForAdmin(
                'ecg_completed',
                'ECG Examination Completed - Pre-Employment',
                "ECG Tech {$ecgtech->name} has completed ECG examination for {$patientName} (Pre-Employment).",
                [
                    'examination_id' => $preEmployment->id,
                    'patient_name' => $patientName,
                    'ecgtech_name' => $ecgtech->name,
                    'examination_type' => 'pre_employment',
                    'ecg_result' => $request->ecg,
                    'heart_rate' => $request->heart_rate
                ],
                'medium',
                $ecgtech,
                $preEmployment
            );

            return redirect()->route('ecgtech.pre-employment')->with('success', 'ECG examination results updated successfully and sent to doctor for review.');
        } catch (\Exception $e) {
            \Log::error('Error in updatePreEmployment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update ECG examination results.')->withInput();
        }
    }

    /**
     * Update annual physical examination ECG results
     */
    public function updateAnnualPhysical(Request $request, $id)
    {
        try {
            $request->validate([
                'ecg' => 'required|string|max:1000',
                'heart_rate' => 'nullable|string|max:255',
            ]);

            // Find the patient
            $patient = \App\Models\Patient::findOrFail($id);
            
            // Find or create the examination record
            $annualPhysical = $patient->annualPhysicalExamination;
            if (!$annualPhysical) {
                $annualPhysical = $patient->annualPhysicalExamination()->create([
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);
            }
            
            // Get existing physical_exam data or create new array
            $physicalExam = $annualPhysical->physical_exam ?? [];
            if ($request->heart_rate) {
                $physicalExam['heart_rate'] = $request->heart_rate;
            }
            
            $annualPhysical->update([
                'ecg' => $request->ecg,
                'ecg_date' => now()->toDateString(),
                'ecg_technician' => Auth::user()->fname . ' ' . Auth::user()->lname,
                'physical_exam' => $physicalExam,
                'status' => 'completed', // Update status to completed
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            return redirect()->route('ecgtech.annual-physical')->with('success', 'ECG examination results updated successfully and sent to doctor for review.');
        } catch (\Exception $e) {
            \Log::error('Error in updateAnnualPhysical: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update ECG examination results.')->withInput();
        }
    }

    /**
     * Show create OPD examination form
     */
    public function createOpd(Request $request)
    {
        $userId = $request->query('user_id');
        $opdPatient = User::where('role', 'opd')->findOrFail($userId);
        
        return view('ecgtech.opd-create', compact('opdPatient'));
    }

    /**
     * Show the form for editing OPD examination
     */
    public function editOpd($id)
    {
        try {
            // Find the OPD patient by user ID
            $opdPatient = User::where('role', 'opd')->findOrFail($id);
            
            // Find or create the examination record
            $opdExamination = $opdPatient->opdExamination;
            if (!$opdExamination) {
                $opdExamination = $opdPatient->opdExamination()->create([
                    'status' => 'pending',
                    'name' => trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? '')),
                    'date' => now()->toDateString(),
                ]);
            }
            
            return view('ecgtech.opd-edit', compact('opdExamination', 'opdPatient'));
        } catch (\Exception $e) {
            \Log::error('Error in editOpd: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load OPD examination for editing.');
        }
    }

    /**
     * Update OPD examination ECG results
     */
    public function updateOpd(Request $request, $id)
    {
        try {
            $request->validate([
                'ecg' => 'required|string|max:1000',
                'heart_rate' => 'nullable|string|max:255',
            ]);

            // Find the OPD patient
            $opdPatient = User::where('role', 'opd')->findOrFail($id);
            
            // Find or create the examination record
            $opdExamination = $opdPatient->opdExamination;
            if (!$opdExamination) {
                $opdExamination = $opdPatient->opdExamination()->create([
                    'status' => 'pending',
                    'name' => trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? '')),
                    'date' => now()->toDateString(),
                ]);
            }
            
            // Get existing physical_exam data or create new array
            $physicalExam = $opdExamination->physical_exam ?? [];
            if ($request->heart_rate) {
                $physicalExam['heart_rate'] = $request->heart_rate;
            }
            
            $opdExamination->update([
                'ecg' => $request->ecg,
                'physical_exam' => $physicalExam,
            ]);

            return redirect()->route('ecgtech.opd')->with('success', 'ECG examination results updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error in updateOpd: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update ECG examination results.')->withInput();
        }
    }

    /**
     * Get unread message count for the current ecgtech user.
     */
    public function getUnreadMessageCount()
    {
        $userId = Auth::id();
        $count = Message::where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();
        
        return response()->json(['count' => $count]);
    }
}
