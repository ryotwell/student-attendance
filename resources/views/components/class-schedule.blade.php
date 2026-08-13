@if($class)
<div class="overflow-x-auto">
    <table class="w-full min-w-[800px]">
        <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                <th class="px-4 py-3 text-left font-medium text-gray-500 text-sm dark:text-gray-400 w-24">Jam</th>
                @foreach(['MONDAY'=>'Senin','TUESDAY'=>'Selasa','WEDNESDAY'=>'Rabu','THURSDAY'=>'Kamis','FRIDAY'=>'Jumat','SATURDAY'=>'Sabtu','SUNDAY'=>'Minggu'] as $dayKey => $dayLabel)
                <th class="px-4 py-3 text-center font-medium text-gray-500 text-sm dark:text-gray-400">
                    {{ $dayLabel }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $timeSlots = [];
                if ($class && $class->schedules->count()) {
                    foreach ($class->schedules as $schedule) {
                        $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                        $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
                        $timeSlots[$start.'-'.$end] = true;
                    }
                }
                ksort($timeSlots);
            @endphp
            @forelse(array_keys($timeSlots) as $timeSlot)
                @php
                    [$startTime, $endTime] = explode('-', $timeSlot);
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-medium">
                        {{ $startTime }} - {{ $endTime }}
                    </td>
                    @foreach(['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'] as $day)
                        @php
                            $schedule = collect($schedulesByDay[$day] ?? [])->firstWhere(fn($s) =>
                                \Carbon\Carbon::parse($s->start_time)->format('H:i') === $startTime &&
                                \Carbon\Carbon::parse($s->end_time)->format('H:i') === $endTime
                            );
                        @endphp
                        <td class="px-2 py-3 text-center align-top">
                            @if($schedule)
                                <div class="bg-brand-50 dark:bg-brand-500/10 rounded-lg p-2 min-h-[60px]">
                                    <p class="font-medium text-brand-700 dark:text-brand-400 text-sm">{{ $schedule->subject?->name }}</p>
                                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </p>
                                </div>
                            @else
                                <div class="text-gray-300 dark:text-gray-700 text-theme-xs h-[60px] flex items-center justify-center">
                                    —
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada jadwal untuk kelas ini.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@else
<div class="text-center py-10">
    <p class="text-sm text-gray-500 dark:text-gray-400">Kelas tidak ditemukan.</p>
</div>
@endif