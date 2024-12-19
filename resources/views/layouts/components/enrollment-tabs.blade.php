@php
$formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
@endphp

<style>
    .custom-heading .section-heading {
        background: var(--blue);
        padding: 6px;
        margin-bottom: 5px;
    }

    .custom-heading .section-heading h4 {
        font-size: 20px;
        color: var(--white);
    }

    .custom-heading dl {
        background-color: #d4f2ff;
        padding: 10px;
        margin-bottom: 5px;
    }

    .custom-heading .col-4 dl {
        background-color: #d4f2ff;
        padding: 10px;
        height: 100%;
    }

    .custom-heading .col-4 {
        margin-bottom: 5px;
    }

    .custom-heading .col-left dl {
        margin-right: 5px;
    }

    .custom-heading .col-right dl {
        margin-left: 5px;
    }

    .additional-table .table {
        --bs-table-border-color: #0fa2d5;
    }

    .container1 {
        position: relative;


    }

    .horizontal-list {
        list-style-type: none;
        margin: 0;
        padding-bottom: 5px;
        display: flex;
        white-space: nowrap;
        flex-wrap: nowrap;
        overflow: hidden;
    }

    .horizontal-list li {
        display: inline-block;
        /* margin-right: 10px; */
        /* Adjust spacing between items */
    }

    .nav-item button:hover {
        -webkit-transform: scale(1.09);
        -ms-transform: scale(1.09);
        transform: scale(1.09);
        transition: 1s ease;
    }

    .nav-tabs#enrolTabs li .nav-link {
        transition: 1s ease;
    }



    .arrow:hover {
        opacity: 1;

    }

    /* .arrow.disabled {
        cursor: default;
        
    }  */

    .left-arrow,
    .right-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: #EEEBEB;
        border: none;
        padding: 10px;
        border-radius: 50%;
        cursor: pointer;
    }

    .left-arrow {
        left: -3vw;
    }

    .right-arrow {
        right: -3vw;
    }
</style>
<!-- <a id="enrollmentModal_trigger" style="display:none;" href="#launchEnrollmentModal">modalEnrollment</a> -->
<div class="modal" id="launchEnrollmentModal" style="display:none; position: fixed; top: 70%; left: 60%; transform: translate(-50%, -50%);">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close" onclick="updatePoints()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        {{-- <a class="btn-danger btn" href="/logout"></a> --}}
        <button class="btn-info btn modal_close" data-dismiss="modal" onclick="updatePoints()"></button>
      </div>
    </div>
  </div>
</div>


<section class="enroll-banner  px-2 px-md-0" style="padding-bottom: 10px;">
    <div class="container bg-white container-card">
        <div class="container1">
            <ul class="nav nav-tabs horizontal-list" id="enrolTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="howwork-tab" data-bs-toggle="tab" data-bs-target="#howwork" type="button" role="tab" aria-controls="howwork" aria-selected="true">
                        <img src="{{ asset('assets/images/icon1.png') }}" alt="query icon" />
                        How It Works
                    </button>
                </li>
                @if (Auth::check())
                @if ($data['is_enrollment_window'])
                @foreach ($data['category'] as $item)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tabLink{{ $item['id'] }}" data-bs-toggle="tab" data-bs-target="#content-tab-{{ $item['id'] }}" type="button" role="tab" aria-controls="content-tab-{{ $item['id'] }}" aria-selected="false">
                        <img src="{{ asset('assets/images/icon-plus.png') }}" alt="query icon" />
                        {{ $item['name'] }}
                    </button>
                </li>
                @endforeach
                @endif
                @php
                $current_fy_id = collect($data['fyData'])->firstWhere('is_active', 1)['id'] ?? 0;
                @endphp

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="Summary-tab{{ base64_encode($current_fy_id) }}" data-bs-toggle="tab" data-id="{{ base64_encode($current_fy_id) }}" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="false">
                        <img src="{{ asset('assets/images/icon-img5.png') }}" alt="query icon" />
                        Summary
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#enrollment-history" type="button" role="tab" aria-controls="enrollment-history" aria-selected="false">
                        <img src="{{ asset('assets/images/icon-plus.png') }}" alt="query icon" />
                        Enrollment History
                    </button>
                </li>
                @endif
            </ul>
            <button class="arrow left-arrow">&lt;</button>
            <button class="arrow right-arrow">&gt;</button>
        </div>
    </div>
