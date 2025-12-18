@extends('layouts.app')

@section('title', 'Employee')

@section('content')
<div class="page-header">
    <h1>Employee Details</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @include('admin.partials.employee-details')
    </div>
</div>

@endsection
