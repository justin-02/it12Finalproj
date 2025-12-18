@extends('layouts.app')

@section('title','Productivity Report')

@section('content')
<div class="page-header">
    <h1>Productivity Report</h1>
</div>
<form class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="date" name="start_date" value="{{ request('start_date', now()->subDays(7)->toDateString()) }}" class="form-control" />
    </div>
    <div class="col-md-3">
        <input type="date" name="end_date" value="{{ request('end_date', now()->toDateString()) }}" class="form-control" />
    </div>
    <div class="col-md-2"><button class="btn btn-primary">Run</button></div>
    <div class="col-md-2">
        <a href="{{ route('admin.productivity-report.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-secondary">Export CSV</a>
    </div>
</form>
<div class="card">
    <div class="card-body">
        @if($metrics->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Metrics</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics as $m)
                <tr>
                    <td>{{ $m->date }}</td>
                    <td>{{ $m->user?->name ?? '—' }}</td>
                    <td><pre class="small">{{ json_encode($m->metrics) }}</pre></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center text-muted py-4">No metrics for the selected period.</div>
        @endif
    </div>
</div>
@endsection