</section>

<section>
    <div class="col-11">
        <div class="tab-content" id="tabcontent_section">
            <div class="tab-pane fade show active" id="howwork" role="tabpanel" aria-labelledby="howwork-tab">
                @include('_partial.how-it-works')
            </div>

            @if($data['is_enrollment_window'] && count($data['category']))
                @foreach($data['category'] as $item)
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
                                            <th @foreach($data['sub_categories_data'] as $subcat)
                                                @if($item['id']==$subcat->ic_id)
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
                                                        <td><a id="enrollmentSubCategory{{ $subcat->id }}" data-cat-id="{{ $subcat->id }}" href="javascript:return false;">{{ $subcat->name }}</a></td>
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
                        </div>

                        @if(count($data['sub_categories_data']))
                            @foreach($data['sub_categories_data'] as $subcat)
                                @if($item['id'] == $subcat->ic_id)
                                    <div style="display:none;" class="container enrollmentSubCategory mt-lg-3" id="subCtgryDetail{{ $subcat->id }}">
                                        <div class="row">
                                            <div class="col-12 custom-heading">
                                                <div class="section-heading">
                                                    <h4 class="p-lg-2">{{ $subcat->name }}</h4>
                                                    <img src="{{asset('assets/images/heading-line-dec.png') }}" alt="">
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
                                        {{-- Paste your card template code here --}}
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach 
            @else
                <div class="tab-pane fade" id="no-category" role="tabpanel">
                    No Category Found. Wrong Setup.
                </div>
            @endif

            <div class="tab-pane fade show" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                <h3>Summary</h3>
                <div id="summary_content"></div>
                @if(!session('is_submitted'))
                <div class="final-button">
                    <h5 class="text-secondary" style="text-align:right;">Make your decision <em>FINAL</em> by clicking 
                        <a href="#" class="btn btn-primary" id="finalSubmit_trigger">Final Submission</a>
                    </h5>
                </div>
                @endif
            </div>

            <div id="enrollment-history" class="tab-pane fade show" role="tabpanel" aria-labelledby="summary-tab">
                <h3>History</h3>
                @include('_partial.enrollment-history')
            </div>
        </div>
    </div>
</section>








<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
      document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('finalSubmit_trigger').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default action (opening modal)

            Swal.fire({
                title: 'Do you really want to submit?',
                text: 'This step is IRREVERSIBLE. Once submission is done, no further modification is possible until next year!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/enrollment/finalSubmit',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            is_submitted: 1
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.status) {
                                $(".final-button").hide();
                                Swal.fire('Submitted!', 'Your decision has been successfully submitted.', 'success');
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });

                }
            });
        });
    });

    function resetSelection(catId, buttonObj) {
        Swal.fire({
            title: "Are you sure?",
            text: "Resetting the previous selection will discard all changes. Do you want to proceed?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/enrollment/resetCategory",
                    type: "POST",
                    data: {
                        "_token": '{{ csrf_token() }}',
                        'catId': catId,
                    },
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status) {
                            updatePoints();
                        }
                    }
                });
            } else {
                // User clicked cancel, do nothing
            }
        });
    }
</script>

<script>
    document.getElementById('downloadPdf').addEventListener('click', function() {
        var element = document.getElementById('enrollment_history_content');

        html2pdf(element, {
            filename: 'enrollment_history.pdf',
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                orientation: 'portrait'
            }
        });
    });
</script>

<script>
// tabs
var tabLinks = document.querySelectorAll(".tablinks");
var tabContent = document.querySelectorAll(".tabcontent");


tabLinks.forEach(function(el) {
   el.addEventListener("click", openTabs);
});


