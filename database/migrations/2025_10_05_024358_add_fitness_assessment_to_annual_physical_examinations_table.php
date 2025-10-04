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
            $table->string('fitness_assessment')->nullable()->after('drug_test');
            $table->integer('drug_positive_count')->default(0)->after('fitness_assessment');
            $table->integer('medical_abnormal_count')->default(0)->after('drug_positive_count');
            $table->integer('physical_abnormal_count')->default(0)->after('medical_abnormal_count');
            $table->text('assessment_details')->nullable()->after('physical_abnormal_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annual_physical_examinations', function (Blueprint $table) {
            $table->dropColumn([
                'fitness_assessment',
                'drug_positive_count', 
                'medical_abnormal_count',
                'physical_abnormal_count',
                'assessment_details'
            ]);
        });
    }
};
