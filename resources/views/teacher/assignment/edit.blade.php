@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb pageTitle="Edit Tugas" />

    <div class="mx-auto max-w-3xl space-y-6">

        @include('teacher.assignment._form', [
            'assignment' => $assignment,
        ])

    </div>

@endsection