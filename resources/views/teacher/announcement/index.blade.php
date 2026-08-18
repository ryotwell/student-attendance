@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Daftar Pengumuman" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <div class="max-w-6xl mx-auto">
        {{-- <x-common.component-card title="📢 Daftar Pengumuman"> --}}
            @if ($announcements->isEmpty())
                {{-- EMPTY STATE --}}
                <div class="text-center py-12">
                    <div class="mx-auto w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-400">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
                        Belum Ada Pengumuman
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        Pengumuman dari sekolah akan muncul di sini.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($announcements as $announcement)
                        <a href="{{ route('announcements.show', $announcement) }}"
                            class="group block rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl">
                            {{-- ICON HEADER --}}
                            <div class="flex items-start justify-between mb-5">
                                <div class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    </svg>
                                </div>

                                @if ($announcement->status === 'PUBLISHED')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400">
                                        ✓ Published
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400">
                                        📝 Draft
                                    </span>
                                @endif
                            </div>

                            {{-- TITLE --}}
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-3 group-hover:text-brand-500 transition">
                                {{ $announcement->title }}
                            </h3>

                            {{-- DATE --}}
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                {{ $announcement->created_at->format('d F Y') }}
                            </div>

                            {{-- ACTION AREA --}}
                            <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400 dark:text-gray-500 group-hover:text-brand-500 transition">
                                        Baca Selengkapnya
                                    </span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400 group-hover:text-brand-500 transition">
                                        <path d="M5 12h14" />
                                        <path d="M12 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        {{-- </x-common.component-card> --}}
    </div>
@endsection
