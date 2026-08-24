@extends('layouts.app')

@section('content')
    {{-- <x-common.page-breadcrumb pageTitle="Dokumen Dapodik Guru" /> --}}

    <div class="mx-auto max-w-4xl">

        @if(session('success'))
            <div class="mb-6">
                <x-ui.alert
                    variant="success"
                    title="Berhasil"
                    :message="session('success')" />
            </div>
        @endif

        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    Dokumen Dapodik Guru
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kirim link Google Drive dokumen untuk proses verifikasi admin.
                </p>
            </div>

            @if(!$document)
                <a href="{{ route('teacher.documents.create') }}"
                    class="w-fit rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                    + Tambah Dokumen
                </a>
            @endif
        </div>


        @if($document)

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">

                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            Link Dokumen Google Drive
                        </h3>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Dikirim {{ $document->created_at->format('d M Y') }}
                        </p>
                    </div>


                    @switch($document->status)

                        @case('VERIFIED')
                            <span class="w-fit rounded-lg bg-green-100 px-3 py-1 text-xs font-bold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                Terverifikasi
                            </span>
                        @break

                        @case('REJECTED')
                            <span class="w-fit rounded-lg bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                Ditolak
                            </span>
                        @break

                        @default
                            <span class="w-fit rounded-lg bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                Menunggu Verifikasi
                            </span>

                    @endswitch

                </div>


                <div class="mb-5">

                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        URL Google Drive
                    </label>

                    <a href="{{ $document->file_url }}"
                        target="_blank"
                        class="block break-all rounded-xl bg-gray-50 p-4 text-sm text-brand-600 hover:underline dark:bg-gray-800 dark:text-brand-400">

                        {{ $document->file_url }}

                    </a>

                </div>


                @if($document->note)

                    <div class="mb-5 rounded-xl bg-red-50 p-4 dark:bg-red-900/20">

                        <p class="mb-1 text-sm font-semibold text-red-700 dark:text-red-400">
                            Catatan Admin
                        </p>

                        <p class="text-sm text-red-600 dark:text-red-300">
                            {{ $document->note }}
                        </p>

                    </div>

                @endif


                @if($document->status !== 'VERIFIED')

                    <div class="flex justify-end">

                        <a href="{{ route('teacher.documents.edit') }}"
                            class="rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-600">

                            Edit Dokumen

                        </a>

                    </div>

                @endif

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">

                <h3 class="mb-2 font-semibold text-gray-800 dark:text-white">
                    Belum Ada Dokumen
                </h3>

                <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">
                    Silakan kirim link Google Drive yang berisi dokumen Dapodik Anda.
                </p>

                <a href="{{ route('teacher.documents.create') }}"
                    class="inline-flex rounded-xl bg-brand-500 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-600">

                    Tambah Dokumen

                </a>

            </div>

        @endif

    </div>
@endsection