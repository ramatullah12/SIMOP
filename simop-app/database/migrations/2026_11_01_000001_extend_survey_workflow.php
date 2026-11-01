<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Melengkapi alur survei: pengajuan oleh Admin, persetujuan Direktur,
 * SPS, form hasil survei, dan tanda tangan pelanggan.
 * Hanya menambah kolom dan tabel baru, data lama tidak dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            // Data pengajuan dari Admin
            $table->string('customer_name')->nullable()->after('requested_by');
            $table->string('survey_type')->nullable()->after('location');
            $table->text('purpose')->nullable()->after('survey_type');

            // Keputusan Direktur
            $table->foreignId('rejected_by')->nullable()->after('rejection_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('approval_notes')->nullable()->after('rejected_at');
            $table->timestamp('sps_issued_at')->nullable()->after('approval_notes');

            // Pelaksanaan dan hasil survei
            $table->timestamp('started_at')->nullable()->after('sps_issued_at');
            $table->timestamp('field_finished_at')->nullable()->after('started_at');
            $table->date('executed_on')->nullable()->after('field_finished_at');
            $table->text('location_condition')->nullable()->after('executed_on');
            $table->text('result_notes')->nullable()->after('location_condition');
            $table->text('conclusion')->nullable()->after('result_notes');
            $table->timestamp('completed_at')->nullable()->after('conclusion');

            // Tanda tangan pelanggan (file PNG di storage, bukan base64)
            $table->string('signature_path')->nullable()->after('completed_at');
            $table->string('signer_name')->nullable()->after('signature_path');
            $table->string('signer_identity')->nullable()->after('signer_name');
            $table->boolean('signer_agreed')->default(false)->after('signer_identity');
            $table->timestamp('signed_at')->nullable()->after('signer_agreed');
        });

        Schema::create('survey_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action', 60);
            $table->text('description')->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // Menyesuaikan status lama ke status alur baru (tanpa menghapus baris)
        DB::table('surveys')->where('status', 'diajukan_client')->update(['status' => 'draft']);
        DB::table('surveys')->where('status', 'dijadwalkan')->update(['status' => 'menunggu_pelaksanaan']);
        DB::table('surveys')->whereNotNull('approved_at')->whereNull('sps_issued_at')
            ->update(['sps_issued_at' => DB::raw('approved_at')]);
    }

    public function down(): void
    {
        DB::table('surveys')->where('status', 'draft')->update(['status' => 'diajukan_client']);
        DB::table('surveys')->whereIn('status', ['menunggu_pelaksanaan', 'disetujui_direktur'])->update(['status' => 'dijadwalkan']);
        DB::table('surveys')->where('status', 'menunggu_hasil')->update(['status' => 'berlangsung']);

        Schema::dropIfExists('survey_activities');
        Schema::dropIfExists('survey_photos');

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rejected_by');
            $table->dropColumn([
                'customer_name', 'survey_type', 'purpose', 'rejected_at', 'approval_notes',
                'sps_issued_at', 'started_at', 'field_finished_at', 'executed_on',
                'location_condition', 'result_notes', 'conclusion', 'completed_at',
                'signature_path', 'signer_name', 'signer_identity', 'signer_agreed', 'signed_at',
            ]);
        });
    }
};
