<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PreEmploymentExamination;

$examination = PreEmploymentExamination::find(3);

if ($examination) {
    echo "Lab Report:\n";
    print_r($examination->lab_report);
    
    echo "\nFecalysis value: ";
    $fecalysis = data_get($examination->lab_report, 'fecalysis', 'NOT FOUND');
    echo $fecalysis . "\n";
    
    echo "\nIs fecalysis 'not normal'? ";
    echo (strtolower($fecalysis) === 'not normal' ? 'YES' : 'NO') . "\n";
    
    echo "\nAll lab report keys:\n";
    if (is_array($examination->lab_report)) {
        foreach ($examination->lab_report as $key => $value) {
            echo "  $key = $value\n";
        }
    }
} else {
    echo "Examination not found\n";
}
