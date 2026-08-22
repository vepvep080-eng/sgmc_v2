<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function update(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['fr', 'ar'], true), 404);

        $request->session()->put('locale', $locale);

        $previous = url()->previous();
        $applicationUrl = rtrim(url('/'), '/');

        return redirect()->to(
            $previous === $applicationUrl || str_starts_with($previous, $applicationUrl.'/')
                ? $previous
                : route('login'),
        );
    }
}
