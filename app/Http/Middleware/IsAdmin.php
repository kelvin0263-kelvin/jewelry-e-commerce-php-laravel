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
        // 检查用户是否已登录 并且 is_admin 字段是否为 true (1)
        if (auth()->check() && auth()->user()->is_admin) {
            // 如果是管理员，则允许继续访问
            return $next($request);
        }

        // 如果不是管理员，则终止请求，并返回 403 Forbidden (禁止访问) 错误
        abort(403, 'Unauthorized Action.');
    }
}
