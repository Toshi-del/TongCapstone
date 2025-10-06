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
            // Add columns if they don't exist
            if (!Schema::hasColumn('pre_employment_examinations', 'fitness_assessment')) {
                $table->string('fitness_assessment')->nullable()->after('drug_test');
            }
            if (!Schema::hasColumn('pre_employment_examinations', 'drug_positive_count')) {
                $table->integer('drug_positive_count')->default(0)->after('fitness_assessment');
            }
            if (!Schema::hasColumn('pre_employment_examinations', 'medical_abnormal_count')) {
                $table->integer('medical_abnormal_count')->default(0)->after('drug_positive_count');
            }
            if (!Schema::hasColumn('pre_employment_examinations', 'physical_abnormal_count')) {
                $table->integer('physical_abnormal_count')->default(0)->after('medical_abnormal_count');
            }
            if (!Schema::hasColumn('pre_employment_examinations', 'assessment_details')) {
                $table->text('assessment_details')->nullable()->after('physical_abnormal_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_employment_examinations', function (Blueprint $table) {
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
