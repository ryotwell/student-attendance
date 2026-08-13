@props(['academicYear' => null])

<form method="POST" action="{{ $academicYear ? route('academic-years.update', $academicYear) : route('academic-years.store') }}">
    @csrf
    @if ($academicYear)
        @method('PUT')
    @endif

    <x-common.component-card :title="$academicYear ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Nama Tahun Ajaran<span class="text-error-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name', $academicYear->name ?? '') }}"
                placeholder="cth: Tahun Ajaran 2026/2027"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('name')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="semester" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Semester<span class="text-error-500">*</span>
            </label>
            <select id="semester" name="semester"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                <option value="">Pilih Semester</option>
                <option value="GANJIL" @selected(old('semester', $academicYear->semester ?? '') === 'GANJIL')>Ganjil</option>
                <option value="GENAP" @selected(old('semester', $academicYear->semester ?? '') === 'GENAP')>Genap</option>
            </select>
            @error('semester')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="is_active" class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $academicYear->is_active ?? false)) class="size-4 rounded border-gray-300 text-brand-500 dark:border-gray-700 dark:bg-gray-900" />
                Set sebagai tahun ajaran aktif
            </label>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                {{ $academicYear ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('academic-years.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </x-common.component-card>
</form>
