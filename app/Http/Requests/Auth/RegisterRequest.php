<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    private $phoneNumberRegx = '/^\+20(10|11|12|15)[0-9]{8}$/';
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $phone = $this->phone;

            if (str_starts_with($phone, '01')) {
                $phone = '+2' . $phone;
            } 
            elseif (str_starts_with($phone, '201')) {
                $phone = '+' . $phone;
            }

            $this->merge([
                'phone' => $phone,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone'    => ['required', 'string', 'regex:' . $this->phoneNumberRegx, 'unique:users,phone'],
        ];
    }
}
