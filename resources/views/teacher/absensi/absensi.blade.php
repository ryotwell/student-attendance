@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="{{ $title }}" />


<div class="max-w-6xl mx-auto">


<x-common.component-card 
    title="Absensi {{ $class->name }} - {{ $schedule->subject->name }}">



{{-- HEADER INFO --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">


    <div>

        <h2 class="text-xl font-bold text-gray-800 dark:text-white">
            {{ $class->name }}
        </h2>


        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $schedule->subject->name }}
        </p>

    </div>



    <div class="flex flex-col sm:flex-row gap-3">


        {{-- JUMLAH SISWA --}}
        <div class="flex items-center gap-2 px-4 py-3 rounded-xl 
        bg-brand-50 dark:bg-brand-900/30">


            <svg width="22" height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="text-brand-500">

                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>

            </svg>


            <span class="font-semibold text-brand-600 dark:text-brand-400">
                {{ $class->students->count() }} Siswa
            </span>


        </div>





        {{-- DATE PICKER --}}
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl 
        bg-gray-50 dark:bg-gray-800
        border border-gray-200 dark:border-gray-700">


            <svg width="22" height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="text-brand-500">

                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>

            </svg>



            <div>

                <label class="text-xs text-gray-500 dark:text-gray-400">
                    Tanggal Absensi
                </label>


                <input 
                    type="date"
                    id="attendanceDate"
                    value="{{ $date->format('Y-m-d') }}"
                    class="
                    block mt-1
                    bg-transparent
                    text-sm font-semibold
                    text-gray-800
                    dark:text-white
                    outline-none
                    cursor-pointer
                    "
                >

            </div>


        </div>


    </div>


</div>





<form action="{{ route('attendance.store') }}" method="POST">

@csrf


<input type="hidden" 
name="class_id" 
value="{{ $class->id }}">


<input type="hidden"
name="schedule_id"
value="{{ $schedule->id }}">


<input type="hidden"
name="date"
id="formDate"
value="{{ $date->format('Y-m-d') }}">





<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">



@foreach($class->students as $index=>$student)


@php

$existing = $existingAttendances->get($student->id);

$current = $existing->status ?? 'ALPHA';


@endphp





<div class="
group rounded-2xl
border border-gray-200
dark:border-gray-700

bg-white dark:bg-gray-900

p-5

transition-all duration-200

hover:-translate-y-1
hover:shadow-xl
">





{{-- STUDENT HEADER --}}

<div class="flex items-center gap-4 mb-5">


<div class="
w-14 h-14
rounded-xl

bg-blue-100
dark:bg-blue-900/30

flex items-center justify-center

text-blue-600
dark:text-blue-400
">


<svg width="30" height="30"
viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="1.7">


<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>

<circle cx="12" cy="7" r="4"/>


</svg>


</div>




<div>

<h4 class="
font-bold
text-lg

text-gray-800
dark:text-white
">

{{ $student->name }}

</h4>


<p class="
text-sm
text-gray-500
dark:text-gray-400
">

NIS : {{ $student->nis }}

</p>


</div>


</div>





<input type="hidden"
name="attendances[{{ $index }}][student_id]"
value="{{ $student->id }}">






{{-- STATUS --}}

<div class="grid grid-cols-4 gap-2">


@php

$statusList=[

'HADIR'=>[
'icon'=>'✓',
'label'=>'Hadir',
'color'=>'green'
],

'IZIN'=>[
'icon'=>'📘',
'label'=>'Izin',
'color'=>'blue'
],

'SAKIT'=>[
'icon'=>'🤒',
'label'=>'Sakit',
'color'=>'yellow'
],

'ALPHA'=>[
'icon'=>'✕',
'label'=>'Alpha',
'color'=>'red'
]

];


@endphp




@foreach($statusList as $key=>$item)



<label class="cursor-pointer">


<input

type="radio"

name="attendances[{{ $index }}][status]"

value="{{ $key }}"

class="hidden peer"

{{ $current==$key?'checked':'' }}

>



<div class="
rounded-xl
p-3

text-center

bg-gray-100
dark:bg-gray-800

text-gray-500


peer-checked:bg-{{ $item['color'] }}-500

peer-checked:text-white


transition-all

hover:scale-105

">


<div class="text-xl mb-1">

{{ $item['icon'] }}

</div>


<div class="text-xs font-bold">

{{ $item['label'] }}

</div>


</div>



</label>


@endforeach



</div>



</div>



@endforeach



</div>





{{-- BUTTON --}}

<div class="
mt-8
pt-5

border-t
border-gray-100
dark:border-gray-800

flex
justify-end
gap-3
">



<a href="{{ route('absensi.schedules') }}"

class="
px-5
py-3

rounded-xl

border
border-gray-200
dark:border-gray-700


text-gray-700
dark:text-gray-300


hover:bg-gray-50
dark:hover:bg-gray-800


font-semibold
text-sm
">

← Kembali

</a>





<button type="submit"

class="
px-6
py-3

rounded-xl

bg-brand-500
hover:bg-brand-600


text-white

font-semibold

text-sm


transition

flex items-center gap-2
">


<svg width="18" height="18"
viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="2">


<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>

<polyline points="17 21 17 13 7 13 7 21"/>


</svg>


Simpan Absensi


</button>


</div>



</form>


</x-common.component-card>


</div>





<script>


document
.getElementById('attendanceDate')
.addEventListener('change',function(){


let date=this.value;


let url=new URL(window.location.href);


url.searchParams.set('date',date);



window.location.href=url.toString();


});



</script>



@endsection