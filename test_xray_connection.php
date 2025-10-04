<?php
/**
 * Test script to verify chest X-ray data connection between radiologist and doctor views
 * Run this script to check if the data flow is working correctly
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PreEmploymentExamination;
use App\Models\PreEmploymentRecord;

echo "=== Chest X-Ray Connection Test ===\n\n";

// Find examinations with lab_findings
$examinations = PreEmploymentExamination::whereNotNull('lab_findings')->get();

echo "Found " . $examinations->count() . " examinations with lab_findings\n\n";

foreach ($examinations as $exam) {
    echo "Examination ID: {$exam->id}\n";
    echo "Patient Name: {$exam->name}\n";
    echo "Status: {$exam->status}\n";
    
    if ($exam->lab_findings && is_array($exam->lab_findings)) {
        echo "Lab Findings Keys: " . implode(', ', array_keys($exam->lab_findings)) . "\n";
        
        // Check for chest X-ray data
        $chestXray = $exam->lab_findings['chest_xray'] ?? null;
        if ($chestXray) {
            echo "✓ Chest X-ray data found:\n";
            echo "  - Result: " . ($chestXray['result'] ?? 'Not set') . "\n";
            echo "  - Finding: " . ($chestXray['finding'] ?? 'Not set') . "\n";
            echo "  - Reviewed by: " . ($chestXray['reviewed_by'] ?? 'Not set') . "\n";
            echo "  - Reviewed at: " . ($chestXray['reviewed_at'] ?? 'Not set') . "\n";
            
            if (isset($chestXray['reviews']) && is_array($chestXray['reviews'])) {
                echo "  - Number of reviews: " . count($chestXray['reviews']) . "\n";
            }
        } else {
            echo "✗ No chest X-ray data found\n";
        }
    } else {
        echo "✗ No lab_findings or not an array\n";
        echo "Lab findings type: " . gettype($exam->lab_findings) . "\n";
        echo "Lab findings value: " . json_encode($exam->lab_findings) . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

if ($examinations->count() === 0) {
    echo "No examinations found with lab_findings.\n";
    echo "This could mean:\n";
    echo "1. No radiologist has submitted X-ray results yet\n";
    echo "2. There's an issue with data saving in the radiologist controller\n";
    echo "3. The examination records haven't been created yet\n\n";
    
    // Check for any examinations at all
    $totalExams = PreEmploymentExamination::count();
    echo "Total examinations in database: {$totalExams}\n";
    
    if ($totalExams > 0) {
        echo "\nSample examination data:\n";
        $sampleExam = PreEmploymentExamination::first();
        echo "ID: {$sampleExam->id}\n";
        echo "Name: {$sampleExam->name}\n";
        echo "Lab findings: " . json_encode($sampleExam->lab_findings) . "\n";
    }
}

echo "\n=== Test Complete ===\n";
