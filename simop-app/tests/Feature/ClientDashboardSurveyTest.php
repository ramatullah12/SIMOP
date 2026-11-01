<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDashboardSurveyTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_view_survey_monitoring_and_history(): void
    {
        $clientRole = Role::firstOrCreate(['slug' => 'client'], ['name' => 'Klien']);
        $client = User::factory()->create(['role_id' => $clientRole->id]);

        $order = Order::create([
            'client_id' => $client->id,
            'order_number' => 'ORD-202611-001',
            'company_name' => 'PT Maju Terus',
            'contact_person' => 'Budi Santoso',
            'phone' => '08123456789',
            'email' => 'client@test.com',
            'event_name' => 'Konser Musik 2026',
            'event_location' => 'Stadion Utama',
            'event_date' => now()->addDays(10)->toDateString(),
            'description' => 'Pekerjaan panggung dan tenda',
            'status' => 'menunggu_survey',
        ]);

        $survey = Survey::create([
            'order_id' => $order->id,
            'sps_number' => 'SPS/2026/11/0001',
            'location' => 'Stadion Utama',
            'status' => Survey::STATUS_WAITING_EXECUTION,
            'scheduled_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($client)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Monitoring Status & Riwayat Survei', false);
        $response->assertSee('SPS/2026/11/0001');
        $response->assertSee('Stadion Utama');
    }
}
