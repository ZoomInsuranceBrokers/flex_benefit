<style>
    .flex-avl {
        background-color: #043C4B;
        margin: 10px;
        border-radius: 6px;
        color: white;
        background-image: linear-gradient(rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1));
        /* White gradient */

    }

    .flex-uti {
        background-color: #0FA2D5;
        margin: 10px;
        border-radius: 6px;
        color: white;
        text-align: center;
    }

    .dropdown-item-wallet {
        display: block;
        width: 100%;
        clear: both;
        font-weight: 400;
        color: var(--bs-dropdown-link-color);
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }

    .wallet-score {
        display: flex;
        flex-direction: column;
        align-content: center;
        justify-content: center;
        align-items: center;
    }
</style>

<header class="position-relative">
    <div class="custom-nav">
        <nav class="navbar navbar-expand-lg header-nav">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('assets/images/flex-logo.png') }}" alt="logo" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex">
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
                                <a class="dropdown-item" href="/claim/loadHospital">Network Hospital</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dependants/life-events">Life Events</a>
                    </li>
                    <!-- Wallet Section with Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center" href="#" id="walletDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="profile-img">
                                <img src="{{ asset('assets/images/wallet-png.png') }}" alt="profile" style="width: 28px;" />
                            </span>
                            <div style="font-size: 10px; margin-left:10px; color: #2d2f30; font-weight:700;">
                                <div>{{ Auth::user()->points_available }}*</div>
                                <div style="font-size: 8px;">Avl. Points</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="walletDropdown" style="width: max-content;">
                            <!-- Dropdown Menu Items -->
                            <li><span class="dropdown-item-text flex-avl">FlexPoints Allocated: 5000</span></li>
                            <li><span class="dropdown-item-text flex-uti">FlexPoints Utilized</span></li>
                            <hr>
                            <div class="wallet-score">
                                <li><span class="dropdown-item-wallet">Term Life: 456</span></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><span class="dropdown-item-wallet">Medical: 456</span></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><span class="dropdown-item-wallet">Accidental: 456</span></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><span class="dropdown-item-wallet">Non-insured: 456</span></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><span class="dropdown-item-text">Total: 1824</span></li>
                            </div>
                        </ul>
                    </li>


                    <li class="nav-item dropdown profile-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex">
                            <span class="profile-img"><img src=" {{ asset('assets/images//profile.jpg') }}" alt="profile" /></span><span>
                                {{ Auth::user()->fname}} <i class="fa-solid fa-list ms-2"></i></span>
                        </a>
                        <ul class="dropdown-menu me-2">
                            @if (Auth::check())
                            <li><a class="dropdown-item userProfileTrigger" data-bs-toggle="modal" data-bs-target="#userProfileModal">User Profile</a></li>
                            <!-- 
                            <li><a class="dropdown-item" id="userProfile_trigger" onclick="userProfile_trigger()">User
                                    Profile</a></li>
                            <li> -->
                            <hr class="dropdown-divider" />
                    </li>
                    <li>
                        @if (Auth::user()->id == 13)
                        <a class="dropdown-item" href="{{ asset('assets/documents/ECard -Demo.pdf') }}" target="_blank">Download Ecard</a>
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
                    <li><a class="dropdown-item" href="{{ asset('assets/documents/MyBenefits@Zoom.pdf') }}" target="_blank">User Manual</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
                    @endif
                </ul>
                </li>
                @else
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item dropdown profile-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="modal" data-bs-target="#loginModal" class="d-flex">
                                <span> <i class="fa-solid fa-right-to-bracket me-2"></i></span><span>Sign In Now</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
                </ul>
            </div>
        </nav>
    </div>
</header>


<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-overlay"></div>
            <div class="modal-body">
                <div class="col-12 logout-text">
                    <h3>Confirm Logout ?</h3>
                    <p>Are you sure you want to logout?</p>
                </div>
                <div class="mt-3 d-flex btns">
                    <button type="submit" class="btn cancel--btn d-block">
                        Cancel
                    </button>
                    <a class="nav-link" href="/user/logout"><button type="submit" class="btn primary--btn d-block mx-auto">
                            Logout
                        </button></a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const currentLocation = location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentLocation) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>