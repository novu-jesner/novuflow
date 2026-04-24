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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'Completed', 'On Hold'])->default('Active');
            $table->date('start_date');
            $table->date('due_date');
            $table->integer('progress')->default(0);
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('projects');
    }
};
