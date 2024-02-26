<header class="position-relative">
    <div class="custom-nav">
        <nav class="navbar navbar-expand-lg header-nav">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('assets/images/flex-logo.png') }}" alt="logo" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    @if (Auth::check())
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/">Program Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/dependants">Dependants</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/enrollment">Enrollment</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false" class="d-flex">
                                Claims
                            </a>
                            <ul class="dropdown-menu me-2">
                                <li><a class="dropdown-item" href="/claim/initiate">Intimate Claim</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a class="dropdown-item" href="/claim/submit">Submit Claim</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a class="dropdown-item" href="/claim/track">Track Claim</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/claim/loadHospitals">Network Hospital</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/dependants/life-events">Life Events</a>
                        </li>

                        <li class="nav-item dropdown profile-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false" class="d-flex">
                                <span class="profile-img"><img src=" {{ asset('assets/images//profile.jpg') }}"
                                        alt="profile" /></span><span>
                                    Welcome {{ Auth::user()->fname . ' ' . Auth::user()->lname }} <i
                                        class="fa-solid fa-list ms-2"></i></span>
                            </a>
                            <ul class="dropdown-menu me-2">
                                @if (Auth::check())
                                    <li><a class="dropdown-item" id="userProfile_trigger" href="#userProfile">User
                                            Profile</a></li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        @if (Auth::user()->id == 13)
                                            <a class="dropdown-item"href="{{ asset('assets/documents/ECard -Demo.pdf') }}"
                                                target="_blank">Download Ecard</a>
                                        @else
                                            <a class="dropdown-item" href="/user/ecard" target="_blank">Download
                                                Ecard</a>
                                        @endif
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/user/summary">Enrollment Summary</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ asset('assets/documents/MyBenefits@Zoom.pdf') }}"
                                            target="_blank">User Manual</a></li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li><a class="dropdown-item" id="logoutModal_trigger"
                                            href="#launchLogout">Logout</a></li>
                                @endif
                            </ul>
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
            </div>
        </nav>
    </div>
</header>

@if (Auth::check())
    @if (request()->path() == 'enrollment')
        <div id="points-header" class="float-left text-center slideInDown animated">
            <div class="row pt-1" style="height:40px;">
                <div id="points-header-l" class="col-2 offset-3">
                    Total Points:
                    <label id="points-head-tot">{{ Auth::user()->points_used + Auth::user()->points_available }}</label>
                </div>
                <div id="points-header-m" class="col-2">
                    Consumed Points:
                    <label id="points-head-used">{{ Auth::user()->points_used }}</label>
                </div>
                <div id="points-header-r" class="col-2">
                    Available Points:
                    <label id="points-head-avail">{{ Auth::user()->points_available }}</label>
                </div>
            </div>
        </div>
    @endif
@endif

<script>
  document.addEventListener("DOMContentLoaded", function() {
      const currentLocation = location.pathname;
      const navLinks = document.querySelectorAll('.nav-link');
  
      navLinks.forEach(link => {
          if(link.getAttribute('href') === currentLocation) {
              link.classList.add('active');
          } else {
              link.classList.remove('active');
          }
      });
  });
  </script>
