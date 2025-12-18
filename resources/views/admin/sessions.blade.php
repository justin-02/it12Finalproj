@extends('layouts.app')

@section('title','Active Sessions')

@section('content')
<div class="page-header">
    <h1>Sessions</h1>
</div>
<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Started</th>
                    <th>Last Activity</th>
                    <th>Ended</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $s)
                <tr>
                    <td>{{ $s->user?->name ?? '—' }}</td>
                    <td>{{ $s->ip_address }}</td>
                    <td>{{ $s->user_agent }}</td>
                    <td>{{ optional($s->started_at)->diffForHumans() }}</td>
                    <td>{{ optional($s->last_activity_at)->diffForHumans() }}</td>
                    <td>{{ optional($s->ended_at)->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $sessions->links() }}</div>
    </div>
</div>
<div class="mt-2">
    <a href="{{ route('admin.sessions.export') }}" class="btn btn-secondary">Export Sessions CSV</a>
</div>
@endsection
