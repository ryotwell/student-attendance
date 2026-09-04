@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Anda belum berlangganan" />

<div class="mx-auto max-w-4xl">
    <x-common.component-card>
        <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
            {{-- Icon --}}
            <div
                class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/15">
                <svg
                    class="h-10 w-10 text-brand-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20h15.6a2 2 0 001.73-2.64l-7.82-13.5a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            {{-- Title --}}
            <h2 class="mb-3 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Maaf, Anda Belum Berlangganan
            </h2>

            {{-- Description --}}
            <p class="mb-8 max-w-xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                Fitur Ini hanya dapat digunakan oleh pengguna yang
                telah berlangganan. Silakan hubungi admin untuk melakukan
                aktivasi atau berlangganan layanan.
            </p>

            {{-- WhatsApp Button --}}
            <a
                href="https://wa.me/6281947556108?text=Halo%20Admin,%20saya%20ingin%20berlangganan%20layanan."
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-500 px-5 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-600 focus:outline-none focus:ring-3 focus:ring-green-500/20">
                
                <svg
                    class="h-5 w-5"
                    fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.206-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.273.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982 1-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.002 5.45-4.437 9.884-9.887 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.158 11.89c0 2.096.547 4.142 1.588 5.946L.057 24l6.304-1.654a11.933 11.933 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.892-11.89a11.821 11.821 0 00-3.477-8.416" />
                </svg>

                Hubungi Admin via WhatsApp
            </a>

            <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                +62 819-4755-6108
            </p>
        </div>
    </x-common.component-card>
</div>

@endsection
