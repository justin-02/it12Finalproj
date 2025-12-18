@extends('layouts.app')

@section('title','Activity Logs')

@section('content')
<div class="page-header">
    <h1>Activity Logs</h1>
</div>
<div class="mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><input name="user_id" class="form-control" placeholder="User ID" value="{{ request('user_id') }}"></div>
        <div class="col-md-3"><input name="event" class="form-control" placeholder="Event" value="{{ request('event') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
    </form>
</div>
<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Event</th>
                    <th>Context</th>
                    <th>IP</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user?->name ?? '—' }}</td>
                    <td>{{ $log->event }}</td>
                    <td><pre class="small">{{ json_encode($log->context) }}</pre></td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ optional($log->logged_at)->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
