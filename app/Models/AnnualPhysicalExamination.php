<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AnnualPhysicalExamination extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'name',
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
        'drug_test',
        'physical_findings',
        'lab_findings',
        'ecg',
        'ecg_date',
        'ecg_technician',
        'created_by',
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
        'drug_test' => 'array',
        'physical_findings' => 'array',
        'lab_findings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function drugTestResults(): HasMany
    {
        return $this->hasMany(DrugTestResult::class);
    }

    public function medicalChecklist()
    {
        return $this->hasOne(MedicalChecklist::class, 'annual_physical_examination_id');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) return $value;
                $patient = $this->patient;
                return $patient ? $patient->full_name : null;
            }
        );
    }

    /**
     * Calculate and update fitness assessment based on drug test, medical test, and physical examination results
     */
    public function calculateFitnessAssessment()
    {
        $lab = $this->lab_report ?? [];
        $drugTest = $this->drug_test ?? [];
        $physicalFindings = $this->physical_findings ?? [];
        
        // Count drug test positive results
        $drugPositiveCount = 0;
        
        // Since result fields are not being saved, use remarks to determine results
        $methRemarks = $drugTest['methamphetamine_remarks'] ?? '';
        $mariRemarks = $drugTest['marijuana_remarks'] ?? '';
        
        // Interpret remarks as results
        $methResult = '';
        if (in_array(strtolower(trim($methRemarks)), ['with findings', 'positive', 'abnormal', 'detected'])) {
            $methResult = 'Positive';
            $drugPositiveCount++;
        } elseif (in_array(strtolower(trim($methRemarks)), ['normal', 'negative', 'no findings', 'not detected'])) {
            $methResult = 'Negative';
        }
        
        $mariResult = '';
        if (in_array(strtolower(trim($mariRemarks)), ['with findings', 'positive', 'abnormal', 'detected'])) {
            $mariResult = 'Positive';
            $drugPositiveCount++;
        } elseif (in_array(strtolower(trim($mariRemarks)), ['normal', 'negative', 'no findings', 'not detected'])) {
            $mariResult = 'Negative';
        }
        
        // Count "Not normal" results from medical tests (excluding drug test)
        $medicalNotNormalCount = 0;
        $medicalTests = ['chest_x_ray', 'urinalysis', 'fecalysis', 'cbc', 'hbsag_screening', 'hepa_a_igg_igm', 'others'];
        
        foreach($medicalTests as $test) {
            $result = data_get($lab, $test, '');
            if (in_array(strtolower($result), ['not normal', 'abnormal', 'positive'])) {
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
        if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0 && $physicalNotNormalCount == 0) {
            $assessment = 'Fit to work';
        } elseif ($drugPositiveCount >= 1) {
            $assessment = 'Not fit for work';
        } elseif ($medicalNotNormalCount >= 2 || $physicalNotNormalCount >= 2 || ($medicalNotNormalCount >= 1 && $physicalNotNormalCount >= 1)) {
            $assessment = 'Not fit for work';
        } elseif ($medicalNotNormalCount == 1 || $physicalNotNormalCount == 1) {
            $assessment = 'For evaluation';
        } else {
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
        foreach($medicalTests as $test) {
            $result = data_get($lab, $test, '');
            if (in_array(strtolower($result), ['not normal', 'abnormal', 'positive'])) {
                $details['medical_results']['abnormal_tests'][] = [
                    'test' => $test,
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
            'physical_abnormal_count' => $physicalNotNormalCount,
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
