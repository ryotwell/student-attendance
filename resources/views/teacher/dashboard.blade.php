@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:gap-6">
        @if (Auth::user()->isTeacher())
            <div class="col-span-12 space-y-6">
                <x-guru-menu />
            </div>
        @endif
        @if (Auth::user()->isAdmin())
            <div class="col-span-12 space-y-6 xl:col-span-7">
                <x-admin.total-students :totalStudents={{ $totalStudents }} :totalClasses={{ $totalClasses }} />
            </div>
        @endif
    </div>
@endsection
