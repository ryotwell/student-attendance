@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Verifikasi Dokumen Guru" />

<div class="mx-auto max-w-6xl">

    @if(session('success'))
        <div class="mb-6">
            <x-ui.alert variant="success" title="Berhasil" :message="session('success')" />
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Dokumen Guru
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Verifikasi dokumen pendukung Dapodik guru.
            </p>
        </div>

        <form method="GET">
            <select name="status"
                onchange="this.form.submit()"
                class="h-11 rounded-xl border border-gray-300 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                <option value="">Semua Status</option>
                <option value="PENDING" @selected(request('status') === 'PENDING')>
                    Pending
                </option>
                <option value="VERIFIED" @selected(request('status') === 'VERIFIED')>
                    Verified
                </option>
                <option value="REJECTED" @selected(request('status') === 'REJECTED')>
                    Rejected
                </option>

            </select>
        </form>
    </div>


    <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">

                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Guru
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Status
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Tanggal
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">

                    @forelse($documents as $document)

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">

                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-800 dark:text-white">
                                    {{ $document->user->name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $document->user->email }}
                                </div>
                            </td>


                            <td class="px-5 py-4">

                                @if($document->status === 'VERIFIED')

                                    <span class="rounded-lg bg-green-100 px-3 py-1 text-xs font-bold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        Verified
                                    </span>

                                @elseif($document->status === 'REJECTED')

                                    <span class="rounded-lg bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        Rejected
                                    </span>

                                @else

                                    <span class="rounded-lg bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        Pending
                                    </span>

                                @endif

                            </td>


                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $document->created_at->format('d M Y') }}
                            </td>


                            <td class="px-5 py-4 text-right">

                                <a href="{{ route('admin.teacher-documents.show', $document) }}"
                                    class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-600">

                                    Detail

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4"
                                class="px-5 py-12 text-center text-sm text-gray-500">
                                Belum ada dokumen guru.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>


    <div class="mt-5">
        {{ $documents->links() }}
    </div>

</div>
@endsection