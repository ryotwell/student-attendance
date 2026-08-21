@props(['counselingCase' => null, 'students' => [], 'categoryOptions' => [], 'selectedStudentId' => null])

<form method="POST" action="{{ $counselingCase ? route('bk.cases.update', $counselingCase) : route('bk.cases.store') }}">
    @csrf
    @if ($counselingCase)
        @method('PUT')
    @endif

    <x-common.component-card :title="$counselingCase ? 'Edit Catatan Kasus' : 'Tambah Catatan Kasus'">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="student_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Siswa<span class="text-error-500">*</span>
                </label>
                <select id="student_id" name="student_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <option value="">Pilih Siswa</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}"
                            @selected(old('student_id', $counselingCase->student_id ?? $selectedStudentId) == $student->id)>
                            {{ $student->name }} — {{ $student->xclass->name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Kategori<span class="text-error-500">*</span>
                </label>
                <select id="category" name="category"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <option value="">Pilih Kategori</option>
                    @foreach ($categoryOptions as $key => $option)
                        <option value="{{ $key }}" @selected(old('category', $counselingCase->category ?? '') === $key)>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Tanggal<span class="text-error-500">*</span>
            </label>
            <input type="date" id="date" name="date"
                value="{{ old('date', isset($counselingCase) ? $counselingCase->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('date')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Deskripsi Masalah<span class="text-error-500">*</span>
            </label>
            <textarea id="description" name="description" rows="4"
                placeholder="Jelaskan kronologi atau permasalahan yang terjadi..."
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('description', $counselingCase->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="action_taken" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Tindak Lanjut
            </label>
            <textarea id="action_taken" name="action_taken" rows="4"
                placeholder="Tindakan yang sudah/akan dilakukan..."
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('action_taken', $counselingCase->action_taken ?? '') }}</textarea>
            @error('action_taken')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                {{ $counselingCase ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('bk.cases.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </x-common.component-card>
</form>