@extends('layouts.layout')

@php
    //dd($relationLE_Table);
@endphp
@section('content')
    <div id="dependents" class="pricing-tables">
        <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                    <div class="section-heading">
                        <h4>Keep your <em>Life Events</em> updated </h4>
                        <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                        <h5>Ensure nominations add upto <em>100%</em> across elected dependents</h5>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-border">
                            <div class="section-heading m-3">
                                <h6>Who all can be your secured dependents under Zoom Benefits!!</h6>
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

                            @include('layouts.components.dependentLE-tabs')
                        </div>
                    </div>
                @endif                    
            </div>
        </div>
    </div>
@stop