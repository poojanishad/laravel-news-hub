<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class ResolveUser
{
    public function handle($request, Closure $next)
    {
        $userId = $request->header('X-User-ID');

        if (!$userId) {
            return response()->json([
                'error' => 'User ID missing'
            ], 400);
        }

        $user = User::firstOrCreate([
            'id' => $userId
        ]);

        $request->attributes->set('resolved_user', $user);

        return $next($request);
    }
}
