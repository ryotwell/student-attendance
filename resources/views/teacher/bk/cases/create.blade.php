@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Catatan Kasus" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('teacher.bk.cases._form', [
            'counselingCase' => null,
            'categoryOptions' => $categoryOptions,
            'selectedStudent' => $selectedStudent,
        ])
    </div>
@endsection