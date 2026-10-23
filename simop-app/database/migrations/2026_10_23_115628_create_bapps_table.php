<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bapps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('production_id')
                ->constrained('productions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('bapp_number')->unique();
            $table->date('completion_date');
            $table->string('location')->nullable();
            $table->text('work_result')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->unique('production_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bapps');
    }
};