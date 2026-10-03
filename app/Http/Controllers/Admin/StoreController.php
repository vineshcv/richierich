<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\CurrentStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(private CurrentStore $current)
    {
    }

    public function index(): View
    {
        return view('admin.stores.index', [
            'stores' => Store::query()->orderByDesc('is_default')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.stores.form', [
            'store' => new Store(['is_default' => Store::query()->doesntExist()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateStore($request);
        $data['is_default'] = Store::query()->doesntExist();

        $store = Store::query()->create($data);
        session(['admin_store_scope' => $store->id]);

        return redirect()->route('admin.stores.index')->with('success', 'Store created.');
    }

    public function edit(Store $store): View
    {
        return view('admin.stores.form', [
            'store' => $store,
        ]);
    }

    public function update(Request $request, Store $store): RedirectResponse
    {
        $store->update($this->validateStore($request));

        return redirect()->route('admin.stores.index')->with('success', 'Store updated.');
    }

    public function destroy(Store $store): RedirectResponse
    {
        if ($store->products()->exists() || $store->categories()->exists() || $store->orders()->exists()) {
            return back()->withErrors(['store' => 'Move or delete this store’s products, categories, and orders before deleting it.']);
        }

        if (Store::query()->count() <= 1) {
            return back()->withErrors(['store' => 'Keep at least one store.']);
        }

        $wasDefault = $store->is_default;
        $store->delete();

        if ($wasDefault) {
            Store::query()->orderBy('id')->limit(1)->update(['is_default' => true]);
        }

        if ((int) session('admin_store_scope') === $store->id) {
            session(['admin_store_scope' => 'all']);
        }

        return redirect()->route('admin.stores.index')->with('success', 'Store deleted.');
    }

    public function switch(Request $request): RedirectResponse
    {
        if (! $this->current->canSwitch()) {
            abort(403);
        }

        if ($request->input('store_id') === 'all') {
            session(['admin_store_scope' => 'all']);

            return back();
        }

        $data = $request->validate([
            'store_id' => ['required', 'integer', 'exists:stores,id'],
        ]);

        session(['admin_store_scope' => (int) $data['store_id']]);

        return back();
    }

    private function validateStore(Request $request): array
    {
        $request->merge([
            'whatsapp_number' => preg_replace('/\D+/', '', (string) $request->input('whatsapp_number')) ?: null,
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp_number' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'pin' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
        ], [
            'pin.regex' => 'Enter a 6-digit PIN code.',
            'whatsapp_number.regex' => 'Enter the WhatsApp number with country code, digits only.',
        ]);

        $data['phone'] = trim((string) ($data['phone'] ?? '')) ?: null;

        return $data;
    }
}
