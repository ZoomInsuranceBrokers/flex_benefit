<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/sweet-alert.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/all.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/responsive.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/responsive.bootstrap5.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <title>My Benefits</title>

</head>

<body>
  <div class="overlay">
    <div class="loader"></div>
  </div>


  @component('layouts.components.header') @endcomponent
  @yield('content')
  @component('layouts.components.footer') @endcomponent

  <script>
    window.showLoader = function() {
      document.querySelector('.loader').style.display = 'block';
      document.querySelector('.overlay').style.display = 'block';
    }

    window.hideLoader = function() {
      document.querySelector('.loader').style.display = 'none';
      document.querySelector('.overlay').style.display = 'none';
    }

    window.addEventListener('load', function() {
      hideLoader();
    });

    // setTimeout(function() {
    //   hideLoader();
    // }, 1000);
</script>


  </script>
  <script src="{{ asset('assets/js/all.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('assets/js/sweet-alert.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
  <script src="{{ asset('assets/js/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/js/dataTables.responsive.js') }}"></script>
  <script src="{{ asset('assets/js/responsive.bootstrap5.js') }}"></script>

</body>

</html>