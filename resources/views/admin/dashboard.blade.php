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
      {{-- <x-ecommerce.ecommerce-metrics /> --}}
      <x-admin.total-students :totalStudents={{ $totalStudents }} :totalClasses={{ $totalClasses }} />
      {{-- <x-ecommerce.monthly-sale /> --}}
    </div>
    {{-- <div class="col-span-12 xl:col-span-5">
        <x-ecommerce.monthly-target />
    </div> --}}
    @endif

    <div class="col-span-12">
      <x-ecommerce.statistics-chart />
    </div>

    {{-- <div class="col-span-12 xl:col-span-5">
      <x-ecommerce.customer-demographic />
    </div>

    <div class="col-span-12 xl:col-span-7">
      <x-ecommerce.recent-orders />
    </div> --}}
  </div>
@endsection
