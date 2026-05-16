@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>System Logs</h2>
        <p class="muted">Showing categorized Laravel log entries from <code>storage/logs/laravel.log</code>.</p>

        @if(session('status'))
            <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:10px;border-radius:8px;margin-bottom:12px;">
                {{ session('status') }}
            </div>
        @endif

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            <span style="padding:4px 10px;border-radius:999px;background:#fee2e2;color:#991b1b;font-weight:700;">Error: {{ $counts['error'] }}</span>
            <span style="padding:4px 10px;border-radius:999px;background:#fef3c7;color:#92400e;font-weight:700;">Warning: {{ $counts['warning'] }}</span>
            <span style="padding:4px 10px;border-radius:999px;background:#dbeafe;color:#1e40af;font-weight:700;">Info: {{ $counts['info'] }}</span>
            <span style="padding:4px 10px;border-radius:999px;background:#e0e7ff;color:#3730a3;font-weight:700;">Debug: {{ $counts['debug'] }}</span>
            <span style="padding:4px 10px;border-radius:999px;background:#e5e7eb;color:#374151;font-weight:700;">Other: {{ $counts['other'] }}</span>
        </div>

        <form method="post" action="{{ route('admin.logs.delete') }}" onsubmit="return confirm('Delete all entries from laravel.log?');" style="margin-bottom:14px;">
            @csrf
            <button type="submit" style="background:#b91c1c;color:#fff;border:0;border-radius:8px;padding:8px 12px;cursor:pointer;">Delete Log File Content</button>
        </form>

        @if(empty($logEntries))
            <p>No log entries found.</p>
        @else
            <div style="max-height:70vh;overflow:auto;border:1px solid #e5e7eb;border-radius:10px;background:#0b1220;color:#e5e7eb;padding:12px;">
                @foreach($logEntries as $entry)
                    <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,.08);">
                        @php
                            $colors = [
                                'error' => ['bg' => '#7f1d1d', 'fg' => '#fee2e2'],
                                'warning' => ['bg' => '#78350f', 'fg' => '#fef3c7'],
                                'info' => ['bg' => '#1e3a8a', 'fg' => '#dbeafe'],
                                'debug' => ['bg' => '#312e81', 'fg' => '#e0e7ff'],
                                'other' => ['bg' => '#374151', 'fg' => '#f3f4f6'],
                            ];
                            $c = $colors[$entry['level']] ?? $colors['other'];
                        @endphp
                        <div style="margin-bottom:6px;">
                            <span style="padding:2px 8px;border-radius:999px;background:{{ $c['bg'] }};color:{{ $c['fg'] }};font-size:11px;font-weight:700;text-transform:uppercase;">
                                {{ $entry['level'] }}
                            </span>
                        </div>
                        <pre style="white-space:pre-wrap;margin:0;font-family:Menlo,Monaco,Consolas,monospace;font-size:12px;">{{ $entry['line'] }}</pre>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
