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
     * Show annual physical patients
     */
    public function annualPhysical()
    {
        // Show all approved patients with their examination status
        // Nurses can see all patients regardless of examination completion
        $patients = Patient::with(['annualPhysicalExamination'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);
        
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
            'findings' => 'nullable|string',
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
            'findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'drug_test' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
        ]);

        $annualPhysical->update($validated);

        return redirect()->route('nurse.annual-physical')->with('success', 'Annual physical examination updated successfully.');
    }

    /**
     * Show medical checklist for pre-employment
     */
    public function showMedicalChecklistPreEmployment($recordId)
    {
        $preEmploymentRecord = PreEmploymentRecord::findOrFail($recordId);
        $medicalChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $recordId)->first();
        $examinationType = 'pre-employment';
        $number = 'EMP-' . str_pad($preEmploymentRecord->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($preEmploymentRecord->first_name ?? '') . ' ' . ($preEmploymentRecord->last_name ?? ''));
        $age = $preEmploymentRecord->age ?? null;
        $date = now()->format('Y-m-d');
        
        return view('nurse.medical-checklist', compact('medicalChecklist', 'preEmploymentRecord', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist for annual physical
     */
    public function showMedicalChecklistAnnualPhysical($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $annualPhysicalExamination = \App\Models\AnnualPhysicalExamination::where('patient_id', $patientId)->first();
        $medicalChecklist = \App\Models\MedicalChecklist::where('annual_physical_examination_id', $annualPhysicalExamination->id ?? 0)->first();
        $examinationType = 'annual-physical';
        
        return view('nurse.medical-checklist', compact('medicalChecklist', 'patient', 'annualPhysicalExamination', 'examinationType'));
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
            'physical_exam_done_by' => 'nullable|string',
            'optional_exam' => 'nullable|string',
            'nurse_signature' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $checklist = \App\Models\MedicalChecklist::create($validated);

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

        // Redirect out of the checklist page back to the appropriate list
        $redirectRoute = match($validated['examination_type']) {
            'pre-employment' => 'nurse.pre-employment',
            'annual-physical' => 'nurse.annual-physical',
            'opd' => 'nurse.opd',
            default => 'nurse.dashboard'
        };

        return redirect()->route($redirectRoute)->with('success', 'Medical checklist created successfully.');
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

        // Redirect out of the checklist page back to the appropriate list
        $redirectRoute = match($request->input('examination_type')) {
            'pre-employment' => 'nurse.pre-employment',
            'annual-physical' => 'nurse.annual-physical',
            'opd' => 'nurse.opd',
            default => 'nurse.dashboard'
        };

        return redirect()->route($redirectRoute)->with('success', 'Medical checklist updated successfully.');
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
        
        // Check if medical checklist exists and is completed
        $medicalChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $recordId)
            ->where('examination_type', 'pre-employment')
            ->first();
        
        if (!$medicalChecklist || empty($medicalChecklist->physical_exam_done_by)) {
            return redirect()->route('nurse.medical-checklist.pre-employment', $recordId)
                ->with('error', 'Please complete the medical checklist before creating the examination form.');
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
            'findings' => 'nullable|string',
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
        $patient = Patient::with(['appointment.medicalTest'])->findOrFail($patientId);
        
        // Check if medical checklist exists and is completed
        $medicalChecklist = \App\Models\MedicalChecklist::where('patient_id', $patientId)
            ->where('examination_type', 'annual-physical')
            ->first();
        
        if (!$medicalChecklist || empty($medicalChecklist->physical_exam_done_by)) {
            return redirect()->route('nurse.medical-checklist.annual-physical', $patientId)
                ->with('error', 'Please complete the medical checklist before creating the examination form.');
        }
        
        return view('nurse.annual-physical-create', compact('patient'));
    }

    /**
     * Store new annual physical examination
     */
    public function storeAnnualPhysical(Request $request)
    {
        // Get patient with medical test information to determine validation rules
        $patient = Patient::with(['appointment.medicalTest'])->findOrFail($request->patient_id);
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
            'findings' => 'nullable|string',
            'lab_report' => 'nullable|array',
            'physical_findings' => 'nullable|array',
            'lab_findings' => 'nullable|array',
            'ecg' => 'nullable|string',
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
        
        $examination = \App\Models\AnnualPhysicalExamination::create($validated);
        
        // Log created examination data
        \Log::info('Annual Physical Examination created:', [
            'id' => $examination->id,
            'visual' => $examination->visual,
            'visual_from_db' => $examination->fresh()->visual
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
     * Show OPD walk-in patients
     */
    public function opd()
    {
        // Show all OPD patients with their examination status
        // Nurses can see all OPD patients regardless of examination completion
        $opdPatients = User::with(['opdExamination'])
            ->where('role', 'opd')
            ->latest()
            ->get();
        
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
            'findings' => 'nullable|string',
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
            'findings' => 'nullable|string',
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
        $medicalChecklist = \App\Models\MedicalChecklist::where('opd_examination_id', $opdExamination->id ?? 0)->first();
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
}
