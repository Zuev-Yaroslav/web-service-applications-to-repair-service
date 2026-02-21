<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dispatcherRoleId = Role::query()->where('name', 'dispatcher')->value('id');
        $masterRoleId = Role::query()->where('name', 'master')->value('id');

        User::factory()->count(5)->create(['role_id' => $dispatcherRoleId]);
        User::factory()->count(20)->create(['role_id' => $masterRoleId]);
    }
}
