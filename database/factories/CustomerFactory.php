<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::query()->latest()->first();
        return [
            'name'          => $this->faker->name,
            'email'         => $this->faker->unique()->safeEmail,
            'address'       => $this->faker->address,
            'status'        => true,
            'created_by'    => "{$user?->getKey()}"
        ];
    }
}
