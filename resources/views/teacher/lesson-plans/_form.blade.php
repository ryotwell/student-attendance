@props(['lessonPlan' => null])

<form method="POST"
    action="{{ $lessonPlan
        ? route('lesson-plans.update', $lessonPlan)
        : route('lesson-plans.store') }}">

    @csrf

    @if($lessonPlan)
        @method('PUT')
    @endif

    <x-common.component-card
        :title="$lessonPlan ? 'Edit Rencana Pembelajaran' : 'Tambah Rencana Pembelajaran'">

        {{-- Judul / Topik --}}
        <div>
            <label for="title"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Judul / Topik Pembelajaran
                <span class="text-error-500">*</span>
            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title', $lessonPlan->title ?? '') }}"
                placeholder="Contoh: Sistem Persamaan Linear Dua Variabel"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm"
                required>

            @error('title')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tanggal --}}
        <div>
            <label for="date"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Tanggal
                <span class="text-error-500">*</span>
            </label>

            <input
                type="date"
                id="date"
                name="date"
                value="{{ old('date', $lessonPlan?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm"
                required>

            @error('date')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jadwal Mengajar --}}
        <div>
            <label for="schedule_id"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Jadwal Mengajar
            </label>

            <select
                id="schedule_id"
                name="schedule_id"
                class="h-11 w-full rounded-lg border border-gray-300 px-4">

                <option value="">Tanpa Jadwal (Umum)</option>

                @foreach($schedules as $schedule)
                    <option
                        value="{{ $schedule->id }}"
                        @selected(
                            old('schedule_id', $lessonPlan->schedule_id ?? '')
                            ==
                            $schedule->id
                        )>
                        {{ $schedule->subject_name }} —
                        Kelas {{ $schedule->xclass?->name ?? '-' }} —
                        {{ \App\Helpers\MenuHelper::getDayName($schedule->day) }}
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}-
                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </option>
                @endforeach

            </select>

            @error('schedule_id')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Deskripsi / Materi
                <span class="text-error-500">*</span>
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="Tuliskan ringkasan materi, tujuan pembelajaran, dan langkah kegiatan..."
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm"
                required>{{ old('description', $lessonPlan->description ?? '') }}</textarea>

            @error('description')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Button --}}
        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white">
                {{ $lessonPlan ? 'Perbarui' : 'Simpan' }}
            </button>

            <a href="{{ route('lesson-plans.index') }}"
                class="rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300">
                Batal
            </a>
        </div>

    </x-common.component-card>

</form>
