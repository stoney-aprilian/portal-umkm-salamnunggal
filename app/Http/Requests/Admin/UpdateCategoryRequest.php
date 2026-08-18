<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates category updates. The type is never accepted here: the
 * `type` column cannot be manipulated through the edit form, keeping
 * UMKM categories strictly separate from product categories.
 */
class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->where('type', $category->type)->ignore($category->id),
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