@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Daftar Siswa" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Daftar Siswa">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $students->total() }} siswa terdaftar
                </p>
                <a href="{{ route('students.create') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Tambah Siswa
                </a>
            </div>

            <!-- Form Filter -->
            <form method="GET" action="{{ route('students.index') }}" class="flex flex-wrap items-end gap-4">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Nama, NIS, NISN"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <!-- Gender -->
                <div class="w-32">
                    <label for="gender" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">L/P</label>
                    <select id="gender" name="gender"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        <option value="MALE" @selected(request('gender') === 'MALE')>Laki-laki</option>
                        <option value="FEMALE" @selected(request('gender') === 'FEMALE')>Perempuan</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="w-36">
                    <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select id="status" name="status"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        <option value="AKTIF" @selected(request('status') === 'AKTIF')>Aktif</option>
                        <option value="LULUS" @selected(request('status') === 'LULUS')>Lulus</option>
                        <option value="PINDAH" @selected(request('status') === 'PINDAH')>Pindah</option>
                        <option value="KELUAR" @selected(request('status') === 'KELUAR')>Keluar</option>
                    </select>
                </div>

                <!-- Kelas -->
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

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                        Filter
                    </button>
                    <a href="{{ route('students.index') }}"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-3 py-3 text-center sm:px-4">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">#</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nama</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">NIS</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">NISN</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">L/P</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kelas</p>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-3 py-4 text-center sm:px-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + $students->firstItem() - 1 }}
                                </span>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $student->name }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $student->nis }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $student->nisn }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if ($student->gender === 'MALE')
                                    <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">Laki-laki</span>
                                @else
                                    <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-pink-50 text-pink-700 dark:bg-pink-500/15 dark:text-pink-400">Perempuan</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $student->currentEnrollment?->xclass?->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('students.edit', $student) }}"
                                        class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('students.destroy', $student) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus siswa {{ $student->name }}?')">
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
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada siswa yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </x-common.component-card>
@endsection