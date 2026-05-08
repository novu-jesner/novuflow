<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['task_id', 'user_id']);
        });
        
        // Migrate existing assigned_to data
        DB::statement('
            INSERT INTO task_user (task_id, user_id, created_at, updated_at)
            SELECT id, assigned_to, NOW(), NOW()
            FROM tasks
            WHERE assigned_to IS NOT NULL
        ');
        
        // Remove old assigned_to column
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }

    public function down(): void
    {
        // Restore assigned_to column
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        });
        
        // Restore data (first assignee only)
        DB::statement('
            UPDATE tasks t
            SET assigned_to = (
                SELECT user_id FROM task_user tu WHERE tu.task_id = t.id LIMIT 1
            )
        ');
        
        Schema::dropIfExists('task_user');
    }
};
