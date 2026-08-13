@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Tahun Ajaran" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('admin.academic-year._form', ['academicYear' => $academicYear])
    </div>
@endsection
