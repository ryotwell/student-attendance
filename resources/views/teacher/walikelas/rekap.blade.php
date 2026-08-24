@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rekap Absensi Kelas" />

    <div class="mx-auto max-w-6xl">

        {{-- Header info --}}
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    Kelas {{ $xclass->name }}
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Tahun Ajaran {{ $xclass->academicYear->name }}
                    &middot;
                    {{ $xclass->academicYear->semester }}
                    &middot;
                    Rekap lintas semua mata pelajaran
                </p>
            </div>


            <div class="flex flex-wrap gap-3">

                <a href="{{ route('walikelas.rekap.export', [$xclass, 'month' => $selectedMonth]) }}"
                    class="flex w-fit items-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">

                    <svg width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                    </svg>

                    Export PDF
                </a>


                <a href="{{ route('walikelas.dashboard', $xclass) }}"
                    class="flex w-fit items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">

                    ← Kembali ke Dashboard

                </a>

            </div>
        </div>



        {{-- Pilihan bulan --}}
        <div class="mb-6 overflow-x-auto">

            <div class="flex min-w-max gap-2 pb-2">

                @foreach ($monthOptions as $option)

                    <a href="{{ route('walikelas.rekap', [$xclass, 'month' => $option['value']]) }}"
                        class="rounded-xl px-4 py-3 text-sm font-semibold transition
                        {{ $selectedMonth === $option['value']
                            ? 'bg-brand-500 text-white'
                            : 'border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">

                        {{ $option['label'] }}

                    </a>

                @endforeach

            </div>

        </div>



        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">

            <div class="overflow-x-auto">

                <table class="min-w-[750px] divide-y divide-gray-100 whitespace-nowrap dark:divide-gray-800">


                    <thead class="bg-gray-50 dark:bg-gray-800">

                        <tr>

                            <th class="sticky left-0 z-10 bg-gray-50 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                Nama Siswa
                            </th>


                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">
                                Hadir
                            </th>


                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                                Izin
                            </th>


                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-yellow-600 dark:text-yellow-400">
                                Sakit
                            </th>


                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">
                                Alpha
                            </th>


                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                % Hadir
                            </th>

                        </tr>

                    </thead>



                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">


                        @forelse ($recap as $row)

                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/60">


                                <td class="sticky left-0 z-10 bg-white px-5 py-3 text-sm font-medium text-gray-800 dark:bg-gray-900 dark:text-white">
                                    {{ $row['student']->name }}
                                </td>



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



                                <td class="px-5 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">

                                    {{ $row['rate'] }}%

                                </td>


                            </tr>


                        @empty

                            <tr>

                                <td colspan="6"
                                    class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">

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