function openTabs(el) {
   var btnTarget = el.currentTarget;
   var country = btnTarget.dataset.country;

   tabContent.forEach(function(el) {
      el.classList.remove("active");
   });

   tabLinks.forEach(function(el) {
      el.classList.remove("active");
   });

   document.querySelector("#" + country).classList.add("active");
   
   btnTarget.classList.add("active");  

   
}

function countNumber(trgItem, countToNumber) {
    $('#' + trgItem).each(function() {
    var countTo = Number(countToNumber);
    $(this).prop('Counter', 0).animate({
        Counter: countTo - 1
    }, {
        duration: 1000,
        easing: 'swing',
        step: function(now) {
            var ceil = Math.floor(Math.random() * Math.floor(now))
            if(countTo > 0) {
                if (ceil < countTo) {
                    $(this).text(ceil);
                }
            }
            if(countTo < 0) {
                if (ceil > countTo) {
                    $(this).text(ceil);
                }
            }            
        },
        complete: function() {
            $(this).text(countTo);
        }
        });
    });
}

function getMemberFullNames(code) {
    reltionshipArr = @php
        $dependantTextArr = [];
        foreach(config('constant.dependant_code') as $dcode => $reltionshipTypes) {
            $relationText = [];
            foreach ($reltionshipTypes as $relationKey) {
                $relationText[] = config('constant.relationship_type')[$relationKey];
            }
            $dependantTextArr[$dcode] = implode(',', $relationText);
        }

        echo json_encode($dependantTextArr);    
    @endphp;
    return code + ': ' + reltionshipArr[code];
}

function getDependentList(dependantstructure){
    // search for / first and remove from original string
    let dsArr = [];
    if (dependantstructure.search('/') > -1) {
        dsArr.push('/');
        dependantstructure = dependantstructure.replace('/', '');
    }
    //search for PIL and remove from original string
    if (dependantstructure.search('PIL') > -1) {
        dsArr.push('PIL');
        dependantstructure = dependantstructure.replace('PIL', '');
    }
    return dsArr.concat(dependantstructure.split(''));
}

function generateDependentItems(subCatId, depList) {
    $('#memcvrd' + subCatId).html('');
    var memCvrdStr = '';
    var parentRadio = false;

    if (depList.length > 0) {
        if (depList[0] == '/') {
            parentRadio = true;
            depList.splice(0, 1);
        }

        console.log(depList);

        // Check for condition: if 'P' is present and '1' is present in depList
        if (depList.includes('P') && depList.includes('1')) {
            Swal.fire({
                title: 'Parent Coverage Warning',
                text: 'Single parent plan will be offered only to employees whose other parent is not alive.  In addition, cross-selection of single parent from each parent-set is not allowed.Are you sure you want to continue?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) {
                    // Exit the function if user cancels
                    return;
                }
            });
        }

        var existingDependent = [];
        $('[id^=dependant-list]').each(function () {
            var dCode = $(this).attr('data-depcode');
            var depId = $(this).attr('data-depId');
            var depName = $(this).attr('data-name');
            var depNom = $(this).attr('data-depNom');

            if (typeof existingDependent[dCode] === 'undefined') {
                existingDependent[dCode] = [[depId, depName, depNom]]; // array of array
            } else {
                existingDependent[dCode].push([depId, depName, depNom]);
            }
        });

        for (var depCode in existingDependent) {
            var depId = [];
            var depName = [];
            var depCodeFullName = getMemberFullNames(depCode);

            existingDependent[depCode].forEach(function (depRow) {
                depId.push(depRow[0]);
                depName.push(depRow[1]);
            });

            depList.forEach(function (x) {
                if (depCode.toLowerCase() == x.toLowerCase()) {
                    if (['PIL', 'P'].includes(x.toUpperCase())) {
                        memCvrdStr += '<div class="col-12 m-1 mt-2 mb-2"><input id="depMemCrvd_' + depId.join('_') +
                            '" type="' + (parentRadio ? 'radio' : 'checkbox') + '" name="depMemCrvd[]" value="' + depId.join(',') +
                            '" checked /><label for="depMemCrvd_' + depId.join('_') +
                            '">' + depName.join(',') + '[' + depCodeFullName + ']' + '</label></div>';
                    } else {
                        memCvrdStr += '<div class="col-12 m-1 mt-2 mb-2"><input id="depMemCrvd_' + depId.join('_') + '"' +
                            (x.toLowerCase() == 'e' ? 'disabled checked' : '') +
                            ' type="checkbox" name="depMemCrvd[]" value="' + depId.join(',') +
                            '" checked/><label for="depMemCrvd_' + depId.join('_') +
                            '">' + depName.join(',') + '[' + depCodeFullName + ']' + '</label></div>';
                    }
                }
            });
        }

        $('#memcvrd' + subCatId).html(memCvrdStr);
    }
    return memCvrdStr;
}


