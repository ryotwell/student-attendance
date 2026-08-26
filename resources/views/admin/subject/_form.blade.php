@props(['subject' => null])

@php

$level = Auth::user()->school->level
    
@endphp

<form method="POST" action="{{ $subject ? route('subjects.update', $subject) : route('subjects.store') }}">
    @csrf
    @if ($subject)
        @method('PUT')
    @endif

    <x-common.component-card :title="$subject ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Nama Mata Pelajaran<span class="text-error-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name', $subject->name ?? '') }}"
                placeholder="cth: Matematika"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('name')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="grade" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Tingkatan<span class="text-error-500">*</span>
            </label>
            <select name="grade" id="grade"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                <option value="">Pilih Tingkatan</option>
                @foreach (config("school.grade_groups.{$level}.levels", []) as $grade)
                    <option value="{{ $grade }}" {{ old('grade', $subject->grade ?? '') == $grade ? 'selected' : '' }}>
                        {{ $grade }}
                    </option>
                @endforeach
            </select>
            @error('grade')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                {{ $subject ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('subjects.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </x-common.component-card>
</form>
