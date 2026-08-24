@props([
    'counselingCase' => null,
    'categoryOptions' => [],
    'selectedStudent' => null,
])

<form method="POST" action="{{ $counselingCase ? route('bk.cases.update', $counselingCase) : route('bk.cases.store') }}">
    @csrf

    @if($counselingCase)
        @method('PUT')
    @endif

    <x-common.component-card :title="$counselingCase ? 'Edit Catatan Kasus' : 'Tambah Catatan Kasus'">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- SISWA --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Siswa <span class="text-error-500">*</span>
                </label>

                <select 
                    id="student_id"
                    name="student_id"
                    class="h-11 w-full rounded-lg border border-gray-300">
                </select>

                @error('student_id')
                    <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>


            {{-- KATEGORI --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Kategori <span class="text-error-500">*</span>
                </label>

                <select 
                    id="category"
                    name="category"
                    class="h-11 w-full rounded-lg border border-gray-300 px-4">

                    <option value="">Pilih kategori</option>

                    @foreach($categoryOptions as $key => $option)
                        <option value="{{ $key }}"
                            @selected(old('category', $counselingCase->category ?? '') == $key)>
                            {{ $option['label'] }}
                        </option>
                    @endforeach

                </select>

                @error('category')
                    <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

        </div>


        {{-- TANGGAL --}}
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium">
                Tanggal
            </label>

            <input 
                type="date"
                name="date"
                value="{{ old('date', $counselingCase ? $counselingCase->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                class="h-11 w-full rounded-lg border px-4">

            @error('date')
                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>


        {{-- MASALAH --}}
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium">
                Deskripsi Masalah
            </label>

            <textarea
                name="description"
                rows="4"
                class="w-full rounded-lg border px-4 py-2">{{ old('description', $counselingCase->description ?? '') }}</textarea>

            @error('description')
                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>


        {{-- TINDAK LANJUT --}}
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium">
                Tindak Lanjut
            </label>

            <textarea
                name="action_taken"
                rows="4"
                class="w-full rounded-lg border px-4 py-2">{{ old('action_taken', $counselingCase->action_taken ?? '') }}</textarea>

            @error('action_taken')
                <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>


        {{-- BUTTON --}}
        <div class="mt-6 flex gap-3">

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
$(document).ready(function () {

    $('#student_id').select2({
        placeholder: 'Ketik nama atau NIS siswa...',
        minimumInputLength: 1,
        width: '100%',

        ajax: {
            url: "{{ route('bk.students.search') }}",
            dataType: 'json',
            delay: 300,

            data: function (params) {
                return {
                    q: params.term
                };
            },

            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            phone: item.parent_phone
                        };
                    })
                };
            }
        }
    });


    @if($selectedStudent)
        $('#student_id')
            .append(new Option(
                "{{ $selectedStudent->name }}",
                "{{ $selectedStudent->id }}",
                true,
                true
            ))
            .trigger('change');
    @endif


    @if($counselingCase)
        $('#student_id')
            .append(new Option(
                "{{ $counselingCase->student->name }}",
                "{{ $counselingCase->student_id }}",
                true,
                true
            ))
            .trigger('change');
    @endif

});
</script>
@endpush