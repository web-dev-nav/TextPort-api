<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\SyncEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'device_id' => ['nullable', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_model' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:255'],
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

        $user->forceFill([
            'device_id' => $validated['device_id'] ?? $user->device_id,
            'device_name' => $validated['device_name'] ?? $user->device_name,
            'device_model' => $validated['device_model'] ?? $user->device_model,
            'last_seen_at' => now(),
        ])->save();

        $event = SyncEvent::query()->create([
            'user_id' => $user->id,
            'status' => 'queued',
            'message_count' => count($validated['messages']),
            'device_id' => $validated['device_id'] ?? null,
            'device_name' => $validated['device_name'] ?? null,
            'device_model' => $validated['device_model'] ?? null,
            'app_version' => $validated['app_version'] ?? null,
        ]);

        try {
            DB::transaction(function () use ($validated, $user): void {
                $rows = array_map(function (array $msg) use ($user): array {
                    return [
                        'user_id' => $user->id,
                        'sender' => $msg['sender'],
                        'body' => $msg['body'],
                        'timestamp' => $msg['timestamp'],
                        'direction' => $msg['direction'] ?? 'inbound',
                        'sync_status' => 'synced',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $validated['messages']);

                Message::query()->insert($rows);
            });
            $event->forceFill(['status' => 'synced'])->save();
        } catch (\Throwable $e) {
            $event->forceFill([
                'status' => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 5000),
            ])->save();
            throw $e;
        }

        return response()->json(['ok' => true]);
    }
}
