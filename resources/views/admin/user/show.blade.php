@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail User" />

    @if (session('success'))
        <div class="mb-6 max-w-2xl">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <div class="mx-auto max-w-2xl space-y-6">
        <x-common.component-card title="Informasi User">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                </div>

                <div>
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Email</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Role</p>
                    <div class="mt-1">
                        @php
                            $roleBadge = match ($user->role) {
                                'ADMIN' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                'GURU' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
                                'GURU_BK' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
                                default => 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400',
                            };
                        @endphp
                        <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium {{ $roleBadge }}">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Email Terverifikasi</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Belum diverifikasi' }}
                    </p>
                </div>

                <div>
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Terdaftar Sejak</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->created_at->format('d M Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('users.edit', $user) }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                    Edit User
                </a>
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Kembali
                </a>
            </div>
        </x-common.component-card>

        @if ($user->isWaliKelas())
            <x-common.component-card title="Kelas yang Diwalikan (Tahun Ajaran Aktif)">
                <div class="space-y-2">
                    @foreach ($user->currentClasses as $xclass)
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3 dark:border-gray-800">
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $xclass->name }}</p>
                        </div>
                    @endforeach
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection