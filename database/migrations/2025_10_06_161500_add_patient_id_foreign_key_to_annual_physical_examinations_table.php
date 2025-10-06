<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('annual_physical_examinations', function (Blueprint $table) {
            // Add foreign key constraint for patient_id to reference users table
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('set null');
            
            // Add index for better query performance
            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annual_physical_examinations', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['patient_id']);
            
            // Drop index
            $table->dropIndex(['patient_id']);
        });
    }
};
