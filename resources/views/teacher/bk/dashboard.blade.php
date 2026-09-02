@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard BK" />

    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Dashboard Bimbingan Konseling</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pantauan kehadiran siswa sekolah-wide</p>
        </div>

        {{-- Kehadiran hari ini --}}
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
        <div class="mb-8 rounded-2xl bg-brand-50 p-5 dark:bg-brand-900/30">
            <div class="text-3xl font-bold text-brand-600 dark:text-brand-400">{{ $attendanceRate }}%</div>
            <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">Tingkat Kehadiran Sekolah — {{ $monthLabel }}</div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Breakdown per kelas --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kehadiran per Kelas — {{ $monthLabel }}</h4>
                </div>
                <div class="max-h-[420px] overflow-y-auto">
                    @forelse ($classBreakdown as $row)
                        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3 last:border-0 dark:border-gray-800">
                            <div>
                                <div class="text-sm font-medium text-gray-800 dark:text-white">{{ $row['xclass']->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row['xclass']->students_count }} siswa</div>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($row['alpha'] > 0)
                                    <span class="rounded-lg bg-red-100 px-2 py-1 text-xs font-bold text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        {{ $row['alpha'] }}x Alpha
                                    </span>
                                @endif

                                @if (is_null($row['rate']))
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Belum ada data</span>
                                @else
                                    <span class="text-sm font-bold
                                        {{ $row['rate'] >= 90 ? 'text-green-600 dark:text-green-400' : ($row['rate'] >= 75 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                        {{ $row['rate'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data kelas.</div>
                    @endforelse
                </div>
            </div>

            {{-- Top siswa Alpha terbanyak --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Siswa dengan Alpha Terbanyak — {{ $monthLabel }}</h4>
                </div>
                <div class="max-h-[420px] overflow-y-auto">
                    @forelse ($topAlpha as $row)
                        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3 last:border-0 dark:border-gray-800">
                            <div>
                                {{-- Attendance::getStudentAttribute() sudah mengarah ke
                                     studentEnrollment->student, jadi $row->student tetap aman.
                                     Kelas TIDAK bisa lewat $row->student->xclass (Student tidak
                                     punya relasi xclass) — harus lewat studentEnrollment->xclass. --}}
                                <div class="text-sm font-medium text-gray-800 dark:text-white">{{ $row->student->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row->studentEnrollment->xclass->name }}</div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="rounded-lg bg-red-100 px-2 py-1 text-xs font-bold text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    {{ $row->total }}x
                                </span>
                                <a href="{{ route('bk.cases.by-student', $row->student) }}"
                                    class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">
                                    Lihat →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada catatan Alpha bulan ini. 🎉</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection