@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Detail Dokumen Guru" />

<div class="mx-auto max-w-4xl">

    @if(session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Detail Dokumen Guru
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Verifikasi dokumen pendukung Dapodik.
            </p>
        </div>

        <a href="{{ route('admin.teacher-documents.index') }}"
            class="w-fit rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            ← Kembali
        </a>
    </div>


    <div class="space-y-6">

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">

            <h3 class="mb-5 font-bold text-gray-800 dark:text-white">
                Informasi Guru
            </h3>

            <div class="grid gap-5 sm:grid-cols-2">

                <div>
                    <p class="text-xs text-gray-500">Nama</p>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        {{ $teacherDocument->user->name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        {{ $teacherDocument->user->email }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Tanggal Upload</p>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        {{ $teacherDocument->created_at->format('d M Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Status</p>

                    @if($teacherDocument->status === 'VERIFIED')
                        <span class="inline-flex rounded-lg bg-green-100 px-3 py-1 text-xs font-bold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                            VERIFIED
                        </span>
                    @elseif($teacherDocument->status === 'REJECTED')
                        <span class="inline-flex rounded-lg bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            REJECTED
                        </span>
                    @else
                        <span class="inline-flex rounded-lg bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                            PENDING
                        </span>
                    @endif

                </div>

            </div>

        </div>


        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">

            <h3 class="mb-4 font-bold text-gray-800 dark:text-white">
                Dokumen Google Drive
            </h3>

            <a href="{{ $teacherDocument->file_url }}"
                target="_blank"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-100 dark:bg-brand-900/20 dark:text-brand-400">

                <svg width="20" height="20" viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>

                Buka Dokumen

            </a>

        </div>


        @if($teacherDocument->note)

            <div class="rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-900/40 dark:bg-red-900/20">

                <h3 class="mb-2 font-bold text-red-700 dark:text-red-400">
                    Catatan Admin
                </h3>

                <p class="text-sm text-red-600 dark:text-red-300">
                    {{ $teacherDocument->note }}
                </p>

            </div>

        @endif


        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">

            <h3 class="mb-5 font-bold text-gray-800 dark:text-white">
                Verifikasi Dokumen
            </h3>

            @if($teacherDocument->status !== 'VERIFIED')

                <div class="grid gap-4 md:grid-cols-2">

                    <form action="{{ route('admin.teacher-documents.verify', $teacherDocument) }}"
                        method="POST">

                        @csrf

                        <button type="submit"
                            onclick="return confirm('Verifikasi dokumen ini?')"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-600">

                            <svg width="18" height="18" viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>

                            Verifikasi Dokumen

                        </button>

                    </form>


                    <form action="{{ route('admin.teacher-documents.reject', $teacherDocument) }}"
                        method="POST">

                        @csrf

                        <textarea name="note"
                            rows="3"
                            required
                            placeholder="Alasan penolakan dokumen..."
                            class="mb-3 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('note') }}</textarea>

                        <button type="submit"
                            onclick="return confirm('Tolak dokumen ini?')"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-600">

                            Tolak Dokumen

                        </button>

                    </form>

                </div>

            @else

                <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                    Dokumen sudah diverifikasi.
                    @if($teacherDocument->verified_at)
                        <br>
                        Verifikasi:
                        {{ $teacherDocument->verified_at->format('d M Y H:i') }}
                    @endif
                </div>

            @endif

        </div>

    </div>

</div>
@endsection