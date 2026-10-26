<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_estimate_id')
                ->constrained('work_estimates')
                ->cascadeOnDelete();

            $table->foreignId('approver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')->default('pending');

            $table->text('notes')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};