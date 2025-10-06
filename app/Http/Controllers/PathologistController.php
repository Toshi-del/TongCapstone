<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\User;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use App\Models\OpdExamination;
use App\Models\MedicalTest;
use App\Models\MedicalTestCategory;
use App\Models\MedicalChecklist;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PathologistController extends Controller
{
    /**
     * Show the pathologist dashboard with comprehensive metrics
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get comprehensive dashboard metrics
        $metrics = $this->getDashboardMetrics();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get lab statistics
        $labStats = $this->getLabStatistics();
        
        // Get pending tasks
        $pendingTasks = $this->getPendingTasks();
        
        // Get blood chemistry tests
        $bloodChemistryTests = $this->getBloodChemistryTests();
        
        return view('pathologist.dashboard', compact(
            'metrics',
            'recentActivities', 
            'labStats',
            'pendingTasks',
            'bloodChemistryTests'
        ));
    }

    /**
     * Show pre-employment records with enhanced filtering and search
     */
    public function preEmployment(Request $request)
    {
        $query = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest', 'preEmploymentExamination', 'medicalChecklist'])
            ->whereIn('status', ['Approved', 'approved']);

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company')) {
            $query->where('company_name', 'like', "%{$request->company}%");
        }

        // Lab status filtering - simplified to two tabs
        // Set default lab_status to 'needs_attention' if not specified
        $labStatus = $request->filled('lab_status') ? $request->lab_status : 'needs_attention';
        
        switch ($labStatus) {
            case 'needs_attention':
                // Default: Records that need pathologist attention (blood collection completed but no lab results yet)
                $query->where(function($mainQuery) {
                    // Option 1: Blood collection completed by phlebotomist
                    $mainQuery->whereHas('medicalChecklist', function($q) {
                        $q->where('examination_type', 'pre_employment')
                          ->whereNotNull('blood_extraction_done_by')
                          ->where('blood_extraction_done_by', '!=', '');
                    })
                    // Option 2: OR records with pre-employment examinations created
                    ->orWhereHas('preEmploymentExamination', function($q) {
                        $q->whereIn('status', ['completed', 'sent_to_admin']);
                    });
                })
                ->whereDoesntHave('preEmploymentExamination', function($q) {
                    $q->whereNotNull('lab_report')
                      ->where('lab_report', '!=', '[]')
                      ->where('lab_report', '!=', '{}')
                      ->where(function($subQuery) {
                          // Check for actual meaningful lab results (not just "Not available" or empty)
                          $subQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"cbc_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"urinalysis_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"stool_exam_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"fbs_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"bun_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"creatinine_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"additional_exams_results\"')) NOT IN ('', 'Not available')");
                      });
                });
                break;
                
            case 'lab_completed':
                // Records with actual meaningful lab results completed
                $query->where(function($mainQuery) {
                    // Option 1: Blood collection completed by phlebotomist
                    $mainQuery->whereHas('medicalChecklist', function($q) {
                        $q->where('examination_type', 'pre_employment')
                          ->whereNotNull('blood_extraction_done_by')
                          ->where('blood_extraction_done_by', '!=', '');
                    })
                    // Option 2: OR records with pre-employment examinations created
                    ->orWhereHas('preEmploymentExamination', function($q) {
                        $q->whereIn('status', ['completed', 'sent_to_admin']);
                    });
                })
                ->whereHas('preEmploymentExamination', function($q) {
                    $q->whereNotNull('lab_report')
                      ->where('lab_report', '!=', '[]')
                      ->where('lab_report', '!=', '{}')
                      ->where(function($subQuery) {
                          // Check for actual meaningful lab results (not just "Not available" or empty)
                          $subQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"cbc_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"urinalysis_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"stool_exam_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"fbs_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"bun_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"creatinine_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"additional_exams_results\"')) NOT IN ('', 'Not available')");
                      });
                });
                break;
        }

        $preEmployments = $query->latest()->paginate(15);
        
        // Get companies for filter dropdown
        $companies = PreEmploymentRecord::distinct()->pluck('company_name')->filter()->sort()->values();
        
        return view('pathologist.pre-employment', compact('preEmployments', 'companies'));
    }

    /**
     * Show a specific pre-employment record
     */
    public function showPreEmployment($id)
    {
        $preEmployment = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest', 'preEmploymentExamination'])
            ->findOrFail($id);
        
        return view('pathologist.pre-employment-show', compact('preEmployment'));
    }

    /**
     * Show annual physical examination patients with enhanced filtering
     */
    public function annualPhysical(Request $request)
    {
        // Start with all approved patients from appointments
        $query = Patient::with(['appointment.medicalTestCategory', 'appointment.medicalTest', 'annualPhysicalExamination', 'medicalChecklists'])
            ->where('status', 'approved')
            ->whereHas('appointment', function($q) {
                $q->where('status', 'approved');
            });

        // Apply basic filters
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

        if ($request->filled('gender')) {
            $query->where('sex', $request->gender);
        }

        if ($request->filled('company')) {
            $query->where('company_name', 'like', "%{$request->company}%");
        }

        // Simplified lab status filtering
        $labStatus = $request->filled('lab_status') ? $request->lab_status : 'needs_attention';
        
        switch ($labStatus) {
            case 'needs_attention':
                // Show patients that need lab work (either no lab results yet OR blood extraction done but no complete lab report)
                $query->where(function($mainQuery) {
                    // Patients without any annual physical examination record
                    $mainQuery->whereDoesntHave('annualPhysicalExamination')
                    // OR patients with blood extraction done but incomplete lab results
                    ->orWhere(function($subQuery) {
                        $subQuery->whereHas('medicalChecklists', function($q) {
                            $q->where('examination_type', 'annual_physical')
                              ->whereNotNull('blood_extraction_done_by')
                              ->where('blood_extraction_done_by', '!=', '');
                        })
                        ->whereDoesntHave('annualPhysicalExamination', function($q) {
                            $q->whereNotNull('lab_report')
                              ->where('lab_report', '!=', '[]')
                              ->where('lab_report', '!=', '{}');
                        });
                    });
                });
                break;
                
            case 'lab_completed':
                // Show patients with completed lab results
                $query->whereHas('annualPhysicalExamination', function($q) {
                    $q->whereNotNull('lab_report')
                      ->where('lab_report', '!=', '[]')
                      ->where('lab_report', '!=', '{}')
                      ->where('lab_report', '!=', 'null')
                      ->where(function($subQuery) {
                          // Additional check for meaningful lab results
                          $subQuery->whereRaw("JSON_LENGTH(lab_report) > 0")
                                   ->orWhereRaw("CHAR_LENGTH(lab_report) > 2");
                      });
                });
                break;
        }

        // Exclude patients whose results have already been sent to company (unless viewing completed lab status)
        if ($labStatus !== 'lab_completed') {
            $query->whereDoesntHave('annualPhysicalExamination', function ($q) {
                $q->whereIn('status', ['sent_to_company']);
            });
        }

        $patients = $query->latest()->paginate(15);
        
        return view('pathologist.annual-physical', compact('patients'));
    }

    /**
     * Show medical checklist with enhanced functionality
     */
    public function medicalChecklist(Request $request)
    {
        $query = MedicalChecklist::with(['patient', 'preEmploymentRecord'])
            ->where(function($query) {
                $query->whereHas('patient', function($subQuery) {
                    $subQuery->where('status', 'approved');
                })
                ->orWhereHas('preEmploymentRecord', function($subQuery) {
                    $subQuery->where('status', 'approved');
                });
            });

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('examination_type')) {
            $query->where('examination_type', $request->examination_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where(function($q) {
                    $q->whereNull('blood_extraction_done_by')
                      ->orWhere('blood_extraction_done_by', '');
                });
            } elseif ($request->status === 'in_progress') {
                $query->whereNotNull('blood_extraction_done_by')
                      ->where('blood_extraction_done_by', '!=', '');
            }
        }

        $checklists = $query->latest()->paginate(20);

        // Handle patient data if coming from annual physical or pre-employment
        $patient = null;
        $preEmploymentRecord = null;
        $examinationType = $request->get('examination_type', 'annual_physical');
        
        
        // Set default values for the form
        $medicalChecklist = null;
        $annualPhysicalExamination = null;
        $name = '';
        $date = now()->format('Y-m-d');
        $age = '';
        $number = '';
        $optionalExam = 'Audiometry/Ishihara';

        // Handle edit mode
        if ($request->has('edit') && $request->edit) {
            $medicalChecklist = MedicalChecklist::find($request->edit);
            if ($medicalChecklist) {
                $name = $medicalChecklist->name;
                $date = $medicalChecklist->date;
                $age = $medicalChecklist->age;
                $number = $medicalChecklist->number;
                $optionalExam = $medicalChecklist->optional_exam;
                $examinationType = $medicalChecklist->examination_type;
                
                // Load related data
                if ($medicalChecklist->patient_id) {
                    $patient = $medicalChecklist->patient;
                }
                if ($medicalChecklist->pre_employment_record_id) {
                    $preEmploymentRecord = $medicalChecklist->preEmploymentRecord;
                }
            }
        }

        // Check for patient_id parameter
        if ($request->has('patient_id') && $request->patient_id) {
            $patient = Patient::find($request->patient_id);
            if ($patient) {
                $name = $patient->full_name;
                $age = $patient->age;
                $number = 'APEP-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
                
                // Try to find existing medical checklist for this patient
                if (!$medicalChecklist) {
                    $medicalChecklist = MedicalChecklist::where('patient_id', $patient->id)
                        ->where('examination_type', 'annual_physical')
                        ->first();
                }
            }
        }
        
        // Check for pre_employment_record_id parameter
        if ($request->has('pre_employment_record_id') && $request->pre_employment_record_id) {
            $preEmploymentRecord = PreEmploymentRecord::find($request->pre_employment_record_id);
            if ($preEmploymentRecord) {
                $name = $preEmploymentRecord->first_name . ' ' . $preEmploymentRecord->last_name;
                $age = $preEmploymentRecord->age;
                $number = 'PPEP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT);
                $examinationType = 'pre_employment';
                
                // Try to find existing medical checklist for this record (remove examination_type filter for now)
                if (!$medicalChecklist) {
                    $medicalChecklist = MedicalChecklist::where('pre_employment_record_id', $preEmploymentRecord->id)
                        ->first();
                }
            }
        }

        return view('pathologist.medical-checklist', compact(
            'checklists', 
            'examinationType', 
            'medicalChecklist', 
            'patient', 
            'preEmploymentRecord', 
            'annualPhysicalExamination',
            'name',
            'date',
            'age',
            'number',
            'optionalExam'
        ));
    }

    /**
     * Store medical checklist with enhanced validation
     */
    public function storeMedicalChecklist(Request $request)
    {
        
        // Add validation for required fields
        $request->validate([
            'examination_type' => 'required|string|in:annual_physical,pre_employment',
            'name' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'age' => 'nullable|integer|min:0',
            'number' => 'nullable|string|max:255',
            'stool_exam_done_by' => 'nullable|string|max:255',
            'urinalysis_done_by' => 'nullable|string|max:255',
            'optional_exam' => 'nullable|string|max:255',
            'blood_extraction_done_by' => 'nullable|string|max:255',
            'chest_xray_done_by' => 'nullable|string|max:255',
            'drug_test_done_by' => 'nullable|string|max:255',
            'ecg_done_by' => 'nullable|string|max:255',
            'physical_exam_done_by' => 'nullable|string|max:255',
            'patient_id' => 'nullable|exists:patients,id',
            'pre_employment_record_id' => 'nullable|exists:pre_employment_records,id',
        ]);

        try {
            DB::beginTransaction();

            // Get patient/record data - use form data first, then fallback to defaults
            $patient = null;
            $preEmploymentRecord = null;
            $name = $request->name ?: 'Unknown';
            $age = $request->age ?: 0;
            $number = $request->number ?: 'UNKNOWN-0000';
            $date = $request->date ?: now()->format('Y-m-d');

            // Always try to get the patient/record from the request, regardless of examination_type
            if ($request->has('patient_id') && $request->patient_id) {
                $patient = Patient::find($request->patient_id);
                if ($patient) {
                    // Only override if form data is empty/default
                    if ($name === 'Unknown' || empty($name)) {
                        $name = $patient->full_name ?: 'Unknown Patient';
                    }
                    if ($age == 0) {
                        $age = (int) $patient->age ?: 0;
                    }
                    if ($number === 'UNKNOWN-0000' || empty($number)) {
                        $number = 'APEP-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
                    }
                }
            }
            
            if ($request->has('pre_employment_record_id') && $request->pre_employment_record_id) {
                $preEmploymentRecord = PreEmploymentRecord::find($request->pre_employment_record_id);
                if ($preEmploymentRecord) {
                    // Only override if form data is empty/default
                    if ($name === 'Unknown' || empty($name)) {
                        $name = trim($preEmploymentRecord->first_name . ' ' . $preEmploymentRecord->last_name) ?: 'Unknown Employee';
                    }
                    if ($age == 0) {
                        $age = (int) $preEmploymentRecord->age ?: 0;
                    }
                    if ($number === 'UNKNOWN-0000' || empty($number)) {
                        $number = 'PPEP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT);
                    }
                }
            }

            // Prepare data for creation
            $data = [
                'user_id' => auth()->id(), // Add the authenticated user ID
                'name' => $name,
                'date' => $date,
                'age' => $age,
                'number' => $number,
                'examination_type' => $request->examination_type,
                'stool_exam_done_by' => $request->stool_exam_done_by,
                'urinalysis_done_by' => $request->urinalysis_done_by,
                'optional_exam' => $request->optional_exam,
                'blood_extraction_done_by' => $request->blood_extraction_done_by,
                'chest_xray_done_by' => $request->chest_xray_done_by,
                'drug_test_done_by' => $request->drug_test_done_by,
                'ecg_done_by' => $request->ecg_done_by,
                'physical_exam_done_by' => $request->physical_exam_done_by,
            ];

            // Set the appropriate foreign key
            if ($patient) {
                $data['patient_id'] = $patient->id;
            } elseif ($preEmploymentRecord) {
                $data['pre_employment_record_id'] = $preEmploymentRecord->id;
            }
            $medicalChecklist = MedicalChecklist::create($data);

            DB::commit();

            // Redirect back to the form with the created checklist data
            $redirectUrl = route('pathologist.medical-checklist');
            if ($preEmploymentRecord) {
                $redirectUrl .= '?pre_employment_record_id=' . $preEmploymentRecord->id . '&examination_type=pre_employment';
            } elseif ($patient) {
                $redirectUrl .= '?patient_id=' . $patient->id . '&examination_type=annual_physical';
            }
            
            return redirect($redirectUrl)->with('success', 'Medical checklist created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create medical checklist: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update medical checklist
     */
    public function updateMedicalChecklist(Request $request, $id)
    {
        $medicalChecklist = MedicalChecklist::findOrFail($id);

        $request->validate([
            'stool_exam_done_by' => 'nullable|string|max:255',
            'urinalysis_done_by' => 'nullable|string|max:255',
            'optional_exam' => 'nullable|string|max:255',
            'blood_extraction_done_by' => 'nullable|string|max:255',
            'chest_xray_done_by' => 'nullable|string|max:255',
            'drug_test_done_by' => 'nullable|string|max:255',
            'ecg_done_by' => 'nullable|string|max:255',
            'physical_exam_done_by' => 'nullable|string|max:255',
        ]);

        try {
            $updateData = $request->only([
                'stool_exam_done_by',
                'urinalysis_done_by',
                'optional_exam',
                'blood_extraction_done_by',
                'chest_xray_done_by',
                'drug_test_done_by',
                'ecg_done_by',
                'physical_exam_done_by'
            ]);
            
            // Add user_id to track who updated the record
            $updateData['user_id'] = auth()->id();
            
            $medicalChecklist->update($updateData);

            // Redirect back to the form with the updated checklist data
            $redirectUrl = route('pathologist.medical-checklist');
            if ($medicalChecklist->pre_employment_record_id) {
                $redirectUrl .= '?pre_employment_record_id=' . $medicalChecklist->pre_employment_record_id . '&examination_type=pre_employment';
            } elseif ($medicalChecklist->patient_id) {
                $redirectUrl .= '?patient_id=' . $medicalChecklist->patient_id . '&examination_type=annual_physical';
            }
            
            return redirect($redirectUrl)->with('success', 'Medical checklist updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update medical checklist: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Show pathologist messages view
     */
    public function messages()
    {
        return view('pathologist.messages');
    }

    /**
     * Get users that pathologists can chat with (admin and doctor only)
     */
    public function chatUsers()
    {
        $currentUser = Auth::user();
        $users = User::whereIn('role', ['admin', 'doctor'])
            ->where('id', '!=', $currentUser->id)
            ->select('id', 'fname', 'lname', 'role', 'company')
            ->get();
            
        // Add unread message count for each user
        $usersWithUnread = $users->map(function($user) use ($currentUser) {
            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $currentUser->id)
                ->whereNull('read_at')
                ->count();
            $user->unread_count = $unreadCount;
            return $user;
        });
        
        return response()->json([
            'current_user' => $currentUser->only(['id', 'fname', 'lname', 'role']),
            'filtered_users' => $usersWithUnread
        ]);
    }

    /**
     * Fetch messages for the current pathologist
     */
    public function fetchMessages()
    {
        $userId = Auth::id();
        
        // Mark messages to this user as delivered if not set
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
     * Send a message (pathologist can only send to admin or doctor)
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
     * Get comprehensive dashboard metrics
     */
    private function getDashboardMetrics()
    {
        return [
            'total_patients' => Patient::where('status', 'approved')->count(),
            'total_pre_employment' => PreEmploymentRecord::where('status', 'approved')->count(),
            'pending_lab_requests' => $this->getPendingLabRequests(),
            'blood_samples_in_process' => $this->getBloodSamplesInProcess(),
            'results_to_review' => $this->getResultsToReview(),
            'completed_today' => $this->getCompletedToday(),
            'monthly_completed' => $this->getMonthlyCompleted(),
            'average_processing_time' => $this->getAverageProcessingTime(),
        ];
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities()
    {
        $activities = collect();
        
        // Recent medical checklists
        $recentChecklists = MedicalChecklist::with(['patient', 'preEmploymentRecord'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($checklist) {
                $patientName = $checklist->patient ? $checklist->patient->full_name : 
                              ($checklist->preEmploymentRecord ? $checklist->preEmploymentRecord->full_name : $checklist->name);
                
                return [
                    'type' => 'medical_checklist',
                    'title' => 'Medical Checklist Created',
                    'description' => "Created for {$patientName}",
                    'time' => $checklist->created_at,
                    'icon' => 'fas fa-clipboard-list',
                    'color' => 'blue'
                ];
            });
        
        $activities = $activities->merge($recentChecklists);
        
        // Recent completed examinations
        $recentExaminations = AnnualPhysicalExamination::with('patient')
            ->where('status', 'completed')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($exam) {
                return [
                    'type' => 'annual_physical',
                    'title' => 'Annual Physical Completed',
                    'description' => "Completed for {$exam->name}",
                    'time' => $exam->updated_at,
                    'icon' => 'fas fa-user-check',
                    'color' => 'green'
                ];
            });
        
        $activities = $activities->merge($recentExaminations);
        
        return $activities->sortByDesc('time')->take(8);
    }

    /**
     * Get lab statistics
     */
    private function getLabStatistics()
    {
        return [
            'blood_chemistry_tests' => MedicalTestCategory::where('name', 'Blood Chemistry')
                ->withCount('medicalTests')
                ->first()?->medical_tests_count ?? 0,
            'pending_blood_samples' => $this->getPendingLabRequests(),
            'completed_this_week' => MedicalChecklist::whereBetween('updated_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->whereNotNull('blood_extraction_done_by')->count(),
            'average_turnaround_time' => $this->getAverageProcessingTime(),
        ];
    }

    /**
     * Get pending tasks
     */
    private function getPendingTasks()
    {
        return [
            'pending_annual_physical' => Patient::where('status', 'approved')
                ->whereDoesntHave('annualPhysicalExamination', function ($q) {
                    $q->whereIn('status', ['sent_to_company']);
                })->count(),
            'pending_pre_employment' => PreEmploymentRecord::where('status', 'approved')
                ->whereDoesntHave('preEmploymentExamination', function ($q) {
                    $q->whereIn('status', ['Approved', 'sent_to_company']);
                })->count(),
            'pending_lab_requests' => $this->getPendingLabRequests(),
            'results_to_review' => $this->getResultsToReview(),
        ];
    }

    /**
     * Get blood chemistry tests
     */
    private function getBloodChemistryTests()
    {
        return MedicalTestCategory::where('name', 'Blood Chemistry')
            ->with('medicalTests')
            ->first()?->medicalTests ?? collect();
    }

    /**
     * Get pending lab requests count
     */
    private function getPendingLabRequests()
    {
        return MedicalChecklist::where(function($query) {
            $query->whereHas('patient', function($subQuery) {
                $subQuery->where('status', 'approved');
            })
            ->orWhereHas('preEmploymentRecord', function($subQuery) {
                $subQuery->where('status', 'approved');
            });
        })
        ->where(function($query) {
            $query->whereNull('blood_extraction_done_by')
                  ->orWhere('blood_extraction_done_by', '');
        })
        ->count();
    }

    /**
     * Get blood samples currently in process
     */
    private function getBloodSamplesInProcess()
    {
        return MedicalChecklist::where(function($query) {
            $query->whereHas('patient', function($subQuery) {
                $subQuery->where('status', 'approved');
            })
            ->orWhereHas('preEmploymentRecord', function($subQuery) {
                $subQuery->where('status', 'approved');
            });
        })
        ->whereNotNull('blood_extraction_done_by')
        ->where('blood_extraction_done_by', '!=', '')
        ->where(function($query) {
            $query->whereDoesntHave('annualPhysicalExamination', function($subQuery) {
                $subQuery->whereIn('status', ['sent_to_company']);
            })
            ->orWhereDoesntHave('preEmploymentRecord', function($subQuery) {
                $subQuery->whereHas('preEmploymentExamination', function($examQuery) {
                    $examQuery->whereIn('status', ['Approved', 'sent_to_company']);
                });
            });
        })
        ->count();
    }

    /**
     * Get results that need review
     */
    private function getResultsToReview()
    {
        return MedicalChecklist::where(function($query) {
            $query->whereHas('patient', function($subQuery) {
                $subQuery->where('status', 'approved');
            })
            ->orWhereHas('preEmploymentRecord', function($subQuery) {
                $subQuery->where('status', 'approved');
            });
        })
        ->whereNotNull('blood_extraction_done_by')
        ->where('blood_extraction_done_by', '!=', '')
        ->where(function($query) {
            $query->whereDoesntHave('annualPhysicalExamination')
                  ->orWhereHas('annualPhysicalExamination', function($subQuery) {
                      $subQuery->whereNotIn('status', ['sent_to_company']);
                  });
        })
        ->where(function($query) {
            $query->whereDoesntHave('preEmploymentRecord')
                  ->orWhereDoesntHave('preEmploymentRecord', function($subQuery) {
                      $subQuery->whereHas('preEmploymentExamination', function($examQuery) {
                          $examQuery->whereNotIn('status', ['Approved', 'sent_to_company']);
                      });
                  });
        })
        ->count();
    }

    /**
     * Get completed lab work today
     */
    private function getCompletedToday()
    {
        return MedicalChecklist::whereDate('updated_at', today())
        ->whereNotNull('blood_extraction_done_by')
        ->where('blood_extraction_done_by', '!=', '')
        ->count();
    }

    /**
     * Get monthly completed count
     */
    private function getMonthlyCompleted()
    {
        return MedicalChecklist::whereMonth('updated_at', now()->month)
        ->whereYear('updated_at', now()->year)
        ->whereNotNull('blood_extraction_done_by')
        ->where('blood_extraction_done_by', '!=', '')
        ->count();
    }

    /**
     * Get average processing time in hours
     */
    private function getAverageProcessingTime()
    {
        $completedChecklists = MedicalChecklist::whereNotNull('blood_extraction_done_by')
            ->where('blood_extraction_done_by', '!=', '')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        if ($completedChecklists->isEmpty()) {
            return 0;
        }

        $totalHours = $completedChecklists->sum(function($checklist) {
            return $checklist->created_at->diffInHours($checklist->updated_at);
        });

        return round($totalHours / $completedChecklists->count(), 1);
    }

    /**
     * Show the edit form for annual physical examination
     */
    public function editAnnualPhysical($id)
    {
        // First try to find existing examination
        $examination = AnnualPhysicalExamination::with('patient')->find($id);
        
        // If no examination exists, create one for the patient but don't save it yet
        if (!$examination) {
            $patient = Patient::findOrFail($id);
            $examination = new AnnualPhysicalExamination([
                'patient_id' => $patient->id,
                'user_id' => Auth::id(),
                'name' => $patient->full_name,
                'date' => now()->toDateString(),
                'status' => 'Pending',
                'lab_report' => []
            ]);
            // Set the patient relationship manually since we haven't saved yet
            $examination->patient = $patient;
            $examination->exists = false; // Indicate this is a new record
        }
        
        return view('pathologist.annual-physical-edit', compact('examination'));
    }

    /**
     * Update the annual physical examination
     */
    public function updateAnnualPhysical(Request $request, $id)
    {
        // Try to find existing examination, or get patient for new examination
        $examination = AnnualPhysicalExamination::find($id);
        $isNewExamination = false;
        
        if (!$examination) {
            // This means we're creating a new examination
            $patient = Patient::findOrFail($id);
            $examination = new AnnualPhysicalExamination();
            $examination->patient_id = $patient->id;
            $examination->user_id = Auth::id();
            $examination->name = $patient->full_name;
            $examination->date = now()->toDateString();
            $isNewExamination = true;
        }

        // Log incoming data for debugging
        \Log::info('Annual Physical Update Request', [
            'examination_id' => $id,
            'lab_report_count' => count($request->lab_report ?? []),
            'lab_report_keys' => array_keys($request->lab_report ?? []),
            'status' => $request->status
        ]);

        $request->validate([
            'status' => 'required|string|in:Pending,completed,sent_to_company',
            'lab_report' => 'nullable|array',
            'lab_report.*' => 'nullable|string|max:1000',
            'visual' => 'nullable|string|max:255',
            'ishihara_test' => 'nullable|string|max:255',
            'ecg' => 'nullable|string|max:255',
            'skin_marks' => 'nullable|string|max:500',
            'physical_findings' => 'nullable|string|max:1000',
            'lab_findings' => 'nullable|string|max:1000',
            'findings' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Only update lab_report and status - don't overwrite nurse's data
            $updateData = [
                'status' => $request->status,
                'lab_report' => $request->lab_report ?? [],
            ];
            
            \Log::info('Update Data', ['lab_report' => $updateData['lab_report']]);
            
            // Only update other fields if they are actually provided (not null/empty)
            if ($request->filled('visual')) {
                $updateData['visual'] = $request->visual;
            }
            if ($request->filled('ishihara_test')) {
                $updateData['ishihara_test'] = $request->ishihara_test;
            }
            if ($request->filled('ecg')) {
                $updateData['ecg'] = $request->ecg;
            }
            if ($request->filled('skin_marks')) {
                $updateData['skin_marks'] = $request->skin_marks;
            }
            if ($request->filled('physical_findings')) {
                $updateData['physical_findings'] = $request->physical_findings;
            }
            if ($request->filled('lab_findings')) {
                $updateData['lab_findings'] = $request->lab_findings;
            }
            if ($request->filled('findings')) {
                $updateData['findings'] = $request->findings;
            }

            if ($isNewExamination) {
                // For new examinations, fill and save
                $examination->fill($updateData);
                $examination->save();
            } else {
                // For existing examinations, update
                $examination->update($updateData);
            }
            
            \Log::info('Annual Physical Examination Updated Successfully', [
                'id' => $examination->id,
                'lab_report_saved' => $examination->fresh()->lab_report
            ]);

            // Create notification for admin when pathologist completes lab report
            if (!empty($request->lab_report) && is_array($request->lab_report) && count(array_filter($request->lab_report)) > 0) {
                $pathologist = Auth::user();
                $patientName = $examination->name ?? 'Unknown Patient';
                
                Notification::createForAdmin(
                    'pathologist_report_submitted',
                    'Lab Report Submitted - Annual Physical',
                    "Pathologist {$pathologist->name} has submitted lab report for patient {$patientName} (Annual Physical).",
                    [
                        'examination_id' => $examination->id,
                        'patient_name' => $patientName,
                        'pathologist_name' => $pathologist->name,
                        'examination_type' => 'annual_physical',
                        'status' => $request->status,
                        'lab_report_fields' => array_keys(array_filter($request->lab_report ?? []))
                    ],
                    'medium',
                    $pathologist,
                    $examination
                );
            }

            DB::commit();

            return redirect()->route('pathologist.annual-physical')->with('success', 'Annual physical examination updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update annual physical examination: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the edit form for pre-employment examination
     */
    public function editPreEmployment($id)
    {
        // First try to find existing examination by ID
        $examination = PreEmploymentExamination::with('preEmploymentRecord')->find($id);
        
        // If no examination exists, try to find it by pre_employment_record_id
        if (!$examination) {
            $examination = PreEmploymentExamination::with('preEmploymentRecord')
                ->where('pre_employment_record_id', $id)
                ->first();
        }
        
        // If still no examination exists, create one for the pre-employment record
        if (!$examination) {
            $record = PreEmploymentRecord::findOrFail($id);
            
            // Double-check that no examination exists for this record to prevent duplicates
            $existingExamination = PreEmploymentExamination::where('pre_employment_record_id', $record->id)->first();
            
            if ($existingExamination) {
                // If examination exists, redirect to it
                return redirect()->route('pathologist.pre-employment.edit', $existingExamination->id);
            }
            
            $examination = PreEmploymentExamination::create([
                'pre_employment_record_id' => $record->id,
                'user_id' => Auth::id(),
                'name' => $record->full_name,
                'company_name' => $record->company_name,
                'date' => now()->toDateString(),
                'status' => 'Pending',
                'lab_report' => [
                    'urinalysis' => 'Not available',
                    'cbc' => 'Not available',
                    'xray' => 'Not available',
                    'fecalysis' => 'Not available',
                    'blood_chemistry' => 'Not available',
                    'others' => 'Not available',
                    'hbsag_screening' => 'Not available',
                    'hepa_a_igg_igm' => 'Not available',
                ]
            ]);
            $examination->load('preEmploymentRecord');
        }
        
        return view('pathologist.pre-employment-edit', compact('examination'));
    }

    /**
     * Update the pre-employment examination
     */
    public function updatePreEmployment(Request $request, $id)
    {
        $examination = PreEmploymentExamination::findOrFail($id);

        // Log incoming data for debugging
        \Log::info('Pre-Employment Update Request', [
            'examination_id' => $id,
            'lab_report_count' => count($request->lab_report ?? []),
            'lab_report_keys' => array_keys($request->lab_report ?? []),
            'status' => $request->status
        ]);

        $request->validate([
            'status' => 'required|string|in:Pending,Approved,sent_to_company',
            'lab_report' => 'nullable|array',
            'lab_report.*' => 'nullable|string|max:1000',
            'visual' => 'nullable|string|max:255',
            'ishihara_test' => 'nullable|string|max:255',
            'ecg' => 'nullable|string|max:255',
            'skin_marks' => 'nullable|string|max:500',
            'illness_history' => 'nullable|string|max:1000',
            'accidents_operations' => 'nullable|string|max:1000',
            'past_medical_history' => 'nullable|string|max:1000',
            'family_history' => 'nullable|string|max:1000',
            'physical_findings' => 'nullable|string|max:1000',
            'lab_findings' => 'nullable|string|max:1000',
            'findings' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Only update lab_report and status - don't overwrite nurse's data
            $updateData = [
                'status' => $request->status,
                'lab_report' => $request->lab_report ?? [],
            ];
            
            \Log::info('Update Data', ['lab_report' => $updateData['lab_report']]);
            
            // Only update other fields if they are actually provided (not null/empty)
            if ($request->filled('visual')) {
                $updateData['visual'] = $request->visual;
            }
            if ($request->filled('ishihara_test')) {
                $updateData['ishihara_test'] = $request->ishihara_test;
            }
            if ($request->filled('ecg')) {
                $updateData['ecg'] = $request->ecg;
            }
            if ($request->filled('skin_marks')) {
                $updateData['skin_marks'] = $request->skin_marks;
            }
            if ($request->filled('illness_history')) {
                $updateData['illness_history'] = $request->illness_history;
            }
            if ($request->filled('accidents_operations')) {
                $updateData['accidents_operations'] = $request->accidents_operations;
            }
            if ($request->filled('past_medical_history')) {
                $updateData['past_medical_history'] = $request->past_medical_history;
            }
            if ($request->filled('family_history')) {
                $updateData['family_history'] = $request->family_history;
            }
            if ($request->filled('physical_findings')) {
                $updateData['physical_findings'] = $request->physical_findings;
            }
            if ($request->filled('lab_findings')) {
                $updateData['lab_findings'] = $request->lab_findings;
            }
            if ($request->filled('findings')) {
                $updateData['findings'] = $request->findings;
            }

            $examination->update($updateData);
            
            \Log::info('Pre-Employment Examination Updated Successfully', [
                'id' => $examination->id,
                'lab_report_saved' => $examination->fresh()->lab_report
            ]);

            // Create notification for admin when pathologist completes lab report
            if (!empty($request->lab_report) && is_array($request->lab_report) && count(array_filter($request->lab_report)) > 0) {
                $pathologist = Auth::user();
                $patientName = $examination->name ?? 'Unknown Patient';
                
                Notification::createForAdmin(
                    'pathologist_report_submitted',
                    'Lab Report Submitted - Pre-Employment',
                    "Pathologist {$pathologist->name} has submitted lab report for patient {$patientName} (Pre-Employment).",
                    [
                        'examination_id' => $examination->id,
                        'patient_name' => $patientName,
                        'pathologist_name' => $pathologist->name,
                        'examination_type' => 'pre_employment',
                        'status' => $request->status,
                        'lab_report_fields' => array_keys(array_filter($request->lab_report ?? []))
                    ],
                    'medium',
                    $pathologist,
                    $examination
                );
            }

            DB::commit();

            return redirect()->route('pathologist.pre-employment')->with('success', 'Pre-employment examination updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update pre-employment examination: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show OPD walk-in patients for pathologist with enhanced filtering
     */
    public function opd(Request $request)
    {
        $query = User::with(['opdExamination', 'medicalChecklists'])
            ->where('role', 'opd');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$search}%"]);
            });
        }

        // Gender filtering
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Lab status filtering - match the view tab parameters
        $labStatus = $request->filled('lab_status') ? $request->lab_status : 'needs_attention';
        
        switch ($labStatus) {
            case 'needs_attention':
                // OPD patients that need pathologist attention (no meaningful lab results yet)
                $query->whereDoesntHave('opdExamination', function($q) {
                    $q->whereNotNull('lab_report')
                      ->where('lab_report', '!=', '[]')
                      ->where('lab_report', '!=', '{}')
                      ->where(function($subQuery) {
                          // Check for actual meaningful lab results (not just "Not available" or empty)
                          $subQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"cbc_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"urinalysis_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"stool_exam_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"fbs_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"bun_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"creatinine_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"additional_exams_results\"')) NOT IN ('', 'Not available')");
                      });
                });
                break;
                
            case 'lab_completed':
                // OPD patients with actual meaningful lab results completed
                $query->whereHas('opdExamination', function($q) {
                    $q->whereNotNull('lab_report')
                      ->where('lab_report', '!=', '[]')
                      ->where('lab_report', '!=', '{}')
                      ->where(function($subQuery) {
                          // Check for actual meaningful lab results (not just "Not available" or empty)
                          $subQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"cbc_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"urinalysis_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"stool_exam_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"fbs_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"bun_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"creatinine_result\"')) NOT IN ('', 'Not available')")
                                   ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_report, '$.\"additional_exams_results\"')) NOT IN ('', 'Not available')");
                      });
                });
                break;
        }

        // Filter patients that don't have completed examinations (unless specifically filtering for completed lab status)
        if ($labStatus !== 'lab_completed') {
            $query->whereDoesntHave('opdExamination', function ($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            });
        }

        $opdPatients = $query->latest()->paginate(15);
        
        return view('pathologist.opd', compact('opdPatients'));
    }

    /**
     * Show the edit form for OPD examination
     */
    public function editOpd($id)
    {
        // Find the OPD patient
        $opdPatient = User::where('role', 'opd')->findOrFail($id);
        
        // First try to find existing examination
        $examination = OpdExamination::where('user_id', $id)->first();
        
        // If no examination exists, create one for the OPD patient
        if (!$examination) {
            $examination = OpdExamination::create([
                'user_id' => $opdPatient->id,
                'name' => trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? '')),
                'date' => now()->toDateString(),
                'status' => 'pending',
                'lab_report' => [
                    'urinalysis' => 'Not available',
                    'cbc' => 'Not available',
                    'xray' => 'Not available',
                    'fecalysis' => 'Not available',
                    'blood_chemistry' => 'Not available',
                    'others' => 'Not available',
                    'hbsag_screening' => 'Not available',
                    'hepa_a_igg_igm' => 'Not available',
                ]
            ]);
        }
        
        return view('pathologist.opd-edit', compact('examination', 'opdPatient'));
    }

    /**
     * Update the OPD examination
     */
    public function updateOpd(Request $request, $id)
    {
        $examination = OpdExamination::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,completed,sent_to_company',
            'lab_report' => 'nullable|array',
            'lab_report.*' => 'nullable|string|max:500',
            'lab_results' => 'nullable|array',
            'lab_results.*' => 'nullable|string|max:500',
            'visual' => 'nullable|string|max:255',
            'ishihara_test' => 'nullable|string|max:255',
            'ecg' => 'nullable|string|max:255',
            'skin_marks' => 'nullable|string|max:500',
            'illness_history' => 'nullable|string|max:1000',
            'accidents_operations' => 'nullable|string|max:1000',
            'past_medical_history' => 'nullable|string|max:1000',
            'family_history' => 'nullable|string|max:1000',
            'physical_findings' => 'nullable|string|max:1000',
            'lab_findings' => 'nullable|string|max:1000',
            'findings' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Only update lab_report, lab_results and status - don't overwrite nurse's data
            $updateData = [
                'status' => $request->status,
                'lab_report' => $request->lab_report ?? [],
                'lab_results' => $request->lab_results ?? [],
            ];
            
            // Only update other fields if they are actually provided (not null/empty)
            if ($request->filled('visual')) {
                $updateData['visual'] = $request->visual;
            }
            if ($request->filled('ishihara_test')) {
                $updateData['ishihara_test'] = $request->ishihara_test;
            }
            if ($request->filled('ecg')) {
                $updateData['ecg'] = $request->ecg;
            }
            if ($request->filled('skin_marks')) {
                $updateData['skin_marks'] = $request->skin_marks;
            }
            if ($request->filled('illness_history')) {
                $updateData['illness_history'] = $request->illness_history;
            }
            if ($request->filled('accidents_operations')) {
                $updateData['accidents_operations'] = $request->accidents_operations;
            }
            if ($request->filled('past_medical_history')) {
                $updateData['past_medical_history'] = $request->past_medical_history;
            }
            if ($request->filled('family_history')) {
                $updateData['family_history'] = $request->family_history;
            }
            if ($request->filled('physical_findings')) {
                $updateData['physical_findings'] = $request->physical_findings;
            }
            if ($request->filled('lab_findings')) {
                $updateData['lab_findings'] = $request->lab_findings;
            }
            if ($request->filled('findings')) {
                $updateData['findings'] = $request->findings;
            }

            $examination->update($updateData);

            // Create notification for admin when pathologist completes lab report
            if (!empty($request->lab_report) && is_array($request->lab_report) && count(array_filter($request->lab_report)) > 0) {
                $pathologist = Auth::user();
                $patientName = $examination->name ?? 'Unknown Patient';
                
                Notification::createForAdmin(
                    'pathologist_report_submitted',
                    'Lab Report Submitted - OPD',
                    "Pathologist {$pathologist->name} has submitted lab report for patient {$patientName} (OPD).",
                    [
                        'examination_id' => $examination->id,
                        'patient_name' => $patientName,
                        'pathologist_name' => $pathologist->name,
                        'examination_type' => 'opd',
                        'status' => $request->status,
                        'lab_report_fields' => array_keys(array_filter($request->lab_report ?? []))
                    ],
                    'medium',
                    $pathologist,
                    $examination
                );
            }

            DB::commit();

            return redirect()->route('pathologist.opd')->with('success', 'OPD examination updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update OPD examination: ' . $e->getMessage())->withInput();
        }
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
        
        // Mark as completed from pathologist to send up to doctor
        $exam->update(['status' => 'completed']);
        
        return redirect()->route('pathologist.opd')->with('success', 'OPD examination sent to doctor.');
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
        
        return view('pathologist.medical-checklist', compact('medicalChecklist', 'opdPatient', 'opdExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }
}