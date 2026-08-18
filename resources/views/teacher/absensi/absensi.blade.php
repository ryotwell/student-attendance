@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <div class="mx-auto max-w-6xl">
        <x-common.component-card title="Absensi {{ $class->name }} - {{ $schedule->subject->name }}">
            {{-- Header info --}}
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $class->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->subject->name }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    {{-- Jumlah siswa --}}
                    <div class="flex items-center gap-2 rounded-xl bg-brand-50 px-4 py-3 dark:bg-brand-900/30">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-500">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span class="font-semibold text-brand-600 dark:text-brand-400">{{ $class->students->count() }} Siswa</span>
                    </div>

                    {{-- Pilih tanggal absensi --}}
                    <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-500">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>

                        <div>
                            <label for="attendanceDate" class="block text-xs text-gray-500 dark:text-gray-400">Tanggal Absensi</label>
                            <input type="date" id="attendanceDate" value="{{ $date->format('Y-m-d') }}"
                                class="mt-1 block cursor-pointer bg-transparent text-sm font-semibold text-gray-800 outline-none dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf

                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($class->students as $index => $student)
                        @php
                            $existing = $existingAttendances->get($student->id);
                            $currentStatus = $existing->status ?? 'ALPHA';
                        @endphp

                        <div class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900">
                            {{-- Data siswa --}}
                            <div class="mb-5 flex items-center gap-4">
                                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>

                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">{{ $student->name }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">NIS : {{ $student->nis }}</p>
                                </div>
                            </div>

                            <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">

                            {{-- Pilihan status kehadiran --}}
                            <div class="grid grid-cols-4 gap-2">
                                @foreach (App\Models\Attendance::STATUS_OPTIONS as $key => $option)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendances[{{ $index }}][status]" value="{{ $key }}"
                                            class="sr-only peer" @checked($currentStatus === $key)>
                                        <div class="rounded-xl bg-gray-100 p-3 text-center text-gray-500 transition-all hover:scale-105 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-400 dark:bg-gray-800 {{ $option['checkedClass'] }}">
                                            <div class="mb-1 text-xl">{{ $option['icon'] }}</div>
                                            <div class="text-xs font-bold">{{ $option['label'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-12 text-center dark:border-gray-700 dark:bg-gray-900/50">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada siswa terdaftar di kelas ini.</p>
                        </div>
                    @endforelse
                </div>


                {{-- Aksi --}}
                <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
                    <a href="{{ route('absensi.schedules') }}"
                        class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        ← Kembali
                    </a>

                    <button type="submit"
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                        </svg>
                        Simpan Absensi
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('attendanceDate');

        if (!dateInput) return;

        dateInput.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('date', this.value);
            window.location.href = url.toString();
        });
    });
</script>
@endpush