function planBindEvents() {
    // trigger for radio button 
    $('[id^=planId]').click(function(){ 
        if ($(this).is(':checked')) {
            var subCatId = $(this).attr('data-sc-id');
            var planId = $(this).attr('data-plan-id');
            let planDetailArr = ['bpName', 'ptf','pt','name',/*'allo','currs','avail',*/'tots','effecp','prorf','annup','totdc','psd','ped','bpsa',
            'opplpt', 'opplsa', 'totpt', 'totsa', 'corem', 'coresa',/*'is-lupsm',*/'is-si-sa','is-sa','is_grade_based','isvp', 'isvbsd'];

            planDetailArr.forEach(function (item,index) {
                itemVal = $('#' + item + subCatId).html($('#planDetails' + planId).attr('data-' + item));
            });

            // price tag vs points
            //$('#policyCalcPoints' + planId).html($('#planDetails' + planId).attr('data-opplpt'));
            $('#planName' + subCatId).html($('#planDetails' + planId).attr('data-name'));
            
            // Conditional UI
            $('#coresumRow' + subCatId).hide();
            $('#coreSum' + subCatId).hide();
            $('#coreMultiple' + subCatId).hide();
            //console.log($('#planDetails' + planId).attr('data-bpsa'));
            // summary core row having base plan
            if ($('#planDetails' + planId).attr('data-bpsa') != '') {
                $('#coresumRow' + subCatId).show();
                $('#coreSum' + subCatId).show();
            }
            //core-lumpsum or core-sa
            /* if ($('#planDetails' + planId).attr('data-bpsa') != '' && 
            ($('#planDetails' + planId).attr('data-is-lupsm') == '1' || 
                $('#planDetails' + planId).attr('data-is-sa') == '1' || 
                $('#planDetails' + planId).attr('data-is-si-sa') == '1')) {
                $('#coreSum' + subCatId).show();
            }  */               
            //core-multiple
            if ($('#planDetails' + planId).attr('data-is-si-sa') == '1') {
                $('#coreMultiple' + subCatId).show();
            }

            // dependant structure
            var depList = getDependentList($('#planDetails' + planId).attr('data-memcvrd'));

            //membersCovered = depList.flatMap((x) => getMemberFullNames(x));
            //$('#memcvrd' + subCatId).html(membersCovered.join(','));
            //$('#memcvrd' + subCatId).html(generateDependentItems(depList));
            generateDependentItems(subCatId, depList);
            //$('#fp-numbers-mcoverage' + subCatId).show();

            $('#parentSubLimit' + subCatId).hide();
            let parent_sublimit_amount = $('#planDetails' + planId).attr('data-prntSbLim');
            if( parent_sublimit_amount != '0') {
                //console.log('#planDetails' + planId + '----' +parent_sublimit_amount);
                $('#parentSubLimit' + subCatId).show();
                $('#prntSbLim' + subCatId).html(parent_sublimit_amount);
            }
        }

        // toggle disable of point based policy
        $('[id^=chkValuePlanId]').on('change', function(){
            var polId = $(this).attr('data-plan-id');
            if ($(this).is(':checked')) {
                $('#txtValuePlanId' + polId).removeAttr('disabled');
            } else {
                $('#txtValuePlanId' + polId).attr('disabled', 'disabled');
                $('#txtValuePlanId' + polId).val('');
            }
        });        
    });
}

