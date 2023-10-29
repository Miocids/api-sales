<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
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
            'sku'           => $this->faker->unique()->name,
            'price'         => rand(1, 100),
            'status'        => true,
            'created_by'    => "{$user?->getKey()}"
        ];
    }
}
