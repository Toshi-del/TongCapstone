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
        // Check if we're using MySQL or SQLite and handle accordingly
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: Extend the enum to include all roles used across the app, including OPD
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('patient','company','admin','doctor','nurse','ecgtech','radtech','radiologist','plebo','pathologist','opd') NOT NULL DEFAULT 'patient'");
        } else {
            // SQLite: Change column type to VARCHAR with check constraint
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('patient')->change();
            });
            
            // Add check constraint for SQLite (if supported by version)
            try {
                DB::statement("CREATE TRIGGER check_user_role_insert 
                    BEFORE INSERT ON users 
                    FOR EACH ROW 
                    WHEN NEW.role NOT IN ('patient','company','admin','doctor','nurse','ecgtech','radtech','radiologist','plebo','pathologist','opd')
                    BEGIN 
                        SELECT RAISE(ABORT, 'Invalid role value'); 
                    END");
                    
                DB::statement("CREATE TRIGGER check_user_role_update 
                    BEFORE UPDATE ON users 
                    FOR EACH ROW 
                    WHEN NEW.role NOT IN ('patient','company','admin','doctor','nurse','ecgtech','radtech','radiologist','plebo','pathologist','opd')
                    BEGIN 
                        SELECT RAISE(ABORT, 'Invalid role value'); 
                    END");
            } catch (\Exception $e) {
                // Triggers not supported, continue without constraint
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            // Revert to the original enum set
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('patient','company','admin','doctor','nurse') NOT NULL DEFAULT 'patient'");
        } else {
            // SQLite: Drop triggers if they exist
            try {
                DB::statement("DROP TRIGGER IF EXISTS check_user_role_insert");
                DB::statement("DROP TRIGGER IF EXISTS check_user_role_update");
            } catch (\Exception $e) {
                // Triggers might not exist, continue
            }
            
            // Keep the column as string since we can't easily revert to original type
        }
    }
};
