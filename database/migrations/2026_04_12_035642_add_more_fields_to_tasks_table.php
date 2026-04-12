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
    Schema::table('tasks', function (Blueprint $table) {

        $table->enum('priority', ['low', 'medium', 'high'])
              ->default('low')
              ->after('description');

        $table->date('due_date')
              ->nullable()
              ->after('priority');

        $table->foreignId('created_by')
              ->nullable()
              ->after('assigned_to')
              ->constrained('users')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn('priority');
        $table->dropColumn('due_date');
        $table->dropConstrainedForeignId('created_by');
    });
}
};
