<?php

namespace Tests\Feature;

use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FridgeItemDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that the owner sees a delete action for their fridge item.
     */
    public function test_authenticated_user_sees_delete_action_for_their_fridge_item(): void
    {
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
            ]);

        $response = $this->actingAs($user1)
            ->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText('Kinder');
        $response->assertSeeText('Supprimer');
        $response->assertSeeHtml('action="'.route('fridge-items.destroy', $fridgeItem).'"');
        $response->assertSeeHtml('name="_method" value="DELETE"');
    }

    /**
     * Verify that the owner can delete one fridge item without deleting another
     * item with the same product name.
     */
    public function test_authenticated_user_can_delete_their_fridge_item(): void
    {
        $user1 = User::factory()->create();
        $fridgeItem1 = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
            ]);
        $fridgeItem2 = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(10),
            ]);

        $response = $this->actingAs($user1)
            ->delete(route('fridge-items.destroy', $fridgeItem1));

        $response->assertRedirect(route('fridge-items.index'));
        $this->assertDatabaseMissing('fridge_items', [
            'id' => $fridgeItem1->id,
        ]);
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem2->id,
        ]);
    }

    /**
     * Verify that deleting another user's fridge item returns not found and
     * leaves the item in the database.
     */
    public function test_user_cannot_delete_another_users_fridge_item(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
            ]);

        $response = $this->actingAs($user2)
            ->delete(route('fridge-items.destroy', $fridgeItem));

        $response->assertNotFound();
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
        ]);
    }

    /**
     * Verify that a guest is redirected to login and cannot delete an item.
     */
    public function test_guest_cannot_delete_a_fridge_item(): void
    {
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays(5),
            ]);

        $response = $this->actingAsGuest()
            ->delete(route('fridge-items.destroy', $fridgeItem));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('fridge_items', [
            'id' => $fridgeItem->id,
        ]);
    }
}
