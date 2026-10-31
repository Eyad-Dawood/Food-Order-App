<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(
    'manager_id',
    'name',
    'slug',
    'description',
    'cuisine',
    'logo_path',
    'phone',
    'opening_time',
    'closing_time',
    'is_open',
    'is_active',
    'min_order_amount',
    'delivery_fee',
)]
class Restaurant extends Model
{
    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'is_active' => 'boolean',
            'min_order_amount' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'avg_rating' => 'decimal:2',
            'ratings_count' => 'integer',
            'opening_time' => 'datetime:H:i',
            'closing_time' => 'datetime:H:i',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
