@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Detail Assignment" />

<div class="mx-auto max-w-4xl space-y-6">

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs
                dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $assignment->title }}
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Assignment #{{ $assignment->id }}
                </p>
            </div>

            <span class="w-fit rounded-full px-3 py-1 text-xs font-medium
                @if($assignment->status === 'PUBLISHED')
                    bg-success-50 text-success-600
                @elseif($assignment->status === 'CLOSED')
                    bg-gray-100 text-gray-600
                @else
                    bg-warning-50 text-warning-600
                @endif
            ">
                {{ $assignment->status }}
            </span>

        </div>

        {{-- Description --}}
        <div class="mt-6">

            <h3 class="mb-2 text-sm font-semibold text-gray-800 dark:text-white/90">
                Deskripsi
            </h3>

            <div class="rounded-xl bg-gray-50 p-4 text-sm leading-6 text-gray-600
                        dark:bg-gray-800/50 dark:text-gray-300">
                {!! nl2br(e($assignment->description ?? 'Tidak ada deskripsi.')) !!}
            </div>

        </div>

        {{-- Information --}}
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Guru
                </p>

                <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">
                    {{ $assignment->user->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Kelas
                </p>

                <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">
                    {{ $assignment->xclass->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Tahun Akademik
                </p>

                <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">
                    {{ $assignment->academicYear->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Deadline
                </p>

                <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">
                    {{ $assignment->due_date
                        ? $assignment->due_date->format('d M Y H:i')
                        : '-'
                    }}
                </p>
            </div>

        </div>

        {{-- Attachment --}}
        @if($assignment->attachment)

            <div class="mt-6 border-t border-gray-100 pt-6 dark:border-gray-800">

                <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">
                    Lampiran
                </h3>

                <a
                    href="{{ Storage::disk('s3')->url($assignment->attachment) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-gray-200 px-4 py-2.5 text-sm font-medium
                           text-gray-700 hover:bg-gray-50
                           dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Lihat / Download Lampiran
                </a>

            </div>

        @endif

        {{-- Back --}}
        <div class="mt-6 border-t border-gray-100 pt-6 dark:border-gray-800">

            <a
                href="{{ route('admin.assignments.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-200
                       px-4 py-2.5 text-sm font-medium text-gray-700
                       hover:bg-gray-50 dark:border-gray-700
                       dark:text-gray-300 dark:hover:bg-gray-800"
            >
                Kembali
            </a>

        </div>

    </div>

</div>

@endsection