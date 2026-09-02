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
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
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
            const dayNames = @json(App\Helpers\MenuHelper::days());

            classSelect.addEventListener('change', function() {
                const classId = this.value;

                // Reset schedule select
                scheduleSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>';
                scheduleSelect.disabled = true;

                if (!classId) return;

                scheduleSelect.innerHTML = '<option value="">Memuat jadwal...</option>';

                fetch(`/attendance/schedules/${classId}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal memuat jadwal');
                        return response.json();
                    })
                    .then(data => {
                        scheduleSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>';

                        if (!data.length) {
                            scheduleSelect.innerHTML = '<option value="">Tidak ada jadwal untuk kelas ini</option>';
                            return;
                        }

                        data.forEach(schedule => {
                            const option = document.createElement('option');
                            option.value = schedule.id;
                            const dayName = dayNames[schedule.day] || schedule.day;
                            // Perubahan: pakai subject_name langsung
                            option.textContent = `${schedule.subject_name} - ${dayName} ${schedule.start_time} - ${schedule.end_time}`;
                            scheduleSelect.appendChild(option);
                        });

                        scheduleSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading schedules:', error);
                        scheduleSelect.innerHTML = '<option value="">Gagal memuat jadwal</option>';
                    });
            });
        });
    </script>
    @endpush
@endsection