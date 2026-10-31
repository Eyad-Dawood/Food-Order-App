<?php

namespace App\Http\Requests\Customer\Address;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label'      => ['sometimes', 'required', 'string', 'max:50'],
            'city'       => ['sometimes', 'required', 'string', 'max:100'],
            'street'     => ['sometimes', 'required', 'string', 'max:255'],
            'building'   => ['sometimes', 'required', 'string', 'max:100'],
            'notes'      => ['nullable', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}