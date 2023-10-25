

<header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="index.html" class="logo">
              <img src="{{ asset('assets/images/zoom-logo.svg') }}" alt="Chain App Dev">
            </a>
            <!-- ***** Logo End ***** -->
            <!-- ***** Menu Start ***** -->            
            <ul class="nav">
              @if(Auth::check())
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_home" href="/" class="active">Program Details</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_dependents" href="/dependents">Dependents</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_enrollment" href="/enrollment">Enrollment</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_pricing" href="#pricing">Claims</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_dependentsLE" href="/dependents/life-events">Life Events</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_hradmin" href="#hradmin">HR Admin</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_report" href="#report">Report</a></li>
                &nbsp;
                <li>
                    <div class="gradient-button">
                        <a id="modal_trigger_logout" href="#modal">
                            <i class="fa fa-sign-out-alt"></i>
                            Welcome {{ Auth::user()->fname . ' ' . Auth::user()->lname }}
                        </a>
                    </div>
                </li>

              @else
              <li><div class="gradient-button"><a id="modal_trigger" href="#modal"><i class="fa fa-sign-in-alt"></i> Sign In Now</a></div></li> 
              @endif
            </ul>        
            <a class='menu-trigger'>
                <span>Menu</span>
            </a>
            <!-- ***** Menu End ***** -->
          </nav>
          
        </div>
      </div>
    </div>
  </header>
@if(Auth::check() && (request()->path() == 'enrollment'))
  <div id="points-header" class="float-left text-center slideInDown animated">
      <div class="row pt-1" style="height:40px;">
          <div id="points-header-l" class="col-2 offset-3">
            Total Points:
            <label>{{ Auth::user()->points_used + Auth::user()->points_available }}</label>
          </div>
          <div id="points-header-m" class="col-2">
            Consumed Points:
            <label>{{ Auth::user()->points_used }}</label></div>
          <div id="points-header-r" class="col-2">
            Available Points:
            <label>{{ Auth::user()->points_available }}</label></div>
      </div>
  </div>
@endif