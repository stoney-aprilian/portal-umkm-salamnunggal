<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'umkm_id' => [
                'required',
                'integer',
                Rule::exists('umkms', 'id')->where('status', 'approved'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('type', 'product'),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'regex:/^(?:\d{1,8})(?:\.\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'umkm_id.required' => 'UMKM wajib dipilih.',
            'umkm_id.integer' => 'UMKM yang dipilih tidak valid.',
            'umkm_id.exists' => 'UMKM yang dipilih tidak valid.',
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.integer' => 'Kategori yang dipilih tidak valid.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'price.regex' => 'Harga maksimal 2 angka di belakang koma dan tidak melebihi 99.999.999,99.',
        ];
    }
}