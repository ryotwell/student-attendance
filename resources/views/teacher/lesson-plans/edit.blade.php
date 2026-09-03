@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Rencana Pembelajaran" />
    <div class="mx-auto max-w-2xl space-y-6">
        @include('teacher.lesson-plans._form', ['lessonPlan' => $lessonPlan])
    </div>
@endsection
