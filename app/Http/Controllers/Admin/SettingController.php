<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private SettingService $settings)
    {
    }

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => $this->settings->get(),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        return back()->with('success', 'Settings saved.');
    }
}
