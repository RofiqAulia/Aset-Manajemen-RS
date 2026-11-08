<?php

declare(strict_types=1);

namespace App\Http\Requests\Asset;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'acquisition_date' => ['nullable', 'date'],
            'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
            'funding_source' => ['nullable', 'string', 'max:255'],
            'warranty_expiry_date' => ['nullable', 'date', 'after_or_equal:acquisition_date'],
            'condition' => ['nullable', Rule::enum(AssetCondition::class)],
            'status' => ['nullable', Rule::enum(AssetStatus::class)],
            'is_critical' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama aset wajib diisi.',
            'category_id.required' => 'Kategori aset wajib dipilih.',
            'category_id.exists' => 'Kategori aset yang dipilih tidak terdaftar.',
            'location_id.required' => 'Ruangan/lokasi aset wajib dipilih.',
            'location_id.exists' => 'Ruangan/lokasi aset tidak terdaftar.',
            'department_id.required' => 'Unit penanggung jawab wajib dipilih.',
            'department_id.exists' => 'Unit penanggung jawab tidak terdaftar.',
            'acquisition_cost.numeric' => 'Biaya perolehan harus berupa angka numerik.',
            'warranty_expiry_date.after_or_equal' => 'Masa garansi berakhir tidak boleh lebih awal dari tanggal perolehan.',
        ];
    }
}
