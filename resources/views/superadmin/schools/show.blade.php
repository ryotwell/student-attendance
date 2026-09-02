@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Sekolah" />

    <div class="mx-auto max-w-6xl">
        <div class="mb-4">
            <a href="{{ route('superadmin.schools.index') }}"
                class="inline-flex items-center text-sm text-brand-500 hover:text-brand-600">
                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Sekolah
            </a>
        </div>

        <!-- Detail Sekolah -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $school->name }}</h2>
                    <div class="mt-1 flex flex-wrap gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <span>NPSN: {{ $school->npsn ?? '-' }}</span>
                        <span>•</span>
                        <span>Level: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $school->level }}</span></span>
                        <span>•</span>
                        <span>
                            Status:
                            @if ($school->is_active)
                                <span class="inline-block rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-400">Aktif</span>
                            @else
                                <span class="inline-block rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-500/15 dark:text-red-400">Tidak Aktif</span>
                            @endif
                        </span>
                    </div>
                </div>
                <a href="{{ route('superadmin.schools.edit', $school) }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Edit Sekolah
                </a>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Alamat</p>
                    <p class="text-sm text-gray-800 dark:text-white/90">{{ $school->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Telepon</p>
                    <p class="text-sm text-gray-800 dark:text-white/90">{{ $school->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                    <p class="text-sm text-gray-800 dark:text-white/90">{{ $school->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Logo</p>
                    @if ($school->logo)
                        <img src="{{ $school->logo }}" alt="Logo" class="mt-1 h-16 w-16 rounded-lg object-cover">
                    @else
                        <p class="text-sm text-gray-400">-</p>
                    @endif
                </div>
            </div>

            <!-- Statistik ringkas -->
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/30">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalStudents }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Siswa</p>
                </div>
                <div class="rounded-lg bg-purple-50 p-3 dark:bg-purple-900/30">
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $totalTeachers }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Guru</p>
                </div>
                <div class="rounded-lg bg-pink-50 p-3 dark:bg-pink-900/30">
                    <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ $totalBk }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total BK</p>
                </div>
                <div class="rounded-lg bg-indigo-50 p-3 dark:bg-indigo-900/30">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalAdmins }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Admin</p>
                </div>
            </div>
        </div>

        <!-- Daftar User -->
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Pengguna Sekolah</h3>
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Total {{ $users->count() }} pengguna terdaftar</p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nama</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b border-gray-100 last:border-0 dark:border-gray-800">
                                <td class="px-3 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3 text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</td>
                                <td class="px-3 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium
                                        @if ($user->role === 'SUPERADMIN') bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-400
                                        @elseif ($user->role === 'ADMIN') bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400
                                        @elseif ($user->role === 'GURU') bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400
                                        @elseif ($user->role === 'GURU_BK') bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400
                                        @else bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400 @endif">
                                        {{ $user->role }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Belum ada pengguna terdaftar di sekolah ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection