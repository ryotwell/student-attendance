@extends('layouts.app')

@section('content')

<div class="mx-auto w-full max-w-5xl space-y-6">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex items-center gap-3">

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">

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
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9c0-1.042-.133-2.052-.382-3.016z"
                            />
                        </svg>

                    </div>

                    <div>

                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                            Absensi Guru
                        </h1>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Silakan catat kehadiran Anda hari ini.
                        </p>

                    </div>

                </div>

            </div>


            {{-- Tanggal --}}
            <div class="rounded-xl bg-gray-50 px-4 py-3 text-left dark:bg-gray-900 sm:text-right">

                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    HARI INI
                </p>

                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">
                    {{ now()->translatedFormat('l') }}
                </p>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ now()->translatedFormat('d F Y') }}
                </p>

            </div>

        </div>

    </div>


    {{-- ============================================================
         NOTIFICATION SUCCESS
         ============================================================ --}}
    @if(session('success'))

        <div class="rounded-2xl border border-green-200 bg-green-50 p-5 dark:border-green-800 dark:bg-green-500/10">

            <div class="flex items-start gap-4">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/20">

                    <svg
                        class="h-5 w-5 text-green-600 dark:text-green-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>

                </div>

                <div>

                    <p class="font-semibold text-green-800 dark:text-green-300">
                        Berhasil!
                    </p>

                    <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- ============================================================
         ERROR
         ============================================================ --}}
    @if($errors->any())

        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-800 dark:bg-red-500/10">

            <div class="flex items-start gap-4">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20">

                    <svg
                        class="h-5 w-5 text-red-600 dark:text-red-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M10.29 3.86l-7.82 14a1 1 0 001.71 1.71h15.64a1 1 0 001.71-1.71l-7.82-14a1 1 0 00-1.71 0z"
                        />
                    </svg>

                </div>

                <div>

                    <p class="font-semibold text-red-800 dark:text-red-300">
                        Perhatian
                    </p>

                    <div class="mt-1 space-y-1 text-sm text-red-700 dark:text-red-400">

                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- ============================================================
         ABSENSI HARI INI
         ============================================================ --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- ========================================================
             BELUM ABSEN
             ======================================================== --}}
        @if(!$todayAttendance)

            <div class="p-6 sm:p-8">

                {{-- Judul --}}
                <div class="text-center">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/10">

                        <svg
                            class="h-10 w-10 text-brand-500"
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

                    <h2 class="mt-5 text-2xl font-bold text-gray-800 dark:text-white">
                        Selamat Datang!
                    </h2>

                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Sebelum memulai aktivitas, silakan pilih status kehadiran Anda hari ini.
                    </p>

                </div>


                {{-- =================================================
                     FORM
                     ================================================= --}}
                <form
                    action="{{ route('teacher.attendance.check-in') }}"
                    method="POST"
                    class="mx-auto mt-8 max-w-3xl"
                >

                    @csrf


                    {{-- STATUS --}}
                    <div>

                        <p class="mb-4 text-center text-base font-semibold text-gray-800 dark:text-white">
                            Bagaimana kehadiran Anda hari ini?
                        </p>


                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

                            {{-- HADIR --}}
                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="status"
                                    value="HADIR"
                                    class="peer sr-only"
                                    {{ old('status', 'HADIR') === 'HADIR' ? 'checked' : '' }}
                                >

                                <div class="flex min-h-[130px] flex-col items-center justify-center rounded-2xl border-2 border-gray-200 bg-white p-4 text-center transition-all
                                    hover:-translate-y-0.5
                                    hover:border-green-300
                                    hover:bg-green-50
                                    peer-checked:border-green-500
                                    peer-checked:bg-green-50
                                    dark:border-gray-700
                                    dark:bg-gray-900
                                    dark:hover:border-green-500
                                    dark:hover:bg-green-500/10
                                    dark:peer-checked:border-green-500
                                    dark:peer-checked:bg-green-500/10
                                ">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-3xl dark:bg-green-500/20">
                                        ✓
                                    </div>

                                    <p class="mt-3 text-base font-bold text-gray-800 dark:text-white">
                                        Hadir
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Saya hadir hari ini
                                    </p>

                                </div>

                            </label>


                            {{-- IZIN --}}
                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="status"
                                    value="IZIN"
                                    class="peer sr-only"
                                    {{ old('status') === 'IZIN' ? 'checked' : '' }}
                                >

                                <div class="flex min-h-[130px] flex-col items-center justify-center rounded-2xl border-2 border-gray-200 bg-white p-4 text-center transition-all
                                    hover:-translate-y-0.5
                                    hover:border-blue-300
                                    hover:bg-blue-50
                                    peer-checked:border-blue-500
                                    peer-checked:bg-blue-50
                                    dark:border-gray-700
                                    dark:bg-gray-900
                                    dark:hover:border-blue-500
                                    dark:hover:bg-blue-500/10
                                    dark:peer-checked:border-blue-500
                                    dark:peer-checked:bg-blue-500/10
                                ">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-3xl dark:bg-blue-500/20">
                                        📘
                                    </div>

                                    <p class="mt-3 text-base font-bold text-gray-800 dark:text-white">
                                        Izin
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Saya sedang izin
                                    </p>

                                </div>

                            </label>


                            {{-- SAKIT --}}
                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="status"
                                    value="SAKIT"
                                    class="peer sr-only"
                                    {{ old('status') === 'SAKIT' ? 'checked' : '' }}
                                >

                                <div class="flex min-h-[130px] flex-col items-center justify-center rounded-2xl border-2 border-gray-200 bg-white p-4 text-center transition-all
                                    hover:-translate-y-0.5
                                    hover:border-yellow-300
                                    hover:bg-yellow-50
                                    peer-checked:border-yellow-500
                                    peer-checked:bg-yellow-50
                                    dark:border-gray-700
                                    dark:bg-gray-900
                                    dark:hover:border-yellow-500
                                    dark:hover:bg-yellow-500/10
                                    dark:peer-checked:border-yellow-500
                                    dark:peer-checked:bg-yellow-500/10
                                ">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-yellow-100 text-3xl dark:bg-yellow-500/20">
                                        🤒
                                    </div>

                                    <p class="mt-3 text-base font-bold text-gray-800 dark:text-white">
                                        Sakit
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Saya sedang sakit
                                    </p>

                                </div>

                            </label>


                            {{-- ALPHA --}}
                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="status"
                                    value="ALPHA"
                                    class="peer sr-only"
                                    {{ old('status') === 'ALPHA' ? 'checked' : '' }}
                                >

                                <div class="flex min-h-[130px] flex-col items-center justify-center rounded-2xl border-2 border-gray-200 bg-white p-4 text-center transition-all
                                    hover:-translate-y-0.5
                                    hover:border-red-300
                                    hover:bg-red-50
                                    peer-checked:border-red-500
                                    peer-checked:bg-red-50
                                    dark:border-gray-700
                                    dark:bg-gray-900
                                    dark:hover:border-red-500
                                    dark:hover:bg-red-500/10
                                    dark:peer-checked:border-red-500
                                    dark:peer-checked:bg-red-500/10
                                ">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-3xl dark:bg-red-500/20">
                                        ✕
                                    </div>

                                    <p class="mt-3 text-base font-bold text-gray-800 dark:text-white">
                                        Alpha
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Tidak hadir
                                    </p>

                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- =================================================
                         KETERANGAN
                         ================================================= --}}
                    <div class="mt-7">

                        <label
                            for="note"
                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300"
                        >
                            Keterangan
                            <span class="font-normal text-gray-400">
                                (opsional)
                            </span>
                        </label>

                        <textarea
                            id="note"
                            name="note"
                            rows="3"
                            maxlength="1000"
                            placeholder="Contoh: Ada keperluan keluarga..."
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder-gray-600"
                        >{{ old('note') }}</textarea>

                    </div>


                    {{-- =================================================
                         BUTTON
                         ================================================= --}}
                    <div class="mt-7">

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-3 rounded-2xl bg-brand-500 px-6 py-4 text-base font-bold text-white shadow-sm transition hover:bg-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-500/20 active:scale-[0.99]"
                        >

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
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9c0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>

                            <span>
                                Simpan Kehadiran Saya
                            </span>

                        </button>

                        <p class="mt-3 text-center text-xs text-gray-400 dark:text-gray-500">
                            Pastikan status yang dipilih sudah benar sebelum menyimpan.
                        </p>

                    </div>

                </form>

            </div>


        {{-- ========================================================
             SUDAH ABSEN
             ======================================================== --}}
        @else

            @php
                $status = $todayAttendance->status;

                $statusConfig = match($status) {

                    'HADIR' => [
                        'title' => 'Anda Sudah Hadir',
                        'description' => 'Kehadiran Anda hari ini sudah berhasil dicatat.',
                        'icon' => '✓',
                        'bg' => 'bg-green-50 dark:bg-green-500/10',
                        'iconBg' => 'bg-green-100 dark:bg-green-500/20',
                        'iconText' => 'text-green-600 dark:text-green-400',
                        'border' => 'border-green-200 dark:border-green-800',
                    ],

                    'IZIN' => [
                        'title' => 'Status Izin Tercatat',
                        'description' => 'Status izin Anda hari ini sudah berhasil dicatat.',
                        'icon' => '📘',
                        'bg' => 'bg-blue-50 dark:bg-blue-500/10',
                        'iconBg' => 'bg-blue-100 dark:bg-blue-500/20',
                        'iconText' => 'text-blue-600 dark:text-blue-400',
                        'border' => 'border-blue-200 dark:border-blue-800',
                    ],

                    'SAKIT' => [
                        'title' => 'Status Sakit Tercatat',
                        'description' => 'Status sakit Anda hari ini sudah berhasil dicatat.',
                        'icon' => '🤒',
                        'bg' => 'bg-yellow-50 dark:bg-yellow-500/10',
                        'iconBg' => 'bg-yellow-100 dark:bg-yellow-500/20',
                        'iconText' => 'text-yellow-600 dark:text-yellow-400',
                        'border' => 'border-yellow-200 dark:border-yellow-800',
                    ],

                    'ALPHA' => [
                        'title' => 'Status Alpha Tercatat',
                        'description' => 'Status ketidakhadiran Anda hari ini sudah berhasil dicatat.',
                        'icon' => '✕',
                        'bg' => 'bg-red-50 dark:bg-red-500/10',
                        'iconBg' => 'bg-red-100 dark:bg-red-500/20',
                        'iconText' => 'text-red-600 dark:text-red-400',
                        'border' => 'border-red-200 dark:border-red-800',
                    ],

                    default => [
                        'title' => 'Absensi Tercatat',
                        'description' => 'Absensi Anda hari ini sudah dicatat.',
                        'icon' => '✓',
                        'bg' => 'bg-gray-50 dark:bg-gray-500/10',
                        'iconBg' => 'bg-gray-100 dark:bg-gray-500/20',
                        'iconText' => 'text-gray-600 dark:text-gray-400',
                        'border' => 'border-gray-200 dark:border-gray-800',
                    ],
                };
            @endphp


            {{-- STATUS UTAMA --}}
            <div class="p-6 sm:p-8">

                <div class="rounded-2xl border {{ $statusConfig['border'] }} {{ $statusConfig['bg'] }} p-6 text-center sm:p-8">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full {{ $statusConfig['iconBg'] }} text-4xl">

                        <span class="{{ $statusConfig['iconText'] }}">
                            {{ $statusConfig['icon'] }}
                        </span>

                    </div>

                    <h2 class="mt-5 text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $statusConfig['title'] }}
                    </h2>

                    <p class="mx-auto mt-2 max-w-lg text-sm text-gray-600 dark:text-gray-400">
                        {{ $statusConfig['description'] }}
                    </p>

                </div>


                {{-- =================================================
                     INFORMASI ABSENSI
                     ================================================= --}}
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

                    {{-- STATUS --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
                            Status
                        </p>

                        <div class="mt-3 flex items-center gap-3">

                            <span class="text-2xl">
                                {{ $todayAttendance->status_icon }}
                            </span>

                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $todayAttendance->status_label }}
                            </p>

                        </div>

                    </div>


                    {{-- JAM MASUK --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
                            Jam Masuk
                        </p>

                        <div class="mt-3 flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400">

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
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>

                            </div>

                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $todayAttendance->check_in ?? '-' }}
                            </p>

                        </div>

                    </div>


                    {{-- JAM PULANG --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
                            Jam Pulang
                        </p>

                        <div class="mt-3 flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">

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
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>

                            </div>

                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $todayAttendance->check_out ?? '-' }}
                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     CHECK OUT
                     ================================================= --}}
                @if(
                    $todayAttendance->status === 'HADIR' &&
                    !$todayAttendance->check_out
                )

                    <div class="mt-6 rounded-2xl border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">

                        <div class="text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">

                                <svg
                                    class="h-7 w-7"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                                    />
                                </svg>

                            </div>

                            <h3 class="mt-4 text-xl font-bold text-gray-800 dark:text-white">
                                Sudah selesai bekerja?
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">
                                Jika Anda sudah selesai bekerja hari ini, tekan tombol di bawah untuk mencatat jam pulang.
                            </p>


                            <form
                                action="{{ route('teacher.attendance.check-out') }}"
                                method="POST"
                                class="mt-6"
                                onsubmit="return confirm('Apakah Anda yakin sudah selesai bekerja dan ingin melakukan check-out?');"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="w-full rounded-2xl bg-gray-800 px-6 py-4 text-base font-bold text-white shadow-sm transition hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-500/20 active:scale-[0.99] dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 sm:w-auto sm:min-w-[280px]"
                                >

                                    <span class="flex items-center justify-center gap-3">

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
                                                d="M17 8l4 4m0 0l-4 4m4-4H3"
                                            />
                                        </svg>

                                        Saya Sudah Pulang

                                    </span>

                                </button>

                            </form>

                        </div>

                    </div>


                @elseif(
                    $todayAttendance->status === 'HADIR' &&
                    $todayAttendance->check_out
                )

                    <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-500/10">

                        <div class="flex flex-col items-center justify-center gap-3 text-center sm:flex-row sm:text-left">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/20">

                                <svg
                                    class="h-6 w-6 text-green-600 dark:text-green-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>

                            </div>

                            <div>

                                <p class="font-bold text-green-800 dark:text-green-300">
                                    Absensi hari ini sudah selesai.
                                </p>

                                <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                                    Anda masuk pukul {{ $todayAttendance->check_in }}
                                    dan pulang pukul {{ $todayAttendance->check_out }}.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- =================================================
                     KETERANGAN
                     ================================================= --}}
                @if($todayAttendance->note)

                    <div class="mt-6">

                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            Keterangan
                        </p>

                        <div class="mt-2 rounded-xl bg-gray-50 p-4 dark:bg-gray-900">

                            <p class="text-sm leading-6 text-gray-600 dark:text-gray-400">
                                {{ $todayAttendance->note }}
                            </p>

                        </div>

                    </div>

                @endif

            </div>

        @endif

    </div>


    {{-- ============================================================
         RIWAYAT ABSENSI
         ============================================================ --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">

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
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>

                </div>

                <div>

                    <h2 class="text-base font-bold text-gray-800 dark:text-white">
                        Riwayat Absensi
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Lihat catatan kehadiran Anda sebelumnya.
                    </p>

                </div>

            </div>

        </div>


        {{-- ========================================================
             MOBILE: CARD LIST
             ======================================================== --}}
        <div class="space-y-3 p-4 sm:hidden">

            @forelse($attendances as $attendance)

                @php
                    $statusClass = match($attendance->status) {
                        'HADIR' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                        'IZIN' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                        'SAKIT' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                        'ALPHA' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
                    };
                @endphp

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">

                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ $attendance->date->translatedFormat('d F Y') }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $attendance->date->translatedFormat('l') }}
                            </p>

                        </div>


                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">

                            {{ $attendance->status_icon }}

                            {{ $attendance->status_label }}

                        </span>

                    </div>


                    <div class="mt-4 grid grid-cols-2 gap-3">

                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900">

                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Masuk
                            </p>

                            <p class="mt-1 font-semibold text-gray-700 dark:text-gray-300">
                                {{ $attendance->check_in ?? '-' }}
                            </p>

                        </div>


                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900">

                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Pulang
                            </p>

                            <p class="mt-1 font-semibold text-gray-700 dark:text-gray-300">
                                {{ $attendance->check_out ?? '-' }}
                            </p>

                        </div>

                    </div>


                    @if($attendance->note)

                        <div class="mt-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">

                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Keterangan
                            </p>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $attendance->note }}
                            </p>

                        </div>

                    @endif

                </div>

            @empty

                <div class="py-10 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">

                        <svg
                            class="h-6 w-6 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>

                    </div>

                    <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Belum ada riwayat
                    </p>

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Data absensi Anda akan muncul di sini.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- ========================================================
             DESKTOP: TABLE
             ======================================================== --}}
        <div class="hidden overflow-x-auto sm:block">

            <table class="w-full min-w-[700px]">

                <thead>

                    <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900/50">

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Tanggal
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Masuk
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Pulang
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Keterangan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                    @forelse($attendances as $attendance)

                        @php
                            $statusClass = match($attendance->status) {
                                'HADIR' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                'IZIN' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                'SAKIT' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                                'ALPHA' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
                            };
                        @endphp

                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">

                            <td class="px-6 py-4">

                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $attendance->date->translatedFormat('d F Y') }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $attendance->date->translatedFormat('l') }}
                                </p>

                            </td>


                            <td class="px-6 py-4">

                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium {{ $statusClass }}">

                                    {{ $attendance->status_icon }}

                                    {{ $attendance->status_label }}

                                </span>

                            </td>


                            <td class="px-6 py-4">

                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $attendance->check_in ?? '-' }}
                                </span>

                            </td>


                            <td class="px-6 py-4">

                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $attendance->check_out ?? '-' }}
                                </span>

                            </td>


                            <td class="max-w-xs px-6 py-4">

                                @if($attendance->note)

                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $attendance->note }}
                                    </p>

                                @else

                                    <span class="text-sm text-gray-400 dark:text-gray-600">
                                        Tidak ada keterangan
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-12 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">

                                        <svg
                                            class="h-7 w-7 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>

                                    </div>

                                    <p class="mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Belum ada riwayat absensi
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Data absensi akan tampil setelah Anda melakukan absensi.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================
             PAGINATION
             ======================================================== --}}
        @if($attendances->hasPages())

            <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">

                {{ $attendances->links() }}

            </div>

        @endif

    </div>


    {{-- ============================================================
         PETUNJUK SINGKAT
         ============================================================ --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="flex items-start gap-4">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">

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
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
                    />
                </svg>

            </div>

            <div>

                <p class="font-semibold text-gray-800 dark:text-white">
                    Cara melakukan absensi
                </p>

                <ol class="mt-2 space-y-1 text-sm text-gray-500 dark:text-gray-400">

                    <li>
                        1. Pilih status <strong class="text-gray-700 dark:text-gray-300">Hadir</strong> jika Anda datang ke sekolah.
                    </li>

                    <li>
                        2. Tekan tombol <strong class="text-gray-700 dark:text-gray-300">Simpan Kehadiran Saya</strong>.
                    </li>

                    <li>
                        3. Setelah selesai bekerja, tekan <strong class="text-gray-700 dark:text-gray-300">Saya Sudah Pulang</strong>.
                    </li>

                </ol>

            </div>

        </div>

    </div>

</div>

@endsection