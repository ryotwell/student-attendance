{{-- teacher/bk/cases/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Catatan Kasus" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('teacher.bk.cases._form', [
            'counselingCase' => $counselingCase,
            'categoryOptions' => $categoryOptions,
        ])
    </div>
@endsection