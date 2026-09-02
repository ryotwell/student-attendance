@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kenaikan Kelas" />

    <div class="mx-auto max-w-4xl space-y-6">

        <x-common.component-card title="Kenaikan Kelas Siswa">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                {{-- KELAS ASAL (tahun ajaran non-aktif) --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kelas Asal</label>
                    <select id="source_xclass" class="h-11 w-full rounded-lg border border-gray-300 px-4">
                        <option value="">Pilih kelas asal</option>
                        @forelse ($sourceXclasses as $xclass)
                            <option value="{{ $xclass->id }}">
                                {{ $xclass->name }} ({{ $xclass->academicYear->name }})
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada kelas dari tahun ajaran non-aktif</option>
                        @endforelse
                    </select>
                </div>

                {{-- KELAS TUJUAN (tahun ajaran aktif) --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kelas Tujuan</label>
                    <select id="target_xclass" class="h-11 w-full rounded-lg border border-gray-300 px-4">
                        <option value="">Pilih kelas tujuan</option>
                        @forelse ($targetXclasses as $xclass)
                            <option value="{{ $xclass->id }}">
                                {{ $xclass->name }} ({{ $xclass->academicYear->name }})
                            </option>
                        @empty
                            <option value="" disabled>Belum ada kelas di tahun ajaran aktif</option>
                        @endforelse
                    </select>
                    <p class="mt-1 text-xs text-gray-400">
                        Kelas tujuan diambil dari tahun ajaran yang sedang aktif.
                    </p>
                </div>

            </div>

            {{-- DAFTAR SISWA --}}
            <div id="student-list-wrapper" class="mt-6 hidden">

                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-medium text-gray-700">Pilih Siswa</label>
                    <label class="flex items-center gap-2 text-sm text-gray-500">
                        <input type="checkbox" id="select-all">
                        Pilih Semua
                    </label>
                </div>

                <div id="student-list" class="max-h-96 space-y-1 overflow-y-auto rounded-lg border border-gray-200 p-3">
                    {{-- diisi via JS --}}
                </div>

            </div>

            <div id="empty-state" class="mt-6 text-sm text-gray-400">
                Pilih kelas asal untuk menampilkan daftar siswa.
            </div>

            {{-- AKSI --}}
            {{-- Sengaja TIDAK diberi atribut `disabled` di Blade: komponen
                 x-ui.button men-snapshot prop disabled jadi class statis
                 saat render, sehingga class visualnya tidak ikut berubah
                 saat JS toggle disabled via .prop(). Disabled state
                 (atribut + class) dikelola manual lewat JS di bawah. --}}
            <div class="mt-6 flex gap-3">
                <x-ui.button type="button" id="btn-promote" variant="primary" className="cursor-not-allowed opacity-50">
                    Pindahkan / Naikkan Siswa Terpilih
                </x-ui.button>

                <x-ui.button type="button" id="btn-graduate" variant="primary" className="cursor-not-allowed opacity-50">
                    Luluskan Siswa Terpilih
                </x-ui.button>
            </div>

        </x-common.component-card>

    </div>

    {{-- Form tersembunyi untuk submit --}}
    <form id="promotion-form" method="POST" action="{{ route('admin.promotion.store') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" id="form_action">
        <input type="hidden" name="target_xclass_id" id="form_target_xclass_id">
        <div id="student-ids-inputs"></div>
    </form>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Set disabled state awal secara manual (atribut native + class visual),
    // karena atribut `disabled` sengaja tidak dipasang lewat Blade.
    $('#btn-promote, #btn-graduate')
        .prop('disabled', true)
        .addClass('cursor-not-allowed opacity-50');

    function loadStudents(xclassId) {
        if (!xclassId) {
            $('#student-list-wrapper').addClass('hidden');
            $('#empty-state').removeClass('hidden');
            return;
        }

        $.get("{{ url('admin/promotion/students') }}/" + xclassId, function (students) {
            const $list = $('#student-list').empty();

            if (students.length === 0) {
                $list.append('<p class="text-sm text-gray-400 p-2">Tidak ada siswa aktif di kelas ini.</p>');
            } else {
                students.forEach(function (student) {
                    $list.append(`
                        <label class="flex items-center gap-2 rounded-lg p-2 hover:bg-gray-50">
                            <input type="checkbox" class="student-checkbox" value="${student.id}">
                            <span class="text-sm">${student.name} <span class="text-gray-400">(${student.nis})</span></span>
                        </label>
                    `);
                });
            }

            $('#student-list-wrapper').removeClass('hidden');
            $('#empty-state').addClass('hidden');
            $('#select-all').prop('checked', false);
            updateButtons();
        });
    }

    $('#source_xclass').on('change', function () {
        loadStudents($(this).val());
    });

    $('#select-all').on('change', function () {
        $('.student-checkbox').prop('checked', $(this).is(':checked'));
        updateButtons();
    });

    $(document).on('change', '.student-checkbox', updateButtons);
    $('#target_xclass').on('change', updateButtons);

    // Toggle disabled state (atribut + class) bersamaan, supaya tampilan
    // tombol selalu sinkron dengan kondisi bisa-diklik-atau-tidak.
    function setButtonEnabled($btn, enabled) {
        $btn.prop('disabled', !enabled)
            .toggleClass('cursor-not-allowed opacity-50', !enabled);
    }

    function updateButtons() {
        const anyChecked = $('.student-checkbox:checked').length > 0;
        const hasTarget = $('#target_xclass').val() !== '';

        setButtonEnabled($('#btn-promote'), anyChecked && hasTarget);
        setButtonEnabled($('#btn-graduate'), anyChecked);
    }

    function submitPromotion(action) {
        const selectedIds = $('.student-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) return;

        if (action === 'NAIK' && !confirm(`Pindahkan/naikkan ${selectedIds.length} siswa ke kelas tujuan?`)) return;
        if (action === 'LULUS' && !confirm(`Luluskan ${selectedIds.length} siswa? Tindakan ini menandai mereka LULUS.`)) return;

        $('#form_action').val(action);
        $('#form_target_xclass_id').val(action === 'NAIK' ? $('#target_xclass').val() : '');

        const $inputs = $('#student-ids-inputs').empty();
        selectedIds.forEach(function (id) {
            $inputs.append(`<input type="hidden" name="student_ids[]" value="${id}">`);
        });

        $('#promotion-form').submit();
    }

    $('#btn-promote').on('click', function () { submitPromotion('NAIK'); });
    $('#btn-graduate').on('click', function () { submitPromotion('LULUS'); });

});
</script>
@endpush