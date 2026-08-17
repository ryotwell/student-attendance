@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Daftar Absensi per Kelas" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Pilih Kelas">
        <div class="space-y-4">
            @forelse ($classes as $class)
                <a href="{{ route('attendance.list.show', $class) }}"
                    class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $class->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $class->academicYear?->name ?? '-' }} - {{ $class->students_count ?? 0 }} siswa</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @empty
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">Tidak ada kelas yang tersedia</p>
            @endforelse
        </div>
    </x-common.component-card>
@endsection