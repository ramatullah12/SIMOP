<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;
    private Role $clientRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $this->clientRole = Role::firstOrCreate(['slug' => 'client'], ['name' => 'Client']);

        $this->admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->adminRole->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_registered_user_requires_admin_verification(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Calon Klien',
            'email' => 'klien@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register.pending'));

        $user = User::where('email', 'klien@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->isVerified());

        // Mencoba login sebelum verifikasi harus gagal
        $loginResponse = $this->post(route('login'), [
            'email' => 'klien@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_verify_new_user(): void
    {
        $user = User::create([
            'name' => 'User Baru',
            'email' => 'baru@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null,
        ]);

        $this->assertFalse($user->isVerified());

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.verify', $user));

        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->isVerified());
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals($this->clientRole->id, $user->role_id);
    }

    public function test_admin_can_unverify_user_but_not_self(): void
    {
        $user = User::create([
            'name' => 'Klien Aktif',
            'email' => 'aktif@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->clientRole->id,
            'email_verified_at' => now(),
        ]);

        // Unverify user lain berhasil
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.unverify', $user));

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertFalse($user->isVerified());

        // Admin mencoba unverify diri sendiri harus dicegah
        $selfResponse = $this->actingAs($this->admin)
            ->post(route('admin.users.unverify', $this->admin));

        $selfResponse->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue($this->admin->isVerified());
    }

    public function test_admin_can_verify_incoming_order(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'client_id' => $this->admin->id,
            'company_name' => 'PT Mitra Sejahtera',
            'contact_person' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@mitra.com',
            'event_name' => 'Pameran Otomotif 2026',
            'event_location' => 'JIExpo Kemayoran Jakarta',
            'description' => 'Kebutuhan panggung dan partisi booth',
            'status' => 'masuk',
        ]);

        // Verifikasi ke survei
        $response = $this->actingAs($this->admin)
            ->post(route('orders.verify', $order), [
                'next_action' => 'menunggu_survey',
                'admin_notes' => 'Pesanan valid dan lokasi siap disurvei.',
            ]);

        $response->assertRedirect(route('surveys.create', ['order_id' => $order->id]));

        $order->refresh();
        $this->assertEquals('menunggu_survey', $order->status);
        $this->assertEquals('Pesanan valid dan lokasi siap disurvei.', $order->admin_notes);
    }

    public function test_admin_can_reject_order_with_reason(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'client_id' => $this->admin->id,
            'company_name' => 'CV Tidak Lengkap',
            'contact_person' => 'Anonim',
            'phone' => '0800000000',
            'event_name' => 'Kegiatan Fiktif',
            'event_location' => 'Tidak Diketahui',
            'description' => 'Deskripsi tidak jelas',
            'status' => 'masuk',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('orders.reject', $order), [
                'rejection_reason' => 'Data lokasi dan kebutuhan tidak dapat diverifikasi.',
            ]);

        $response->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertEquals('ditolak', $order->status);
        $this->assertEquals('Data lokasi dan kebutuhan tidak dapat diverifikasi.', $order->admin_notes);
    }
}

