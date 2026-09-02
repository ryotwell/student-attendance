@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Jadwal" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.schedule._form', ['schedule' => $schedule, 'users' => $users])
    </div>
@endsection
