@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Wali Kelas" />

    <div class="mx-auto max-w-6xl">
        <h2 class="mb-6 text-xl font-bold text-gray-800 dark:text-white">Pilih Kelas Perwalian</h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($xclasses as $xclass)
                <a href="{{ route('walikelas.dashboard', $xclass) }}"
                    class="rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">{{ $xclass->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $xclass->students_count }} Siswa</p>
                </a>
            @endforeach
        </div>
    </div>
@endsection