@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengaturan Sekolah" />

    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Success Alert --}}
        @if (session('success'))
            <x-ui.alert
                variant="success"
                title="Berhasil"
                :message="session('success')"
            />
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="rounded-xl border border-error-200 bg-error-50 p-4 dark:border-error-900/30 dark:bg-error-900/10">
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-error-100 text-error-600 dark:bg-error-900/30 dark:text-error-400">
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.71 2.64h15.64a2 2 0 001.71-2.64l-7.82-14a2.5 2.5 0 00-2.5-2.5h-13A2.5 2.5 0 003 7.5v9a2.5 2.5 0 002.5 2.5h13a2.5 2.5 0 002.5-2.5v-9z"
                            />
                        </svg>
                    </div>

                    <div>
                        <h4 class="font-semibold text-error-700 dark:text-error-400">
                            Terdapat kesalahan
                        </h4>

                        <ul class="mt-1 list-disc pl-5 text-sm text-error-600 dark:text-error-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Pengaturan Sekolah
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola informasi dan identitas sekolah Anda.
            </p>
        </div>

        {{-- Form --}}
        <form
            action="{{ route('school-settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- =====================================================
                    LOGO SEKOLAH
                ====================================================== --}}
                <div class="lg:col-span-1">
                    <x-common.component-card title="Logo Sekolah">

                        <div class="flex flex-col items-center">

                            {{-- Logo Preview --}}
                            <div
                                class="flex h-44 w-44 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900"
                            >

                                @if ($school->logo)
                                    <img
                                        id="logo-preview"
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('s3')->url($school->logo) }}"
                                        alt="Logo {{ $school->name }}"
                                        class="h-full w-full object-contain p-3"
                                    >
                                @else
                                    <div
                                        id="logo-placeholder"
                                        class="flex flex-col items-center justify-center text-gray-400"
                                    >
                                        <svg
                                            class="h-16 w-16"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M3 7.5A2.5 2.5 0 015.5 5h13A2.5 2.5 0 0121 7.5v9a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 16.5v-9z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M8 10h.01M21 15l-4.5-4.5L7 20"
                                            />
                                        </svg>

                                        <span class="mt-2 text-xs">
                                            Belum ada logo
                                        </span>
                                    </div>
                                @endif

                            </div>

                            {{-- Upload Logo --}}
                            <div class="mt-5 w-full">

                                <label
                                    for="logo"
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Ganti Logo
                                </label>

                                <input
                                    id="logo"
                                    name="logo"
                                    type="file"
                                    accept=".jpg,.jpeg,.png"
                                    class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-brand-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-brand-900/20 dark:file:text-brand-400"
                                >

                                <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                    Format yang didukung: JPG, JPEG, PNG.
                                    <br>
                                    Ukuran maksimal 2 MB.
                                </p>

                            </div>

                        </div>

                    </x-common.component-card>
                </div>


                {{-- =====================================================
                    INFORMASI SEKOLAH
                ====================================================== --}}
                <div class="lg:col-span-2">
                    <x-common.component-card title="Informasi Sekolah">

                        <div class="space-y-5">

                            {{-- Nama Sekolah --}}
                            <div>
                                <label
                                    for="name"
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Nama Sekolah
                                    <span class="text-error-500">*</span>
                                </label>

                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $school->name) }}"
                                    required
                                    maxlength="255"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-800"
                                    placeholder="Masukkan nama sekolah"
                                >

                                @error('name')
                                    <p class="mt-1 text-sm text-error-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- NPSN & Jenjang --}}
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                {{-- NPSN --}}
                                <div>
                                    <label
                                        for="npsn"
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        NPSN
                                    </label>

                                    <input
                                        id="npsn"
                                        name="npsn"
                                        type="text"
                                        value="{{ old('npsn', $school->npsn) }}"
                                        maxlength="50"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-800"
                                        placeholder="Masukkan NPSN"
                                    >

                                    @error('npsn')
                                        <p class="mt-1 text-sm text-error-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                {{-- Jenjang --}}
                                <div>
                                    <label
                                        for="level"
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Jenjang Sekolah
                                        <span class="text-error-500">*</span>
                                    </label>

                                    <select
                                        id="level"
                                        name="level"
                                        required
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs outline-none transition focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                                    >
                                        <option value="">
                                            Pilih Jenjang
                                        </option>

                                        <option
                                            value="SD"
                                            @selected(old('level', $school->level) === 'SD')
                                        >
                                            SD
                                        </option>

                                        <option
                                            value="SMP"
                                            @selected(old('level', $school->level) === 'SMP')
                                        >
                                            SMP
                                        </option>

                                        <option
                                            value="SMA"
                                            @selected(old('level', $school->level) === 'SMA')
                                        >
                                            SMA
                                        </option>
                                    </select>

                                    @error('level')
                                        <p class="mt-1 text-sm text-error-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>


                            {{-- Alamat --}}
                            <div>
                                <label
                                    for="address"
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Alamat Sekolah
                                </label>

                                <textarea
                                    id="address"
                                    name="address"
                                    rows="4"
                                    maxlength="500"
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-800"
                                    placeholder="Masukkan alamat lengkap sekolah"
                                >{{ old('address', $school->address) }}</textarea>

                                @error('address')
                                    <p class="mt-1 text-sm text-error-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Telepon & Email --}}
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                {{-- Telepon --}}
                                <div>
                                    <label
                                        for="phone"
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Nomor Telepon
                                    </label>

                                    <input
                                        id="phone"
                                        name="phone"
                                        type="text"
                                        value="{{ old('phone', $school->phone) }}"
                                        maxlength="20"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-800"
                                        placeholder="Contoh: 0370xxxxxxxx"
                                    >

                                    @error('phone')
                                        <p class="mt-1 text-sm text-error-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                {{-- Email --}}
                                <div>
                                    <label
                                        for="email"
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Email Sekolah
                                    </label>

                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email', $school->email) }}"
                                        maxlength="255"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs outline-none transition placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-800"
                                        placeholder="email@sekolah.sch.id"
                                    >

                                    @error('email')
                                        <p class="mt-1 text-sm text-error-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            FOOTER / BUTTON
                        ================================================== --}}
                        <div class="mt-6 flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus:outline-none focus:ring-3 focus:ring-brand-500/20"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>

                                Simpan Perubahan
                            </button>

                        </div>

                    </x-common.component-card>
                </div>

            </div>

        </form>

    </div>


    {{-- =============================================================
        LOGO PREVIEW
    ============================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const logoInput = document.getElementById('logo');

            if (!logoInput) {
                return;
            }

            logoInput.addEventListener('change', function (event) {

                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                // Validasi ukuran file di sisi client
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran logo maksimal 2 MB.');

                    logoInput.value = '';

                    return;
                }

                // Validasi format file
                const allowedTypes = [
                    'image/jpeg',
                    'image/png'
                ];

                if (!allowedTypes.includes(file.type)) {
                    alert('Logo harus berformat JPG, JPEG, atau PNG.');

                    logoInput.value = '';

                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {

                    let preview = document.getElementById('logo-preview');
                    const placeholder = document.getElementById('logo-placeholder');

                    // Jika sebelumnya belum memiliki logo
                    if (!preview) {

                        preview = document.createElement('img');

                        preview.id = 'logo-preview';

                        preview.className =
                            'h-full w-full object-contain p-3';

                        preview.alt = 'Preview Logo Sekolah';

                        const container = logoInput
                            .closest('.mt-5')
                            .previousElementSibling;

                        if (placeholder) {
                            placeholder.remove();
                        }

                        container.appendChild(preview);
                    }

                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection