<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Medical Tests for Ken Tuazon ===\n\n";

// Pre-Employment
$preEmp = \App\Models\PreEmploymentExamination::with('preEmploymentRecord.preEmploymentMedicalTests.medicalTest')->where('name', 'LIKE', '%Ken Tuazon%')->first();
if($preEmp && $preEmp->preEmploymentRecord) {
    echo "PRE-EMPLOYMENT TESTS:\n";
    if($preEmp->preEmploymentRecord->preEmploymentMedicalTests) {
        foreach($preEmp->preEmploymentRecord->preEmploymentMedicalTests as $test) {
            if($test->medicalTest) {
                echo "- " . $test->medicalTest->name . "\n";
            }
        }
    }
}

// Annual Physical
$annual = \App\Models\AnnualPhysicalExamination::with('patient.appointment.selectedTests')->whereHas('patient', function($q) {
    $q->where('full_name', 'LIKE', '%Ken Tuazon%');
})->first();

if($annual && $annual->patient && $annual->patient->appointment) {
    echo "\nANNUAL PHYSICAL TESTS:\n";
    $selectedTests = $annual->patient->appointment->selected_tests ?? collect();
    foreach($selectedTests as $test) {
        echo "- " . ($test->name ?? 'Unknown') . "\n";
    }
}

echo "\n=== Complete ===\n";
