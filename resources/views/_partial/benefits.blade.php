<div class="benefits section">
    <div class="container">
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
            <h4>Plan Categories under <em>MyBenefit@Zoom</em> program</h4>
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
              <li>Option to select Critical Illness rider</li>
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
            <h4>Flexi Cash Benefits</h4>
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
          <div id="flexi-cash-benefit"  class="col-lg-12 mt-3 mb-3 service-item service-more">
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
              Furthermore, employees have the opportunity to select the Critical Illness rider, offering financial assistance to address their treatment expenses. Both the term and critical illness plans provide continuous, worldwide coverage 24 hours a day, seven days a week.
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
<script>
function serviceShowMore(id) {
  $('.service-more').hide();
  $('.service-more-container').show();
  $('.service-more-container .row').show();
  $('#'+id).toggle().focus();}
</script>