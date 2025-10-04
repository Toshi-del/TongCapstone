<?php
/**
 * Check what examinations appear in the doctor's pre-employment list
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PreEmploymentExamination;

echo "=== Doctor Pre-Employment List Check ===\n\n";

// Simulate what the DoctorController::preEmployment() method does
$preEmploymentExaminations = PreEmploymentExamination::with([
    'preEmploymentRecord.medicalTest', 
    'preEmploymentRecord.medicalTestCategory', 
    'user'
])
->whereIn('status', ['pending', 'completed', 'Approved', 'collection_completed'])
->latest()
->get();

echo "Found {$preEmploymentExaminations->count()} examinations in doctor's list\n\n";

foreach ($preEmploymentExaminations as $exam) {
    echo "ID: {$exam->id} | Name: {$exam->name} | Status: {$exam->status}\n";
    
    // Check if this has chest X-ray data
    $hasChestXray = false;
    if ($exam->lab_findings && is_array($exam->lab_findings) && isset($exam->lab_findings['chest_xray'])) {
        $hasChestXray = true;
        $result = $exam->lab_findings['chest_xray']['result'] ?? 'No result';
        echo "  ✓ Has chest X-ray data: {$result}\n";
    } else {
        echo "  ✗ No chest X-ray data\n";
    }
    
    // Show the URL that would be generated
    echo "  URL: /doctor/pre-employment/{$exam->id}/examination\n";
    echo "\n";
}

if ($preEmploymentExaminations->count() === 0) {
    echo "No examinations found in doctor's list!\n";
    echo "This means the status filtering might be excluding the records.\n\n";
    
    // Check all examinations regardless of status
    $allExams = PreEmploymentExamination::all();
    echo "All examinations (any status):\n";
    foreach ($allExams as $exam) {
        echo "ID: {$exam->id} | Name: {$exam->name} | Status: {$exam->status}\n";
    }
}

echo "\n=== Check Complete ===\n";
