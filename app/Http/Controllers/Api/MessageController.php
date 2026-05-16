<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:100'],
            'messages.*.sender' => ['required', 'string', 'min:1'],
            'messages.*.body' => ['required', 'string'],
            'messages.*.timestamp' => ['required', 'integer'],
            'messages.*.direction' => ['nullable', 'string'],
        ]);

        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (! $user->sync_enabled) {
            return response()->json(['error' => 'Sync paused'], 403);
        }

        $rows = array_map(function (array $msg) use ($user): array {
            return [
                'user_id' => $user->id,
                'sender' => $msg['sender'],
                'body' => $msg['body'],
                'timestamp' => $msg['timestamp'],
                'direction' => $msg['direction'] ?? 'inbound',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $validated['messages']);

        Message::query()->insert($rows);

        return response()->json(['ok' => true]);
    }
}
