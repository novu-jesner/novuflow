<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add constrained column_id
            $table->foreignId('column_id')->nullable()->after('description')->constrained()->cascadeOnDelete();
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Drop status enum
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo')->after('description');
            $table->dropForeign(['column_id']);
            $table->dropColumn('column_id');
        });
    }
};
