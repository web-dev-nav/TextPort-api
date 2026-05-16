@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>Connected Devices</h2>
        <table style="width:100%;border-collapse:collapse;min-width:760px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">User Email</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Device</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Sync State</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Connection</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Active Tokens</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Total SMS</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Last SMS Timestamp</th>
            </tr>
            </thead>
            <tbody>
            @forelse($devices as $device)
                <tr>
                    @php
                        $online = $device->last_seen_at && \Carbon\Carbon::parse($device->last_seen_at)->gt(now()->subMinutes(3));
                    @endphp
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->email }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        {{ $device->device_name ?: 'Unknown device' }}
                        @if($device->device_model)
                            <div style="font-size:12px;color:#6b7280;">{{ $device->device_model }}</div>
                        @endif
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @if($device->sync_enabled)
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;">Active</span>
                        @else
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#fee2e2;color:#991b1b;">Paused</span>
                        @endif
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @if($online)
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;">Online</span>
                        @else
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#fee2e2;color:#991b1b;">Offline</span>
                        @endif
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->active_tokens }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->total_messages }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @if($device->last_sms_timestamp)
                            {{ \Carbon\Carbon::createFromTimestampMs((int)$device->last_sms_timestamp)->format('Y-m-d H:i:s') }}
                        @else
                            N/A
                        @endif
                        @if($device->last_seen_at)
                            <div style="font-size:12px;color:#6b7280;">Seen: {{ \Carbon\Carbon::parse($device->last_seen_at)->diffForHumans() }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="7">No connected devices yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>SMS Synchronization Feed</h2>
        <table style="width:100%;border-collapse:collapse;min-width:760px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Device</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Status</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Sender</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Direction</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Timestamp</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Message</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentMessages as $sms)
                <tr>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        {{ $sms->device_name ?: $sms->device_email }}
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;">
                            {{ $sms->sync_status }}
                        </span>
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->sender }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->direction }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ \Carbon\Carbon::createFromTimestampMs((int)$sms->timestamp)->format('Y-m-d H:i:s') }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->body }}</td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="6">No SMS sync data available.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
