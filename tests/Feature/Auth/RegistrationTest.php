<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_returns_404_when_registration_disabled()
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is enabled');
        }

        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_registration_post_returns_404_when_registration_disabled()
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is enabled');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNotFound();
    }
}
