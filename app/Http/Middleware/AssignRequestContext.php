<?php

// Tujuan: Menetapkan request_id unik + user_id (jika login) + ip ke log context tiap request.
// Caller: bootstrap/app.php (web middleware stack, append).
// Side Effects: Context::add (ikut ke semua log entry request ini), tambah header X-Request-Id ke response.

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();

        Context::add('request_id', $requestId);
        Context::add('ip', $request->ip());
        Context::addIf('user_id', $request->user()?->id);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
