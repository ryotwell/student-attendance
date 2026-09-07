@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Daftar Kelas" />

{{-- =========================================================
    FLASH SUCCESS
========================================================== --}}
@if (session('success'))
    <div class="mb-6">
        <x-ui.alert
            variant="success"
            title="Berhasil"
            :message="session('success')"
        />
    </div>
@endif


{{-- =========================================================
    HASIL IMPORT
========================================================== --}}
@if (session('import_errors'))

    <div class="mb-6">

        <div class="rounded-lg border border-error-200 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/10">

            <div class="flex gap-3">

                {{-- Icon --}}
                <div class="shrink-0">

                    <svg
                        class="h-5 w-5 text-error-600 dark:text-error-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20h15.6a2 2 0 001.73-2.64l-7.82-13.5a2 2 0 00-3.46 0z"
                        />
                    </svg>

                </div>


                {{-- Content --}}
                <div class="flex-1 min-w-0">

                    <p class="text-sm font-medium text-error-800 dark:text-error-300">
                        Beberapa data gagal diimport
                    </p>


                    {{-- Summary --}}
                    <p class="mt-1 text-sm text-error-700 dark:text-error-400">

                        @if (session('import_success_count') > 0)

                            <strong>
                                {{ session('import_success_count') }}
                            </strong>
                            kelas berhasil diimport.

                        @endif

                        <strong>
                            {{ session('import_error_count') }}
                        </strong>
                        kelas gagal diimport.

                    </p>


                    {{-- Error List --}}
                    <div class="mt-4 space-y-2">

                        @foreach (session('import_errors') as $error)

                            <div class="rounded-lg border border-error-200 bg-white p-3 dark:border-error-500/20 dark:bg-white/[0.03]">

                                <p class="text-sm font-semibold text-error-700 dark:text-error-400">
                                    Baris {{ $error['row'] }}
                                </p>

                                <p class="mt-0.5 text-sm text-error-600 dark:text-error-400">
                                    {{ $error['message'] }}
                                </p>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </div>

@endif


