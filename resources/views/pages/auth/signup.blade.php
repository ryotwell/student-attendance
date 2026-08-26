@extends('layouts.fullscreen-layout')

@section('content')
    {{-- Background utama: brand di mobile, abu-abu di desktop --}}
    <div class="relative z-1 bg-brand-600 dark:bg-brand-900 lg:bg-gray-100 lg:dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col lg:flex-row">

            <!-- ============ SISI KIRI: Branding / Portal Info ============ -->
            <div class="relative hidden w-full flex-col justify-between overflow-hidden bg-brand-600 p-10 lg:flex lg:w-1/2 dark:bg-brand-900">
                <!-- Dekorasi lingkaran blur -->
                <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -right-16 bottom-0 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute top-1/3 right-1/4 h-40 w-40 rounded-full bg-brand-400/20 blur-2xl"></div>

                <!-- Logo & Nama Sekolah -->
                <div class="relative z-10 flex items-center gap-3">
                    <img src="{{ asset('/logo-sekolah.png') }}" alt="Logo Sekolah" class="h-8 w-8 object-contain">
                    <div>
                        <p class="text-base font-semibold text-white">{{ config('app.name', 'Nama Sekolah') }}</p>
                        <p class="text-xs text-white/70">Portal Aplikasi Sekolah</p>
                    </div>
                </div>

                <!-- Konten tengah: pesan sambutan -->
                <div class="relative z-10 my-auto max-w-md">
                    <h2 class="mb-4 text-3xl leading-tight font-bold text-white">
                        Daftar Akun Baru
                    </h2>
                    <p class="text-base leading-relaxed text-white/80">
                        Isi formulir di bawah untuk membuat akun dan sekaligus mendaftarkan sekolah Anda.
                    </p>

                    <!-- Highlight fitur singkat -->
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

                <!-- Footer kiri -->
                <div class="relative z-10">
                    <p class="text-xs text-white/60">&copy; {{ date('Y') }} satak.id Seluruh hak cipta dilindungi.</p>
                </div>
            </div>

            <!-- ============ SISI KANAN: Form Registrasi ============ -->
            <div class="flex w-full flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
                <div class="mx-auto w-full max-w-md">

                    <!-- Logo untuk mobile -->
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

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="space-y-5">
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

                                <!-- Email -->
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
                                            <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3" />
                                            </svg>
                                            <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                    fill="#98A2B3" />
                                            </svg>
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

                                <!-- Nama Sekolah (baru) -->
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
                                
                                <!-- Level Sekolah (wajib) -->
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

                                <!-- Link ke login -->
                                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                                    Sudah punya akun?
                                    <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 font-medium dark:text-brand-400">
                                        Masuk di sini
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer kanan (mobile) -->
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
                    <!-- SVG sama seperti sebelumnya, saya singkat -->
                </button>
            </div>
        </div>
    </div>
@endsection