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

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $record->refresh();
        $this->assertSame(RequestRecordStatus::InProgress, $record->status);
    }

    public function test_start_work_returns_403_when_request_already_in_progress(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $masterRole = Role::query()->where('name', 'master')->first();
        $master = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->assigned($master)->create();

        $this->actingAs($master)->post(route('request-record-panel.start-work', $record));

        $response = $this->actingAs($master)->post(
            route('request-record-panel.start-work', $record)
        );

        $response->assertStatus(403);
    }

    public function test_finish_succeeds_when_request_is_in_progress_for_master(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $masterRole = Role::query()->where('name', 'master')->first();
        $master = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->inProgress($master)->create();

        $response = $this->actingAs($master)->post(
            route('request-record-panel.finish', $record)
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $record->refresh();
        $this->assertSame(RequestRecordStatus::Done, $record->status);
    }

    public function test_finish_returns_403_when_request_is_not_in_progress(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $masterRole = Role::query()->where('name', 'master')->first();
        $master = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->assigned($master)->create();

        $response = $this->actingAs($master)->post(
            route('request-record-panel.finish', $record)
        );

        $response->assertStatus(403);
    }

    public function test_finish_returns_403_when_request_assigned_to_another_master(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $masterRole = Role::query()->where('name', 'master')->first();
        $master1 = User::factory()->create(['role_id' => $masterRole->id]);
        $master2 = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->inProgress($master1)->create();

        $response = $this->actingAs($master2)->post(
            route('request-record-panel.finish', $record)
        );

        $response->assertStatus(403);
    }
}
