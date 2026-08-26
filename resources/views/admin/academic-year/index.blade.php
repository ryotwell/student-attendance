@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tahun Ajaran" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Daftar Tahun Ajaran">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $academicYears->total() }} tahun ajaran terdaftar
                </p>
                <a href="{{ route('academic-years.create') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Tambah Tahun Ajaran
                </a>
            </div>

            <!-- Form Filter -->
            <form method="GET" action="{{ route('academic-years.index') }}" class="flex flex-wrap items-end gap-4">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Nama tahun ajaran"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <!-- Filter Semester -->
                <div class="w-36">
                    <label for="semester" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Semester</label>
                    <select id="semester" name="semester"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        <option value="GANJIL" @selected(request('semester') === 'GANJIL')>Ganjil</option>
                        <option value="GENAP" @selected(request('semester') === 'GENAP')>Genap</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="w-36">
                    <label for="is_active" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select id="is_active" name="is_active"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                        <option value="0" @selected(request('is_active') === '0')>Tidak Aktif</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                        Filter
                    </button>
                    <a href="{{ route('academic-years.index') }}"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nama</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Semester</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jumlah Kelas</p>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($academicYears as $academicYear)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $academicYear->name }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium {{ $academicYear->semester === 'GANJIL' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400' }}">
                                    {{ $academicYear->semester }}
                                </span>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if ($academicYear->is_active)
                                    <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500">Aktif</span>
                                @else
                                    <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $academicYear->xclasses_count }} kelas</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('academic-years.edit', $academicYear) }}"
                                        class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('academic-years.destroy', $academicYear) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus tahun ajaran {{ $academicYear->name }}?')">
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
                            <td colspan="5" class="px-5 py-10 text-center sm:px-6">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada tahun ajaran yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $academicYears->links() }}
        </div>
    </x-common.component-card>
@endsection