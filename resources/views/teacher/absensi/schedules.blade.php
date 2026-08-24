@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Pilih Absensi" />


<div class="mx-auto max-w-6xl">


    @if($schedules->isEmpty())


        <x-common.component-card>

            <div class="py-12 text-center">


                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full 
                bg-brand-50 text-brand-500
                dark:bg-brand-900/30 dark:text-brand-400">


                    <svg
                        width="32"
                        height="32"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >

                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M3 10h18"/>
                        <path d="M8 3v4"/>
                        <path d="M16 3v4"/>

                    </svg>


                </div>



                <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white">
                    Belum Ada Jadwal
                </h3>


                <p class="text-gray-500 dark:text-gray-400">
                    Anda belum memiliki jadwal mengajar untuk saat ini.
                </p>


            </div>


        </x-common.component-card>



    @else



        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">


            @foreach ($schedules as $schedule)


            <a
                href="{{ route('attendance.show', [
                    'schedule_id'=>$schedule->id,
                    'class_id'=>$schedule->xclass_id,
                    'date'=>now()->format('Y-m-d')
                ]) }}"

                class="
                group relative overflow-hidden rounded-2xl

                border border-gray-200
                bg-white p-5

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




                {{-- ICON + HARI --}}

                <div class="mb-5 flex items-start justify-between">


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



                    <span
                        class="
                        rounded-full

                        bg-brand-50
                        px-3 py-1

                        text-xs font-semibold

                        text-brand-600

                        dark:bg-brand-900/30
                        dark:text-brand-400
                        "
                    >

                        {{ \App\Helpers\MenuHelper::getDayName($schedule->day) }}

                    </span>


                </div>





                {{-- SUBJECT --}}

                <h4
                    class="
                    mb-1

                    text-lg font-bold

                    text-gray-800

                    transition

                    group-hover:text-brand-600

                    dark:text-white
                    dark:group-hover:text-brand-400
                    "
                >

                    Mata Pelajaran
                    {{ $schedule->subject?->name ?? 'Mata Pelajaran' }}

                </h4>




                {{-- CLASS --}}

                <p class="mb-3 text-sm text-gray-500 dark:text-gray-400">

                    <span class="font-medium text-gray-700 dark:text-gray-300">

                        Kelas {{ $schedule->xclass?->name ?? 'Kelas' }}

                    </span>

                </p>





                {{-- TIME --}}

                <div
                    class="
                    mb-5 flex items-center gap-2

                    text-sm

                    text-gray-500

                    dark:text-gray-400
                    "
                >


                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >

                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>

                    </svg>


                    <span>

                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}

                        —

                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}

                    </span>


                </div>





                {{-- BUTTON --}}

                <div class="border-t border-gray-100 pt-4 dark:border-gray-800">


                    <span
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


                        Mulai Absensi


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


                    </span>


                </div>



            </a>



            @endforeach


        </div>



    @endif



</div>


@endsection