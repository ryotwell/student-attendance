@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 min-h-screen bg-gradient-to-br from-[oklch(28%_0.13_263)] via-[oklch(42%_0.19_268)] to-[oklch(56%_0.22_258)] text-white">
        <div class="flex min-h-screen w-full flex-col">

            {{-- =====================================================
                FORM REGISTRASI
            ====================================================== --}}
            <div class="flex w-full flex-1 flex-col items-center justify-center px-4 py-8 sm:px-6 sm:py-10">
                <div class="w-full max-w-md">

                    <div class="rounded-3xl border border-white/20 bg-white p-6 shadow-2xl sm:p-8 md:p-10">
                        <div class="mb-6">
                            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">
                                Daftar Akun & Sekolah
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Lengkapi data di bawah untuk membuat akun dan mendaftarkan sekolah Anda
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-error-400/30 bg-error-500/10 px-4 py-3 text-sm text-error-600">
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
                                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">
                                        Nama Lengkap <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" autofocus
                                            placeholder="Nama lengkap"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('name') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('name')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
                                        Email <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6" />
                                            </svg>
                                        </span>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
                                            placeholder="email@sekolah.sch.id"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('email') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
                                        Kata Sandi <span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" autocomplete="new-password"
                                            placeholder="Minimal 8 karakter"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('password') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 transition hover:text-gray-600">
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
                                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">
                                        Konfirmasi Kata Sandi <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                            placeholder="Ulangi kata sandi"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('password_confirmation') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- ====== DATA SEKOLAH ====== -->
                                <div>
                                    <label for="school_name" class="mb-1 block text-sm font-medium text-gray-700">
                                        Nama Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 21h18M3 10.5h18M5.25 3.75h13.5M5.25 3.75v13.5M18.75 3.75v13.5" />
                                            </svg>
                                        </span>
                                        <input type="text" id="school_name" name="school_name" value="{{ old('school_name') }}"
                                            placeholder="Nama sekolah Anda"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('school_name') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('school_name')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="npsn" class="mb-1 block text-sm font-medium text-gray-700">
                                        NPSN <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                            </svg>
                                        </span>
                                        <input type="text" id="npsn" name="npsn" value="{{ old('npsn') }}"
                                            placeholder="Nomor Pokok Sekolah Nasional (8 digit)"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('npsn') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('npsn')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_level" class="mb-1 block text-sm font-medium text-gray-700">
                                        Jenjang Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </span>
                                        <select id="school_level" name="school_level" required
                                            class="h-11 w-full appearance-none rounded-lg border bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 transition focus:ring-3 focus:outline-hidden
                                            @error('school_level') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror">
                                            <option value="" class="text-gray-900">Pilih Jenjang</option>
                                            <option value="SD" class="text-gray-900" {{ old('school_level') == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                            <option value="SMP" class="text-gray-900" {{ old('school_level') == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                            <option value="SMA" class="text-gray-900" {{ old('school_level') == 'SMA' ? 'selected' : '' }}>SMA / MA / SMK / MAK</option>
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
                                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">
                                        Alamat Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                                            placeholder="Alamat lengkap sekolah"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('address') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('address')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_phone" class="mb-1 block text-sm font-medium text-gray-700">
                                        Telepon Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6" />
                                            </svg>
                                        </span>
                                        <input type="text" id="school_phone" name="school_phone" value="{{ old('school_phone') }}"
                                            placeholder="Nomor telepon sekolah"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('school_phone') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('school_phone')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_email" class="mb-1 block text-sm font-medium text-gray-700">
                                        Email Sekolah <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                            </svg>
                                        </span>
                                        <input type="email" id="school_email" name="school_email" value="{{ old('school_email') }}"
                                            placeholder="email@sekolah.sch.id"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('school_email') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('school_email')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="school_logo" class="mb-1 block text-sm font-medium text-gray-700">
                                        Logo Sekolah <span class="text-xs text-gray-400">(maks. 2MB, jpg/png)</span> <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute top-1/2 left-3 -translate-y-1/2" style="color: oklch(56% 0.22 258);">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </span>
                                        <input type="file" id="school_logo" name="school_logo" accept="image/*"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 transition focus:ring-3 focus:outline-hidden file:mr-4 file:rounded-md file:border-0 file:bg-[oklch(56%_0.22_258)]/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-[oklch(56%_0.22_258)] hover:file:bg-[oklch(56%_0.22_258)]/20
                                            @error('school_logo') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('school_logo')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tombol Daftar -->
                                <div>
                                    <button type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[oklch(50%_0.2_268)] to-[oklch(56%_0.22_290)] px-4 py-3 text-sm font-medium text-white shadow-lg shadow-black/20 transition hover:opacity-90 hover:shadow-xl">
                                        <span>Daftar</span>
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-center text-sm text-gray-500">
                                    Sudah punya akun?
                                    <a href="{{ route('login') }}" class="font-medium text-[oklch(56%_0.22_258)] hover:underline">
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

            {{-- =====================================================
                FOOTER
            ====================================================== --}}
            <footer class="px-6 py-5">
                <p class="text-center text-white text-xs">
                    Copyright &copy; SaaS SATAK-{{ date('Y') }}
                </p>
            </footer>
        </div>
    </div>
@endsection