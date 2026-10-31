<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\Normalizations\NormalizesPhoneNumber;
use App\Rules\ValidPhoneNumber;
use App\Traits\Normalizations\NormalizesTime;

class UpdateRestaurantRequest extends FormRequest
{
    use NormalizesPhoneNumber, NormalizesTime;

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

        if ($this->has('phone')) {
            $this->merge([
                'phone' => $this->normalizePhone($this->phone)
            ]);
        }
        if ($this->has('is_open')) {
        $this->merge([
            'is_open' => filter_var($this->is_open, FILTER_VALIDATE_BOOLEAN)
        ]);
        }

        if ($this->has('opening_time')) {
            $this->merge([
                'opening_time' => $this->normalizeTime($this->opening_time)
            ]);
        }

        if ($this->has('closing_time')) {
            $this->merge([
                'closing_time' => $this->normalizeTime($this->closing_time)
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
            'phone' => [
            'sometimes', 
            'required', 
            'string', 
            new ValidPhoneNumber(), 
            Rule::unique('restaurants', 'phone')->ignore($restaurantId)
                        ],
            'opening_time'     => ['sometimes', 'required', 'date_format:H:i'],
            'closing_time'     => ['sometimes', 'required', 'date_format:H:i'],
            'is_open'          => ['sometimes', 'boolean'],
            'min_order_amount' => ['sometimes', 'numeric', 'min:0'],
            'delivery_fee'     => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