function checkPoints(planId){
    // value based keyup validation 
    //$('#txtValuePlanId' + planId).on('keyup', function(){
        $('#txtValuePlanId' + planId).removeClass('bg-danger');
        var polId = $('#txtValuePlanId' + planId).attr('data-plan-id');
        var totalPointsAvailable = {{ Auth::user()->points_available }};
        var pointsVal = $('#txtValuePlanId' + planId).val();
        var totalPointEntered = parseInt(pointsVal != '' ? pointsVal : 0);
        {{-- $('#txtValuePlanId' + ).each(function(){
            
            totalPointEntered += parseInt($(this).val() != '' ? $(this).val() : 0); 
        }); --}}

        if (totalPointEntered > totalPointsAvailable) {
            $('#txtValuePlanId' + planId).val('').addClass('bg-danger');
            alert('Maximum points across all benefit cannot be more than available ' + totalPointsAvailable + ' point(s)');
        }
    //});
}

function triggerInitialClick() {
    // trigger click for plan which is marked as selected
    $('[id^=planId]:radio').each(function(){
        if ($(this).attr('data-default-select') == 1 || $(this).is(':checked')) {
            $(this).click();
        }
    });    
    $('[name=closeSubCategory]').click(function(){ 
        $('[id^=subCtgryDetail]').hide();
        $('#wrapper_tabcontent').focus();
    });
}

function policyDetailsforSubCategory(subCatId) {
    var timeout = setTimeout(function() {
        $.ajax({
            url: '/enrollment/getPolicybySubCategory',
            data: {'subCatId':subCatId},
            success: function(response){
                $('#policySubCategoryList' + subCatId).html(response.html);
                planBindEvents();
                
                $('[id^=planId]').each(function(){
                    var subCatId = $(this).attr('data-sc-id');
                    var planId = $(this).attr('data-plan-id');

                    // price tag vs points
                    $('#policyCalcPoints' + planId).html($(planId).attr('data-opplpt'));
                    $('#planName' + subCatId).html($('#planDetails' + planId).attr('data-name'));    
                });
                triggerInitialClick();
            },
            error: function(error){

            }
        });
    }, 3000);
}

