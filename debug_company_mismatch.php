<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PreEmploymentExamination;
use App\Models\User;

// Get the examination ID from command line argument
$examinationId = $argv[1] ?? null;

if (!$examinationId) {
    echo "Usage: php debug_company_mismatch.php <examination_id>\n";
    exit(1);
}

$examination = PreEmploymentExamination::with('preEmploymentRecord')->find($examinationId);

if (!$examination) {
    echo "Examination with ID {$examinationId} not found.\n";
    exit(1);
}

echo "=== EXAMINATION DETAILS ===\n";
echo "Examination ID: {$examination->id}\n";
echo "Patient Name: {$examination->name}\n";
echo "Company Name (in examination): '{$examination->company_name}'\n";
echo "Status: {$examination->status}\n";
echo "\n";

// Get all company users
$companyUsers = User::where('role', 'company')->get();

echo "=== COMPANY USERS ===\n";
foreach ($companyUsers as $user) {
    echo "User ID: {$user->id}\n";
    echo "User Name: {$user->name}\n";
    echo "Company Field: '{$user->company}'\n";
    echo "Match: " . ($user->company === $examination->company_name ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

echo "\n=== PRE-EMPLOYMENT RECORD ===\n";
if ($examination->preEmploymentRecord) {
    echo "Record ID: {$examination->preEmploymentRecord->id}\n";
    echo "Company Name (in record): '{$examination->preEmploymentRecord->company_name}'\n";
    echo "Created By User ID: {$examination->preEmploymentRecord->created_by}\n";
    
    $creator = User::find($examination->preEmploymentRecord->created_by);
    if ($creator) {
        echo "Creator Name: {$creator->name}\n";
        echo "Creator Company: '{$creator->company}'\n";
        echo "Creator Role: {$creator->role}\n";
    }
} else {
    echo "No pre-employment record linked.\n";
}
