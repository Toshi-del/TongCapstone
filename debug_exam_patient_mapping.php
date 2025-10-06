<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Patient;
use App\Models\AnnualPhysicalExamination;

echo "=== Patient to Examination Mapping Debug ===\n\n";

// Get all patients
$patients = Patient::where('status', 'approved')->get();

echo "All Approved Patients:\n";
foreach ($patients as $patient) {
    echo "Patient ID: {$patient->id} - Name: {$patient->full_name}\n";
}

echo "\nAll Annual Physical Examinations:\n";
$examinations = AnnualPhysicalExamination::all();
foreach ($examinations as $exam) {
    echo "Exam ID: {$exam->id} - Patient ID: {$exam->patient_id} - Name: {$exam->name}\n";
}

echo "\nPatient-Examination Mapping:\n";
foreach ($patients as $patient) {
    $exam = AnnualPhysicalExamination::where('patient_id', $patient->id)->first();
    if ($exam) {
        echo "✓ Patient ID {$patient->id} ({$patient->full_name}) -> Exam ID {$exam->id} ({$exam->name})\n";
        if ($patient->full_name !== $exam->name) {
            echo "  ⚠️  NAME MISMATCH! Patient: '{$patient->full_name}' vs Exam: '{$exam->name}'\n";
        }
    } else {
        echo "✗ Patient ID {$patient->id} ({$patient->full_name}) -> No examination found\n";
    }
}

echo "\nChecking specific issue - Aki Nakagawa (Patient ID 6):\n";
$aki = Patient::find(6);
if ($aki) {
    echo "Patient: {$aki->full_name} (ID: {$aki->id})\n";
    $akiExam = AnnualPhysicalExamination::where('patient_id', 6)->first();
    if ($akiExam) {
        echo "Examination: {$akiExam->name} (Exam ID: {$akiExam->id}, Patient ID: {$akiExam->patient_id})\n";
        echo "Edit URL should be: /nurse/annual-physical/{$akiExam->id}/edit\n";
    } else {
        echo "No examination found for Aki\n";
    }
}

echo "\nChecking Pol pelayo (Patient ID 1):\n";
$pol = Patient::find(1);
if ($pol) {
    echo "Patient: {$pol->full_name} (ID: {$pol->id})\n";
    $polExam = AnnualPhysicalExamination::where('patient_id', 1)->first();
    if ($polExam) {
        echo "Examination: {$polExam->name} (Exam ID: {$polExam->id}, Patient ID: {$polExam->patient_id})\n";
    } else {
        echo "No examination found for Pol\n";
    }
}
