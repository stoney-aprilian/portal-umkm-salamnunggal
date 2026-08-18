<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

/**
 * Shares the portal settings (key => value) with every layout that
 * renders portal branding: meta description, favicon, navigation,
 * guest authentication pages, and the footer. Views always fall back
 * to a documented default when a key is missing, so deleting a
 * setting never breaks a page.
 */
class PortalSettingsComposer
{
    public function compose(View $view): void
    {
        $view->with('settings', Setting::query()->pluck('value', 'key'));
    }
}
