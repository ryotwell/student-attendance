@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Sekolah" />

    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Form Edit Sekolah</h3>
            @include('superadmin.schools._form', ['school' => $school])
        </div>
    </div>
@endsection