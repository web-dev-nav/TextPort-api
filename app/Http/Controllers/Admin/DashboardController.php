<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
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
}
