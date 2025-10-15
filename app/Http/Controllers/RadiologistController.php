<?php

namespace App\Http\Controllers;

use App\Models\MedicalChecklist;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use App\Models\PreEmploymentRecord;
use App\Models\Patient;
use App\Models\Notification;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class RadiologistController extends Controller
{
    public function dashboard()
    {
        $checklists = MedicalChecklist::whereNotNull('xray_image_path')
            ->latest('date')
            ->take(20)
            ->get();

        $preEmployments = PreEmploymentExamination::with('preEmploymentRecord')
            ->whereHas('preEmploymentRecord', function($query) {
                $query->where('status', 'approved');
            })
            ->whereNotIn('status', ['Approved', 'sent_to_company'])
            ->latest('date')
            ->take(20)
            ->get()
            ->map(function ($exam) {
                $finding = null;
                $labFindings = $exam->lab_findings ?? [];
                if (is_array($labFindings)) {
                    $finding = $labFindings['chest_xray'] ?? ($labFindings['Chest X-Ray'] ?? null);
                    if (is_array($finding)) {
                        $finding = implode(', ', array_map(function ($v) {
                            return is_scalar($v) ? (string)$v : '';
                        }, $finding));
                        $finding = trim($finding, ', ');
                    }
                }
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'company' => optional($exam->preEmploymentRecord)->company_name,
                    'finding' => $finding,
                ];
            });

        $annuals = AnnualPhysicalExamination::with('patient')
            ->whereHas('patient', function($query) {
                $query->where('status', 'approved');
            })
            ->whereNotIn('status', ['completed', 'sent_to_company'])
            ->latest('date')
            ->take(20)
            ->get()
            ->map(function ($exam) {
                $finding = null;
                $labFindings = $exam->lab_findings ?? [];
                if (is_array($labFindings)) {
                    $finding = $labFindings['chest_xray'] ?? ($labFindings['Chest X-Ray'] ?? null);
                    if (is_array($finding)) {
                        $finding = implode(', ', array_map(function ($v) {
                            return is_scalar($v) ? (string)$v : '';
                        }, $finding));
                        $finding = trim($finding, ', ');
                    }
                }
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'company' => optional($exam->patient)->company_name ?? optional($exam->patient)->company,
                    'finding' => $finding,
                ];
            });

        return view('radiologist.dashboard', compact('checklists', 'preEmployments', 'annuals'));
    }

    public function showPreEmployment($id)
    {
        // $id is PreEmploymentRecord ID
        $record = PreEmploymentRecord::findOrFail($id);
        
        // Get the examination for this record
        $exam = PreEmploymentExamination::where('pre_employment_record_id', $id)->first();
        
        $cxr_result = '—';
        $cxr_finding = '—';
        
        if ($exam) {
            $cxr = $exam->lab_findings['chest_xray'] ?? ($exam->lab_findings['Chest X-Ray'] ?? null);
            if (is_array($cxr)) {
                $cxr_result = is_scalar($cxr['result'] ?? null) ? (string)$cxr['result'] : '—';
                $cxr_finding = is_scalar($cxr['finding'] ?? null) ? (string)$cxr['finding'] : '—';
            } else {
                $cxr_finding = is_scalar($cxr) ? (string)$cxr : '—';
            }
        }
        
        // Get the medical checklist with X-ray image
        $checklist = MedicalChecklist::where('pre_employment_record_id', $id)
            ->whereNotNull('xray_image_path')
            ->latest('date')
            ->first();
            
        // Fallback: attempt match by full name if still null
        if (!$checklist) {
            $fullName = $record->first_name . ' ' . $record->last_name;
            $checklist = MedicalChecklist::where('name', $fullName)
                ->whereNotNull('xray_image_path')
                ->latest('date')
                ->first();
        }
        
        $data = [
            'record' => $record,
            'exam' => $exam,
            'full_name' => $record->first_name . ' ' . $record->last_name,
            'sex' => $record->sex,
            'age' => $record->age,
            'company' => $record->company_name,
            'cxr_result' => $cxr_result,
            'cxr_finding' => $cxr_finding,
            'checklist' => $checklist,
        ];
        return view('radiologist.pre-employment-show', $data);
    }

    public function showAnnualPhysical($id)
    {
        // $id is Patient ID
        $patient = Patient::findOrFail($id);
        
        // Get the examination for this patient
        $exam = AnnualPhysicalExamination::where('patient_id', $id)->first();
        
        $cxr_result = '—';
        $cxr_finding = '—';
        
        if ($exam) {
            $cxr = $exam->lab_findings['chest_xray'] ?? ($exam->lab_findings['Chest X-Ray'] ?? null);
            if (is_array($cxr)) {
                $cxr_result = is_scalar($cxr['result'] ?? null) ? (string)$cxr['result'] : '—';
                $cxr_finding = is_scalar($cxr['finding'] ?? null) ? (string)$cxr['finding'] : '—';
            } else {
                $cxr_finding = is_scalar($cxr) ? (string)$cxr : '—';
            }
        }
        
        // Get the medical checklist with X-ray image
        $checklist = MedicalChecklist::where('patient_id', $id)
            ->whereNotNull('xray_image_path')
            ->latest('date')
            ->first();
            
        // Fallback: attempt match by full name if still null
        if (!$checklist) {
            $fullName = $patient->first_name . ' ' . $patient->last_name;
            $checklist = MedicalChecklist::where('name', $fullName)
                ->whereNotNull('xray_image_path')
                ->latest('date')
                ->first();
        }
        
        $data = [
            'patient' => $patient,
            'exam' => $exam,
            'full_name' => $patient->first_name . ' ' . $patient->last_name,
            'sex' => $patient->sex,
            'age' => $patient->age,
            'company' => $patient->company_name ?? ($patient->company ?? null),
            'cxr_result' => $cxr_result,
            'cxr_finding' => $cxr_finding,
            'checklist' => $checklist,
        ];
        return view('radiologist.annual-physical-show', $data);
    }

    public function updatePreEmployment(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'cxr_result' => 'nullable|string',
            'cxr_finding' => 'nullable|string',
        ]);
        
        // $id is PreEmploymentRecord ID
        $record = PreEmploymentRecord::findOrFail($id);
        
        // Get or create the examination
        $exam = PreEmploymentExamination::where('pre_employment_record_id', $id)->first();
        
        if (!$exam) {
            // Create examination if it doesn't exist
            $exam = new PreEmploymentExamination();
            $exam->pre_employment_record_id = $id;
            $exam->name = $record->first_name . ' ' . $record->last_name;
            $exam->date = now();
            $exam->status = 'collection_completed';
            $exam->lab_findings = [];
        }
        
        $lab = is_array($exam->lab_findings) ? $exam->lab_findings : [];
        
        // Initialize chest_xray array if it doesn't exist
        if (!isset($lab['chest_xray'])) {
            $lab['chest_xray'] = [];
        }
        
        // Store the current radiologist's review
        $currentRadiologistId = auth()->id();
        $radiologist = Auth::user();
        
        // Initialize reviews array if it doesn't exist
        if (!isset($lab['chest_xray']['reviews'])) {
            $lab['chest_xray']['reviews'] = [];
        }
        
        // Add or update this radiologist's review
        $lab['chest_xray']['reviews'][$currentRadiologistId] = [
            'result' => $request->input('cxr_result'),
            'finding' => $request->input('cxr_finding'),
            'radiologist_name' => $radiologist->name,
            'reviewed_at' => now()->toDateTimeString(),
        ];
        
        // Keep the most recent review as the primary result (for backward compatibility)
        $lab['chest_xray']['result'] = $request->input('cxr_result');
        $lab['chest_xray']['finding'] = $request->input('cxr_finding');
        $lab['chest_xray']['reviewed_by'] = $currentRadiologistId;
        $lab['chest_xray']['reviewed_at'] = now()->toDateTimeString();
        
        // Debug: Log what we're saving
        \Log::info('Radiologist Controller - Saving Pre-Employment Lab Findings:', [
            'examination_id' => $exam->id,
            'cxr_result' => $request->input('cxr_result'),
            'cxr_finding' => $request->input('cxr_finding'),
            'lab_findings_before_save' => $lab,
            'chest_xray_data' => $lab['chest_xray'] ?? null
        ]);
        
        $exam->lab_findings = $lab;
        $exam->save();
        
        // Debug: Verify what was actually saved
        $exam->refresh();
        \Log::info('Radiologist Controller - After Pre-Employment Save Verification:', [
            'examination_id' => $exam->id,
            'saved_lab_findings' => $exam->lab_findings,
            'saved_chest_xray' => $exam->lab_findings['chest_xray'] ?? null
        ]);
        
        // Check and update collection status after radiologist review
        $this->checkAndUpdateCollectionStatus($record);
        
        // Create notification for admin when X-ray interpretation is completed
        $patientName = $record->full_name;
        
        Notification::createForAdmin(
            'xray_interpreted',
            'X-Ray Interpretation Completed - Pre-Employment',
            "Radiologist {$radiologist->name} has completed X-ray interpretation for {$patientName} (Pre-Employment). Result: " . ($request->input('cxr_result') ?: 'No result specified'),
            [
                'examination_id' => $exam->id,
                'patient_name' => $patientName,
                'radiologist_name' => $radiologist->name,
                'examination_type' => 'pre_employment',
                'xray_result' => $request->input('cxr_result'),
                'xray_finding' => $request->input('cxr_finding'),
                'has_findings' => !empty($request->input('cxr_finding'))
            ],
            'medium',
            $radiologist,
            $exam
        );
        
        return redirect()->route('radiologist.pre-employment-xray')->with('success', 'Chest X-Ray findings submitted successfully. Record status updated to Collection Completed and is now ready for doctor review.');
    }

    public function updateAnnualPhysical(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'cxr_result' => 'nullable|string',
            'cxr_finding' => 'nullable|string',
        ]);
        
        // $id is Patient ID
        $patient = Patient::findOrFail($id);
        
        // Get or create the examination
        $exam = AnnualPhysicalExamination::where('patient_id', $id)->first();
        
        if (!$exam) {
            // Create examination if it doesn't exist
            $exam = new AnnualPhysicalExamination();
            $exam->patient_id = $id;
            $exam->name = $patient->first_name . ' ' . $patient->last_name;
            $exam->date = now();
            $exam->status = 'collection_completed';
            $exam->lab_findings = [];
        }
        
        $lab = is_array($exam->lab_findings) ? $exam->lab_findings : [];
        
        // Initialize chest_xray array if it doesn't exist
        if (!isset($lab['chest_xray'])) {
            $lab['chest_xray'] = [];
        }
        
        // Store the current radiologist's review
        $currentRadiologistId = auth()->id();
        $radiologist = Auth::user();
        
        // Initialize reviews array if it doesn't exist
        if (!isset($lab['chest_xray']['reviews'])) {
            $lab['chest_xray']['reviews'] = [];
        }
        
        // Add or update this radiologist's review
        $lab['chest_xray']['reviews'][$currentRadiologistId] = [
            'result' => $request->input('cxr_result'),
            'finding' => $request->input('cxr_finding'),
            'radiologist_name' => $radiologist->name,
            'reviewed_at' => now()->toDateTimeString(),
        ];
        
        // Keep the most recent review as the primary result (for backward compatibility)
        $lab['chest_xray']['result'] = $request->input('cxr_result');
        $lab['chest_xray']['finding'] = $request->input('cxr_finding');
        $lab['chest_xray']['reviewed_by'] = $currentRadiologistId;
        $lab['chest_xray']['reviewed_at'] = now()->toDateTimeString();
        
        // Debug: Log what we're saving
        \Log::info('Radiologist Controller - Saving Annual Physical Lab Findings:', [
            'examination_id' => $exam->id,
            'patient_id' => $patient->id,
            'cxr_result' => $request->input('cxr_result'),
            'cxr_finding' => $request->input('cxr_finding'),
            'lab_findings_before_save' => $lab,
            'chest_xray_data' => $lab['chest_xray'] ?? null
        ]);
        
        $exam->lab_findings = $lab;
        $exam->save();
        
        // Debug: Verify what was actually saved
        $exam->refresh();
        \Log::info('Radiologist Controller - After Annual Physical Save Verification:', [
            'examination_id' => $exam->id,
            'patient_id' => $patient->id,
            'saved_lab_findings' => $exam->lab_findings,
            'saved_chest_xray' => $exam->lab_findings['chest_xray'] ?? null
        ]);
        
        // Check and update collection status after radiologist review
        $this->checkAndUpdateCollectionStatusForAnnual($patient);
        
        // Create notification for admin when X-ray interpretation is completed
        $patientName = $patient->full_name;
        
        Notification::createForAdmin(
            'xray_interpreted',
            'X-Ray Interpretation Completed - Annual Physical',
            "Radiologist {$radiologist->name} has completed X-ray interpretation for {$patientName} (Annual Physical). Result: " . ($request->input('cxr_result') ?: 'No result specified'),
            [
                'examination_id' => $exam->id,
                'patient_name' => $patientName,
                'radiologist_name' => $radiologist->name,
                'examination_type' => 'annual_physical',
                'xray_result' => $request->input('cxr_result'),
                'xray_finding' => $request->input('cxr_finding'),
                'has_findings' => !empty($request->input('cxr_finding'))
            ],
            'medium',
            $radiologist,
            $exam
        );
        
        return redirect()->route('radiologist.annual-physical-xray')->with('success', 'Chest X-Ray findings submitted successfully. Record status updated to Collection Completed and is now ready for doctor review.');
    }

    /**
     * Show pre-employment X-ray list
     */
    public function preEmploymentXray(Request $request)
    {
        $currentRadiologistId = auth()->id();
        
        $query = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest', 'medicalChecklist', 'preEmploymentExamination'])
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

        $xrayStatus = $request->get('xray_status', 'needs_attention');
        
        // First, ensure records have X-ray images available
        $query->whereHas('medicalChecklist', function ($q) {
            $q->whereNotNull('chest_xray_done_by')
              ->where('chest_xray_done_by', '!=', '')
              ->whereNotNull('xray_image_path')
              ->where('xray_image_path', '!=', '');
        });

        $allRecords = $query->latest()->get();
        
        // Filter records based on tab selection
        $preEmployments = $allRecords->filter(function ($record) use ($currentRadiologistId, $xrayStatus) {
            $exam = $record->preEmploymentExamination;
            
            // If no examination exists yet, show in needs_attention only
            if (!$exam) {
                return $xrayStatus === 'needs_attention';
            }
            
            // Check if this radiologist has already reviewed this record
            $labFindings = $exam->lab_findings ?? [];
            $hasReviewed = isset($labFindings['chest_xray']['reviews'][$currentRadiologistId]);
            
            if ($xrayStatus === 'needs_attention') {
                // Show records that this radiologist hasn't reviewed yet
                return !$hasReviewed;
            } elseif ($xrayStatus === 'review_completed') {
                // Show records that this radiologist has already reviewed
                return $hasReviewed;
            }
            
            return true;
        });

        return view('radiologist.pre-employment-xray', compact('preEmployments'));
    }

    /**
     * Show annual physical X-ray list
     */
    public function annualPhysicalXray(Request $request)
    {
        $currentRadiologistId = auth()->id();
        
        $query = Patient::with(['medicalChecklist', 'annualPhysicalExamination'])
            ->where('status', 'approved');

        $xrayStatus = $request->get('xray_status', 'needs_attention');
        
        // First, ensure patients have X-ray images available
        $query->whereHas('medicalChecklist', function ($q) {
            $q->whereNotNull('chest_xray_done_by')
              ->where('chest_xray_done_by', '!=', '')
              ->whereNotNull('xray_image_path')
              ->where('xray_image_path', '!=', '');
        });

        $allPatients = $query->latest()->get();
        
        // Debug: Log what patients we found
        \Log::info('Radiologist Controller - Annual Physical Patients Debug:', [
            'total_patients_found' => $allPatients->count(),
            'xray_status' => $xrayStatus,
            'current_radiologist_id' => $currentRadiologistId,
            'patients_with_checklists' => $allPatients->filter(function($p) { return $p->medicalChecklist; })->count(),
            'patients_with_examinations' => $allPatients->filter(function($p) { return $p->annualPhysicalExamination; })->count()
        ]);
        
        // Filter patients based on tab selection
        $patients = $allPatients->filter(function ($patient) use ($currentRadiologistId, $xrayStatus) {
            $exam = $patient->annualPhysicalExamination;
            
            // If no examination exists yet, show in needs_attention only
            if (!$exam) {
                return $xrayStatus === 'needs_attention';
            }
            
            // Check if this radiologist has already reviewed this patient
            $labFindings = $exam->lab_findings ?? [];
            $hasReviewed = isset($labFindings['chest_xray']['reviews'][$currentRadiologistId]);
            
            if ($xrayStatus === 'needs_attention') {
                // Show patients that this radiologist hasn't reviewed yet
                return !$hasReviewed;
            } elseif ($xrayStatus === 'review_completed') {
                // Show patients that this radiologist has already reviewed
                return $hasReviewed;
            }
            
            return true;
        });

        // Debug: Log final filtered results
        \Log::info('Radiologist Controller - Annual Physical Final Results:', [
            'filtered_patients_count' => $patients->count(),
            'xray_status' => $xrayStatus,
            'patient_ids' => $patients->pluck('id')->toArray()
        ]);

        return view('radiologist.annual-physical-xray', compact('patients'));
    }

    /**
     * Show OPD X-ray list
     */
    public function opdXray(Request $request)
    {
        $currentRadiologistId = auth()->id();
        
        $query = \App\Models\User::where('role', 'opd');

        // Debug: Log total OPD users
        $totalOpdUsers = \App\Models\User::where('role', 'opd')->count();
        $opdUsersWithChecklists = \App\Models\User::where('role', 'opd')
            ->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd');
            })->count();
        $opdUsersWithXrays = \App\Models\User::where('role', 'opd')
            ->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('xray_image_path')
                  ->where('xray_image_path', '!=', '');
            })->count();
            
        \Log::info('Radiologist OPD Debug:', [
            'total_opd_users' => $totalOpdUsers,
            'opd_with_checklists' => $opdUsersWithChecklists,
            'opd_with_xrays' => $opdUsersWithXrays
        ]);

        // Handle tab filtering
        $xrayStatus = $request->get('xray_status', 'needs_attention');
        
        if ($xrayStatus === 'all') {
            // Show all OPD patients with X-ray images (for debugging)
            $query->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('xray_image_path')
                  ->where('xray_image_path', '!=', '');
            });
        } elseif ($xrayStatus === 'needs_attention') {
            // OPD patients who have X-ray images but no radiologist findings yet
            $query->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('xray_image_path')
                  ->where('xray_image_path', '!=', '');
            });
            
            // Exclude those who already have radiologist findings
            // Check if they have an OPD examination with chest X-ray results
            $query->whereDoesntHave('opdExamination', function($q) {
                $q->whereNotNull('lab_findings')
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) IS NOT NULL")
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) != ''");
            });
        } elseif ($xrayStatus === 'completed') {
            // OPD patients who have completed radiologist review
            $query->whereHas('medicalChecklists', function($q) {
                $q->where('examination_type', 'opd')
                  ->whereNotNull('xray_image_path')
                  ->where('xray_image_path', '!=', '');
            })->whereHas('opdExamination', function($q) {
                $q->whereNotNull('lab_findings')
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) IS NOT NULL")
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(lab_findings, '$.\"chest_xray\".\"result\"')) != ''");
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
            $q->where('examination_type', 'opd')
              ->whereNotNull('xray_image_path');
        }])->latest()->get();

        return view('radiologist.opd-xray', compact('opdPatients'));
    }

    /**
     * Show OPD X-ray details for radiologist review
     */
    public function showOpd($userId)
    {
        $opdPatient = \App\Models\User::where('role', 'opd')->findOrFail($userId);
        
        // Find the most recent OPD examination record for this patient
        $opdExamination = \App\Models\OpdExamination::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Find medical checklist with X-ray image
        $medicalChecklist = \App\Models\MedicalChecklist::where('user_id', $userId)
            ->where('examination_type', 'opd')
            ->whereNotNull('xray_image_path')
            ->where('xray_image_path', '!=', '')
            ->first();

        if (!$medicalChecklist) {
            return redirect()->route('radiologist.opd.xray')->with('error', 'No X-ray image found for this OPD patient.');
        }

        // Get existing X-ray findings from OpdExamination
        $cxr_result = '—';
        $cxr_finding = '—';
        
        if ($opdExamination) {
            $cxr = $opdExamination->lab_findings['chest_xray'] ?? ($opdExamination->lab_findings['Chest X-Ray'] ?? null);
            if (is_array($cxr)) {
                $cxr_result = is_scalar($cxr['result'] ?? null) ? (string)$cxr['result'] : '—';
                $cxr_finding = is_scalar($cxr['finding'] ?? null) ? (string)$cxr['finding'] : '—';
            } else {
                $cxr_finding = is_scalar($cxr) ? (string)$cxr : '—';
            }
        }

        $data = [
            'opdPatient' => $opdPatient,
            'opdExamination' => $opdExamination,
            'medicalChecklist' => $medicalChecklist,
            'number' => 'OPD-' . str_pad($opdPatient->id, 4, '0', STR_PAD_LEFT),
            'name' => trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? '')),
            'age' => $opdPatient->age ?? null,
            'gender' => $opdPatient->gender ?? null,
            'email' => $opdPatient->email,
            'date' => $medicalChecklist->date ?? now()->format('Y-m-d'),
            'cxr_result' => $cxr_result,
            'cxr_finding' => $cxr_finding,
        ];

        return view('radiologist.opd-show', $data);
    }

    /**
     * Update OPD X-ray findings
     */
    public function updateOpd(\Illuminate\Http\Request $request, $userId)
    {
        $request->validate([
            'cxr_result' => 'nullable|string',
            'cxr_finding' => 'nullable|string',
        ]);

        $opdPatient = \App\Models\User::where('role', 'opd')->findOrFail($userId);
        
        // Find or create OPD examination
        $opdExamination = \App\Models\OpdExamination::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$opdExamination) {
            return redirect()->back()->with('error', 'OPD examination not found.');
        }

        // Get current radiologist info
        $currentRadiologistId = auth()->id();
        $radiologist = auth()->user();
        $patientName = trim(($opdPatient->fname ?? '') . ' ' . ($opdPatient->lname ?? ''));

        // Get existing lab findings or initialize
        $lab = $opdExamination->lab_findings ?? [];
        
        // Initialize chest_xray section if it doesn't exist
        if (!isset($lab['chest_xray'])) {
            $lab['chest_xray'] = [];
        }
        
        // Initialize reviews array if it doesn't exist
        if (!isset($lab['chest_xray']['reviews'])) {
            $lab['chest_xray']['reviews'] = [];
        }
        
        // Add or update this radiologist's review
        $lab['chest_xray']['reviews'][$currentRadiologistId] = [
            'result' => $request->input('cxr_result'),
            'finding' => $request->input('cxr_finding'),
            'radiologist_name' => $radiologist->name,
            'reviewed_at' => now()->toDateTimeString(),
        ];
        
        // Keep the most recent review as the primary result (for backward compatibility)
        $lab['chest_xray']['result'] = $request->input('cxr_result');
        $lab['chest_xray']['finding'] = $request->input('cxr_finding');
        $lab['chest_xray']['reviewed_by'] = $currentRadiologistId;
        $lab['chest_xray']['reviewed_at'] = now()->toDateTimeString();
        
        // Save the updated lab findings
        $opdExamination->lab_findings = $lab;
        $opdExamination->save();

        // Create notification for admin
        \App\Models\Notification::createForAdmin(
            'xray_interpreted',
            'X-Ray Interpretation Completed - OPD',
            "Radiologist {$radiologist->name} has completed X-ray interpretation for {$patientName} (OPD). Result: " . ($request->input('cxr_result') ?: 'No result specified'),
            [
                'examination_id' => $opdExamination->id,
                'patient_name' => $patientName,
                'radiologist_name' => $radiologist->name,
                'examination_type' => 'opd',
                'xray_result' => $request->input('cxr_result'),
                'xray_finding' => $request->input('cxr_finding'),
                'has_findings' => !empty($request->input('cxr_finding'))
            ],
            'medium',
            $radiologist,
            $opdExamination
        );

        return redirect()->route('radiologist.opd.xray')->with('success', 'OPD X-Ray findings submitted successfully. Record is now ready for doctor review.');
    }

    /**
     * Show X-ray gallery
     */
    public function xrayGallery()
    {
        $checklists = MedicalChecklist::whereNotNull('xray_image_path')
            ->whereNotNull('chest_xray_done_by')
            ->with(['preEmploymentRecord', 'patient'])
            ->latest('date')
            ->paginate(20);

        return view('radiologist.xray-gallery', compact('checklists'));
    }

    /**
     * Show radiologist messages view
     */
    public function messages()
    {
        return view('radiologist.messages');
    }

    /**
     * Get users that radiologist can chat with (admin and doctor only)
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
            // Get the last message between radiologist user and this user
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
     * Fetch messages for the current radiologist
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

        // Get messages between radiologist user and the specific user
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
     * Send a message (radiologist can only send to admin or doctor)
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
     * Get unread message count for the current radiologist user.
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
     * Update examination status after radiologist review - integrates with PleboController workflow
     */
    private function checkAndUpdateCollectionStatus($record)
    {
        // Get the examination that was created by PleboController when blood extraction was completed
        $exam = PreEmploymentExamination::where('pre_employment_record_id', $record->id)->first();
        
        if ($exam && $exam->status === 'collection_completed') {
            // Examination already exists with collection_completed status from PleboController
            // Radiologist has now added X-ray findings, so examination is ready for doctor
            // Update lab_report to indicate radiologist review is complete
            $labReport = $exam->lab_report ?? [];
            $labReport['radiologist_review_completed'] = true;
            $labReport['radiologist_review_completed_at'] = now()->toDateTimeString();
            $labReport['radiologist_name'] = Auth::user()->name;
            
            $exam->lab_report = $labReport;
            $exam->save();
        }
    }

    /**
     * Update examination status after radiologist review for annual physical
     */
    private function checkAndUpdateCollectionStatusForAnnual($patient)
    {
        // Get the examination that was created by PleboController when blood extraction was completed
        $exam = AnnualPhysicalExamination::where('patient_id', $patient->id)->first();
        
        if ($exam && $exam->status === 'collection_completed') {
            // Examination already exists with collection_completed status from PleboController
            // Radiologist has now added X-ray findings, so examination is ready for doctor
            // Update lab_report to indicate radiologist review is complete
            $labReport = $exam->lab_report ?? [];
            $labReport['radiologist_review_completed'] = true;
            $labReport['radiologist_review_completed_at'] = now()->toDateTimeString();
            $labReport['radiologist_name'] = Auth::user()->name;
            
            $exam->lab_report = $labReport;
            $exam->save();
        }
    }

    /**
     * Show the edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('radiologist.profile.edit', compact('user'));
    }

    /**
     * Update the radiologist's profile
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

        return redirect()->route('radiologist.profile.edit')->with('success', 'Profile updated successfully!');
    }
}


