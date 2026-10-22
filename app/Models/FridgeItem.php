<?php

namespace App\Models;

use Database\Factories\FridgeItemFactory;
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
}
