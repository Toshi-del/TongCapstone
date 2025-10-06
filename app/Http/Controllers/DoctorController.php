<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PreEmploymentExamination;
use App\Models\MedicalTestCategory;
use App\Models\MedicalTest;
use App\Models\MedicalTestReferenceRange;

class DoctorController extends Controller
{
    /**
     * Show the doctor dashboard
     */
    public function dashboard()
    {
        // Get pre-employment records
        $preEmployments = PreEmploymentRecord::where('status', 'approved')->latest()->take(5)->get();
        $preEmploymentCount = PreEmploymentRecord::where('status', 'approved')->count();

        // Get appointments with patients
        $appointments = Appointment::with('patients')->latest()->take(10)->get();
        $appointmentCount = Appointment::count();

        // Get all patients
        $patients = Patient::with(['appointment', 'medicalTests'])->where('status', 'pending')->latest()->take(10)->get();
        $patientCount = Patient::where('status', 'pending')->count();

        // Legacy column 'appointment_type' may be absent; compute count without it
        $annualPhysicals = Appointment::count();

        return view('doctor.dashboard', compact(
            'preEmployments',
            'preEmploymentCount',
            'appointments',
            'appointmentCount',
            'patients',
            'patientCount',
            'annualPhysicals'
        ));
    }

    /**
     * Show pre-employment records
     */
    public function preEmployment(Request $request)
    {
        $filter = $request->get('filter');
        
        // Base query for pre-employment examinations
        $query = \App\Models\PreEmploymentExamination::with(['preEmploymentRecord.medicalTest', 'preEmploymentRecord.medicalTestCategory', 'user']);
        
        // Apply filtering based on the tab selected
        switch ($filter) {
            case 'needs_attention':
                // Show examinations that need doctor's attention (pending status)
                $query->whereIn('status', ['pending', 'collection_completed', 'Pending']);
                break;
                
            case 'submitted':
                // Show examinations that have been submitted to admin
                $query->whereIn('status', ['sent_to_company', 'Approved', 'sent_to_both']);
                break;
                
            default:
                // Show all examinations that are ready for doctor review
                $query->whereIn('status', ['pending', 'completed', 'Approved', 'collection_completed', 'sent_to_company', 'Pending', 'sent_to_both']);
                break;
        }
        
        $preEmploymentExaminations = $query->latest()->paginate(15);
        
        // Get all examinations for tab counts (without pagination) - don't filter by status for accurate counts
        $allExaminations = \App\Models\PreEmploymentExamination::with(['preEmploymentRecord.medicalTest', 'preEmploymentRecord.medicalTestCategory', 'user'])
            ->get();
            
        // Log the count and statuses for debugging
        \Log::info('Pre-employment examinations count: ' . $preEmploymentExaminations->count());
        \Log::info('Filter applied: ' . ($filter ?? 'none'));
        \Log::info('Pre-employment examinations statuses: ' . 
            \App\Models\PreEmploymentExamination::select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toJson(JSON_PRETTY_PRINT)
        );
            
        return view('doctor.pre-employment', compact('preEmploymentExaminations', 'allExaminations'));
    }

    /**
     * Submit a pre-employment examination for a record to Admin.
     */
    public function submitPreEmploymentByRecordId($recordId)
    {
        $record = \App\Models\PreEmploymentRecord::findOrFail($recordId);
        // Ensure examination exists
        $examination = \App\Models\PreEmploymentExamination::firstOrCreate(
            ['pre_employment_record_id' => $recordId],
            [
                'user_id' => $record->created_by,
                'name' => $record->full_name,
                'company_name' => $record->company_name,
                'date' => now()->toDateString(),
                'status' => $record->status,
            ]
        );

        // Mark as sent to company (doctor submission to admin)
        $examination->update(['status' => 'sent_to_company']);

        return redirect()->route('doctor.pre-employment')->with('success', 'Pre-employment examination submitted to admin.');
    }

