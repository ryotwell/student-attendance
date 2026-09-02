@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rekap Absensi" />

    <div class="mx-auto max-w-6xl">
        {{-- Header info --}}
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                {{-- PERUBAHAN: subject_name langsung --}}
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $schedule->subject_name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kelas {{ $schedule->xclass->name }} &middot; {{ App\Helpers\MenuHelper::getDayName($schedule->day) }}, {{ $schedule->start_time->format('H:i') }}–{{ $schedule->end_time->format('H:i') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('absensi.recap.export', [$schedule, 'month' => $selectedMonth]) }}"
                    class="flex w-fit items-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                    </svg>
                    Export PDF
                </a>

                <a href="{{ route('absensi.recap') }}"
                    class="flex w-fit items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    ← Pilih Jadwal Lain
                </a>
            </div>
        </div>

        {{-- Pilihan bulan --}}
        <div class="mb-6 flex flex-wrap gap-2">
            @foreach ($monthOptions as $option)
                <a href="{{ route('absensi.recap.show', [$schedule, 'month' => $option['value']]) }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold transition
                        {{ $selectedMonth === $option['value']
                            ? 'bg-brand-500 text-white'
                            : 'border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    {{ $option['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Ringkasan --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Siswa</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">Hadir</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">Izin</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-yellow-600 dark:text-yellow-400">Sakit</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">Alpha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">
                    @forelse ($recap as $row)
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <td class="px-5 py-3 text-sm font-medium text-gray-800 dark:text-white">{{ $row['student']->name }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex min-w-8 justify-center rounded-lg bg-green-100 px-2 py-1 text-xs font-bold text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $row['HADIR'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex min-w-8 justify-center rounded-lg bg-blue-100 px-2 py-1 text-xs font-bold text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $row['IZIN'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex min-w-8 justify-center rounded-lg bg-yellow-100 px-2 py-1 text-xs font-bold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    {{ $row['SAKIT'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex min-w-8 justify-center rounded-lg bg-red-100 px-2 py-1 text-xs font-bold text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    {{ $row['ALPHA'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection