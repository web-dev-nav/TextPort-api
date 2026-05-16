@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>Create Device Account</h2>
        @if(session('status'))
            <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:10px;border-radius:8px;margin-bottom:12px;">
                {{ session('status') }}
            </div>
        @endif
        <form method="post" action="{{ route('admin.accounts.create') }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div>
                <label style="display:block;font-size:13px;color:#374151;margin-bottom:4px;">Label (optional)</label>
                <input name="label" type="text" placeholder="sales-phone-1" style="padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;min-width:220px;">
            </div>
            <button type="submit" style="background:#111827;color:#fff;border:0;border-radius:8px;padding:9px 12px;cursor:pointer;">Create Account + Code</button>
        </form>
    </div>

    <div class="card">
        <h2>Device Accounts</h2>
        <table style="width:100%;border-collapse:collapse;min-width:980px;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Created</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Account</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Activation Code</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Activation Status</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Device</th>
                <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:10px;background:#f3f4f6;">Online State</th>
            </tr>
            </thead>
            <tbody>
            @forelse($accounts as $a)
                @php
                    $online = $a->last_seen_at && $a->last_seen_at->gt(now()->subMinutes(3));
                @endphp
                <tr>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ optional($a->created_at)->format('Y-m-d H:i:s') }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">{{ $a->email }}</td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;"><code>{{ $a->activation_code ?: '—' }}</code></td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @if($a->activated_at)
                            Activated ({{ $a->activated_at->format('Y-m-d H:i:s') }})
                        @else
                            Pending
                        @endif
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        {{ $a->device_name ?: '—' }}
                        @if($a->device_model)
                            <div style="font-size:12px;color:#6b7280;">{{ $a->device_model }}</div>
                        @endif
                    </td>
                    <td style="border-bottom:1px solid #e5e7eb;padding:10px;">
                        @if($online)
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#dcfce7;color:#166534;">Online</span>
                        @else
                            <span style="padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#fee2e2;color:#991b1b;">Offline</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td style="padding:10px;" colspan="6">No device accounts yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
