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
    public function annualPhysicalXray(Request $request)
    {
        $query = Patient::where('status', 'approved');

        // Handle tab filtering
        $xrayStatus = $request->get('xray_status', 'needs_attention');
        
        if ($xrayStatus === 'needs_attention') {
            // Records that need X-ray imaging (no chest_xray_done_by)
            $query->whereDoesntHave('medicalChecklists', function ($q) {
                $q->where('examination_type', 'annual_physical')
                  ->whereNotNull('chest_xray_done_by')
                  ->where('chest_xray_done_by', '!=', '');
            });
        } elseif ($xrayStatus === 'xray_completed') {
            // Records where X-ray imaging is completed
            $query->whereHas('medicalChecklists', function ($q) {
                $q->where('examination_type', 'annual_physical')
                  ->whereNotNull('chest_xray_done_by')
                  ->where('chest_xray_done_by', '!=', '');
            });
        }

        // Apply additional filters
        if ($request->filled('gender')) {
            $query->where('sex', $request->get('gender'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->get();

        return view('radtech.annual-physical-xray', compact('patients'));
    }

    /**
     * Show OPD X-ray records
     */
    public function opdXray(Request $request)
    {
        $query = \App\Models\User::where('role', 'opd');

        // Handle tab filtering
        $xrayStatus = $request->get('xray_status', 'needs_attention');
        
        if ($xrayStatus === 'all') {
            // Show all OPD patients (for debugging)
            // No additional filtering
        } elseif ($xrayStatus === 'needs_attention') {
            // OPD patients who need X-ray attention (no chest X-ray completed yet)
            $query->whereDoesntHave('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('chest_xray_done_by')
                  ->where('chest_xray_done_by', '!=', '');
            });
        } elseif ($xrayStatus === 'xray_completed') {
            // OPD patients who have completed X-ray
            $query->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('chest_xray_done_by')
                  ->where('chest_xray_done_by', '!=', '');
            });
        }

        // Handle search
        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $opdPatients = $query->with(['medicalChecklists' => function($q) {
            $q->where('examination_type', 'opd');
        }])->latest()->get();

        // Debug: Log OPD patient and checklist info
        $totalOpdUsers = \App\Models\User::where('role', 'opd')->count();
        $opdWithChecklists = \App\Models\User::where('role', 'opd')
            ->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd');
            })->count();
        $opdWithCompletedXray = \App\Models\User::where('role', 'opd')
            ->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('chest_xray_done_by')
                  ->where('chest_xray_done_by', '!=', '');
            })->count();
            
        // Debug: Get detailed info about each OPD patient
        $allOpdUsers = \App\Models\User::where('role', 'opd')->with('medicalChecklists')->get();
        $debugInfo = [];
        foreach ($allOpdUsers as $user) {
            $checklists = $user->medicalChecklists->where('examination_type', 'opd');
            $debugInfo[] = [
                'user_id' => $user->id,
                'name' => trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')),
                'checklists_count' => $checklists->count(),
                'chest_xray_done_by' => $checklists->first()->chest_xray_done_by ?? null,
                'has_xray_image' => $checklists->first()->xray_image_path ?? null,
            ];
        }
        
        \Log::info('Radtech OPD Debug:', [
            'total_opd_users' => $totalOpdUsers,
            'opd_with_checklists' => $opdWithChecklists,
            'opd_with_completed_xray' => $opdWithCompletedXray,
            'current_tab' => $xrayStatus,
            'filtered_patients_count' => $opdPatients->count(),
            'detailed_user_info' => $debugInfo
        ]);

        return view('radtech.opd-xray', compact('opdPatients'));
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
        $examinationType = 'annual_physical';
        
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
     * Show medical checklist for OPD
     */
    public function showMedicalChecklistOpd($userId)
    {
        $opdPatient = \App\Models\User::where('role', 'opd')->findOrFail($userId);
        
        // Find the most recent OPD examination record for this patient
        $opdExamination = \App\Models\OpdExamination::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Try to find checklist linked to this examination
        $medicalChecklist = null;
        if ($opdExamination) {
            $medicalChecklist = MedicalChecklist::where('opd_examination_id', $opdExamination->id)
                ->where('examination_type', 'opd')
                ->first();
        }
        
        // Fallback: check by user_id for unlinked checklists
        if (!$medicalChecklist) {
            $medicalChecklist = MedicalChecklist::where('user_id', $userId)
                ->where('examination_type', 'opd')
                ->first();
        }
        
        $examinationType = 'opd';
        $examinations = [
            'chest_xray' => ['name' => 'Chest X-Ray', 'icon' => 'fas fa-x-ray', 'color' => 'indigo'],
            'stool_exam' => ['name' => 'Stool Examination', 'icon' => 'fas fa-flask', 'color' => 'amber'],
            'urinalysis' => ['name' => 'Urinalysis', 'icon' => 'fas fa-tint', 'color' => 'blue'],
            'drug_test' => ['name' => 'Drug Test', 'icon' => 'fas fa-pills', 'color' => 'red'],
            'blood_extraction' => ['name' => 'Blood Extraction', 'icon' => 'fas fa-syringe', 'color' => 'rose'],
            'ecg' => ['name' => 'ElectroCardioGram (ECG)', 'icon' => 'fas fa-heartbeat', 'color' => 'green'],
            'physical_exam' => ['name' => 'Physical Examination', 'icon' => 'fas fa-stethoscope', 'color' => 'purple'],
        ];
        $number = 'OPD-' . str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? ''));
        $age = $opdPatient->age ?? null;
        $date = now()->format('Y-m-d');
        
        return view('radtech.medical-checklist', compact(
            'medicalChecklist', 
            'opdPatient', 
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
        \Log::info('UPDATE Medical Checklist called:', [
            'id' => $id,
            'method' => $request->method(),
            'all_data' => $request->all()
        ]);
        
        try {
            $medicalChecklist = MedicalChecklist::findOrFail($id);
            
            $validated = $request->validate([
                'chest_xray_done_by' => 'nullable|string|max:100',
                'xray_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25000',
                'examination_type' => 'nullable|string',
                'user_id' => 'nullable|integer',
                'name' => 'nullable|string',
                'date' => 'nullable|date',
                'age' => 'nullable|integer',
                'number' => 'nullable|string',
            ]);

            if ($request->hasFile('xray_image') && $request->file('xray_image')->isValid()) {
                $path = $request->file('xray_image')->store('xray-images', 'public');
                $validated['xray_image_path'] = $path;
            }

            // Update all fields
            foreach ($validated as $key => $value) {
                if ($key !== 'xray_image') { // Skip the file field
                    $medicalChecklist->$key = $value;
                }
            }
            
            $medicalChecklist->save();

            // Create notification for admin when X-ray is completed
            if (!empty($validated['chest_xray_done_by'])) {
                $radtech = Auth::user();
                $patientName = $medicalChecklist->name;
                
                // Determine examination type
                if ($medicalChecklist->pre_employment_record_id) {
                    $examinationType = 'Pre-Employment';
                } elseif ($medicalChecklist->patient_id) {
                    $examinationType = 'Annual Physical';
                } elseif ($medicalChecklist->user_id || $medicalChecklist->examination_type === 'opd') {
                    $examinationType = 'OPD';
                } else {
                    $examinationType = 'Unknown';
                }
                
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
            }

            // Determine redirect route based on examination type
            if ($medicalChecklist->pre_employment_record_id) {
                return redirect()->route('radtech.pre-employment-xray')
                    ->with('success', 'X-ray information updated successfully.');
            } elseif ($medicalChecklist->patient_id) {
                return redirect()->route('radtech.annual-physical-xray')
                    ->with('success', 'X-ray information updated successfully.');
            } elseif ($medicalChecklist->user_id || $medicalChecklist->examination_type === 'opd') {
                return redirect()->route('radtech.opd.xray')
                    ->with('success', 'X-ray information updated successfully.');
            }

            return redirect()->route('radtech.dashboard')
                ->with('success', 'X-ray information updated successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update X-ray information: ' . $e->getMessage());
        }
    }

    /**
     * Store a new medical checklist (RadTech can create if missing)
     */
    public function storeMedicalChecklist(Request $request)
    {
        // Debug: Log all incoming request data
        \Log::info('Store Medical Checklist Debug:', [
            'all_request_data' => $request->all(),
            'examination_type' => $request->input('examination_type'),
            'user_id' => $request->input('user_id'),
            'chest_xray_done_by' => $request->input('chest_xray_done_by'),
            'has_xray_file' => $request->hasFile('xray_image')
        ]);
        
        try {
            $validated = $request->validate([
                'examination_type' => 'required|in:pre-employment,annual_physical,opd',
                'pre_employment_record_id' => 'required_if:examination_type,pre-employment|nullable|exists:pre_employment_records,id',
                'patient_id' => 'required_if:examination_type,annual_physical|nullable|exists:patients,id',
                'user_id' => 'required_if:examination_type,opd|nullable|exists:users,id',
                'name' => 'required|string|max:255',
                'date' => 'required|date',
                'age' => 'required|integer|min:0',
                'number' => 'nullable|string|max:255',
                'chest_xray_done_by' => 'nullable|string|max:100',
                'xray_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25000',
            ]);

        \Log::info('Validation passed, validated data:', $validated);

        // Set user_id based on examination type
        if ($validated['examination_type'] === 'opd') {
            // For OPD, user_id should be the OPD patient's ID (from form)
            // Keep the user_id from validation
        } else {
            // For other types, user_id is the authenticated radtech
            $validated['user_id'] = Auth::id();
        }

        if ($request->hasFile('xray_image')) {
            $path = $request->file('xray_image')->store('xray-images', 'public');
            $validated['xray_image_path'] = $path;
        }

        // Ensure foreign keys persist based on context
        if ($validated['examination_type'] === 'pre-employment' && $request->filled('pre_employment_record_id')) {
            $validated['pre_employment_record_id'] = (int)$request->input('pre_employment_record_id');
        }
        if ($validated['examination_type'] === 'annual_physical') {
            if ($request->filled('patient_id')) {
                $validated['patient_id'] = (int)$request->input('patient_id');
                
                // Find or create annual physical examination and link it
                $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::where('patient_id', $validated['patient_id'])
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$annualPhysicalExam) {
                    $annualPhysicalExam = \App\Models\AnnualPhysicalExamination::create([
                        'patient_id' => $validated['patient_id'],
                        'status' => 'Pending'
                    ]);
                }
                
                $validated['annual_physical_examination_id'] = $annualPhysicalExam->id;
            }
        }
        if ($validated['examination_type'] === 'opd') {
            if ($request->filled('user_id')) {
                $validated['user_id'] = (int)$request->input('user_id');
                
                // Find or create OPD examination and link it
                $opdExamination = \App\Models\OpdExamination::where('user_id', $validated['user_id'])
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$opdExamination) {
                    $opdExamination = \App\Models\OpdExamination::create([
                        'user_id' => $validated['user_id'],
                        'status' => 'pending'
                    ]);
                }
                
                $validated['opd_examination_id'] = $opdExamination->id;
            }
        }

        // Check for existing checklist to update instead of creating duplicate
        $existingChecklist = null;
        if (!empty($validated['annual_physical_examination_id'])) {
            $existingChecklist = MedicalChecklist::where('annual_physical_examination_id', $validated['annual_physical_examination_id'])
                ->whereIn('examination_type', ['annual_physical', 'annual-physical'])
                ->first();
        } elseif (!empty($validated['pre_employment_record_id'])) {
            $existingChecklist = MedicalChecklist::where('pre_employment_record_id', $validated['pre_employment_record_id'])
                ->whereIn('examination_type', ['pre_employment', 'pre-employment'])
                ->first();
        } elseif (!empty($validated['opd_examination_id'])) {
            $existingChecklist = MedicalChecklist::where('opd_examination_id', $validated['opd_examination_id'])
                ->where('examination_type', 'opd')
                ->first();
        }
        
        if ($existingChecklist) {
            $existingChecklist->update($validated);
            $medicalChecklist = $existingChecklist;
        } else {
            $medicalChecklist = MedicalChecklist::create($validated);
        }

        // Create notification for admin when X-ray is completed
        if (!empty($validated['chest_xray_done_by'])) {
            $radtech = Auth::user();
            $patientName = $validated['name'];
            if ($validated['examination_type'] === 'pre-employment') {
                $examinationType = 'Pre-Employment';
            } elseif ($validated['examination_type'] === 'annual_physical') {
                $examinationType = 'Annual Physical';
            } else {
                $examinationType = 'OPD';
            }
            
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
        }

        // Determine redirect route based on examination type
        if ($validated['examination_type'] === 'pre-employment') {
            return redirect()->route('radtech.pre-employment-xray')
                ->with('success', 'X-ray information saved successfully.');
        } elseif ($validated['examination_type'] === 'annual_physical') {
            return redirect()->route('radtech.annual-physical-xray')
                ->with('success', 'X-ray information saved successfully.');
        } elseif ($validated['examination_type'] === 'opd') {
            return redirect()->route('radtech.opd.xray')
                ->with('success', 'X-ray information saved successfully.');
        }

        return redirect()->route('radtech.dashboard')
            ->with('success', 'Medical checklist created successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save X-ray information: ' . $e->getMessage());
        }
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
