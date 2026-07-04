<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LecturerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $lecturer = session('lecturer');

        if (! $lecturer || empty($lecturer['email'])) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
