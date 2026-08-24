@php
    $user = Auth::user();

    $user->load(['teacherDocument']);
    $user->loadExists('classes');

    $menuItems = [
        [
            'title' => 'Input Absensi',
            'description' => 'Input dan kelola data kehadiran siswa',
            'url' => route('absensi.schedules'),
            'icon' => 'clipboard-plus',
        ],
        [
            'title' => 'Rekap Absensi',
            'description' => 'Lihat dan cetak rekap kehadiran siswa',
            'url' => route('absensi.recap'),
            'icon' => 'file-chart',
        ],
        [
            'title' => 'Riwayat Absensi',
            'description' => 'Lihat riwayat absensi berdasarkan jadwal & tanggal',
            'url' => route('absensi.history'),
            'icon' => 'calendar-check',
        ],
        [
            'title' => 'Jadwal Mengajar',
            'description' => 'Lihat jadwal kelas dan jam mengajar',
            'url' => route('schedules.index'),
            'icon' => 'calendar',
        ],
        [
            'title' => 'Pengumuman',
            'description' => 'Baca informasi dan pengumuman terbaru',
            'url' => route('announcements.index'),
            'icon' => 'megaphone',
        ],
    ];


    if ($user->role === 'GURU' && $user->isWaliKelas()) {
        $menuItems[] = [
            'title' => 'Menu Wali Kelas',
            'description' => 'Kelola dan pantau kelas yang Anda wali-i',
            'url' => route('walikelas.index'),
            'icon' => 'users-group',
        ];
    }


    if ($user->role === 'GURU') {
        $menuItems[] = [
            'title' => 'Dokumen Dapodik',
            'description' => 'Kelola dokumen dan status verifikasi Dapodik',
            'url' => route('teacher.documents.index'),
            'icon' => 'document',
        ];
    }
@endphp


@extends('layouts.app')


@section('content')

@if($user->role === 'GURU')

    @if(!$user->teacherDocument)

        <div class="mb-6 rounded-2xl border border-yellow-200 bg-yellow-50 p-5 dark:border-yellow-900/40 dark:bg-yellow-900/20">
            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-100 text-yellow-600 dark:bg-yellow-900/40 dark:text-yellow-400">
                    <svg width="26" height="26" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.3 2.9L1.8 17a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <h4 class="font-bold text-yellow-800 dark:text-yellow-400">
                        Dokumen Dapodik Belum Dikirim
                    </h4>

                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                        Silakan kirim link Google Drive dokumen untuk proses verifikasi admin.
                    </p>

                    <a href="{{ route('teacher.documents.create') }}"
                        class="mt-3 inline-flex items-center gap-2 rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-yellow-600">
                        Lengkapi Sekarang →
                    </a>
                </div>

            </div>
        </div>


    @elseif($user->teacherDocument->status === 'REJECTED')

        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-900/40 dark:bg-red-900/20">
            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400">
                    <svg width="26" height="26" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>

                <div class="flex-1">

                    <h4 class="font-bold text-red-800 dark:text-red-400">
                        Dokumen Dapodik Ditolak
                    </h4>

                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                        {{ $user->teacherDocument->note ?? 'Silakan perbaiki dokumen.' }}
                    </p>

                    <a href="{{ route('teacher.documents.edit') }}"
                        class="mt-3 inline-flex items-center gap-2 rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-600">
                        Perbaiki Dokumen →
                    </a>

                </div>

            </div>
        </div>


    @elseif($user->teacherDocument->status === 'PENDING')

        <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900/40 dark:bg-blue-900/20">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">

                    <svg width="26" height="26" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">

                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>

                    </svg>

                </div>


                <div class="flex-1">

                    <h4 class="font-bold text-blue-800 dark:text-blue-400">
                        Dokumen Sedang Diverifikasi
                    </h4>

                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                        Admin sedang melakukan pemeriksaan dokumen Dapodik Anda.
                    </p>


                    <a href="{{ route('teacher.documents.index') }}"
                        class="mt-3 inline-flex items-center gap-2 rounded-xl bg-blue-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-600">

                        Lihat Dokumen →

                    </a>

                </div>

            </div>

        </div>


    @elseif($user->teacherDocument->status === 'VERIFIED')

        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-5 dark:border-green-900/40 dark:bg-green-900/20">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400">

                    <svg width="26" height="26" viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <polyline points="20 6 9 17 4 12"/>

                    </svg>

                </div>


                <div class="flex-1">

                    <h4 class="font-bold text-green-800 dark:text-green-400">
                        Dokumen Dapodik Terverifikasi
                    </h4>

                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                        Dokumen Anda telah diverifikasi oleh admin.
                    </p>


                    <a href="{{ route('teacher.documents.index') }}"
                        class="mt-3 inline-flex items-center gap-2 rounded-xl bg-green-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-600">

                        Lihat Dokumen →

                    </a>

                </div>

            </div>

        </div>

    @endif

@endif


<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">

    @foreach($menuItems as $item)

        <a href="{{ $item['url'] }}"
            class="group flex flex-col rounded-2xl border border-gray-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">


            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600">


                @switch($item['icon'])

                    @case('clipboard-plus')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="6" y="4" width="12" height="16" rx="2"/>
                            <path d="M12 10v5"/>
                            <path d="M9.5 12.5h5"/>
                        </svg>

                    @break


                    @case('file-chart')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M8 17l2-2 2 2 4-5"/>
                        </svg>

                    @break


                    @case('calendar-check')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M9 15l2 2 4-4"/>
                        </svg>

                    @break


                    @case('calendar')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M8 3v4"/>
                            <path d="M16 3v4"/>
                        </svg>

                    @break


                    @case('megaphone')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M9 7L18 3v18l-9-4"/>
                            <path d="M20 9a3 3 0 010 6"/>
                        </svg>

                    @break


                    @case('users-group')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        </svg>

                    @break


                    @case('document')

                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="8" y1="13" x2="16" y2="13"/>
                        </svg>

                    @break

                @endswitch

            </div>


            <h4 class="mt-4 text-xl font-bold text-gray-800 dark:text-white">
                {{ $item['title'] }}
            </h4>


            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $item['description'] }}
            </p>


            <span class="mt-4 text-sm font-medium text-brand-600">
                Buka →
            </span>


        </a>

    @endforeach

</div>

@endsection