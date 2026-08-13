@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Kelas" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.class._form', ['class' => $class])
    </div>
@endsection
