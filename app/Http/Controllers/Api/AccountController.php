<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function pause(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->forceFill(['sync_enabled' => false])->save();

        return response()->json(['ok' => true]);
    }

    public function resume(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->forceFill(['sync_enabled' => true])->save();

        return response()->json(['ok' => true]);
    }

    public function export(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $rows = Message::query()
            ->where('user_id', $user->id)
            ->orderByDesc('timestamp')
            ->get(['sender', 'body', 'timestamp', 'direction']);

        return response()->json([
            'download_url' => 'data:application/json,'.rawurlencode($rows->toJson()),
        ]);
    }

    public function delete(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Message::query()->where('user_id', $user->id)->delete();
        ApiToken::query()->where('user_id', $user->id)->delete();
        $user->delete();

        return response()->json(['ok' => true]);
    }
}
