<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PreEmploymentExamination extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'name',
        'company_name',
        'date',
        'status',
        'illness_history',
        'accidents_operations',
        'past_medical_history',
        'family_history',
        'personal_habits',
        'physical_exam',
        'skin_marks',
        'visual',
        'ishihara_test',
        'findings',
        'lab_report',
        'pre_employment_record_id',
        'physical_findings',
        'lab_findings',
        'ecg',
        'ecg_date',
        'ecg_technician',
        'created_by',
        'drug_test',
        'fitness_assessment',
        'drug_positive_count',
        'medical_abnormal_count',
        'physical_abnormal_count',
        'assessment_details',
    ];

    protected $casts = [
        'date' => 'date',
        'family_history' => 'array',
        'personal_habits' => 'array',
        'physical_exam' => 'array',
        'lab_report' => 'array',
        'physical_findings' => 'array',
        'lab_findings' => 'array',
        'drug_test' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function preEmploymentRecord(): BelongsTo
    {
        return $this->belongsTo(PreEmploymentRecord::class);
    }

    public function drugTestResults(): HasMany
    {
        return $this->hasMany(DrugTestResult::class);
    }

    /**
     * Get the total price from the appointment via patient relationship
     */
    public function getTotalPriceFromAppointment(): ?float
    {
        // Get the patient associated with this examination
        $patient = $this->patient;
        
        if (!$patient) {
            return null;
        }
        
        // Get the appointment associated with the patient
        $appointment = $patient->appointment;
        
        if (!$appointment) {
            return null;
        }
        
        // Return the total price from the appointment
        return $appointment->total_price;
    }

    /**
     * Get formatted total price from appointment
     */
    public function getFormattedTotalPriceFromAppointment(): string
    {
        $totalPrice = $this->getTotalPriceFromAppointment();
        
        if ($totalPrice === null) {
            return 'N/A';
        }
        
        return '₱' . number_format($totalPrice, 2);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) return $value;
                $record = $this->preEmploymentRecord;
                return $record ? ($record->first_name . ' ' . $record->last_name) : null;
            }
        );
    }

    protected function companyName(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) return $value;
                $record = $this->preEmploymentRecord;
                return $record ? $record->company_name : null;
            }
        );
    }

    /**
     * Calculate and update fitness assessment based on drug test and medical test results
     */
    public function calculateFitnessAssessment()
    {
        $lab = $this->lab_report ?? [];
        $labFindings = $this->lab_findings ?? [];
        $drugTest = $this->drug_test ?? [];
        $physicalFindings = $this->physical_findings ?? [];
        
        // Count drug test positive results
        $drugPositiveCount = 0;
        
        // Get actual result fields (methamphetamine_result and marijuana_result)
        $methResult = $drugTest['methamphetamine_result'] ?? '';
        $mariResult = $drugTest['marijuana_result'] ?? '';
        
        // Count positive results
        if (strtolower(trim($methResult)) === 'positive') {
            $drugPositiveCount++;
        }
        
        if (strtolower(trim($mariResult)) === 'positive') {
            $drugPositiveCount++;
        }
        
        // Count "Not normal" results from medical tests (excluding drug test)
        // Check both lab_report (legacy) and lab_findings (current Laboratory Test Results section)
        $medicalNotNormalCount = 0;
        
        // Laboratory Test Results section stores data in lab_report with _result suffix
        $labTestFields = [
            'cbc' => 'CBC',
            'urinalysis' => 'Urinalysis', 
            'fecalysis' => 'Fecalysis',
            'sodium' => 'Sodium',
            'potassium' => 'Potassium',
            'fbs' => 'FBS',
            'bua' => 'BUA',
            'cholesterol' => 'Cholesterol',
            'creatinine' => 'Creatinine',
            'hbsag_screening' => 'HBsAg Screening',
            'hepa_a_igg_igm' => 'HEPA A IGG & IGM',
            'xray' => 'Chest X-Ray' // Note: chest_x_ray becomes 'xray' in the view
        ];
        
        foreach($labTestFields as $fieldKey => $displayName) {
            $result = '';
            
            // Check lab_findings first (for radiologist data like chest x-ray)
            if ($fieldKey === 'xray') {
                $result = data_get($labFindings, 'chest_xray.result', '');
            } else {
                $result = data_get($labFindings, $fieldKey . '.result', '');
            }
            
            // If not found in lab_findings, check lab_report with _result suffix (current Laboratory Test Results section)
            if (empty($result)) {
                $result = data_get($lab, $fieldKey . '_result', '') ?: data_get($lab, $fieldKey, '');
            }
            
            if (in_array(strtolower(trim($result)), ['not normal', 'abnormal', 'positive'])) {
                $medicalNotNormalCount++;
            }
        }
        
        // Count abnormal physical examination findings
        $physicalNotNormalCount = 0;
        $physicalExaminations = ['Neck', 'Chest-Breast Axilla', 'Lungs', 'Heart', 'Abdomen', 'Extremities', 'Anus-Rectum', 'GUT', 'Inguinal / Genital'];
        
        foreach($physicalExaminations as $exam) {
            $result = data_get($physicalFindings, $exam . '.result', '');
            if (strtolower(trim($result)) === 'not normal') {
                $physicalNotNormalCount++;
            }
        }
        
        // Determine assessment based on drug test, medical test, and physical examination combination
        // Enhanced logic including physical examination findings
        if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0 && $physicalNotNormalCount == 0) {
            // All negative, all normal, no physical abnormalities - Fit to Work
            $assessment = 'Fit to work';
        } elseif ($drugPositiveCount >= 1) {
            // Any positive drug test - Not Fit (regardless of other findings)
            $assessment = 'Not fit for work';
        } elseif ($medicalNotNormalCount >= 2 || $physicalNotNormalCount >= 2 || ($medicalNotNormalCount >= 1 && $physicalNotNormalCount >= 1)) {
            // 2+ medical abnormal OR 2+ physical abnormal OR 1+ medical + 1+ physical - Not Fit
            $assessment = 'Not fit for work';
        } elseif ($medicalNotNormalCount == 1 || $physicalNotNormalCount == 1) {
            // Only 1 abnormal finding (either medical or physical) - For Evaluation
            $assessment = 'For evaluation';
        } else {
            // Fallback
            $assessment = 'For evaluation';
        }
        
        // Create assessment details
        $details = [
            'drug_results' => [
                'methamphetamine' => $methResult,
                'marijuana' => $mariResult,
                'positive_count' => $drugPositiveCount
            ],
            'medical_results' => [
                'abnormal_count' => $medicalNotNormalCount,
                'abnormal_tests' => []
            ],
            'physical_results' => [
                'abnormal_count' => $physicalNotNormalCount,
                'abnormal_examinations' => []
            ],
            'applied_rule' => $this->getAppliedRule($drugPositiveCount, $medicalNotNormalCount, $physicalNotNormalCount),
            'calculated_at' => now()->toISOString()
        ];
        
        // Add abnormal test details
        foreach($labTestFields as $fieldKey => $displayName) {
            $result = '';
            
            // Check lab_findings first (for radiologist data like chest x-ray)
            if ($fieldKey === 'xray') {
                $result = data_get($labFindings, 'chest_xray.result', '');
            } else {
                $result = data_get($labFindings, $fieldKey . '.result', '');
            }
            
            // If not found in lab_findings, check lab_report with _result suffix (current Laboratory Test Results section)
            if (empty($result)) {
                $result = data_get($lab, $fieldKey . '_result', '') ?: data_get($lab, $fieldKey, '');
            }
            
            if (in_array(strtolower(trim($result)), ['not normal', 'abnormal', 'positive'])) {
                $details['medical_results']['abnormal_tests'][] = [
                    'test' => $displayName,
                    'result' => $result
                ];
            }
        }
        
        // Add abnormal physical examination details
        foreach($physicalExaminations as $exam) {
            $result = data_get($physicalFindings, $exam . '.result', '');
            if (in_array(strtolower(trim($result)), ['not normal', 'abnormal', 'positive', 'abnormal findings', 'with findings'])) {
                $details['physical_results']['abnormal_examinations'][] = [
                    'examination' => $exam,
                    'result' => $result,
                    'findings' => data_get($physicalFindings, $exam . '.findings', '')
                ];
            }
        }
        
        // Update the record
        $this->update([
            'fitness_assessment' => $assessment,
            'drug_positive_count' => $drugPositiveCount,
            'medical_abnormal_count' => $medicalNotNormalCount,
            'physical_abnormal_count' => $physicalNotNormalCount,
            'assessment_details' => json_encode($details)
        ]);
        
        return [
            'assessment' => $assessment,
            'drug_positive_count' => $drugPositiveCount,
            'medical_abnormal_count' => $medicalNotNormalCount,
            'details' => $details
        ];
    }
    
    /**
     * Get the rule that was applied for the assessment
     */
    private function getAppliedRule($drugPositiveCount, $medicalNotNormalCount, $physicalNotNormalCount)
    {
        if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0 && $physicalNotNormalCount == 0) {
            return 'All Negative, All Normal, No Physical Abnormalities → Fit to Work';
        } elseif ($drugPositiveCount >= 1) {
            return 'Any Positive Drug Test → Not Fit';
        } elseif ($medicalNotNormalCount >= 2) {
            return '2+ Medical Abnormal → Not Fit';
        } elseif ($physicalNotNormalCount >= 2) {
            return '2+ Physical Abnormal → Not Fit';
        } elseif ($medicalNotNormalCount >= 1 && $physicalNotNormalCount >= 1) {
            return '1+ Medical + 1+ Physical Abnormal → Not Fit';
        } elseif ($medicalNotNormalCount == 1) {
            return '1 Medical Abnormal → For Evaluation';
        } elseif ($physicalNotNormalCount == 1) {
            return '1 Physical Abnormal → For Evaluation';
        } else {
            return 'Fallback → For Evaluation';
        }
    }
}
