@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Siswa" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.student._form', ['student' => null])
    </div>
@endsection
