<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_estimate_id')
                ->constrained('work_estimates')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('project_number')->unique();
            $table->string('title');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->default('planning');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('work_estimate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};