<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Add 'user' to the allowed roles enum
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','team_lead','user') NOT NULL DEFAULT 'team_lead'");
    }

    public function down(): void {
        // Revert to original enum values
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','team_lead') NOT NULL DEFAULT 'team_lead'");
    }
};
