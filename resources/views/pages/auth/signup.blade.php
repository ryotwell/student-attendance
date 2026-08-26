@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 min-h-screen bg-brand-600 dark:bg-brand-900 lg:bg-gray-100 lg:dark:bg-gray-900">
        <div class="flex min-h-screen w-full flex-col lg:flex-row">

            <!-- ============ SISI KIRI: Branding ============ -->
            <div class="relative hidden w-full flex-col justify-between overflow-hidden bg-brand-600 p-6 sm:p-10 lg:flex lg:w-2/5 xl:w-1/2 dark:bg-brand-900">
                <!-- Dekorasi blur -->
                <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -right-16 bottom-0 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute top-1/3 right-1/4 h-40 w-40 rounded-full bg-brand-400/20 blur-2xl"></div>

                <div class="relative z-10 my-auto max-w-md">
                    <h2 class="mb-4 text-2xl font-bold text-white sm:text-3xl">
                        Daftar Akun & Sekolah
                    </h2>
                    <p class="text-sm leading-relaxed text-white/80 sm:text-base">
                        Isi formulir di bawah untuk membuat akun dan mendaftarkan sekolah Anda.
                    </p>
                    <div class="mt-6 space-y-3 sm:mt-8 sm:space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15 sm:h-9 sm:w-9">
                                <svg class="h-4 w-4 text-white sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="text-xs text-white/85 sm:text-sm">Kelola data sekolah Anda sendiri</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15 sm:h-9 sm:w-9">
                                <svg class="h-4 w-4 text-white sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="text-xs text-white/85 sm:text-sm">Akses penuh sebagai Admin Sekolah</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15 sm:h-9 sm:w-9">
                                <svg class="h-4 w-4 text-white sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <p class="text-xs text-white/85 sm:text-sm">Antarmuka simpel dan mudah digunakan</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10">
                    <p class="text-xs text-white/60">&copy; {{ date('Y') }} satak.id</p>
                </div>
            </div>

            <!-- ============ SISI KANAN: Form Registrasi ============ -->
            <div class="flex w-full flex-1 flex-col items-center justify-center px-4 py-8 sm:px-6 sm:py-10 lg:w-3/5 xl:w-1/2 lg:px-8">
                <div class="w-full max-w-md">

                    <div class="rounded-2xl bg-white p-6 shadow-theme-sm dark:bg-gray-800 sm:p-8 md:p-10">
                        <div class="mb-6">
                            <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90 sm:text-2xl">
                                Daftar Akun & Sekolah
                            </h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Lengkapi data di bawah untuk membuat akun dan mendaftarkan sekolah Anda
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="space-y-4 sm:space-y-5">
                                <!-- ====== DATA USER ====== -->
                                <div>
                                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama Lengkap <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" autofocus
                                            placeholder="Nama lengkap"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('name') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('name')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Email <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6" />
                                            </svg>
                                        </span>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
                                            placeholder="email@sekolah.sch.id"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Kata Sandi <span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" autocomplete="new-password"
                                            placeholder="Minimal 8 karakter"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Konfirmasi Kata Sandi <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                            placeholder="Ulangi kata sandi"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password_confirmation') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- ====== DATA SEKOLAH ====== -->
                                <div>
                                    <label for="school_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 21h18M3 10.5h18M5.25 3.75h13.5M5.25 3.75v13.5M18.75 3.75v13.5" />
                                            </svg>
                                        </span>
                                        <input type="text" id="school_name" name="school_name" value="{{ old('school_name') }}"
                                            placeholder="Nama sekolah Anda"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_name') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_name')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="npsn" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NPSN <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                            </svg>
                                        </span>
                                        <input type="text" id="npsn" name="npsn" value="{{ old('npsn') }}"
                                            placeholder="Nomor Pokok Sekolah Nasional (8 digit)"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('npsn') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('npsn')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_level" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jenjang Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </span>
                                        <select id="school_level" name="school_level" required
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_level') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror">
                                            <option value="">Pilih Jenjang</option>
                                            <option value="SD" {{ old('school_level') == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                            <option value="SMP" {{ old('school_level') == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                            <option value="SMA" {{ old('school_level') == 'SMA' ? 'selected' : '' }}>SMA / MA / SMK / MAK</option>
                                        </select>
                                        <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('school_level')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Alamat Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                                            placeholder="Alamat lengkap sekolah"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('address') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('address')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_phone" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Telepon Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6" />
                                            </svg>
                                        </span>
                                        <input type="text" id="school_phone" name="school_phone" value="{{ old('school_phone') }}"
                                            placeholder="Nomor telepon sekolah"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_phone') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_phone')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Email Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                            </svg>
                                        </span>
                                        <input type="email" id="school_email" name="school_email" value="{{ old('school_email') }}"
                                            placeholder="email@sekolah.sch.id"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('school_email') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_email')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_logo" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Logo Sekolah <span class="text-gray-400 text-xs">(opsional, maks. 2MB, jpg/png)</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </span>
                                        <input type="file" id="school_logo" name="school_logo" accept="image/*"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 file:mr-4 file:rounded-md file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-900/30 dark:file:text-brand-400 @error('school_logo') border-error-400 focus:border-error-400 focus:ring-error-500/10 @enderror" />
                                    </div>
                                    @error('school_logo')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
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

                    <!-- Footer mobile -->
                    <p class="mt-4 text-center text-xs text-white/60 lg:hidden">
                        &copy; {{ date('Y') }} satak.id
                    </p>
                </div>
            </div>

            <!-- Toggler dark/light mode -->
            <div class="fixed right-4 bottom-4 z-50 sm:right-6 sm:bottom-6">
                <button
                    class="bg-brand-500 hover:bg-brand-600 inline-flex h-12 w-12 items-center justify-center rounded-full text-white shadow-lg transition-colors sm:h-14 sm:w-14"
                    @click.prevent="$store.theme.toggle()">
                    <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z" />
                    </svg>
                    <svg class="fill-current dark:hidden" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection