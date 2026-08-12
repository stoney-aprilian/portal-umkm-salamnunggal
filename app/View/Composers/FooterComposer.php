<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view): void
    {
        $settings = Setting::query()
            ->whereIn('key', ['site.name', 'contact.address', 'contact.phone', 'contact.email', 'contact.hours'])
            ->pluck('value', 'key');

        $view->with('settings', $settings);
    }
}
