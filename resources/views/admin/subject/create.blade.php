@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Mata Pelajaran" />
    <div class="max-w-2xl space-y-6">
        @include('admin.subject._form', ['subject' => null])
    </div>
@endsection
