@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Dokumen Dapodik" />

    <div class="mx-auto max-w-3xl">

        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Tambah Dokumen Dapodik
            </h2>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Masukkan link Google Drive yang berisi dokumen pendukung untuk verifikasi admin.
            </p>
        </div>


        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">

            <form action="{{ route('teacher.documents.store') }}" method="POST">
                @csrf


                <div class="mb-5">

                    <label for="file_url"
                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">

                        Link Google Drive

                    </label>


                    <input type="url"
                        name="file_url"
                        id="file_url"
                        value="{{ old('file_url') }}"
                        placeholder="https://drive.google.com/..."

                        class="dark:bg-dark-900 h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90"

                        required>


                    @error('file_url')

                        <p class="mt-2 text-sm text-red-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div class="mb-6 rounded-xl bg-blue-50 p-4 dark:bg-blue-900/20">

                    <p class="mb-2 text-sm font-semibold text-blue-700 dark:text-blue-400">
                        Contoh dokumen yang dapat dimasukkan:
                    </p>


                    <ul class="list-inside list-disc text-sm text-blue-600 dark:text-blue-300">

                        <li>KTP</li>
                        <li>Ijazah</li>
                        <li>SK Pengangkatan</li>
                        <li>Sertifikat Pendidik</li>
                        <li>Dokumen pendukung Dapodik lainnya</li>

                    </ul>

                </div>


                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a href="{{ route('teacher.documents.index') }}"
                        class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">

                        Batal

                    </a>


                    <button type="submit"
                        class="rounded-xl bg-brand-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">

                        Kirim Dokumen

                    </button>

                </div>


            </form>

        </div>

    </div>
@endsection