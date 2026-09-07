@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Assignment" />

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Assignment
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Daftar assignment yang dibuat oleh guru di sekolah.
            </p>
        </div>
    </div>

    {{-- Assignment Grid --}}
    @if($assignments->count())

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

            @foreach($assignments as $assignment)

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs
                            dark:border-gray-800 dark:bg-white/[0.03]">

                    {{-- Status --}}
                    <div class="mb-4 flex items-center justify-between">

                        <span class="rounded-full px-3 py-1 text-xs font-medium
                            @if($assignment->status === 'PUBLISHED')
                                bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400
                            @elseif($assignment->status === 'CLOSED')
                                bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                            @else
                                bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400
                            @endif
                        ">
                            {{ $assignment->status }}
                        </span>

                        <span class="text-xs text-gray-400">
                            #{{ $assignment->id }}
                        </span>

                    </div>

                    {{-- Title --}}
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ $assignment->title }}
                    </h3>

                    {{-- Description --}}
                    @if($assignment->description)
                        <p class="mt-2 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ $assignment->description }}
                        </p>
                    @endif

                    {{-- Information --}}
                    <div class="mt-5 space-y-3">

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Guru
                            </span>

                            <span class="font-medium text-gray-800 dark:text-white/90">
                                {{ $assignment->user->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Kelas
                            </span>

                            <span class="font-medium text-gray-800 dark:text-white/90">
                                {{ $assignment->xclass->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Tahun Akademik
                            </span>

                            <span class="font-medium text-gray-800 dark:text-white/90">
                                {{ $assignment->academicYear->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Deadline
                            </span>

                            <span class="font-medium text-gray-800 dark:text-white/90">
                                {{ $assignment->due_date
                                    ? $assignment->due_date->format('d M Y H:i')
                                    : '-'
                                }}
                            </span>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">

                        <a
                            href="{{ route('admin.assignments.show', $assignment) }}"
                            class="inline-flex w-full items-center justify-center rounded-lg
                                   bg-brand-500 px-4 py-2.5 text-sm font-medium text-white
                                   transition hover:bg-brand-600"
                        >
                            Lihat Detail
                        </a>

                    </div>

                </div>

            @endforeach

        </div>

        {{-- Pagination --}}
        <div>
            {{ $assignments->links() }}
        </div>

    @else

        {{-- Empty State --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center
                    shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full
                        bg-gray-100 dark:bg-gray-800">

                <svg
                    class="h-7 w-7 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"
                    />
                </svg>

            </div>

            <h3 class="mt-4 text-base font-semibold text-gray-800 dark:text-white/90">
                Belum Ada Assignment
            </h3>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Belum ada assignment yang dibuat oleh guru.
            </p>

        </div>

    @endif

</div>

@endsection