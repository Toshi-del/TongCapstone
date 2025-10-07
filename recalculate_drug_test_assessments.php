<?php

/**
 * Script to recalculate fitness assessments for examinations with drug test results
 * This fixes the issue where drug test results were not being properly read from
 * methamphetamine_result and marijuana_result fields
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Recalculating Drug Test Assessments ===\n\n";

// Recalculate Pre-Employment Examinations
echo "Processing Pre-Employment Examinations...\n";
$preEmploymentExams = \App\Models\PreEmploymentExamination::whereNotNull('drug_test')->get();
$preEmploymentCount = 0;

foreach ($preEmploymentExams as $exam) {
    $drugTest = $exam->drug_test;
    if (is_array($drugTest) && (isset($drugTest['methamphetamine_result']) || isset($drugTest['marijuana_result']))) {
        echo "  - Recalculating Exam ID: {$exam->id} ({$exam->name})\n";
        $exam->calculateFitnessAssessment();
        $preEmploymentCount++;
    }
}

echo "✓ Recalculated {$preEmploymentCount} pre-employment examinations\n\n";

// Recalculate Annual Physical Examinations
echo "Processing Annual Physical Examinations...\n";
$annualPhysicalExams = \App\Models\AnnualPhysicalExamination::whereNotNull('drug_test')->get();
$annualPhysicalCount = 0;

foreach ($annualPhysicalExams as $exam) {
    $drugTest = $exam->drug_test;
    if (is_array($drugTest) && (isset($drugTest['methamphetamine_result']) || isset($drugTest['marijuana_result']))) {
        $patientName = $exam->patient ? $exam->patient->full_name : 'Unknown';
        echo "  - Recalculating Exam ID: {$exam->id} (Patient: {$patientName})\n";
        $exam->calculateFitnessAssessment();
        $annualPhysicalCount++;
    }
}

echo "✓ Recalculated {$annualPhysicalCount} annual physical examinations\n\n";

echo "=== Complete ===\n";
echo "Total examinations recalculated: " . ($preEmploymentCount + $annualPhysicalCount) . "\n";
