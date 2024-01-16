<style>
  .main-para {
    display: list-item;
    text-align: -webkit-match-parent;
    padding: 0;
    margin: 0;
    font-size: 12px;
    font-family: roboto, sans-serif;
    margin-left: 10px;
  }

  .para {
    padding: 0;
    margin: 0;
    font-size: 14px;
    font-family: roboto, sans-serif;
    font-weight: 400;
    color: #000000;
    line-height: 20px;
  }

  .li-image {
    height: 10px;
    width: 10px;
  }

  .li-items {
    display: flex;
    margin-right: 10px;
  }
</style>
<div class="benefits section">
  <div class="container" style="display: none;">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="section-heading  wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.5s">
          <!-- <h4>Amazing <em>Services &amp; Features</em> for you</h4> -->
          <h4>Choose <em>any level</em> of benefits <em>every year</em></h4>
          <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4 offset-lg-1">
        <div class="section-heading  wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.5s">
          <!-- <h4>Amazing <em>Services &amp; Features</em> for you</h4> -->
          <h4><em>Core</em> Benefits</h4>
          <p><em>Mandatory protection provided to each employee:</em></p>
          <div class="box-item">
            <h4><a href="#">Medical Insurance</a></h4>
            <p>Employee Only - 5 lacs</p>
          </div>
          <div class="box-item">
            <h4><a href="#">Accident Insurance</a></h4>
            <p>As per Grade</p>
          </div>
          <div class="box-item">
            <h4><a href="#">Term Life Insurance</a></h4>
            <p>3x Base Salary</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 offset-lg-1">
        <div class="section-heading  wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.5s">
          <!-- <h4>Amazing <em>Services &amp; Features</em> for you</h4> -->
          <h4><em>Default</em> Benefits</h4>
          <p><em>For new employee lorem, and for existing employee below:</em></p>
          <div class="box-item">
            <h4><a href="#">Group Medical</a></h4>
            <p>Employee(E) + Spouse(S) + 2 Children(C) of 5 Lacs</p>
          </div>
          <div class="box-item">
            <h4><a href="#">Group Accidental</a></h4>
            <p>12 Lacs to 1 Cr - Based on Grade</p>
          </div>
          <div class="box-item">
            <h4><a href="#">Group Term Life</a></h4>
            <p>3x Base Salary</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container pt-5" id="plan-categories">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="section-heading  wow fadeInDown" data-wow-duration="1s" data-wow-delay="0.5s">
          <h4>Plan Categories under <em>MyBenefits@Zoom</em> program</h4>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3">
        <div class="service-item first-service">
          <div class="icon"></div>
          <h4>Medical Insurance Plans</h4>
          <ul class="service-item-list">
            <li>In-patient hospitalization</li>
            <li>Day-care procedures through the Group Medical Plans</li>
            <li>Cashless & reimbursement claims</li>
          </ul>
          <div class="text-button">
            <a href="#plan-categories" onclick="serviceShowMore('medical-insurance-plan')">Read More <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="service-item second-service">
          <div class="icon"></div>
          <h4>Term Life Insurance Plans</h4>
          <ul class="service-item-list">
            <li>Option to select a basic Term Life Plan</li>
            <li>24/7 worldwide coverage</li>
          </ul>
          <!-- <ul class="service-item-list">
              <li>In-Patient Treatment i.e. Hospitalization</li>
              <li>Daycare procedures through Group Medical Plan</li>
              <li>Out-Patient treament  under Group OPD Plan</li>
            </ul> -->
          <div class="text-button">
            <a href="#plan-categories" onclick="serviceShowMore('term-life-insurance-plan')">Read More <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="service-item third-service">
          <div class="icon"></div>
          <h4>Accidental Insurance Plans</h4>
          <ul class="service-item-list">
            <li>Cash benefits for accidental injuries and disabilities</li>
            <li>Option to enhance the coverage upto 30 lakhs</li>
            <li>Can also purchase additional accidental insurance for their spouse</li>
          </ul>
          <div class="text-button">
            <a href="#plan-categories" onclick="serviceShowMore('accidental-insurance-plan')">Read More <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="service-item fourth-service">
          <div class="icon"></div>
          <h4>Non Insured Benefits</h4>
          <ul class="service-item-list">
            <li>Leverage your FlexPoints for specific flexible cash advantage & wellness services</li>
            <li>These points can be used throughout the year</li>
            <li>Pro-rata basis for new employees</li>
          </ul>
          <div class="text-button">
            <a href="#plan-categories" onclick="serviceShowMore('flexi-cash-benefit')">Read More <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- Servies read more section--}}
  <div class="container service-more-container">
    <div class="row">
      <div id="medical-insurance-plan" class="col-lg-12 mt-3 mb-3 service-item service-more">
        <h4>Medical Insurance Plans</h4>
        <p>
          The MyBenefits@Zoom initiative provides insurance solutions encompassing hospitalization for in-patient medical care and certain day-care procedures through the Group Medical Plans. Additionally, employees have the option to augment the terms of their Group Medical plans by selecting supplementary modules tailored for specific treatments or procedures. Presently, the program offers a selection of 13 Group Medical Plans.
          It's important to note that coverage is applicable only for treatments conducted within India. For more details, please click here.
        </p>
        <div class="text-button float-right">
          <a href="#">Show Less<i class="fa fa-arrow-left"></i></a>
        </div>
      </div>
      <div id="flexi-cash-benefit" class="col-lg-12 mt-3 mb-3 service-item service-more">
        <h4>Flexi Cash Benefit</h4>
        <p>In addition to the benefits covered by insurance, you have the opportunity to leverage your FlexPoints for specific flexible cash advantages through reimbursement & wellness services. A comprehensive list of these flexible cash benefits & wellness services, including eligibility criteria and entitlement details, can be found below. </p>
        <h5>When is the window for selecting flexible cash benefits?</h5>
        <p>Please be aware that you are only eligible to choose flexible cash benefits and allocate points if your point balance is positive. Once allocated, these points can be used throughout the year. For new employees, you can assign your FlexPoints, distributed on a pro-rata basis. </p>
        <div class="text-button float-right">
          <a href="#">Show Less<i class="fa fa-arrow-left"></i></a>
        </div>
      </div>
      <div id="term-life-insurance-plan" class="col-lg-12 mt-3 mb-3 service-item service-more">
        <h4>Term Life Insurance Plans</h4>
        <p>
          Ensuring your family's financial well-being in unforeseen circumstances is a crucial aspect of financial planning. Employees have the option to opt for a basic Term Life Plan, covering both themselves and their spouses. This plan serves as a financial safety net for the family in the event of an unfortunate incident resulting in the loss of the insured employee or spouse.

        </p>
        <div class="text-button float-right">
          <a href="#">Show Less<i class="fa fa-arrow-left"></i></a>
        </div>
      </div>
      <div id="accidental-insurance-plan" class="col-lg-12 mt-3 mb-3 service-item service-more">
        <h4>Accidental Insurance Plans</h4>
        <p>
          Accidental Insurance serves as a financial safety net, shielding you from unforeseen economic strain in the event of an accident involving you or your spouse. This insurance offers cash benefits for accidental injuries and disabilities, complementing your primary medical plan.
          Payments are made independent of any other existing insurance plans you may hold. Employees have the option to augment their accident coverage by up to 30 lakh and can also purchase additional accidental insurance for their spouse, providing coverage up to 50 lakh.
          This coverage is applicable globally, providing around-the-clock protection.
        </p>
        <div class="text-button float-right">
          <a href="#">Show Less<i class="fa fa-arrow-left"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="about" class="about-us section">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="section-heading" style="margin-bottom: 10px;">
          <h4> <em>Tax implications</em> of my contributions</h4>
          <!-- <img src="assets/images/heading-line-dec.png" alt=""> -->
          <p class="para">If you utilize the points allocated by the company and acquire extra coverage or enhanced insured limits, the corresponding additional premium will be deducted directly from your salary. The premium you contribute encompasses an 18% GST.</p>
        </div>
        <div class="row">
          <div class="col-lg-6" style="display: none;">
            <div class="box-item">
              <h4><a href="#">Maintance Problems</a></h4>
              <p>Lorem Ipsum Text</p>
            </div>
          </div>
          <div class="col-lg-6" style="display: none;">
            <div class="box-item">
              <h4><a href="#">24/7 Support &amp; Help</a></h4>
              <p>Lorem Ipsum Text</p>
            </div>
          </div>
          <div class="col-lg-6" style="display: none;">
            <div class="box-item">
              <h4><a href="#">Fixing Issues About</a></h4>
              <p>Lorem Ipsum Text</p>
            </div>
          </div>
          <div class="col-lg-6" style="display: none;">
            <div class="box-item">
              <h4><a href="#">Co. Development</a></h4>
              <p>Lorem Ipsum Text</p>
            </div>
          </div>
          <div class="col-lg-12">
            <p class="para"><b>You can leverage tax advantages on your premium contributions according to the following sections of the income tax code:</b></p>
            <ul class="service-item-list" style="margin-top: 10px;">
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Health insurance for self, spouse, children, and parents falls under section 80D. However, the selection of parents-in-law is ineligible for a tax rebate under section 80D.</li>
              </div>
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Flex Modules and OPD Plan qualify under section 80D.</li>
              </div>
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Life insurance for self and spouse falls under section 80C.</li>
              </div>
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Critical Illness coverage for self is eligible for a tax benefit under section 80C.</li>
              </div>
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Personal Accident coverage for self or spouse does not offer any income tax benefits.</li>
              </div>
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Certain Flex Cash benefits may attract perquisite taxes, which will be deducted from your salary.</li>
              </div>
              <div class="li-items">
                <img class="li-image" src="{{asset('assets/images/flex-icon.png')}}" alt="">
                <li class="main-para"> Form 16 will be updated to accurately reflect the above purchases.</li>
              </div>

            </ul>

            <!-- <div class="gradient-button">
              <a href="#">Start 14-Day Free Trial</a>
            </div> -->
          </div>
        </div>
      </div>
      <div class="col-lg-6" style="height: 300px;">
        <!-- Adjust the value (300px) as needed -->
        <div class="right-image">
          <img src="assets/images/tax-vector.png" alt="" style="max-height: 400px;     max-width: fit-content; margin-left: 150px; margin-top: -64px;">
        </div>
      </div>

    </div>
  </div>
</div>
<script>
  function serviceShowMore(id) {
    $('.service-more').hide();
    $('.service-more-container').show();
    $('.service-more-container .row').show();
    $('#' + id).toggle().focus();
  }
</script>