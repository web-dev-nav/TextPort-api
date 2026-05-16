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
        <p style="font-size:13px;color:#6b7280;margin:0 0 16px;">
            {{ $conversations->count() }} conversation{{ $conversations->count() === 1 ? '' : 's' }}
            &mdash; click any row to expand the full thread.
        </p>

        @forelse($conversations as $conv)
            @php
                $initials = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($conv->sender, -2)));
                $colors   = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#ef4444'];
                $bg       = $colors[abs(crc32($conv->sender)) % count($colors)];
            @endphp
            <details style="border:1px solid #e5e7eb;border-radius:12px;margin-bottom:10px;overflow:hidden;">
                <summary style="display:flex;align-items:center;gap:12px;padding:14px 16px;cursor:pointer;list-style:none;background:#fff;user-select:none;">
                    {{-- Avatar --}}
                    <div style="width:42px;height:42px;border-radius:50%;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:700;font-size:14px;">
                        {{ $initials ?: '?' }}
                    </div>
                    {{-- Main info --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                            <span style="font-weight:700;font-size:15px;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $conv->sender }}</span>
                            <span style="font-size:12px;color:#9ca3af;white-space:nowrap;">
                                {{ \Carbon\Carbon::createFromTimestampMs((int)$conv->latest_timestamp)->diffForHumans() }}
                            </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-top:2px;">
                            <span style="font-size:13px;color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:420px;">{{ $conv->latest_body }}</span>
                            <span style="background:#f3f4f6;color:#374151;border-radius:999px;padding:2px 9px;font-size:12px;font-weight:600;white-space:nowrap;">
                                {{ $conv->message_count }} msg{{ $conv->message_count === 1 ? '' : 's' }}
                            </span>
                        </div>
                        <div style="font-size:11px;color:#d1d5db;margin-top:2px;">{{ $conv->device_name ?: $conv->user_email }}</div>
                    </div>
                    {{-- Expand arrow --}}
                    <svg style="flex-shrink:0;transition:transform .2s;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>

                {{-- Thread messages --}}
                <div style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                    @foreach($conv->messages as $msg)
                        @php $isSent = $msg->direction === 'outbound'; @endphp
                        <div style="display:flex;justify-content:{{ $isSent ? 'flex-end' : 'flex-start' }};">
                            <div style="max-width:72%;background:{{ $isSent ? 'linear-gradient(135deg,#4f46e5,#7c3aed)' : '#fff' }};color:{{ $isSent ? '#fff' : '#111827' }};padding:10px 14px;border-radius:{{ $isSent ? '18px 18px 4px 18px' : '4px 18px 18px 18px' }};box-shadow:0 1px 2px rgba(0,0,0,.06);">
                                <div style="font-size:14px;line-height:1.5;word-break:break-word;">{{ $msg->body }}</div>
                                <div style="font-size:11px;margin-top:4px;text-align:right;opacity:.65;">
                                    {{ \Carbon\Carbon::createFromTimestampMs((int)$msg->timestamp)->format('M j, H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </details>
        @empty
            <p style="color:#9ca3af;padding:16px 0;">No SMS conversations found for this filter.</p>
        @endforelse
    </div>

    <style>
        details[open] > summary svg { transform: rotate(180deg); }
        details > summary::-webkit-details-marker { display: none; }
    </style>
@endsection
