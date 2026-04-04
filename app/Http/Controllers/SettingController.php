<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $defaultPrintCopies = Setting::get('default_print_copies', 1);

        return view('settings.index', compact('defaultPrintCopies'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'default_print_copies' => 'required|integer|min:1|max:20',
        ]);

        Setting::set('default_print_copies', $request->default_print_copies);

        return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
