@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Absensi Siswa" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Pilih Kelas, Jadwal, dan Tanggal">
        <form action="{{ route('attendance.show') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas <span class="text-error-500">*</span></label>
                    <select name="class_id" id="class_id" required
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors"
                        wire:model.live="classId">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear?->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="schedule_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jadwal Pelajaran <span class="text-error-500">*</span></label>
                    <select name="schedule_id" id="schedule_id" required
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors"
                        disabled>
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal <span class="text-error-500">*</span></label>
                    <input type="date" name="date" id="date" required value="{{ now()->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Tampilkan Absensi
                </button>
            </div>
        </form>
    </x-common.component-card>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('class_id');
            const scheduleSelect = document.getElementById('schedule_id');

            classSelect.addEventListener('change', function() {
                const classId = this.value;

                // Reset schedule select
                scheduleSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>';
                scheduleSelect.disabled = !classId;

                if (!classId) return;

                // Fetch schedules via AJAX
                fetch(`/attendance/schedules/${classId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(schedule => {
                            const option = document.createElement('option');
                            option.value = schedule.id;
                            const dayName = getDayName(schedule.day);
                            option.textContent = `${schedule.subject.name} - ${dayName} ${schedule.start_time} - ${schedule.end_time}`;
                            scheduleSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading schedules:', error);
                    });
            });
        });

        function getDayName(day) {
            const days = {
                'MONDAY': 'Senin',
                'TUESDAY': 'Selasa',
                'WEDNESDAY': 'Rabu',
                'THURSDAY': 'Kamis',
                'FRIDAY': 'Jumat',
                'SATURDAY': 'Sabtu',
                'SUNDAY': 'Minggu',
            };
            return days[day] || day;
        }
    </script>
    @endpush
@endsection