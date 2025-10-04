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

    public function preEmploymentRecord(): BelongsTo
    {
        return $this->belongsTo(PreEmploymentRecord::class);
    }

    public function drugTestResults(): HasMany
    {
        return $this->hasMany(DrugTestResult::class);
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
        $drugTest = $this->drug_test ?? [];
        
        // Count drug test positive results
        $drugPositiveCount = 0;
        $methResult = $drugTest['methamphetamine_result'] ?? '';
        $mariResult = $drugTest['marijuana_result'] ?? '';
        
        if (strtolower($methResult) === 'positive') {
            $drugPositiveCount++;
        }
        if (strtolower($mariResult) === 'positive') {
            $drugPositiveCount++;
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
        
        // Determine assessment based on drug test and medical test combination
        if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0) {
            $assessment = 'Fit to work';
        } elseif ($drugPositiveCount == 0 && $medicalNotNormalCount >= 2) {
            $assessment = 'Not fit for work';
        } elseif ($drugPositiveCount == 0 && $medicalNotNormalCount == 1) {
            $assessment = 'For evaluation';
        } elseif ($drugPositiveCount >= 1) {
            $assessment = 'Not fit for work';
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
            'applied_rule' => $this->getAppliedRule($drugPositiveCount, $medicalNotNormalCount),
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
        
        // Update the record
        $this->update([
            'fitness_assessment' => $assessment,
            'drug_positive_count' => $drugPositiveCount,
            'medical_abnormal_count' => $medicalNotNormalCount,
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
    private function getAppliedRule($drugPositiveCount, $medicalNotNormalCount)
    {
        if ($drugPositiveCount == 0 && $medicalNotNormalCount == 0) {
            return 'All Negative, All Normal → Fit to Work';
        } elseif ($drugPositiveCount == 0 && $medicalNotNormalCount >= 2) {
            return 'All Negative, 2+ Abnormal → Not Fit';
        } elseif ($drugPositiveCount == 0 && $medicalNotNormalCount == 1) {
            return 'All Negative, 1 Abnormal → For Evaluation';
        } elseif ($drugPositiveCount >= 1) {
            return 'Any Positive Drug Test → Not Fit';
        } else {
            return 'Fallback → For Evaluation';
        }
    }
}
