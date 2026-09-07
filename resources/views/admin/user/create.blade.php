@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Pengguna" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.user._form', ['user' => null])
    </div>
@endsection