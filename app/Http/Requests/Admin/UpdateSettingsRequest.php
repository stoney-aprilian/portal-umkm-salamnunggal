<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Server-side validation for the portal settings page.
 *
 * Text settings are validated per key and file uploads reuse the same
 * image constraints as UMKM/Product media (max 2 MB). The favicon
 * accepts any image (JPG, PNG, WEBP, SVG, ICO). Empty text settings
 * are stored as missing rows so views fall back to their documented
 * defaults and optional sections are hidden.
 */
class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site.name' => ['required', 'string', 'max:255'],
            'site.tagline' => ['nullable', 'string', 'max:255'],
            'site.description' => ['nullable', 'string', 'max:1000'],
            'site.hero_title' => ['required', 'string', 'max:255'],
            'site.hero_description' => ['nullable', 'string', 'max:1000'],
            'contact.address' => ['nullable', 'string', 'max:255'],
            'contact.phone' => ['nullable', 'string', 'max:50'],
            'contact.whatsapp' => ['nullable', 'string', 'max:50'],
            'contact.email' => ['nullable', 'email', 'max:255'],
            'contact.website' => ['nullable', 'url', 'max:255'],
            'contact.hours' => ['nullable', 'string', 'max:255'],
            'contact.maps_url' => ['nullable', 'url', 'max:255'],
            'social.instagram' => ['nullable', 'url', 'max:255'],
            'social.facebook' => ['nullable', 'url', 'max:255'],
            'file_logo' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'],
            'file_favicon' => ['nullable', 'file', 'image', 'max:2048'],
            'file_hero_image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'site.name.required' => 'Nama portal wajib diisi.',
            'site.name.max' => 'Nama portal maksimal 255 karakter.',
            'site.tagline.max' => 'Tagline maksimal 255 karakter.',
            'site.description.max' => 'Deskripsi portal maksimal 1000 karakter.',
            'site.hero_title.required' => 'Judul hero wajib diisi.',
            'site.hero_title.max' => 'Judul hero maksimal 255 karakter.',
            'site.hero_description.max' => 'Deskripsi hero maksimal 1000 karakter.',
            'contact.address.max' => 'Alamat maksimal 255 karakter.',
            'contact.phone.max' => 'Nomor telepon maksimal 50 karakter.',
            'contact.whatsapp.max' => 'Nomor WhatsApp maksimal 50 karakter.',
            'contact.email.email' => 'Email kontak tidak valid.',
            'contact.email.max' => 'Email kontak maksimal 255 karakter.',
            'contact.website.url' => 'Situs web harus berupa URL yang valid (termasuk https://).',
            'contact.hours.max' => 'Jam pelayanan maksimal 255 karakter.',
            'contact.maps_url.url' => 'Tautan Google Maps harus berupa URL yang valid.',
            'social.instagram.url' => 'Tautan Instagram harus berupa URL yang valid.',
            'social.facebook.url' => 'Tautan Facebook harus berupa URL yang valid.',
            'file_logo.image' => 'Logo harus berupa gambar.',
            'file_logo.mimes' => 'Format logo harus JPG, PNG, WEBP, atau SVG.',
            'file_logo.max' => 'Ukuran logo maksimal 2 MB.',
            'file_favicon.image' => 'Favicon harus berupa gambar.',
            'file_favicon.max' => 'Ukuran favicon maksimal 2 MB.',
            'file_hero_image.image' => 'Gambar hero harus berupa gambar.',
            'file_hero_image.mimes' => 'Format gambar hero harus JPG, PNG, atau WEBP.',
            'file_hero_image.max' => 'Ukuran gambar hero maksimal 2 MB.',
        ];
    }
}
