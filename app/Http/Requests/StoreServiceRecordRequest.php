<?php

namespace App\Http\Requests;

use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled via route middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehicleId = $this->route('vehicle') ?? $this->input('vehicle_id');

        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'service_type' => ['required', 'string', 'in:'.implode(',', array_keys(ServiceRecord::SERVICE_TYPES))],
            'service_date' => ['required', 'date', 'before_or_equal:today'],
            'odometer_at_service' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $vehicle = Vehicle::find($this->input('vehicle_id'));
                    if ($vehicle) {
                        $serviceRecordId = $this->route('service_record')?->id;
                        if ($serviceRecordId) {
                            $maxPriorOdometer = $vehicle->serviceRecords()
                                ->where('id', '!=', $serviceRecordId)
                                ->max('odometer_at_service');
                            if ($maxPriorOdometer !== null && (int) $value < $maxPriorOdometer) {
                                $fail("Odometer tidak boleh kurang dari odometer sebelum service ini ({$maxPriorOdometer} km).");
                            }
                        } else {
                            if ((int) $value < $vehicle->current_odometer) {
                                $fail("Odometer tidak boleh kurang dari odometer terakhir kendaraan ({$vehicle->current_odometer} km).");
                            }
                        }
                    }
                },
            ],
            'mechanic_notes' => ['nullable', 'string', 'max:2000'],
            'total_cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:completed,in_progress'],

            // Spareparts (optional, dynamic rows)
            'parts' => ['nullable', 'array'],
            'parts.*.part_name' => ['required_with:parts', 'string', 'max:255'],
            'parts.*.quantity' => ['required_with:parts', 'integer', 'min:1'],
            'parts.*.unit_price' => ['required_with:parts', 'numeric', 'min:0'],
            'parts.*.part_category' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Kendaraan harus dipilih.',
            'vehicle_id.exists' => 'Kendaraan tidak ditemukan.',
            'service_type.required' => 'Jenis service harus dipilih.',
            'service_type.in' => 'Jenis service tidak valid.',
            'service_date.required' => 'Tanggal service harus diisi.',
            'service_date.before_or_equal' => 'Tanggal service tidak boleh di masa depan.',
            'odometer_at_service.required' => 'Odometer saat service harus diisi.',
            'odometer_at_service.min' => 'Odometer tidak boleh negatif.',
            'total_cost.required' => 'Total biaya harus diisi.',
            'total_cost.min' => 'Total biaya tidak boleh negatif.',
            'parts.*.part_name.required_with' => 'Nama sparepart harus diisi.',
            'parts.*.quantity.required_with' => 'Jumlah sparepart harus diisi.',
            'parts.*.unit_price.required_with' => 'Harga satuan sparepart harus diisi.',
        ];
    }
}
