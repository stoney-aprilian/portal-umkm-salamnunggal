<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $settings = Setting::query()
            ->whereIn('key', ['site.name', 'contact.address', 'contact.phone', 'contact.email', 'contact.hours'])
            ->pluck('value', 'key');

        return view('public.contact', ['settings' => $settings]);
    }
}
