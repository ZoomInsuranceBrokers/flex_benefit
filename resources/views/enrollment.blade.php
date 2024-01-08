@php
    //dd($data['dependant']);
    $tabCount = Auth::check() ? (($data['is_enrollment_window'] ? count($data['category']) : 0) + 3) : 1;
@endphp

@extends('layouts.layout')

@section('inline-page-style')
<style>
.pricing-tables .section-heading {
    margin-bottom:0px;
}
.tablinks {
    width: calc(100%/{{ $tabCount }});
}
.tablinks p {
    font-size:15px;
}
.tablinks p:before {
    font-size:22px;
}
.wrapper_tabcontent {
    padding: 20px 0px 0px 60px;
}
.wrapper_tabcontent .section-heading h4 {
    font-size:18px;
    text-transform:uppercase;
}
#enrollment-tabs .section-border {
    border-radius:0px;
    border-width:2px;
    width:100%;
}
#enrollment-tabs .tab-content-table {
    border-radius:0px;
    border-width:2px;
    width:100%;
}
#enrollment-tabs .tab-content-table thead, .enrollmentSubCategory .section-heading h4 {
    color:#fff;
    background: -moz-linear-gradient(90deg, rgba(91,104,235,1) 0%, rgba(40,225,253,1));
    background: -webkit-linear-gradient(90deg, rgba(91,104,235,1) 0%, rgba(40,225,253,1));
    background: -o-linear-gradient(90deg, rgba(91,104,235,1) 0%, rgba(40,225,253,1));
    background: -ms-linear-gradient(90deg, rgba(91,104,235,1) 0%, rgba(40,225,253,1));
    background: linear-gradient(90deg, rgba(91,104,235,1) 0%, rgba(40,225,253,1));
}
#enrollment-tabs .tab-content-table th,#enrollment-tabs .tab-content-table td {
    padding:5px 10px;
    border: 1px solid rgba(91,104,235,1);
}
#enrollment-tabs .tab-content-table tr:nth-child(even) {
    background : linear-gradient(90deg, rgba(91,104,235,0.05) 0%, rgba(40,225,253,0.05));
}

.enrollmentSubCategory .col-12:first-child {
    background-color: aliceblue;
    padding-right:0;
    padding-left:0;

}

.enrollmentSubCategory .row:first-child {
    background-color: aliceblue;
    margin-right:0;
    margin-left:0;
}

/* CARDS CUSTOMS CSS */
.card .title {
font-size:14px;
}
.card .card-body p {
    font-size:12px;
    font-weight:normal;
    color:#a2a2a2;
}
.card .list-group-item {
    font-size:11px;
}

.fp-numbers .row{
    background : linear-gradient(90deg, rgba(91,104,235,0.15) 0%, rgba(40,225,253,0.15));
    background : -moz-linear-gradient(90deg, rgba(91,104,235,0.15) 0%, rgba(40,225,253,0.15));
    background : -o-linear-gradient(90deg, rgba(91,104,235,0.15) 0%, rgba(40,225,253,0.15));
    background : -ms-linear-gradient(90deg, rgba(91,104,235,0.15) 0%, rgba(40,225,253,0.15));
    background : linear-gradient(90deg, rgba(91,104,235,0.15) 0%, rgba(40,225,253,0.15));
}
.fp-numbers .row [class^=col-]{
   /* border:1px solid rgba(91,104,235,1); */
    border:2px solid #FFF;
    text-align:center;
}
</style>
@stop

@section('content')
    <div id="enrollements" class="pricing-tables">
        <div class="container">
                <div class="row">
                    @php
                    /*    
                    <div class="col-lg-12">
                        <div class="section-heading">                        
                            <h4>Follow below instructions to complete enrollment for <em>2023-2024</em> year</h4>
                            {{-- <h4>Secure your <em>loved</em> ones and avail <em>insured benefits</em></h4> --}}
                            <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-border">
                                
                                {{-- <div class="section-heading m-3">
                                    <h6>Please note below points while reviewing your dependants</h6>
                                </div> --}}
                                <ul class="ul-points">
                                    <li>Each employee is protected with a core benefit to avoid erroneous selection.</li>
                                    <li>In addition to the core benefits, Zoom will provide all eligible employees 23,500 flex points at the beginning of the policy year i.e. 1stApril 2023 and valid till 31st March 2024</li>
                                    <li>Flex points can be used to avail additional and optional benefits</li>
                                    <li>Each flex point is equivalent to INR 1.</li>
                                    <li>Flex point allocation for new hires, during the first year, will be prorated based on the number of months left in the calendar year.</li>
                                    <li>Unused flex pointswill lapse at the end of the year and cannot be encashed.</li>
                                </ul> 
                            </div>     
                        </div>  
                    </div> 
                    */
                    @endphp                   
                    <div class="col-lg-12" id="enrollment-tabs">
                        @include('layouts.components.enrollment-tabs')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @php
        if ($data['is_enrollment_window'] && count($data['dependant'])) {
            foreach ($data['dependant'] as $depItem) {
    @endphp
            <span id="dependant-list{{ $depItem['id'] }}" style="display:none;" data-name="{{ $depItem['dependent_name'] }}"
            data-depCode="{{ $depItem['dependent_code'] }}" data-depNom="{{ $depItem['nominee_percentage'] }}"
            data-depId = "{{ $depItem['id'] }}"></span>
    @php
           }
        }
    @endphp
@stop