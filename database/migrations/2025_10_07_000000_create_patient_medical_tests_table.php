<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the pivot table for patient-medical test relationships
        Schema::create('patient_medical_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_test_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['patient_id', 'medical_test_id']);
            
            // Add indexes for performance
            $table->index(['patient_id']);
            $table->index(['medical_test_id']);
        });
        
        // Populate the pivot table with existing data
        $this->populateExistingData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medical_tests');
    }
    
    /**
     * Populate the pivot table with existing patient-appointment-medical test relationships
     */
    private function populateExistingData(): void
    {
        // Get all patients with their appointments
        $patients = DB::table('patients')
            ->join('appointments', 'patients.appointment_id', '=', 'appointments.id')
            ->select('patients.id as patient_id', 'appointments.medical_test_id')
            ->whereNotNull('appointments.medical_test_id')
            ->get();

        foreach ($patients as $patient) {
            $medicalTestIds = [];
            
            // Handle JSON format
            if (is_string($patient->medical_test_id)) {
                $decoded = json_decode($patient->medical_test_id, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $medicalTestIds = $decoded;
                } else {
                    // Not JSON, treat as single ID
                    $medicalTestIds = [$patient->medical_test_id];
                }
            } else {
                $medicalTestIds = [$patient->medical_test_id];
            }
            
            // Insert records for each medical test
            foreach ($medicalTestIds as $testId) {
                if ($testId && is_numeric($testId)) {
                    DB::table('patient_medical_tests')->insertOrIgnore([
                        'patient_id' => $patient->patient_id,
                        'medical_test_id' => $testId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
};
