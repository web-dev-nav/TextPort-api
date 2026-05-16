<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->groupBy(
                'users.id',
                'users.email',
                'users.sync_enabled',
                'users.device_name',
                'users.device_model',
                'users.last_seen_at'
            )
            ->orderByDesc(DB::raw('MAX(messages.timestamp)'))
            ->select([
                'users.id',
                'users.email',
                'users.sync_enabled',
                'users.device_name',
                'users.device_model',
                'users.last_seen_at',
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
                'messages.sync_status',
                'users.email as device_email',
                'users.device_name as device_name',
            ]);

        return view('admin.dashboard', [
            'devices' => $devices,
            'recentMessages' => $recentMessages,
        ]);
    }

    public function history(Request $request)
    {
        $selectedUserId = (int) $request->query('user_id', 0);

        $devices = User::query()
            ->where('is_admin', false)
            ->orderBy('email')
            ->get(['id', 'email', 'device_name', 'device_model']);

        $smsFeed = Message::query()
            ->join('users', 'users.id', '=', 'messages.user_id')
            ->where('users.is_admin', false)
            ->when($selectedUserId > 0, function ($q) use ($selectedUserId): void {
                $q->where('users.id', $selectedUserId);
            })
            ->orderByDesc('messages.timestamp')
            ->limit(300)
            ->get([
                'users.id as user_id',
                'messages.sender',
                'messages.body',
                'messages.timestamp',
                'messages.direction',
                'messages.sync_status',
                'users.email as user_email',
                'users.device_name as device_name',
            ]);

        return view('admin.history', [
            'devices' => $devices,
            'smsFeed' => $smsFeed,
            'selectedUserId' => $selectedUserId,
        ]);
    }

    public function accounts()
    {
        $accounts = User::query()
            ->where('is_admin', false)
            ->orderByDesc('created_at')
            ->limit(500)
            ->get([
                'id',
                'email',
                'activation_code',
                'activated_at',
                'device_id',
                'device_name',
                'device_model',
                'device_brand',
                'device_manufacturer',
                'android_version',
                'sdk_int',
                'device_hardware',
                'device_board',
                'device_product',
                'last_seen_at',
                'sync_enabled',
                'created_at',
            ]);

        return view('admin.accounts', ['accounts' => $accounts]);
    }

    public function createAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $code = $this->generateUniqueCode();
        $label = trim((string)($validated['label'] ?? 'device'));
        $safe = preg_replace('/[^a-z0-9]+/i', '-', strtolower($label)) ?: 'device';

        User::query()->create([
            'email' => $safe.'-'.$code.'@textport.local',
            'password' => Str::random(32),
            'sync_enabled' => true,
            'is_admin' => false,
            'activation_code' => $code,
        ]);

        return redirect()->route('admin.accounts')->with('status', "Account created. Activation code: {$code}");
    }

    public function deleteAccount(User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return redirect()->route('admin.accounts')->with('status', 'Admin accounts cannot be deleted here.');
        }

        $user->delete();

        return redirect()->route('admin.accounts')->with('status', 'Device account deleted successfully.');
    }

    public function logs()
    {
        $path = storage_path('logs/laravel.log');
        if (! File::exists($path)) {
            return view('admin.logs', [
                'logEntries' => [],
                'counts' => ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0, 'other' => 0],
            ]);
        }

        $content = File::get($path);
        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];
        $entries = [];
        $counts = ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0, 'other' => 0];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $level = $this->detectLogLevel($trimmed);
            $counts[$level]++;
            $entries[] = [
                'level' => $level,
                'line' => $trimmed,
            ];
        }

        $entries = array_slice($entries, -500);

        return view('admin.logs', ['logEntries' => $entries, 'counts' => $counts]);
    }

    public function deleteLogs(Request $request): RedirectResponse
    {
        $path = storage_path('logs/laravel.log');
        if (File::exists($path)) {
            File::put($path, '');
        }

        return redirect()->route('admin.logs')->with('status', 'Log file cleared successfully.');
    }

    private function detectLogLevel(string $line): string
    {
        $normalized = strtoupper($line);
        if (str_contains($normalized, '.ERROR:')) {
            return 'error';
        }
        if (str_contains($normalized, '.WARNING:') || str_contains($normalized, '.WARN:')) {
            return 'warning';
        }
        if (str_contains($normalized, '.INFO:')) {
            return 'info';
        }
        if (str_contains($normalized, '.DEBUG:')) {
            return 'debug';
        }

        return 'other';
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (User::query()->where('activation_code', $code)->exists());

        return $code;
    }
}
