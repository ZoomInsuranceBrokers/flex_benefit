@extends('layouts.layout')
@section('link_rel')
    <link href="{{ asset('assets/css/jtable/themes/metro/blue/jtable.min.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('inline-page-style')
    <style>.ui-dialog{z-index:2 !important;}</style>
@stop
@section('document_ready')
        $('[id^=header_]').removeClass('active');
        $('#header_claim').addClass('active');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        $('#networkHos_list').jtable({
            title: 'Network Hospitals',
            toolbar:{
                show:false
            },
            paging: true,
            sorting: true,
            {{-- defaultSorting: 'Name ASC', --}}
            dialogShowEffect:'scale',
            actions: {
                listAction: '/claim/searchHospital',
            },
            fields: {
                Name: {
                    title: 'Name',
                    width: '200px',
                    create: false,
                    edit: false
                },
                Address: {
                    title: 'Address',
                    width: 'auto'
                },
                city: {
                    title: 'City',
                    width: '100px',
                },
                state: {
                    title: 'State',
                    width: '70px',
                },
                pincode: {
                    title: 'Pincode',
                    width: '70px',
                },
                phone: {
                    title: 'Contact',
                    width: '150px'
                },
                location: {
                    title: 'Location',
                    width: '70px',
                }
            }
        });
            
        //Re-load records when user click 'load records' button.
        $('#LoadRecordsButton').click(function (e) {
            e.preventDefault();
            $('#networkHos_list').jtable('load', {
                name: $('#netHosName').val(),
                city: $('#netHosCity').val(),
                state: $('#netHosState').val()
            });
        });

        //Load all records when page is first shown
        $('#LoadRecordsButton').click();
    
@endsection

@section('content')
    <div id="networkHospitals" class="pricing-tables">
        <div class="container">
                <div class="row" style="height:30px;">
                    <div class="col-lg-8 offset-lg-2">
                    <div class="section-heading">
                        <h4><em>network</em> hospitals in case of <em>emergencies</em></h4>
                        <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
                        {{-- <h5>Ensure nominations add upto <em>100%</em> across elected dependants</h5> --}}
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

                                <li><b>Children</b> – 2 children upto 25 years of age, unmarried and must be dependant on employee for financial support, are eligible. Children definition also covers legally adopted.</li>

                                <li><b>Parents</b> - Dependant parents or parents-in law. Cross selection of 1parent and 1 parent-in-law is not applicable under plans 5-10. This is possible if the employee has declared the other parent and parent-in-law as deceased on the tool. Employee can include both parents and parents-in-law together under plan 11-13</li>

                                <li><b>LGBTQIA+</b> - (Lesbian, Gay, Bi-sexual, Transgender, Queer, Agender) - Employees are eligible to cover their partners (married or unmarried) as dependants</li>

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
                            <h5>Manage your dependants by adding new ones, modify existing ones</h5>
                        </div>
                    </div> --}}
                    <div class="col-lg-12">
                            <section class="tab-wrapper">
                            <div class="tab-content">
                                <!-- Tab links -->
                                <div class="tabs">
                                    <button class="tablinks" data-country="network-hospitals"><p data-title="Network Hospitals">Network Hospitals</p></button>
                                    {{-- <button class="tablinks active" data-country="add-new-dependant"><p data-title="Add New Dependant">Add New Dependant</p></button> --}}
                                </div>
                                <!-- Tab content -->
                                <div class="wrapper_tabcontent">
                                    <div id="network-hospitals" class="tabcontent active">
                                        <h3>Hospitals</h3>
                                        <div class="row mb-3">
                                            <form>
                                            <div class="col-3 offset-1 pt-2">Name: <input type="text" name="name" id="netHosName" /></div>
                                            <div class="col-3 pt-2">City: <input type="text" name="city" id="netHosCity" /></div>
                                            <div class="col-3 pt-2">State: <input type="text" name="state" id="netHosState" /></div>
                                            <div class="col-2">
                                                <button type="submit"id="LoadRecordsButton" class="btn btn-secondary">Load records</button>
                                            </div>
                                            </form>
                                        </div>
                                        <div id="networkHos_list"></div>
                                    </>
                                </div>
                            </div>
                            </section>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop