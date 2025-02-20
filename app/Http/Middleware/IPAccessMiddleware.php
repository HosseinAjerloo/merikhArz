<?php

namespace App\Http\Middleware;

use App\Models\IP;
use App\Services\IPAccess\IPService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class IPAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('ip_access_last_check') < Carbon::now()->subHours()) {
            $ip_is_iran = IPService::check_ip($request->ip());
            session(['ip_access_last_check'=>Carbon::now()]);
            if (!$ip_is_iran)
                return response()->view('errors.IPAccess');
        }
        return $next($request);
    }
}
