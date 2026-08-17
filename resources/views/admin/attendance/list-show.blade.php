@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Jadwal Mengajar">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hari</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Waktu</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($schedules as $schedule)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3">{{ \App\Helpers\MenuHelper::getDayName($schedule->day) }}</td>
                            <td class="px-4 py-3 font-medium">{{ $schedule->subject->name }}</td>
                            <td class="px-4 py-3">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('attendance.show', ['class_id' => $class->id, 'schedule_id' => $schedule->id, 'date' => now()->format('Y-m-d')]) }}"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">
                                    Input Baru
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada jadwal untuk kelas ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-common.component-card>

    <x-common.component-card title="Riwayat Absensi Terbaru" class="mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hari</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Waktu</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hadir</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Izin</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Sakit</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Alpha</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
@forelse ($attendances->groupBy(function($item) {
                        return $item->date->format('Y-m-d') . '|' . $item->schedule_id;
                    }) as $group)
                        @php
                            $first = $group->first();
                            $stats = [
                                'HADIR' => $group->where('status', 'HADIR')->count(),
                                'IZIN' => $group->where('status', 'IZIN')->count(),
                                'SAKIT' => $group->where('status', 'SAKIT')->count(),
                                'ALPHA' => $group->where('status', 'ALPHA')->count(),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3">{{ $first->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-medium">{{ $first->schedule->subject->name }}</td>
                            <td class="px-4 py-3">{{ \App\Helpers\MenuHelper::getDayName($first->schedule->day) }}</td>
                            <td class="px-4 py-3">{{ $first->schedule->start_time }} - {{ $first->schedule->end_time }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $stats['HADIR'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $stats['IZIN'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    {{ $stats['SAKIT'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    {{ $stats['ALPHA'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('attendance.list.edit', [$class, $first->schedule_id, $first->date->format('Y-m-d')]) }}"
                                    class="px-3 py-1.5 text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/30 rounded-lg hover:bg-brand-100 dark:hover:bg-brand-900/50 transition-colors">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data absensi untuk kelas ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($attendances->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $attendances->links() }}
            </div>
        @endif
    </x-common.component-card>
@endsection