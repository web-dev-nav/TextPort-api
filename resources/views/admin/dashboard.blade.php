<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TextPort Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; color: #111827; margin: 0; }
        .top { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #111827; color: #fff; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 20px; overflow: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { border-bottom: 1px solid #e5e7eb; text-align: left; padding: 10px; font-size: 14px; vertical-align: top; }
        th { background: #f3f4f6; }
        .tag { padding: 3px 8px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .on { background: #dcfce7; color: #166534; }
        .off { background: #fee2e2; color: #991b1b; }
        .logout { background: #ef4444; color: #fff; border: 0; border-radius: 8px; padding: 8px 12px; cursor: pointer; }
        .muted { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="top">
    <div>
        <strong>TextPort Admin Panel</strong>
        <div class="muted">Connected devices and SMS synchronization</div>
    </div>
    <form method="post" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout" type="submit">Logout</button>
    </form>
</div>

<div class="container">
    <div class="card">
        <h2>Connected Devices</h2>
        <table>
            <thead>
            <tr>
                <th>User Email</th>
                <th>Sync State</th>
                <th>Active Tokens</th>
                <th>Total SMS</th>
                <th>Last SMS Timestamp</th>
            </tr>
            </thead>
            <tbody>
            @forelse($devices as $device)
                <tr>
                    <td>{{ $device->email }}</td>
                    <td>
                        @if($device->sync_enabled)
                            <span class="tag on">Active</span>
                        @else
                            <span class="tag off">Paused</span>
                        @endif
                    </td>
                    <td>{{ $device->active_tokens }}</td>
                    <td>{{ $device->total_messages }}</td>
                    <td>{{ $device->last_sms_timestamp ? $device->last_sms_timestamp : 'N/A' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No connected devices yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>SMS Synchronization Feed</h2>
        <table>
            <thead>
            <tr>
                <th>Device</th>
                <th>Sender</th>
                <th>Direction</th>
                <th>Timestamp</th>
                <th>Message</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentMessages as $sms)
                <tr>
                    <td>{{ $sms->device_email }}</td>
                    <td>{{ $sms->sender }}</td>
                    <td>{{ $sms->direction }}</td>
                    <td>{{ $sms->timestamp }}</td>
                    <td>{{ $sms->body }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No SMS sync data available.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
