<!-- Banner Section Start -->
<style>
    .banner__section .left-cols {
        background: url('{{ asset('assets/images/1.png') }}');
        background-repeat: no-repeat;
        position: relative;
        height: 70vh;
        /* top: 73px; */
    }

    .right-col {
        background: url('{{ asset('assets/images/bg-icon1.png') }}') center no-repeat;
        background-size: 300px;
    }
</style>
<section class="banner__section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6 left-cols"></div>

            <div class="col-xl-6 ms-auto right-col">
                <div class="new-banner">
                    <h1><span class="color-black">My Benefits,</span> My Way</h1>
                    <p>
                        Empower your choices with MyBenefits@Zoom, offering enhanced
                        flexibility and a personalized approach to shaping your perks
                        package. Annually, you'll have the opportunity to curate your
                        benefits portfolio during the enrollment period. The new benefit
                        year will start from 17th Dec 2023 & will be valid till 16th Dec
                        2024. The benefits you choose will be applicable for the same
                        period.
                    </p>
                    {{-- <button class="login-btn">Login Now</button> --}}
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<!-- Work Section Start -->
<section>
    <div class="row">
        <div class="col-lg-6 m-0 p-0 work-bg">
            <div class="work p-5">
                <div>
                    <h2 class="text-center">How it works?</h2>
                    <p class="side-border">
                        Every employee is automatically enrolled in a foundational set
                        of Core Benefits to prevent inadvertent choices. In addition to
                        this, you will be granted 5000 FlexPoints( 1 flex point to 1 INR
                        ) for a personalized touch to your benefits package. These
                        FlexPoints allow you to tailor your coverage by selecting from
                        various options available. Should the selected insured benefits
                        surpass the available FlexPoints, any excess will be covered
                        through salary contributions deducted via payroll.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 m-0 p-0 hire-bg">
            <div class="hire">
                <div>
                    <h2 class="text-center">for new hire</h2>
                    <p class="gray-border">
                        For new hires enrolling after December 17, 2023, your coverage
                        will commence from your joining date and extend through 16th Dec
                        2024 of the subsequent year. The FlexPoints will be prorated
                        based on the joining date.
                    </p>
                    <p class="custom-text">To know more please reach out to us at</p>
                    <a href="#" class="white-btn">support_mybenefits@zoominsurancebrokers.com</a>
                </div>
            </div>
        </div>
    </div>
</section>
