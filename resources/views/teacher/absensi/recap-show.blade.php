@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rekap Absensi" />

    <div class="mx-auto max-w-6xl">
        {{-- Header info --}}
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $schedule->subject->name }}</h2>
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

            <div class="overflow-x-auto">

                <table class="min-w-[300px] w-full divide-y divide-gray-100 dark:divide-gray-800">

                    {{-- Header --}}
                    <thead class="bg-brand-50 dark:bg-brand-900/20">
                        <tr>

                            <th class="sticky left-0 z-10 bg-brand-50 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-brand-600 dark:bg-brand-900/20 dark:text-brand-400">
                                Nama Siswa
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">
                                Hadir
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">
                                Izin
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">
                                Sakit
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">
                                Alpha
                            </th>

                        </tr>

                    </thead>


                    {{-- Body --}}
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">


                        @forelse ($recap as $row)

                            <tr class="transition hover:bg-brand-50/50 dark:hover:bg-gray-800/60">


                                {{-- Nama --}}
                                <td class="sticky left-0 bg-white px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-900 dark:text-white">

                                    {{ $row['student']->name }}

                                </td>



                                {{-- Hadir --}}
                                <td class="px-5 py-3 text-center">

                                    <span class="inline-flex min-w-10 justify-center rounded-lg bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700 dark:bg-brand-900/30 dark:text-brand-400">

                                        {{ $row['HADIR'] }}

                                    </span>

                                </td>




                                {{-- Izin --}}
                                <td class="px-5 py-3 text-center">

                                    <span class="inline-flex min-w-10 justify-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">

                                        {{ $row['IZIN'] }}

                                    </span>

                                </td>




                                {{-- Sakit --}}
                                <td class="px-5 py-3 text-center">

                                    <span class="inline-flex min-w-10 justify-center rounded-lg bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">

                                        {{ $row['SAKIT'] }}

                                    </span>

                                </td>




                                {{-- Alpha --}}
                                <td class="px-5 py-3 text-center">

                                    <span class="inline-flex min-w-10 justify-center rounded-lg bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">

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
    </div>
@endsection