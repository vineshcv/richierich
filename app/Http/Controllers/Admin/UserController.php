<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->with('store')
                ->whereIn('role', ['superadmin', 'store_admin'])
                ->orderBy('username')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['status' => 'active', 'role' => 'store_admin']),
            'stores' => Store::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request);
        $data['email'] = $data['username'].'@staff.richierich.local';
        $data['name'] = $data['username'];
        User::query()->create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        abort_unless(in_array($user->role, ['superadmin', 'store_admin'], true), 404);

        return view('admin.users.form', [
            'user' => $user,
            'stores' => Store::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['superadmin', 'store_admin'], true), 404);

        $data = $this->validateUser($request, $user);
        if ($data['password'] === null || $data['password'] === '') {
            unset($data['password']);
        }
        $data['name'] = $data['username'];
        if (! $user->email || str_ends_with((string) $user->email, '@staff.richierich.local')) {
            $data['email'] = $data['username'].'@staff.richierich.local';
        }
        $this->guardLastSuperAdmin($user, $data);
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['superadmin', 'store_admin'], true), 404);

        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'You cannot delete the account you are using.']);
        }
        if ($user->role === 'superadmin' && $this->superAdminCount() <= 1) {
            return back()->withErrors(['user' => 'Keep at least one active superadmin.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    /** @return array<string, mixed> */
    private function validateUser(Request $request, ?User $user = null): array
    {
        if ($request->input('store_id') === '') {
            $request->merge(['store_id' => null]);
        }

        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:40', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'role' => ['required', Rule::in(['superadmin', 'store_admin'])],
            'store_id' => ['nullable', 'integer', 'exists:stores,id', 'required_if:role,store_admin'],
        ]);

        $data['store_id'] = $data['role'] === 'store_admin' ? ($data['store_id'] ?? null) : null;

        return $data;
    }

    /** @param  array<string, mixed>  $data */
    private function guardLastSuperAdmin(User $user, array $data): void
    {
        $leaving = $user->role === 'superadmin' && $user->status === 'active'
            && ($data['role'] !== 'superadmin' || $data['status'] !== 'active');

        if ($leaving && $this->superAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'role' => 'Keep at least one active superadmin.',
            ]);
        }
    }

    private function superAdminCount(): int
    {
        return User::query()->where('role', 'superadmin')->where('status', 'active')->count();
    }
}
