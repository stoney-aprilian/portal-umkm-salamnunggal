<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Server-side upload validation for UMKM and Product media.
 *
 * The input name is derived from the route collection parameter
 * (file_logo, file_banner, file_product) so validation errors stay
 * scoped to the matching form on the page.
 */
class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->route('collection') === 'gallery') {
            return [
                'gallery' => ['required', 'array', 'max:5'],
                'gallery.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            ];
        }

        return [
            $this->inputName() => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        if ($this->route('collection') === 'gallery') {
            return [
                'gallery.required' => 'Pilih minimal satu gambar untuk galeri.',
                'gallery.array' => 'Data galeri tidak valid.',
                'gallery.max' => 'Maksimal 5 gambar dalam satu unggahan.',
                'gallery.*.image' => 'Semua file harus berupa gambar.',
                'gallery.*.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
                'gallery.*.max' => 'Ukuran setiap gambar maksimal 2 MB.',
            ];
        }

        $key = $this->inputName();
        $label = $this->label();

        return [
            $key.'.required' => $label.' wajib dipilih.',
            $key.'.image' => 'File harus berupa gambar.',
            $key.'.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            $key.'.max' => 'Ukuran file maksimal 2 MB.',
        ];
    }

    /**
     * The file input name used by the matching form on the edit page.
     */
    public function inputName(): string
    {
        return 'file_'.$this->route('collection');
    }

    private function label(): string
    {
        return match ($this->route('collection')) {
            'logo' => 'Logo',
            'banner' => 'Banner',
            'product' => 'Foto produk',
            default => 'File',
        };
    }
}
