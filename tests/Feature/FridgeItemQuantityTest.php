<?php

namespace Tests\Feature;

use App\Enums\UnitLabel;
use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FridgeItemQuantityTest extends TestCase
{
    use RefreshDatabase;

    /**
     *   Verify that an authenticated user can increase the quantity of a fridge item.
     */
    public function test_authenticated_user_can_increase_the_quantity_of_a_fridge_item(): void
    {
        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)
            ->patch(route('fridge-items.increase', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('fridge-items.index'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 2,
        ]);
    }

    /**
     * Verify that an authenticated user can decrease the quantity of a fridge item.
     */
    public function test_authenticated_user_can_decrease_the_quantity_of_a_fridge_item(): void
    {
        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 2,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)
            ->patch(route('fridge-items.decrease', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('fridge-items.index'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 1,
        ]);

    }

    /**
     * Verify that an authenticated user cannot decrease the quantity of a fridge item if the quantity is at one.
     */
    public function test_authenticated_user_cannot_decrease_the_quantity_of_a_fridge_item_if_quantity_is_1(): void
    {
        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)
            ->patch(route('fridge-items.decrease', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('fridge-items.index'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 1,
        ]);
    }

    /**
     * Verify that an authenticated user cannot increase the quantity of a fridge item owned by another user.
     */
    public function test_authenticated_user_cannot_increase_the_quantity_of_a_fridge_item_owned_by_other_user(): void
    {

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user2)
            ->patch(route('fridge-items.increase', ['fridge_item' => $fridgeItem->id]));

        $response->assertNotFound();
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 1,
        ]);

    }

    /**
     * Verify that an authenticated user cannot decrease the quantity of a fridge item owned by another user.
     */
    public function test_authenticated_user_cannot_decrease_the_quantity_of_a_fridge_item_owned_by_other_user(): void
    {

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 2,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user2)
            ->patch(route('fridge-items.decrease', ['fridge_item' => $fridgeItem->id]));

        $response->assertNotFound();
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 2,
        ]);

    }

    /**
     * Verify that an unauthenticated user (Guest) tries to increase fridge item quantity is redirected.
     */
    public function test_guest_is_redirected_to_login_when_increasing_the_quantity_of_a_fridge_item(): void
    {

        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAsGuest()
            ->patch(route('fridge-items.increase', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 1,
        ]);
    }

    /**
     * Verify that an unauthenticated user (Guest) tries to decrease fridge item quantity is redirected.
     */
    public function test_guest_is_redirected_to_login_when_decreasing_the_quantity_of_a_fridge_item(): void
    {

        $user1 = User::factory()->create();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
                'unit_count' => 2,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAsGuest()
            ->patch(route('fridge-items.decrease', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'unit_count' => 2,
        ]);
    }
}
