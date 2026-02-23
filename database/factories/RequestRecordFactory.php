<?php

namespace Database\Factories;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestRecord>
 */
class RequestRecordFactory extends Factory
{
    protected $model = RequestRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'problem_text' => fake()->paragraphs(2, true),
            'status' => RequestRecordStatus::New,
            'assigned_to' => null,
        ];
    }

    public function assigned(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
            'status' => RequestRecordStatus::Assigned,
        ]);
    }

    public function inProgress(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
            'status' => RequestRecordStatus::InProgress,
        ]);
    }
}
