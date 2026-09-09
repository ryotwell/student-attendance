@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 min-h-screen bg-gradient-to-br from-[oklch(28%_0.13_263)] via-[oklch(42%_0.19_268)] to-[oklch(56%_0.22_258)] text-white">
        <div class="flex min-h-screen w-full flex-col">

            {{-- =====================================================
                FORM LUPA KATA SANDI
            ====================================================== --}}
            <div class="flex w-full flex-1 flex-col items-center justify-center px-4 py-8 sm:px-6 sm:py-10">
                <div class="w-full max-w-md">

                    <div class="rounded-3xl border border-white/20 bg-white p-6 shadow-2xl sm:p-8 md:p-10">
                        <div class="mb-6">
                            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">
                                Lupa Kata Sandi
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Masukkan email yang terdaftar. Kami akan mengirimkan link untuk reset kata sandi.
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="mb-4 rounded-lg border border-success-400/30 bg-success-500/10 px-4 py-3 text-sm text-success-600">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-error-400/30 bg-error-500/10 px-4 py-3 text-sm text-error-600">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="space-y-4 sm:space-y-5">
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
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus
                                            placeholder="email@sekolah.sch.id"
                                            class="h-11 w-full rounded-lg border bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 transition focus:ring-3 focus:outline-hidden
                                            @error('email') border-error-400/60 focus:border-error-400 focus:ring-error-500/10 @else border-gray-200 focus:border-[oklch(56%_0.22_258)] focus:ring-[oklch(56%_0.22_258)]/10 @enderror" />
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <button type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[oklch(50%_0.2_268)] to-[oklch(56%_0.22_290)] px-4 py-3 text-sm font-medium text-white shadow-lg shadow-black/20 transition hover:opacity-90 hover:shadow-xl">
                                        <span>Kirim Link Reset</span>
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-center text-sm text-gray-500">
                                    Sudah ingat kata sandi?
                                    <a href="{{ route('login') }}" class="font-medium text-[oklch(56%_0.22_258)] hover:underline">
                                        Kembali ke Login
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
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