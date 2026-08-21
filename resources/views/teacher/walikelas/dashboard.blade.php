@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard Wali Kelas" />

    <div class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Kelas {{ $xclass->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Tahun Ajaran {{ $xclass->academicYear->name }} &middot; {{ $xclass->academicYear->semester }}
                </p>
            </div>

            <a href="{{ route('walikelas.rekap', $xclass) }}"
                class="flex w-fit items-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                Lihat Rekap Bulanan →
            </a>
        </div>

        {{-- Ringkasan hari ini --}}
        <div class="mb-6">
            <h3 class="mb-3 text-sm font-semibold text-gray-600 dark:text-gray-300">Kehadiran Hari Ini</h3>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $todayCounts->get('HADIR', 0) }}</div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Hadir</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $todayCounts->get('IZIN', 0) }}</div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Izin</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $todayCounts->get('SAKIT', 0) }}</div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Sakit</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $todayCounts->get('ALPHA', 0) }}</div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Alpha</div>
                </div>
            </div>
        </div>

        {{-- Ringkasan bulan ini --}}
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-brand-50 p-5 dark:bg-brand-900/30 md:col-span-1">
                <div class="text-3xl font-bold text-brand-600 dark:text-brand-400">{{ $attendanceRate }}%</div>
                <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">Tingkat Kehadiran {{ $monthLabel }}</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900 md:col-span-2">
                <h4 class="mb-3 text-sm font-semibold text-gray-600 dark:text-gray-300">Siswa dengan Alpha Terbanyak ({{ $monthLabel }})</h4>

                @forelse ($topAlpha as $row)
                    <div class="flex items-center justify-between border-b border-gray-100 py-2 last:border-0 dark:border-gray-800">
                        <span class="text-sm text-gray-800 dark:text-white">{{ $row->student->name }}</span>
                        <span class="rounded-lg bg-red-100 px-2 py-1 text-xs font-bold text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            {{ $row->total }}x Alpha
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada catatan Alpha bulan ini. 🎉</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection