  <!-- ***** Header Area Start ***** -->
  @include('_partial.header')
  <!-- ***** Header Area End ***** -->
  @yield('css_style')
  @include('_partial.login-form')
  @include('_partial.logout-form')
  @if(Auth::check())
    @include('_partial.userProfile')
  @endif
  @yield('script')