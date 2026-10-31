<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class UpdateRestaurantRequest extends FormRequest
{
public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name)
            ]);
        }
    }

    public function rules(): array
    {
        $restaurantId = $this->route('id'); // Get the restaurant ID from the route parameter 

        return [
            'name'             => ['sometimes', 'required', 'string', 'max:255'],
            'slug'             => ['sometimes', 'required', 'string', 'max:255', Rule::unique('restaurants')->ignore($restaurantId)],
            'description'      => ['nullable', 'string', 'max:1000'],
            'cuisine'          => ['nullable', 'string', 'max:100'],
            'logo'             => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'phone'            => ['sometimes', 'required', 'string', 'max:20'],
            'opening_time'     => ['sometimes', 'required', 'date_format:H:i'],
            'closing_time'     => ['sometimes', 'required', 'date_format:H:i'],
            'is_open'          => ['sometimes', 'boolean'],
            'min_order_amount' => ['sometimes', 'numeric', 'min:0'],
            'delivery_fee'     => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
