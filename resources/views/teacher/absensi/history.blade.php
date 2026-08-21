@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Riwayat Absensi" />

    <div class="mx-auto max-w-6xl space-y-5 grid grid-cols-2 gap-4">

        @forelse($histories as $history)
            <a
                href="{{ route('absensi.history.show', [
                    'date' => \Carbon\Carbon::parse($history->date)->format('Y-m-d'),
                    'class' => $history->xclass_id,
                    'schedule' => $history->schedule_id,
                ]) }}">


                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">


                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">


                        <div>


                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">

                                {{ $history->xclass->name }}

                            </h3>


                            <p class="text-sm text-gray-500 dark:text-gray-400">


                                {{ $history->schedule->subject->name }}


                            </p>


                        </div>




                        {{-- <div
                            class="rounded-xl bg-brand-50 px-4 py-3 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">


                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">

                                <rect x="3" y="4" width="18" height="18" rx="2" />

                                <line x1="16" y1="2" x2="16" y2="6" />

                                <line x1="8" y1="2" x2="8" y2="6" />

                            </svg>


                        </div> --}}


                    </div>



                    <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">


                        <p class="text-sm text-gray-500 dark:text-gray-400">


                            Tanggal


                            <span class="font-semibold text-gray-800 dark:text-white">

                                {{ \Carbon\Carbon::parse($history->date)->translatedFormat('d F Y') }}

                            </span>


                        </p>


                    </div>


                </div>


            </a>


        @empty


            <div class="rounded-xl border border-dashed p-10 text-center">

                <p class="text-gray-500">
                    Belum ada riwayat absensi
                </p>

            </div>
        @endforelse



    </div>
@endsection
