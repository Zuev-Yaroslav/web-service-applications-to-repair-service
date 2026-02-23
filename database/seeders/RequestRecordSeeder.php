<?php

namespace Database\Seeders;

use App\Models\RequestRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $masterRoleId = Role::query()->where('name', 'master')->value('id');
        $masters = User::query()->where('role_id', $masterRoleId)->get();

        foreach ($masters as $master) {
            RequestRecord::factory()
                ->count(10)
                ->assigned($master)
                ->create();
        }

        RequestRecord::factory()->count(10)->create();
    }
}
