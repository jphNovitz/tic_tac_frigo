<?php

namespace Tests\Feature;

use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FridgeItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_is_persisted_in_database(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.test',
            'password' => Hash::make('password')]);

        $beforeCount = FridgeItem::all()->count();

        FridgeItem::factory()->create([
            'user_id' => $user->id,
            'product_name' => 'Lait demi-écrémé',
            'unit_count' => 2,
            'unit_label' => 'bouteilles',
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertGreaterThan($beforeCount, FridgeItem::all()->count());

    }

    public function test_relation_between_user_and_fridge_items(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.test',
            'password' => Hash::make('password')]);

        $item = FridgeItem::factory()->create([
            'user_id' => $user->id,
            'product_name' => 'Lait demi-écrémé',
            'unit_count' => 2,
            'unit_label' => 'bouteilles',
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertTrue($user->fridgeItems->contains($item));
    }

    public function test_relation_between_item_and_user(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.test',
            'password' => Hash::make('password')]);

        $item = FridgeItem::factory()->create([
            'user_id' => $user->id,
            'product_name' => 'Lait demi-écrémé',
            'unit_count' => 2,
            'unit_label' => 'bouteilles',
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertTrue($item->user->is($user));
    }

    public function test_dates_are_cast_to_carbon_instances(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.test',
            'password' => Hash::make('password')]);

        $item = FridgeItem::factory()->create([
            'user_id' => $user->id,
            'product_name' => 'Lait demi-écrémé',
            'unit_count' => 2,
            'unit_label' => 'bouteilles',
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);

        $this->assertInstanceOf(Carbon::class, $item->expires_at);
    }

    public function test_deleting_user_deletes_their_fridge_items(): void
    {
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.test',
            'password' => Hash::make('password')]);

        $item1 = FridgeItem::factory()->create([
            'user_id' => $user1->id,
            'product_name' => 'Lait',
            'unit_count' => 2,
            'unit_label' => 'bouteilles',
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);
        $user2 = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@doe.test',
            'password' => Hash::make('password')]);

        $item2 = FridgeItem::factory()->create([
            'user_id' => $user2->id,
            'product_name' => 'Lait',
            'unit_count' => 2,
            'unit_label' => 'bouteilles',
            'expires_at' => now()->addDays(7)->toDateString(),
        ]);

        $user1->delete();

        $this->assertTrue(FridgeItem::find($item1->id) === null);
        $this->assertTrue(FridgeItem::find($item2->id) !== null);
    }
}
