<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PreEmploymentExamination;
use App\Models\Patient;
use App\Models\Appointment;

echo "Testing PreEmploymentExamination total price functionality...\n\n";

try {
    // Get a sample PreEmploymentExamination with patient data
    $examination = PreEmploymentExamination::with(['patient.appointment'])
        ->whereHas('patient.appointment')
        ->first();
    
    if (!$examination) {
        echo "No PreEmploymentExamination found with patient and appointment data.\n";
        echo "Creating test data would require more setup, but the methods are ready to use.\n\n";
        
        // Show the available methods
        echo "Available methods added to PreEmploymentExamination model:\n";
        echo "1. getTotalPriceFromAppointment() - Returns float or null\n";
        echo "2. getFormattedTotalPriceFromAppointment() - Returns formatted string with peso sign\n\n";
        
        echo "Usage examples:\n";
        echo "\$examination = PreEmploymentExamination::find(1);\n";
        echo "\$totalPrice = \$examination->getTotalPriceFromAppointment();\n";
        echo "\$formattedPrice = \$examination->getFormattedTotalPriceFromAppointment();\n\n";
        
        exit;
    }
    
    echo "Found PreEmploymentExamination ID: " . $examination->id . "\n";
    echo "Patient: " . ($examination->patient ? $examination->patient->full_name : 'No patient') . "\n";
    echo "Appointment ID: " . ($examination->patient->appointment ? $examination->patient->appointment->id : 'No appointment') . "\n\n";
    
    // Test the new methods
    $totalPrice = $examination->getTotalPriceFromAppointment();
    $formattedPrice = $examination->getFormattedTotalPriceFromAppointment();
    
    echo "Results:\n";
    echo "Raw total price: " . ($totalPrice !== null ? $totalPrice : 'null') . "\n";
    echo "Formatted total price: " . $formattedPrice . "\n\n";
    
    // Show appointment details for verification
    if ($examination->patient && $examination->patient->appointment) {
        $appointment = $examination->patient->appointment;
        echo "Appointment Details:\n";
        echo "- Date: " . $appointment->appointment_date . "\n";
        echo "- Status: " . $appointment->status . "\n";
        echo "- Total Price (direct): " . ($appointment->total_price ? '₱' . number_format($appointment->total_price, 2) : 'N/A') . "\n";
        echo "- Patient Count: " . $appointment->patients()->count() . "\n";
    }
    
    echo "\n✅ Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
