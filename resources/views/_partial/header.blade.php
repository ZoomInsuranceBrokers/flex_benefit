<header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="/" class="logo">
              <img src="{{ asset('assets/images/flex-logo.png') }}" alt="Chain App Dev" style="width: 280px;">
            </a>
            <!-- ***** Logo End ***** -->
            <!-- ***** Menu Start ***** -->            
            <ul class="nav">
              @if(Auth::check())
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_home" href="/" class="active">Program Details</a>
                </li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_dependants" href="/dependants">dependants</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_enrollment" href="/enrollment">Enrollment</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light submenu">
                    <a id="header_claim" onclick="getEcard()" href="javascript:return false;">Claims</a>
                    <ul>
                      <li>
                        <a href="/claim/initiate">Initiate Claim</a>
                      </li>
                      <li>
                        <a href="/claim/track">Track Claim</a>
                      </li>
                      <li>
                        <a href="/claim/loadHospital">Network Hospital</a>
                      </li>
                    </ul>
                </li>
                &nbsp;
                @if(!session('is_enrollment_window'))
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_dependantsLE" href="/dependants/life-events">Life Events</a>
                </li>
                @endif
                &nbsp;
                {{-- <li class="nav-item scroll-to-section bg-light">
                    <a id="header_hradmin" href="#hradmin">HR Admin</a></li>
                &nbsp;
                <li class="nav-item scroll-to-section bg-light">
                    <a id="header_report" href="#report">Report</a></li>
                &nbsp; --}}
                <li id="modal_trigger_user"class="submenu">
                    <div class="gradient-button">
                        <a>                        
                            <i class="fa fa-list-ul"></i>                           
                            Welcome {{ Auth::user()->fname . ' ' . Auth::user()->lname }}                      
                            {{-- <i class="fa fa-align-justify"></i>   --}}
                            {{-- <i class="fa fa-plus-square"></i>    --}}
                        </a>
                    </div>
                    @if(Auth::check())
                      <ul>
                        <li>
                          <a id="userProfile_trigger" href="#userProfile">User Profile</a>
                        </li>
                        <li>
                          <a href="/user/ecard" target="_blank">Download Ecard</a>
                        </li>
                        <li>
                          <a href="/user/summary" target="_blank">Enrollment Summary</a>
                        </li>
                        <li>
                          <a href="{{ asset('assets/documents/MyBenefits@Zoom.pdf')}}" target="_blank">User Manual</a>
                        </li>
                        <li>
                          <a id="logoutModal_trigger" href="#launchLogout">Logout</a>
                        </li>
                      </ul>
                    @endif
                </li>

              @else
              <li>
                <div class="gradient-button">
                <a id="modal_trigger" href="#modal">
                  <i class="fa fa-sign-in-alt"></i> Sign In Now</a>
                </div>
              </li> 
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

@if(Auth::check())
  @if(request()->path() == 'enrollment')
  <div id="points-header" class="float-left text-center slideInDown animated">
      <div class="row pt-1" style="height:40px;">
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

<script>
</script>
