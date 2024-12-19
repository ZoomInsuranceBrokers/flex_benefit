<style>
    .flex-avl {
        /* background-color: #043C4B; */
        margin: 10px;
        border-radius: 6px;
        color: white;
        background-image: linear-gradient(to right, #043C4B, #0FA2D5, #043C4B);
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
        display: inline-block;
        width: 100%;
        clear: both;
        font-weight: 400;
        color: var(--bs-dropdown-link-color);
        text-align: start;
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
        /* align-items: center; */
    }

    .shape-container {
        display: flex;
        align-items: center;
    }

    .circle {

        background-color: #0FA2D5;
        padding: 4px;
        border-radius: 50%;
    }

    .rectangle {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        background-color: #0FA2D5;
        padding: 1px 6px 1px 1px;
        margin-left: -4px;

    }

    .wallet-litags {
        width: 80%;
        display: flex;
        text-align: center;
    }

    .walliet-div {
        display: flex;
        justify-content: center;
    }

    .wallet-break-line {
        width: 85%;
        border: 1px solid var(--bs-dropdown-divider-bg);
        margin: 4px 0;
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
                    <li class="nav-item dropdown" id="pointsInfo">
                        <a class="nav-link d-flex align-items-center" href="#" id="walletDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="profile-img">
                                <img src="{{ asset('assets/images/wallet-png.png') }}" alt="profile" style="width: 28px;" />
                            </span>
                            <div style="font-size: 10px; margin-left:10px; color: #2d2f30; font-weight:700;">
                                <div>{{ Auth::user()->points_available }}*</div>
                                <div style="font-size: 8px;">Avl. Points</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="walletDropdown" style="width: max-content;border-radius:20px;">
                            <!-- Dropdown Menu Items -->
                            @php
                            // Define the dates
                            $comparisonDate = new DateTime('2023-12-17 12:00:00');
                            $endDate = new DateTime('2024-12-16 12:00:00');

                            // Get the hire_date of the authenticated user
                            $hireDate = new DateTime(Auth::user()->hire_date);

                            // Initialize the result
                            $result = 5000;

                            // Check if hire_date is greater than the comparison date
                            if ($hireDate > $comparisonDate) {
                            // Calculate the number of days between hire_date and endDate
                            $interval = $hireDate->diff($endDate);
                            $daysBetween = $interval->days;

                            // Multiply the number of days by 13.6
                            $result = $daysBetween * 13.6;
                            }
                            @endphp

                            <li><span class="dropdown-item-text flex-avl">FlexPoints Allocated: {{$result}}</span></li>
                            <li><span class="dropdown-item-text flex-uti">FlexPoints Utilized</span></li>
                            <div class="walliet-div">
                                <li class="wallet-break-line"></li>
                            </div>
                            <div class="wallet-score">

                                <div class="walliet-div">
                                    <li class="wallet-litags"><span class="dropdown-item-wallet" style="text-align: center;">*FP(FlexPoints)</span></li>
                                </div>
                            </div>
                        </ul>
                    </li>


                    <li class="nav-item dropdown profile-dropdown" style="background: none;">
                        <a class="nav-link dropdown-toggle shape-container" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex">
                            <div class="circle"> <span class="profile-img"><img src="{{ asset('assets/images//profile.jpg') }}" alt="profile" /></span>
                            </div>
                            <div class="rectangle"><span>{{ Auth::user()->fname }} <i class="fa-solid fa-list ms-2"></i></span></div>
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
                    <!-- <li>
                        <a class="dropdown-item" href="/download-pdf">Enrollment Summary</a>
                    </li> -->
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
                                <span class="profile-img"> <img src="{{asset('assets/images//before-login.png')}}" alt="profile" /></span><span>Sign In Now</span>
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
                    <button type="submit" class="btn cancel--btn d-block" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <a class="nav-link" href="/user/logout"><button type="submit" class="btn primary--btn d-block mx-auto" style="background:var(--blue); color:white">
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

<script>
    $(document).ready(function() {
        $('#pointsInfo').click(function() {
            $.ajax({
                url: '{{ route("get-points") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Handle successful response here
                    console.log(response);
                    var mapUserFYPolicyData = response.mapUserFYPolicyData;

                    // Empty the wallet-score div before adding new elements
                    $('.wallet-score').empty();

                    var totalPoints = 0; // Initialize total points counter

                    mapUserFYPolicyData.forEach(function(item) {
                        var categoryName = item.fy_policy.policy.subcategory.categories.name;
                        var points = item.points_used;

                        // Check if points are not zero
                        if (points !== 0) {
                            // Create <li> elements
                            var $categoryLi = $('<li>').addClass('wallet-litags').append(
                                $('<span>').addClass('dropdown-item-wallet').text(categoryName),
                                $('<span>').addClass('dropdown-item-wallet').css({
                                    'text-align': 'center',
                                    'font-weight': '600'
                                }).text(points)
                            );

                            var $breakLineLi = $('<li>').addClass('wallet-break-line');

                            // Create <div> elements
                            var $categoryDiv = $('<div>').addClass('walliet-div').append($categoryLi);
                            var $breakLineDiv = $('<div>').addClass('walliet-div').append($breakLineLi);

                            // Append the created elements to wallet-score div
                            $('.wallet-score').append($categoryDiv, $breakLineDiv);

                            // Add points to total
                            totalPoints += points;
                        }
                    });

                    // Add the Total and FP elements
                    var $totalLi = $('<li>').addClass('wallet-litags').append(
                        $('<span>').addClass('dropdown-item-wallet').css({
                            'font-weight': '600',
                            'color': 'black'
                        }).text('Total'),
                        $('<span>').addClass('dropdown-item-wallet').css({
                            'text-align': 'center',
                            'font-weight': '600'
                        }).text(totalPoints)
                    );
                    var $totalDiv = $('<div>').addClass('walliet-div').append($totalLi);
                    var $totalBreakLine = $('<div>').addClass('walliet-div').append($('<li>').addClass('wallet-break-line'));

                    var $fpLi = $('<li>').addClass('wallet-litags').append(
                        $('<span>').addClass('dropdown-item-wallet').css({
                            'text-align': 'center'
                        }).text('*FP(FlexPoints)')
                    );
                    var $fpDiv = $('<div>').addClass('walliet-div').append($fpLi);

                    // Append the Total and FP elements
                    $('.wallet-score').append($totalDiv, $totalBreakLine, $fpDiv);
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>