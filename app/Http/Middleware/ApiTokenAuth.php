<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization');
        if (! is_string($auth) || ! Str::startsWith($auth, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $plainToken = trim(Str::after($auth, 'Bearer '));
        if ($plainToken === '') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $hashed = hash('sha256', $plainToken);
        $token = ApiToken::query()
            ->where('token_hash', $hashed)
            ->where(function ($q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::query()->find($token->user_id);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        Auth::setUser($user);

        return $next($request);
    }
}
