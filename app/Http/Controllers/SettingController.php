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
        $detailedCopies = Setting::get('default_print_copies_detailed', 1);
        $summaryCopies = Setting::get('default_print_copies_summary', 1);

        return view('settings.index', compact('detailedCopies', 'summaryCopies'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'default_print_copies_detailed' => 'required|integer|min:1|max:20',
            'default_print_copies_summary' => 'required|integer|min:1|max:20',
        ]);

        Setting::set('default_print_copies_detailed', $request->default_print_copies_detailed);
        Setting::set('default_print_copies_summary', $request->default_print_copies_summary);

        return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
