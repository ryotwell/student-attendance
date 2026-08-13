@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Jadwal Pelajaran" />

    <x-common.component-card title="Jadwal Pelajaran">
        <div class="flex items-center justify-between">
            <a href="{{ route('schedules.create', ['class' => $class->id]) }}"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                Tambah Jadwal
            </a>
        </div>

        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <x-class-schedule :classWithSchedules="$class" />
        </div>
    </x-common.component-card>
@endsection
