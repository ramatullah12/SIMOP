
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

            // Work Estimate yang membutuhkan persetujuan
            $table->foreignId('work_estimate_id')
                ->constrained('work_estimates')
                ->cascadeOnDelete();

            // Direktur atau CEO yang memproses persetujuan
            $table->foreignId('approver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status persetujuan
            $table->string('status')->default('pending');

            // Catatan dari pemberi persetujuan
            $table->text('notes')->nullable();

            // Waktu keputusan diberikan
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
