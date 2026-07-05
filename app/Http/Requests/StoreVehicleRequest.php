<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plate_number' => ['required', 'string', 'unique:vehicles,plate_number'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color' => ['nullable', 'string', 'max:255'],
            'fuel_type' => ['required', 'string', 'in:gasoline,diesel,electric,hybrid'],
            'engine_number' => ['nullable', 'string', 'max:255'],
            'chassis_number' => ['required', 'string', 'size:17', 'alpha_num', 'unique:vehicles,chassis_number'],
            'current_odometer' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // JPG/PNG, max 5MB
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'plate_number.required' => 'Nomor plat kendaraan harus diisi.',
            'plate_number.unique' => 'Plat nomor sudah terdaftar.',
            'brand.required' => 'Merek kendaraan harus diisi.',
            'model.required' => 'Model kendaraan harus diisi.',
            'year.required' => 'Tahun pembuatan kendaraan harus diisi.',
            'year.integer' => 'Tahun pembuatan harus berupa angka.',
            'year.min' => 'Tahun pembuatan minimal tahun 1900.',
            'year.max' => 'Tahun pembuatan tidak valid.',
            'fuel_type.required' => 'Jenis bahan bakar harus dipilih.',
            'fuel_type.in' => 'Jenis bahan bakar tidak valid.',
            'chassis_number.required' => 'Nomor VIN (nomor rangka) harus diisi.',
            'chassis_number.size' => 'Nomor VIN harus terdiri dari tepat 17 karakter.',
            'chassis_number.alpha_num' => 'Nomor VIN harus berupa karakter alfanumerik (huruf dan angka).',
            'chassis_number.unique' => 'VIN sudah terdaftar.',
            'current_odometer.required' => 'Odometer awal harus diisi.',
            'current_odometer.integer' => 'Odometer awal harus berupa angka.',
            'current_odometer.min' => 'Odometer awal tidak boleh bernilai negatif.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berupa JPEG, JPG, atau PNG.',
            'photo.max' => 'Ukuran foto maksimal adalah 5MB.',
        ];
    }
}
