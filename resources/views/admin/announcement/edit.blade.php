@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Pengumuman" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.announcement._form', ['announcement' => $announcement])
    </div>
@endsection