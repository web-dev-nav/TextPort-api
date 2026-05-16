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
        <h2>Device SMS Feed</h2>
        <table style="width:100%;border-collapse:collapse;min-width:1100px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Device</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">SMS Body</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Status</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Sender</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Direction</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Timestamp</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Action</th>
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
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        <button type="button" onclick="toggleDetails('sms-{{ $loop->index }}')" style="background:#1f2937;color:#fff;border:0;border-radius:8px;padding:6px 10px;cursor:pointer;">Details</button>
                    </td>
                </tr>
                <tr id="sms-{{ $loop->index }}" style="display:none;background:#f9fafb;">
                    <td colspan="7" style="border-bottom:1px solid #e5e7eb;padding:12px 14px;">
                        <div style="font-weight:700;margin-bottom:6px;">Sync history for this device/account</div>
                        @php $history = collect($eventsByUser[$sms->user_id] ?? [])->take(8); @endphp
                        @if($history->isEmpty())
                            <div style="color:#6b7280;">No sync event history found.</div>
                        @else
                            <table style="width:100%;border-collapse:collapse;">
                                <thead>
                                <tr>
                                    <th style="text-align:left;padding:8px;border-bottom:1px solid #d1d5db;">Time</th>
                                    <th style="text-align:left;padding:8px;border-bottom:1px solid #d1d5db;">Status</th>
                                    <th style="text-align:left;padding:8px;border-bottom:1px solid #d1d5db;">Messages</th>
                                    <th style="text-align:left;padding:8px;border-bottom:1px solid #d1d5db;">Error</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($history as $h)
                                    <tr>
                                        <td style="padding:8px;border-bottom:1px solid #e5e7eb;">{{ \Carbon\Carbon::parse($h->created_at)->format('Y-m-d H:i:s') }}</td>
                                        <td style="padding:8px;border-bottom:1px solid #e5e7eb;">{{ $h->status }}</td>
                                        <td style="padding:8px;border-bottom:1px solid #e5e7eb;">{{ $h->message_count }}</td>
                                        <td style="padding:8px;border-bottom:1px solid #e5e7eb;">{{ $h->error_message ?: '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="7">No SMS messages found for this filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <script>
        function toggleDetails(id) {
            const row = document.getElementById(id);
            if (!row) return;
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
    </script>
@endsection
