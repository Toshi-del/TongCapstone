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
        Schema::table('pre_employment_examinations', function (Blueprint $table) {
            // Add patient_id foreign key column
            $table->unsignedBigInteger('patient_id')->nullable()->after('user_id');
            
            // Add foreign key constraint
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
        Schema::table('pre_employment_examinations', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['patient_id']);
            
            // Drop the column
            $table->dropColumn('patient_id');
        });
    }
};
