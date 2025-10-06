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
use App\Models\Message;
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
                // Only show records without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if ANY of the required fields are empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('stool_exam_done_by')
                                   ->orWhere('stool_exam_done_by', '')
                                   ->orWhereNull('urinalysis_done_by')
                                   ->orWhere('urinalysis_done_by', '')
                                   ->orWhereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
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
                // Only show records without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if ANY of the required fields are empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('stool_exam_done_by')
                                   ->orWhere('stool_exam_done_by', '')
                                   ->orWhereNull('urinalysis_done_by')
                                   ->orWhere('urinalysis_done_by', '')
                                   ->orWhereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
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
                // Only show patients without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if blood extraction is empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
                          });
                      });
            })
            ->whereDoesntHave('annualPhysicalExamination', function($q) {
                $q->whereIn('status', ['completed']);
            })
            ->latest()
            ->take(5)
            ->get();
            
        $patientCount = Patient::where('status', 'approved')
            ->where(function($query) {
                // Only show patients without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if blood extraction is empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
                          });
                      });
            })
            ->whereDoesntHave('annualPhysicalExamination', function($q) {
                $q->whereIn('status', ['completed']);
            })
            ->count();

        // Get OPD patients that don't have a medical checklist or have an incomplete one
        $opdPatients = User::where('role', 'opd')
            ->where(function($query) {
                // Only show patients without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if blood extraction is empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
                          });
                      });
            })
            ->whereDoesntHave('opdExamination', function($q) {
                $q->whereIn('status', ['completed']);
            })
            ->latest()
            ->take(5)
            ->get();
            
        $opdCount = User::where('role', 'opd')
            ->where(function($query) {
                // Only show patients without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if blood extraction is empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
                          });
                      });
            })
            ->whereDoesntHave('opdExamination', function($q) {
                $q->whereIn('status', ['completed']);
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
            // Records that need blood collection (phlebotomy focus - only blood_extraction_done_by required)
            $query->where(function($q) {
                $q->whereDoesntHave('medicalChecklist')
                  ->orWhereHas('medicalChecklist', function($subQ) {
                      // Incomplete if blood extraction is empty (phlebotomy focus)
                      $subQ->where(function($checkQ) {
                          $checkQ->whereNull('blood_extraction_done_by')
                                 ->orWhere('blood_extraction_done_by', '');
                      });
                  })
                  ->orWhereDoesntHave('preEmploymentExamination', function($subQ) {
                      // Also show if no examination exists yet or no lab_report
                      $subQ->whereNotNull('lab_report');
                  });
            });
        } elseif ($bloodStatus === 'collection_completed') {
            // Records where blood collection is completed AND lab_report exists (phlebotomy focus)
            $query->whereHas('medicalChecklist', function($q) {
                $q->whereNotNull('blood_extraction_done_by')
                  ->where('blood_extraction_done_by', '!=', '');
            })
            ->whereHas('preEmploymentExamination', function($q) {
                $q->whereNotNull('lab_report');
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
    public function annualPhysical(Request $request)
    {
        $query = Patient::where('status', 'approved');

        // Handle tab filtering
        $bloodStatus = $request->get('blood_status', 'needs_attention');
        
        if ($bloodStatus === 'needs_attention') {
            // Patients that need blood collection (phlebotomy focus - only blood_extraction_done_by required)
            $query->where(function($q) {
                $q->whereDoesntHave('medicalChecklist')
                  ->orWhereHas('medicalChecklist', function($subQ) {
                      // Incomplete if blood extraction is empty (phlebotomy focus)
                      $subQ->where(function($checkQ) {
                          $checkQ->whereNull('blood_extraction_done_by')
                                 ->orWhere('blood_extraction_done_by', '');
                      });
                  })
                  ->orWhereDoesntHave('annualPhysicalExamination', function($subQ) {
                      // Also show if no examination exists yet or no lab_report
                      $subQ->whereNotNull('lab_report');
                  });
            });
        } elseif ($bloodStatus === 'collection_completed') {
            // Patients where blood collection is completed AND lab_report exists (phlebotomy focus)
            $query->whereHas('medicalChecklist', function($q) {
                $q->whereNotNull('blood_extraction_done_by')
                  ->where('blood_extraction_done_by', '!=', '');
            })
            ->whereHas('annualPhysicalExamination', function($q) {
                $q->whereNotNull('lab_report');
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
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        $patients = $query->latest()->paginate(15);
            
        return view('plebo.annual-physical', compact('patients'));
    }

    /**
     * List OPD patients for plebo
     */
    public function opd()
    {
        $opdPatients = User::where('role', 'opd')
            ->where(function($query) {
                // Only show patients without checklist OR with incomplete checklist
                $query->whereDoesntHave('medicalChecklist')
                      ->orWhereHas('medicalChecklist', function($q) {
                          // Incomplete if blood extraction is empty
                          $q->where(function($subQ) {
                              $subQ->whereNull('blood_extraction_done_by')
                                   ->orWhere('blood_extraction_done_by', '');
                          });
                      });
            })
            ->whereDoesntHave('opdExamination', function($q) {
                $q->whereIn('status', ['completed']);
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

            // Check and update collection status after medical checklist save
            $this->checkAndUpdateCollectionStatus($medicalChecklist, $data);

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

        // Check and update collection status after medical checklist update
        $this->checkAndUpdateCollectionStatus($medicalChecklist, $data);

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

    /**
     * Show plebo messages view
     */
    public function messages()
    {
        return view('plebo.messages');
    }

    /**
     * Get users that plebo can chat with (admin and doctor only)
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
            // Get the last message between plebo user and this user
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
     * Fetch messages for the current plebo
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

        // Get messages between plebo user and the specific user
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
     * Send a message (plebo can only send to admin or doctor)
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
     * Get unread message count for the current plebo user.
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
     * Check and update collection status when medical checklist is completed
     * Based on phlebotomy-focused workflow: only blood_extraction_done_by is required
     */
    private function checkAndUpdateCollectionStatus($medicalChecklist, $data)
    {
        // Only proceed if blood extraction is completed (phlebotomy focus)
        if (empty($data['blood_extraction_done_by'])) {
            return;
        }

        if ($data['examination_type'] === 'pre_employment' && !empty($data['pre_employment_record_id'])) {
            // Handle pre-employment examination
            $record = PreEmploymentRecord::find($data['pre_employment_record_id']);
            if ($record) {
                // Find or create examination
                $exam = PreEmploymentExamination::where('pre_employment_record_id', $record->id)->first();
                
                if (!$exam) {
                    // Create new examination
                    $exam = PreEmploymentExamination::create([
                        'pre_employment_record_id' => $record->id,
                        'name' => $record->first_name . ' ' . $record->last_name,
                        'company_name' => $record->company_name,
                        'date' => now(),
                        'status' => 'collection_completed',
                        'lab_report' => [
                            'collection_completed_at' => now()->toDateTimeString(),
                            'blood_extraction_completed' => true,
                            'phlebotomist' => Auth::user()->name ?? 'Unknown'
                        ]
                    ]);
                } else {
                    // Update existing examination
                    $exam->status = 'collection_completed';
                    
                    // Ensure lab_report exists
                    $labReport = $exam->lab_report ?? [];
                    $labReport['collection_completed_at'] = now()->toDateTimeString();
                    $labReport['blood_extraction_completed'] = true;
                    $labReport['phlebotomist'] = Auth::user()->name ?? 'Unknown';
                    
                    $exam->lab_report = $labReport;
                    $exam->save();
                }
            }
        } elseif ($data['examination_type'] === 'annual_physical' && !empty($data['patient_id'])) {
            // Handle annual physical examination
            $patient = Patient::find($data['patient_id']);
            if ($patient) {
                // Find or create examination
                $exam = AnnualPhysicalExamination::where('patient_id', $patient->id)->first();
                
                if (!$exam) {
                    // Create new examination
                    $exam = AnnualPhysicalExamination::create([
                        'patient_id' => $patient->id,
                        'name' => $patient->first_name . ' ' . $patient->last_name,
                        'date' => now(),
                        'status' => 'collection_completed',
                        'lab_report' => [
                            'collection_completed_at' => now()->toDateTimeString(),
                            'blood_extraction_completed' => true,
                            'phlebotomist' => Auth::user()->name ?? 'Unknown'
                        ]
                    ]);
                } else {
                    // Update existing examination
                    $exam->status = 'collection_completed';
                    
                    // Ensure lab_report exists
                    $labReport = $exam->lab_report ?? [];
                    $labReport['collection_completed_at'] = now()->toDateTimeString();
                    $labReport['blood_extraction_completed'] = true;
                    $labReport['phlebotomist'] = Auth::user()->name ?? 'Unknown';
                    
                    $exam->lab_report = $labReport;
                    $exam->save();
                }
            }
        }
    }
}


