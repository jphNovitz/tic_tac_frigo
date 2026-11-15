<?php

namespace App\Models;

use Database\Factories\FridgeItemFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FridgeItem extends Model
{
    /** @use HasFactory<FridgeItemFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'expires_at',
        'consumed_at',
        'alerted_at',
        'unit_count',
        'unit_label',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit_count' => 'integer',
        'expires_at' => 'date:d-m-Y',
        'consumed_at' => 'datetime:d-m-Y',
        'alerted_at' => 'datetime:d-m-Y',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function expireAlert(): Attribute
    {
        return Attribute::get(function () {
            $date = $this->expires_at;

            $diff = (int) today()->diffInDays($date, false);
            $days = abs($diff);

            return match (true) {
                $diff === 0 => [
                    'diff' => $diff,
                    'label' => __('status.expiration.today'),
                ],
                $diff === -1 => [
                    'diff' => $diff,
                    'label' => __('status.expiration.yesterday'),
                ],
                $diff < -1 => [
                    'diff' => $diff,
                    'label' => trans_choice('status.expiration.expired_days', $days, ['days' => $days]),
                ],
                $diff === 1 => [
                    'diff' => $diff,
                    'label' => __('status.expiration.tomorrow'),
                ],
                default => [
                    'diff' => $diff,
                    'label' => trans_choice('status.expiration.expires_in_days', $days, ['days' => $days]),
                ],
            };

        });
    }
}
