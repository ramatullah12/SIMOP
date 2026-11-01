<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $clientA;
    private User $clientB;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);
        $clientRole = Role::firstOrCreate(['slug' => 'client'], ['name' => 'Klien']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id, 'name' => 'Admin User']);
        $this->clientA = User::factory()->create(['role_id' => $clientRole->id, 'name' => 'Klien Alpha', 'email' => 'alpha@test.com']);
        $this->clientB = User::factory()->create(['role_id' => $clientRole->id, 'name' => 'Klien Beta', 'email' => 'beta@test.com']);
    }

    public function test_admin_and_client_can_access_order_create_page(): void
    {
        // Admin access
        $responseAdmin = $this->actingAs($this->admin)->get(route('orders.create'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Buat Pesanan untuk Pelanggan (Admin)');
        $responseAdmin->assertSee('Klien Alpha');

        // Client access
        $responseClient = $this->actingAs($this->clientA)->get(route('orders.create'));
        $responseClient->assertStatus(200);
        $responseClient->assertSee('Buat Pesanan Kegiatan Baru');
    }

    public function test_admin_can_create_order_for_selected_client(): void
    {
        $response = $this->actingAs($this->admin)->post(route('orders.store'), [
            'client_id' => $this->clientA->id,
            'company_name' => 'PT Alpha Solusi',
            'contact_person' => 'Budi Alpha',
            'phone' => '08123456789',
            'email' => 'budi@alpha.com',
            'event_name' => 'Launching Produk Alpha',
            'event_location' => 'Ballroom Hotel Mulia',
            'event_date' => now()->addDays(14)->toDateString(),
            'description' => 'Instalasi panggung dan lighting untuk launching produk.',
            'status' => 'menunggu_survey',
        ]);

        $this->assertDatabaseHas('orders', [
            'client_id' => $this->clientA->id,
            'company_name' => 'PT Alpha Solusi',
            'event_name' => 'Launching Produk Alpha',
            'status' => 'menunggu_survey',
        ]);

        $order = Order::where('company_name', 'PT Alpha Solusi')->first();
        $response->assertRedirect(route('orders.show', $order));
    }

    public function test_client_can_create_order_and_is_automatically_linked_to_their_account(): void
    {
        $response = $this->actingAs($this->clientA)->post(route('orders.store'), [
            'company_name' => 'CV Mandiri Alpha',
            'contact_person' => 'Klien Alpha Langsung',
            'phone' => '08987654321',
            'email' => 'alpha@test.com',
            'event_name' => 'Pameran Seni Alpha',
            'event_location' => 'Galeri Nasional',
            'event_date' => now()->addDays(20)->toDateString(),
            'description' => 'Pemasangan sekat booth dan pencahayaan pameran seni.',
        ]);

        $this->assertDatabaseHas('orders', [
            'client_id' => $this->clientA->id,
            'company_name' => 'CV Mandiri Alpha',
            'event_name' => 'Pameran Seni Alpha',
            'status' => 'masuk',
        ]);

        $order = Order::where('company_name', 'CV Mandiri Alpha')->first();
        $response->assertRedirect(route('orders.show', $order));
    }

    public function test_client_only_sees_own_orders_and_cannot_view_other_client_orders(): void
    {
        $orderA = Order::create([
            'order_number' => 'ORD-TEST-001',
            'client_id' => $this->clientA->id,
            'company_name' => 'Perusahaan Alpha',
            'contact_person' => 'Alpha PIC',
            'phone' => '0811111111',
            'email' => 'alpha@test.com',
            'event_name' => 'Event Alpha',
            'event_location' => 'Lokasi Alpha',
            'event_date' => now()->toDateString(),
            'description' => 'Kebutuhan Alpha',
            'status' => 'masuk',
        ]);

        $orderB = Order::create([
            'order_number' => 'ORD-TEST-002',
            'client_id' => $this->clientB->id,
            'company_name' => 'Perusahaan Beta',
            'contact_person' => 'Beta PIC',
            'phone' => '0822222222',
            'email' => 'beta@test.com',
            'event_name' => 'Event Beta',
            'event_location' => 'Lokasi Beta',
            'event_date' => now()->toDateString(),
            'description' => 'Kebutuhan Beta',
            'status' => 'masuk',
        ]);

        // Client A list orders
        $responseList = $this->actingAs($this->clientA)->get(route('orders.index'));
        $responseList->assertStatus(200);
        $responseList->assertSee('ORD-TEST-001');
        $responseList->assertDontSee('ORD-TEST-002');

        // Client A can view Order A
        $responseShowA = $this->actingAs($this->clientA)->get(route('orders.show', $orderA));
        $responseShowA->assertStatus(200);

        // Client A cannot view Order B (403 forbidden)
        $responseShowB = $this->actingAs($this->clientA)->get(route('orders.show', $orderB));
        $responseShowB->assertStatus(403);
    }

    public function test_admin_can_view_all_client_orders(): void
    {
        $orderA = Order::create([
            'order_number' => 'ORD-TEST-003',
            'client_id' => $this->clientA->id,
            'company_name' => 'Perusahaan Alpha',
            'contact_person' => 'Alpha PIC',
            'phone' => '0811111111',
            'email' => 'alpha@test.com',
            'event_name' => 'Event Alpha 2',
            'event_location' => 'Lokasi Alpha',
            'event_date' => now()->toDateString(),
            'description' => 'Kebutuhan Alpha',
            'status' => 'masuk',
        ]);

        $orderB = Order::create([
            'order_number' => 'ORD-TEST-004',
            'client_id' => $this->clientB->id,
            'company_name' => 'Perusahaan Beta',
            'contact_person' => 'Beta PIC',
            'phone' => '0822222222',
            'email' => 'beta@test.com',
            'event_name' => 'Event Beta 2',
            'event_location' => 'Lokasi Beta',
            'event_date' => now()->toDateString(),
            'description' => 'Kebutuhan Beta',
            'status' => 'masuk',
        ]);

        $response = $this->actingAs($this->admin)->get(route('orders.index'));
        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-003');
        $response->assertSee('ORD-TEST-004');

        $responseShow = $this->actingAs($this->admin)->get(route('orders.show', $orderB));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Perusahaan Beta');
    }

    public function test_survey_creation_is_strictly_separated_and_only_accessible_by_admin(): void
    {
        // Client cannot access survey creation
        $responseClient = $this->actingAs($this->clientA)->get(route('surveys.create'));
        $responseClient->assertStatus(403);

        // Admin can access survey creation
        $responseAdmin = $this->actingAs($this->admin)->get(route('surveys.create'));
        $responseAdmin->assertStatus(200);
    }
}
