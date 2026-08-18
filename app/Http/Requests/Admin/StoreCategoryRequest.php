<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates category creation. The type comes from the route parameter
 * (`/admin/categories/{type}`) and is constrained to `umkm`/`product` by
 * the route definition; the name must be unique within the same type.
 * The slug is generated automatically from the name.
 */
class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->where('type', $this->route('type')),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.unique' => 'Kategori dengan nama ini sudah ada pada jenis yang sama.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ];
    }
}