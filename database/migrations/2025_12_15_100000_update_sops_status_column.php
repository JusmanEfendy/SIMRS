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
        // Step 1: First, alter column to include ALL values (old + new)
        // This prevents data truncation error
        DB::statement("ALTER TABLE sops MODIFY COLUMN status ENUM('Pending', 'In Review', 'Approve', 'Rejected', 'Expired', 'Aktif', 'Kadaluarsa') DEFAULT 'Aktif'");
        
        // Step 2: Now update existing data to new values
        DB::statement("UPDATE sops SET status = 'Aktif' WHERE status = 'Approve'");
        DB::statement("UPDATE sops SET status = 'Kadaluarsa' WHERE status = 'Expired'");
        
        // Step 3: Update records with other statuses to 'Aktif' (or you can delete them)
        DB::statement("UPDATE sops SET status = 'Aktif' WHERE status IN ('Pending', 'In Review', 'Rejected')");
        
        // Step 4: Finally, alter column to only have new values
        DB::statement("ALTER TABLE sops MODIFY COLUMN status ENUM('Aktif', 'Kadaluarsa') DEFAULT 'Aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: First, alter column to include ALL values
        DB::statement("ALTER TABLE sops MODIFY COLUMN status ENUM('Pending', 'In Review', 'Approve', 'Rejected', 'Expired', 'Aktif', 'Kadaluarsa') DEFAULT 'Pending'");
        
        // Step 2: Map back to original values
        DB::statement("UPDATE sops SET status = 'Approve' WHERE status = 'Aktif'");
        DB::statement("UPDATE sops SET status = 'Expired' WHERE status = 'Kadaluarsa'");
        
        // Step 3: Alter column to original values only
        DB::statement("ALTER TABLE sops MODIFY COLUMN status ENUM('Pending', 'In Review', 'Approve', 'Rejected', 'Expired') DEFAULT 'Pending'");
    }
};
