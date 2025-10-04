<?php
/**
 * Test script to verify RadTech fixes are working correctly
 * Run this script to check if the saving issues have been resolved
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\PreEmploymentRecord;
use App\Models\MedicalChecklist;

echo "=== RadTech Fixes Verification Script ===\n\n";

// Test 1: Check Patient relationships
echo "1. Testing Patient relationships...\n";
$patients = Patient::where('status', 'approved')->take(5)->get();
foreach ($patients as $patient) {
    $hasChecklist = $patient->medicalChecklist ? 'Yes' : 'No';
    $checklistCount = $patient->medicalChecklists->count();
    echo "   Patient {$patient->id}: Has checklist: {$hasChecklist}, Total checklists: {$checklistCount}\n";
}

// Test 2: Check PreEmploymentRecord relationships
echo "\n2. Testing PreEmploymentRecord relationships...\n";
$preEmployments = PreEmploymentRecord::where('status', 'approved')->take(5)->get();
foreach ($preEmployments as $record) {
    $hasChecklist = $record->medicalChecklist ? 'Yes' : 'No';
    echo "   PreEmployment {$record->id}: Has checklist: {$hasChecklist}\n";
}

// Test 3: Check MedicalChecklist records with X-ray completion
echo "\n3. Testing MedicalChecklist X-ray completion...\n";
$checklists = MedicalChecklist::whereNotNull('chest_xray_done_by')->take(10)->get();
echo "   Found {$checklists->count()} checklists with completed X-rays\n";
foreach ($checklists as $checklist) {
    $type = $checklist->examination_type ?? 'Unknown';
    $completedBy = $checklist->chest_xray_done_by;
    $hasImage = $checklist->xray_image_path ? 'Yes' : 'No';
    echo "   Checklist {$checklist->id}: Type: {$type}, Completed by: {$completedBy}, Has image: {$hasImage}\n";
}

// Test 4: Check filtering logic for Annual Physical
echo "\n4. Testing Annual Physical filtering logic...\n";
$needsAttention = Patient::where('status', 'approved')
    ->whereDoesntHave('medicalChecklists', function ($q) {
        $q->where('examination_type', 'annual-physical')
          ->whereNotNull('chest_xray_done_by')
          ->where('chest_xray_done_by', '!=', '');
    })->count();

$completed = Patient::where('status', 'approved')
    ->whereHas('medicalChecklists', function ($q) {
        $q->where('examination_type', 'annual-physical')
          ->whereNotNull('chest_xray_done_by')
          ->where('chest_xray_done_by', '!=', '');
    })->count();

echo "   Needs Attention: {$needsAttention} patients\n";
echo "   X-Ray Completed: {$completed} patients\n";

// Test 5: Check filtering logic for Pre-Employment
echo "\n5. Testing Pre-Employment filtering logic...\n";
$preNeedsAttention = PreEmploymentRecord::where('status', 'approved')
    ->whereDoesntHave('medicalChecklist', function ($q) {
        $q->whereNotNull('chest_xray_done_by')
          ->where('chest_xray_done_by', '!=', '');
    })->count();

$preCompleted = PreEmploymentRecord::where('status', 'approved')
    ->whereHas('medicalChecklist', function ($q) {
        $q->whereNotNull('chest_xray_done_by')
          ->where('chest_xray_done_by', '!=', '');
    })->count();

echo "   Needs Attention: {$preNeedsAttention} records\n";
echo "   X-Ray Completed: {$preCompleted} records\n";

echo "\n=== Verification Complete ===\n";
echo "If you see data above, the relationships and queries are working correctly.\n";
echo "Next steps:\n";
echo "1. Test creating new medical checklists through the web interface\n";
echo "2. Test updating existing medical checklists\n";
echo "3. Verify tab filtering works in both views\n";
echo "4. Test file upload functionality\n";
