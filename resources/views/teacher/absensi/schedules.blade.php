@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pilih Jadwal Mengajar" />

    <div class="max-w-6xl mx-auto">
        @if($schedules->isEmpty())
            <x-common.component-card>
                <div class="text-center py-12">
                    <div class="mx-auto w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M3 10h18"></path>
                            <path d="M8 3v4"></path>
                            <path d="M16 3v4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Belum Ada Jadwal</h3>
                    <p class="text-gray-500 dark:text-gray-400">Anda belum memiliki jadwal mengajar untuk saat ini.</p>
                </div>
            </x-common.component-card>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($schedules as $schedule)
                    @php
                        $dayLabels = [
                            'MONDAY' => 'Senin', 'TUESDAY' => 'Selasa', 'WEDNESDAY' => 'Rabu',
                            'THURSDAY' => 'Kamis', 'FRIDAY' => 'Jumat', 'SATURDAY' => 'Sabtu', 'SUNDAY' => 'Minggu',
                        ];
                        $dayColors = [
                            'MONDAY' => 'blue', 'TUESDAY' => 'green', 'WEDNESDAY' => 'yellow',
                            'THURSDAY' => 'orange', 'FRIDAY' => 'red', 'SATURDAY' => 'purple', 'SUNDAY' => 'pink',
                        ];
                        $day = $schedule->day;
                        $color = $dayColors[$day] ?? 'gray';
                    @endphp
                    <a
                        href="{{ route('attendance.show', ['schedule_id' => $schedule->id, 'class_id' => $schedule->xclass_id, 'date' => now()->format('Y-m-d')]) }}"
                        class="group block rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 transition-all duration-200 hover:-translate-y-1 hover:border-{{$color}}-400 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-{{$color}}-500"
                    >
                        {{-- ICON & DAY --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center justify-center w-14 h-14 bg-{{$color}}-100 dark:bg-{{$color}}-900/30 rounded-xl text-{{$color}}-600 dark:text-{{$color}}-400 group-hover:bg-{{$color}}-200 dark:group-hover:bg-{{$color}}-900/50 transition-colors">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                    <path d="M8 3v4"></path>
                                    <path d="M16 3v4"></path>
                                </svg>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold text-{{$color}}-700 dark:text-{{$color}}-300 bg-{{$color}}-100 dark:bg-{{$color}}-900/30 rounded-full">
                                {{ $dayLabels[$day] }}
                            </span>
                        </div>

                        {{-- SUBJECT --}}
                        <h4 class="font-bold text-lg text-gray-800 dark:text-white mb-1 group-hover:text-{{$color}}-600 dark:group-hover:text-{{$color}}-400 transition-colors">
                            {{ $schedule->subject?->name ?? 'Mata Pelajaran' }}
                        </h4>

                        {{-- CLASS --}}
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $schedule->xclass?->name ?? 'Kelas' }}</span>
                        </p>

                        {{-- TIME --}}
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 6v6l4 2"></path>
                            </svg>
                            <span>
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </span>
                        </div>

                        {{-- ACTION BUTTON --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-{{$color}}-50 dark:bg-{{$color}}-900/30 text-{{$color}}-600 dark:text-{{$color}}-400 font-semibold text-sm group-hover:bg-{{$color}}-100 dark:group-hover:bg-{{$color}}-900/50 transition-colors">
                                Mulai Absensi
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"></path>
                                    <path d="M13 6l6 6-6 6"></path>
                                </svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection