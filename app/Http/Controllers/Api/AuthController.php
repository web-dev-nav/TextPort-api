<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiToken;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function activate(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:24'],
            'device_id' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_model' => ['nullable', 'string', 'max:255'],
            'device_brand' => ['nullable', 'string', 'max:255'],
            'device_manufacturer' => ['nullable', 'string', 'max:255'],
            'android_version' => ['nullable', 'string', 'max:255'],
            'sdk_int' => ['nullable', 'string', 'max:255'],
            'device_hardware' => ['nullable', 'string', 'max:255'],
            'device_board' => ['nullable', 'string', 'max:255'],
            'device_product' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:255'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $user = User::query()
            ->where('activation_code', $code)
            ->where('is_admin', false)
            ->first();

        if (! $user) {
            return response()->json(['error' => 'Invalid activation code'], 404);
        }

        $user->forceFill([
            'device_id' => $validated['device_id'],
            'device_name' => $validated['device_name'] ?? null,
            'device_model' => $validated['device_model'] ?? null,
            'device_brand' => $validated['device_brand'] ?? null,
            'device_manufacturer' => $validated['device_manufacturer'] ?? null,
            'android_version' => $validated['android_version'] ?? null,
            'sdk_int' => $validated['sdk_int'] ?? null,
            'device_hardware' => $validated['device_hardware'] ?? null,
            'device_board' => $validated['device_board'] ?? null,
            'device_product' => $validated['device_product'] ?? null,
            'activated_at' => $user->activated_at ?? now(),
            'last_seen_at' => now(),
        ])->save();

        $token = $this->issueToken($user->id);

        return response()->json(['token' => $token]);
    }

    public function requestCode(Request $request)
    {
        if (! config('textport.allow_public_code_request', false)) {
            return response()->json(['error' => 'Code request is disabled by server policy'], 403);
        }

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $ip = (string) $request->ip();
        $cacheKey = 'textport:request-code:'.$ip;
        if (Cache::has($cacheKey)) {
            return response()->json(['error' => 'Please wait before requesting another code'], 429);
        }
        Cache::put($cacheKey, true, now()->addSeconds(30));

        $code = $this->generateUniqueCode();
        $label = trim((string) ($validated['label'] ?? 'device'));
        $safe = preg_replace('/[^a-z0-9]+/i', '-', strtolower($label)) ?: 'device';

        User::query()->create([
            'email' => $safe.'-'.$code.'@textport.local',
            'password' => Str::random(32),
            'sync_enabled' => true,
            'is_admin' => false,
            'activation_code' => $code,
        ]);

        return response()->json([
            'ok' => true,
            'code' => $code,
            'message' => 'Code generated successfully',
        ], 201);
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

    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (User::query()->where('activation_code', $code)->exists());

        return $code;
    }
}
