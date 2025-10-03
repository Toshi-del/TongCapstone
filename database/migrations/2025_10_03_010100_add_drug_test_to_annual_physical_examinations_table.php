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
        // Check if the column already exists before adding it
        if (!Schema::hasColumn('annual_physical_examinations', 'drug_test')) {
            Schema::table('annual_physical_examinations', function (Blueprint $table) {
                $table->text('drug_test')->nullable()->after('lab_report'); // JSON for drug test results
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop the column if it exists and wasn't part of the original table
        if (Schema::hasColumn('annual_physical_examinations', 'drug_test')) {
            Schema::table('annual_physical_examinations', function (Blueprint $table) {
                $table->dropColumn('drug_test');
            });
        }
    }
};
