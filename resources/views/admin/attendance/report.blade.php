@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Laporan Absensi" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Filter Laporan Absensi">
        <form action="{{ route('attendance.report.show') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                    <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Siswa (Opsional)</label>
                    <select name="student_id" id="student_id"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors"
                        disabled>
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('attendance.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                    Kembali
                </a>
                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition w-full sm:w-auto">
                    Tampilkan Laporan
                </button>
            </div>
        </form>
    </x-common.component-card>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('class_id');
            const studentSelect = document.getElementById('student_id');

            classSelect.addEventListener('change', function() {
                const classId = this.value;

                // Reset student select
                studentSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
                studentSelect.disabled = !classId;

                if (!classId) return;

                // Fetch students via AJAX
                fetch(`/classes/${classId}/students`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = `${student.nis} - ${student.name}`;
                            studentSelect.appendChild(option);
                        });
                        studentSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading students:', error);
                    });
            });
        });
    </script>
    @endpush
@endsection