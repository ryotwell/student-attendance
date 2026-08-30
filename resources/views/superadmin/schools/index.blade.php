@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Manajemen Sekolah" />

    <div class="mx-auto max-w-6xl">
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Sekolah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $schools->total() }} sekolah</p>
                </div>
                {{-- <a href="{{ route('superadmin.schools.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Tambah Sekolah
                </a> --}}
            </div>

            <!-- Filter -->
            <form method="GET" action="{{ route('superadmin.schools.index') }}" class="mt-4 flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Nama, NPSN, Alamat"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <div class="w-32">
                    <label for="level" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Level</label>
                    <select id="level" name="level"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Semua</option>
                        <option value="SD" @selected(request('level') === 'SD')>SD</option>
                        <option value="SMP" @selected(request('level') === 'SMP')>SMP</option>
                        <option value="SMA" @selected(request('level') === 'SMA')>SMA</option>
                    </select>
                </div>

                <div class="w-32">
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
                        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        Filter
                    </button>
                    <a href="{{ route('superadmin.schools.index') }}"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        Reset
                    </a>
                </div>
            </form>

            <!-- Tabel -->
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nama</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">NPSN</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Level</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Paket</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + $schools->firstItem() - 1 }}
                                </td>
                                <td class="px-3 py-4">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $school->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $school->address ?? '-' }}</p>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $school->npsn ?? '-' }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">
                                        {{ $school->level }}
                                    </span>
                                </td>
                                <td class="px-3 py-4">
                                    @if ($school->package === 'premium')
                                        <span class="inline-flex items-center rounded-full bg-gradient-to-r from-yellow-400 to-yellow-500 px-2.5 py-0.5 text-xs font-medium text-white shadow-sm">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Premium
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Basic
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-4">
                                    @if ($school->is_active)
                                        <span class="inline-block rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">Aktif</span>
                                    @else
                                        <span class="inline-block rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-500/15 dark:text-red-400">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('superadmin.schools.show', $school) }}"
                                            class="text-xs font-medium text-blue-500 hover:text-blue-600">Detail</a>
                                        <a href="{{ route('superadmin.schools.edit', $school) }}"
                                        class="text-xs font-medium text-brand-500 hover:text-brand-600">Edit</a>
                                        {{-- <form action="{{ route('superadmin.schools.toggle-active', $school) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-xs font-medium {{ $school->is_active ? 'text-red-500 hover:text-red-600' : 'text-green-500 hover:text-green-600' }}">
                                                {{ $school->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada sekolah yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $schools->links() }}
            </div>
        </div>
    </div>
@endsection