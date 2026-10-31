<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'description'      => $this->description,
            'cuisine'          => $this->cuisine,
            'phone'            => $this->phone,
            'opening_time'     => $this->opening_time,
            'closing_time'     => $this->closing_time,
            'is_open'          => $this->is_open,
            'is_active'        => $this->is_active,
            'min_order_amount' => (float) $this->min_order_amount,
            'delivery_fee'     => (float) $this->delivery_fee,
            'manager_id'       => $this->manager_id,
            'logo_url'         => $this->logo_path ? asset('storage/' . $this->logo_path) : null,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
