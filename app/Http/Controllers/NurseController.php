<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\User;
use App\Models\OpdExamination;
use App\Models\DrugTestResult;
use App\Models\Notification;
use App\Services\MedicalWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NurseController extends Controller
{
    /**
     * Show the nurse dashboard
     */
    public function dashboard()
    {
        // Get all approved patients with their examination status
        $patients = Patient::where('status', 'approved')
            ->with(['annualPhysicalExamination'])
            ->latest()
            ->take(5)
            ->get();
            
        $patientCount = Patient::where('status', 'approved')->count();

        // Get appointments with linked medical tests
        $appointments = Appointment::with(['patients', 'medicalTestCategory', 'medicalTest'])
            ->latest()
            ->take(5)
            ->get();
            
        $appointmentCount = Appointment::count();

        // Get all pre-employment records with their examination status
        $preEmployments = PreEmploymentRecord::with([
                'medicalTestCategory',
                'medicalTest',
                'preEmploymentExamination'
            ])
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();
            
        $preEmploymentCount = PreEmploymentRecord::where('status', 'approved')->count();

        // Get OPD walk-in patients (users with 'opd' role)
        $opdPatients = User::where('role', 'opd')
            ->with(['opdExamination'])
            ->latest()
            ->take(5)
            ->get();
            
        $opdCount = User::where('role', 'opd')->count();

        // Count all approved patients (nurses can work with all patients)
        $annualPhysicalCount = Patient::where('status', 'approved')->count();

        return view('nurse.dashboard', compact(
            'patients',
            'patientCount',
            'appointments',
            'appointmentCount',
            'preEmployments',
            'preEmploymentCount',
            'opdPatients',
            'opdCount',
            'annualPhysicalCount'
        ));
    }




    /**
     * Show pre-employment records with enhanced filtering
     */
    public function preEmployment(Request $request)
    {
        $query = PreEmploymentRecord::with([
                'medicalTestCategory', 
                'medicalTest',
                'preEmploymentExamination',
                'medicalChecklist'
            ])
            ->where('status', 'approved');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('company')) {
            $query->where('company_name', 'like', "%{$request->company}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Gender filtering
        if ($request->filled('gender')) {
            $query->where('sex', $request->gender);
        }

        // Date range filtering
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Examination status filtering - simplified to two tabs
        // Set default exam_status to 'needs_attention' if not specified
        $examStatus = $request->filled('exam_status') ? $request->exam_status : 'needs_attention';
        
        switch ($examStatus) {
            case 'needs_attention':
                // Default: Records that need nurse attention (no examination created yet)
                $query->whereDoesntHave('preEmploymentExamination');
                break;
                
            case 'exam_completed':
                // Records that have physical examinations completed
                $query->whereHas('preEmploymentExamination');
                break;
        }

        $preEmployments = $query->latest()->paginate(15);
        
        // Get companies for filter dropdown
        $companies = PreEmploymentRecord::distinct()->pluck('company_name')->filter()->sort()->values();
        
        return view('nurse.pre-employment', compact('preEmployments', 'companies'));
    }


    /**
     * Show annual physical patients with filtering
     */
    public function annualPhysical(Request $request)
    {
        $query = Patient::with(['annualPhysicalExamination', 'appointment', 'medicalTests'])
            ->where('status', 'approved')
            ->whereHas('appointment', function($q) {
                $q->where('status', 'approved');
            });

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply exam status filtering to match existing tabs - default to 'needs_attention'
        $examStatus = $request->filled('exam_status') ? $request->exam_status : 'needs_attention';
        
        switch ($examStatus) {
            case 'needs_attention':
                // Patients that need attention (no examination OR examination with no meaningful data)
                $query->where(function($mainQuery) {
                    // Patients without any annual physical examination record
                    $mainQuery->whereDoesntHave('annualPhysicalExamination')
                    // OR patients with examination but no meaningful data filled
                    ->orWhereHas('annualPhysicalExamination', function($q) {
                        $q->where(function($subQuery) {
                            // No physical exam data
                            $subQuery->where(function($sq) {
                                $sq->whereNull('physical_exam')
                                   ->orWhere('physical_exam', '[]')
                                   ->orWhere('physical_exam', '{}');
                            })
                            // AND no lab report data
                            ->where(function($sq) {
                                $sq->whereNull('lab_report')
                                   ->orWhere('lab_report', '[]')
                                   ->orWhere('lab_report', '{}');
                            })
                       
                            // AND no illness history
                            ->where(function($sq) {
                                $sq->whereNull('illness_history')
                                   ->orWhere('illness_history', '');
                            });
                        });
                    });
                });
                break;
                
            case 'exam_completed':
                // Patients with completed examinations (has meaningful data)
                $query->whereHas('annualPhysicalExamination', function($q) {
                    $q->where(function($subQuery) {
                        // Has physical exam data OR
                        $subQuery->where(function($sq) {
                            $sq->whereNotNull('physical_exam')
                               ->where('physical_exam', '!=', '[]')
                               ->where('physical_exam', '!=', '{}');
                        })
                        // Has lab report data OR
                        ->orWhere(function($sq) {
                            $sq->whereNotNull('lab_report')
                               ->where('lab_report', '!=', '[]')
                               ->where('lab_report', '!=', '{}');
                        })
                        // Has findings OR
                        ->orWhere(function($sq) {
                            $sq->whereNotNull('findings')
                               ->where('findings', '!=', '');
                        })
                        // Has illness history
                        ->orWhere(function($sq) {
                            $sq->whereNotNull('illness_history')
                               ->where('illness_history', '!=', '');
                        });
                    });
                });
                break;
                
            case 'all':
            default:
                // Show all patients - no additional filtering
                break;
        }

        $patients = $query->latest()->paginate(15);
        
        return view('nurse.annual-physical', compact('patients'));
    }

    /**
     * Show pre-employment edit form
     */
    public function editPreEmployment($id)
    {
        $preEmployment = \App\Models\PreEmploymentExamination::with([
            'preEmploymentRecord.medicalTest',
            'drugTestResults'
        ])->findOrFail($id);
        
        return view('nurse.pre-employment-edit', compact('preEmployment'));
    }

    /**
     * Update pre-employment examination
     */
    public function updatePreEmployment(Request $request, $id)
    {
        $preEmployment = \App\Models\PreEmploymentExamination::findOrFail($id);
        
        $validated = $request->validate([
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => 'nullable|array',
            'skin_marks' => 'nullable|string',
            'visual' => 'nullable|string',
            'ishihara_test' => 'nullable|string',
            'final_findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
            'drug_test' => 'nullable|array',
        ]);

        // Set status to 'Approved' to make it immediately visible to doctor
        $validated['status'] = 'Approved';
        
        $preEmployment->update($validated);
        
        // Calculate and store fitness assessment automatically
        $preEmployment->calculateFitnessAssessment();

        return redirect()->route('nurse.pre-employment')->with('success', 'Pre-employment examination updated and sent to doctor for review.');
    }

    /**
     * Show annual physical edit form
     */
    public function editAnnualPhysical($id)
    {
        $annualPhysical = \App\Models\AnnualPhysicalExamination::with('patient')->findOrFail($id);
        
        return view('nurse.annual-physical-edit', compact('annualPhysical'));
    }

    /**
     * Update annual physical examination
     */
    public function updateAnnualPhysical(Request $request, $id)
    {
        $annualPhysical = \App\Models\AnnualPhysicalExamination::findOrFail($id);
        
        $validated = $request->validate([
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => 'nullable|array',
            'skin_marks' => 'nullable|string',
            'visual' => 'nullable|string',
            'ishihara_test' => 'nullable|string',
            'final_findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'drug_test' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
            'fitness_assessment' => 'nullable|string',
            'drug_positive_count' => 'nullable|integer',
            'medical_abnormal_count' => 'nullable|integer',
            'physical_abnormal_count' => 'nullable|integer',
            'assessment_details' => 'nullable|string',
        ]);

        // Set status to completed when nurse updates examination
        $validated['status'] = 'completed';
        
        $annualPhysical->update($validated);
        
        // Calculate and store fitness assessment automatically
        $annualPhysical->calculateFitnessAssessment();

        return redirect()->route('nurse.annual-physical', ['exam_status' => 'needs_attention'])->with('success', 'Annual physical examination updated successfully.');
    }

    /**
     * Send annual physical examination to doctor
     */
    public function sendAnnualPhysicalToDoctor($id)
    {
        $annualPhysical = \App\Models\AnnualPhysicalExamination::findOrFail($id);
        
        // Update status to completed to make it visible to doctor
        $annualPhysical->update(['status' => 'completed']);
        
        // Create notification for doctor
        $patient = $annualPhysical->patient;
        $patientName = $patient ? $patient->full_name : $annualPhysical->name;
        
        Notification::createForRole(
            'doctor',
            'Annual Physical Examination Ready',
            "Annual physical examination for {$patientName} has been completed and is ready for doctor review.",
            json_encode([
                'type' => 'annual_physical_completed',
                'examination_id' => $annualPhysical->id,
                'patient_id' => $annualPhysical->patient_id,
                'patient_name' => $patientName
            ])
        );
        
        return redirect()->route('nurse.annual-physical', ['exam_status' => 'needs_attention'])
            ->with('success', "Annual physical examination for {$patientName} has been sent to doctor successfully.");
    }

    /**
     * Show medical checklist for pre-employment
     */
    public function showMedicalChecklistPreEmployment($recordId)
    {
        $preEmploymentRecord = PreEmploymentRecord::findOrFail($recordId);
        $medicalChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $recordId)
            ->where('examination_type', 'pre_employment')
            ->first();
        $examinationType = 'pre_employment';
        $number = $medicalChecklist->number ?? ('PPEP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT));
        $name = trim(($preEmploymentRecord->first_name ?? '') . ' ' . ($preEmploymentRecord->last_name ?? ''));
        $age = $preEmploymentRecord->age ?? null;
        $date = $medicalChecklist->date ?? now()->format('Y-m-d');
        
        return view('nurse.medical-checklist', compact('medicalChecklist', 'preEmploymentRecord', 'examinationType', 'number', 'name', 'age', 'date'));
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
            $medicalChecklist = \App\Models\MedicalChecklist::where('annual_physical_examination_id', $annualPhysicalExamination->id)
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->first();
        }
        
        // If no checklist found, also check by patient_id (for legacy records)
        if (!$medicalChecklist) {
            $medicalChecklist = \App\Models\MedicalChecklist::where('patient_id', $patientId)
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->whereNull('annual_physical_examination_id') // Only unlinked checklists
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        $examinationType = 'annual_physical';
        $number = $medicalChecklist->number ?? ('APMC-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT) . '-' . date('Ymd'));
        $name = $patient->full_name;
        $age = $patient->age;
        $date = $medicalChecklist->date ?? now()->format('Y-m-d');
        
        return view('nurse.medical-checklist', compact('medicalChecklist', 'patient', 'annualPhysicalExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Store medical checklist
     */
    public function storeMedicalChecklist(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'age' => 'required|integer',
            'number' => 'nullable|string',
            'examination_type' => 'required|string',
            'pre_employment_record_id' => 'nullable|integer',
            'patient_id' => 'nullable|integer',
            'annual_physical_examination_id' => 'nullable|integer',
            'opd_examination_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            // All examination fields
            'chest_xray_done_by' => 'nullable|string',
            'stool_exam_done_by' => 'nullable|string',
            'urinalysis_done_by' => 'nullable|string',
            'drug_test_done_by' => 'nullable|string',
            'blood_extraction_done_by' => 'nullable|string',
            'ecg_done_by' => 'nullable|string',
            'physical_exam_done_by' => 'nullable|string',
            'optional_exam' => 'nullable|string',
            'nurse_signature' => 'nullable|string',
        ]);

        // Set user_id to current nurse only if not already provided (for OPD, user_id is the patient ID)
        if (!isset($validated['user_id']) || empty($validated['user_id'])) {
            $validated['user_id'] = Auth::id();
        }

        // For annual physical: create examination record if it doesn't exist and link it
        if (($validated['examination_type'] === 'annual_physical' || $validated['examination_type'] === 'annual-physical') 
            && !empty($validated['patient_id'])) {
            
            // Find or create the examination record
            $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::where('patient_id', $validated['patient_id'])
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$annualPhysicalExam) {
                // Create new examination record
                $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::create([
                    'patient_id' => $validated['patient_id'],
                    'status' => 'Pending'
                ]);
            }
            
            // Link the checklist to this examination
            $validated['annual_physical_examination_id'] = $annualPhysicalExam->id;
        }

        // Check for existing checklist to prevent duplicates
        $existingChecklist = null;
        
        if (!empty($validated['pre_employment_record_id'])) {
            $existingChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $validated['pre_employment_record_id'])
                ->whereIn('examination_type', [$validated['examination_type'], str_replace('_', '-', $validated['examination_type'])])
                ->first();
        } elseif (!empty($validated['annual_physical_examination_id'])) {
            $existingChecklist = \App\Models\MedicalChecklist::where('annual_physical_examination_id', $validated['annual_physical_examination_id'])
                ->whereIn('examination_type', [$validated['examination_type'], str_replace('_', '-', $validated['examination_type'])])
                ->first();
        } elseif (!empty($validated['patient_id'])) {
            $existingChecklist = \App\Models\MedicalChecklist::where('patient_id', $validated['patient_id'])
                ->whereIn('examination_type', [$validated['examination_type'], str_replace('_', '-', $validated['examination_type'])])
                ->whereNull('annual_physical_examination_id') // Only unlinked checklists
                ->first();
        } elseif (!empty($validated['opd_examination_id'])) {
            $existingChecklist = \App\Models\MedicalChecklist::where('opd_examination_id', $validated['opd_examination_id'])
                ->whereIn('examination_type', [$validated['examination_type'], str_replace('_', '-', $validated['examination_type'])])
                ->first();
        }

        if ($existingChecklist) {
            // Update existing checklist
            $existingChecklist->update($validated);
            $checklist = $existingChecklist;
            $action = 'updated';
        } else {
            // Create new checklist
            $checklist = \App\Models\MedicalChecklist::create($validated);
            $action = 'created';
        }

        // Create notification for admin when medical checklist is completed
        if (!empty($validated['physical_exam_done_by'])) {
            $nurse = Auth::user();
            $patientName = $validated['name'];
            $examinationType = ucwords(str_replace('-', ' ', $validated['examination_type']));
            
            Notification::createForAdmin(
                'checklist_completed',
                'Medical Checklist Completed',
                "Nurse {$nurse->name} has completed the medical checklist for {$patientName} ({$examinationType}).",
                [
                    'checklist_id' => $checklist->id,
                    'patient_name' => $patientName,
                    'nurse_name' => $nurse->name,
                    'examination_type' => $validated['examination_type'],
                    'completed_by' => $validated['physical_exam_done_by']
                ],
                'medium',
                $nurse,
                $checklist
            );
        }

        // Trigger automatic workflow check
        $workflowService = new MedicalWorkflowService();
        $workflowService->onMedicalChecklistUpdated($checklist);

        // Redirect back to the main listing page with success message
        $redirectRoute = match($validated['examination_type']) {
            'pre-employment', 'pre_employment' => 'nurse.pre-employment',
            'annual-physical', 'annual_physical' => 'nurse.annual-physical',
            'opd' => 'nurse.opd',
            default => 'nurse.dashboard'
        };

        return redirect()->route($redirectRoute)->with('success', 'Medical checklist ' . $action . ' successfully. You can now proceed to create the examination form.');
    }

    /**
     * Update medical checklist
     */
    public function updateMedicalChecklist(Request $request, $id)
    {
        $medicalChecklist = \App\Models\MedicalChecklist::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'age' => 'required|integer',
            'number' => 'nullable|string',
            'examination_type' => 'required|string',
            'pre_employment_record_id' => 'nullable|integer',
            'patient_id' => 'nullable|integer',
            'annual_physical_examination_id' => 'nullable|integer',
            'opd_examination_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            // All examination fields
            'chest_xray_done_by' => 'nullable|string',
            'stool_exam_done_by' => 'nullable|string',
            'urinalysis_done_by' => 'nullable|string',
            'drug_test_done_by' => 'nullable|string',
            'blood_extraction_done_by' => 'nullable|string',
            'ecg_done_by' => 'nullable|string',
            'physical_exam_done_by' => 'nullable|string',
            'optional_exam' => 'nullable|string',
            'nurse_signature' => 'nullable|string',
        ]);

        $medicalChecklist->update($validated);

        // Create notification for admin when medical checklist is completed/updated
        if (!empty($validated['physical_exam_done_by'])) {
            $nurse = Auth::user();
            $patientName = $medicalChecklist->name;
            $examinationType = ucwords(str_replace('-', ' ', $medicalChecklist->examination_type));
            
            Notification::createForAdmin(
                'checklist_completed',
                'Medical Checklist Updated',
                "Nurse {$nurse->name} has updated the medical checklist for {$patientName} ({$examinationType}).",
                [
                    'checklist_id' => $medicalChecklist->id,
                    'patient_name' => $patientName,
                    'nurse_name' => $nurse->name,
                    'examination_type' => $medicalChecklist->examination_type,
                    'completed_by' => $validated['physical_exam_done_by']
                ],
                'medium',
                $nurse,
                $medicalChecklist
            );
        }

        // Trigger automatic workflow check
        $workflowService = new MedicalWorkflowService();
        $workflowService->onMedicalChecklistUpdated($medicalChecklist);

        // Redirect back to the main listing page with success message
        $examinationType = $request->input('examination_type');
        $redirectRoute = match($examinationType) {
            'pre-employment', 'pre_employment' => 'nurse.pre-employment',
            'annual-physical', 'annual_physical' => 'nurse.annual-physical',
            'opd' => 'nurse.opd',
            default => 'nurse.dashboard'
        };

        return redirect()->route($redirectRoute)->with('success', 'Medical checklist updated successfully. You can now proceed to create the examination form.');
    }

    /**
     * Show nurse messages view
     */
    public function messages()
    {
        return view('nurse.messages');
    }

    /**
     * Get users that nurses can chat with (admin and doctor only)
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
            // Get the last message between nurse user and this user
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
     * Fetch messages for the current nurse
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

        // Get messages between nurse user and the specific user
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
     * Send a message (nurse can only send to admin or doctor)
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
     * Get unread message count for the current nurse user.
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
     * Show create pre-employment examination form
     */
    public function createPreEmployment(Request $request)
    {
        $recordId = $request->query('record_id');
        $preEmploymentRecord = PreEmploymentRecord::with(['medicalTest'])->findOrFail($recordId);
        
        // Check if medical checklist exists and is completed (check both formats)
        $medicalChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $recordId)
            ->whereIn('examination_type', ['pre-employment', 'pre_employment'])
            ->first();
        
        // Check if checklist exists
        if (!$medicalChecklist) {
            return redirect()->route('nurse.medical-checklist.pre-employment', $recordId)
                ->with('error', 'Medical checklist not found. Please create and complete the checklist first.');
        }
        
        // Check if physical exam is signed
        if (empty($medicalChecklist->physical_exam_done_by)) {
            return redirect()->route('nurse.medical-checklist.pre-employment', $recordId)
                ->with('error', 'Please sign the Physical Examination field in the medical checklist before creating the examination form.');
        }
        
        // For drug test examinations, also check if drug test is signed
        $medicalTestName = strtolower($preEmploymentRecord->medicalTest->name ?? '');
        $requiresDrugTest = str_contains($medicalTestName, 'drug test');
        
        if ($requiresDrugTest && empty($medicalChecklist->drug_test_done_by)) {
            return redirect()->route('nurse.medical-checklist.pre-employment', $recordId)
                ->with('error', 'Please sign the Drug Test field in the medical checklist before creating the examination form.');
        }
        
        return view('nurse.pre-employment-create', compact('preEmploymentRecord'));
    }

    /**
     * Store new pre-employment examination
     */
    public function storePreEmployment(Request $request)
    {
        // Get pre-employment record with medical test information to determine validation rules
        $preEmploymentRecord = PreEmploymentRecord::with(['medicalTest'])->findOrFail($request->pre_employment_record_id);
        $medicalTestName = $preEmploymentRecord->medicalTest->name ?? '';
        $isAudiometryIshiharaOnly = strtolower($medicalTestName) === 'audiometry and ishihara only';
        $showIshiharaTest = in_array(strtolower($medicalTestName), [
            'audiometry and ishihara only',
            'pre-employment with drug test and audio and ishihara'
        ]);

        // Dynamic validation rules based on medical test type
        $validationRules = [
            'pre_employment_record_id' => 'required|exists:pre_employment_records,id',
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => $isAudiometryIshiharaOnly ? 'nullable|array' : 'required|array',
            'physical_exam.temp' => $isAudiometryIshiharaOnly ? 'nullable|string' : 'required|string',
            'physical_exam.height' => $isAudiometryIshiharaOnly ? 'nullable|string' : 'required|string',
            'physical_exam.weight' => $isAudiometryIshiharaOnly ? 'nullable|string' : 'required|string',
            'physical_exam.heart_rate' => $isAudiometryIshiharaOnly ? 'nullable|string' : 'required|string',
            'skin_marks' => $isAudiometryIshiharaOnly ? 'nullable|string' : 'required|string',
            'visual' => $isAudiometryIshiharaOnly ? 'nullable|string' : 'required|string',
            'ishihara_test' => $showIshiharaTest ? 'required|string' : 'nullable|string',
            'final_findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
        ];

        // Dynamic validation messages
        $validationMessages = [
        ];

        // Add validation messages only for fields that are required
        if (!$isAudiometryIshiharaOnly) {
            $validationMessages = array_merge($validationMessages, [
                'physical_exam.required' => 'Physical examination data is required.',
                'physical_exam.temp.required' => 'Temperature is required.',
                'physical_exam.height.required' => 'Height is required.',
                'physical_exam.weight.required' => 'Weight is required.',
                'physical_exam.heart_rate.required' => 'Heart rate is required.',
                'skin_marks.required' => 'Skin marks/tattoos are required.',
                'visual.required' => 'Visual acuity is required.',
            ]);
        }

        // Add Ishihara test validation message only if it's required
        if ($showIshiharaTest) {
            $validationMessages['ishihara_test.required'] = 'Ishihara test is required.';
        }

        $validated = $request->validate($validationRules, $validationMessages);

        // Log visual field for debugging
        \Log::info('Pre-Employment Examination - Visual field data:', [
            'visual' => $validated['visual'] ?? 'NOT SET',
            'request_visual' => $request->input('visual'),
            'all_validated' => array_keys($validated)
        ]);

        // Auto-populate linkage fields from the source record
        $record = PreEmploymentRecord::findOrFail($validated['pre_employment_record_id']);
        $validated['user_id'] = $record->created_by;
        $validated['name'] = $record->first_name . ' ' . $record->last_name;
        $validated['company_name'] = $record->company_name;
        $validated['date'] = now()->toDateString();
        // Set status to 'Approved' - immediately visible to doctor
        $validated['status'] = 'Approved';
        $validated['created_by'] = auth()->id();
        
        $examination = \App\Models\PreEmploymentExamination::create($validated);
        

        // Handle drug test form if present
        $this->handleDrugTestForm($request, [
            'user_id' => $validated['user_id'],
            'pre_employment_record_id' => $validated['pre_employment_record_id'],
            'pre_employment_examination_id' => $examination->id,
            'patient_name' => $validated['name']
        ]);

        // Trigger automatic workflow check
        $workflowService = new MedicalWorkflowService();
        $workflowService->onExaminationUpdated($examination, 'pre_employment');

        return redirect()->route('nurse.pre-employment')->with('success', 'Pre-employment examination created and sent to doctor for review.');
    }

    /**
     * Show create annual physical examination form
     */
    public function createAnnualPhysical(Request $request)
    {
        $patientId = $request->query('patient_id');
        $patient = Patient::with(['appointment.medicalTest', 'appointment', 'medicalTests'])->findOrFail($patientId);
        
        // Get the most recent examination record
        $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Check if medical checklist exists and is completed (linked to examination)
        $medicalChecklist = null;
        if ($annualPhysicalExam) {
            $medicalChecklist = \App\Models\MedicalChecklist::where('annual_physical_examination_id', $annualPhysicalExam->id)
                ->whereIn('examination_type', ['annual-physical', 'annual_physical'])
                ->first();
        }
        
        // Fallback: check by patient_id for unlinked checklists
        if (!$medicalChecklist) {
            $medicalChecklist = \App\Models\MedicalChecklist::where('patient_id', $patientId)
                ->whereIn('examination_type', ['annual-physical', 'annual_physical'])
                ->whereNull('annual_physical_examination_id')
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        // Check if checklist exists
        if (!$medicalChecklist) {
            return redirect()->route('nurse.medical-checklist.annual-physical', $patientId)
                ->with('error', 'Medical checklist not found. Please create and complete the checklist first.');
        }
        
        // Check if physical exam is signed
        if (empty($medicalChecklist->physical_exam_done_by)) {
            return redirect()->route('nurse.medical-checklist.annual-physical', $patientId)
                ->with('error', 'Please sign the Physical Examination field in the medical checklist before creating the examination form.');
        }
        
        // For drug test examinations, also check if drug test is signed
        $medicalTestName = strtolower($patient->appointment->medicalTest->name ?? '');
        $requiresDrugTest = str_contains($medicalTestName, 'drug test');
        
        if ($requiresDrugTest && empty($medicalChecklist->drug_test_done_by)) {
            return redirect()->route('nurse.medical-checklist.annual-physical', $patientId)
                ->with('error', 'Please sign the Drug Test field in the medical checklist before creating the examination form.');
        }
        
        return view('nurse.annual-physical-create', compact('patient'));
    }

    /**
     * Store new annual physical examination
     */
    public function storeAnnualPhysical(Request $request)
    {
        // Get patient with medical test information to determine validation rules
        $patient = Patient::with(['appointment.medicalTest', 'appointment', 'medicalTests'])->findOrFail($request->patient_id);
        $medicalTestName = $patient->appointment->medicalTest->name ?? '';
        $isAnnualMedicalExam = in_array(strtolower($medicalTestName), [
            'annual medical examination',
            'annual medical examination with drug test',
            'annual medical examination with drug test and ecg'
        ]);

        // Dynamic validation rules based on medical test type
        $validationRules = [
            'patient_id' => 'required|exists:patients,id',
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => 'required|array',
            'physical_exam.temp' => 'required|string',
            'physical_exam.height' => 'required|string',
            'physical_exam.heart_rate' => 'required|string',
            'physical_exam.weight' => 'required|string',
            'skin_marks' => 'required|string',
            'visual' => 'required|string',
            'ishihara_test' => $isAnnualMedicalExam ? 'nullable|string' : 'required|string',
            'final_findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
            'drug_test' => 'nullable|array',
        ];

        // Dynamic validation messages
        $validationMessages = [
            'physical_exam.required' => 'Physical examination data is required.',
            'physical_exam.temp.required' => 'Temperature is required.',
            'physical_exam.height.required' => 'Height is required.',
            'physical_exam.heart_rate.required' => 'Heart rate is required.',
            'physical_exam.weight.required' => 'Weight is required.',
            'skin_marks.required' => 'Skin identification marks are required.',
            'visual.required' => 'Visual examination is required.',
        ];

        // Add Ishihara test validation message only if it's required
        if (!$isAnnualMedicalExam) {
            $validationMessages['ishihara_test.required'] = 'Ishihara test is required.';
        }

        $validated = $request->validate($validationRules, $validationMessages);

        // Log visual field for debugging
        \Log::info('Annual Physical Examination - Visual field data:', [
            'visual' => $validated['visual'] ?? 'NOT SET',
            'request_visual' => $request->input('visual'),
            'all_validated' => array_keys($validated)
        ]);

        // Auto-populate linkage fields from the patient
        $patient = Patient::findOrFail($validated['patient_id']);
        $validated['user_id'] = Auth::id();
        $validated['name'] = $patient->full_name;
        $validated['date'] = now()->toDateString();
        // Set status to 'completed' - immediately visible to doctor
        $validated['status'] = 'completed';
        $validated['created_by'] = auth()->id();
        
        // Set default lab_report to ensure it appears in "Completed" tab
        // The filtering logic requires lab_report to have meaningful data
        if (!isset($validated['lab_report']) || empty($validated['lab_report'])) {
            $validated['lab_report'] = [
                'nurse_examination_completed' => true, 
                'completed_at' => now()->format('Y-m-d H:i:s'),
                'status' => 'completed_by_nurse'
            ];
        }
        
        $examination = \App\Models\AnnualPhysicalExamination::create($validated);
        
        // Log created examination data
        \Log::info('Annual Physical Examination created:', [
            'id' => $examination->id,
            'visual' => $examination->visual,
            'visual_from_db' => $examination->fresh()->visual,
            'lab_report_set' => $validated['lab_report'] ?? 'NOT SET',
            'lab_report_from_db' => $examination->fresh()->lab_report
        ]);

        // Handle drug test form if present
        $this->handleDrugTestForm($request, [
            'user_id' => $patient->user_id ?? Auth::id(),
            'appointment_id' => $patient->appointment->id ?? null,
            'annual_physical_examination_id' => $examination->id,
            'patient_name' => $validated['name']
        ]);

        // Create notification for admin
        $nurse = Auth::user();
        Notification::createForAdmin(
            'annual_physical_created',
            'Annual Physical Examination Created',
            "Nurse {$nurse->name} has created an annual physical examination for patient {$patient->full_name}.",
            [
                'examination_id' => $examination->id,
                'patient_id' => $patient->id,
                'patient_name' => $patient->full_name,
                'nurse_name' => $nurse->name,
                'examination_date' => $examination->date
            ],
            'medium',
            $nurse,
            $examination
        );

        // Trigger automatic workflow check
        $workflowService = new MedicalWorkflowService();
        $workflowService->onExaminationUpdated($examination, 'annual_physical');

        return redirect()->route('nurse.annual-physical')->with('success', 'Annual physical examination created successfully.');
    }

    /**
     * Show OPD walk-in patients with filtering
     */
    public function opd(Request $request)
    {
        $query = User::with(['opdExamination'])
            ->where('role', 'opd');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Apply exam status filtering - default to 'needs_attention'
        $examStatus = $request->filled('exam_status') ? $request->exam_status : 'needs_attention';
        
        switch ($examStatus) {
            case 'needs_attention':
                // Patients that need attention (no examination created yet)
                $query->whereDoesntHave('opdExamination');
                break;
                
            case 'exam_completed':
                // Patients with completed examinations
                $query->whereHas('opdExamination');
                break;
        }

        $opdPatients = $query->latest()->get();
        
        return view('nurse.opd', compact('opdPatients'));
    }

    /**
     * Show create OPD examination form
     */
    public function createOpdExamination(Request $request)
    {
        $userId = $request->query('user_id');
        $opdPatient = User::where('role', 'opd')->findOrFail($userId);
        
        return view('nurse.opd-create', compact('opdPatient'));
    }

    /**
     * Store new OPD examination
     */
    public function storeOpdExamination(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => 'required|array',
            'physical_exam.temp' => 'required|string',
            'physical_exam.height' => 'required|string',
            'physical_exam.weight' => 'required|string',
            'physical_exam.heart_rate' => 'required|string',
            'skin_marks' => 'required|string',
            'visual' => 'required|string',
            'ishihara_test' => 'required|string',
            'final_findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
        ], [
            'physical_exam.required' => 'Physical examination data is required.',
            'physical_exam.temp.required' => 'Temperature is required.',
            'physical_exam.height.required' => 'Height is required.',
            'physical_exam.weight.required' => 'Weight is required.',
            'physical_exam.heart_rate.required' => 'Heart rate is required.',
            'skin_marks.required' => 'Skin marks/tattoos are required.',
            'visual.required' => 'Visual acuity is required.',
            'ishihara_test.required' => 'Ishihara test is required.',
        ]);

        // Auto-populate linkage fields from the OPD patient
        $opdPatient = User::findOrFail($validated['user_id']);
        $validated['name'] = trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? ''));
        $validated['date'] = now()->toDateString();
        // Set status to 'pending' - doctor can view but won't auto-appear in review queue
        $validated['status'] = 'pending';
        
        $examination = OpdExamination::create($validated);

        // Handle drug test form if present
        $this->handleDrugTestForm($request, [
            'user_id' => $validated['user_id'],
            'opd_examination_id' => $examination->id,
            'patient_name' => $validated['name']
        ]);

        // Trigger automatic workflow check
        $workflowService = new MedicalWorkflowService();
        $workflowService->onExaminationUpdated($examination, 'opd');

        return redirect()->route('nurse.opd')->with('success', 'OPD examination created successfully.');
    }

    /**
     * Show edit OPD examination form
     */
    public function editOpdExamination($id)
    {
        $opdExamination = OpdExamination::with('user')->findOrFail($id);
        
        return view('nurse.opd-edit', compact('opdExamination'));
    }

    /**
     * Update OPD examination
     */
    public function updateOpdExamination(Request $request, $id)
    {
        $opdExamination = OpdExamination::findOrFail($id);
        
        $validated = $request->validate([
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => 'nullable|array',
            'skin_marks' => 'nullable|string',
            'visual' => 'nullable|string',
            'ishihara_test' => 'nullable|string',
            'final_findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
        ]);

        $opdExamination->update($validated);

        return redirect()->route('nurse.opd')->with('success', 'OPD examination updated successfully.');
    }

    /**
     * Send OPD examination to doctor
     */
    public function sendOpdToDoctor($userId)
    {
        $opdPatient = User::where('role', 'opd')->findOrFail($userId);
        $exam = OpdExamination::firstOrCreate(
            ['user_id' => $userId],
            [
                'name' => trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? '')),
                'date' => now()->toDateString(),
                'status' => 'pending',
            ]
        );
        
        // Mark as completed from nurse to send up to doctor
        $exam->update(['status' => 'completed']);
        
        return redirect()->route('nurse.opd')->with('success', 'OPD examination sent to doctor.');
    }

    /**
     * Show medical checklist for OPD
     */
    public function showMedicalChecklistOpd($userId)
    {
        $opdPatient = User::where('role', 'opd')->findOrFail($userId);
        $opdExamination = OpdExamination::where('user_id', $userId)->first();
        
        // Look for medical checklist by user_id first, then by opd_examination_id
        $medicalChecklist = \App\Models\MedicalChecklist::where('user_id', $userId)
            ->where('examination_type', 'opd')
            ->first();
            
        if (!$medicalChecklist && $opdExamination) {
            $medicalChecklist = \App\Models\MedicalChecklist::where('opd_examination_id', $opdExamination->id)->first();
        }
        
        $examinationType = 'opd';
        $number = 'OPD-' . str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? ''));
        $age = $opdPatient->age ?? null;
        $date = now()->format('Y-m-d');
        
        return view('nurse.medical-checklist', compact('medicalChecklist', 'opdPatient', 'opdExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Handle drug test form submission
     */
    private function handleDrugTestForm(Request $request, array $context)
    {
        // Check if drug test data is present in the request
        if (!$request->has('drug_test') || empty($request->input('drug_test'))) {
            return;
        }

        $drugTestData = $request->input('drug_test');
        
        // Skip if no results are provided
        if (empty($drugTestData['methamphetamine_result']) && empty($drugTestData['marijuana_result'])) {
            return;
        }

        // Validate drug test data
        $validatedDrugTest = $request->validate([
            'drug_test.patient_name' => 'required|string|max:255',
            'drug_test.address' => 'required|string',
            'drug_test.age' => 'required|integer|min:1|max:150',
            'drug_test.gender' => 'required|in:Male,Female',
            'drug_test.examination_datetime' => 'required|date',
            'drug_test.last_intake_date' => 'nullable|date',
            'drug_test.test_method' => 'required|string|max:255',
            'drug_test.methamphetamine_result' => 'required|in:Negative,Positive',
            'drug_test.methamphetamine_remarks' => 'nullable|string',
            'drug_test.marijuana_result' => 'required|in:Negative,Positive',
            'drug_test.marijuana_remarks' => 'nullable|string',
        ], [
            'drug_test.patient_name.required' => 'Patient name is required for drug test.',
            'drug_test.address.required' => 'Patient address is required for drug test.',
            'drug_test.age.required' => 'Patient age is required for drug test.',
            'drug_test.gender.required' => 'Patient gender is required for drug test.',
            'drug_test.examination_datetime.required' => 'Examination date and time is required for drug test.',
            'drug_test.test_method.required' => 'Test method is required for drug test.',
            'drug_test.methamphetamine_result.required' => 'Methamphetamine test result is required.',
            'drug_test.marijuana_result.required' => 'Marijuana test result is required.',
        ]);

        // Prepare drug test result data
        $drugTestResult = array_merge($validatedDrugTest['drug_test'], [
            'user_id' => $context['user_id'],
            'nurse_id' => Auth::id(),
            'pre_employment_record_id' => $context['pre_employment_record_id'] ?? null,
            'pre_employment_examination_id' => $context['pre_employment_examination_id'] ?? null,
            'annual_physical_examination_id' => $context['annual_physical_examination_id'] ?? null,
            'appointment_id' => $context['appointment_id'] ?? null,
            'opd_examination_id' => $context['opd_examination_id'] ?? null,
            'test_conducted_by' => Auth::user()->full_name,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Create drug test result
        DrugTestResult::create($drugTestResult);
    }

    /**
     * Check if a medical test requires drug testing
     */
    private function requiresDrugTest($medicalTestName): bool
    {
        $drugTestRequiredTests = [
            // Pre-Employment Tests
            'pre-employment with drug test',
            'pre-employment with ecg and drug test',
            'pre-employment with drug test and audio and ishihara',
            'drug test only (bring valid i.d)',
            
            // Annual Physical Tests
            'annual medical with drug test',
            'annual medical with drug test and ecg',
            'annual medical examination with drug test',
            'annual medical examination with drug test and ecg',
        ];

        return in_array(strtolower($medicalTestName), $drugTestRequiredTests);
    }

    /**
     * Show the edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('nurse.profile.edit', compact('user'));
    }

    /**
     * Update the nurse's profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birthday' => 'nullable|date',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Check current password if user wants to change password
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $validated['password'] = Hash::make($request->new_password);
        }

        // Remove password fields if not changing password
        unset($validated['current_password'], $validated['new_password'], $validated['new_password_confirmation']);

        // Calculate age if birthday is provided
        if (isset($validated['birthday'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['birthday'])->age;
        }

        $user->update($validated);

        return redirect()->route('nurse.profile.edit')->with('success', 'Profile updated successfully!');
    }
}
