<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_employee_monitoring()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.employee-monitoring'));

        $response->assertStatus(200);
    }

    public function test_employees_index_shows_list_and_export()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $emp1 = User::factory()->create(['role' => 'cashier']);
        $emp2 = User::factory()->create(['role' => 'helper']);

        $resp = $this->actingAs($admin)->get(route('admin.employees.index'));
        $resp->assertStatus(200)
            ->assertSeeText($emp1->name)
            ->assertSeeText($emp2->name);

        $csv = $this->actingAs($admin)->get(route('admin.employees.export'));
        $csv->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csv->headers->get('content-type'));
    }
}
