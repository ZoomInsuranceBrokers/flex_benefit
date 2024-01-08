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
                    <h4><em>Track</em> your all claims <em>here</em></h4>
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
                        <p data-title="Claim Status">Claim Status</p>
                    </button>
                    {{-- <button class="tablinks active" data-country="add-new-dependent"><p data-title="Add New Dependent">Add New Dependent</p></button> --}}
                </div>
                <!-- Tab content -->
                <div class="wrapper_tabcontent">
                    <div id="network-hospitals" class="tabcontent active">
                        <h3>Claim Status</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">

                                    <div class="panel-title">

                                    </div>

                                    <div class="panel-body table-responsive">

                                        <table id="example0" class="table display">
                                            <thead>
                                                <tr>
                                                    <td>Sr.No</td>
                                                    <td>Policy Name</td>
                                                    <td>Policy No.</td>
                                                    <td>Patient Name</td>
                                                    <td>Patient Relation</td>
                                                    <td>Claim Amount</td>
                                                    <td>Claim Status</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($claims as $claim)
                                                <tr>
                                                    @if($claim['message'] == "Claim data not found !!")
                                                    <td colspan="7" class="text-center"><b>{{ "Claim data not found !!" }}</b></td>
                                                    @else
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><b>{{ $claim['policy_name'] }}</b></td>
                                                    <td><b>{{ $claim['policy_number'] }}</b></td>
                                                    <td><b>{{ $claim['patient_name'] }}</b></td>
                                                    <td><b>{{ $claim['patient_relation'] }}</b></td>
                                                    <td><b>{{ !empty($claim['claim_amount']) ? $claim['claim_amount'] : 0 }}</b></td>
                                                    <td><b>{{ $claim['claim_status'] }}</b></td>
                                                    @endif
                                                </tr>
                                                @endforeach


                                            </tbody>
                                        </table>

                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>

        </section>
    </div>
</div>
</div>
</div>
</div>


@stop