@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Riwayat Absensi" />


<div class="mx-auto max-w-6xl">


    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">


        @forelse($histories as $history)


            @php
                $xclass = $history->xclass;
            @endphp



            @if($xclass)


                <a
                    href="{{ route('absensi.history.show', [
                        'date' => $history->date->format('Y-m-d'),
                        'class' => $xclass->id,
                        'schedule' => $history->schedule_id
                    ]) }}"

                    class="
                    group relative overflow-hidden

                    rounded-2xl

                    border border-gray-200

                    bg-white

                    p-5

                    shadow-sm

                    transition-all duration-300

                    hover:-translate-y-1

                    hover:border-brand-300

                    hover:shadow-xl


                    dark:border-gray-700

                    dark:bg-gray-900

                    dark:hover:border-brand-500
                    "
                >


                    {{-- Border kiri --}}
                    <span
                        class="
                        absolute left-0 top-0

                        h-full w-1

                        bg-brand-500

                        transition-all

                        group-hover:w-2
                        "
                    ></span>




                    {{-- Header --}}
                    <div class="mb-5 flex items-center gap-4">


                        <div
                            class="
                            flex h-14 w-14 items-center justify-center

                            rounded-xl

                            bg-brand-50

                            text-brand-600

                            transition

                            group-hover:scale-110


                            dark:bg-brand-900/30

                            dark:text-brand-400
                            "
                        >


                            <svg
                                width="28"
                                height="28"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >

                                <rect x="3" y="5" width="18" height="16" rx="2"/>

                                <path d="M3 10h18"/>

                                <path d="M8 3v4"/>

                                <path d="M16 3v4"/>

                            </svg>


                        </div>




                        <div>


                            <h3
                                class="
                                text-lg font-bold

                                text-gray-800

                                transition

                                group-hover:text-brand-600


                                dark:text-white

                                dark:group-hover:text-brand-400
                                "
                            >

                                {{ $xclass->name }}

                            </h3>



                            <p class="text-sm text-gray-500 dark:text-gray-400">

                                {{ $history->schedule->subject->name }}

                            </p>


                        </div>


                    </div>






                    {{-- Tanggal --}}
                    <div
                        class="
                        mb-4

                        flex items-center gap-2

                        rounded-xl

                        bg-brand-50

                        px-4 py-3

                        text-sm

                        font-medium

                        text-brand-600


                        dark:bg-brand-900/30

                        dark:text-brand-400
                        "
                    >


                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <rect x="3" y="5" width="18" height="16" rx="2"/>

                            <line x1="8" y1="3" x2="8" y2="7"/>

                            <line x1="16" y1="3" x2="16" y2="7"/>

                        </svg>



                        <span>

                            {{ $history->date->translatedFormat('d F Y') }}

                        </span>


                    </div>





                    {{-- Button --}}
                    <div
                        class="
                        flex w-full items-center justify-center

                        rounded-xl

                        bg-brand-500

                        px-4 py-2.5

                        text-sm font-semibold

                        text-white

                        transition-all duration-300

                        group-hover:bg-brand-600

                        group-hover:shadow-md
                        "
                    >

                        Lihat Riwayat


                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"

                            class="ml-2 transition-transform group-hover:translate-x-1"
                        >

                            <path d="M5 12h14"/>

                            <path d="M13 6l6 6-6 6"/>

                        </svg>


                    </div>



                </a>



            @else



                {{-- Data kelas tidak tersedia --}}
                <div
                    class="
                    relative overflow-hidden

                    rounded-2xl

                    border border-gray-200

                    bg-white

                    p-5

                    opacity-60


                    dark:border-gray-700

                    dark:bg-gray-900
                    "
                >


                    <span
                        class="
                        absolute left-0 top-0

                        h-full w-1

                        bg-gray-400
                        "
                    ></span>



                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">

                        Kelas tidak diketahui

                    </h3>



                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">

                        {{ $history->schedule->subject->name }}

                    </p>



                    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">

                        <p class="text-sm text-gray-500 dark:text-gray-400">

                            Tanggal

                            <span class="font-semibold text-gray-800 dark:text-white">

                                {{ $history->date->translatedFormat('d F Y') }}

                            </span>

                        </p>

                    </div>


                </div>


            @endif



        @empty



            <div
                class="
                col-span-full

                rounded-2xl

                border border-dashed

                border-gray-200

                bg-gray-50

                py-12

                text-center


                dark:border-gray-700

                dark:bg-gray-900/50
                "
            >

                <p class="text-sm text-gray-500 dark:text-gray-400">

                    Belum ada riwayat absensi.

                </p>


            </div>



        @endforelse


    </div>


</div>


@endsection