@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>Device Filter</h2>
        <form method="get" action="{{ route('admin.history') }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label style="display:block;font-size:13px;color:#374151;margin-bottom:4px;">Show data for device/account</label>
                <select name="user_id" style="padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;min-width:260px;">
                    <option value="0">All devices</option>
                    @foreach($devices as $d)
                        <option value="{{ $d->id }}" {{ $selectedUserId === (int)$d->id ? 'selected' : '' }}>
                            {{ $d->device_name ?: $d->email }}{{ $d->device_model ? ' - '.$d->device_model : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="background:#111827;color:#fff;border:0;border-radius:8px;padding:9px 12px;cursor:pointer;">Apply Filter</button>
        </form>
    </div>

    <div class="card">
        <h2>Device SMS History Feed</h2>
        <table style="width:100%;border-collapse:collapse;min-width:1100px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Device</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">SMS Body</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Status</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Sender</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Direction</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Timestamp</th>
            </tr>
            </thead>
            <tbody>
            @forelse($smsFeed as $sms)
                <tr>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->device_name ?: $sms->user_email }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;max-width:440px;white-space:pre-wrap;word-break:break-word;font-weight:600;">{{ $sms->body }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;">{{ $sms->sync_status }}</span>
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->sender }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $sms->direction }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ \Carbon\Carbon::createFromTimestampMs((int)$sms->timestamp)->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="6">No SMS messages found for this filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
