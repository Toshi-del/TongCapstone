<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PreEmploymentExamination;

$examination = PreEmploymentExamination::find(3);

if ($examination) {
    echo "✅ VERIFICATION RESULTS:\n\n";
    echo "📋 Examination ID: {$examination->id}\n";
    echo "🏥 Assessment: {$examination->fitness_assessment}\n";
    echo "📊 Medical Abnormal Count: {$examination->medical_abnormal_count}\n";
    echo "📊 Physical Abnormal Count: {$examination->physical_abnormal_count}\n";
    echo "📊 Drug Positive Count: {$examination->drug_positive_count}\n\n";
    
    $assessmentDetails = json_decode($examination->assessment_details, true);
    if ($assessmentDetails) {
        echo "📝 Applied Rule: " . ($assessmentDetails['applied_rule'] ?? 'N/A') . "\n\n";
        
        if (!empty($assessmentDetails['medical_results']['abnormal_tests'])) {
            echo "🚨 Abnormal Medical Tests:\n";
            foreach ($assessmentDetails['medical_results']['abnormal_tests'] as $test) {
                echo "   - " . str_replace('_result', '', $test['test']) . ": {$test['result']}\n";
            }
        }
    }
    
    echo "\n" . ($examination->fitness_assessment === 'For evaluation' ? "✅ SUCCESS: Assessment correctly shows 'For evaluation'" : "❌ ISSUE: Assessment should be 'For evaluation'") . "\n";
    echo ($examination->medical_abnormal_count == 1 ? "✅ SUCCESS: Medical abnormal count is 1" : "❌ ISSUE: Medical abnormal count should be 1") . "\n";
} else {
    echo "❌ Examination not found\n";
}
