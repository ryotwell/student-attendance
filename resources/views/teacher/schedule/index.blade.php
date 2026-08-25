@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Jadwal Mengajar" />

<x-common.component-card>

    @if($schedules->count())

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[900px]">

                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900/50">
                        <th class="w-28 px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                            Jam
                        </th>

                        @foreach(\App\Helpers\MenuHelper::days() as $dayKey => $dayLabel)
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
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
                            $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
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
                                <span class="block text-xs text-gray-400">{{ $endTime }}</span>
                            </td>

                            @foreach($days as $day)

                                @php
                                    $schedule = $schedules
                                        ->where('day', $day)
                                        ->first(fn($item) =>
                                            \Carbon\Carbon::parse($item->start_time)->format('H:i') == $startTime &&
                                            \Carbon\Carbon::parse($item->end_time)->format('H:i') == $endTime
                                        );
                                @endphp

                                <td class="px-2 py-3 text-center align-top">

                                    @if($schedule)

                                        <div class="min-h-[80px] rounded-xl border border-brand-100 bg-brand-50 p-3 transition hover:shadow-md dark:border-brand-500/20 dark:bg-brand-500/10">

                                            <div class="mb-2 flex justify-center">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            <p class="text-sm font-bold text-brand-700 dark:text-brand-400">
                                                {{ $schedule->subject?->name ?? '-' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $schedule->xclass?->name ?? '-' }}
                                            </p>

                                        </div>

                                    @else

                                        <div class="flex h-[80px] items-center justify-center text-sm text-gray-300 dark:text-gray-700">
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

        <div class="py-12 text-center">

            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-900/30 dark:text-brand-400">

                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M3 10h18"/>
                    <path d="M8 3v4"/>
                    <path d="M16 3v4"/>
                </svg>

            </div>

            <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white">
                Belum Ada Jadwal Mengajar
            </h3>

            <p class="text-gray-500 dark:text-gray-400">
                Jadwal mengajar Anda belum tersedia.
            </p>

        </div>

    @endif

</x-common.component-card>

@endsection