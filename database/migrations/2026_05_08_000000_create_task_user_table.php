<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('task_user');

        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
        });

        // Migrate existing assigned_to data only if column exists
        if (Schema::hasColumn('tasks', 'assigned_to')) {
            $tasks = \Illuminate\Support\Facades\DB::table('tasks')
                ->whereNotNull('assigned_to')
                ->select('id', 'assigned_to')
                ->get();
            foreach ($tasks as $task) {
                \Illuminate\Support\Facades\DB::table('task_user')->insert([
                    'task_id' => $task->id,
                    'user_id' => $task->assigned_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Remove foreign key constraint first
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['assigned_to']);
            });

            // Then remove old assigned_to column
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('assigned_to');
            });
        }
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
