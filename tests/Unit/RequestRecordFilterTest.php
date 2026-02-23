<?php

namespace Tests\Unit;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestRecordFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_valid_status_returns_only_matching_records(): void
    {
        RequestRecord::factory()->create(['status' => RequestRecordStatus::New]);
        RequestRecord::factory()->create(['status' => RequestRecordStatus::Assigned]);
        RequestRecord::factory()->create(['status' => RequestRecordStatus::Assigned]);

        $results = RequestRecord::query()->filter(['status' => 'assigned'])->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->every(fn ($r) => $r->status === RequestRecordStatus::Assigned));
    }

    public function test_filter_ignores_invalid_status(): void
    {
        RequestRecord::factory()->count(3)->create();

        $results = RequestRecord::query()->filter(['status' => 'invalid_status'])->get();

        $this->assertCount(3, $results);
    }

    public function test_filter_ignores_empty_string_status(): void
    {
        RequestRecord::factory()->count(2)->create();

        $results = RequestRecord::query()->filter(['status' => ''])->get();

        $this->assertCount(2, $results);
    }
}
