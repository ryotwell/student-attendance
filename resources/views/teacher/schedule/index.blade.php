@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb pageTitle="Jadwal Mengajar" />

    {{-- <x-common.component-card title="Jadwal Mengajar"> --}}

        @if($schedules->count())

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-sm dark:text-gray-400 w-28">
                                Jam
                            </th>

                            @foreach(\App\Helpers\MenuHelper::days() as $dayKey => $dayLabel)
                                <th class="px-4 py-3 text-center font-medium text-gray-500 text-sm dark:text-gray-400">
                                    {{ $dayLabel }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $timeSlots = [];
                            foreach ($schedules as $schedule) {
                                $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                $end   = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
                                $timeSlots[$start . '-' . $end] = true;
                            }
                            ksort($timeSlots);

                            $days = [
                                'MONDAY',
                                'TUESDAY',
                                'WEDNESDAY',
                                'THURSDAY',
                                'FRIDAY',
                                'SATURDAY',
                                'SUNDAY',
                            ];
                        @endphp

                        @foreach(array_keys($timeSlots) as $timeSlot)
                            @php
                                [$startTime, $endTime] = explode('-', $timeSlot);
                            @endphp

                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    {{ $startTime }}
                                    <br>
                                    <span class="text-xs text-gray-400">{{ $endTime }}</span>
                                </td>

                                @foreach($days as $day)
                                    @php
                                        $schedule = $schedules
                                            ->where('day', $day)
                                            ->first(function ($item) use ($startTime, $endTime) {
                                                return \Carbon\Carbon::parse($item->start_time)->format('H:i') == $startTime
                                                    && \Carbon\Carbon::parse($item->end_time)->format('H:i') == $endTime;
                                            });
                                    @endphp

                                    <td class="px-2 py-3 text-center align-top">
                                        @if($schedule)
                                            <div class="rounded-xl p-3 bg-brand-50 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20 min-h-[80px] hover:shadow-md transition">
                                                <div class="flex justify-center mb-2">
                                                    <div class="w-8 h-8 rounded-lg bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <p class="font-bold text-brand-700 dark:text-brand-400 text-sm">
                                                    {{ $schedule->subject?->name ?? '-' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $schedule->xclass?->name ?? '-' }}
                                                </p>
                                            </div>
                                        @else
                                            <div class="h-[80px] flex items-center justify-center text-gray-300 dark:text-gray-700 text-sm">
                                                —
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else

            <div class="text-center py-12">
                <div class="mx-auto w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    📅
                </div>

                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
                    Belum Ada Jadwal Mengajar
                </h3>

                <p class="text-gray-500 dark:text-gray-400">
                    Jadwal mengajar Anda belum tersedia.
                </p>
            </div>

        @endif

    {{-- </x-common.component-card> --}}

@endsection