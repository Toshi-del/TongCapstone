<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$exam = \App\Models\PreEmploymentExamination::find(1);

if ($exam) {
    echo "Has drug_test field: " . ($exam->drug_test ? 'YES' : 'NO') . "\n";
    
    if ($exam->drug_test && is_array($exam->drug_test)) {
        echo "\nDrug test keys: " . implode(', ', array_keys($exam->drug_test)) . "\n";
        
        echo "\nmethamphetamine_result: " . ($exam->drug_test['methamphetamine_result'] ?? 'NOT SET') . "\n";
        echo "marijuana_result: " . ($exam->drug_test['marijuana_result'] ?? 'NOT SET') . "\n";
    }
    
    // Try to recalculate
    echo "\n--- Recalculating assessment ---\n";
    $exam->calculateFitnessAssessment();
    $exam->refresh();
    
    echo "New fitness assessment: {$exam->fitness_assessment}\n";
    
    if ($exam->assessment_details) {
        $details = is_string($exam->assessment_details) ? json_decode($exam->assessment_details, true) : $exam->assessment_details;
        if (isset($details['drug_results'])) {
            echo "\nDrug results in assessment:\n";
            echo "  Methamphetamine: " . ($details['drug_results']['methamphetamine'] ?? 'NOT SET') . "\n";
            echo "  Marijuana: " . ($details['drug_results']['marijuana'] ?? 'NOT SET') . "\n";
        }
    }
}
