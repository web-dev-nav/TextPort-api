@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>Connected Devices</h2>
        <table style="width:100%;border-collapse:collapse;min-width:760px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">User Email</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Sync State</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Active Tokens</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Total SMS</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Last SMS Timestamp</th>
            </tr>
            </thead>
            <tbody>
            @forelse($devices as $device)
                <tr>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->email }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @if($device->sync_enabled)
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;">Active</span>
                        @else
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#fee2e2;color:#991b1b;">Paused</span>
                        @endif
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->active_tokens }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->total_messages }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $device->last_sms_timestamp ? $device->last_sms_timestamp : 'N/A' }}</td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="5">No connected devices yet.</td></tr>
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
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Sender</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Direction</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Timestamp</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Message</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentMessages as $sms)
                <tr>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->device_email }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->sender }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->direction }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->timestamp }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->body }}</td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="5">No SMS sync data available.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
