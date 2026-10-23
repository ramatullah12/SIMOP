<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('planning_id')
                ->constrained('plannings')
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

            $table->string('production_number')->unique();
            $table->string('title');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status')->default('pending');
            $table->unsignedTinyInteger('progress')->default(0);

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('planning_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};