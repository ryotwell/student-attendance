@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Rencana Pembelajaran" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('teacher.lesson-plans._form', ['lessonPlan' => null])
    </div>
@endsection
