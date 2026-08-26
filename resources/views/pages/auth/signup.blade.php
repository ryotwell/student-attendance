@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 bg-brand-600 dark:bg-brand-900 lg:bg-gray-100 lg:dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col lg:flex-row">

            <!-- SISI KIRI: Branding (sama) -->
            <div class="relative hidden w-full flex-col justify-between overflow-hidden bg-brand-600 p-10 lg:flex lg:w-1/2 dark:bg-brand-900">
                <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -right-16 bottom-0 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute top-1/3 right-1/4 h-40 w-40 rounded-full bg-brand-400/20 blur-2xl"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <img src="{{ asset('/logo-sekolah.png') }}" alt="Logo Sekolah" class="h-8 w-8 object-contain">
                    <div>
                        <p class="text-base font-semibold text-white">{{ config('app.name', 'Nama Sekolah') }}</p>
                        <p class="text-xs text-white/70">Portal Aplikasi Sekolah</p>
                    </div>
                </div>

                <div class="relative z-10 my-auto max-w-md">
                    <h2 class="mb-4 text-3xl leading-tight font-bold text-white">
                        Daftar Akun & Sekolah
                    </h2>
                    <p class="text-base leading-relaxed text-white/80">
                        Isi formulir di bawah untuk membuat akun dan mendaftarkan sekolah Anda.
                    </p>
                    <div class="mt-8 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="text-sm text-white/85">Kelola data sekolah Anda sendiri</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="text-sm text-white/85">Akses penuh sebagai Admin Sekolah</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="text-sm text-white/85">Antarmuka simpel dan mudah digunakan</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10">
                    <p class="text-xs text-white/60">&copy; {{ date('Y') }} satak.id Seluruh hak cipta dilindungi.</p>
                </div>
            </div>

            <!-- SISI KANAN: Form Registrasi -->
            <div class="flex w-full flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
                <div class="mx-auto w-full max-w-md">

                    <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                        <img src="{{ asset('/logo-sekolah.png') }}" alt="Logo Sekolah" class="h-15 w-15 object-contain">
                        <div class="text-left">
                            <p class="text-xl font-semibold text-white dark:text-white">
                                {{ config('app.name', 'Nama Sekolah') }}
                            </p>
                            <p class="text-white/70 dark:text-white/70">
                                Portal Aplikasi Sekolah
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-8 shadow-theme-sm dark:bg-gray-800 sm:p-10">
                        <div class="mb-7">
                            <h1 class="mb-2 text-title-sm font-semibold text-gray-800 sm:text-title-md dark:text-white/90">
                                Daftar Akun & Sekolah
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Lengkapi data di bawah untuk membuat akun dan mendaftarkan sekolah Anda
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="mb-5 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="space-y-5">
                                <!-- ====== DATA USER ====== -->
                                <!-- Nama User -->
                                <div>
                                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama Lengkap <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" autofocus
                                            placeholder="Nama lengkap"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('name') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('name')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email User -->
                                <div>
                                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Email <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6" />
                                            </svg>
                                        </span>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
                                            placeholder="email@sekolah.sch.id"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('email')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Kata Sandi <span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" autocomplete="new-password"
                                            placeholder="Minimal 8 karakter"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                        <span @click="showPassword = !showPassword"
                                            class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                            <!-- SVG show/hide (sama) -->
                                        </span>
                                    </div>
                                    @error('password')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password -->
                                <div>
                                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Konfirmasi Kata Sandi <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                            placeholder="Ulangi kata sandi"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password_confirmation') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- ====== DATA SEKOLAH ====== -->
                                <!-- Nama Sekolah -->
                                <div>
                                    <label for="school_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 21h18M3 10.5h18M5.25 3.75h13.5M5.25 3.75v13.5M18.75 3.75v13.5" />
                                            </svg>
                                        </span>
                                        <input type="text" id="school_name" name="school_name" value="{{ old('school_name') }}"
                                            placeholder="Nama sekolah Anda"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_name') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_name')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- NPSN -->
                                <div>
                                    <label for="npsn" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NPSN <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                            </svg>
                                        </span>
                                        <input type="text" id="npsn" name="npsn" value="{{ old('npsn') }}"
                                            placeholder="Nomor Pokok Sekolah Nasional (8 digit)"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('npsn') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('npsn')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Level Sekolah -->
                                <div>
                                    <label for="school_level" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jenjang Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </span>
                                        <select id="school_level" name="school_level" required
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_level') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror">
                                            <option value="">Pilih Jenjang</option>
                                            <option value="SD" {{ old('school_level') == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                            <option value="SMP" {{ old('school_level') == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                            <option value="SMA" {{ old('school_level') == 'SMA' ? 'selected' : '' }}>SMA / MA / SMK / MAK</option>
                                        </select>
                                        <span class="absolute top-1/2 right-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('school_level')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Alamat Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                                            placeholder="Alamat lengkap sekolah"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('address') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('address')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Telepon Sekolah -->
                                <div>
                                    <label for="school_phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Telepon Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6" />
                                            </svg>
                                        </span>
                                        <input type="text" id="school_phone" name="school_phone" value="{{ old('school_phone') }}"
                                            placeholder="Nomor telepon sekolah"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_phone') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_phone')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email Sekolah -->
                                <div>
                                    <label for="school_email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Email Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                            </svg>
                                        </span>
                                        <input type="email" id="school_email" name="school_email" value="{{ old('school_email') }}"
                                            placeholder="email@sekolah.sch.id"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_email') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_email')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Logo Sekolah (opsional) -->
                                <div>
                                    <label for="school_logo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Logo Sekolah <span class="text-gray-400 text-xs">(opsional, maks. 2MB, jpg/png)</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3.5 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </span>
                                        <input type="file" id="school_logo" name="school_logo" accept="image/*"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 file:mr-4 file:rounded-md file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-900/30 dark:file:text-brand-400 @error('school_logo') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_logo')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tombol Daftar -->
                                <div>
                                    <button type="submit"
                                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                        <span>Daftar</span>
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                                    Sudah punya akun?
                                    <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 font-medium dark:text-brand-400">
                                        Masuk di sini
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <p class="mt-6 text-center text-xs text-white/60 lg:text-gray-400 lg:dark:text-gray-400 lg:hidden">
                        &copy; {{ date('Y') }} satak.id Seluruh hak cipta dilindungi.
                    </p>
                </div>
            </div>

            <!-- Toggler dark/light mode -->
            <div class="fixed right-6 bottom-6 z-50">
                <button
                    class="bg-brand-500 hover:bg-brand-600 inline-flex size-14 items-center justify-center rounded-full text-white shadow-lg transition-colors"
                    @click.prevent="$store.theme.toggle()">
                    <!-- SVG toggler -->
                </button>
            </div>
        </div>
    </div>
@endsection