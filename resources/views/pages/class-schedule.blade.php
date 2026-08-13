@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Jadwal Pelajaran Kelas {{ $class->name }}" subtitle="Tahun Ajaran: {{ $class->academicYear?->name }}">
        <x-app.view.components.class-schedule :class-id="$class->id" />
    </x-common.component-card>
@endsection