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
        Schema::table('drug_test_results', function (Blueprint $table) {
            $table->dropColumn('admission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drug_test_results', function (Blueprint $table) {
            $table->date('admission_date')->nullable()->after('examination_datetime');
        });
    }
};