function saveEnrollment(catId) {
    var polData = new Object();
    var fypmapId = 0;
    if ($('form#subCategoryForm' + catId).attr('data-ispv') == 1) {
        var checkboxCounter = 0;
        var policySelected = [];
        $('form#subCategoryForm' + catId).find('input[type=checkbox]:checked').each(function(item) {
            checkboxCounter++;
            policySelected.push($(this).val());
        });
        if (checkboxCounter && policySelected.length) {
            let savePoints = [];
            let isvbsd = 0;
            let polData = [];
            let summary = [];
            policySelected.forEach(function(policyID) {
                polDet = $('#planDetails' + policyID);
                isvbsd = parseInt(polDet.attr('data-isvbsd'));
                var fypmap = polDet.attr('data-fypmap');
                polDetJs = document.getElementById('planDetails' + policyID);
                for (var i = 0; i < polDetJs.attributes.length; i++) {
                    attr = polDetJs.attributes[i];
                    if (/^data-/.test(attr.nodeName)) {
                        attrName = attr.nodeName.replace(/^data-/, '');
                        summary.push(fypmap + ':' + attrName + ':' + attr.nodeValue);
                    }
                }
                if (isvbsd) {
                    savePoints.push(fypmap + ':' + parseInt($('#txtValuePlanId' + policyID).val()));
                } else {
                    savePoints.push(fypmap + ':' + parseInt(polDet.attr('data-annupwocurr')));
                }
            });
            if (savePoints.length) {
                $.ajax({
                    url: "/enrollment/savePV",
                    type: "POST",
                    data: {
                        "_token": '{{ csrf_token() }}',
                        'savePoints': btoa(unescape(encodeURIComponent(JSON.stringify(savePoints)))),
                        'catId': catId,
                        'dependants': 'N.A.'
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#launchEnrollmentModal .modal-title').html('Yay!! Selection Saved').addClass('text-success');
                            $('#launchEnrollmentModal .btn.modal_close').html('Proceed Ahead');
                        } else {
                            $('#launchEnrollmentModal .modal-title').html('OOPS.. :( Something went wrong').addClass('text-danger');
                            $('#launchEnrollmentModal .btn.modal_close').html('Try Again');
                            $("#launchEnrollmentModal").show();
                        }
                        $('#launchEnrollmentModal .modal-body>p').html(response.message);
                        $("#launchEnrollmentModal").show();
                    }
                });
            }
        } else {
            $('#launchEnrollmentModal .modal-title').html('No benefits selected!').addClass('text-danger');
            $('#launchEnrollmentModal .modal-body>p').html('Please choose available benefits as you still have <b>' +
                @php echo Auth::user()->points_available @endphp + '</b> points');
            $('#launchEnrollmentModal .btn.modal_close').html('I\'ll select again');
            $("#launchEnrollmentModal").show();
        }
    } else {
        $('form#subCategoryForm' + catId).find('input[type=radio]:checked').each(function(item) {
            let policyID = $(this).val();
            polDet = $('#planDetails' + policyID);
            let points = 0;
            let depSelected = [];

            var polDet = document.getElementById('planDetails' + policyID);
            if (parseInt(polDet.attributes['data-isbp'].nodeValue)) {
                $('#launchEnrollmentModal .modal-title').html('Invalid Selection!').addClass('text-danger');
                $('#launchEnrollmentModal .modal-body>p').html('Base plan is already present. Please select top-up to increase coverage.');
                $('#launchEnrollmentModal .btn.modal_close').html('I\'ll select again');
                $("#launchEnrollmentModal").show();
            } else {
                for (var i = 0; i < polDet.attributes.length; i++) {
                    attr = polDet.attributes[i];
                    if (/^data-/.test(attr.nodeName)) {
                        attrName = attr.nodeName.replace(/^data-/, '');
                        polData[attrName] = attr.nodeValue;
                        if (attrName == 'fypmap') {
                            fypmapId = parseInt(attr.nodeValue);
                        }
                        if (attrName == 'totptwocurr') {
                            points = attr.nodeValue;
                        }
                    }
                }
                $('#memcvrd' + catId).find('input[type="checkbox"]:checked').each(function() {
                    depSelected.push($(this).val())
                });
                if (fypmapId) {
                    $.ajax({
                        url: "/enrollment/save",
                        type: "POST",
                        data: {
                            "_token": '{{ csrf_token() }}',
                            'fypmap': fypmapId,
                            'catId': catId,
                            'policyId': policyID,
                            'points': points,
                            'sd': depSelected.join('###'),
                        },
                        success: function(response) {
                            if (response.status) {
                                $('#launchEnrollmentModal .modal-title').html('Yay!! Selection Saved').addClass('text-success');
                                $('#launchEnrollmentModal .btn.modal_close').html('Proceed Ahead');
                            } else {
                                $('#launchEnrollmentModal .modal-title').html('OOPS.. :( Something went wrong').addClass('text-danger');
                                $('#launchEnrollmentModal .btn.modal_close').html('Try Again');
                                $("#launchEnrollmentModal").show();
                            }
                            $('#launchEnrollmentModal .modal-body>p').html(response.message);
                            $("#launchEnrollmentModal").show();
                        }
                    });
                }
            }
        });
    }
}




