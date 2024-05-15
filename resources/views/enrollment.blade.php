@php
    //dd($data['dependant']);
    $tabCount = Auth::check() ? (($data['is_enrollment_window'] ? count($data['category']) : 0) + 3) : 1;
@endphp

@extends('layouts.layout')

<link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<!-- JQUERY UI CSS -->
<link href="{{ asset('assets/css/jquery/jquery-ui.css') }}" rel="stylesheet">
<!-- Additional CSS Files -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('assets/css/animated.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/owl.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/app-custom.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css')}}">

@section('content')
  
@include('layouts.components.enrollment-tabs')

@if(Auth::check())
  @if(request()->path() == 'enrollment')
  <div id="points-header" class="float-left text-center slideInDown animated">
      <div class="row pt-1" style="height:40px; margin-bottom: 30px;">
          <div id="points-header-l" class="col-2 offset-3">
            Total Points:
            <label id="points-head-tot">{{ Auth::user()->points_used + Auth::user()->points_available }}</label>
          </div>
          <div id="points-header-m" class="col-2">
            Consumed Points:
            <label id="points-head-used">{{ Auth::user()->points_used }}</label></div>
          <div id="points-header-r" class="col-2">
            Available Points:
            <label id="points-head-avail">{{ Auth::user()->points_available }}</label></div>
      </div>
  </div>
  @endif
  
@endif                    
    @php
        if ($data['is_enrollment_window'] && count($data['dependant'])) {
            foreach ($data['dependant'] as $depItem) {
    @endphp
            <span id="dependant-list{{ $depItem['id'] }}" style="display:none;" data-name="{{ $depItem['dependent_name'] }}"
            data-depCode="{{ $depItem['dependent_code'] }}" data-depNom="{{ $depItem['nominee_percentage'] }}"
            data-depId = "{{ $depItem['id'] }}"></span>
    @php
           }
        }
    @endphp
@stop


<script src="{{ asset('vendor/jquery/jquery-1.9.1.min.js')}}"></script>
  <script src="{{ asset('vendor/jquery/jquery-ui-1.10.0.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('assets/js/jquery.jtable.js')}}" type="text/javascript"></script>
  <script src="{{ asset('assets/js/jquery.validationEngine.min.js')}}" type="text/javascript"></script>
  <script src="{{ asset('assets/js/jquery.validationEngine-en.min.js')}}" type="text/javascript"></script>

  <script src="{{ asset('assets/js/owl-carousel.js')}}"></script>
  <script src="{{ asset('assets/js/animation.js')}}"></script>
  <script src="{{ asset('assets/js/imagesloaded.js')}}"></script>
  <script src="{{ asset('assets/js/popup.js')}}"></script>
  <script src="{{ asset('assets/js/custom.js')}}"></script>
  <script src="{{ asset('assets/js/main.js')}}"></script>
  <script src="{{ asset('assets/js/jspdf.umd.min.js')}}"></script>


  