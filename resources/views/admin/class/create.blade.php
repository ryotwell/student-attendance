@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Kelas" />
    <div class="max-w-2xl space-y-6">
        @include('admin.class._form', ['class' => null])
    </div>
@endsection