{{-- =========================================================
    MAIN CARD
========================================================== --}}
<x-common.component-card title="Daftar Kelas">

    {{-- =====================================================
        HEADER & FILTER
    ====================================================== --}}
    <div class="flex flex-col gap-4">


        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $classes->total() }} kelas terdaftar
                </p>

            </div>


            {{-- Action --}}
            <div class="flex flex-wrap items-center gap-2">

                {{-- Import Excel --}}
                <a
                    href="{{ route('classes.import.form') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition"
                >

                    {{-- Excel Icon --}}
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"
                        />
                    </svg>

                    Import Excel

                </a>


                {{-- Tambah Kelas --}}
                <a
                    href="{{ route('classes.create') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition"
                >

                    {{-- Plus Icon --}}
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Tambah Kelas

                </a>

            </div>

        </div>


        {{-- =================================================
            FORM FILTER
        ================================================== --}}
        <form
            method="GET"
            action="{{ route('classes.index') }}"
            class="flex flex-wrap items-end gap-4"
        >

            {{-- Search --}}
            <div class="min-w-[200px] flex-1">

                <label
                    for="search"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Cari Kelas
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nama atau kode kelas"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                />

            </div>


            {{-- Tahun Ajaran --}}
            <div class="w-48">

                <label
                    for="academic_year_id"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Tahun Ajaran
                </label>

                <select
                    id="academic_year_id"
                    name="academic_year_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >

                    <option value="">
                        Semua
                    </option>

                    @foreach ($academicYears as $year)

                        <option
                            value="{{ $year->id }}"
                            @selected(request('academic_year_id') == $year->id)
                        >
                            {{ $year->name }}
                            ({{ $year->semester }})
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Wali Kelas --}}
            <div class="w-48">

                <label
                    for="user_id"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Wali Kelas
                </label>

                <select
                    id="user_id"
                    name="user_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >

                    <option value="">
                        Semua
                    </option>

                    @foreach ($users as $user)

                        <option
                            value="{{ $user->id }}"
                            @selected(request('user_id') == $user->id)
                        >
                            {{ $user->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Buttons --}}
            <div class="flex items-center gap-2">

                <button
                    type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition"
                >

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.35-4.35m2.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>

                    Filter

                </button>


                <a
                    href="{{ route('classes.index') }}"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- =====================================================
        TABLE
    ====================================================== --}}
    <div class="mt-6 max-w-full overflow-x-auto custom-scrollbar">

        <table class="w-full min-w-[900px]">

            <thead>

                <tr class="border-b border-gray-100 dark:border-gray-800">

                    {{-- Nama --}}
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Nama Kelas
                        </p>
                    </th>


                    {{-- Kode --}}
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Kode Kelas
                        </p>
                    </th>


                    {{-- Tahun --}}
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Tahun Ajaran
                        </p>
                    </th>


                    {{-- Siswa --}}
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Jumlah Siswa
                        </p>
                    </th>


                    {{-- Wali --}}
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Wali Kelas
                        </p>
                    </th>


                    {{-- Aksi --}}
                    <th class="px-5 py-3 text-right sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Aksi
                        </p>
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse ($classes as $class)

                    <tr class="border-b border-gray-100 dark:border-gray-800">

                        {{-- Nama Kelas --}}
                        <td class="px-5 py-4 sm:px-6">

                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                {{ $class->name }}
                            </p>

                        </td>


                        {{-- Kode Kelas --}}
                        <td class="px-5 py-4 sm:px-6">

                            @if (filled($class->kode_kelas))

                                <span
                                    class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400"
                                >
                                    {{ $class->kode_kelas }}
                                </span>

                            @else

                                <div class="flex flex-col gap-1">

                                    <span
                                        class="inline-flex w-fit items-center rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400"
                                    >
                                        Belum Diatur
                                    </span>

                                    <p class="text-xs text-error-500 dark:text-error-400">
                                        Kode kelas belum diisi.
                                    </p>

                                </div>

                            @endif

                        </td>


                        {{-- Tahun Ajaran --}}
                        <td class="px-5 py-4 sm:px-6">

                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">

                                {{ $class->academicYear?->name }}

                                <span
                                    class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium
                                    {{ $class->academicYear?->is_active
                                        ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500'
                                        : 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400' }}"
                                >

                                    {{ $class->academicYear?->is_active
                                        ? 'Aktif'
                                        : 'Tidak Aktif'
                                    }}

                                </span>

                            </p>

                        </td>


                        {{-- Jumlah Siswa --}}
                        <td class="px-5 py-4 sm:px-6">

                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                {{ $class->students_count }} siswa
                            </p>

                        </td>


                        {{-- Wali Kelas --}}
                        <td class="px-5 py-4 sm:px-6">

                            @if ($class->user)

                                <div class="flex flex-col">

                                    <p class="text-gray-700 text-theme-sm dark:text-gray-300">
                                        {{ $class->user->name }}
                                    </p>

                                    @if ($class->user->email)
                                        <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                            {{ $class->user->email }}
                                        </p>
                                    @endif

                                </div>

                            @else

                                <div class="flex flex-col gap-1">

                                    <span
                                        class="inline-flex w-fit items-center rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400"
                                    >
                                        Belum Diatur
                                    </span>

                                    <p class="text-xs text-error-500 dark:text-error-400">
                                        Wali kelas belum ditentukan.
                                    </p>

                                </div>

                            @endif

                        </td>


                        {{-- Aksi --}}
                        <td class="px-5 py-4 sm:px-6">

                            <div class="flex items-center justify-end gap-3">

                                {{-- Jadwal --}}
                                <a
                                    href="{{ route('classes.schedule', $class) }}"
                                    class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm"
                                >
                                    Jadwal
                                </a>


                                {{-- Edit --}}
                                <a
                                    href="{{ route('classes.edit', $class) }}"
                                    class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm"
                                >
                                    Edit
                                </a>


                                {{-- Hapus --}}
                                <form
                                    method="POST"
                                    action="{{ route('classes.destroy', $class) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus kelas {{ $class->name }}?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="font-medium text-error-500 hover:text-error-600 text-theme-sm"
                                    >
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-5 py-10 text-center sm:px-6"
                        >

                            <div class="flex flex-col items-center justify-center">

                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tidak ada kelas yang ditemukan.
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Silakan ubah filter atau tambahkan kelas baru.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
        PAGINATION
    ====================================================== --}}
    @if ($classes->hasPages())

        <div class="mt-4">

            {{ $classes->links() }}

        </div>

    @endif

</x-common.component-card>

@endsection
