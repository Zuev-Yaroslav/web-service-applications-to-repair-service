<?php

namespace Tests\Feature;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_record_has_required_attributes_and_default_status(): void
    {
        $record = RequestRecord::factory()->create([
            'client_name' => 'John Doe',
            'phone' => '+1 234 567 8900',
            'address' => '123 Main St',
            'problem_text' => 'Broken appliance',
        ]);

        $this->assertSame('John Doe', $record->client_name);
        $this->assertSame('+1 234 567 8900', $record->phone);
        $this->assertSame('123 Main St', $record->address);
        $this->assertSame('Broken appliance', $record->problem_text);
        $this->assertNull($record->assigned_to);
        $this->assertSame(RequestRecordStatus::New, $record->status);
    }

    public function test_status_is_cast_to_enum(): void
    {
        $record = RequestRecord::factory()->create(['status' => RequestRecordStatus::InProgress]);

        $this->assertSame(RequestRecordStatus::InProgress, $record->status);
        $this->assertSame('in_progress', $record->status->value);
    }

    public function test_request_record_can_be_assigned_to_master_user(): void
    {
        $masterRole = Role::factory()->master()->create();
        $master = User::factory()->create(['role_id' => $masterRole->id]);

        $record = RequestRecord::factory()->assigned($master)->create();

        $this->assertTrue($record->assignedTo->is($master));
        $this->assertSame(RequestRecordStatus::Assigned, $record->status);
        $this->assertCount(1, $master->assignedRequestRecords);
        $this->assertTrue($master->assignedRequestRecords->first()->is($record));
    }
}
