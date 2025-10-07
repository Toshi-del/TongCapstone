<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PreEmploymentExamination;

// Get the examination ID from command line argument
$examinationId = $argv[1] ?? null;

if (!$examinationId) {
    echo "Usage: php check_examination_status.php <examination_id>\n";
    exit(1);
}

$examination = PreEmploymentExamination::find($examinationId);

if (!$examination) {
    echo "Examination with ID {$examinationId} not found.\n";
    exit(1);
}

echo "Examination ID: {$examination->id}\n";
echo "Name: {$examination->name}\n";
echo "Company: {$examination->company_name}\n";
echo "Status: " . ($examination->status ?? 'NULL') . "\n";
echo "Status type: " . gettype($examination->status) . "\n";
echo "Status length: " . strlen($examination->status ?? '') . "\n";
echo "Status (raw): " . var_export($examination->status, true) . "\n";
echo "\nChecking conditions:\n";
echo "status === 'sent_to_company': " . ($examination->status === 'sent_to_company' ? 'TRUE' : 'FALSE') . "\n";
echo "status === 'sent_to_both': " . ($examination->status === 'sent_to_both' ? 'TRUE' : 'FALSE') . "\n";
echo "status === 'Approved': " . ($examination->status === 'Approved' ? 'TRUE' : 'FALSE') . "\n";
