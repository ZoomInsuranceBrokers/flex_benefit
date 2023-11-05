  <!-- ***** Header Area Start ***** -->
  @include('_partial.header')
  <!-- ***** Header Area End ***** -->
  @yield('css_style')
  @include('_partial.login-form')
  @include('_partial.logout-form')
  @yield('script')