@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Riwayat Kasus Siswa" />

    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $student->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    NIS {{ $student->nis }} &middot; Kelas {{ $student->currentEnrollment?->xclass?->name ?? '-' }}
                </p>
            </div>

            <a href="{{ route('bk.cases.create', ['student_id' => $student->id]) }}"
                class="flex w-fit items-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                + Tambah Kasus untuk Siswa Ini
            </a>
        </div>

        <div class="space-y-4">
            @forelse ($cases as $case)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="rounded-lg px-2 py-1 text-xs font-bold {{ $case->category_badge_class }}">
                            {{ $case->category_label }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $case->date->format('d F Y') }}</span>
                    </div>

                    <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">{{ $case->description }}</p>

                    @if ($case->action_taken)
                        <div class="mt-3 rounded-xl bg-gray-50 p-3 dark:bg-gray-800">
                            <p class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Tindak Lanjut:</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $case->action_taken }}</p>
                        </div>
                    @endif

                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                        <span class="text-xs text-gray-400 dark:text-gray-500">Dicatat oleh {{ $case->user->name }}</span>
                        <x-ui.button-link href="{{ route('bk.cases.edit', $case) }}"
                            class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Edit</x-ui.button-link>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-12 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada catatan kasus untuk siswa ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection