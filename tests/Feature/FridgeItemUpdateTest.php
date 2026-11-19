<?php

namespace Tests\Feature;

use App\Enums\UnitLabel;
use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FridgeItemUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that the owner sees the edit form prepopulated with their fridge item.
     */
    public function test_authenticated_user_sees_the_edit_form_prepopulated_with_their_fridge_item(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $formResponse = $this->actingAs($user1)
            ->get(route('fridge-items.edit', ['fridge_item' => $fridgeItem->id]));

        $formResponse->assertOk();
        $formResponse->assertSee('value="Kinder"', false);
        $formResponse->assertSee('value="'.$expiresAt.'"', false);
        $formResponse->assertSee('value="1"', false);
        $formResponse->assertSeeInOrder(['value="'.UnitLabel::Box->value.'"', 'selected'], false);

    }

    /**
     * Verify that an authenticated user cannot access edit page of fridge item owned by other user.
     */
    public function test_authenticated_user_cannot_access_edit_page_owned_by_other_user(): void
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
            ->get(route('fridge-items.edit', ['fridge_item' => $fridgeItem->id]));

        $response->assertNotFound();
    }

    /**
     * Verify that an unauthenticated user (guest) cannot access edit page of fridge item.
     */
    public function test_guest_cannot_access_fridge_item_edit_page(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAsGuest()
            ->get(route('fridge-items.edit', ['fridge_item' => $fridgeItem->id]));

        $response->assertRedirect(route('login'));
    }

    /**
     * Verify that an authenticated user can update a fridge item.
     */
    public function test_authenticated_user_can_update_a_fridge_item(): void
    {
        $expiresAt = today()->addDays(5);
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $newExpiresAt = today()->addDays(10);

        $response = $this->actingAs($user1)
            ->from(route('fridge-items.edit', [
                'fridge_item' => $fridgeItem->id]))
            ->patch(route('fridge-items.update', [
                'fridge_item' => $fridgeItem->id,
            ]), [
                'product_name' => 'Coca Cola',
                'expires_at' => $newExpiresAt->toDateString(),
                'unit_count' => 2,
                'unit_label' => UnitLabel::Bottle->value,
            ]);

        $response->assertRedirect(route('fridge-items.index'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'product_name' => 'Coca Cola',
            'expires_at' => $newExpiresAt->toDateString(),
            'unit_label' => UnitLabel::Bottle->value,
            'unit_count' => 2,
            'user_id' => $user1->id,
        ]);
    }

    /**
     * Verify that an invalid expiration date is rejected when updating a fridge item.
     */
    public function test_invalid_expiration_date_is_rejected_when_updating_a_fridge_item(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)
            ->from(route('fridge-items.edit', [
                'fridge_item' => $fridgeItem->id]))
            ->patch(route('fridge-items.update', [
                'fridge_item' => $fridgeItem->id,
            ]), [
                'product_name' => 'Coca Cola',
                'expires_at' => 'not-a-date',
                'unit_count' => 2,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response->assertRedirect(route('fridge-items.edit', ['fridge_item' => $fridgeItem->id]));
        $response->assertSessionHasErrors('expires_at');

        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'product_name' => 'Kinder',
            'expires_at' => $expiresAt,
            'unit_count' => 1,
            'unit_label' => UnitLabel::Box->value,
        ]);

    }

    /**
     * Verify that a quantity below one is rejected when updating a fridge item.
     */
    public function test_quantity_below_one_is_rejected_when_updating_a_fridge_item(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)
            ->from(route('fridge-items.edit', [
                'fridge_item' => $fridgeItem->id]))
            ->patch(route('fridge-items.update', [
                'fridge_item' => $fridgeItem->id,
            ]), [
                'product_name' => 'Coca Cola',
                'expires_at' => $expiresAt,
                'unit_count' => 0,
                'unit_label' => UnitLabel::Bottle->value,
            ]);

        $response->assertRedirect(route('fridge-items.edit', ['fridge_item' => $fridgeItem->id]));
        $response->assertSessionHasErrors('unit_count');

        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'product_name' => 'Kinder',
            'expires_at' => $expiresAt,
            'unit_count' => 1,
            'unit_label' => UnitLabel::Box->value,
        ]);

    }

    /**
     * Verify that an unsupported unit label is rejected when updating a fridge item.
     */
    public function test_unsupported_unit_label_is_rejected_when_updating_a_fridge_item(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $response = $this->actingAs($user1)
            ->from(route('fridge-items.edit', [
                'fridge_item' => $fridgeItem->id]))
            ->patch(route('fridge-items.update', [
                'fridge_item' => $fridgeItem->id,
            ]), [
                'product_name' => 'Coca Cola',
                'expires_at' => $expiresAt,
                'unit_count' => 2,
                'unit_label' => 'not-a-supported-unit-label',
            ]);

        $response->assertRedirect(route('fridge-items.edit', $fridgeItem));
        $response->assertSessionHasErrors('unit_label');

        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'product_name' => 'Kinder',
            'expires_at' => $expiresAt,
            'unit_label' => UnitLabel::Box->value,
            'unit_count' => 1,
            'user_id' => $user1->id,
        ]);
    }

    /**
     * Verify that a user cannot update another user's fridge item.
     */
    public function test_user_cannot_update_another_users_fridge_item(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $newExpiresAt = today()->addDays(10);

        $response = $this->actingAs($user2)
            ->from(route('fridge-items.edit', [
                'fridge_item' => $fridgeItem->id]))
            ->patch(route('fridge-items.update', [
                'fridge_item' => $fridgeItem->id,
            ]), [
                'product_name' => 'Coca Cola',
                'expires_at' => $newExpiresAt,
                'unit_count' => 2,
                'unit_label' => UnitLabel::Bottle->value,
            ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'product_name' => 'Kinder',
            'expires_at' => $expiresAt,
            'unit_count' => 1,
            'unit_label' => UnitLabel::Box->value,
            'user_id' => $user1->id,
        ]);
    }

    /**
     * Verify that a guest cannot update  fridge item.
     */
    public function test_guest_cannot_update_fridge_item(): void
    {
        $expiresAt = today()->addDays(5)->toDateString();
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => UnitLabel::Box->value,
            ]);

        $newExpiresAt = today()->addDays(10);

        $response = $this->actingAsGuest()
            ->from(route('fridge-items.edit', [
                'fridge_item' => $fridgeItem->id]))
            ->patch(route('fridge-items.update', [
                'fridge_item' => $fridgeItem->id,
            ]), [
                'product_name' => 'Coca Cola',
                'expires_at' => $newExpiresAt,
                'unit_count' => 2,
                'unit_label' => UnitLabel::Bottle->value,
            ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
            'product_name' => 'Kinder',
            'expires_at' => $expiresAt,
            'unit_count' => 1,
            'unit_label' => UnitLabel::Box->value,
            'user_id' => $user1->id,
        ]);
    }
}
