<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdExamination extends Model
{
    use HasFactory;

    protected $table = 'opd_examinations';

    protected $fillable = [
        'user_id',
        'name',
        'date',
        'status',
        'illness_history',
        'accidents_operations',
        'past_medical_history',
        'family_history',
        'personal_habits',
        'physical_exam',
        'physical_findings',
        'lab_findings',
        'ecg',
        'skin_marks',
        'visual',
        'ishihara_test',
        'findings',
        'lab_report',
        'fitness_assessment',
        'drug_positive_count',
        'medical_abnormal_count',
        'physical_abnormal_count',
    ];

    protected $casts = [
        'date' => 'date',
        'family_history' => 'array',
        'personal_habits' => 'array',
        'physical_exam' => 'array',
        'physical_findings' => 'array',
        'lab_findings' => 'array',
        'lab_report' => 'array',
    ];

    /**
     * Get the user that owns the OPD examination
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('M d, Y') : null;
    }

    /**
     * Scope for pending examinations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed examinations
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for approved examinations
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Get drug test result for this OPD examination
     */
    public function drugTestResult()
    {
        return $this->hasOne(\App\Models\DrugTestResult::class);
    }

    /**
     * Calculate fitness assessment based on examination results
     */
    public function calculateFitnessAssessment()
    {
        $drugPositiveCount = 0;
        $medicalAbnormalCount = 0;
        $physicalAbnormalCount = 0;
        $abnormalDetails = [];

        // Check drug test results
        $drugTestResult = $this->drugTestResult;
        if ($drugTestResult) {
            $drugTests = ['marijuana', 'cocaine', 'opiates', 'amphetamine', 'methamphetamine'];
            foreach ($drugTests as $drug) {
                if (isset($drugTestResult->{$drug}) && strtolower($drugTestResult->{$drug}) === 'positive') {
                    $drugPositiveCount++;
                    $abnormalDetails[] = ucfirst($drug) . ': Positive';
                }
            }
        }

        // Check laboratory findings (prioritize lab_findings over lab_report for current data)
        $labFindings = $this->lab_findings ?? [];
        $labReport = $this->lab_report ?? [];
        
        $medicalTests = ['cbc', 'urinalysis', 'fecalysis', 'chest_xray', 'sodium', 'potassium', 'creatinine', 'bun', 'glucose', 'cholesterol'];
        
        foreach ($medicalTests as $test) {
            $result = null;
            
            // Check lab_findings first (current structure)
            if (isset($labFindings[$test]['result'])) {
                $result = $labFindings[$test]['result'];
            }
            // Fallback to lab_report (legacy structure)
            elseif (isset($labReport[$test])) {
                $result = $labReport[$test];
            }
            // Check legacy field names with _result suffix
            elseif (isset($labReport[$test . '_result'])) {
                $result = $labReport[$test . '_result'];
            }
            
            if ($result && strtolower($result) === 'not normal') {
                $medicalAbnormalCount++;
                $abnormalDetails[] = ucwords(str_replace('_', ' ', $test)) . ': Not Normal';
            }
        }

        // Check physical examination findings
        $physicalFindings = $this->physical_findings ?? [];
        foreach ($physicalFindings as $system => $finding) {
            if (isset($finding['result']) && strtolower($finding['result']) === 'not normal') {
                $physicalAbnormalCount++;
                $abnormalDetails[] = ucwords(str_replace('_', ' ', $system)) . ': Not Normal';
            }
        }

        // Determine fitness assessment based on rules
        $assessment = 'For evaluation'; // Default

        if ($drugPositiveCount == 0 && $medicalAbnormalCount == 0 && $physicalAbnormalCount == 0) {
            $assessment = 'Fit to work';
        } elseif ($drugPositiveCount >= 1) {
            $assessment = 'Not fit for work';
        } elseif ($medicalAbnormalCount >= 2) {
            $assessment = 'Not fit for work';
        } elseif ($physicalAbnormalCount >= 2) {
            $assessment = 'Not fit for work';
        } elseif ($medicalAbnormalCount >= 1 && $physicalAbnormalCount >= 1) {
            $assessment = 'Not fit for work';
        } elseif ($medicalAbnormalCount == 1 || $physicalAbnormalCount == 1) {
            $assessment = 'For evaluation';
        }

        // Update the model with calculated values
        $this->update([
            'fitness_assessment' => $assessment,
            'drug_positive_count' => $drugPositiveCount,
            'medical_abnormal_count' => $medicalAbnormalCount,
            'physical_abnormal_count' => $physicalAbnormalCount,
        ]);

        return [
            'assessment' => $assessment,
            'drug_positive_count' => $drugPositiveCount,
            'medical_abnormal_count' => $medicalAbnormalCount,
            'physical_abnormal_count' => $physicalAbnormalCount,
            'abnormal_details' => $abnormalDetails,
        ];
    }
}
