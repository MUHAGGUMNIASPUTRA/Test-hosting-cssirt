<?php

// Tujuan: Membatasi akses seluruh grup route /admin/* hanya untuk role staff atau admin.
// Caller: bootstrap/app.php (alias 'staff'), dipasang di routes/web.php grup prefix('admin').
// Side Effects: abort(403) jika role bukan staff/admin.

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = auth()->user()?->role;

        if (! auth()->check() || ! in_array($role, [UserRole::Admin, UserRole::Staff], true)) {
            abort(403, 'AKSES DITOLAK. HANYA UNTUK STAFF/ADMIN.');
        }

        return $next($request);
    }
}
