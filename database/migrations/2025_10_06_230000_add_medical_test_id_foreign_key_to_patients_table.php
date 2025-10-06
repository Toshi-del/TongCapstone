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
        if (Schema::hasTable('medical_tests') && Schema::hasColumn('patients', 'medical_test_id')) {
            // First, check the current column type
            $columnInfo = DB::select("
                SELECT DATA_TYPE, COLUMN_TYPE 
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'patients' 
                AND COLUMN_NAME = 'medical_test_id'
            ");
            
            // If the column is not the correct type, change it
            if (!empty($columnInfo) && $columnInfo[0]->DATA_TYPE !== 'bigint') {
                Schema::table('patients', function (Blueprint $table) {
                    $table->unsignedBigInteger('medical_test_id')->nullable()->change();
                });
            }
            
            // Check if foreign key already exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'patients' 
                AND COLUMN_NAME = 'medical_test_id'
                AND REFERENCED_TABLE_NAME = 'medical_tests'
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('patients', function (Blueprint $table) {
                    $table->foreign('medical_test_id')
                          ->references('id')
                          ->on('medical_tests')
                          ->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Drop foreign key constraint if it exists
            try {
                $table->dropForeign(['medical_test_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop column if it exists
            if (Schema::hasColumn('patients', 'medical_test_id')) {
                $table->dropColumn('medical_test_id');
            }
        });
    }
};
