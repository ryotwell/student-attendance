@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard Admin" />

    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Dashboard</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan sistem sekolah</p>
        </div>

        {{-- Kartu ringkasan sistem --}}
        <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-900/30 dark:text-brand-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totals['students'] }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Siswa</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-500 dark:bg-purple-900/30 dark:text-purple-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totals['classes'] }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Kelas</div>
            </div>

            {{-- KARTU INI DIUBAH LABELNYA --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totals['subjects'] }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Jadwal Pelajaran</div> {{-- <-- DIUBAH --}}
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-500 dark:bg-green-900/30 dark:text-green-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totals['teachers'] }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Guru</div>
            </div>
        </div>

        {{-- Kehadiran hari ini --}}
        <div class="mb-6">
            <h3 class="mb-3 text-sm font-semibold text-gray-600 dark:text-gray-300">Kehadiran Siswa Hari Ini</h3>
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Ringkasan bulan ini --}}
            <div class="rounded-2xl bg-brand-50 p-5 dark:bg-brand-900/30 lg:col-span-1">
                <div class="text-3xl font-bold text-brand-600 dark:text-brand-400">{{ $attendanceRate }}%</div>
                <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">Tingkat Kehadiran Siswa Sekolah</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $monthLabel }}</div>

                <div class="mt-4 space-y-1.5 border-t border-brand-100 pt-4 text-xs dark:border-brand-800">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Hadir</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $monthCounts->get('HADIR', 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Izin</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $monthCounts->get('IZIN', 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Sakit</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $monthCounts->get('SAKIT', 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Alpha</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $monthCounts->get('ALPHA', 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Aktivitas terbaru --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 lg:col-span-2">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Aktivitas Terbaru</h4>
                </div>
                <div class="max-h-[380px] overflow-y-auto">
                    @forelse ($recentActivities as $activity)
                        <div class="flex items-start gap-3 border-b border-gray-100 px-5 py-3 last:border-0 dark:border-gray-800">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                                {{ $activity['type'] === 'user'
                                    ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'
                                    : 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                @if ($activity['type'] === 'user')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                @else
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activity['timestamp']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection