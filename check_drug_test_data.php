<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Drug Test Data for Examination ID 1 ===\n\n";

// Check Pre-Employment Examination ID 1
$exam = \App\Models\PreEmploymentExamination::find(1);

if ($exam) {
    echo "Pre-Employment Examination ID: 1\n";
    echo "Patient Name: {$exam->name}\n";
    echo "Fitness Assessment: {$exam->fitness_assessment}\n\n";
    
    echo "Drug Test Data:\n";
    if ($exam->drug_test) {
        echo json_encode($exam->drug_test, JSON_PRETTY_PRINT) . "\n\n";
    } else {
        echo "  No drug test data found\n\n";
    }
    
    echo "Assessment Details:\n";
    if ($exam->assessment_details) {
        if (is_string($exam->assessment_details)) {
            $details = json_decode($exam->assessment_details, true);
            echo json_encode($details, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo json_encode($exam->assessment_details, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "  No assessment details found\n";
    }
} else {
    echo "Pre-Employment Examination ID 1 not found\n";
}
