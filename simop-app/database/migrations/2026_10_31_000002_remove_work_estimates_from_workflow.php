<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (! Schema::hasColumn('projects', 'order_id')) {
                    $table->foreignId('order_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('orders')
                        ->cascadeOnDelete();
                }

                if (! Schema::hasColumn('projects', 'survey_id')) {
                    $table->foreignId('survey_id')
                        ->nullable()
                        ->after('order_id')
                        ->constrained('surveys')
                        ->nullOnDelete();
                }
            });

            if (Schema::hasTable('work_estimates') && Schema::hasColumn('projects', 'work_estimate_id')) {
                $projects = DB::table('projects')->whereNotNull('work_estimate_id')->get();
                foreach ($projects as $proj) {
                    $we = DB::table('work_estimates')->where('id', $proj->work_estimate_id)->first();
                    if ($we) {
                        DB::table('projects')
                            ->where('id', $proj->id)
                            ->update([
                                'order_id' => $we->order_id,
                                'survey_id' => $we->survey_id,
                            ]);
                    }
                }
            }

            if (Schema::hasColumn('projects', 'work_estimate_id')) {
                Schema::table('projects', function (Blueprint $table) {
                    $table->dropForeign(['work_estimate_id']);
                    $table->dropUnique(['work_estimate_id']);
                    $table->dropColumn('work_estimate_id');
                });
            }
        }

        Schema::dropIfExists('approvals');
        Schema::dropIfExists('work_estimate_items');
        Schema::dropIfExists('work_estimates');
    }

    public function down(): void
    {
        //
    }
};

