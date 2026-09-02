@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rencana Pembelajaran" />

    <div class="mx-auto max-w-6xl">

        @if (session('success'))
            <div class="mb-6">
                <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
            </div>
        @endif

        {{-- Header: judul + tombol tambah --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $lessonPlans->total() }} rencana pembelajaran
            </p>

            <a href="{{ route('lesson-plans.create') }}"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>
                </svg>
                Tambah Rencana
            </a>
        </div>

        {{-- Filter Tahun Ajaran --}}
        <form method="GET" action="{{ route('lesson-plans.index') }}" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="w-64">
                <label for="academic_year_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Tahun Ajaran
                </label>
                <select id="academic_year_id" name="academic_year_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}" @selected($selectedAcademicYearId == $year->id)>
                            {{ $year->name }} ({{ $year->semester }})
                            @if ($year->is_active) — Aktif @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium text-white transition">
                Filter
            </button>
        </form>

        @if ($lessonPlans->isEmpty())

            {{-- Empty state --}}
            <x-common.component-card>
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-900/30 dark:text-brand-400">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="8" y1="13" x2="16" y2="13"/>
                            <line x1="8" y1="17" x2="16" y2="17"/>
                        </svg>
                    </div>

                    <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white">
                        Belum Ada Rencana Pembelajaran
                    </h3>

                    <p class="mb-6 text-gray-500 dark:text-gray-400">
                        Klik tombol "Tambah Rencana" di atas untuk membuat rencana pembelajaran pertama Anda.
                    </p>

                    <a href="{{ route('lesson-plans.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-600">
                        Tambah Rencana Pembelajaran
                    </a>
                </div>
            </x-common.component-card>

        @else

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

                @foreach ($lessonPlans as $lessonPlan)

                    <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900 dark:hover:border-brand-500">

                        <span class="absolute left-0 top-0 h-full w-1 bg-brand-500 transition-all group-hover:w-2"></span>

                        <div class="mb-5 flex items-start justify-between">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:scale-110 dark:bg-brand-900/30 dark:text-brand-400">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="8" y1="13" x2="16" y2="13"/>
                                    <line x1="8" y1="17" x2="16" y2="17"/>
                                </svg>
                            </div>

                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                                Kelas {{ $lessonPlan->xclass?->name ?? 'Umum' }}
                            </span>
                        </div>

                        <h4 class="mb-1 text-lg font-bold text-gray-800 transition group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400">
                            {{ $lessonPlan->title }}
                        </h4>

                        <p class="mb-3 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ $lessonPlan->description }}
                        </p>

                        <div class="mb-5 flex flex-col gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path d="M8 3v4"/>
                                    <path d="M16 3v4"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                {{ $lessonPlan->date->translatedFormat('d F Y') }}
                            </span>

                            <span class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                {{ $lessonPlan->schedule?->subject_name ?? 'Umum' }}
                            </span>
                        </div>

                        <div class="mt-auto flex gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <a href="{{ route('lesson-plans.edit', $lessonPlan) }}"
                                class="flex flex-1 items-center justify-center rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 hover:bg-brand-600 hover:shadow-md">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('lesson-plans.destroy', $lessonPlan) }}"
                                onsubmit="return confirm('Yakin ingin menghapus rencana pembelajaran {{ $lessonPlan->title }}?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>

                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $lessonPlans->links() }}
            </div>

        @endif

    </div>
@endsection
