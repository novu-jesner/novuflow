<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('task_status_histories')) {
            return;
        }

        if (Schema::hasColumn('task_status_histories', 'duration_in_status')) {
            Schema::table('task_status_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('duration_in_seconds')->nullable()->after('duration_in_status');
            });

            DB::table('task_status_histories')
                ->whereNotNull('duration_in_status')
                ->update(['duration_in_seconds' => DB::raw('duration_in_status * 60')]);

            Schema::table('task_status_histories', function (Blueprint $table) {
                $table->dropColumn('duration_in_status');
            });
        } elseif (! Schema::hasColumn('task_status_histories', 'duration_in_seconds')) {
            Schema::table('task_status_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('duration_in_seconds')->nullable()->after('changed_by_user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('task_status_histories')) {
            return;
        }

        if (Schema::hasColumn('task_status_histories', 'duration_in_seconds')) {
            Schema::table('task_status_histories', function (Blueprint $table) {
                $table->unsignedInteger('duration_in_status')->nullable()->after('changed_by_user_id');
            });

            DB::table('task_status_histories')
                ->whereNotNull('duration_in_seconds')
                ->update(['duration_in_status' => DB::raw('duration_in_seconds / 60')]);

            Schema::table('task_status_histories', function (Blueprint $table) {
                $table->dropColumn('duration_in_seconds');
            });
        }
    }
};
