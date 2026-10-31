<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRestaurantRequest extends FormRequest
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
        return [
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:255', 'unique:restaurants,slug'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'cuisine'          => ['nullable', 'string', 'max:100'],
            'logo'             => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], //uploads images only, max size 2MB 
            'phone'            => ['required', 'string', 'max:20'], 
            'opening_time'     => ['required', 'date_format:H:i'],
            'closing_time'     => ['required', 'date_format:H:i'], 
            'is_open'          => ['sometimes', 'boolean'],
            'min_order_amount' => ['sometimes', 'numeric', 'min:0'],
            'delivery_fee'     => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
