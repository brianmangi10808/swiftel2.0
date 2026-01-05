<?php
namespace App\Http\Middleware;

use Closure;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $model, $action)
    {
        $user = $request->user();

        if (!$user || !$user->canDo($model, $action)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
