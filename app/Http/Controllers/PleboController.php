<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use App\Models\OpdExamination;
use App\Models\MedicalChecklist;
use App\Models\User;
use App\Models\AppointmentTestAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PleboController extends Controller
{
    /**
     * Show the plebo dashboard
     */
    public function dashboard()
    {
        // Get pre-employment records that don't have a medical checklist or have an incomplete one
        $preEmployments = PreEmploymentRecord::where('status', 'approved')
            ->where(function($query) {
                // Check medical test relationships OR other_exams column
                $query->whereHas('medicalTest', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%CBC%')
                             ->orWhere('name', 'like', '%FECA%')
                             ->orWhere('name', 'like', '%Urine%')
                             ->orWhere('name', 'like', '%Blood%')
                             ->orWhere('name', 'like', '%Laboratory%');
                    });
                })->orWhereHas('medicalTests', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%CBC%')
                             ->orWhere('name', 'like', '%FECA%')
                             ->orWhere('name', 'like', '%Urine%')
                             ->orWhere('name', 'like', '%Blood%')
                             ->orWhere('name', 'like', '%Laboratory%');
                    });
                })->orWhere(function($q) {
                    // Also check other_exams column for medical test information
                    $q->where('other_exams', 'like', '%Pre-Employment%')
                      ->orWhere('other_exams', 'like', '%CBC%')
                      ->orWhere('other_exams', 'like', '%FECA%')
                      ->orWhere('other_exams', 'like', '%Urine%')
                      ->orWhere('other_exams', 'like', '%Blood%')
                      ->orWhere('other_exams', 'like', '%Laboratory%');
                });
            })
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->where(function($subQ) {
                              $subQ->whereNull('stool_exam_done_by')
                                   ->orWhereNull('urinalysis_done_by')
                                   ->orWhereNull('blood_extraction_done_by');
                          });
                      });
            })
            ->whereDoesntHave('preEmploymentExamination', function($q) {
                $q->whereIn('status', ['Approved', 'sent_to_company']);
            })
            ->latest()
            ->take(5)
            ->get();
            
        $preEmploymentCount = PreEmploymentRecord::where('status', 'approved')
            ->where(function($query) {
                // Check medical test relationships OR other_exams column
                $query->whereHas('medicalTest', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%CBC%')
                             ->orWhere('name', 'like', '%FECA%')
                             ->orWhere('name', 'like', '%Urine%')
                             ->orWhere('name', 'like', '%Blood%')
                             ->orWhere('name', 'like', '%Laboratory%');
                    });
                })->orWhereHas('medicalTests', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%CBC%')
                             ->orWhere('name', 'like', '%FECA%')
                             ->orWhere('name', 'like', '%Urine%')
                             ->orWhere('name', 'like', '%Blood%')
                             ->orWhere('name', 'like', '%Laboratory%');
                    });
                })->orWhere(function($q) {
                    // Also check other_exams column for medical test information
                    $q->where('other_exams', 'like', '%Pre-Employment%')
                      ->orWhere('other_exams', 'like', '%CBC%')
                      ->orWhere('other_exams', 'like', '%FECA%')
                      ->orWhere('other_exams', 'like', '%Urine%')
                      ->orWhere('other_exams', 'like', '%Blood%')
                      ->orWhere('other_exams', 'like', '%Laboratory%');
                });
            })
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->where(function($subQ) {
                              $subQ->whereNull('stool_exam_done_by')
                                   ->orWhereNull('urinalysis_done_by')
                                   ->orWhereNull('blood_extraction_done_by');
                          });
                      });
            })
            ->whereDoesntHave('preEmploymentExamination', function($q) {
                $q->whereIn('status', ['Approved', 'sent_to_company']);
            })
            ->count();

        // Get annual physical patients that don't have a medical checklist or have an incomplete one
        $patients = Patient::where('status', 'approved')
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->whereNull('blood_extraction_done_by');
                      });
            })
            ->whereDoesntHave('annualPhysicalExamination', function($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->latest()
            ->take(5)
            ->get();
            
        $patientCount = Patient::where('status', 'approved')
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->whereNull('blood_extraction_done_by');
                      });
            })
            ->whereDoesntHave('annualPhysicalExamination', function($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->count();

        // Get OPD patients that don't have a medical checklist or have an incomplete one
        $opdPatients = User::where('role', 'opd')
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->whereNull('blood_extraction_done_by');
                      });
            })
            ->whereDoesntHave('opdExamination', function($q) {
                $q->whereIn('status', ['completed', 'sent_to_doctor']);
            })
            ->latest()
            ->take(5)
            ->get();
            
        $opdCount = User::where('role', 'opd')
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->whereNull('blood_extraction_done_by');
                      });
            })
            ->whereDoesntHave('opdExamination', function($q) {
                $q->whereIn('status', ['completed', 'sent_to_doctor']);
            })
            ->count();

        $appointments = Appointment::with('patients')->latest()->take(5)->get();
        $appointmentCount = Appointment::count();

        return view('plebo.dashboard', compact(
            'preEmployments',
            'preEmploymentCount',
            'patients',
            'patientCount',
            'opdPatients',
            'opdCount',
            'appointments',
            'appointmentCount'
        ));
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

        return view('plebo.medical-checklist', compact('medicalChecklist', 'preEmploymentRecord', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist for annual physical
     */
    public function showMedicalChecklistAnnualPhysical($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $medicalChecklist = MedicalChecklist::where('patient_id', $patientId)->first();
        $examinationType = 'annual-physical';
        $number = 'PAT-' . str_pad($patient->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $age = $patient->age ?? null;
        $date = now()->format('Y-m-d');

        return view('plebo.medical-checklist', compact('medicalChecklist', 'patient', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * Show medical checklist for OPD
     */
    public function showMedicalChecklistOpd($userId)
    {
        $user = User::findOrFail($userId);
        $opdExamination = $user->opdExamination;
        $medicalChecklist = MedicalChecklist::where('opd_examination_id', optional($opdExamination)->id)->first();
        $examinationType = 'opd';
        $number = 'OPD-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $age = $user->age ?? null;
        $date = now()->format('Y-m-d');

        return view('plebo.medical-checklist', compact('medicalChecklist', 'user', 'opdExamination', 'examinationType', 'number', 'name', 'age', 'date'));
    }

    /**
     * List pre-employment records for plebo
     */
    public function preEmployment(Request $request)
    {
        $query = PreEmploymentRecord::where('status', 'approved')
            ->where(function($query) {
                // Check medical test relationships OR other_exams column
                $query->whereHas('medicalTest', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%CBC%')
                             ->orWhere('name', 'like', '%FECA%')
                             ->orWhere('name', 'like', '%Urine%')
                             ->orWhere('name', 'like', '%Blood%')
                             ->orWhere('name', 'like', '%Laboratory%');
                    });
                })->orWhereHas('medicalTests', function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%Pre-Employment%')
                             ->orWhere('name', 'like', '%CBC%')
                             ->orWhere('name', 'like', '%FECA%')
                             ->orWhere('name', 'like', '%Urine%')
                             ->orWhere('name', 'like', '%Blood%')
                             ->orWhere('name', 'like', '%Laboratory%');
                    });
                })->orWhere(function($q) {
                    // Also check other_exams column for medical test information
                    $q->where('other_exams', 'like', '%Pre-Employment%')
                      ->orWhere('other_exams', 'like', '%CBC%')
                      ->orWhere('other_exams', 'like', '%FECA%')
                      ->orWhere('other_exams', 'like', '%Urine%')
                      ->orWhere('other_exams', 'like', '%Blood%')
                      ->orWhere('other_exams', 'like', '%Laboratory%');
                });
            });

        // Handle tab filtering
        $bloodStatus = $request->get('blood_status', 'needs_attention');
        
        if ($bloodStatus === 'needs_attention') {
            // Records that need blood collection (no medical checklist or incomplete)
            $query->where(function($q) {
                $q->whereDoesntHave('medicalChecklist')
                  ->orWhereHas('medicalChecklist', function($subQ) {
                      $subQ->where(function($checkQ) {
                          $checkQ->whereNull('stool_exam_done_by')
                                 ->orWhereNull('urinalysis_done_by')
                                 ->orWhereNull('blood_extraction_done_by');
                      });
                  });
            });
        } elseif ($bloodStatus === 'collection_completed') {
            // Records where blood collection is completed
            $query->whereHas('medicalChecklist', function($q) {
                $q->whereNotNull('stool_exam_done_by')
                  ->where('stool_exam_done_by', '!=', '')
                  ->whereNotNull('urinalysis_done_by')
                  ->where('urinalysis_done_by', '!=', '')
                  ->whereNotNull('blood_extraction_done_by')
                  ->where('blood_extraction_done_by', '!=', '');
            });
        }

        // Apply additional filters
        if ($request->filled('company')) {
            $query->where('company_name', 'like', '%' . $request->company . '%');
        }

        if ($request->filled('gender')) {
            $query->where('sex', $request->gender);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        $preEmployments = $query
            ->with(['medicalTest', 'medicalChecklist'])
            ->latest()
            ->paginate(15);
            
        return view('plebo.pre-employment', compact('preEmployments'));
    }

    /**
     * List annual physical patients for plebo
     */
    public function annualPhysical()
    {
        $patients = Patient::where('status', 'approved')
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->whereNull('blood_extraction_done_by');
                      });
            })
            ->whereDoesntHave('annualPhysicalExamination', function($q) {
                $q->whereIn('status', ['completed', 'sent_to_company']);
            })
            ->latest()
            ->paginate(15);
            
        return view('plebo.annual-physical', compact('patients'));
    }

    /**
     * List OPD patients for plebo
     */
    public function opd()
    {
        $opdPatients = User::where('role', 'opd')
            ->where(function($query) {
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          $q->whereNull('blood_extraction_done_by');
                      });
            })
            ->whereDoesntHave('opdExamination', function($q) {
                $q->whereIn('status', ['completed', 'sent_to_doctor']);
            })
            ->latest()
            ->paginate(15);
            
        return view('plebo.opd', compact('opdPatients'));
    }


    /**
     * Store a new medical checklist
     */
    public function storeMedicalChecklist(Request $request)
    {
        $data = $request->validate([
            'examination_type' => 'required|in:pre_employment,annual_physical,opd',
            'pre_employment_record_id' => 'nullable|exists:pre_employment_records,id',
            'patient_id' => 'nullable|exists:patients,id',
            'annual_physical_examination_id' => 'nullable|exists:annual_physical_examinations,id',
            'opd_examination_id' => 'nullable|exists:opd_examinations,id',
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
            'special_notes' => 'nullable|string',
            'phlebotomist_name' => 'nullable|string',
            'phlebotomist_signature' => 'nullable|string',
        ]);

        // Set the user_id to the current user for OPD patients
        if ($data['examination_type'] === 'opd') {
            $data['user_id'] = $data['opd_examination_id'];
        } else {
            $data['user_id'] = Auth::id();
        }
        
        // Set the blood extraction done by the current user
        $data['blood_extraction_done_by'] = Auth::user()->full_name;
        
        // Debug logging
        \Log::info('Medical Checklist Data:', $data);

        // Find existing medical checklist or create new one
        $medicalChecklist = null;
        
        if ($data['examination_type'] === 'pre_employment' && $data['pre_employment_record_id']) {
            $medicalChecklist = MedicalChecklist::where('pre_employment_record_id', $data['pre_employment_record_id'])->first();
        } elseif ($data['examination_type'] === 'annual_physical' && $data['patient_id']) {
            $medicalChecklist = MedicalChecklist::where('patient_id', $data['patient_id'])->first();
        } elseif ($data['examination_type'] === 'opd' && $data['opd_examination_id']) {
            $medicalChecklist = MedicalChecklist::where('opd_examination_id', $data['opd_examination_id'])
                ->orWhere('user_id', $data['opd_examination_id'])
                ->first();
        }

        try {
            if ($medicalChecklist) {
                $medicalChecklist->update($data);
                \Log::info('Medical Checklist Updated:', ['id' => $medicalChecklist->id, 'data' => $data]);
            } else {
                $medicalChecklist = MedicalChecklist::create($data);
                \Log::info('Medical Checklist Created:', ['id' => $medicalChecklist->id, 'data' => $data]);
            }

            // Redirect based on examination type
            if ($data['examination_type'] === 'pre_employment') {
                return redirect()->route('plebo.pre-employment')->with('success', 'Medical checklist saved successfully.');
            } elseif ($data['examination_type'] === 'opd') {
                return redirect()->route('plebo.opd')->with('success', 'Medical checklist saved successfully.');
            } else {
                return redirect()->route('plebo.annual-physical')->with('success', 'Medical checklist saved successfully.');
            }
        } catch (\Exception $e) {
            \Log::error('Medical Checklist Save Error:', ['error' => $e->getMessage(), 'data' => $data]);
            return redirect()->back()->with('error', 'Failed to save medical checklist: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing medical checklist
     */
    public function updateMedicalChecklist(Request $request, $id)
    {
        $medicalChecklist = MedicalChecklist::findOrFail($id);

        $data = $request->validate([
            'examination_type' => 'required|in:pre_employment,annual_physical,opd',
            'pre_employment_record_id' => 'nullable|exists:pre_employment_records,id',
            'patient_id' => 'nullable|exists:patients,id',
            'annual_physical_examination_id' => 'nullable|exists:annual_physical_examinations,id',
            'opd_examination_id' => 'nullable|exists:opd_examinations,id',
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
            'special_notes' => 'nullable|string',
            'phlebotomist_name' => 'nullable|string',
            'phlebotomist_signature' => 'nullable|string',
        ]);

        $medicalChecklist->update($data);

        // Create notification for admin when blood collection is completed
        if (!empty($data['stool_exam_done_by']) || !empty($data['urinalysis_done_by']) || !empty($data['blood_extraction_done_by'])) {
            $phlebotomist = Auth::user();
            $patientName = $data['name'];
            $examinationType = ucwords(str_replace('_', ' ', $data['examination_type']));
            
            $completedTests = [];
            if (!empty($data['stool_exam_done_by'])) $completedTests[] = 'Stool Exam';
            if (!empty($data['urinalysis_done_by'])) $completedTests[] = 'Urinalysis';
            if (!empty($data['blood_extraction_done_by'])) $completedTests[] = 'Blood Extraction';
            
            Notification::createForAdmin(
                'specimen_collected',
                'Specimen Collection Completed',
                "Phlebotomist {$phlebotomist->name} has completed specimen collection for {$patientName} ({$examinationType}). Tests: " . implode(', ', $completedTests),
                [
                    'checklist_id' => $medicalChecklist->id,
                    'patient_name' => $patientName,
                    'phlebotomist_name' => $phlebotomist->name,
                    'examination_type' => $data['examination_type'],
                    'completed_tests' => $completedTests,
                    'stool_exam_done' => !empty($data['stool_exam_done_by']),
                    'urinalysis_done' => !empty($data['urinalysis_done_by']),
                    'blood_extraction_done' => !empty($data['blood_extraction_done_by'])
                ],
                'medium',
                $phlebotomist,
                $medicalChecklist
            );
        }

        // Redirect based on examination type
        if ($data['examination_type'] === 'pre_employment') {
            return redirect()->route('plebo.pre-employment')->with('success', 'Medical checklist updated successfully.');
        } elseif ($data['examination_type'] === 'opd') {
            return redirect()->route('plebo.opd')->with('success', 'Medical checklist updated successfully.');
        } else {
            return redirect()->route('plebo.annual-physical')->with('success', 'Medical checklist updated successfully.');
        }
    }

    /**
     * Show test assignments for phlebotomist
     */
    public function testAssignments()
    {
        $assignments = AppointmentTestAssignment::with([
            'appointment.creator',
            'appointment.medicalTestCategory', 
            'medicalTest',
            'assignedToUser'
        ])
        ->where('staff_role', 'phlebotomist')
        ->where(function($query) {
            $query->where('assigned_to_user_id', Auth::id())
                  ->orWhereNull('assigned_to_user_id');
        })
        ->orderBy('assigned_at', 'desc')
        ->paginate(15);

        $stats = [
            'total' => AppointmentTestAssignment::where('staff_role', 'phlebotomist')->count(),
            'pending' => AppointmentTestAssignment::where('staff_role', 'phlebotomist')->where('status', 'pending')->count(),
            'in_progress' => AppointmentTestAssignment::where('staff_role', 'phlebotomist')->where('status', 'in_progress')->count(),
            'completed' => AppointmentTestAssignment::where('staff_role', 'phlebotomist')->where('status', 'completed')->count(),
        ];

        return view('plebo.test-assignments', compact('assignments', 'stats'));
    }

    /**
     * Update test assignment status
     */
    public function updateTestAssignmentStatus(Request $request, $id)
    {
        $assignment = AppointmentTestAssignment::findOrFail($id);
        
        // Ensure this assignment is for phlebotomist role
        if ($assignment->staff_role !== 'phlebotomist') {
            return redirect()->back()->with('error', 'Unauthorized access to this assignment.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'results' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $request->status,
            'assigned_to_user_id' => Auth::id(),
        ];

        if ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        }

        if ($request->filled('results')) {
            $updateData['results'] = $request->results;
        }

        if ($request->filled('notes')) {
            $updateData['special_notes'] = $request->notes;
        }

        $assignment->update($updateData);

        return redirect()->route('plebo.test-assignments')->with('success', 'Test assignment status updated successfully.');
    }
}


