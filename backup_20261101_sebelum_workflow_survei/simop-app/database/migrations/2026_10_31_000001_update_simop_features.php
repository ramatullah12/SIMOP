<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('sps_number')->nullable()->after('id');
            $table->foreignId('requested_by')->nullable()->after('order_id')->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_to_direktur_at')->nullable()->after('notes');
            $table->foreignId('approved_by')->nullable()->after('submitted_to_direktur_at')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_notes')->nullable()->after('approved_at');
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit')->default('unit');
            $table->integer('total_stock')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('project_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('status')->default('allocated');
            $table->timestamp('allocated_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('field_documentations', function (Blueprint $table) {
            $table->id();
            $table->string('documentation_number')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('production_id')->nullable()->constrained('productions')->nullOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->date('activity_date');
            $table->integer('progress_percentage')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::table('items')->insert([
            [
                'item_code' => 'BRG-001',
                'name' => 'Kursi Event',
                'category' => 'Perlengkapan Event',
                'unit' => 'unit',
                'total_stock' => 100,
                'notes' => 'Kursi operasional event',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'BRG-002',
                'name' => 'Meja Lipat',
                'category' => 'Perlengkapan Event',
                'unit' => 'unit',
                'total_stock' => 30,
                'notes' => 'Meja lipat operasional',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'BRG-003',
                'name' => 'Tenda Sarnafil 3x3',
                'category' => 'Tenda',
                'unit' => 'set',
                'total_stock' => 15,
                'notes' => 'Tenda sarnafil standar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'BRG-004',
                'name' => 'Panel Partisi',
                'category' => 'Dekorasi & Booth',
                'unit' => 'panel',
                'total_stock' => 50,
                'notes' => 'Panel partisi pameran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('field_documentations');
        Schema::dropIfExists('project_items');
        Schema::dropIfExists('items');

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropConstrainedForeignId('requested_by');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'sps_number',
                'submitted_to_direktur_at',
                'approved_at',
                'rejection_notes',
            ]);
        });
    }
};
