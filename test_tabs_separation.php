<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING TAB SEPARATION ===\n\n";

// Test NEEDS REVIEW query
echo "--- NEEDS REVIEW TAB ---\n";
$needsReview = \App\Models\Patient::where('status', 'approved')
    ->whereHas('appointment', function($q) {
        $q->where('status', 'approved');
    })
    ->where(function($mainQuery) {
        $mainQuery->whereHas('medicalChecklists', function($q) {
            $q->where('examination_type', 'annual_physical')
              ->whereNotNull('blood_extraction_done_by')
              ->where('blood_extraction_done_by', '!=', '');
        })
        ->where(function($subQuery) {
            $subQuery->whereDoesntHave('annualPhysicalExamination')
            ->orWhereHas('annualPhysicalExamination', function($q) {
                $q->where(function($labQuery) {
                    $labQuery->whereNull('lab_report')
                             ->orWhere('lab_report', '[]')
                             ->orWhere('lab_report', '{}')
                             ->orWhere('lab_report', 'null')
                             ->orWhereRaw("JSON_LENGTH(lab_report) = 0")
                             ->orWhereRaw("CHAR_LENGTH(lab_report) <= 2")
                             ->orWhere(function($noActualData) {
                                 $noActualData->whereNotNull('lab_report')
                                              ->whereRaw("JSON_LENGTH(lab_report) > 0")
                                              ->whereRaw("JSON_SEARCH(lab_report, 'one', 'Normal') IS NULL")
                                              ->whereRaw("JSON_SEARCH(lab_report, 'one', 'Not Normal') IS NULL")
                                              ->whereRaw("JSON_SEARCH(lab_report, 'one', 'Abnormal') IS NULL")
                                              ->whereRaw("JSON_SEARCH(lab_report, 'one', 'Positive') IS NULL")
                                              ->whereRaw("JSON_SEARCH(lab_report, 'one', 'Negative') IS NULL");
                             });
                });
            });
        });
    })
    ->whereDoesntHave('annualPhysicalExamination', function ($q) {
        $q->whereIn('status', ['sent_to_company']);
    })
    ->get();

echo "Count: " . $needsReview->count() . "\n";
foreach($needsReview as $patient) {
    echo "- {$patient->first_name} {$patient->last_name} (ID: {$patient->id})\n";
}

// Test LAB COMPLETED query
echo "\n--- LAB COMPLETED TAB ---\n";
$labCompleted = \App\Models\Patient::where('status', 'approved')
    ->whereHas('appointment', function($q) {
        $q->where('status', 'approved');
    })
    ->whereHas('medicalChecklists', function($q) {
        $q->where('examination_type', 'annual_physical')
          ->whereNotNull('blood_extraction_done_by')
          ->where('blood_extraction_done_by', '!=', '');
    })
    ->whereHas('annualPhysicalExamination', function($q) {
        $q->whereNotNull('lab_report')
          ->where('lab_report', '!=', '[]')
          ->where('lab_report', '!=', '{}')
          ->where('lab_report', '!=', 'null')
          ->whereRaw("JSON_LENGTH(lab_report) > 0")
          ->where(function($subQuery) {
              $subQuery->whereRaw("JSON_LENGTH(lab_report) > 1")
                       ->where(function($hasActualData) {
                           $hasActualData->whereRaw("JSON_SEARCH(lab_report, 'one', 'Normal') IS NOT NULL")
                                        ->orWhereRaw("JSON_SEARCH(lab_report, 'one', 'Not Normal') IS NOT NULL")
                                        ->orWhereRaw("JSON_SEARCH(lab_report, 'one', 'Abnormal') IS NOT NULL")
                                        ->orWhereRaw("JSON_SEARCH(lab_report, 'one', 'Positive') IS NOT NULL")
                                        ->orWhereRaw("JSON_SEARCH(lab_report, 'one', 'Negative') IS NOT NULL");
                       });
          });
    })
    ->get();

echo "Count: " . $labCompleted->count() . "\n";
foreach($labCompleted as $patient) {
    echo "- {$patient->first_name} {$patient->last_name} (ID: {$patient->id})\n";
}

// Check for overlap
echo "\n--- CHECKING FOR OVERLAP ---\n";
$needsReviewIds = $needsReview->pluck('id')->toArray();
$labCompletedIds = $labCompleted->pluck('id')->toArray();
$overlap = array_intersect($needsReviewIds, $labCompletedIds);

if(empty($overlap)) {
    echo "✓ NO OVERLAP - Tabs are properly separated!\n";
} else {
    echo "✗ OVERLAP DETECTED - Patient IDs in both tabs: " . implode(', ', $overlap) . "\n";
}

echo "\n=== END TEST ===\n";
