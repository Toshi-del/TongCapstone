<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PreEmploymentExamination;

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
        $patients = Patient::with('appointment')->where('status', 'pending')->latest()->take(10)->get();
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
    public function preEmployment()
    {
        // Show only records that have been explicitly submitted to the doctor
        // by staff (nurse/plebo/radtech/pathologist) via PreEmploymentExamination status 'Approved'.
        $preEmployments = \App\Models\PreEmploymentRecord::where('status', 'approved')
            ->whereHas('preEmploymentExamination', function ($q) {
                $q->where('status', 'Approved');
            })
            ->latest()
            ->get();
        
        return view('doctor.pre-employment', compact('preEmployments'));
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

        // Mark as Approved (doctor submission)
        $examination->update(['status' => 'Approved']);

        return redirect()->route('doctor.pre-employment')->with('success', 'Pre-employment examination submitted to admin.');
    }

    /**
     * Show annual physical examination patients
     */
    public function annualPhysical()
    {
        // Show patients that have examinations ready for doctor (status 'completed' by pathologist)
        // Exclude those already sent by the doctor (status 'sent_to_company')
        $patients = Patient::with(['appointment', 'annualPhysicalExamination'])
            ->where('status', 'approved')
            ->whereHas('annualPhysicalExamination', function ($q) {
                $q->where('status', 'completed');
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
        $currentUser = Auth::user();
        
        $users = User::whereIn('role', ['nurse', 'admin'])
            ->where('id', '!=', Auth::id())
            ->select('id', 'fname', 'lname', 'role', 'company')
            ->get();

        // Add unread message count for each user
        $usersWithUnread = $users->map(function($user) {
            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', Auth::id())
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
     * Fetch messages for the current user
     */
    public function fetchMessages()
    {
        $messages = Message::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
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
     * Show the form for editing a pre-employment examination.
     */
    public function editPreEmployment($id)
    {
        $preEmployment = PreEmploymentExamination::findOrFail($id);
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
        ]);
        $preEmployment->update($data);
        return redirect()->route('doctor.pre-employment.edit', $preEmployment->id)->with('success', 'Pre-Employment Examination updated successfully.');
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
     * Show the form for editing an annual physical examination.
     */
    public function editAnnualPhysical($id)
    {
        $annualPhysical = \App\Models\AnnualPhysicalExamination::findOrFail($id);
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

        // Mark as sent to company/admin so it no longer appears in the doctor list
        $examination->update(['status' => 'sent_to_company']);

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
}
