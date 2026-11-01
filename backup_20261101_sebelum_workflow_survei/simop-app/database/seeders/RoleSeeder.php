<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Mengelola administrasi dan operasional SIMOP.',
            ],
            [
                'name' => 'Direktur',
                'slug' => 'direktur',
                'description' => 'Mengevaluasi Work Estimate, harga, memberikan persetujuan final, dan memantau project.',
            ],
            [
                'name' => 'Petugas Lapangan',
                'slug' => 'petugas-lapangan',
                'description' => 'Menangani survey dan pelaksanaan pekerjaan lapangan.',
            ],
            [
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'Mengajukan order dan memantau project miliknya.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}