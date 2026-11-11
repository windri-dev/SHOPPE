<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application language between Indonesian (id) and English (en).
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, ['id', 'en'], true)) {
            session(['locale' => $locale]);
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));
            app()->setLocale($locale);
        }

        return back();
    }
}
