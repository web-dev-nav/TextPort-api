@extends('admin.layout')

@section('content')
    <div class="card">
        <h2>System Logs</h2>
        <p class="muted">Showing latest Laravel error entries from <code>storage/logs/laravel.log</code>.</p>

        @if(empty($logEntries))
            <p>No error entries found.</p>
        @else
            <div style="max-height:70vh;overflow:auto;border:1px solid #e5e7eb;border-radius:10px;background:#0b1220;color:#e5e7eb;padding:12px;">
                @foreach($logEntries as $entry)
                    <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,.08);">
                        <pre style="white-space:pre-wrap;margin:0;font-family:Menlo,Monaco,Consolas,monospace;font-size:12px;">{{ $entry }}</pre>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
