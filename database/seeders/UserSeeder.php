<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dispatcherRoleId = Role::query()->where('name', 'dispatcher')->value('id');
        $masterRoleId = Role::query()->where('name', 'master')->value('id');

        User::firstOrCreate(['email' => 'kkeebler@example.org'], [
            'name' => 'Earnest Hamill',
            'role_id' => $dispatcherRoleId,
            'password' => Hash::make(config('user.password')),
        ]);
        User::firstOrCreate(['email' => 'hlockman@example.net'], [
            'name' => 'Dillan Monahan',
            'role_id' => $dispatcherRoleId,
            'password' => Hash::make(config('user.password')),
        ]);
        User::firstOrCreate(['email' => 'njaskolski@example.com'], [
            'name' => 'Mrs. Else Kunde III',
            'role_id' => $masterRoleId,
            'password' => Hash::make(config('user.password')),
        ]);
        User::firstOrCreate(['email' => 'mzboncak@example.org'], [
            'name' => 'Alanis McGlynn',
            'role_id' => $masterRoleId,
            'password' => Hash::make(config('user.password')),
        ]);
        User::firstOrCreate(['email' => 'ihomenick@example.com'], [
            'name' => 'Arnaldo Orn',
            'role_id' => $masterRoleId,
            'password' => Hash::make(config('user.password')),
        ]);
    }
}
