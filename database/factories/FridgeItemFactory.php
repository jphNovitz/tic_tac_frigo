<?php

namespace Database\Factories;

use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FridgeItem>
 */
class FridgeItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_name' => fake()->word(),
            'expires_at' => fake()->dateTimeBetween('now', '+1 month'),
            'quantity' => fake()->randomElement([
                '1 bouteille',
                '2 pots',
                '500 g',
                '1 L',
            ]),
        ];
    }
}
