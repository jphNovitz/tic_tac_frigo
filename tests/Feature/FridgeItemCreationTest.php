<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FridgeItemCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that an authenticated user can see every field and the submit
     * button in the add-fridge-item form.
     */
    public function test_authenticated_user_can_see_the_add_fridge_item_form(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->get(route('fridge-items.create'));

        $response->assertOk();
        $response->assertSee('name="product_name"', false);
        $response->assertSee('name="expires_at"', false);
        $response->assertSee('name="unit_count"', false);
        $response->assertSee('name="unit_label"', false);
        $response->assertSee('type="submit"', false);
    }

    /**
     * Verify that a guest cannot submit the add-fridge-item form and is
     * redirected to the login page.
     */
    public function test_guest_cannot_add_a_fridge_item(): void
    {
        $response = $this->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 10,
            'unit_label' => 'pièces',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('fridge_items');
    }

    /**
     * Verify that a valid request creates a fridge item owned by the
     * authenticated user and returns the expected redirect.
     */
    public function test_authenticated_user_can_add_a_fridge_item(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 10,
            'unit_label' => 'pièces',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('fridge_items', [
            'user_id' => $user1->id,
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 10,
            'unit_label' => 'pièces',
        ]);
    }

    /**
     * Verify that missing required fields are rejected and that no fridge
     * item is persisted.
     */
    public function test_required_fields_are_validated_when_adding_a_fridge_item(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => '',
            'expires_at' => null,
            'unit_count' => null,
        ]);

        $response->assertSessionHasErrors([
            'product_name',
            'expires_at',
            'unit_count',
        ]);

        $this->assertDatabaseCount('fridge_items', 0);
    }

    /**
     * Verify that a unit count lower than one is rejected and that no fridge
     * item is persisted.
     */
    public function test_unit_count_must_be_at_least_one(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 0,
            'unit_label' => 'pièces',
        ]);

        $response->assertSessionHasErrors(['unit_count']);
        $this->assertDatabaseCount('fridge_items', 0);
    }

    /**
     * Verify that a fridge item can be created without a unit label.
     */
    public function test_unit_label_is_optional(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('fridge_items', 1);
        $this->assertDatabaseHas('fridge_items', [
            'user_id' => $user1->id,
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1,
            'unit_label' => null,
        ]);
    }

    /**
     * Verify that expires_at must be a valid date.
     */
    public function test_invalid_expires_at_date_is_rejected(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => 'not-a-date',
            'unit_count' => 1,
        ]);

        $response->assertSessionHasErrors('expires_at');
        $response->assertRedirectBack();
        $this->assertDatabaseEmpty('fridge_items');
    }

    /**
     * Verify that unit count must be an integer.
     */
    public function test_unit_count_is_integer(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1.5,
        ]);

        $response->assertSessionHasErrors('unit_count');
        $response->assertRedirectBack();
        $this->assertDatabaseEmpty('fridge_items');
    }

    /**
     * Verify that product name cannot exceed 255 characters.
     */
    public function test_product_name_cannot_exceed_255_characters(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => str_repeat('a', 256),
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1,
        ]);

        $response->assertSessionHasErrors('product_name');
        $response->assertRedirectBack();
        $this->assertDatabaseEmpty('fridge_items');
    }

    /**
     * Verify that unit label cannot exceed 255 characters.
     */
    public function test_unit_label_cannot_exceed_255_characters(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1,
            'unit_label' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors('unit_label');
        $response->assertRedirectBack();
        $this->assertDatabaseEmpty('fridge_items');
    }

    /**
     * Verify that the same product can be added twice with different expiration dates.
     */
    public function test_same_product_can_be_added_twice_with_different_expiration_dates(): void
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1,
            'unit_label' => 'pièces',
        ]);
        $response = $this->actingAs($user1)->post(route('fridge-items.store'), [
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(8)->toDateString(),
            'unit_count' => 1,
            'unit_label' => 'pièces',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('fridge_items', 2);
        $this->assertDatabaseHas('fridge_items', [
            'user_id' => $user1->id,
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(7)->toDateString(),
            'unit_count' => 1,
            'unit_label' => 'pièces',
        ]);

        $this->assertDatabaseHas('fridge_items', [
            'user_id' => $user1->id,
            'product_name' => 'Kinder',
            'expires_at' => now()->addDays(8)->toDateString(),
            'unit_count' => 1,
            'unit_label' => 'pièces',
        ]);
    }
}
