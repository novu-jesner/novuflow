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
        $projects = \App\Models\Project::all();
        foreach ($projects as $project) {
            $columns = ['To Do', 'In Progress', 'Review', 'Completed'];
            foreach ($columns as $index => $name) {
                \DB::table('project_columns')->insert([
                    'project_id' => $project->id,
                    'name' => $name,
                    'order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('project_columns')->truncate();
    }
};
