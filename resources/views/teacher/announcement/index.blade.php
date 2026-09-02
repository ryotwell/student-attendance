@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Daftar Pengumuman" />

@if (session('success'))

    <div class="mb-6">
        <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
    </div>

@endif

<div class="mx-auto max-w-6xl">

    @if ($announcements->isEmpty())

        <div class="py-12 text-center">

            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-900/30 dark:text-brand-400">

                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>

            </div>

            <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white">
                Belum Ada Pengumuman
            </h3>

            <p class="text-gray-500 dark:text-gray-400">
                Pengumuman dari sekolah akan muncul di sini.
            </p>

        </div>

    @else

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

            @foreach ($announcements as $announcement)

                <a href="{{ route('announcements.show', $announcement) }}"
                    class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900">

                    <span class="absolute left-0 top-0 h-full w-1 bg-brand-500 transition-all group-hover:w-2"></span>


                    <div class="mb-5 flex items-start justify-between">

                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:scale-110 dark:bg-brand-900/30 dark:text-brand-400">

                            <svg
                                width="30"
                                height="30"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>

                        </div>


                        @if ($announcement->status === 'PUBLISHED')

                            <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-400">
                                Published
                            </span>

                        @else

                            <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400">
                                Draft
                            </span>

                        @endif

                    </div>


                    <h3 class="mb-3 text-lg font-bold text-gray-800 transition group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400">

                        {{ $announcement->title }}

                    </h3>


                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>

                        {{ $announcement->created_at->format('d F Y') }}

                    </div>


                    <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">

                        <div class="flex items-center justify-between">

                            <span class="text-xs text-gray-400 transition group-hover:text-brand-500 dark:text-gray-500">
                                Baca Selengkapnya
                            </span>


                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="text-gray-400 transition group-hover:text-brand-500"
                            >
                                <path d="M5 12h14"/>
                                <path d="M12 5l7 7-7 7"/>
                            </svg>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

    @endif

</div>

@endsection