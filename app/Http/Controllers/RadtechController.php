<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\MedicalChecklist;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use App\Models\Notification;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RadtechController extends Controller
{
    /**
     * Show the radtech dashboard
     */
    public function dashboard()
    {
        // Get pre-employment records not yet submitted and without chest X-ray completion
        $preEmployments = PreEmploymentRecord::where('status', 'approved')
            ->whereDoesntHave('preEmploymentExamination', function ($q) {
                $q->whereIn('status', ['Approved', 'sent_to_company']);
            })
            ->whereDoesntHave('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by');
            })
            ->latest()->take(5)->get();
        $preEmploymentCount = PreEmploymentRecord::where('status', 'approved')
            ->whereDoesntHave('preEmploymentExamination', function ($q) {
                $q->whereIn('status', ['Approved', 'sent_to_company']);
            })
            ->whereDoesntHave('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by');
            })
            ->count();

        // Get patients for annual physical not yet submitted and without chest X-ray completion
        $patients = Patient::where('status', 'approved')
            ->whereDoesntHave('annualPhysicalExamination', function ($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->whereDoesntHave('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by');
            })
            ->latest()->take(5)->get();
        $patientCount = Patient::where('status', 'approved')
            ->whereDoesntHave('annualPhysicalExamination', function ($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->whereDoesntHave('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by');
            })
            ->count();

        // Get appointments
        $appointments = Appointment::with('patients')->latest()->take(5)->get();
        $appointmentCount = Appointment::count();

        return view('radtech.dashboard', compact(
            'preEmployments',
            'preEmploymentCount',
            'patients',
            'patientCount',
            'appointments',
            'appointmentCount'
        ));
    }

    /**
     * Show pre-employment X-ray records
     */
    public function preEmploymentXray(Request $request)
    {
        $query = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest'])
            ->where('status', 'approved')
            ->where(function($query) {
                // Check medical test relationships OR other_exams column for X-ray services
                $query->whereHas('medicalTest', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%X-ray%')
                             ->orWhere('name', 'like', '%Chest%')
                             ->orWhere('name', 'like', '%Radiology%');
                    });
                })->orWhereHas('medicalTests', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%X-ray%')
                             ->orWhere('name', 'like', '%Chest%')
                             ->orWhere('name', 'like', '%Radiology%');
                    });
                })->orWhere(function($q) {
                    // Also check other_exams column for X-ray services
                    $q->where('other_exams', 'like', '%Pre-Employment%')
                      ->orWhere('other_exams', 'like', '%X-ray%')
                      ->orWhere('other_exams', 'like', '%Chest%')
                      ->orWhere('other_exams', 'like', '%Radiology%');
                });
            });

        // Handle tab filtering
        $xrayStatus = $request->get('xray_status', 'needs_attention');
        
        if ($xrayStatus === 'needs_attention') {
            // Records that need X-ray imaging (no chest_xray_done_by)
            $query->whereDoesntHave('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by');
            });
        } elseif ($xrayStatus === 'xray_completed') {
            // Records where X-ray imaging is completed
            $query->whereHas('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by')
                  ->where('chest_xray_done_by', '!=', '');
            });
        }

        $preEmployments = $query->latest()->get();

        return view('radtech.pre-employment-xray', compact('preEmployments'));
    }

    /**
     * Show annual physical X-ray records
     */
    public function annualPhysicalXray()
    {
        $patients = Patient::where('status', 'approved')
            ->whereDoesntHave('annualPhysicalExamination', function ($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->whereDoesntHave('medicalChecklist', function ($q) {
                $q->whereNotNull('chest_xray_done_by');
            })
            ->latest()
            ->get();

        return view('radtech.annual-physical-xray', compact('patients'));
    }

    /**
     * Show medical checklist for pre-employment
     */
    public function showMedicalChecklistPreEmployment($recordId)
    {
        $preEmploymentRecord = PreEmploymentRecord::findOrFail($recordId);
        $medicalChecklist = MedicalChecklist::where('pre_employment_record_id', $recordId)->first();
        $examinationType = 'pre-employment';
        $number = 'EMP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($preEmploymentRecord->first_name ?? '') . ' ' . ($preEmploymentRecord->last_name ?? ''));
        $age = $preEmploymentRecord->age ?? null;
        $date = now()->format('Y-m-d');
        
        $examinations = [
            'chest_xray' => ['name' => 'Chest X-Ray', 'icon' => 'fas fa-x-ray', 'color' => 'cyan'],
            'stool_exam' => ['name' => 'Stool Examination', 'icon' => 'fas fa-flask', 'color' => 'amber'],
            'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-tint', 'color' => 'blue'],
            'drug_test' => ['name' => 'Drug Test', 'icon' => 'fas fa-pills', 'color' => 'red'],
            'blood_extraction' => ['name' => 'Blood Extraction', 'icon' => 'fas fa-syringe', 'color' => 'rose'],
            'ecg' => ['name' => 'ElectroCardioGram (ECG)', 'icon' => 'fas fa-heartbeat', 'color' => 'green'],
            'physical_exam' => ['name' => 'Physical Examination', 'icon' => 'fas fa-stethoscope', 'color' => 'purple'],
        ];
        
        return view('radtech.medical-checklist', compact(
            'medicalChecklist', 
            'preEmploymentRecord', 
            'examinationType', 
            'number', 
            'name', 
            'age', 
            'date',
            'examinations'
        ));
    }

    /**
     * Show medical checklist for annual physical
     */
    public function showMedicalChecklistAnnualPhysical($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $medicalChecklist = MedicalChecklist::where('patient_id', $patientId)->first();
        $examinationType = 'annual-physical';
        
        $examinations = [
            'chest_xray' => ['name' => 'Chest X-Ray', 'icon' => 'fas fa-x-ray', 'color' => 'cyan'],
            'stool_exam' => ['name' => 'Stool Examination', 'icon' => 'fas fa-flask', 'color' => 'amber'],
            'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-tint', 'color' => 'blue'],
            'drug_test' => ['name' => 'Drug Test', 'icon' => 'fas fa-pills', 'color' => 'red'],
            'blood_extraction' => ['name' => 'Blood Extraction', 'icon' => 'fas fa-syringe', 'color' => 'rose'],
            'ecg' => ['name' => 'ElectroCardioGram (ECG)', 'icon' => 'fas fa-heartbeat', 'color' => 'green'],
            'physical_exam' => ['name' => 'Physical Examination', 'icon' => 'fas fa-stethoscope', 'color' => 'purple'],
        ];
        $number = 'PAT-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $age = $patient->age ?? null;
        $date = now()->format('Y-m-d');
        
        return view('radtech.medical-checklist', compact(
            'medicalChecklist', 
            'patient', 
            'examinationType', 
            'number', 
            'name', 
            'age', 
            'date',
            'examinations'
        ));
    }

    /**
     * Update medical checklist (add radtech initials and X-ray image)
     */
    public function updateMedicalChecklist(Request $request, $id)
    {
        $medicalChecklist = MedicalChecklist::findOrFail($id);
        
        $validated = $request->validate([
            'chest_xray_done_by' => 'required|string|max:100',
            'xray_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25000',
        ]);

        if ($request->hasFile('xray_image') && $request->file('xray_image')->isValid()) {
            $path = $request->file('xray_image')->store('xray-images', 'public');
            $validated['xray_image_path'] = $path;
        }

        // Update chest X-ray completion
        if (array_key_exists('chest_xray_done_by', $validated)) {
            $medicalChecklist->chest_xray_done_by = $validated['chest_xray_done_by'];
        }
        if (array_key_exists('xray_image_path', $validated)) {
            $medicalChecklist->xray_image_path = $validated['xray_image_path'];
        }
        $medicalChecklist->save();

        // Create notification for admin when X-ray is completed
        $radtech = Auth::user();
        $patientName = $medicalChecklist->name;
        $examinationType = $medicalChecklist->pre_employment_record_id ? 'Pre-Employment' : 'Annual Physical';
        
        Notification::createForAdmin(
            'xray_completed',
            'X-Ray Examination Completed',
            "Radtech {$radtech->name} has completed X-ray examination for {$patientName} ({$examinationType}).",
            [
                'checklist_id' => $medicalChecklist->id,
                'patient_name' => $patientName,
                'radtech_name' => $radtech->name,
                'examination_type' => strtolower(str_replace('-', '_', $examinationType)),
                'completed_by' => $validated['chest_xray_done_by'],
                'has_image' => !empty($validated['xray_image_path'])
            ],
            'medium',
            $radtech,
            $medicalChecklist
        );

        // Determine redirect route based on examination type
        if ($medicalChecklist->pre_employment_record_id) {
            return redirect()->route('radtech.pre-employment-xray')
                ->with('success', 'X-ray information updated successfully. Record removed from your list.');
        } elseif ($medicalChecklist->patient_id) {
            return redirect()->route('radtech.annual-physical-xray')
                ->with('success', 'X-ray information updated successfully. Record removed from your list.');
        }

        return redirect()->route('radtech.dashboard')
            ->with('success', 'X-ray information updated successfully.');
    }

    /**
     * Store a new medical checklist (RadTech can create if missing)
     */
    public function storeMedicalChecklist(Request $request)
    {
        $validated = $request->validate([
            'examination_type' => 'required|in:pre-employment,annual-physical',
            'pre_employment_record_id' => 'required_if:examination_type,pre-employment|nullable|exists:pre_employment_records,id',
            'patient_id' => 'required_if:examination_type,annual-physical|nullable|exists:patients,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'age' => 'required|integer|min:0',
            'number' => 'nullable|string|max:255',
            'xray_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25000',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('xray_image')) {
            $path = $request->file('xray_image')->store('xray-images', 'public');
            $validated['xray_image_path'] = $path;
        }

        // Ensure foreign keys persist based on context
        if ($validated['examination_type'] === 'pre-employment' && $request->filled('pre_employment_record_id')) {
            $validated['pre_employment_record_id'] = (int)$request->input('pre_employment_record_id');
        }
        if ($validated['examination_type'] === 'annual-physical') {
            if ($request->filled('annual_physical_examination_id')) {
                $validated['annual_physical_examination_id'] = (int)$request->input('annual_physical_examination_id');
            }
            if ($request->filled('patient_id')) {
                $validated['patient_id'] = (int)$request->input('patient_id');
            }
        }

        $medicalChecklist = MedicalChecklist::create($validated);

        return redirect()->back()->with('success', 'Medical checklist created successfully.');
    }

    /**
     * Show radtech messages view
     */
    public function messages()
    {
        return view('radtech.messages');
    }

    /**
     * Get users that radtech can chat with (admin and doctor only)
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
            // Get the last message between radtech user and this user
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
     * Fetch messages for the current radtech
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

        // Get messages between radtech user and the specific user
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
     * Send a message (radtech can only send to admin or doctor)
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);
        
        $receiver = User::find($request->receiver_id);
        if (!in_array($receiver->role, ['admin', 'doctor'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);
        
        return response()->json($message);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id'
        ]);
        
        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count for the current radtech user.
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
