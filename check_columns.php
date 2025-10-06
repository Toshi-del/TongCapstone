<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== Checking pre_employment_examinations table columns ===\n\n";

$columns = [
    'fitness_assessment',
    'drug_positive_count', 
    'medical_abnormal_count',
    'physical_abnormal_count',
    'assessment_details'
];

foreach ($columns as $column) {
    $exists = Schema::hasColumn('pre_employment_examinations', $column);
    echo sprintf("%-25s: %s\n", $column, $exists ? '✅ EXISTS' : '❌ MISSING');
}

echo "\n=== Check Complete ===\n";