    /**
     * Show annual physical examination patients
     */
    public function annualPhysical()
    {
        // Show patients that have examinations ready for doctor (status 'completed' by pathologist)
        // Exclude those already sent by the doctor (status 'sent_to_admin' or 'sent_to_company')
        $patients = Patient::with(['appointment', 'medicalTests', 'annualPhysicalExamination'])
            ->where('status', 'approved')
            ->whereHas('annualPhysicalExamination', function ($q) {
                $q->whereIn('status', ['completed', 'collection_completed']);
            })
            ->latest()
            ->get();

        // Compute whether each patient can be sent to admin (must have checklist and results filled)
        $canSendByPatientId = [];
        foreach ($patients as $patient) {
            $exam = $patient->annualPhysicalExamination;
            $hasPhysicalFindings = !empty($exam?->physical_findings);
            $hasLabResults = !empty($exam?->lab_findings) || !empty($exam?->lab_report);
            $hasChecklist = \App\Models\MedicalChecklist::where('patient_id', $patient->id)
                ->where('examination_type', 'annual_physical')
                ->exists();
            $canSendByPatientId[$patient->id] = $hasPhysicalFindings && $hasLabResults && $hasChecklist;
        }
        
        return view('doctor.annual-physical', compact('patients', 'canSendByPatientId'));
    }

    /**
     * Show messages view
     */
    public function messages()
    {
        return view('doctor.messages');
    }

