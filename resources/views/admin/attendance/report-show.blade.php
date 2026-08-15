@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Filter Laporan">
        <form action="{{ route('attendance.report.show') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Siswa</label>
                    <select name="student_id" id="student_id"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
                        <option value="">-- Semua Siswa --</option>
                        @foreach ($class->students as $s)
                            <option value="{{ $s->id }}" {{ $student && $student->id == $s->id ? 'selected' : '' }}>
                                {{ $s->nis }} - {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

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

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <x-common.component-card class="p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Absensi</div>
        </x-common.component-card>

        <x-common.component-card class="p-4">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $summary['hadir'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Hadir</div>
        </x-common.component-card>

        <x-common.component-card class="p-4">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['izin'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Izin</div>
        </x-common.component-card>

        <x-common.component-card class="p-4">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $summary['sakit'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Sakit</div>
        </x-common.component-card>

        <x-common.component-card class="p-4">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $summary['alpha'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Alpha</div>
        </x-common.component-card>
    </div>
</div>

    <!-- Attendance Table -->
    <x-common.component-card title="Detail Absensi">
        @if ($attendances->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Siswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Jam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($attendances as $attendance)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3">{{ $attendance->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $attendance->schedule->subject->name ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $attendance->student->name }} ({{ $attendance->student->nis }})</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attendance->status_badge_class }}">
                                        {{ $attendance->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $attendance->schedule->start_time }} - {{ $attendance->schedule->end_time }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center">
                {{ $attendances->links() }}
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                Tidak ada data absensi untuk filter ini
            </div>
        @endif
    </x-common.component-card>
@endsection