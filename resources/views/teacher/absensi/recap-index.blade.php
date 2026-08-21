@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rekap Absensi" />

    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pilih Jadwal</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih jadwal mengajar untuk melihat rekap absensi.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($schedules as $schedule)
                <a href="{{ route('absensi.recap.show', $schedule) }}"
                    class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-900/30 dark:text-brand-400">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white">{{ $schedule->subject->name }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelas {{ $schedule->xclass->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 rounded-xl bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        {{ \App\Helpers\MenuHelper::getDayName($schedule->day) }}, {{ $schedule->start_time->format('H:i') }}–{{ $schedule->end_time->format('H:i') }}
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-12 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Anda belum memiliki jadwal mengajar.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection