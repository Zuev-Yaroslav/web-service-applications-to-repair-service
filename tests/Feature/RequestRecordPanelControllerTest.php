<?php

namespace Tests\Feature;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestRecordPanelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_work_succeeds_when_request_is_assigned_to_master(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $masterRole = Role::query()->where('name', 'master')->first();
        $master = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->assigned($master)->create();

        $response = $this->actingAs($master)->post(
            route('request-record-panel.start-work', $record)
        );

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Work started');

        $record->refresh();
        $this->assertSame(RequestRecordStatus::InProgress, $record->status);
    }

    public function test_start_work_returns_409_when_request_already_taken(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $masterRole = Role::query()->where('name', 'master')->first();
        $master = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->assigned($master)->create();

        $this->actingAs($master)->post(route('request-record-panel.start-work', $record));

        $response = $this->actingAs($master)->post(
            route('request-record-panel.start-work', $record)
        );

        $response->assertStatus(409);
        $response->assertSee('Request already taken or no longer assigned to you');
    }
}
