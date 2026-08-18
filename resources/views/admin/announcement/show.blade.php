@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Pengumuman" />

    <div class="mx-auto max-w-3xl space-y-6">
        <x-back-to link="{{ route('announcements.index') }}">
            Kembali ke Pengumuman
        </x-back-to>

        <x-common.component-card title="Detail Pengumuman">
            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    Pengumuman Sekolah
                </span>

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
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
                {{ $announcement->title }}
            </h1>

            {{-- DATE --}}
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                {{ $announcement->created_at->format('d F Y') }}
            </div>

            {{-- CONTENT --}}
            <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                <div class="whitespace-pre-line text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $announcement->content }}
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="pt-6 border-t border-gray-100 dark:border-gray-800 flex items-center gap-4">
                <a href="{{ route('announcements.edit', $announcement) }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}"
                    onsubmit="return confirm('Yakin ingin menghapus pengumuman {{ $announcement->title }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-error-500 ring-1 ring-error-500/30 hover:bg-error-50 dark:hover:bg-error-500/10 transition">
                        Hapus
                    </button>
                </form>
            </div>
        </x-common.component-card>
    </div>
@endsection