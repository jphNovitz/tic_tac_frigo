<?php

namespace Tests\Feature;

use App\Models\FridgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FridgeItemExpirationCountdownTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify that a fridge item which expired yesterday displays the French
     * calendar-day countdown for yesterday.
     */
    public function test_item_expired_yesterday_displays_yesterday_countdown(): void
    {
        $user1 = User::factory()->create();

        $this->travelTo('2026-01-15 12:00:00');
        $expiresAt = today()->subDay();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => 'bouteille',
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText($fridgeItem->product_name);
        $response->assertSeeText('Périmé depuis hier');
    }

    /**
     * Verify that a fridge item which expired several days ago displays the
     * elapsed number of calendar days in French.
     */
    public function test_item_expired_several_days_ago_displays_elapsed_days_countdown(): void
    {
        $user1 = User::factory()->create();

        $elapsedDays = 5;
        $this->travelTo('2026-01-15 12:00:00');
        $expiresAt = today()->subDays($elapsedDays);

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => 'bouteille',
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText($fridgeItem->product_name);
        $response->assertSeeText('Périmé depuis 5 jours');
    }

    /**
     * Verify that a fridge item expiring today displays the French countdown
     * for today.
     */
    public function test_item_expiring_today_displays_today_countdown(): void
    {
        $user1 = User::factory()->create();

        $this->travelTo('2026-01-15 12:00:00');
        $expiresAt = today();

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => 'bouteille',
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText($fridgeItem->product_name);
        $response->assertSeeText("Expire aujourd'hui");
    }

    /**
     * Verify that a fridge item expiring tomorrow displays the French
     * countdown for tomorrow.
     */
    public function test_item_expiring_tomorrow_displays_tomorrow_countdown(): void
    {
        $user1 = User::factory()->create();

        $this->travelTo('2026-01-15 12:00:00');
        $expiresAt = today()->addDays(1);

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => 'bouteille',
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText($fridgeItem->product_name);
        $response->assertSeeText('Expire demain');
    }

    /**
     * Verify that a fridge item expiring in several days displays the
     * remaining number of calendar days in French.
     */
    public function test_item_expiring_in_several_days_displays_remaining_days_countdown(): void
    {
        $user1 = User::factory()->create();

        $daysUntilExpiration = 5;
        $this->travelTo('2026-01-15 12:00:00');
        $expiresAt = today()->addDays($daysUntilExpiration);

        $fridgeItem = FridgeItem::factory()->for($user1)
            ->create([
                'product_name' => 'Kinder',
                'expires_at' => $expiresAt,
                'unit_count' => 1,
                'unit_label' => 'bouteille',
            ]);

        $response = $this->actingAs($user1)->get(route('fridge-items.index'));

        $response->assertOk();
        $response->assertSeeText($fridgeItem->product_name);
        $response->assertSeeText('Expire dans 5 jours');
    }
}
