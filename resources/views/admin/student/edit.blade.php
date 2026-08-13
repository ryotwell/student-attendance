@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Siswa" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.student._form', ['student' => $student])
    </div>
@endsection
