{{-- teacher/bk/cases/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Catatan Kasus" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('teacher.bk.cases._form', [
            'counselingCase' => null,
            'students' => $students,
            'categoryOptions' => $categoryOptions,
            'selectedStudentId' => $selectedStudentId,
        ])
    </div>
@endsection