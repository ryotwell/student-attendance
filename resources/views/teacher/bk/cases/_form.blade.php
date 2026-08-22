@props([
    'counselingCase' => null,
    'categoryOptions' => [],
    'selectedStudent' => null,
])

<form method="POST"
    action="{{ $counselingCase ? route('bk.cases.update', $counselingCase) : route('bk.cases.store') }}">
    @csrf

    @if ($counselingCase)
        @method('PUT')
    @endif
    <x-common.component-card :title="$counselingCase ? 'Edit Catatan Kasus' : 'Tambah Catatan Kasus'">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- SEARCH SISWA --}}
            <div x-data="studentSearch()" x-init="init()">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Siswa <span class="text-error-500">*</span>
                </label>
                <input type="hidden" name="student_id" x-model="selectedId">
                <div class="relative">
                    <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="showDropdown=true"
                        @keydown="handleKeydown($event)" placeholder="Ketik nama atau NIS siswa..."
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                    <div x-show="showDropdown" @click.away="showDropdown=false"
                        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-lg border bg-white shadow">
                        <template x-if="loading">
                            <div class="px-4 py-2 text-sm">
                                Mencari...
                            </div>
                        </template>
                        <template x-for="(s,index) in results" :key="s.id">
                            <button type="button" @click="select(s)"
                                class="block w-full px-4 py-2 text-left hover:bg-gray-100" x-text="s.text">
                            </button>
                        </template>
                        <template x-if="results.length===0 && !loading && query.length > 0">
                            <div class="px-4 py-2 text-sm text-gray-500">
                                Siswa tidak ditemukan
                            </div>
                        </template>
                    </div>
                </div>
                @error('student_id')
                    <p class="mt-1 text-sm text-error-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Kategori <span class="text-error-500">*</span>
                </label>
                <select id="category" name="category" class="h-11 w-full rounded-lg border border-gray-300 px-4">
                    <option value="">
                        Pilih kategori
                    </option>
                    @foreach ($categoryOptions as $key => $option)
                        <option value="{{ $key }}" @selected(old('category', $counselingCase->category ?? '') == $key)>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-error-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- TANGGAL --}}
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium">
                Tanggal
            </label>
            <input type="date" name="date"
                value="{{ old('date', $counselingCase ? $counselingCase->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                class="h-11 w-full rounded-lg border px-4">
            @error('date')
                <p class="mt-1 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- MASALAH --}}
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium">
                Deskripsi Masalah
            </label>
            <textarea id="description" name="description" rows="4" class="w-full rounded-lg border px-4 py-2">{{ old('description', $counselingCase->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- TINDAK LANJUT --}}
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium">
                Tindak Lanjut
            </label>
            <textarea id="action_taken" name="action_taken" rows="4" class="w-full rounded-lg border px-4 py-2">{{ old('action_taken', $counselingCase->action_taken ?? '') }}</textarea>
            @error('action_taken')
                <p class="mt-1 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- BUTTON --}}
        <div class="mt-6 flex flex-wrap gap-3">
            <x-ui.button type="submit" variant="primary">
                {{ $counselingCase ? 'Perbarui' : 'Simpan' }}
            </x-ui.button>
            <a href="{{ route('bk.cases.index') }}"
                class="inline-flex items-center rounded-lg border px-5 py-3 text-sm">
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
                selectedId: '{{ old('student_id', $counselingCase->student_id ?? optional($selectedStudent)->id ?? '') }}',
                selectedPhone: '',
                showDropdown: false,
                loading: false,
                highlightedIndex: -1,
                init() {
                    @if ($counselingCase)
                        this.query = '{{ $counselingCase->student->name ?? '' }}';
                        this.selectedPhone = '{{ $counselingCase->student->parent_phone ?? '' }}';
                    @elseif ($selectedStudent)
                        this.query = '{{ $selectedStudent->name }}';
                        this.selectedPhone = '{{ $selectedStudent->parent_phone ?? '' }}';
                    @endif
                },
                async search() {
                    if (this.query.length < 1) {
                        this.results = [];
                        return;
                    }
                    this.loading = true;
                    try {
                        let res = await fetch(
                            '{{ route('bk.students.search') }}?q=' +
                            encodeURIComponent(this.query)
                        );
                        this.results = await res.json();
                    } finally {
                        this.loading = false;
                    }
                },
                select(student) {
                    this.selectedId = student.id;
                    this.query = student.text;
                    this.selectedPhone = student.parent_phone ?? '';
                    this.showDropdown = false;
                    this.results = [];
                },
                handleKeydown(e) {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        if (this.results.length) {
                            this.select(this.results[0]);
                        }
                    }
                },
                sendWhatsapp() {
                    if (!this.selectedId) {
                        alert("Pilih siswa terlebih dahulu");
                        return;
                    }
                    if (!this.selectedPhone) {
                        alert("Nomor WhatsApp orang tua belum tersedia");
                        return;
                    }
                    let pesan = `Assalamu'alaikum Bapak/Ibu.

Kami menyampaikan informasi terkait bimbingan siswa:

Nama Siswa: ${this.query}
Kategori: ${document.getElementById('category').value}

Permasalahan:
${document.getElementById('description').value}

Tindak Lanjut:
${document.getElementById('action_taken').value}

Terima kasih atas perhatian dan kerja samanya.`;

                    let nomor = this.selectedPhone.replace(/\D/g, '');
                    if (nomor.startsWith('08')) {
                        nomor = '62' + nomor.substring(1);
                    }
                    window.open(
                        'https://wa.me/' + nomor + '?text=' + encodeURIComponent(pesan),
                        '_blank'
                    );
                }
            }
        }
    </script>
@endpush