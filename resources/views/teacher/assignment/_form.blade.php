@props(['assignment' => null])

<form
    method="POST"
    action="{{ $assignment ? route('assignments.update', $assignment) : route('assignments.store') }}"
    enctype="multipart/form-data">

    @csrf

    @if ($assignment)
        @method('PUT')
    @endif

    <x-common.component-card
        :title="$assignment ? 'Edit Tugas' : 'Tambah Tugas'">

        {{-- Judul --}}
        <div>
            <label
                for="title"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                Judul Tugas<span class="text-error-500">*</span>

            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title', $assignment->title ?? '') }}"
                placeholder="cth: Tugas Matematika Bab Pecahan"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />

            @error('title')
                <p class="mt-1.5 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label
                for="description"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                Deskripsi / Instruksi<span class="text-error-500">*</span>

            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="Tuliskan instruksi atau penjelasan tugas..."
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            >{{ old('description', $assignment->description ?? '') }}</textarea>

            @error('description')
                <p class="mt-1.5 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Kelas + Materi --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- Kelas --}}
            <div>

                <label
                    for="xclass_id"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                    Kelas<span class="text-error-500">*</span>

                </label>

                <select
                    id="xclass_id"
                    name="xclass_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                    <option value="">
                        Pilih Kelas
                    </option>

                    @foreach ($classes as $class)

                        <option
                            value="{{ $class->id }}"
                            @selected(old('xclass_id', $assignment->xclass_id ?? '') == $class->id)>

                            {{ $class->name }}

                            @if ($class->academicYear)
                                - {{ $class->academicYear->name }}
                            @endif

                        </option>

                    @endforeach

                </select>

                @error('xclass_id')
                    <p class="mt-1.5 text-sm text-error-500">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Rencana Pembelajaran --}}
            <div>

                <label
                    for="lesson_plan_id"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                    Rencana Pembelajaran

                </label>

                <select
                    id="lesson_plan_id"
                    name="lesson_plan_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                    <option value="">
                        Tanpa rencana pembelajaran
                    </option>

                    @foreach ($lessonPlans as $lessonPlan)

                        <option
                            value="{{ $lessonPlan->id }}"
                            @selected(old('lesson_plan_id', $assignment->lesson_plan_id ?? '') == $lessonPlan->id)>

                            {{ $lessonPlan->title }}
                            - {{ $lessonPlan->date->format('d/m/Y') }}

                        </option>

                    @endforeach

                </select>

                @error('lesson_plan_id')
                    <p class="mt-1.5 text-sm text-error-500">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

        {{-- Deadline + Status --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- Deadline --}}
            {{-- <div>

                <label
                    for="due_date"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                    Batas Pengumpulan

                </label>

                <input
                    type="datetime-local"
                    id="due_date"
                    name="due_date"
                    value="{{ old('due_date', $assignment?->due_date?->format('Y-m-d\TH:i')) }}"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                />

                @error('due_date')
                    <p class="mt-1.5 text-sm text-error-500">
                        {{ $message }}
                    </p>
                @enderror

            </div> --}}

            {{-- Status --}}
            <div>

                <label
                    for="status"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                    Status<span class="text-error-500">*</span>

                </label>

                <select
                    id="status"
                    name="status"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                    <option value="DRAFT"
                        @selected(old('status', $assignment->status ?? 'DRAFT') === 'DRAFT')>
                        Draft
                    </option>

                    <option value="PUBLISHED"
                        @selected(old('status', $assignment->status ?? '') === 'PUBLISHED')>
                        Dipublikasikan
                    </option>

                    <option value="CLOSED"
                        @selected(old('status', $assignment->status ?? '') === 'CLOSED')>
                        Ditutup
                    </option>

                </select>

                @error('status')
                    <p class="mt-1.5 text-sm text-error-500">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

        {{-- Lampiran --}}
        <div>

            <label
                for="attachment"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                Lampiran Tugas

            </label>

            <input
                type="file"
                id="attachment"
                name="attachment"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 file:mr-4 file:rounded-md file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:file:bg-brand-500/10 dark:file:text-brand-400"
            />

            @if ($assignment?->attachment)

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    File saat ini:
                    <span class="font-medium text-gray-700 dark:text-gray-300">
                        {{ basename($assignment->attachment) }}
                    </span>
                </p>

            @endif

            @error('attachment')
                <p class="mt-1.5 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror

        </div>

        {{-- Button --}}
        <div class="flex items-center gap-4">

            <button
                type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">

                {{ $assignment ? 'Perbarui' : 'Simpan' }}

            </button>

            <a
                href="{{ route('assignments.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">

                Batal

            </a>

        </div>

    </x-common.component-card>

</form>