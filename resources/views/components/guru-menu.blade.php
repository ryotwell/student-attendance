@php

$user = Auth::user();

$user->loadExists('classes');

$menuItems = [
    [
        'title'       => 'Input Absensi',
        'description' => 'Input dan kelola data kehadiran siswa',
        'url'         => route('absensi.schedules'),
        'icon'        => 'clipboard-plus',
    ],
    [
        'title'       => 'Rekap Absensi',
        'description' => 'Lihat dan cetak rekap kehadiran siswa',
        'url'         => route('absensi.recap'),
        'icon'        => 'file-chart',
    ],
    [
        'title'       => 'Riwayat Absensi',
        'description' => 'Lihat riwayat absensi berdasarkan jadwal & tanggal',
        'url'         => route('absensi.history'),
        'icon'        => 'calendar-check',
    ],
    [
        'title'       => 'Jadwal Mengajar',
        'description' => 'Lihat jadwal kelas dan jam mengajar',
        'url'         => route('schedules.index'),
        'icon'        => 'calendar',
    ],
    [
        'title'       => 'Pengumuman',
        'description' => 'Baca informasi dan pengumuman terbaru',
        'url'         => route('announcements.index'),
        'icon'        => 'megaphone',
    ],
];

if ($user->role === 'GURU' && $user->isWaliKelas()) {
    $menuItems[] = [
        'title'       => 'Menu Wali Kelas',
        'description' => 'Kelola dan pantau kelas yang Anda wali-i',
        'url'         => route('walikelas.index'),
        'icon'        => 'users-group',
    ];
}
@endphp


<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">

    @foreach ($menuItems as $item)

        <a
            href="{{ $item['url'] }}"
            class="group flex flex-col rounded-2xl border border-gray-200 bg-white p-5 md:p-6 transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-brand-500"
        >

            {{-- ICON --}}
            <div class="flex items-center justify-center w-14 h-14 bg-brand-50 rounded-xl text-brand-600 group-hover:bg-brand-100">

                @switch($item['icon'])

                    {{-- INPUT ABSENSI --}}
                    @case('clipboard-plus')
                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <rect x="6" y="4" width="12" height="16" rx="2"></rect>
                            <path d="M9 4V3.5C9 3 9.5 2.5 10 2.5h4c.5 0 1 .5 1 1V4"></path>
                            <path d="M12 10v5"></path>
                            <path d="M9.5 12.5h5"></path>

                        </svg>
                    @break


                    {{-- REKAP ABSENSI --}}
                    @case('file-chart')
                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M8 17l2-2 2 2 4-5"></path>

                        </svg>
                    @break


                    {{-- RIWAYAT ABSENSI --}}
                    @case('calendar-check')
                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M8 3v4"></path>
                            <path d="M16 3v4"></path>
                            <path d="M3 10h18"></path>
                            <path d="M9 15l2 2 4-4"></path>

                        </svg>
                    @break


                    {{-- JADWAL --}}
                    @case('calendar')
                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M3 10h18"></path>
                            <path d="M8 3v4"></path>
                            <path d="M16 3v4"></path>

                        </svg>
                    @break


                    {{-- PENGUMUMAN --}}
                    @case('megaphone')
                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <path d="M9 7L18 3v18l-9-4"></path>
                            <path d="M9 7H5.5a2.5 2.5 0 000 5H9v5H7l-1.5-3"></path>
                            <path d="M20 9a3 3 0 010 6"></path>

                        </svg>
                    @break


                    {{-- MENU WALI KELAS --}}
                    @case('users-group')
                        <svg width="28" height="28" viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>

                        </svg>
                    @break

                @endswitch

            </div>


            {{-- TITLE --}}
            <h4 class="mt-4 font-bold text-gray-800 text-xl">
                {{ $item['title'] }}
            </h4>


            {{-- DESCRIPTION --}}
            <p class="mt-1 text-sm text-gray-500">
                {{ $item['description'] }}
            </p>


            {{-- BUTTON --}}
            <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-brand-600">

                Buka

                <svg width="16" height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="transition-transform group-hover:translate-x-1">

                    <path d="M5 12h14"></path>
                    <path d="M13 6l6 6-6 6"></path>

                </svg>

            </span>

        </a>

    @endforeach

</div>