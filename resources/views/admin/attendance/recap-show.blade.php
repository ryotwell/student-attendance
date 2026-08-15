@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Filter Rekap">
        <form action="{{ route('attendance.recap.show') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition w-full">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </x-common.component-card>

    <!-- Overall Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
        <x-common.component-card class="p-3 sm:p-4">
            <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total'] }}</div>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Total Absensi</div>
        </x-common.component-card>

        <x-common.component-card class="p-3 sm:p-4">
            <div class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">{{ $summary['hadir'] }}</div>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Hadir</div>
        </x-common.component-card>

        <x-common.component-card class="p-3 sm:p-4">
            <div class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['izin'] }}</div>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Izin</div>
        </x-common.component-card>

        <x-common.component-card class="p-3 sm:p-4">
            <div class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $summary['sakit'] }}</div>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Sakit</div>
        </x-common.component-card>

        <x-common.component-card class="p-3 sm:p-4">
            <div class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400">{{ $summary['alpha'] }}</div>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Alpha</div>
        </x-common.component-card>

        <x-common.component-card class="p-3 sm:p-4">
            <div class="text-xl sm:text-2xl font-bold {{ $summary['percentage'] >= 75 ? 'text-green-600 dark:text-green-400' : ($summary['percentage'] >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                {{ $summary['percentage'] }}%
            </div>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Kehadiran</div>
        </x-common.component-card>
    </div>

    <!-- Student Recap Table -->
    <x-common.component-card title="Rekap per Siswa">
        @if ($studentRecaps->isNotEmpty())
            <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                <table class="w-full min-w-[640px] text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-medium text-gray-500 dark:text-gray-400">No</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-medium text-gray-500 dark:text-gray-400">NIS</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nama Siswa</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-green-600 dark:text-green-400">Hadir</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-blue-600 dark:text-blue-400">Izin</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-yellow-600 dark:text-yellow-400">Sakit</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-red-600 dark:text-red-400">Alpha</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-gray-500 dark:text-gray-400">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($studentRecaps as $index => $recap)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $index + 1 }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $recap['student']->nis }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 font-medium">{{ $recap['student']->name }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">{{ $recap['total'] }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-green-600 font-medium">{{ $recap['hadir'] }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-blue-600 font-medium">{{ $recap['izin'] }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-yellow-600 font-medium">{{ $recap['sakit'] }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-red-600 font-medium">{{ $recap['alpha'] }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-center font-bold {{ $recap['percentage'] >= 75 ? 'text-green-600' : ($recap['percentage'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $recap['percentage'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                Tidak ada siswa di kelas ini
            </div>
        @endif
    </x-common.component-card>
@endsection