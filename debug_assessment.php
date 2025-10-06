<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PreEmploymentExamination;

echo "=== Debug Pre-Employment Examination Assessment ===\n\n";

// Get examination ID 3 from the URL
$examination = PreEmploymentExamination::find(3);

if (!$examination) {
    echo "❌ Examination with ID 3 not found\n";
    exit;
}

echo "📋 Examination ID: {$examination->id}\n";
echo "👤 Patient: {$examination->name}\n";
echo "🏥 Current Assessment: {$examination->fitness_assessment}\n";
echo "📊 Medical Abnormal Count: {$examination->medical_abnormal_count}\n";
echo "📊 Physical Abnormal Count: {$examination->physical_abnormal_count}\n";
echo "📊 Drug Positive Count: {$examination->drug_positive_count}\n\n";

echo "🧪 Lab Report Data:\n";
$labReport = $examination->lab_report ?? [];
if (empty($labReport)) {
    echo "   No lab report data found\n";
} else {
    foreach ($labReport as $test => $result) {
        echo "   {$test}: {$result}\n";
    }
}

echo "\n🩺 Physical Findings Data:\n";
$physicalFindings = $examination->physical_findings ?? [];
if (empty($physicalFindings)) {
    echo "   No physical findings data found\n";
} else {
    foreach ($physicalFindings as $exam => $data) {
        if (is_array($data)) {
            $result = $data['result'] ?? 'N/A';
            echo "   {$exam}: {$result}\n";
        } else {
            echo "   {$exam}: {$data}\n";
        }
    }
}

echo "\n🧬 Drug Test Data:\n";
$drugTest = $examination->drug_test ?? [];
if (empty($drugTest)) {
    echo "   No drug test data found\n";
} else {
    foreach ($drugTest as $test => $result) {
        echo "   {$test}: {$result}\n";
    }
}

echo "\n🔄 Recalculating Assessment...\n";
$examination->calculateFitnessAssessment();
$examination->refresh();

echo "✅ New Assessment: {$examination->fitness_assessment}\n";
echo "📊 New Medical Abnormal Count: {$examination->medical_abnormal_count}\n";
echo "📊 New Physical Abnormal Count: {$examination->physical_abnormal_count}\n";
echo "📊 New Drug Positive Count: {$examination->drug_positive_count}\n";

echo "\n📝 Assessment Details:\n";
$assessmentDetails = json_decode($examination->assessment_details, true) ?? [];
if (!empty($assessmentDetails)) {
    echo "   Applied Rule: " . ($assessmentDetails['applied_rule'] ?? 'N/A') . "\n";
    echo "   Medical Abnormal Tests: " . count($assessmentDetails['medical_results']['abnormal_tests'] ?? []) . "\n";
    echo "   Physical Abnormal Exams: " . count($assessmentDetails['physical_results']['abnormal_examinations'] ?? []) . "\n";
    
    if (!empty($assessmentDetails['medical_results']['abnormal_tests'])) {
        echo "\n   Abnormal Medical Tests:\n";
        foreach ($assessmentDetails['medical_results']['abnormal_tests'] as $test) {
            echo "     - {$test['test']}: {$test['result']}\n";
        }
    }
}

echo "\n=== Debug Complete ===\n";
