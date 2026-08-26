@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Daftar Pengumuman" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <x-common.component-card title="Daftar Pengumuman">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $announcements->total() }} pengumuman terdaftar
                </p>
                <a href="{{ route('announcements.create') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    Tambah Pengumuman
                </a>
            </div>

            <!-- Form Filter -->
            <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-wrap items-end gap-4">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Judul atau konten"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <!-- Filter Status -->
                <div class="w-36">
                    <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select id="status" name="status"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        <option value="PUBLISHED" @selected(request('status') === 'PUBLISHED')>Published</option>
                        <option value="DRAFT" @selected(request('status') === 'DRAFT')>Draft</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                        Filter
                    </button>
                    <a href="{{ route('announcements.index') }}"
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
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Judul</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal</p>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($announcements as $announcement)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $announcement->title }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $announcement->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if ($announcement->status === 'PUBLISHED')
                                    <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400">Published</span>
                                @else
                                    <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400">Draft</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('announcements.show', $announcement) }}"
                                        class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm">
                                        Lihat
                                    </a>
                                    <a href="{{ route('announcements.edit', $announcement) }}"
                                        class="font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 text-theme-sm">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('announcements.destroy', $announcement) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus pengumuman {{ $announcement->title }}?')">
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
                            <td colspan="4" class="px-5 py-10 text-center sm:px-6">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada pengumuman yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    </x-common.component-card>
@endsection