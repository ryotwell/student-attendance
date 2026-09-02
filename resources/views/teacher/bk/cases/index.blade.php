@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Catatan Kasus Siswa" />

    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <div class="mx-auto max-w-6xl">

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    Catatan Kasus Siswa
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Seluruh catatan dari tim BK
                </p>
            </div>

            <a href="{{ route('bk.cases.create') }}"
                class="flex w-fit items-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-600">
                + Tambah Kasus
            </a>
        </div>


        {{-- Filter --}}
        <form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row">

            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau NIS siswa..."
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 sm:max-w-xs">


            <select name="category"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 sm:max-w-xs">

                <option value="">
                    Semua Kategori
                </option>

                @foreach ($categoryOptions as $key => $option)

                    <option value="{{ $key }}"
                        @selected(request('category') === $key)>
                        {{ $option['label'] }}
                    </option>

                @endforeach

            </select>


            <button type="submit"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Filter
            </button>

        </form>



        {{-- Table Responsive --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">

            <div class="overflow-x-auto">

                <table class="min-w-[900px] divide-y divide-gray-100 whitespace-nowrap dark:divide-gray-800">

                    <thead class="bg-gray-50 dark:bg-gray-800">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Tanggal
                            </th>


                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Siswa
                            </th>


                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Kategori
                            </th>


                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Dicatat Oleh
                            </th>


                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">


                        @forelse ($cases as $case)

                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/60">


                                <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $case->date->format('d/m/Y') }}
                                </td>



                                <td class="px-5 py-3">

                                    <div class="text-sm font-medium text-gray-800 dark:text-white">
                                        {{ $case->student->name }}
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $case->student->currentEnrollment?->xclass?->name ?? '-' }}
                                    </div>

                                </td>



                                <td class="px-5 py-3">

                                    <span class="rounded-lg px-2 py-1 text-xs font-bold {{ $case->category_badge_class }}">
                                        {{ $case->category_label }}
                                    </span>

                                </td>

                                <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $case->user->name }}
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-2">
                                        <x-ui.button-link class="duration-300" href="{{ route('bk.cases.by-student', $case->student) }}">
                                            Riwayat
                                        </x-ui.button-link>
                                        <x-ui.button-link class="bg-yellow-500 hover:bg-yellow-600 duration-300" href="{{ route('bk.cases.edit', $case) }}">
                                            Edit
                                        </x-ui.button-link>

                                        <form action="{{ route('bk.cases.destroy', $case) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus catatan kasus ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <x-ui.button class="bg-red-500 hover:bg-red-600 duration-300" type="submit">
                                                Hapus
                                            </x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty

                            <tr>

                                <td colspan="5"
                                    class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">

                                    Belum ada catatan kasus.

                                </td>

                            </tr>

                        @endforelse


                    </tbody>


                </table>


            </div>

        </div>



        {{-- Pagination --}}
        <div class="mt-4">
            {{ $cases->links() }}
        </div>


    </div>
@endsection