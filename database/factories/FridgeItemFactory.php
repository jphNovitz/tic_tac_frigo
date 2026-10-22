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
            'unit_count' => fake()->numberBetween(1, 20),
            'unit_label' => fake()->randomElement([
                'bouteille de 1L',
                'pots de 125g',
                'pièces',
                'paquets',
            ]),
            'expires_at' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
