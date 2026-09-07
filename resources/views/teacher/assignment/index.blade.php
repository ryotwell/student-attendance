@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Tugas Siswa" />

<div class="mx-auto max-w-6xl">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Tugas Siswa
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola tugas yang diberikan kepada siswa.
            </p>
        </div>

        <a
            href="{{ route('assignments.create') }}"
            class="
            bg-brand-500
            shadow-theme-xs
            hover:bg-brand-600
            inline-flex items-center justify-center
            rounded-xl
            px-4 py-2.5
            text-sm font-medium
            text-white
            transition
            "
        >
            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="mr-2"
            >
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>

            Tambah Tugas
        </a>

    </div>


    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-6">
            <x-ui.alert
                variant="success"
                title="Berhasil"
                :message="session('success')"
            />
        </div>
    @endif


    {{-- Filter --}}
    <div
        class="
        mb-6
        rounded-2xl
        border border-gray-200
        bg-white
        p-5
        shadow-sm
        dark:border-gray-700
        dark:bg-gray-900
        "
    >

        <form
            method="GET"
            action="{{ route('assignments.index') }}"
            class="flex flex-wrap items-end gap-4"
        >

            {{-- Search --}}
            <div class="min-w-[220px] flex-1">

                <label
                    for="search"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Cari Tugas
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul tugas..."
                    class="
                    dark:bg-dark-900
                    shadow-theme-xs
                    focus:border-brand-300
                    focus:ring-brand-500/10
                    dark:focus:border-brand-800
                    h-11 w-full
                    rounded-lg
                    border border-gray-300
                    bg-transparent
                    px-4 py-2.5
                    text-sm
                    text-gray-800
                    placeholder:text-gray-400
                    focus:ring-3
                    focus:outline-hidden
                    dark:border-gray-700
                    dark:bg-gray-900
                    dark:text-white/90
                    dark:placeholder:text-white/30
                    "
                />

            </div>


            {{-- Kelas --}}
            <div class="w-44">

                <label
                    for="xclass_id"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Kelas
                </label>

                <select
                    id="xclass_id"
                    name="xclass_id"
                    class="
                    dark:bg-dark-900
                    shadow-theme-xs
                    focus:border-brand-300
                    focus:ring-brand-500/10
                    dark:focus:border-brand-800
                    h-11 w-full
                    rounded-lg
                    border border-gray-300
                    bg-transparent
                    px-4 py-2.5
                    text-sm
                    text-gray-800
                    focus:ring-3
                    focus:outline-hidden
                    dark:border-gray-700
                    dark:bg-gray-900
                    dark:text-white/90
                    "
                >

                    <option value="">Semua Kelas</option>

                    @foreach ($classes as $class)

                        <option
                            value="{{ $class->id }}"
                            @selected(request('xclass_id') == $class->id)
                        >
                            {{ $class->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Status --}}
            <div class="w-40">

                <label
                    for="status"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="
                    dark:bg-dark-900
                    shadow-theme-xs
                    focus:border-brand-300
                    focus:ring-brand-500/10
                    dark:focus:border-brand-800
                    h-11 w-full
                    rounded-lg
                    border border-gray-300
                    bg-transparent
                    px-4 py-2.5
                    text-sm
                    text-gray-800
                    focus:ring-3
                    focus:outline-hidden
                    dark:border-gray-700
                    dark:bg-gray-900
                    dark:text-white/90
                    "
                >

                    <option value="">Semua Status</option>

                    <option
                        value="DRAFT"
                        @selected(request('status') === 'DRAFT')
                    >
                        Draft
                    </option>

                    <option
                        value="PUBLISHED"
                        @selected(request('status') === 'PUBLISHED')
                    >
                        Dipublikasikan
                    </option>

                    <option
                        value="CLOSED"
                        @selected(request('status') === 'CLOSED')
                    >
                        Ditutup
                    </option>

                </select>

            </div>


            {{-- Button --}}
            <div class="flex items-center gap-2">

                <button
                    type="submit"
                    class="
                    bg-brand-500
                    shadow-theme-xs
                    hover:bg-brand-600
                    inline-flex items-center justify-center
                    rounded-lg
                    px-4 py-2.5
                    text-sm font-medium
                    text-white
                    transition
                    "
                >
                    Filter
                </button>

                <a
                    href="{{ route('assignments.index') }}"
                    class="
                    inline-flex items-center justify-center
                    rounded-lg
                    px-4 py-2.5
                    text-sm font-medium
                    text-gray-700
                    ring-1 ring-gray-300
                    hover:bg-gray-50
                    dark:text-gray-400
                    dark:ring-gray-700
                    dark:hover:bg-white/[0.03]
                    "
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- Jumlah Tugas --}}
    <div class="mb-5">

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Menampilkan
            <span class="font-semibold text-gray-800 dark:text-white">
                {{ $assignments->total() }}
            </span>
            tugas
        </p>

    </div>


    {{-- Assignment Cards --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

        @forelse ($assignments as $assignment)

            @php
                $xclass = $assignment->xclass;
            @endphp


            {{-- Card --}}
            <div
                class="
                group relative overflow-hidden
                rounded-2xl
                border border-gray-200
                bg-white
                p-5
                shadow-sm
                transition-all duration-300
                hover:-translate-y-1
                hover:border-brand-300
                hover:shadow-xl
                dark:border-gray-700
                dark:bg-gray-900
                dark:hover:border-brand-500
                "
            >

                {{-- Border kiri --}}
                <span
                    class="
                    absolute left-0 top-0
                    h-full w-1
                    bg-brand-500
                    transition-all
                    group-hover:w-2
                    "
                ></span>


                {{-- Header --}}
                <div class="mb-5 flex items-start gap-4">

                    <div
                        class="
                        flex h-14 w-14 shrink-0 items-center justify-center
                        rounded-xl
                        bg-brand-50
                        text-brand-600
                        transition
                        group-hover:scale-110
                        dark:bg-brand-900/30
                        dark:text-brand-400
                        "
                    >

                        <svg
                            width="28"
                            height="28"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M8 13h8"/>
                            <path d="M8 17h5"/>
                        </svg>

                    </div>


                    <div class="min-w-0 flex-1">

                        <h3
                            class="
                            line-clamp-2
                            text-lg font-bold
                            text-gray-800
                            transition
                            group-hover:text-brand-600
                            dark:text-white
                            dark:group-hover:text-brand-400
                            "
                        >
                            {{ $assignment->title }}
                        </h3>

                        @if ($xclass)

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $xclass->name }}
                            </p>

                        @else

                            <p class="mt-1 text-sm text-error-500">
                                Kelas tidak diketahui
                            </p>

                        @endif

                    </div>

                </div>


                {{-- Deskripsi --}}
                <div class="mb-4">

                    <p
                        class="
                        line-clamp-3
                        text-sm
                        leading-6
                        text-gray-500
                        dark:text-gray-400
                        "
                    >
                        {{ $assignment->description }}
                    </p>

                </div>


                {{-- Rencana Pembelajaran --}}
                @if ($assignment->lessonPlan)

                    <div
                        class="
                        mb-3
                        flex items-center gap-2
                        rounded-xl
                        bg-gray-50
                        px-4 py-3
                        dark:bg-white/[0.03]
                        "
                    >

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="shrink-0 text-gray-500 dark:text-gray-400"
                        >
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>

                        <div class="min-w-0">

                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Materi Pembelajaran
                            </p>

                            <p class="truncate text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $assignment->lessonPlan->title }}
                            </p>

                        </div>

                    </div>

                @endif


                {{-- Deadline --}}
                <div
                    class="
                    mb-4
                    flex items-center gap-2
                    rounded-xl
                    bg-brand-50
                    px-4 py-3
                    text-sm
                    font-medium
                    text-brand-600
                    dark:bg-brand-900/30
                    dark:text-brand-400
                    "
                >

                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <line x1="8" y1="3" x2="8" y2="7"/>
                        <line x1="16" y1="3" x2="16" y2="7"/>
                    </svg>

                    @if ($assignment->due_date)

                        <span>
                            {{ $assignment->due_date->translatedFormat('d F Y') }}
                            ·
                            {{ $assignment->due_date->format('H:i') }}
                        </span>

                    @else

                        <span>
                            Tidak ada deadline
                        </span>

                    @endif

                </div>


                {{-- Footer --}}
                <div class="flex items-center justify-between gap-3">

                    {{-- Status --}}
                    <div>

                        @if ($assignment->status === 'DRAFT')

                            <span
                                class="
                                text-theme-xs
                                inline-block
                                rounded-full
                                bg-gray-100
                                px-2.5 py-1
                                font-medium
                                text-gray-700
                                dark:bg-gray-700
                                dark:text-gray-300
                                "
                            >
                                Draft
                            </span>

                        @elseif ($assignment->status === 'PUBLISHED')

                            <span
                                class="
                                text-theme-xs
                                inline-block
                                rounded-full
                                bg-green-50
                                px-2.5 py-1
                                font-medium
                                text-green-700
                                dark:bg-green-500/15
                                dark:text-green-400
                                "
                            >
                                Dipublikasikan
                            </span>

                        @elseif ($assignment->status === 'CLOSED')

                            <span
                                class="
                                text-theme-xs
                                inline-block
                                rounded-full
                                bg-red-50
                                px-2.5 py-1
                                font-medium
                                text-red-700
                                dark:bg-red-500/15
                                dark:text-red-400
                                "
                            >
                                Ditutup
                            </span>

                        @endif

                    </div>


                    {{-- Attachment --}}
                    @if ($assignment->attachment)

                        <span
                            class="
                            inline-flex items-center gap-1
                            text-xs
                            font-medium
                            text-brand-500
                            dark:text-brand-400
                            "
                        >

                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                            </svg>

                            Lampiran

                        </span>

                    @endif

                </div>


                {{-- Action --}}
                <div
                    class="
                    mt-4
                    flex items-center gap-2
                    border-t border-gray-100
                    pt-4
                    dark:border-gray-800
                    "
                >

                    <a
                        href="{{ route('assignments.edit', $assignment) }}"
                        class="
                        flex-1
                        rounded-xl
                        bg-brand-500
                        px-4 py-2.5
                        text-center
                        text-sm font-semibold
                        text-white
                        transition-all duration-300
                        hover:bg-brand-600
                        hover:shadow-md
                        "
                    >
                        Edit Tugas
                    </a>


                    <form
                        method="POST"
                        action="{{ route('assignments.destroy', $assignment) }}"
                        onsubmit="return confirm('Yakin ingin menghapus tugas {{ $assignment->title }}?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="
                            inline-flex
                            h-10
                            items-center justify-center
                            rounded-xl
                            border border-gray-200
                            px-3
                            text-gray-500
                            transition
                            hover:border-red-200
                            hover:bg-red-50
                            hover:text-red-500
                            dark:border-gray-700
                            dark:hover:border-red-500/30
                            dark:hover:bg-red-500/10
                            "
                            title="Hapus tugas"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v5"/>
                                <path d="M14 11v5"/>
                            </svg>

                        </button>

                    </form>

                </div>

            </div>

        @empty

            {{-- Empty State --}}
            <div
                class="
                col-span-full
                rounded-2xl
                border border-dashed
                border-gray-200
                bg-gray-50
                py-12
                text-center
                dark:border-gray-700
                dark:bg-gray-900/50
                "
            >

                <div
                    class="
                    mx-auto mb-4
                    flex h-14 w-14
                    items-center justify-center
                    rounded-xl
                    bg-brand-50
                    text-brand-500
                    dark:bg-brand-900/30
                    dark:text-brand-400
                    "
                >

                    <svg
                        width="28"
                        height="28"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M8 13h8"/>
                        <path d="M8 17h5"/>
                    </svg>

                </div>

                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Belum ada tugas
                </p>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Silakan tambahkan tugas untuk siswa.
                </p>

                <a
                    href="{{ route('assignments.create') }}"
                    class="mt-4 inline-flex items-center rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
                >
                    Tambah Tugas
                </a>

            </div>

        @endforelse

    </div>


    {{-- Pagination --}}
    @if ($assignments->hasPages())

        <div class="mt-6">
            {{ $assignments->links() }}
        </div>

    @endif

</div>

@endsection