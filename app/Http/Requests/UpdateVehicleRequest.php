<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->route('vehicle')->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color' => ['nullable', 'string', 'max:255'],
            'fuel_type' => ['required', 'string', 'in:gasoline,diesel,electric,hybrid'],
            'engine_number' => ['nullable', 'string', 'max:255'],
            'current_odometer' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'brand.required' => 'Merek kendaraan harus diisi.',
            'model.required' => 'Model kendaraan harus diisi.',
            'year.required' => 'Tahun pembuatan kendaraan harus diisi.',
            'year.integer' => 'Tahun pembuatan harus berupa angka.',
            'year.min' => 'Tahun pembuatan minimal tahun 1900.',
            'year.max' => 'Tahun pembuatan tidak valid.',
            'fuel_type.required' => 'Jenis bahan bakar harus dipilih.',
            'fuel_type.in' => 'Jenis bahan bakar tidak valid.',
            'current_odometer.required' => 'Odometer harus diisi.',
            'current_odometer.integer' => 'Odometer harus berupa angka.',
            'current_odometer.min' => 'Odometer tidak boleh bernilai negatif.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berupa JPEG, JPG, atau PNG.',
            'photo.max' => 'Ukuran foto maksimal adalah 5MB.',
        ];
    }
}
