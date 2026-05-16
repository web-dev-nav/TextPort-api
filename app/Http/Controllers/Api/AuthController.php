<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiToken;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (User::query()->where('email', $validated['email'])->exists()) {
            return response()->json(['error' => 'Email already exists'], 409);
        }

        $user = User::query()->create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'sync_enabled' => true,
        ]);

        $token = $this->issueToken($user->id);

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->issueToken($user->id);

        return response()->json(['token' => $token]);
    }

    private function issueToken(int $userId): string
    {
        $plain = Str::random(80);
        ApiToken::query()->create([
            'user_id' => $userId,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addDays(30),
        ]);

        return $plain;
    }
}
