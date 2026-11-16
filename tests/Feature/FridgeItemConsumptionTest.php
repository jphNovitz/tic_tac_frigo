<?php

namespace Tests\Feature;

use App\Enums\UnitLabel;
use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FridgeItemConsumptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that an authenticated user can consume their own fridge item and
     * that its consumption date is persisted.
     */
    public function test_authenticated_user_can_consume_their_fridge_item(): void
    {
        $this->travelTo('2026-01-15 12:00:00');

        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)->patch(route('fridge-items.consume', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('fridge-items.index'));

        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'consumed_at' => '2026-01-15 12:00:00',
        ]);
    }

    /**
     * Verify that an authenticated user cannot consume another user's fridge item.
     * The response is not found and its consumption date remains null.
     */
    public function test_user_cannot_consume_another_users_fridge_item(): void
    {
        $this->travelTo('2026-01-15 12:00:00');

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user2)->patch(route('fridge-items.consume', ['fridge_item' => $fridgeItem->id]));

        $response->assertNotFound();

        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'consumed_at' => null,
        ]);
    }

    /**
     * Verify that a guest cannot consume a fridge item.
     * The response redirects to login and its consumption date remains null.
     */
    public function test_guest_cannot_consume_a_fridge_item(): void
    {
        $this->travelTo('2026-01-15 12:00:00');

        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->patch(route('fridge-items.consume', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'consumed_at' => null,
        ]);
    }
}
