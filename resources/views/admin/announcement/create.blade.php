@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Pengumuman" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.announcement._form', ['announcement' => null])
    </div>
@endsection