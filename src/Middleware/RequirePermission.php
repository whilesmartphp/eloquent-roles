<?php

namespace Whilesmart\Roles\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! method_exists($user, 'hasPermission')) {
            return response()->json([
                'message' => 'The user model does not support permissions.',
            ], 500);
        }

        foreach ($permissions as $permission) {
            if (! $user->hasPermission($permission)) {
                return response()->json([
                    'message' => 'Forbidden. You do not have the required permission: '.$permission,
                ], 403);
            }
        }

        return $next($request);
    }
}
