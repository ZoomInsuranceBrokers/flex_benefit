
<div id="content-tab-{{ $item['id'] }}" class="tab-pane fade">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-3">
                {{ $item['name'] }}
            </h1>
            
            <table class="table table-bordered table-responsive table--custom">
                <thead>
                    <tr>
                        <th>Benefit Name</th>
                        <th>Description</th>
                        <th @foreach($data['sub_categories_data'] as $subcat) @if($item['id']==$subcat->ic_id)
                            id="currSelectionHeadCol{{ $subcat->id }}"
                            @endif
                            @endforeach>Current Selection</th>
                        <th>Point Used</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($data['sub_categories_data']))
                    @foreach($data['sub_categories_data'] as $subcat)
                    @if($item['id'] == $subcat->ic_id)
                    <tr>
                        <td><a id="medicalModel">{{ $subcat->name }}</a></td>
                        <td>{{ $subcat->description }}</td>
                        <td id="currSelectionDataCol{{ $subcat->id }}">{{ array_key_exists($subcat->id, $data['currentSelectedData']) ? $data['currentSelectedData'][$subcat->id][0]['polName']  : 'N.A.' }}</td>
                        <td id="currSelectionDataVal{{ $subcat->id }}">@php $sum = 0;
                            if(array_key_exists($subcat->id, $data['currentSelectedData'])) {
                            foreach($data['currentSelectedData'][$subcat->id] as $pointRow) {
                            $sum += $pointRow['points'];
                            }
                            }
                            echo $sum;
                            @endphp</td>
                    </tr>
                    @endif
                    @endforeach
                    @else
                    <tr>
                        <td colspan="4">Missing Sub-Categories. Contact Admin for details!!</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if(count($data['sub_categories_data']))
        @foreach($data['sub_categories_data'] as $subcat)
        @if($item['id'] == $subcat->ic_id)
        <div style="display:none;" class="container enrollmentSubCategory mt-lg-3" id="subCtgryDetail{{ $subcat->id }}">
            <div class="row">
                <div class="col-12 custom-heading">
                    <div class="section-heading">
                        <h4 class="p-lg-2">{{ $subcat->name }}</h4>
                    </div>
                </div>
            </div>
            @php
            $detailPoints = explode('###', $subcat->details);
            @endphp
            @if(count($detailPoints))
            <div class="row">
                <div class="col-12">
                    <ul class="ul-points fs-16">
                        @foreach($detailPoints as $detailItem)
                        <li>{{ $detailItem }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <hr class="my-2">
                    <div class="row custom-heading">
                        <div class="section-heading">
                            <h4 class="py-1">Policy Details</h4>
                        </div>
                        <div class="col text-center">
                            <dl>
                                <dt class="col">Name</dt>
                                <dd class="col">{{ $subcat->fullname }}</dd>
                            </dl>
                        </div>
                        <div class="col text-center" id="coreMultiple{{ $subcat->id }}">
                            <dl>
                                <dt class="col">Core Multiple</dt>
                                <dd class="col">
                                    <label id="corem{{ $subcat->id }}"></label>
                                </dd>
                            </dl>
                        </div>
                        <div class="col text-center" id="coreSum{{ $subcat->id }}">
                            <dl>
                                <dt class="col">Core Sum Assured</dt>
                                <dd class="col">
                                    <label id="coresa{{ $subcat->id }}"></label>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div id="policySubCategoryList{{ $subcat->id }}" data-scid="{{ $subcat->id }}"></div>
            {{-- CARDS TEMPLATE--}}
            @php /*
            <div class="row" style="display:none;">
                <div class="col-lg-3">
                    <div class="card">
                        <img class="card-img-top" src="/assets/images/manager.png" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">GTL - Employee</h5>
                            {{-- <h6 class="card-subtitle mb-2 text-muted">Current Selection : 123456</h6> --}}
                            <p class="card-text font-weight-light">Term Life top-up options (upto 3x of salary) for employee</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Current Selection : GL - Top UP 2X salary</li>
                            <li class="list-group-item">Flex Points Used : 123456</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Show More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <img class="card-img-top" src="/assets/images/employee.png" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">GTL - Employee</h5>
                            {{-- <h6 class="card-subtitle mb-2 text-muted">Current Selection : 123456</h6> --}}
                            <p class="card-text font-weight-light">Term Life top-up options (upto 3x of salary) for employee</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Current Selection : GL - Top UP 2X salary</li>
                            <li class="list-group-item">Flex Points Used : 123456</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Show More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        {{-- <img class="card-img-top" src="..." alt="Card image cap"> --}}
                        <div class="card-body">
                            <h5 class="card-title">GTL - Employee</h5>
                            {{-- <h6 class="card-subtitle mb-2 text-muted">Current Selection : 123456</h6> --}}
                            <p class="card-text font-weight-light">Term Life top-up options (upto 3x of salary) for employee</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Current Selection : GL - Top UP 2X salary</li>
                            <li class="list-group-item">Flex Points Used : 123456</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Show More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        {{-- <img class="card-img-top" src="..." alt="Card image cap"> --}}
                        <div class="card-body">
                            <h5 class="card-title">GTL - Employee</h5>
                            {{-- <h6 class="card-subtitle mb-2 text-muted">Current Selection : 123456</h6> --}}
                            <p class="card-text font-weight-light">Term Life top-up options (upto 3x of salary) for employee</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Current Selection : GL - Top UP 2X salary</li>
                            <li class="list-group-item">Flex Points Used : 123456</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Show More</a>
                        </div>
                    </div>
                </div>
            </div> */
            @endphp

        </div>
        @endif
        @endforeach
        @endif
    </div>
</div>
</div>