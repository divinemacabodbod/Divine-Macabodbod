<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): SymfonyResponse
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if user has any of the required permissions
        if (!empty($permissions)) {
            $hasPermission = false;

            foreach ($permissions as $permission) {
                // Check if it's a role or permission
                if ($request->user()->hasPermissionTo($permission) || $request->user()->hasRole($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                return response()->json([
                    'message' => 'You do not have permission to access this resource.',
                    'required_permissions' => $permissions,
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
