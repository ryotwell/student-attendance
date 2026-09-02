@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Wali Kelas" />

    <div class="mx-auto max-w-6xl">
        <h2 class="mb-6 text-xl font-bold text-gray-800 dark:text-white">Pilih Kelas Perwalian</h2>

        @if($xclasses->isEmpty())
            <x-common.component-card>
                <div class="text-center py-12">
                    <div class="mx-auto w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Belum Ada Kelas Perwalian</h3>
                    <p class="text-gray-500 dark:text-gray-400">Anda belum ditetapkan sebagai wali kelas manapun.</p>
                </div>
            </x-common.component-card>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($xclasses as $xclass)
                    <a
                        href="{{ route('walikelas.dashboard', $xclass) }}"
                        class="group block rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 transition-all duration-200 hover:-translate-y-1 hover:border-brand-400 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                        {{-- ICON KELAS --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center justify-center w-14 h-14 bg-brand-100 dark:bg-brand-900/30 rounded-xl text-brand-600 dark:text-brand-400 group-hover:bg-brand-200 dark:group-hover:bg-brand-900/50 transition-colors">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold text-brand-700 dark:text-brand-300 bg-brand-100 dark:bg-brand-900/30 rounded-full">
                                Wali Kelas
                            </span>
                        </div>

                        {{-- NAMA KELAS --}}
                        <h4 class="font-bold text-lg text-gray-800 dark:text-white mb-3 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                            Kelas {{ $xclass->name }}
                        </h4>

                        {{-- JUMLAH SISWA --}}
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>{{ $xclass->students_count }} Siswa</span>
                        </div>

                        {{-- ACTION BUTTON --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 font-semibold text-sm group-hover:bg-brand-100 dark:group-hover:bg-brand-900/50 transition-colors">
                                Buka Kelas
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"></path>
                                    <path d="M13 6l6 6-6 6"></path>
                                </svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection