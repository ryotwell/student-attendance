@props(['schedule' => null, 'users' => []])

<form method="POST" action="{{ $schedule ? route('schedules.update', $schedule) : route('schedules.store') }}">
    @csrf
    @if ($schedule)
        @method('PUT')
    @endif

    <x-common.component-card :title="$schedule ? 'Edit Jadwal' : 'Tambah Jadwal'">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="day" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Hari<span class="text-error-500">*</span>
                </label>
                <select id="day" name="day"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <option value="">Pilih Hari</option>
                    @foreach ($days as $value => $label)
                        <option value="{{ $value }}" @selected(old('day', $schedule->day ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('day')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="user_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Guru<span class="text-error-500">*</span>
                </label>

                <select id="user_id" name="user_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">

                    <option value="">Pilih Guru</option>

                    {{-- @foreach (\App\Models\User::get() as $teacher) --}}
                    @foreach ($users as $teacher)
                        <option value="{{ $teacher->id }}"
                            @selected(old('user_id', $schedule->user_id ?? '') == $teacher->id)>
                            {{ $teacher->name }}
                        </option>
                    @endforeach

                </select>

                @error('user_id')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="start_time" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Jam Mulai<span class="text-error-500">*</span>
                </label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', optional($schedule->start_time ?? null)?->format('H:i')) }}"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('start_time')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_time" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Jam Selesai<span class="text-error-500">*</span>
                </label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', optional($schedule->end_time ?? null)?->format('H:i')) }}"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('end_time')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Mata Pelajaran<span class="text-error-500">*</span>
                </label>
                <select id="subject_id" name="subject_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id', $schedule->subject_id ?? '') == $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="xclass_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Kelas<span class="text-error-500">*</span>
                </label>
                <select id="xclass_id" name="xclass_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('xclass_id', $schedule->xclass_id ?? request()->class ?? '') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('xclass_id')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                {{ $schedule ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('schedules.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </x-common.component-card>
</form>
