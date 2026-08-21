@props(['student' => null])

<form method="POST" action="{{ $student ? route('students.update', $student) : route('students.store') }}">
    @csrf
    @if ($student)
        @method('PUT')
    @endif

    <x-common.component-card :title="$student ? 'Edit Siswa' : 'Tambah Siswa'">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Nama Lengkap<span class="text-error-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name', $student->name ?? '') }}"
                placeholder="cth: Ahmad Fauzi"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('name')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="nis" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    NIS<span class="text-error-500">*</span>
                </label>
                <input type="text" id="nis" name="nis" value="{{ old('nis', $student->nis ?? '') }}"
                    placeholder="cth: 123456"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('nis')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nisn" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    NISN<span class="text-error-500">*</span>
                </label>
                <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $student->nisn ?? '') }}"
                    placeholder="cth: 1234567890"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('nisn')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="gender" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Jenis Kelamin<span class="text-error-500">*</span>
                </label>
                <select id="gender" name="gender"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="MALE" @selected(old('gender', $student->gender ?? '') === 'MALE')>Laki-laki</option>
                    <option value="FEMALE" @selected(old('gender', $student->gender ?? '') === 'FEMALE')>Perempuan</option>
                </select>
                @error('gender')
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
                        <option value="{{ $class->id }}" @selected(old('xclass_id', $student->xclass_id ?? '') == $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('xclass_id')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="parent_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nama Orang Tua/Wali
                </label>
                <input type="text" id="parent_name" name="parent_name" value="{{ old('parent_name', $student->parent_name ?? '') }}"
                    placeholder="cth: Budi Santoso"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('parent_name')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="parent_phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    No. HP Orang Tua/Wali
                </label>
                <input type="text" id="parent_phone" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone ?? '') }}"
                    placeholder="cth: 081234567890"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('parent_phone')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                {{ $student ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('students.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </x-common.component-card>
</form>