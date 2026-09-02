@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Detail History Absensi" />

<div class="mx-auto max-w-6xl">

    @php
        $first = $attendances->first();
    @endphp

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Kelas {{ $first->xclass->name }}
            </h2>

            {{-- PERUBAHAN: subject_name langsung --}}
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Mata Pelajaran {{ $first->schedule->subject_name }}
            </p>
        </div>

        <a href="{{ route('absensi.history') }}"
            class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            ← Kembali
        </a>

    </div>


    {{-- Info --}}
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">

        <div class="flex items-center gap-3 rounded-xl bg-brand-50 px-4 py-3 dark:bg-brand-900/30">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-brand-600 dark:bg-gray-900 dark:text-brand-400">

                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">

                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>

                </svg>

            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Jumlah Siswa
                </p>

                <p class="font-bold text-brand-600 dark:text-brand-400">
                    {{ $attendances->count() }} Siswa
                </p>
            </div>

        </div>


        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">

                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">

                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>

                </svg>

            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Tanggal
                </p>

                <p class="font-bold text-gray-800 dark:text-white">
                    {{ $first->date->translatedFormat('d F Y') }}
                </p>
            </div>

        </div>

    </div>


    {{-- Search --}}
    <div class="mb-5">

        <div class="relative">

            <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2">

                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>

            </svg>


            <input id="studentSearch"
                type="text"
                placeholder="Cari nama atau NIS siswa..."
                class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

        </div>

    </div>


    {{-- Student Card --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

        @foreach($attendances as $attendance)

            @php
                $statusStyle = match($attendance->status) {
                    'HADIR' => [
                        'box' => 'bg-green-50 dark:bg-green-900/30',
                        'text' => 'text-green-700 dark:text-green-400',
                        'icon' => 'bg-green-100 dark:bg-green-900/50'
                    ],

                    'IZIN' => [
                        'box' => 'bg-blue-50 dark:bg-blue-900/30',
                        'text' => 'text-blue-700 dark:text-blue-400',
                        'icon' => 'bg-blue-100 dark:bg-blue-900/50'
                    ],

                    'SAKIT' => [
                        'box' => 'bg-yellow-50 dark:bg-yellow-900/30',
                        'text' => 'text-yellow-700 dark:text-yellow-400',
                        'icon' => 'bg-yellow-100 dark:bg-yellow-900/50'
                    ],

                    'ALPHA' => [
                        'box' => 'bg-red-50 dark:bg-red-900/30',
                        'text' => 'text-red-700 dark:text-red-400',
                        'icon' => 'bg-red-100 dark:bg-red-900/50'
                    ],

                    default => [
                        'box' => 'bg-gray-50 dark:bg-gray-800',
                        'text' => 'text-gray-700 dark:text-gray-300',
                        'icon' => 'bg-gray-100 dark:bg-gray-700'
                    ],
                };
            @endphp


            <div class="student-card group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900"
                data-name="{{ strtolower($attendance->student->name) }}"
                data-nis="{{ strtolower($attendance->student->nis) }}">


                <span class="absolute left-0 top-0 h-full w-1 bg-brand-500 transition-all group-hover:w-2"></span>


                {{-- Siswa --}}
                <div class="mb-5 flex items-center gap-4">

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:scale-110 dark:bg-brand-900/30 dark:text-brand-400">

                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.7">

                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>

                        </svg>

                    </div>


                    <div>

                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $attendance->student->name }}
                        </h4>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            NIS : {{ $attendance->student->nis }}
                        </p>

                    </div>

                </div>


                {{-- Status --}}
                <div class="rounded-xl {{ $statusStyle['box'] }} px-4 py-3 {{ $statusStyle['text'] }}">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $statusStyle['icon'] }} text-2xl">

                            {{ App\Models\Attendance::STATUS_OPTIONS[$attendance->status]['icon'] }}

                        </div>

                        <div>

                            <p class="text-xs opacity-70">
                                Status Kehadiran
                            </p>

                            <p class="font-bold">
                                {{ $attendance->statusLabel }}
                            </p>

                        </div>

                    </div>

                </div>


            </div>

        @endforeach

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('studentSearch');
    const cards = document.querySelectorAll('.student-card');

    input.addEventListener('input', function () {

        const keyword = this.value.toLowerCase();

        cards.forEach(card => {

            const name = card.dataset.name;
            const nis = card.dataset.nis;

            card.style.display =
                name.includes(keyword) || nis.includes(keyword)
                    ? ''
                    : 'none';
        });
    });
});

</script>

@endpush