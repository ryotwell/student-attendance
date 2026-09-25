@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb pageTitle="Absensi Guru" />

    {{-- ========================================================= --}}
    {{-- ALERT --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert
                variant="success"
                title="Berhasil"
                :message="session('success')"
            />
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6">
            <x-ui.alert
                variant="error"
                title="Terjadi Kesalahan"
                :message="session('error')"
            />
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- STATISTIK --}}
    {{-- ========================================================= --}}

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- TOTAL --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Absensi
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ $statistics['total'] ?? 0 }}
                    </h3>

                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">

                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- HADIR --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Hadir
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ $statistics['hadir'] ?? 0 }}
                    </h3>

                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400">

                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- IZIN --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Izin
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ $statistics['izin'] ?? 0 }}
                    </h3>

                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">

                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- SAKIT --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Sakit
                    </p>

                    <h3 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ $statistics['sakit'] ?? 0 }}
                    </h3>

                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400">

                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"
                        />
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- STATUS TAMBAHAN --}}
    {{-- ========================================================= --}}

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- ALPHA --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Tidak Hadir
                    </p>

                    <p class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ $statistics['alpha'] ?? 0 }}
                    </p>

                </div>

            </div>

        </div>


        {{-- SUDAH PULANG --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l9-9 9 9M5 10v10h14V10"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Sudah Pulang
                    </p>

                    <p class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ $statistics['sudah_pulang'] ?? 0 }}
                    </p>

                </div>

            </div>

        </div>


        {{-- BELUM PULANG --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Belum Pulang
                    </p>

                    <p class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ $statistics['belum_pulang'] ?? 0 }}
                    </p>

                </div>

            </div>

        </div>


        {{-- GURU TERDAFTAR --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Guru Terdaftar
                    </p>

                    <p class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ $teachers->count() }}
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DATA --}}
    {{-- ========================================================= --}}

    <x-common.component-card title="Daftar Absensi Guru">

        <div class="flex flex-col gap-5">

            {{-- HEADER --}}
            <div class="flex flex-wrap items-center justify-between gap-4">

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $attendances->total() }} data absensi ditemukan
                </p>


                {{-- EXPORT PDF --}}
                <a
                    href="{{ route('admin.teacher-attendance.export-pdf', request()->query()) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600"
                >

                    <svg
                        class="mr-2 h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>

                    Export PDF

                </a>

            </div>


            {{-- ================================================= --}}
            {{-- FILTER --}}
            {{-- ================================================= --}}

            <form
                method="GET"
                action="{{ route('admin.teacher-attendance.index') }}"
                class="flex flex-wrap items-end gap-4"
            >

                {{-- SEARCH --}}
                <div class="min-w-[220px] flex-1">

                    <label
                        for="search"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        Cari
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nama atau email guru"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    />

                </div>


                {{-- TANGGAL --}}
                <div class="w-full sm:w-40">

                    <label
                        for="date"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        Tanggal
                    </label>

                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />

                </div>


                {{-- BULAN --}}
                <div class="w-full sm:w-36">

                    <label
                        for="month"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        Bulan
                    </label>

                    <input
                        type="month"
                        id="month"
                        name="month"
                        value="{{ request('month') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    />

                </div>


                {{-- GURU --}}
                <div class="w-full sm:w-52">

                    <label
                        for="teacher_id"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        Guru
                    </label>

                    <select
                        id="teacher_id"
                        name="teacher_id"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >

                        <option value="">
                            Semua Guru
                        </option>

                        @foreach ($teachers as $teacher)

                            <option
                                value="{{ $teacher->id }}"
                                @selected(request('teacher_id') == $teacher->id)
                            >
                                {{ $teacher->name }}
                                @if ($teacher->role === 'GURU_BK')
                                    (Guru BK)
                                @endif
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- STATUS --}}
                <div class="w-full sm:w-36">

                    <label
                        for="status"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >

                        <option value="">
                            Semua
                        </option>

                        <option
                            value="HADIR"
                            @selected(request('status') === 'HADIR')
                        >
                            Hadir
                        </option>

                        <option
                            value="IZIN"
                            @selected(request('status') === 'IZIN')
                        >
                            Izin
                        </option>

                        <option
                            value="SAKIT"
                            @selected(request('status') === 'SAKIT')
                        >
                            Sakit
                        </option>

                        <option
                            value="ALPHA"
                            @selected(request('status') === 'ALPHA')
                        >
                            Tidak Hadir
                        </option>

                    </select>

                </div>


                {{-- BUTTON --}}
                <div class="flex items-center gap-2">

                    <button
                        type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-4 text-sm font-medium text-white transition"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.teacher-attendance.index') }}"
                        class="inline-flex h-11 items-center justify-center rounded-lg px-4 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
                    >
                        Reset
                    </a>

                </div>

            </form>


            {{-- ================================================= --}}
            {{-- TABLE --}}
            {{-- ================================================= --}}

            <div class="max-w-full overflow-x-auto custom-scrollbar">

                <table class="w-full min-w-[1000px]">

                    <thead>

                        <tr class="border-b border-gray-100 dark:border-gray-800">

                            <th class="px-3 py-3 text-center sm:px-4">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    #
                                </p>
                            </th>

                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Guru
                                </p>
                            </th>

                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Tanggal
                                </p>
                            </th>

                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Status
                                </p>
                            </th>

                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Jam Masuk
                                </p>
                            </th>

                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Jam Pulang
                                </p>
                            </th>

                            <th class="px-5 py-3 text-left sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Keterangan
                                </p>
                            </th>

                            <th class="px-5 py-3 text-right sm:px-6">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Aksi
                                </p>
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($attendances as $attendance)

                            <tr class="border-b border-gray-100 dark:border-gray-800">

                                {{-- NO --}}
                                <td class="px-3 py-4 text-center sm:px-4">

                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loop->iteration + $attendances->firstItem() - 1 }}
                                    </span>

                                </td>


                                {{-- GURU --}}
                                <td class="px-5 py-4 sm:px-6">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 font-semibold text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">

                                            {{ strtoupper(substr($attendance->user?->name ?? 'G', 0, 1)) }}

                                        </div>

                                        <div>

                                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                {{ $attendance->user?->name ?? '-' }}
                                            </p>

                                            <p class="text-xs text-gray-500 dark:text-gray-400">

                                                @if ($attendance->user?->role === 'GURU_BK')
                                                    Guru BK
                                                @else
                                                    Guru
                                                @endif

                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- TANGGAL --}}
                                <td class="px-5 py-4 sm:px-6">

                                    <p class="text-gray-800 text-theme-sm dark:text-white/90">
                                        {{ $attendance->date?->translatedFormat('d F Y') ?? '-' }}
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $attendance->date?->translatedFormat('l') ?? '-' }}
                                    </p>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4 sm:px-6">

                                    @if ($attendance->status === 'HADIR')

                                        <span class="text-theme-xs inline-block rounded-full bg-success-50 px-2 py-0.5 font-medium text-success-700 dark:bg-success-500/15 dark:text-success-400">
                                            Hadir
                                        </span>

                                    @elseif ($attendance->status === 'IZIN')

                                        <span class="text-theme-xs inline-block rounded-full bg-warning-50 px-2 py-0.5 font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">
                                            Izin
                                        </span>

                                    @elseif ($attendance->status === 'SAKIT')

                                        <span class="text-theme-xs inline-block rounded-full bg-error-50 px-2 py-0.5 font-medium text-error-700 dark:bg-error-500/15 dark:text-error-400">
                                            Sakit
                                        </span>

                                    @else

                                        <span class="text-theme-xs inline-block rounded-full bg-gray-100 px-2 py-0.5 font-medium text-gray-700 dark:bg-gray-500/15 dark:text-gray-400">
                                            Tidak Hadir
                                        </span>

                                    @endif

                                </td>


                                {{-- MASUK --}}
                                <td class="px-5 py-4 sm:px-6">

                                    @if ($attendance->check_in)

                                        <span class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- PULANG --}}
                                <td class="px-5 py-4 sm:px-6">

                                    @if ($attendance->check_out)

                                        <span class="font-medium text-success-600 text-theme-sm dark:text-success-400">
                                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                        </span>

                                    @elseif ($attendance->status === 'HADIR')

                                        <span class="text-theme-xs inline-block rounded-full bg-warning-50 px-2 py-0.5 font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">
                                            Belum pulang
                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- KETERANGAN --}}
                                <td class="px-5 py-4 sm:px-6">

                                    @if ($attendance->note)

                                        <p
                                            class="max-w-[200px] truncate text-gray-500 text-theme-sm dark:text-gray-400"
                                            title="{{ $attendance->note }}"
                                        >
                                            {{ $attendance->note }}
                                        </p>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- AKSI --}}
                                <td class="px-5 py-4 text-right sm:px-6">

                                    <a
                                        href="{{ route('admin.teacher-attendance.show', $attendance) }}"
                                        class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm"
                                    >
                                        Lihat Detail
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="px-5 py-12 text-center sm:px-6"
                                >

                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Tidak ada data absensi guru yang ditemukan.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ================================================= --}}
            {{-- PAGINATION --}}
            {{-- ================================================= --}}

            @if ($attendances->hasPages())

                <div class="mt-4">

                    {{ $attendances->links() }}

                </div>

            @endif

        </div>

    </x-common.component-card>

@endsection