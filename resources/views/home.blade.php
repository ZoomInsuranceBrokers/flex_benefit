@extends('layouts.layout')

@section('content')
  @include('_partial.main-banner')

  @if(Auth::check())
    @include('_partial.benefits')
  @endif  

@stop