<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Contoh: 'role:admin' (role persis), 'role:admin,guru' (salah satu role),
     * 'role:bk' (user dengan penugasan bk, biasanya role guru),
     * 'role:wali_kelas' (role wali_kelas ATAU guru yang ditunjuk jadi wali).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user) {
            foreach ($roles as $role) {
                if ($user->role === $role) {
                    return $next($request);
                }

                // 'bk' bukan role, melainkan penugasan pada user (mis. role guru + penugasan bk).
                if ($role === 'bk' && $user->bertugasSebagai('bk')) {
                    return $next($request);
                }

                // Wali kelas dilihat dari penunjukan di tabel kelas, bukan sekadar role.
                if ($role === 'wali_kelas' && $user->isWaliKelas()) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
