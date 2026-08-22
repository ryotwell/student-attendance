@props(['counselingCase' => null, 'students' => [], 'categoryOptions' => [], 'selectedStudentId' => null])

<form method="POST" action="{{ $counselingCase ? route('bk.cases.update', $counselingCase) : route('bk.cases.store') }}">
    @csrf
    @if ($counselingCase)
        @method('PUT')
    @endif

    <x-common.component-card :title="$counselingCase ? 'Edit Catatan Kasus' : 'Tambah Catatan Kasus'">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div x-data="studentSearch()" x-init="init()">
                <label for="student_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Siswa<span class="text-error-500">*</span>
                </label>
                <input type="hidden" name="student_id" :value="selectedId" />
                <div class="relative">
                    <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="handleInputFocus()" @keydown="handleKeydown($event)"
                        placeholder="Ketik nama atau NIS siswa..."
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 pr-10" />
                    <span x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2 animate-spin text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <div x-show="showDropdown && (results.length > 0 || loading)" @click.away="showDropdown = false"
                        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">
                        <template x-if="loading">
                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 animate-pulse">Mencari...</div>
                        </template>
                        <template x-for="s in results" :key="s.id">
                            <div class="cursor-pointer px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
                                :class="{ 'bg-gray-100 dark:bg-gray-800': $index === highlightedIndex }"
                                @click="select(s)"
                                @mouseenter="highlightedIndex = $index"
                                x-text="s.text"></div>
                        </template>
                        <template x-if="results.length === 0 && !loading">
                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Siswa tidak ditemukan</div>
                        </template>
                    </div>
                </div>
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

@push('scripts')
<script>
    function studentSearch() {
        return {
            query: '',
            results: [],
            selectedId: '{{ old('student_id', $counselingCase->student_id ?? $selectedStudentId ?? '') }}',
            showDropdown: false,
            loading: false,
            highlightedIndex: -1,
            init() {
                @if($counselingCase ?? null)
                    this.query = '{{ $counselingCase->student->name ?? '' }} — {{ $counselingCase->student->xclass->name ?? '' }}';
                @endif
            },
            async search() {
                if (this.query.length < 1) { this.results = []; this.highlightedIndex = -1; return; }
                this.loading = true;
                try {
                    const res = await fetch('{{ route("bk.students.search") }}?q=' + encodeURIComponent(this.query));
                    this.results = await res.json();
                } finally {
                    this.loading = false;
                }
                this.highlightedIndex = -1;
            },
            select(student) {
                this.selectedId = student.id;
                this.query = student.text;
                this.showDropdown = false;
                this.results = [];
            },
            handleKeydown(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.highlightedIndex >= 0 && this.results[this.highlightedIndex]) {
                        this.select(this.results[this.highlightedIndex]);
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.results.length - 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0);
                } else if (e.key === 'Escape') {
                    this.showDropdown = false;
                }
            },
            handleInputFocus() {
                this.showDropdown = true;
                this.highlightedIndex = -1;
            },
        }
    }
</script>
@endpush