<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InitiateTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_identifier' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'recipient_identifier.required' => 'Email atau nomor telepon penerima harus diisi.',
            'recipient_identifier.string' => 'Format email atau nomor telepon tidak valid.',
            'recipient_identifier.max' => 'Email atau nomor telepon terlalu panjang.',
        ];
    }

    /**
     * Determine if the identifier looks like an email address.
     */
    public function isEmail(): bool
    {
        return filter_var($this->recipient_identifier, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Determine if the identifier looks like a phone number.
     */
    public function isPhone(): bool
    {
        // Accept formats: 08xx, +628xx, 628xx (Indonesian phone numbers)
        return (bool) preg_match('/^(\+?62|0)[0-9]{8,13}$/', preg_replace('/[\s\-]/', '', $this->recipient_identifier));
    }
}
