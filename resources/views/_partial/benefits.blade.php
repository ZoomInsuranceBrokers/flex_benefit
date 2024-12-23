<style>
    .play-button {
        position: absolute;
        top: 62%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 8%;
        box-shadow: none !important;
        /* or box-shadow: 0; */
        /* Adjust size as needed */
    }

    .setsize {

        font-weight: 600;
        color: var(--blue);
    }

    .services {
          transition: 1s ease;
      }

      .services:hover {
          -webkit-transform: scale(1.1);
          -ms-transform: scale(1.1);
          transform: scale(1.1);
          transition: 1s ease;
      }
</style>
<section>
    <div class="row mt-4">
        <h2 class="text-center setsize">
            Know your benefits
        </h2>
    </div>
    <div class="container">

        <div class="videoModal mt-4">
            <a href="#" id="openVideoModal">
                <img src="{{asset('assets/images/homepage.jpg')}}" style="border-radius: 25px;" alt="poster image">
                <img src="{{asset('assets/images/playbutton.png')}}" class="play-button" alt="poster image">
            </a>
        </div>
    </div>
</section>

<!-- Modal Popup for Video -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div>
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-12">
                        <video id="myVideo" height="400" controls controlsList="nofullscreen nodownload nopicure-in-picture">
                            <source src="{{asset('assets/videos/benfitsUsingZoom.mp4')}}" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('openVideoModal').addEventListener('click', function(event) {
        event.preventDefault();
        $('#videoModal').modal('show'); // Show the modal
        document.getElementById('myVideo').play(); // Play the video
    });
    $(document).ready(function() {
        $('#videoModal').on('hidden.bs.modal', function() {
            document.getElementById('myVideo').pause(); // Pause the video
        });
    });
</script>


<!--End Modal Popup for Video -->

<section id="plan" class="plan mb-4">
    <div class="custom-container">
        <div class="row mt-4">

            <h2 class="text-center">
                Plan Categories Under <span class="color-black">MyBenefits@Zoom Program</span>
            </h2>
        </div>
        <div class="row mt-4" style="padding:0vw 5vw;">
            <div class="col-lg-3 service-col">
                <div class="services">
                    <div class="card-items">
                        <div class="card-list">
                            <div class="service-img">
                                <img src="{{ asset('assets/images/close-up-doctor-paper-family.jpg') }}" alt="" class="service-main m-0 p-0" style=" box-shadow: -2px 5px 6px dimgray;" />
                            </div>
                            <div class="img-icon">
                                <img src="{{ asset('assets/images/icon 1-01.png') }}" alt="icon" />
                            </div>
                            <h3>Medical Insurance Plans</h3>
                        </div>
                    </div>
                </div>
                <div class="medical-info">
                    <p>
                        The MyBenefits@Zoom initiative provides insurance solutions
                        encompassing hospitalization for in-patient medical care and
                        certain day-care procedures through the Group Medical Plans.
                        Additionally, employees have the option to augment the terms of
                        their Group Medical plans by selecting supplementary modules
                        tailored for specific treatments or procedures. Presently, the
                        program offers a selection of 13 Group Medical Plans. It's
                        important to note that coverage is applicable only for
                        treatments conducted within India. For more details, please
                        click here.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 terms-col">
                <div class="services">
                    <div class="card-items">
                        <div class="card-list">
                            <div class="service-img">
                                <img src="{{ asset('assets/images/term-life.jpg') }}" alt="" class="service-main m-0 p-0" style=" box-shadow: -2px 5px 6px dimgray;" />
                            </div>
                            <div class="img-icon">
                                <img src="{{ asset('assets/images/icon 2-01.png') }}" alt="icon" />
                            </div>
                            <h3>Term Life Insurance Plans</h3>
                        </div>
                    </div>
                </div>
                <div class="medical-info">
                    <p>
                        Ensuring your family's financial well-being in unforeseen circumstances is a crucial aspect of financial planning. Employees have the option to opt for a basic Term Life Plan, covering both themselves and their spouses. This plan serves as a financial safety net for the family in the event of an unfortunate incident resulting in the loss of the insured employee or spouse.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 plans-col">
                <div class="services">
                    <div class="card-items">
                        <div class="card-list">
                            <div class="service-img">
                                <img src="{{ asset('assets/images/col-img4.jpg') }}" alt="" class="service-main m-0 p-0" style=" box-shadow: -2px 5px 6px dimgray;"/>
                            </div>
                            <div class="img-icon">
                                <img src="{{ asset('assets/images/icon 3-01.png') }}" alt="icon" />
                            </div>
                            <h3>Accidental Insurance Plans</h3>
                        </div>
                    </div>
                </div>
                <div class="medical-info">
                    <p>
                        Accidental Insurance serves as a financial safety net, shielding you from unforeseen economic strain in the event of an accident involving you or your spouse. This insurance offers cash benefits for accidental injuries and disabilities, complementing your primary medical plan. Payments are made independent of any other existing insurance plans you may hold. Employees have the option to augment their accident coverage by up to 30 lakh and can also purchase additional accidental insurance for their spouse, providing coverage up to 50 lakh. This coverage is applicable globally, providing around-the-clock protection.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 non-col">
                <div class="services">
                    <div class="card-items">
                        <div class="card-list">
                            <div class="service-img">
                                <img src="{{ asset('assets/images/main-banner3.jpeg') }}" alt="" class="service-main m-0 p-0" style=" box-shadow: -2px 5px 6px dimgray;"/>
                            </div>
                            <div class="img-icon">
                                <img src="{{ asset('assets/images/icon 4-01.png') }}" alt="icon" />
                            </div>
                            <h3>Non Insured Benefits</h3>
                        </div>
                    </div>
                </div>
                <div class="medical-info popup-leftside">
                    <p>
                        Flexi Cash Benefit
                        In addition to the benefits covered by insurance, you have the opportunity to leverage your FlexPoints for specific flexible cash advantages through reimbursement & wellness services. A comprehensive list of these flexible cash benefits & wellness services, including eligibility criteria and entitlement details, can be found below.
                        When is the window for selecting flexible cash benefits?
                        Please be aware that you are only eligible to choose flexible cash benefits and allocate points if your point balance is positive. Once allocated, these points can be used throughout the year. For new employees, you can assign your FlexPoints, distributed on a pro-rata basis.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tax Section Start -->
