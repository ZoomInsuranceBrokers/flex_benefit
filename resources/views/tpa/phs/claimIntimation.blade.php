@extends('layouts.layout')
@section('link_rel')
<link href="{{ asset('assets/css/jtable/themes/metro/blue/jtable.min.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('inline-page-style')
<style>
    .ui-dialog {
        z-index: 2 !important;
    }
</style>
@stop

@section('content')
<div id="networkHospitals" class="pricing-tables">
    <div class="container">
        <div class="row" style="height:30px;">
            <div class="col-lg-8 offset-lg-2">
                <div class="section-heading">
                    <h4><em>Claim</em> Intimation in case of <em>emergencies</em></h4>
                    <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                    {{-- <h5>Ensure nominations add upto <em>100%</em> across elected dependents</h5> --}}
                </div>
            </div>
        </div>
        {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="section-border">
                            <div class="section-heading m-3">
                                <h6>Select below values to get list of nearest hospitals nearby</h6>
                            </div>
                            <ul class="ul-points">
                                <li><b>Employee (Self)</b></li>

                                <li><b>Spouse</b> - Legally married to the employee. Spouse may include live-in partners of the opposite sex as per the corporate policy of Zoom. Spouse to also include legal guardian of adopted kids</li>

                                <li><b>Children</b> – 2 children upto 25 years of age, unmarried and must be dependent on employee for financial support, are eligible. Children definition also covers legally adopted.</li>

                                <li><b>Parents</b> - Dependent parents or parents-in law. Cross selection of 1parent and 1 parent-in-law is not applicable under plans 5-10. This is possible if the employee has declared the other parent and parent-in-law as deceased on the tool. Employee can include both parents and parents-in-law together under plan 11-13</li>

                                <li><b>LGBTQIA+</b> - (Lesbian, Gay, Bi-sexual, Transgender, Queer, Agender) - Employees are eligible to cover their partners (married or unmarried) as dependents</li>

                                <li><b>Live in Partners</b> - Employees are eligible to cover their live in partners as defined by Indian Law</li>

                            </ul>
                        </div>     
                    </div>  
                </div> --}}
        <div class="row">
            {{-- <div class="col-lg-8 offset-lg-2">
                        <div class="section-heading">
                            <h4>Select below values to get list of nearest hospitals nearby</h4>
                            <img src="{{ asset('assets/images/heading-line-dec.png') }}" alt="">
            <h5>Manage your dependents by adding new ones, modify existing ones</h5>
        </div>
    </div> --}}
    <div class="col-lg-12">
        <section class="tab-wrapper">
            <div class="tab-content">
                <!-- Tab links -->
                <div class="tabs">
                    <button class="tablinks" data-country="network-hospitals">
                        <p data-title="Network Hospitals">Claim Intimation</p>
                    </button>
                    {{-- <button class="tablinks active" data-country="add-new-dependent"><p data-title="Add New Dependent">Add New Dependent</p></button> --}}
                </div>
                <!-- Tab content -->
                <div class="wrapper_tabcontent">
                    <div id="network-hospitals" class="tabcontent active">
                        <h3>Claim Intimate</h3>

                        <form action="{{ url('/claim/initiate') }}" method="post">
                            @csrf


                            <input name="claim_type" type="hidden" value="CASHLESS">
                            <input type="hidden" name="policy_no" value="H1263493">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label form-label">Dependent Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="input4" name="dependent_name" placeholder="Enter Member Name">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label form-label">Select Relation <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <select name="dependent_relation" class="selectpicker" id="slct_relation" required>
                                                <option value="SELF">SELF</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="input002" class="col-sm-12 control-label form-label">Ailment/Disease <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input name="claim_disease" type="text" class="form-control" id="input002" placeholder="Enter Ailment/Disease" required>
                                            <span style="color: red;" id="helpBlock" class="help-block">* Details of your Disease will be confidential and will only be shared with the insurance company.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100 col-sm-2 control-label form-label">Date of Admission <span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input name="claim_date_of_admission" type="date" min='1910-01-01' value="<?php echo date("Y-m-d"); ?>" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="w-100 col-sm-2 control-label form-label">Date of Discharge:</label>
                                        <div class="col-sm-10">
                                            <input name="claim_date_of_discharge" type="date" min='1910-01-01' value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputAmount" class="col-sm-12 control-label form-label">Claim Amount</label>
                                        <div class="col-sm-12">
                                            <label class="sr-only" for="exampleInputAmount">Amount (in rupee)</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">₹</div>
                                                <input name="claim_amt" type="number" class="form-control" id="exampleInputAmount" placeholder="Amount">
                                                <div class="input-group-addon">.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="input002" class="col-sm-12 control-label form-label">Name of Doctor <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input name="claim_name_of_doctor" type="text" class="form-control" id="input002" placeholder="Enter Doctor Name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="input002" class="col-sm-12 control-label form-label">Name of Hospital <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input name="claim_name_of_hospital" type="text" class="form-control" id="input002" placeholder="Enter Hospital Name" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-2">
                                <button type="submit" id="LoadRecordsButton" class="btn btn-secondary">Intimate Claim</button>
                            </div>
                        </form>

                    </div>
                </div>

        </section>
    </div>
</div>
</div>
</div>
</div>


@stop