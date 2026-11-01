<?php

namespace Tests\Feature;

use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FridgeItemListingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that an authenticated user sees their active fridge items ordered
     * by ascending expiration date.
     */
    public function test_authenticated_user_sees_their_active_fridge_items_in_expiration_order(): void
    {
        $user1 = User::factory()->create();
        $longTimeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Longtime product',
                'expires_at' => now()->addMonth(),
            ]);
        $urgentItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Urgent product',
                'expires_at' => now()->addDay(),
            ]);
        $soonItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Soon product',
                'expires_at' => now()->addWeek(),
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));
        $response->assertOk();
        $response->assertSeeTextInOrder([
            $urgentItem->product_name,
            $soonItem->product_name,
            $longTimeItem->product_name,
        ]);
    }

    /**
     * Verify that consumed fridge items are excluded from the active fridge
     * item list.
     */
    public function test_consumed_fridge_items_are_not_displayed(): void
    {
        $user1 = User::factory()->create();
        $availableItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Longtime product',
                'expires_at' => now()->addMonth(),
            ]);
        $consumedItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Urgent product',
                'expires_at' => now()->addMonth(),
                'consumed_at' => now(),
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));
        $response->assertOk();
        $response->assertSeeText($availableItem->product_name);
        $response->assertDontSeeText($consumedItem->product_name);
    }

    /**
     * Verify that an authenticated user cannot see fridge items owned by
     * another user.
     */
    public function test_user_cannot_see_another_users_fridge_items(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1Item = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'User 1 product',
                'expires_at' => now()->addMonth(),
            ]);
        $user2Item = FridgeItem::factory()->for($user2)
            ->create([
                'product_name' => 'User 2 product',
                'expires_at' => now()->addMonth(),
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));
        $response->assertOk();
        $response->assertSeeText($user1Item->product_name);
        $response->assertDontSeeText($user2Item->product_name);

        $response = $this->actingAs($user2)->get(route('fridge-items.index'));
        $response->assertOk();
        $response->assertSeeText($user2Item->product_name);
        $response->assertDontSeeText($user1Item->product_name);

    }

    /**
     * Verify that fridge items display the expected status color according to
     * their expiration date.
     */
    #[DataProvider('expirationStatusCases')]
    public function test_fridge_items_display_the_expected_status_color_for_their_expiration_date(int $daysFromToday,
        string $expectedStatusClass, ): void
    {
        $this->travelTo('2026-01-15 12:00:00');
        $user1 = User::factory()->create();
        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => today()->addDays($daysFromToday),
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText($fridgeItem->product_name);
        $response->assertSee($expectedStatusClass);

        $statusClasses = [
            'status-error',
            'status-warning',
            'status-success',
        ];

        foreach ($statusClasses as $statusClass) {
            if ($statusClass === $expectedStatusClass) {
                continue;
            }

            $response->assertDontSee($statusClass);
        }

    }

    public static function expirationStatusCases(): array
    {
        return [
            'expired yesterday' => [-1, 'status-error'],
            'expires today' => [0, 'status-warning'],
            'expires in thirteen days' => [13, 'status-warning'],
            'expires in fourteen days' => [14, 'status-success'],
        ];
    }
}
