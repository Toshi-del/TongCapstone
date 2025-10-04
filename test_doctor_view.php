<?php
/**
 * Test script to simulate what happens when doctor views an examination
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PreEmploymentExamination;

echo "=== Doctor View Simulation Test ===\n\n";

// Test the examinations we know have chest X-ray data
$examinationIds = [1, 2]; // From our previous test

foreach ($examinationIds as $id) {
    echo "Testing Examination ID: {$id}\n";
    echo str_repeat("=", 40) . "\n";
    
    try {
        // Simulate what the DoctorController does
        $examination = PreEmploymentExamination::with([
            'preEmploymentRecord.medicalTest',
            'preEmploymentRecord.preEmploymentMedicalTests.medicalTest',
            'preEmploymentRecord.preEmploymentMedicalTests.medicalTestCategory',
            'preEmploymentRecord.drugTest',
            'drugTestResults'
        ])->findOrFail($id);
        
        echo "✓ Examination found\n";
        echo "Name: {$examination->name}\n";
        echo "Status: {$examination->status}\n";
        
        // Simulate the PHP code in the blade template
        $chestXrayData = null;
        $xrayImage = null;
        
        echo "\nLab Findings Analysis:\n";
        echo "- lab_findings exists: " . ($examination->lab_findings ? 'YES' : 'NO') . "\n";
        echo "- lab_findings type: " . gettype($examination->lab_findings) . "\n";
        echo "- is_array: " . (is_array($examination->lab_findings) ? 'YES' : 'NO') . "\n";
        
        if ($examination->lab_findings && is_array($examination->lab_findings)) {
            echo "- Available keys: " . implode(', ', array_keys($examination->lab_findings)) . "\n";
            
            // Try both possible keys
            $chestXrayData = $examination->lab_findings['chest_xray'] ?? ($examination->lab_findings['Chest X-Ray'] ?? null);
            
            echo "- chest_xray key exists: " . (isset($examination->lab_findings['chest_xray']) ? 'YES' : 'NO') . "\n";
            echo "- Chest X-Ray key exists: " . (isset($examination->lab_findings['Chest X-Ray']) ? 'YES' : 'NO') . "\n";
            echo "- chestXrayData result: " . ($chestXrayData ? 'FOUND' : 'NOT FOUND') . "\n";
            
            if ($chestXrayData) {
                echo "\nChest X-ray Data Structure:\n";
                echo json_encode($chestXrayData, JSON_PRETTY_PRINT) . "\n";
                
                echo "\nWhat doctor view would show:\n";
                echo "- Result: " . ($chestXrayData['result'] ?? '—') . "\n";
                echo "- Finding: " . ($chestXrayData['finding'] ?? '—') . "\n";
                echo "- Reviewed by: " . ($chestXrayData['reviewed_by'] ?? 'Not set') . "\n";
                echo "- Reviewed at: " . ($chestXrayData['reviewed_at'] ?? 'Not set') . "\n";
            }
        } else {
            echo "- Raw lab_findings value: " . json_encode($examination->lab_findings) . "\n";
        }
        
        // Check for X-ray image
        echo "\nX-ray Image Check:\n";
        if ($examination->preEmploymentRecord) {
            echo "- Pre-employment record exists: YES\n";
            echo "- Record ID: {$examination->preEmploymentRecord->id}\n";
            
            $xrayChecklist = \App\Models\MedicalChecklist::where('pre_employment_record_id', $examination->preEmploymentRecord->id)
                ->whereNotNull('xray_image_path')
                ->latest('date')
                ->first();
                
            echo "- X-ray checklist found: " . ($xrayChecklist ? 'YES' : 'NO') . "\n";
            if ($xrayChecklist) {
                echo "- X-ray image path: {$xrayChecklist->xray_image_path}\n";
            }
        } else {
            echo "- Pre-employment record exists: NO\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== Test Complete ===\n";
