<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $settings = Setting::query()->pluck('value', 'key');

        return view('public.contact', ['settings' => $settings]);
    }
}
