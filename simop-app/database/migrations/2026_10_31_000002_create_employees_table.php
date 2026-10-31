<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('position');
            $table->string('department')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('join_date')->nullable();
            $table->string('status')->default('aktif');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::table('employees')->insert([
            [
                'employee_code' => 'PGW-001',
                'name' => 'Budi Santoso',
                'position' => 'Koordinator Lapangan',
                'department' => 'Operasional',
                'phone' => '081234567801',
                'email' => 'budi.santoso@simop.local',
                'address' => 'Jl. Merdeka No. 12, Jakarta',
                'join_date' => '2024-01-15',
                'status' => 'aktif',
                'notes' => 'Penanggung jawab tim survey dan instalasi lapangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_code' => 'PGW-002',
                'name' => 'Rizky Pratama',
                'position' => 'Teknisi Produksi & Instalasi',
                'department' => 'Teknis & Produksi',
                'phone' => '081234567802',
                'email' => 'rizky.pratama@simop.local',
                'address' => 'Jl. Sudirman No. 45, Jakarta',
                'join_date' => '2024-03-10',
                'status' => 'aktif',
                'notes' => 'Tim pemasangan booth dan tenda event',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_code' => 'PGW-003',
                'name' => 'Siti Rahmawati',
                'position' => 'Staff Administrasi Operasional',
                'department' => 'Administrasi',
                'phone' => '081234567803',
                'email' => 'siti.rahma@simop.local',
                'address' => 'Jl. Gatot Subroto No. 8, Jakarta',
                'join_date' => '2024-06-01',
                'status' => 'aktif',
                'notes' => 'Pengelolaan dokumen proyek dan jadwal survey',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

