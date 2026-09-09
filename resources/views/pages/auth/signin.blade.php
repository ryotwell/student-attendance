@extends('layouts.fullscreen-layout')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-[oklch(28%_0.13_263)] via-[oklch(42%_0.19_268)] to-[oklch(56%_0.22_258)] text-white"
        x-data
    >
        {{-- =========================================================
            MAIN WRAPPER
        ========================================================== --}}
        <div class="flex min-h-screen flex-col">

            {{-- =====================================================
                LOGIN CONTENT
            ====================================================== --}}
            <main class="flex flex-1 items-center justify-center px-5 pb-12 pt-4 sm:px-6">

                <div class="w-full max-w-[420px]">

                    {{-- =================================================
                        STATUS MESSAGE
                    ================================================== --}}
                    @if (session('status'))
                        <div
                            class="mb-5 flex items-start gap-3 rounded-xl border border-success-400/30 bg-success-500/10 px-4 py-3.5 text-sm text-success-100"
                        >
                            <svg
                                class="mt-0.5 h-5 w-5 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                />
                            </svg>

                            <span>
                                {{ session('status') }}
                            </span>
                        </div>
                    @endif


                    {{-- =================================================
                        LOGIN FORM
                    ================================================== --}}
                    <div
                        class="rounded-3xl border border-white/20 bg-white p-6 shadow-2xl sm:p-8"
                    >
                        <div class="mb-6 text-center">

                            <div
                                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-[oklch(56%_0.22_258)]/10"
                            >
                                <svg
                                    width="800px"
                                    height="800px"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-12 w-12"
                                    style="color: oklch(56% 0.22 258);"
                                >
                                    <path
                                        d="M2 10L12 4.5L22 10L17.9457 12.2298M2 10L6.05427 12.2298M2 10V16M6 17.5V12.5C6 12.4084 6.01848 12.3182 6.05427 12.2298M6 17.5C6 18.6046 8.68629 19.5 12 19.5C15.3137 19.5 18 18.6046 18 17.5M6 17.5C6 16.3954 8.68629 15.5 12 15.5C15.3137 15.5 18 16.3954 18 17.5M18 17.5V12.5C18 12.4084 17.9815 12.3182 17.9457 12.2298M17.9457 12.2298C17.9334 12.1993 17.9189 12.1691 17.9025 12.139C17.3927 11.2067 14.9439 10.5 12 10.5C9.05606 10.5 6.60733 11.2067 6.09749 12.139C6.08105 12.1691 6.06663 12.1993 6.05427 12.2298"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <h1
                                class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl"
                            >
                                Selamat Datang
                            </h1>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Masuk ke akun Anda untuk melanjutkan
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('login') }}"
                        >
                            @csrf

                            <div class="space-y-5">

                                {{-- =====================================
                                    EMAIL
                                ====================================== --}}
                                <div>
                                    <label
                                        for="email"
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Email
                                        <span class="text-error-500">*</span>
                                    </label>

                                    <div class="relative">

                                        {{-- Icon --}}
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-gray-400"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m3 7 9 6 9-6"
                                                />
                                            </svg>
                                        </div>

                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            autocomplete="email"
                                            autofocus
                                            placeholder="admin@sekolah.id atau username"
                                            class="h-12 w-full rounded-xl border bg-gray-50 py-2.5 pr-4 pl-11 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:ring-4
                                            @error('email')
                                                border-error-400/60 focus:border-error-400 focus:ring-error-500/10
                                            @else
                                                border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10
                                            @enderror"
                                        />
                                    </div>

                                    @error('email')
                                        <p class="mt-1.5 text-sm text-error-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                {{-- =====================================
                                    PASSWORD
                                ====================================== --}}
                                <div x-data="{ showPassword: false }">

                                    <div class="mb-2 flex items-center justify-between">

                                        <label
                                            for="password"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Kata Sandi
                                            <span class="text-error-500">*</span>
                                        </label>

                                        <a
                                            href="{{ route('password.request') }}"
                                            class="text-sm font-medium text-[oklch(56%_0.22_258)] transition hover:underline"
                                        >
                                            Lupa kata sandi?
                                        </a>
                                    </div>

                                    <div class="relative">

                                        {{-- Lock icon --}}
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-gray-400"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A1.5 1.5 0 0 1 18.75 12v7.5a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z"
                                                />
                                            </svg>
                                        </div>

                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            type="password"
                                            id="password"
                                            name="password"
                                            autocomplete="current-password"
                                            placeholder="Masukkan kata sandi"
                                            class="h-12 w-full rounded-xl border bg-gray-50 py-2.5 pr-12 pl-11 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:ring-4
                                            @error('password')
                                                border-error-400/60 focus:border-error-400 focus:ring-error-500/10
                                            @else
                                                border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10
                                            @enderror"
                                        />

                                        {{-- Password toggle --}}
                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 transition hover:text-gray-600"
                                            aria-label="Tampilkan kata sandi"
                                        >
                                            {{-- Show --}}
                                            <svg
                                                x-show="!showPassword"
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.056.21.056.434 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678Z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                />
                                            </svg>

                                            {{-- Hide --}}
                                            <svg
                                                x-show="showPassword"
                                                x-cloak
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M3 3l18 18"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M10.584 10.587a2 2 0 0 0 2.829 2.829"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9.88 5.09A10.94 10.94 0 0 1 12 4.9c4.64 0 8.577 2.51 9.964 6.678a1.012 1.012 0 0 1 0 .644 10.94 10.94 0 0 1-4.094 5.254M6.228 6.228A10.94 10.94 0 0 0 2.036 11.678a1.012 1.012 0 0 0 0 .644C3.423 16.49 7.36 19 12 19c1.61 0 3.12-.36 4.467-1.004"
                                                />
                                            </svg>
                                        </button>
                                    </div>

                                    @error('password')
                                        <p class="mt-1.5 text-sm text-error-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>


                                {{-- =====================================
                                    LOGIN BUTTON
                                ====================================== --}}
                                <button
                                    type="submit"
                                    class="group flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[oklch(50%_0.2_268)] to-[oklch(56%_0.22_290)] px-4 text-sm font-semibold text-white shadow-lg shadow-black/20 transition duration-200 hover:opacity-90 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[oklch(56%_0.22_258)]/20 active:scale-[0.99]"
                                >
                                    <span>Masuk</span>

                                    <svg
                                        class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                        />
                                    </svg>
                                </button>

                            </div>
                        </form>


                        {{-- =================================================
                            REGISTER
                        ================================================== --}}
                        <div
                            class="mt-6 border-t border-gray-200 pt-6 text-center"
                        >
                            <p class="text-sm text-gray-500">
                                Belum punya akun?
                                <a
                                    href="{{ route('register') }}"
                                    class="font-semibold text-[oklch(56%_0.22_258)] transition hover:underline"
                                >
                                    Daftar sekarang
                                </a>
                            </p>
                        </div>
                    </div>


                    {{-- =================================================
                        SECURITY / INFO
                    ================================================== --}}
                    <div class="mt-6 flex items-center justify-center gap-2 text-xs text-white/50">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 3 5.25 6v5.25c0 4.48 2.87 8.53 6.75 9.75 3.88-1.22 6.75-5.27 6.75-9.75V6L12 3Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m9.5 12 1.75 1.75L14.75 10"
                            />
                        </svg>

                        <span>Hak Cipta di lindungi oleh Undang-undang</span>
                    </div>

                </div>
            </main>


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