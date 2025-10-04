<?php
/**
 * Test both pre-employment and annual physical examinations for chest X-ray data
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;

echo "=== Both Examinations Chest X-Ray Test ===\n\n";

echo "PRE-EMPLOYMENT EXAMINATIONS:\n";
echo str_repeat("=", 40) . "\n";

$preEmploymentExams = PreEmploymentExamination::all();
echo "Total Pre-Employment Examinations: " . $preEmploymentExams->count() . "\n\n";

foreach ($preEmploymentExams as $exam) {
    echo "ID: {$exam->id} | Name: {$exam->name} | Status: {$exam->status}\n";
    
    if ($exam->lab_findings && is_array($exam->lab_findings)) {
        echo "  Lab Findings Keys: " . implode(', ', array_keys($exam->lab_findings)) . "\n";
        
        if (isset($exam->lab_findings['chest_xray'])) {
            $xray = $exam->lab_findings['chest_xray'];
            echo "  ✓ Chest X-ray: " . ($xray['result'] ?? 'No result') . " | " . ($xray['finding'] ?? 'No finding') . "\n";
            echo "  URL: /doctor/pre-employment/{$exam->id}/examination\n";
        } else {
            echo "  ✗ No chest X-ray data\n";
        }
    } else {
        echo "  ✗ No lab_findings\n";
    }
    echo "\n";
}

echo "\nANNUAL PHYSICAL EXAMINATIONS:\n";
echo str_repeat("=", 40) . "\n";

$annualPhysicalExams = AnnualPhysicalExamination::all();
echo "Total Annual Physical Examinations: " . $annualPhysicalExams->count() . "\n\n";

foreach ($annualPhysicalExams as $exam) {
    $patientName = $exam->patient ? ($exam->patient->full_name ?? $exam->patient->first_name . ' ' . $exam->patient->last_name) : 'Unknown';
    echo "ID: {$exam->id} | Name: {$patientName} | Status: {$exam->status}\n";
    
    if ($exam->lab_findings && is_array($exam->lab_findings)) {
        echo "  Lab Findings Keys: " . implode(', ', array_keys($exam->lab_findings)) . "\n";
        
        if (isset($exam->lab_findings['chest_xray'])) {
            $xray = $exam->lab_findings['chest_xray'];
            echo "  ✓ Chest X-ray: " . ($xray['result'] ?? 'No result') . " | " . ($xray['finding'] ?? 'No finding') . "\n";
            echo "  URL: /doctor/annual-physical/{$exam->id}/examination\n";
        } else {
            echo "  ✗ No chest X-ray data\n";
        }
    } else {
        echo "  ✗ No lab_findings\n";
    }
    echo "\n";
}

echo "\nSUMMARY:\n";
echo str_repeat("=", 40) . "\n";
echo "Pre-Employment with X-ray data: " . $preEmploymentExams->filter(function($e) { 
    return $e->lab_findings && is_array($e->lab_findings) && isset($e->lab_findings['chest_xray']); 
})->count() . "\n";

echo "Annual Physical with X-ray data: " . $annualPhysicalExams->filter(function($e) { 
    return $e->lab_findings && is_array($e->lab_findings) && isset($e->lab_findings['chest_xray']); 
})->count() . "\n";

echo "\n=== Test Complete ===\n";
