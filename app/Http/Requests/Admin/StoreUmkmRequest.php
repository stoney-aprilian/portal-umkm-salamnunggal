<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreUmkmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['required', 'integer', 'exists:users,id', Rule::unique('umkms', 'user_id')],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('type', 'umkm'),
            ],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'google_maps' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'operational_hours' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $owner = User::find($this->input('owner_id'));

                if ($owner !== null && ! $owner->hasRole('owner')) {
                    $validator->errors()->add('owner_id', 'Owner yang dipilih tidak valid.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'owner_id.required' => 'Owner wajib dipilih.',
            'owner_id.integer' => 'Owner yang dipilih tidak valid.',
            'owner_id.exists' => 'Owner yang dipilih tidak valid.',
            'owner_id.unique' => 'Owner yang dipilih sudah memiliki UMKM.',
            'name.required' => 'Nama UMKM wajib diisi.',
            'name.string' => 'Nama UMKM harus berupa teks.',
            'name.max' => 'Nama UMKM maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.integer' => 'Kategori yang dipilih tidak valid.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'google_maps.url' => 'Link Google Maps harus berupa URL yang valid (http/https).',
            'website.url' => 'Link website harus berupa URL yang valid (http/https).',
        ];
    }
}