@extends('layouts.layout')
@section('content')
    <div id="dependents" class="pricing-tables">
        <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                    <div class="section-heading">
                        <h4>Secure your <em>loved</em> ones and avail <em>insured benefits</em></h4>
                        <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                        <div class="bg-light">The system has already uploaded your dependents' details based on the information available 
                        from the company's records. If you need to make any changes to the dependent information, please 
                        use the editing function located in the 'Dependents' tab.</div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-border">
                            <div class="text-dark m-3">
                                <h5>Verify your dependent information. Please take note of the following: </h5>
                            </div>
                            <ul class="ul-points">
                                <li>All your dependents for the fiscal year 2022-23 have already been updated. Proceed to add any new dependents or if any dependent is not visible below</li>
                                <li>The maximum number of eligible dependents allowed under MyBenefits@Zoom includes the employee, spouse/partner, 2 dependent children, dependent parents, and parents-in-law</li>
                                <li>Click on the “Edit Icon” to modify the dependent details</li>
                                <li>Click on “Add New Record” to add new dependent</li>
                                <li>The nominee percentage signifies the allocation of proceeds from group term life and accident insurance among your chosen dependents</li>
                                <li>The total nominee percentage for all selected dependents must add up to 100 for the tool to permit enrollment; this is a crucial requirement</li>
                                <li>You have the option to edit dependents or indicate them as deceased. For updates to dependents previously covered, HR approval is mandatory</li>
                                <li>Adding dependents does not automatically include them in the policy. Ensure to select the dependents in the "Medical Insurance" section under the "Enrollment" tab to provide coverage in the policy</li>
                            </ul> 
                            <div class="text-info m-3">
                                Please attach the relevant government document such as Aadhar Card, PAN Card, birth certificate, or death certificate, etc., as applicable when submitting your request. The processing time for updating such dependents is 2 working days
                            </div>
                        </div>     
                    </div>  
                </div>
                @if(Auth::check())
                    <div class="row mt-5">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="section-heading">
                                <h4>Manage your Dependents</em></h4>
                                <img src="{{ asset('assets/images/heading-line-dec.png') }}" alt="">
                                <h5>Manage your dependents by adding new ones, modify existing ones</h5>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            @component('layouts.components.dependent-tabs', ['result' => $result]) @endcomponent
                        </div>
                    </div>
                @endif                    
            </div>
        </div>
    </div>
@stop