function updatePoints() {
    $.ajax({
        url: "/enrollment/updatePoints",
        type:"POST",
        data:{
            "_token": '{{ csrf_token() }}',
        },
        success:function(response) {
            response = JSON.parse(response);
            ctpts = response.catpts;
            for (const [key, value] of Object.entries(ctpts)) {
                let catPointCount = 0;
                ctpts[key].forEach(function(element){
                    catPointCount += element['points'];
                });
                countNumber('currSelectionDataVal' + key, catPointCount); 
            }
            countNumber('points-head-tot', response.userpts[0]['points_used'] + response.userpts[0]['points_available']); 
            countNumber('points-head-used', response.userpts[0]['points_used']); 
            countNumber('points-head-avail', response.userpts[0]['points_available']); 
            $("#walletPointsLiveUpdate").html(response.userpts[0]['points_available']);
            $("#launchEnrollmentModal").hide();

        }
    });
}
</script>
<script src="/assets/js/number-rush.js"></script>

<script>
    $(document).ready(function() {
       

$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
}); 
$('[id^=header_]').removeClass('active');
$('#header_enrollment').addClass('active'); 
$('#enrollmentSubmit').on('click', function() {
    $(this).hide();
    $.ajax({
        url: '/enrollment/finalSubmit',
        type:'POST',
        success: function(response){
            response = JSON.parse(response);
            if (response.status) {
                $('#finalSubmissionModalBody').html(response.msg);
                $('#finalSubmissionModalTitle').html('<b>Success</b>').addClass('text-success');
                $('#finalSubmissionModalClose').html('Close');
            }
        }
    });
});

$('[id^=enrollmentSubCategory]').click(function() {
    $('[id^=subCtgryDetail]').hide();
    $('#subCtgryDetail' + $(this).attr('data-cat-id')).fadeIn(1000);    
    let offset = $('#subCtgryDetail' + $(this).attr('data-cat-id')).offset().top;
});

$('[id^=policySubCategoryList]').each(function(){
    policyDetailsforSubCategory($(this).attr('data-scid'));
});

$('[id^="fyClick"]').on('click', function(){
    $('#enrollment_history_year').html($(this).text());
    $('#enrollment_history_content').load('/enrollment/summary?fid=' + $(this).attr('data-id'));
});

$('[id^="Summary-tab"]').on('click', function(){
    $('#summary_content').load('/enrollment/summary?fid=' + $(this).attr('data-id'));
});

});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('#enrolTabs');
        const list = document.querySelector('.horizontal-list');
        const leftArrow = document.querySelector('.left-arrow');
        const rightArrow = document.querySelector('.right-arrow');

        leftArrow.addEventListener('click', function() {
            container.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
            window.addEventListener('resize', hideOverflowingItems);
            hideOverflowingItems();

        });

        rightArrow.addEventListener('click', function() {
            container.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
            window.addEventListener('resize', hideOverflowingItems);
            hideOverflowingItems();

        });


        var button = document.getElementById("howwork-tab");

        // Add the 'active' class to the button
        button.classList.add("active");

        // function hideOverflowingItems() {
        //     const containerWidth = container.clientWidth;
        //     const listWidth = list.scrollWidth;
        //     const containerScrollLeft = container.scrollLeft;
        //     const containerScrollRight = containerScrollLeft + containerWidth;

        //     // Check if the last <li> is fully visible
        //     const lastLi = list.lastElementChild;
        //     const lastLiRight = lastLi.offsetLeft + lastLi.offsetWidth;
        //     const lastLiVisible = lastLiRight <= containerScrollRight;

        //     // Check if the first <li> is fully visible
        //     const firstLi = list.firstElementChild;
        //     const firstLiLeft = firstLi.offsetLeft;
        //     const firstLiVisible = firstLiLeft >= containerScrollLeft;

        //     // Show/hide or enable/disable arrows based on visibility
        //     if (lastLiVisible) {
        //         rightArrow.style.display = 'none';
        //         rightArrow.disabled = true;
        //     } else {
        //         rightArrow.style.display = 'block';
        //         rightArrow.disabled = false;
        //     }

        //     if (firstLiVisible) {
        //         leftArrow.style.display = 'none';
        //         leftArrow.disabled = true;
        //     } else {
        //         leftArrow.style.display = 'block';
        //         leftArrow.disabled = false;
        //     }
        // }

        // window.addEventListener('resize', hideOverflowingItems);
        // hideOverflowingItems();


    });
</script>

