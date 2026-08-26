@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Jadwal Pelajaran" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Daftar Jadwal Pelajaran">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $schedules->total() }} jadwal terdaftar
                </p>
                <a href="{{ route('schedules.create') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Tambah Jadwal
                </a>
            </div>

            <!-- Form Filter -->
            <form method="GET" action="{{ route('schedules.index') }}" class="flex flex-wrap items-end gap-4">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Mata pelajaran / kelas"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <!-- Filter Hari -->
                <div class="w-32">
                    <label for="day" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Hari</label>
                    <select id="day" name="day"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        @foreach ($days as $key => $label)
                            <option value="{{ $key }}" @selected(request('day') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kelas -->
                <div class="w-40">
                    <label for="xclass_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kelas</label>
                    <select id="xclass_id" name="xclass_id"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(request('xclass_id') == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Mata Pelajaran -->
                <div class="w-48">
                    <label for="subject_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Mata Pelajaran</label>
                    <select id="subject_id" name="subject_id"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Guru (khusus admin) -->
                <div class="w-48">
                    <label for="user_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Guru</label>
                    <select id="user_id" name="user_id"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                        Filter
                    </button>
                    <a href="{{ route('schedules.index') }}"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Hari</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jam</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Mata Pelajaran</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kelas</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tahun Ajaran</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Guru</p>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ \App\Helpers\MenuHelper::getDayName($schedule->day) }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $schedule->subject?->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $schedule->xclass?->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $schedule->xclass?->academicYear?->name ?? '-' }}
                                    @if($schedule->xclass?->academicYear?->is_active)
                                        <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500">Aktif</span>
                                    @else
                                        <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400">Tidak Aktif</span>
                                    @endif
                                </p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $schedule->user?->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('schedules.edit', $schedule) }}"
                                        class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('schedules.destroy', $schedule) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="font-medium text-error-500 hover:text-error-600 text-theme-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center sm:px-6">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    </x-common.component-card>
@endsection