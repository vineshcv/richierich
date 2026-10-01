<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('username');
            $table->string('status', 20)->default('active')->after('role');
        });

        $used = [];
        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $role = $user->role;
            if ($role === 'admin') {
                $role = $user->store_id ? 'store_admin' : 'superadmin';
            }

            $username = $user->username;
            if (in_array($role, ['superadmin', 'store_admin'], true) && ! $username) {
                $base = Str::lower(Str::before((string) $user->email, '@'));
                $base = preg_replace('/[^a-z0-9._-]/', '', $base) ?: 'admin';
                $candidate = $base;
                $n = 2;
                while (isset($used[$candidate]) || DB::table('users')->where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                    $candidate = $base.$n;
                    $n++;
                }
                $username = $candidate;
            }
            if ($username) {
                $used[$username] = true;
            }

            DB::table('users')->where('id', $user->id)->update([
                'role' => $role,
                'username' => $username,
                'status' => $user->status ?? 'active',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->whereIn('role', ['superadmin', 'store_admin'])->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'phone', 'status']);
        });
    }
};
