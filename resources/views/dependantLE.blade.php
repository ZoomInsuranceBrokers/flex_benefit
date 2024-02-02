@extends('layouts.layout')

@php
    //dd($relationLE_Table);
@endphp
@section('content')
    <div id="dependants" class="pricing-tables">
        <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                    <div class="section-heading">
                        <h4>Keep your <em>Life Events</em> updated </h4>
                        <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                        <div class="bg-light">If you recently entered marriage, experienced a change in your marital status, or 
                        welcomed a new addition to your family, you might consider adjusting your benefits to 
                        align with these life changes. Feel free to modify your dependant information in this 
                        section to ensure your benefits accurately reflect your current situation.</div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-border"> 
                            <div class="section-heading m-3">
                                <h6>How it works?</h6>
                            </div>
                            <ul class="ul-points">
                                <li>You can add the dependants within 30 days of the occurrence of the Life Event.
                                </li>
                                <li>Necessary approvals required from the company HR/Admin after submitting the required documents such as (Birth certificates, adoption records, and Marriage Certificate).
                                </li>
                            </ul>
                            <div class="section-heading m-3">
                                <h6>Who can be added?</h6>
                            </div>
                            <ul class="ul-points">
                                <li>Newly married spouse</li>
                                <li>Newborn/ adopted child</li>
                            </ul>
                        </div>     
                    </div>  
                </div>
                @if(Auth::check())
                    <div class="row mt-5">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="section-heading">
                                <h4>Manage your dependants</em></h4>
                                <img src="{{ asset('assets/images/heading-line-dec.png') }}" alt="">
                                <h5>Manage your dependants by adding new ones, modify existing ones</h5>
                            </div>
                        </div>
                        <div class="col-lg-12">

                            @include('layouts.components.dependantLE-tabs')
                        </div>
                    </div>
                @endif                    
            </div>
        </div>
    </div>
@stop