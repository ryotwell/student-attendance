@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Import Siswa" />

    {{-- =========================================================
        FLASH SUCCESS
    ========================================================== --}}
    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert
                variant="success"
                title="Berhasil"
                :message="session('success')"
            />
        </div>
    @endif


    {{-- =========================================================
        HASIL IMPORT
    ========================================================== --}}
    @if (session('import_errors'))

        <div class="mb-6">

            <div class="rounded-lg border border-error-200 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/10">

                <div class="flex gap-3">

                    {{-- Icon --}}
                    <div class="shrink-0">

                        <svg
                            class="h-5 w-5 text-error-600 dark:text-error-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20h15.6a2 2 0 001.73-2.64l-7.82-13.5a2 2 0 00-3.46 0z"
                            />
                        </svg>

                    </div>


                    {{-- Content --}}
                    <div class="flex-1 min-w-0">

                        <p class="text-sm font-medium text-error-800 dark:text-error-300">
                            Beberapa data gagal diimport
                        </p>


                        {{-- Summary --}}
                        <p class="mt-1 text-sm text-error-700 dark:text-error-400">

                            @if (session('import_success_count') > 0)

                                <strong>
                                    {{ session('import_success_count') }}
                                </strong>
                                siswa berhasil diimport.

                            @endif

                            <strong>
                                {{ session('import_error_count') }}
                            </strong>
                            siswa gagal diimport.

                        </p>


                        {{-- Error List --}}
                        <div class="mt-4 space-y-2">

                            @foreach (session('import_errors') as $error)

                                <div class="rounded-lg border border-error-200 bg-white p-3 dark:border-error-500/20 dark:bg-white/[0.03]">

                                    <p class="text-sm font-semibold text-error-700 dark:text-error-400">
                                        Baris {{ $error['row'] }}
                                    </p>

                                    <p class="mt-0.5 text-sm text-error-600 dark:text-error-400">
                                        {{ $error['message'] }}
                                    </p>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        VALIDATION ERROR
    ========================================================== --}}
    @if ($errors->any())

        <div class="mb-6">

            <div class="rounded-lg border border-error-200 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/10">

                <div class="flex gap-3">

                    <div class="shrink-0">

                        <svg
                            class="h-5 w-5 text-error-600 dark:text-error-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20h15.6a2 2 0 001.73-2.64l-7.82-13.5a2 2 0 00-3.46 0z"
                            />
                        </svg>

                    </div>

                    <div class="flex-1">

                        <p class="text-sm font-medium text-error-800 dark:text-error-300">
                            Import Gagal
                        </p>

                        <ul class="mt-2 space-y-1">

                            @foreach ($errors->all() as $error)

                                <li class="text-sm text-error-600 dark:text-error-400">
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        FORM IMPORT
    ========================================================== --}}
    <x-common.component-card title="Import Siswa dari Excel">

        <form
            action="{{ route('students.import') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="space-y-6">


                {{-- =================================================
                    INFORMASI
                ================================================== --}}
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/10">

                    <div class="flex gap-3">

                        <div class="mt-0.5">

                            <svg
                                class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
                                />
                            </svg>

                        </div>

                        <div>

                            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                Format Import
                            </p>

                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">
                                File Excel harus memiliki delapan kolom:
                                <strong>Nama_Siswa</strong>,
                                <strong>NIS</strong>,
                                <strong>NISN</strong>,
                                <strong>Jenis_Kelamin</strong>,
                                <strong>Status</strong>,
                                <strong>Nama_Orang_Tua</strong>,
                                <strong>No_HP_Orang_Tua</strong>,
                                dan
                                <strong>Kode_Kelas</strong>.
                            </p>

                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">
                                <strong>Kode_Kelas</strong> harus sesuai dengan
                                kelas yang telah terdaftar pada tahun ajaran yang dipilih.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    TAHUN AJARAN
                ================================================== --}}
                <div>

                    <label
                        for="academic_year_id"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        Tahun Ajaran
                        <span class="text-error-500">*</span>
                    </label>

                    <select
                        id="academic_year_id"
                        name="academic_year_id"
                        required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >

                        <option value="">
                            Pilih Tahun Ajaran
                        </option>

                        @foreach ($academicYears as $year)

                            <option
                                value="{{ $year->id }}"
                                @selected(old('academic_year_id') == $year->id)
                            >
                                {{ $year->name }}
                                ({{ $year->semester }})
                                {{ $year->is_active ? '- Aktif' : '' }}
                            </option>

                        @endforeach

                    </select>

                    @error('academic_year_id')

                        <p class="mt-1.5 text-sm text-error-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- =================================================
                    UPLOAD FILE
                ================================================== --}}
                <div>

                    <label
                        for="file"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                    >
                        File Excel
                        <span class="text-error-500">*</span>
                    </label>

                    <input
                        type="file"
                        id="file"
                        name="file"
                        required
                        accept=".xlsx,.xls,.csv"
                        class="block h-11 w-full cursor-pointer rounded-lg border border-gray-300 bg-transparent text-sm text-gray-700 file:mr-4 file:h-11 file:border-0 file:bg-gray-100 file:px-4 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-gray-800 dark:file:text-gray-300"
                    />

                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        Format yang diperbolehkan:
                        XLSX, XLS, atau CSV. Maksimal 5 MB.
                    </p>

                    @error('file')

                        <p class="mt-1.5 text-sm text-error-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- =================================================
                    FORMAT KOLOM
                ================================================== --}}
                <div>

                    <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                        Format Kolom Excel
                    </p>

                    <div class="max-w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">

                        <table class="w-full min-w-[900px]">

                            <thead>

                                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        No
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Nama Kolom
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Wajib
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Keterangan
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                {{-- Nama --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        1
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        Nama_Siswa
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">
                                            Wajib
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Nama lengkap siswa.
                                    </td>

                                </tr>


                                {{-- NIS --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        2
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        NIS
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">
                                            Wajib
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Nomor Induk Siswa. Harus unik dalam sekolah.
                                    </td>

                                </tr>


                                {{-- NISN --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        3
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        NISN
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">
                                            Wajib
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Nomor Induk Siswa Nasional. Harus unik dalam sekolah.
                                    </td>

                                </tr>


                                {{-- Jenis Kelamin --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        4
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        Jenis_Kelamin
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">
                                            Wajib
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Gunakan L untuk laki-laki atau P untuk perempuan.
                                    </td>

                                </tr>


                                {{-- Status --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        5
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        Status
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
                                            Opsional
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        AKTIF, LULUS, PINDAH, atau KELUAR. (Default ACTIVE)
                                    </td>

                                </tr>


                                {{-- Nama Orang Tua --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        6
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        Nama_Orang_Tua
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
                                            Opsional
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Nama orang tua atau wali siswa.
                                    </td>

                                </tr>


                                {{-- No HP --}}
                                <tr class="border-b border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        7
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        No_HP_Orang_Tua
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
                                            Opsional
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Nomor HP orang tua atau wali siswa.
                                    </td>

                                </tr>


                                {{-- Kode Kelas --}}
                                <tr>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        8
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                        Kode_Kelas
                                    </td>

                                    <td class="px-4 py-3">

                                        <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">
                                            Wajib
                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Kode kelas sesuai dengan tahun ajaran yang dipilih.
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =================================================
                    CONTOH DATA
                ================================================== --}}
                <div>

                    <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                        Contoh Data
                    </p>

                    <div class="max-w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">

                        <table class="w-full min-w-[1000px]">

                            <thead>

                                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Nama_Siswa
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        NIS
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        NISN
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Jenis_Kelamin
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Status
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Nama_Orang_Tua
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        No_HP_Orang_Tua
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Kode_Kelas
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        Ahmad Fauzan
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        001
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        1234567890
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        L
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        AKTIF
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        Budi
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        08123456789
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        VIIA
                                    </td>

                                </tr>

                                <tr class="border-t border-gray-100 dark:border-gray-800">

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        Siti Aminah
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        002
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        1234567891
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        P
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        AKTIF
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        Ahmad
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        08123456788
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        VIIIA
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =================================================
                    PERHATIAN
                ================================================== --}}
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-500/30 dark:bg-yellow-500/10">

                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                        Perhatian
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-yellow-700 dark:text-yellow-400">

                        <li>
                            Nama kolom Excel harus sesuai dengan format yang telah ditentukan.
                        </li>

                        <li>
                            NIS dan NISN harus unik dalam satu sekolah.
                        </li>

                        <li>
                            Jenis kelamin menggunakan <strong>L</strong> untuk laki-laki
                            dan <strong>P</strong> untuk perempuan.
                        </li>

                        <li>
                            Status siswa dapat menggunakan
                            <strong>AKTIF</strong>,
                            <strong>LULUS</strong>,
                            <strong>PINDAH</strong>, atau
                            <strong>KELUAR</strong>.
                        </li>

                        <li>
                            Kode kelas harus sudah terdaftar pada tahun ajaran yang dipilih.
                        </li>

                        <li>
                            Pastikan data NIS dan NISN tidak sama dengan data siswa
                            yang sudah terdaftar.
                        </li>

                        <li>
                            Jangan mengubah nama kolom pada file Excel.
                        </li>

                    </ul>

                </div>


                {{-- =================================================
                    BUTTON
                ================================================== --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">

                    <a
                        href="{{ route('students.index') }}"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white transition"
                    >
                        Import Siswa
                    </button>

                </div>

            </div>

        </form>

    </x-common.component-card>

@endsection