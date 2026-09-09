<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [],
            ], 401);
        }

        $hasRole = $user->roles()
            ->whereIn('name', $roles)
            ->exists();

        if (!$hasRole) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
                'errors' => [],
            ], 403);
        }

        return $next($request);
    }
}