<section>
    <div class="main-footer">
        <div class="row" style="background: #30616e;">
            <div class="col-xl-8" style="padding: 3rem 5rem 3rem 5rem;">
                <h2>Tax Implications Of My Contributions</h2>
                <p>
                    If you utilize the points allocated by the company and acquire
                    extra coverage or enhanced insured limits, the corresponding
                    additional premium will be deducted directly from your salary. The
                    premium you contribute encompasses an 18% GST.
                </p>
                <p>
                    <b>
                        You can leverage tax advantages on your premium contributions
                        according to the following sections of the income tax code:
                    </b>
                </p>
                <ul class="star-icon text-white white-icon">
                    <li>
                        Health insurance for self, spouse, children, and parents falls
                        under section 80D. However, the selection of parents-in-law is
                        ineligible for a tax rebate under section 80D.
                    </li>
                    <li>Flex Modules and OPD Plan qualify under section 80D.</li>
                    <li>
                        Life insurance for self and spouse falls under section 80C.
                    </li>
                    <li>
                        Critical Illness coverage for self is eligible for a tax benefit
                        under section 80C.
                    </li>
                    <li>
                        Personal Accident coverage for self or spouse does not offer any
                        income tax bene
                    </li>
                    <li>
                        Certain Flex Cash benefits may attract perquisite taxes, which
                        will be deducted fro
                    </li>
                    <li>
                        Form 16 will be updated to accurately reflect the above
                        purchases.
                    </li>
                </ul>
            </div>
            <div class="col-xl-4 tax-banner d-none d-xl-block" style="">
                <div class="tax-img">
                    <img src="{{ asset('assets/images/tax-bg1.png') }}" alt="img" />
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Tax Section End -->

<script>
    window.onload = function() {
        var video = document.getElementById("myVideo");
        video.removeAttribute("controls");
        video.controls = true;

        video.addEventListener("loadedmetadata", function() {
            // Remove the fullscreen button
            var fullscreenButton = video.parentElement.querySelector(".fullscreen-button");
            if (fullscreenButton) {
                fullscreenButton.style.display = "none";
            }
        });
    };
</script>