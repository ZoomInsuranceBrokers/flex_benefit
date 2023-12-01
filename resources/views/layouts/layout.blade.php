<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="_token" content="{{ csrf_token() }}">
    
    @include('_partial.css-includes')
    @yield('link_rel')
    
    @yield('inline-page-style')
  </head>

<body>

  <!-- ***** Preloader Start ***** -->
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- ***** Preloader End ***** -->

@component('layouts.components.header') @endcomponent
@yield('content')
{{-- @component('layouts.components.footer') @endcomponent --}}


  <!-- Scripts -->
  @include('_partial.js-includes')
  @yield('script')
  <script>
    $(document).ready(function(){
      @yield('document_ready')
    });
  </script>
</body>
</html>