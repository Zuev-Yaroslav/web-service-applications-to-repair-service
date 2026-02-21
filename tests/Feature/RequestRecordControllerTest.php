<?php

namespace Tests\Feature;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_displays_form(): void
    {
        $response = $this->get(route('request-record.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('request-record/RequestRecordCreate')
            ->has('status')
        );
    }

    public function test_store_creates_request_record_and_redirects_with_created_status(): void
    {
        $data = [
            'client_name' => 'Jane Doe',
            'phone' => '+1 555 123 4567',
            'address' => '456 Oak Ave',
            'problem_text' => 'Broken washing machine',
        ];

        $response = $this->post(route('request-record.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Created');

        $this->assertDatabaseHas('request_records', [
            'client_name' => 'Jane Doe',
            'phone' => '+1 555 123 4567',
            'address' => '456 Oak Ave',
            'problem_text' => 'Broken washing machine',
            'status' => RequestRecordStatus::New->value,
            'assigned_to' => null,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('request-record.store'), []);

        $response->assertSessionHasErrors(['client_name', 'phone', 'address', 'problem_text']);
        $this->assertDatabaseCount('request_records', 0);
    }
}
