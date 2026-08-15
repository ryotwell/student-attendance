@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Absensi: {{ $class->name }} - {{ $schedule->subject->name }} - {{ $date->format('d/m/Y') }}">
        <form action="{{ route('attendance.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">NIS</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nama Siswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($class->students as $index => $student)
                            @php
                                $existing = $existingAttendances->get($student->id);
                                $currentStatus = $existing->status ?? 'ALPHA';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $student->nis }}</td>
                                <td class="px-4 py-3 font-medium">{{ $student->name }}</td>
                                <td class="px-4 py-3">
                                    <select name="attendances[{{ $index }}][student_id]" class="hidden">
                                        <option value="{{ $student->id }}" selected></option>
                                    </select>
                                    <select name="attendances[{{ $index }}][status]"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors"
                                        @class([
                                            'bg-green-50 dark:bg-green-900/20 border-green-300' => $currentStatus === 'HADIR',
                                            'bg-blue-50 dark:bg-blue-900/20 border-blue-300' => $currentStatus === 'IZIN',
                                            'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300' => $currentStatus === 'SAKIT',
                                            'bg-red-50 dark:bg-red-900/20 border-red-300' => $currentStatus === 'ALPHA',
                                        ])
                                    >
                                        <option value="HADIR" {{ $currentStatus === 'HADIR' ? 'selected' : '' }}>HADIR</option>
                                        <option value="IZIN" {{ $currentStatus === 'IZIN' ? 'selected' : '' }}>IZIN</option>
                                        <option value="SAKIT" {{ $currentStatus === 'SAKIT' ? 'selected' : '' }}>SAKIT</option>
                                        <option value="ALPHA" {{ $currentStatus === 'ALPHA' ? 'selected' : '' }}>ALPHA</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada siswa di kelas ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('attendance.index') }}" class="mr-3 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Kembali
                </a>
                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Simpan Absensi
                </button>
            </div>
        </form>
    </x-common.component-card>
@endsection