@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>Sync History</h2>
        <p class="muted">Recent synchronization jobs with device details, delivery state, and errors.</p>
        <table style="width:100%;border-collapse:collapse;min-width:1100px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Time</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Account</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Device</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Messages</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Status</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Error</th>
            </tr>
            </thead>
            <tbody>
            @forelse($events as $event)
                <tr>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        {{ $event->created_at }}
                        <div style="font-size:12px;color:#6b7280;">Updated: {{ $event->updated_at }}</div>
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $event->user_email }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        {{ $event->device_name ?: 'Unknown device' }}
                        <div style="font-size:12px;color:#6b7280;">{{ $event->device_model ?: 'N/A' }}</div>
                        <div style="font-size:12px;color:#6b7280;">ID: {{ $event->device_id ?: 'N/A' }}</div>
                        <div style="font-size:12px;color:#6b7280;">App: {{ $event->app_version ?: 'N/A' }}</div>
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $event->message_count }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @php
                            $status = strtolower((string)$event->status);
                            $badge = match ($status) {
                                'synced' => ['bg' => '#dcfce7', 'fg' => '#166534'],
                                'queued' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
                                'failed' => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
                                default => ['bg' => '#e5e7eb', 'fg' => '#374151'],
                            };
                        @endphp
                        <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:{{ $badge['bg'] }};color:{{ $badge['fg'] }};">
                            {{ $event->status }}
                        </span>
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        {{ $event->error_message ?: '—' }}
                    </td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="6">No sync history yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
