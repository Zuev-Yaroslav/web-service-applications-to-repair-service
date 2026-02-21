<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_has_name_attribute(): void
    {
        $role = Role::factory()->create(['name' => 'dispatcher']);

        $this->assertSame('dispatcher', $role->name);
    }

    public function test_user_belongs_to_one_role(): void
    {
        $role = Role::factory()->dispatcher()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($user->role->is($role));
        $this->assertSame('dispatcher', $user->role->name);
    }

    public function test_role_seeder_creates_dispatcher_and_master_roles(): void
    {
        $this->seed(RoleSeeder::class);

        $dispatcher = Role::query()->where('name', 'dispatcher')->first();
        $master = Role::query()->where('name', 'master')->first();

        $this->assertNotNull($dispatcher);
        $this->assertNotNull($master);
        $this->assertSame('dispatcher', $dispatcher->name);
        $this->assertSame('master', $master->name);
    }
}
