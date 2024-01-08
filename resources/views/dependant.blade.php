@extends('layouts.layout')
@php
    //dd($relation_Table);
@endphp
@section('content')
    <div id="dependants" class="pricing-tables">
        <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                    <div class="section-heading">
                        <h4>Secure your <em>loved</em> ones and avail <em>insured benefits</em></h4>
                        <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                        <div class="bg-light">The system has already uploaded your dependants' details based on the information available 
                        from the company's records. If you need to make any changes to the dependant information, please 
                        use the editing function located in the 'dependants' tab.</div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-border">
                            <div class="text-dark m-3">
                                <h5>Verify your dependant information. Please take note of the following: </h5>
                            </div>
                            <ul class="ul-points">
                                <li>All your dependants for the fiscal year 2022-23 have already been updated. Proceed to add any new dependants or if any dependant is not visible below</li>
                                <li>The maximum number of eligible dependants allowed under MyBenefits@Zoom includes the employee, spouse/partner, 2 dependant children, dependant parents, and parents-in-law</li>
                                <li>Click on the “Edit Icon” to modify the dependant details</li>
                                <li>Click on “Add New Record” to add new dependant</li>
                                <li>The nominee percentage signifies the allocation of proceeds from group term life and accident insurance among your chosen dependants</li>
                                <li>The total nominee percentage for all selected dependants must add up to 100 for the tool to permit enrollment; this is a crucial requirement</li>
                                <li>You have the option to edit dependants or indicate them as deceased. For updates to dependants previously covered, HR approval is mandatory</li>
                                <li>Adding dependants does not automatically include them in the policy. Ensure to select the dependants in the "Medical Insurance" section under the "Enrollment" tab to provide coverage in the policy</li>
                            </ul> 
                            <div class="text-info m-3">
                                Please attach the relevant government document such as Aadhar Card, PAN Card, birth certificate, or death certificate, etc., as applicable when submitting your request. The processing time for updating such dependants is 2 working days
                            </div>
                        </div>     
                    </div>  
                </div>
                @if(Auth::check())
                    <div class="row mt-5">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="section-heading">
                                <h4>Manage your Dependants</em></h4>
                                <img src="{{ asset('assets/images/heading-line-dec.png') }}" alt="">
                                <h5>Manage your dependants by adding new ones, modify existing ones</h5>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            @component('layouts.components.dependant-tabs',['relation_Table' => $relation_Table]) @endcomponent
                        </div>
                    </div>
                @endif                    
            </div>
        </div>
    </div>
@stop