    /**
     * Get users that doctors can chat with (nurses and admins only)
     */
    public function chatUsers()
    {
        $currentUserId = Auth::id();
        
        $users = User::select('id', 'fname', 'lname', 'role', 'company')
            ->whereIn('role', ['nurse', 'admin'])
            ->where('id', '!=', $currentUserId)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
        
        // Add last message information for each user
        $users = $users->map(function($user) use ($currentUserId) {
            // Get the last message between doctor and this user
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

        return response()->json([
            'current_user' => Auth::user()->only(['id', 'fname', 'lname', 'role']),
            'filtered_users' => $users
        ]);
    }

    /**
     * Fetch messages for the current user
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

        // Get messages between doctor and the specific user
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
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        // Ensure the receiver is a nurse or admin
        $receiver = User::find($request->receiver_id);
        if (!in_array($receiver->role, ['nurse', 'admin'])) {
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
     * Get unread message count for the current doctor user.
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
     * Show the form for editing a pre-employment examination.
     */
    public function editPreEmployment($id)
    {
        $preEmployment = PreEmploymentExamination::with([
            'preEmploymentRecord.medicalTest',
            'drugTestResults'
        ])->findOrFail($id);
        return view('doctor.pre-employment-edit', compact('preEmployment'));
    }

    /**
     * Update the specified pre-employment examination in storage.
     */
    public function updatePreEmployment(Request $request, $id)
    {
        $preEmployment = PreEmploymentExamination::findOrFail($id);
        $data = $request->validate([
            'name' => 'nullable|string',
            'illness_history' => 'nullable|string',
            'accidents_operations' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'family_history' => 'nullable|array',
            'personal_habits' => 'nullable|array',
            'physical_exam' => 'nullable|array',
            'skin_marks' => 'nullable|string',
            'visual' => 'nullable|string',
            'ishihara_test' => 'nullable|string',
            'findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
            'drug_test' => 'nullable|array',
            'fitness_assessment' => 'nullable|string',
            'drug_positive_count' => 'nullable|integer',
            'medical_abnormal_count' => 'nullable|integer',
            'physical_abnormal_count' => 'nullable|integer',
            'assessment_details' => 'nullable|string',
        ]);
        $preEmployment->update($data);
        
        // Calculate and store fitness assessment automatically
        $preEmployment->calculateFitnessAssessment();
        
        // Create notification for admin when doctor completes examination
        $doctor = Auth::user();
        $patientName = $preEmployment->name ?? 'Unknown Patient';
        
        Notification::createForAdmin(
            'examination_updated',
            'Medical Examination Updated by Doctor - Pre-Employment',
            "Doctor {$doctor->name} has updated medical examination for {$patientName} (Pre-Employment).",
            [
                'examination_id' => $preEmployment->id,
                'patient_name' => $patientName,
                'doctor_name' => $doctor->name,
                'examination_type' => 'pre_employment',
                'has_findings' => !empty($data['findings']),
                'has_lab_report' => !empty($data['lab_report'])
            ],
            'medium',
            $doctor,
            $preEmployment
        );
        
        return redirect()->route('doctor.pre-employment.edit', $preEmployment->id)->with('success', 'Pre-Employment Examination updated successfully.');
    }

    /**
     * Show a pre-employment examination
     */
    public function showExamination($id)
    {
        $examination = PreEmploymentExamination::with([
            'preEmploymentRecord.medicalTest',
            'preEmploymentRecord.preEmploymentMedicalTests.medicalTest',
            'preEmploymentRecord.preEmploymentMedicalTests.medicalTestCategory',
            'preEmploymentRecord.drugTest',
            'drugTestResults'
        ])->findOrFail($id);
        
        // Debug: Log what examination data we're passing to the view
        \Log::info('Doctor Controller - Pre-Employment Examination Debug:', [
            'examination_id' => $examination->id,
            'patient_name' => $examination->name,
            'lab_findings_exists' => !is_null($examination->lab_findings),
            'lab_findings_type' => gettype($examination->lab_findings),
            'lab_findings_keys' => is_array($examination->lab_findings) ? array_keys($examination->lab_findings) : 'not_array',
            'chest_xray_data' => is_array($examination->lab_findings) && isset($examination->lab_findings['chest_xray']) ? $examination->lab_findings['chest_xray'] : 'not_found',
            'raw_lab_findings' => $examination->lab_findings
        ]);
        
        // Check if this examination requires a drug test
        $requiresDrugTest = false;
        if ($examination->preEmploymentRecord && $examination->preEmploymentRecord->medicalTest) {
            $medicalTestName = strtolower($examination->preEmploymentRecord->medicalTest->name);
            $requiresDrugTest = in_array($medicalTestName, [
                'pre-employment with drug test',
                'pre-employment with ecg and drug test',
                'pre-employment with drug test and audio and ishihara',
                'drug test only (bring valid i.d)'
            ]) || str_contains($medicalTestName, 'drug test');
        }
        
        return view('doctor.pre-employment-examination', compact('examination', 'requiresDrugTest'));
    }

    /**
     * Find or create an examination by pre_employment_record_id and redirect to edit form
     */
    public function editExaminationByRecordId($recordId)
    {
        // Ensure linked examination exists and is populated from the source record
        $record = \App\Models\PreEmploymentRecord::findOrFail($recordId);

        $examination = \App\Models\PreEmploymentExamination::firstOrCreate(
            ['pre_employment_record_id' => $recordId],
            [
                'pre_employment_record_id' => $recordId,
                'user_id' => $record->created_by,
                'name' => $record->first_name . ' ' . $record->last_name,
                'company_name' => $record->company_name,
                'date' => now()->toDateString(),
                'status' => $record->status,
            ]
        );
        return redirect()->route('doctor.pre-employment.edit', $examination->id);
    }

    /**
     * Show an annual physical examination
     */
    public function showAnnualPhysicalExamination($id)
    {
        $examination = \App\Models\AnnualPhysicalExamination::with([
            'patient.appointment.medicalTest',
            'drugTestResults'
        ])->findOrFail($id);
        
        return view('doctor.annual-physical-examination', compact('examination'));
    }

    /**
     * Show the form for editing an annual physical examination.
     */
    public function editAnnualPhysical($id)
    {
        $annualPhysical = \App\Models\AnnualPhysicalExamination::with([
            'patient.appointment.medicalTest',
            'drugTestResults'
        ])->findOrFail($id);
        return view('doctor.annual-physical-edit', compact('annualPhysical'));
    }

    /**
     * Update the specified annual physical examination in storage.
     */
    public function updateAnnualPhysical(Request $request, $id)
    {
        try {
            $annualPhysical = \App\Models\AnnualPhysicalExamination::findOrFail($id);
            
            $data = $request->validate([
                'illness_history' => 'nullable|string',
                'accidents_operations' => 'nullable|string',
                'past_medical_history' => 'nullable|string',
                'family_history' => 'nullable|array',
                'personal_habits' => 'nullable|array',
                'physical_exam' => 'nullable|array',
                'skin_marks' => 'nullable|string',
                'visual' => 'nullable|string',
                'ishihara_test' => 'nullable|string',
                'findings' => 'nullable|string',
                'lab_report' => 'nullable|array',
                'physical_findings' => 'nullable|array',
                'lab_findings' => 'nullable|array',
                'ecg' => 'nullable|string',
                'drug_test' => 'nullable|array',
                'fitness_assessment' => 'nullable|string',
                'drug_positive_count' => 'nullable|integer',
                'medical_abnormal_count' => 'nullable|integer',
                'physical_abnormal_count' => 'nullable|integer',
                'assessment_details' => 'nullable|string',
            ]);
            
            // Recompose lab_findings from flat lab_report inputs so both result and findings persist
            // Expected keys in lab_report: e.g. xray, xray_findings, drug_test, drug_test_findings, hepa_a_igg_igm, hepa_a_igg_igm_findings, ...
            if (isset($data['lab_report']) && is_array($data['lab_report'])) {
                $labReport = $data['lab_report'];
                $composedLabFindings = $annualPhysical->lab_findings ?? [];

                foreach ($labReport as $key => $value) {
                    // If this key ends with _findings, pair it with its base key
                    if (str_ends_with($key, '_findings')) {
                        $baseKey = substr($key, 0, -9); // remove suffix '_findings'
                        $composedLabFindings[$baseKey]['findings'] = $value;
                    } else {
                        // Treat this as the primary result for the test
                        $composedLabFindings[$key]['result'] = $value;
                    }
                }

                // Store back structured findings and keep raw lab_report for display if needed
                $data['lab_findings'] = $composedLabFindings;
            }

            // Ensure physical_findings keeps existing entries when only some rows are updated
            if (isset($data['physical_findings']) && is_array($data['physical_findings'])) {
                $mergedPhysical = $annualPhysical->physical_findings ?? [];
                foreach ($data['physical_findings'] as $area => $values) {
                    // Normalize values
                    $values = is_array($values) ? array_map(function($v){ return is_string($v) ? trim($v) : $v; }, $values) : [];
                    // If findings provided but result missing, set a sensible default result
                    if ((!isset($values['result']) || $values['result'] === '') && (!empty($values['findings']))) {
                        $values['result'] = 'Abnormal';
                    }
                    $mergedPhysical[$area] = array_merge($mergedPhysical[$area] ?? [], $values ?? []);
                }
                $data['physical_findings'] = $mergedPhysical;
            }
            
            $result = $annualPhysical->update($data);
            
            // Calculate and store fitness assessment automatically
            $annualPhysical->calculateFitnessAssessment();
            
            if ($result) {
                return redirect()->route('doctor.annual-physical.edit', $annualPhysical->id)->with('success', 'Annual Physical Examination updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to update the examination. Please try again.')->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Error updating annual physical examination: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the examination. Please try again.')->withInput();
        }
    }

    /**
     * Find or create an annual physical examination by patient_id and redirect to edit form
     */
    public function editAnnualPhysicalByPatientId($patientId)
    {
        $patient = \App\Models\Patient::findOrFail($patientId);

        $examination = \App\Models\AnnualPhysicalExamination::firstOrCreate(
            ['patient_id' => $patientId],
            [
                'patient_id' => $patientId,
                'user_id' => Auth::id(),
                'name' => $patient->full_name,
                'date' => now()->toDateString(),
                'status' => 'Pending',
            ]
        );
        return redirect()->route('doctor.annual-physical.edit', $examination->id);
    }

    /**
     * Submit an annual physical examination for a patient to Admin.
     * Marks the examination as completed and the patient as approved so it no longer shows in the pending list.
     */
    public function submitAnnualPhysicalByPatientId($patientId)
    {
        $patient = \App\Models\Patient::findOrFail($patientId);
        // Ensure an examination exists
        $examination = \App\Models\AnnualPhysicalExamination::firstOrCreate(
            ['patient_id' => $patientId],
            [
                'patient_id' => $patientId,
                'user_id' => Auth::id(),
                'name' => $patient->full_name,
                'date' => now()->toDateString(),
            ]
        );

        // Guard: Doctor can submit only if checklist and results are present
        $hasChecklist = \App\Models\MedicalChecklist::where('patient_id', $patientId)
            ->where('examination_type', 'annual_physical')
            ->exists();
        $hasPhysicalFindings = !empty($examination->physical_findings);
        $hasLabResults = !empty($examination->lab_findings) || !empty($examination->lab_report);

        if (!($hasChecklist && $hasPhysicalFindings && $hasLabResults)) {
            return redirect()->route('doctor.annual-physical')
                ->with('error', 'Please complete the medical checklist and enter both physical and laboratory results before sending to admin.');
        }

        // Mark as sent to admin so it no longer appears in the doctor list
        $examination->update(['status' => 'sent_to_admin']);

        return redirect()->route('doctor.annual-physical')->with('success', 'Annual physical submitted to admin.');
    }

    /**
     * Show medical checklist form for pre-employment
     */
    public function showMedicalChecklistPreEmployment($recordId)
    {
        $preEmploymentRecord = \App\Models\PreEmploymentRecord::findOrFail($recordId);
        
        // Find existing medical checklist or create empty one
        $medicalChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $recordId)->first() ?? new \App\Models\MedicalChecklist();
        
        return view('doctor.medical-checklist', [
            'examinationType' => 'pre_employment',
            'preEmploymentRecord' => $preEmploymentRecord,
            'medicalChecklist' => $medicalChecklist,
            'name' => $preEmploymentRecord->first_name . ' ' . $preEmploymentRecord->last_name,
            'age' => $preEmploymentRecord->age,
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Show medical checklist form for annual physical
     */
    public function showMedicalChecklistAnnualPhysical($patientId)
    {
        $patient = \App\Models\Patient::findOrFail($patientId);
        
        // Find or create annual physical examination record
        $annualPhysicalExamination = \App\Models\AnnualPhysicalExamination::firstOrCreate(
            ['patient_id' => $patientId],
            ['patient_id' => $patientId]
        );
        
        // Find existing medical checklist or create empty one
        $medicalChecklist = \App\Models\MedicalChecklist::where('annual_physical_examination_id', $annualPhysicalExamination->id)->first() ?? new \App\Models\MedicalChecklist();
        
        return view('doctor.medical-checklist', [
            'examinationType' => 'annual_physical',
            'patient' => $patient,
            'annualPhysicalExamination' => $annualPhysicalExamination,
            'medicalChecklist' => $medicalChecklist,
            'name' => $patient->first_name . ' ' . $patient->last_name,
            'age' => $patient->age,
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Store medical checklist
     */
    public function storeMedicalChecklist(Request $request)
    {
        $data = $request->validate([
            'examination_type' => 'required|in:pre_employment,annual_physical',
            'pre_employment_record_id' => 'nullable|exists:pre_employment_records,id',
            'patient_id' => 'nullable|exists:patients,id',
            'annual_physical_examination_id' => 'nullable|exists:annual_physical_examinations,id',
            'name' => 'required|string',
            'age' => 'required|integer',
            'number' => 'nullable|string',
            'date' => 'required|date',
            // Individual examination fields - only done_by fields
            'chest_xray_done_by' => 'nullable|string',
            'stool_exam_done_by' => 'nullable|string',
            'urinalysis_done_by' => 'nullable|string',
            'drug_test_done_by' => 'nullable|string',
            'blood_extraction_done_by' => 'nullable|string',
            'ecg_done_by' => 'nullable|string',
            'physical_exam_done_by' => 'nullable|string',
            'optional_exam' => 'nullable|string',
            'doctor_signature' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();

        // Find existing medical checklist or create new one
        $medicalChecklist = null;
        
        if ($data['examination_type'] === 'pre_employment' && $data['pre_employment_record_id']) {
            $medicalChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $data['pre_employment_record_id'])->first();
        } elseif ($data['examination_type'] === 'annual_physical' && $data['annual_physical_examination_id']) {
            $medicalChecklist = \App\Models\MedicalChecklist::where('annual_physical_examination_id', $data['annual_physical_examination_id'])->first();
        }

        if ($medicalChecklist) {
            // Update existing record
            $medicalChecklist->update($data);
            $message = 'Medical checklist updated successfully.';
        } else {
            // Create new record
            \App\Models\MedicalChecklist::create($data);
            $message = 'Medical checklist created successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Update medical checklist
     */
    public function updateMedicalChecklist(Request $request, $id)
    {
        $medicalChecklist = \App\Models\MedicalChecklist::findOrFail($id);
        
        $data = $request->validate([
            'examination_type' => 'required|in:pre_employment,annual_physical',
            'pre_employment_record_id' => 'nullable|exists:pre_employment_records,id',
            'patient_id' => 'nullable|exists:patients,id',
            'annual_physical_examination_id' => 'nullable|exists:annual_physical_examinations,id',
            'name' => 'required|string',
            'age' => 'required|integer',
            'number' => 'nullable|string',
            'date' => 'required|date',
            // Individual examination fields - only done_by fields
            'chest_xray_done_by' => 'nullable|string',
            'stool_exam_done_by' => 'nullable|string',
            'urinalysis_done_by' => 'nullable|string',
            'drug_test_done_by' => 'nullable|string',
            'blood_extraction_done_by' => 'nullable|string',
            'ecg_done_by' => 'nullable|string',
            'physical_exam_done_by' => 'nullable|string',
            'optional_exam' => 'nullable|string',
            'doctor_signature' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $medicalChecklist->update($data);

        return redirect()->back()->with('success', 'Medical checklist updated successfully.');
    }

    /**
     * Display medical test categories
     */
    public function medicalTestCategories(Request $request)
    {
        $search = $request->get('search');
        
        $categories = MedicalTestCategory::withCount('medicalTests')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
            })
            ->paginate(12)
            ->appends($request->query());
        
        return view('doctor.medical-test-categories.index', compact('categories', 'search'));
    }

    /**
     * Show specific medical test category
     */
    public function showMedicalTestCategory($id)
    {
        $category = MedicalTestCategory::with('medicalTests')->findOrFail($id);
        
        return view('doctor.medical-test-categories.show', compact('category'));
    }

    /**
     * Display medical tests
     */
    public function medicalTests(Request $request)
    {
        $search = $request->get('search');
        $categoryFilter = $request->get('category');
        
        $tests = MedicalTest::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%")
                           ->orWhereHas('category', function ($q) use ($search) {
                               $q->where('name', 'like', "%{$search}%");
                           });
            })
            ->when($categoryFilter, function ($query, $categoryFilter) {
                return $query->where('medical_test_category_id', $categoryFilter);
            })
            ->paginate(15)
            ->appends($request->query());
            
        $categories = MedicalTestCategory::all();
        
        return view('doctor.medical-tests.index', compact('tests', 'categories', 'search', 'categoryFilter'));
    }

    /**
     * Show edit form for medical test
     */
    public function editMedicalTest($id)
    {
        $test = MedicalTest::with(['category', 'referenceRanges'])->findOrFail($id);
        $categories = MedicalTestCategory::all();
        
        return view('doctor.medical-tests.edit', compact('test', 'categories'));
    }

    /**
     * Update medical test (doctors can only edit reference ranges)
     */
    public function updateMedicalTest(Request $request, $id)
    {
        $test = MedicalTest::findOrFail($id);
        
        $request->validate([
            'reference_ranges.*.reference_name' => 'nullable|string|max:255',
            'reference_ranges.*.reference_range' => 'nullable|string|max:255',
        ]);

        // Doctors can only update reference ranges, not basic test information
        // Handle reference ranges
        if ($request->has('reference_ranges')) {
            // Delete existing reference ranges
            $test->referenceRanges()->delete();
            
            // Add new reference ranges
            $referenceRanges = collect($request->reference_ranges)
                ->filter(function ($range) {
                    return !empty($range['reference_name']) && !empty($range['reference_range']);
                })
                ->map(function ($range, $index) {
                    return [
                        'reference_name' => $range['reference_name'],
                        'reference_range' => $range['reference_range'],
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            if ($referenceRanges->isNotEmpty()) {
                $test->referenceRanges()->createMany($referenceRanges->toArray());
            }
        }

        return redirect()->route('medical-tests.index')->with('success', 'Reference ranges updated successfully.');
    }
}
