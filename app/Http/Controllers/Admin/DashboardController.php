<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = User::query()
            ->where('is_admin', false)
            ->leftJoin('api_tokens', function ($join): void {
                $join->on('api_tokens.user_id', '=', 'users.id')
                    ->where(function ($q): void {
                        $q->whereNull('api_tokens.expires_at')
                            ->orWhere('api_tokens.expires_at', '>', now());
                    });
            })
            ->leftJoin('messages', 'messages.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.email', 'users.sync_enabled')
            ->orderByDesc(DB::raw('MAX(messages.timestamp)'))
            ->select([
                'users.id',
                'users.email',
                'users.sync_enabled',
                DB::raw('COUNT(DISTINCT api_tokens.id) as active_tokens'),
                DB::raw('COUNT(messages.id) as total_messages'),
                DB::raw('MAX(messages.timestamp) as last_sms_timestamp'),
            ])
            ->get();

        $recentMessages = Message::query()
            ->join('users', 'users.id', '=', 'messages.user_id')
            ->where('users.is_admin', false)
            ->orderByDesc('messages.timestamp')
            ->limit(200)
            ->get([
                'messages.sender',
                'messages.body',
                'messages.timestamp',
                'messages.direction',
                'users.email as device_email',
            ]);

        return view('admin.dashboard', [
            'devices' => $devices,
            'recentMessages' => $recentMessages,
        ]);
    }

    public function logs()
    {
        $path = storage_path('logs/laravel.log');
        if (! File::exists($path)) {
            return view('admin.logs', ['logEntries' => []]);
        }

        $content = File::get($path);
        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];

        $errorLines = array_values(array_filter($lines, static function (string $line): bool {
            return str_contains($line, '.ERROR:') || str_contains($line, 'production.ERROR:') || str_contains($line, 'local.ERROR:');
        }));

        $entries = array_slice($errorLines, -300);

        return view('admin.logs', ['logEntries' => $entries]);
    }
}
