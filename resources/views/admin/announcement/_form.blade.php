@props(['announcement' => null])

<form method="POST" action="{{ $announcement ? route('announcements.update', $announcement) : route('announcements.store') }}">
    @csrf
    @if ($announcement)
        @method('PUT')
    @endif

    <x-common.component-card :title="$announcement ? 'Edit Pengumuman' : 'Tambah Pengumuman'">
        <div>
            <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Judul<span class="text-error-500">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}"
                placeholder="cth: Pengumuman Libur Semester"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('title')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="content" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Isi Pengumuman<span class="text-error-500">*</span>
            </label>
            <textarea id="content" name="content" rows="6"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('content', $announcement->content ?? '') }}</textarea>
            @error('content')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Status<span class="text-error-500">*</span>
            </label>
            <select id="status" name="status"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                <option value="DRAFT" @selected(old('status', $announcement->status ?? 'DRAFT') === 'DRAFT')>Draft</option>
                <option value="PUBLISHED" @selected(old('status', $announcement->status ?? 'DRAFT') === 'PUBLISHED')>Published</option>
            </select>
            @error('status')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                {{ $announcement ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('announcements.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </x-common.component-card>
</form>