<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 检查用户是否已登录
        if (!auth()->check()) {
            \Log::warning('Unauthorized access attempt to admin area', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'timestamp' => now()
            ]);
            abort(401, 'Authentication required.');
        }

        $user = auth()->user();
        
        // 检查用户是否为管理员
        if (!$user->is_admin) {
            \Log::warning('Non-admin user attempted to access admin area', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'timestamp' => now()
            ]);
            abort(403, 'Unauthorized Action.');
        }

        // 检查用户账户状态
        if ($user->is_banned || $user->is_suspended) {
            \Log::warning('Banned/Suspended admin attempted to access admin area', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'is_banned' => $user->is_banned ?? false,
                'is_suspended' => $user->is_suspended ?? false,
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            abort(403, 'Account suspended or banned.');
        }

        // 记录管理员访问
        \Log::info('Admin access granted', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ]);

        return $next($request);
    }
}
