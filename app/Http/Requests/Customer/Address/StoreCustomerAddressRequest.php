<?php

namespace App\Http\Requests\Customer\Address;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'      => ['required', 'string', 'max:50'],
            'city'       => ['required', 'string', 'max:100'],
            'street'     => ['required', 'string', 'max:255'],
            'building'   => ['required', 'string', 'max:100'],
            'notes'      => ['nullable', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}