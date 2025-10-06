<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PreEmploymentExamination;

echo "=== Recalculating Assessment for Examination ID 3 ===\n\n";

$examination = PreEmploymentExamination::find(3);

if (!$examination) {
    echo "❌ Examination not found\n";
    exit;
}

echo "📋 Before Recalculation:\n";
echo "   Assessment: {$examination->fitness_assessment}\n";
echo "   Medical Abnormal Count: {$examination->medical_abnormal_count}\n";
echo "   Physical Abnormal Count: {$examination->physical_abnormal_count}\n";
echo "   Drug Positive Count: {$examination->drug_positive_count}\n\n";

echo "🧪 Checking fecalysis_result specifically:\n";
$fecalysisResult = data_get($examination->lab_report, 'fecalysis_result', 'NOT FOUND');
echo "   fecalysis_result: '{$fecalysisResult}'\n";
echo "   Is 'not normal'?: " . (strtolower(trim($fecalysisResult)) === 'not normal' ? 'YES' : 'NO') . "\n\n";

echo "🔄 Recalculating...\n";
$examination->calculateFitnessAssessment();
$examination->refresh();

echo "\n✅ After Recalculation:\n";
echo "   Assessment: {$examination->fitness_assessment}\n";
echo "   Medical Abnormal Count: {$examination->medical_abnormal_count}\n";
echo "   Physical Abnormal Count: {$examination->physical_abnormal_count}\n";
echo "   Drug Positive Count: {$examination->drug_positive_count}\n\n";

$assessmentDetails = json_decode($examination->assessment_details, true);
if (!empty($assessmentDetails['medical_results']['abnormal_tests'])) {
    echo "🚨 Abnormal Medical Tests Found:\n";
    foreach ($assessmentDetails['medical_results']['abnormal_tests'] as $test) {
        echo "   - {$test['test']}: {$test['result']}\n";
    }
} else {
    echo "ℹ️  No abnormal medical tests detected\n";
}

echo "\n=== Complete ===\n";
