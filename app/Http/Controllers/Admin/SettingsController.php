<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

/**
 * Administrator management of portal-level operational settings and
 * content (identity, hero section, official contacts, social media)
 * plus branding assets (logo, favicon, hero image).
 *
 * Text settings live in the existing `settings` table. Branding files
 * are stored on the public disk under `branding/`; replacing or
 * removing an asset deletes the previous file so no orphan files are
 * left behind. An empty text value removes the row so public views
 * fall back to their documented defaults.
 */
class SettingsController extends Controller
{
    private const TEXT_KEYS = [
        'site.name',
        'site.tagline',
        'site.description',
        'site.hero_title',
        'site.hero_description',
        'contact.address',
        'contact.phone',
        'contact.whatsapp',
        'contact.email',
        'contact.website',
        'contact.hours',
        'contact.maps_url',
        'social.instagram',
        'social.facebook',
    ];

    private const DEFAULT_VALUES = [
        'site.name' => 'Portal UMKM Salamnunggal',
        'site.tagline' => 'Desa Salamnunggal',
        'site.hero_title' => 'Portal UMKM Desa Salamnunggal',
    ];

    private const ASSET_FIELDS = [
        'site.logo' => 'file_logo',
        'site.favicon' => 'file_favicon',
        'site.hero_image' => 'file_hero_image',
    ];

    public function index(): View
    {
        $stored = Setting::query()->pluck('value', 'key');

        return view('admin.settings.index', [
            'settings' => collect(self::DEFAULT_VALUES)->merge($stored),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        foreach (self::TEXT_KEYS as $key) {
            if (! Arr::has($data, $key)) {
                continue;
            }

            $value = trim((string) Arr::get($data, $key));

            if ($value === '') {
                Setting::where('key', $key)->delete();
            } else {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => explode('.', $key)[0]],
                );
            }
        }

        foreach (self::ASSET_FIELDS as $key => $input) {
            if ($request->hasFile($input)) {
                $this->replaceAsset($key, $request->file($input)->store('branding', 'public'));
            } elseif ($request->boolean('remove_'.$this->assetName($key))) {
                $this->deleteAsset($key);
            }
        }

        return redirect()->back()
            ->with('status', 'Pengaturan portal berhasil disimpan.');
    }

    /**
     * Stores the new file first, then swaps the setting value inside a
     * transaction, deleting the previous file. A failed database write
     * never leaves an orphan file.
     */
    private function replaceAsset(string $key, string $path): void
    {
        try {
            DB::transaction(function () use ($key, $path) {
                $old = Setting::where('key', $key)->value('value');

                if ($old !== null && $old !== '') {
                    Storage::disk('public')->delete($old);
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $path, 'group' => 'site'],
                );
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }
    }

    private function deleteAsset(string $key): void
    {
        DB::transaction(function () use ($key) {
            $old = Setting::where('key', $key)->value('value');

            if ($old !== null && $old !== '') {
                Storage::disk('public')->delete($old);
            }

            Setting::where('key', $key)->delete();
        });
    }

    private function assetName(string $key): string
    {
        return substr($key, strrpos($key, '.') + 1);
    }
}
