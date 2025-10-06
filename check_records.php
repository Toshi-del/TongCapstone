<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PreEmploymentRecord;

echo "=== Checking Pre-Employment Records ===\n\n";

// Check total records
$totalRecords = PreEmploymentRecord::count();
echo "📊 Total Records: {$totalRecords}\n\n";

// Check records by status
$statuses = PreEmploymentRecord::select('status')
    ->groupBy('status')
    ->selectRaw('status, count(*) as count')
    ->get();

echo "📈 Records by Status:\n";
foreach ($statuses as $status) {
    echo "   {$status->status}: {$status->count}\n";
}

// Check approved records specifically
$approvedRecords = PreEmploymentRecord::where('status', 'Approved')->get();
echo "\n✅ Approved Records Details:\n";
if ($approvedRecords->count() == 0) {
    echo "   No approved records found\n";
} else {
    foreach ($approvedRecords as $record) {
        $linkSent = $record->registration_link_sent ? 'YES' : 'NO';
        echo "   ID: {$record->id} | {$record->first_name} {$record->last_name} | Email: {$record->email} | Link Sent: {$linkSent}\n";
    }
}

// Check records eligible for bulk sending
$eligibleRecords = PreEmploymentRecord::where('status', 'Approved')
    ->where('registration_link_sent', false)
    ->get();

echo "\n📧 Records Eligible for Registration Links:\n";
if ($eligibleRecords->count() == 0) {
    echo "   No records eligible for registration links\n";
    echo "   (Need status='Approved' AND registration_link_sent=false)\n";
} else {
    foreach ($eligibleRecords as $record) {
        echo "   ID: {$record->id} | {$record->first_name} {$record->last_name} | Email: {$record->email}\n";
    }
}

echo "\n=== Check Complete ===\n";
