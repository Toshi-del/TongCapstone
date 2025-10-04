<?php
/**
 * Debug Ken Tuazon's examination specifically
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;

echo "=== Ken Tuazon Debug ===\n\n";

// Find Ken Tuazon's pre-employment examination
$preEmp = PreEmploymentExamination::where('name', 'LIKE', '%Ken Tuazon%')->first();

if ($preEmp) {
    echo "PRE-EMPLOYMENT EXAMINATION:\n";
    echo "ID: {$preEmp->id}\n";
    echo "Name: {$preEmp->name}\n";
    echo "Status: {$preEmp->status}\n";
    echo "Created: {$preEmp->created_at}\n";
    echo "Updated: {$preEmp->updated_at}\n\n";
    
    echo "RAW lab_findings from database:\n";
    $rawData = \DB::table('pre_employment_examinations')->where('id', $preEmp->id)->first();
    echo "Raw lab_findings field: " . $rawData->lab_findings . "\n\n";
    
    echo "PROCESSED lab_findings (after model casting):\n";
    echo "Type: " . gettype($preEmp->lab_findings) . "\n";
    if (is_array($preEmp->lab_findings)) {
        echo "Keys: " . implode(', ', array_keys($preEmp->lab_findings)) . "\n";
        echo "Full data: " . json_encode($preEmp->lab_findings, JSON_PRETTY_PRINT) . "\n";
        
        if (isset($preEmp->lab_findings['chest_xray'])) {
            echo "\nCHEST X-RAY DATA:\n";
            echo json_encode($preEmp->lab_findings['chest_xray'], JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "\nNO CHEST X-RAY DATA FOUND\n";
        }
    } else {
        echo "Not an array: " . json_encode($preEmp->lab_findings) . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    
    // Simulate what the DoctorController does
    echo "SIMULATING DOCTOR CONTROLLER:\n";
    $examination = PreEmploymentExamination::with([
        'preEmploymentRecord.medicalTest',
        'preEmploymentRecord.preEmploymentMedicalTests.medicalTest',
        'preEmploymentRecord.preEmploymentMedicalTests.medicalTestCategory',
        'preEmploymentRecord.drugTest',
        'drugTestResults'
    ])->findOrFail($preEmp->id);
    
    echo "After with() relationships:\n";
    echo "lab_findings type: " . gettype($examination->lab_findings) . "\n";
    if (is_array($examination->lab_findings)) {
        echo "Keys: " . implode(', ', array_keys($examination->lab_findings)) . "\n";
        $chestXrayData = $examination->lab_findings['chest_xray'] ?? null;
        echo "Chest X-ray data found: " . ($chestXrayData ? 'YES' : 'NO') . "\n";
        if ($chestXrayData) {
            echo "Result: " . ($chestXrayData['result'] ?? 'Not set') . "\n";
            echo "Finding: " . ($chestXrayData['finding'] ?? 'Not set') . "\n";
        }
    }
    
} else {
    echo "No pre-employment examination found for Ken Tuazon\n";
}

echo "\n=== Debug Complete ===\n";
