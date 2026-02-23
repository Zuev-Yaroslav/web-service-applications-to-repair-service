<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function dispatcher(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'dispatcher',
        ]);
    }

    public function master(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'master',
        ]);
    }
}
