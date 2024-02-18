@section('link_rel')
<link href="{{ asset('assets/css/jtable/themes/metro/blue/jtable.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@php
//dd($data['gradeAmtData']);
$formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY); 
//dd($data['sub_categories_data']);
    /* foreach($data['category'] as $key => $value) {
        echo '<pre>';
        print_r($value);
    }
    die; */
@endphp


<a id="enrollmentModal_trigger" style="display:none;" href="#launchEnrollmentModal">modalEnrollment</a>
@include('_partial.enrollmentModal')
<section class="tab-wrapper">
   <div class="tab-content">
      <!-- Tab links -->
      <div class="tabs">
            <button class="tablinks active" data-country="how-it-works">
                <p data-title="How It Works">How It Works</p>
            </button>
            @if(Auth::check())
                @if($data['is_enrollment_window'])
                    @foreach($data['category'] as $item)
                        <button class="tablinks" id="tabLink{{ $item['id'] }}" 
                            data-country="{{ 'content-tab-' . $item['id'] }}">
                            <p data-title="{{ $item['name'] }}">{{ $item['name'] }}</p>
                        </button>
                    @endforeach 
                @endif      
                <button class="tablinks" data-country="summary" id="enrollment-summary">
                    <p data-title="Summary">Summary</p>
                </button>
                <button class="tablinks" data-country="enrollment-history">
                    <p data-title="Enrollment History">Enrollment History</p>
                </button>
            @endif                
      </div>
      <!-- Tab content -->
      <div class="wrapper_tabcontent">
         <div id="how-it-works" class="tabcontent active">
            <h3>How It Works</h3>
            <div id="how_it_works_static">
                <h5>Follow the steps provided below to finalize your enrollment for the plan year 2023-24:</h5>
                <ul class="ul-points">
                    
                    <li>Each tab within the enrollment menu provides an opportunity to choose benefits within a specific category. For instance, 
                    benefits such as term life are located in the "Term Life Insurance" section. The four main sections are</li>
                    
                        <ul class="ul-points">
                            <li>Life Insurance</li>
                            <li>Accident Insurance</li>
                            <li>Medical Insurance</li>
                            <li>Flexi Cash Benefits</li>
                        </ul>
                    </li>
                    <li>
                        Below are the default base coverage category wise:
                        <table class="table table-bordered table-info">
                            <tr>
                                <th scope="col">Category</th>
                                <!-- <th scope="col">Sub-Category</th> -->
                                <th scope="col">Core Benefit</th>
                                <th scope="col">Sum Insured</th>
                            </tr>
                            @foreach($data['basePlan'] as $bpRow)
                            @php
                                if (count($data['gradeAmtData']) && array_key_exists($bpRow['subcategory']['categories']['id'], $data['gradeAmtData'])) {
                                    $bpsa = (int)$data['gradeAmtData'][$bpRow['subcategory']['categories']['id']];
                                    $is_grade_based = TRUE;
                                } else {

                                    // Provided values
                                    $encryptedData =  Auth::user()->salary;
                                    $encryptionKey = 'QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4=';
                                    $initializationVector = 'G4bfDHjL3gXiq5NCFFGnqQ==';

                                    // Decrypt the data
                                    $cipher = "aes-256-cbc";
                                    $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

                                    $salary = openssl_decrypt(base64_decode($encryptedData), $cipher, base64_decode($encryptionKey), $options, base64_decode($initializationVector));

                                    if ($salary === false) {
                                        echo "Error during decryption: " . openssl_error_string() . PHP_EOL;
                                    } else {
                                        $salary = floatval(rtrim($salary, "\0"));
                                    }


                                    
                                    $sa = !is_null($bpRow['sum_insured']) ? $bpRow['sum_insured'] : 0;
                                    $sa_si = !is_null($bpRow['si_factor']) ?
                                            $sa_si = $bpRow['si_factor'] * $salary : 0;
                                    if($sa_si > $sa) {
                                        $bpsa = (int)$sa_si;
                                        $is_si_sa = TRUE;
                                        $base_si_factor = $bpRow['si_factor'];
                                    } else {
                                        $bpsa = (int)$sa;
                                        $is_sa = TRUE;
                                    }
                                }
                                // name of base policy
                                $bpName = $bpRow['name'];
                            @endphp
                            <tr>
                                <td scope="row">{{ $bpRow['subcategory']['categories']['name'] }}</td>
                                <!-- <td>{{ $bpRow['subcategory']['name'] }}</td> -->
                                <td>{{ $bpRow['name'] }}</td>
                                <td>{{ $formatter->formatCurrency(round($bpsa), 'INR') }}</td>
                            </tr>
                            @endforeach
                            
                        </table>
                    </li>
                    <li>Sequentially navigate through each of these sections to explore and select from 
                    the various available benefits. It's important to note that a positive point balance 
                    is required for the selection of Flexi Cash Benefits
                    </li>
                    <li>
                    Within each section, you'll find a list of different benefits presented in a tabular 
                    format. Begin your selection by clicking on the desired benefit and then choosing the 
                    relevant option
                    </li> 
                    <li>
                    For insured benefits, the names of your dependants eligible for coverage will be 
                    visible in tabular form. Ensure you select the dependants you wish to include in applicable
                    benefits by checking against their names
                    </li>
                    <li>
                    As you make your choices for benefits, the FlexPoints utilization table will automatically
                    reflect the updates. If the cost of the insured benefits surpasses the available 
                    FlexPoints, any surplus will be covered through salary deduction
                    </li>
                    <li>
                    After finalizing your benefit selections and saving the enrollment, navigate to the
                     'Summary' tab to ensure accurate capture of all details
                    </li>
                    <li>
                    Click the 'Confirm enrollment' button to validate your benefit choices for the plan year
                    2023-24. It's important to note that once enrollment is confirmed, no further 
                    alterations or edits can be made to your selection
                    </li>
                </ul>
            </div>
         </div>
         @if($data['is_enrollment_window'] )
            @if(count($data['category']))
                @foreach($data['category'] as $item)
                    @include('_partial.category-tab')
                @endforeach 
            @else
                No CATGEORY FOUND. WRONG SETUP.
                {{-- @todo: Error to show on no category list available --}}
            @endif 
         @endif
         <div id="summary" class="tabcontent">
            <h3>Summary</h3>
            
            <div id="summary_content"></div>
            @if(!session('is_submitted') && count($data['currentSelectedData']))
                <h5 class="text-secondary" style="text-align:right;">Make your decision <em>FINAL</em> by clicking 
                    <a href="#finalSubmissionModal" class="btn btn-primary" id="finalSubmit_trigger">Final Submission</a>
                </h5>
            @endif
         </div>
         <div id="enrollment-history" class="tabcontent">
            <h3>History</h3>
            @include('_partial.enrollment-history')
         </div>
      </div>
   </div>
</section>

@section('script')
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
            depList.splice(0,1); 
        }
        {{-- $('[id^=dependant-list]').each(function(){
            var dCode = $(this).attr('data-depcode');
            var depId = $(this).attr('data-depId');
            var depName = $(this).attr('data-name');
            var depNom = $(this).attr('data-depNom');
            depList.forEach(function(x) {
                if(dCode.toLowerCase() == x.toLowerCase()) {
                    if(['PIL','P'].includes(dCode)) {   // match if dependant added is PIL or P case
                        memCvrdStr += '<input id="depMemCrvd' + depId + '" name="depMemCrvd[]" type="' + (parentRadio ? 'radio' : 'checkbox')  + '" value="' 
                        + depId + '"/>&nbsp;<label for="depMemCrvd' + depId + '">' + depName + '[' + 
                        getMemberFullNames(dCode) + ',Nomination(%):' +  depNom + ']' + '</label>';
                    } else {
                        memCvrdStr += '<input id="depMemCrvd' + depId + '" name="depMemCrvd[]" type="checkbox" value="' 
                        + depId + '"/>&nbsp;<label for="depMemCrvd' + depId + '">' + depName + '[' + 
                        getMemberFullNames(dCode) + ',Nomination(%):' +  depNom + ']' + '</label>';   
                    }
                }  
            });
        }); --}}
        //console.log(parentRadio);
        var existingDependent = [];
        var i = 0;
        $('[id^=dependant-list]').each(function(){
            var dCode = $(this).attr('data-depcode');
            var depId = $(this).attr('data-depId');
            var depName = $(this).attr('data-name');
            var depNom = $(this).attr('data-depNom');
            if (typeof existingDependent[dCode] === 'undefined'){
                existingDependent[dCode] = [[depId,depName,depNom]];    // array of array
            } else {
                existingDependent[dCode].splice(
                    existingDependent[dCode].length,0,[depId,depName,depNom]
                );
            }
        });
        //console.log(existingDependent);
        for(depCode in existingDependent) {
            depId = [];
            depName = [];
            depCodeFullName = getMemberFullNames(depCode);
            existingDependent[depCode].forEach(function(depRow){
                //console.log(depRow);
                depId.push(depRow[0]);
                depName.push('(' + depRow[1] + ':' + depRow[2] + '%)');
            });
            //console.log('DepCode/:' + depCode);
            depList.forEach(function(x) {
                if(depCode.toLowerCase() == x.toLowerCase()) {
                    //console.log(['PIL','P'].find(depList), depCode.toLowerCase(), x.toLowerCase(),depList);
                    if(['PIL','P'].includes(x.toUpperCase())) {   // match if dependant added is PIL or P case
                        memCvrdStr += '<div class="col-12 m-1 mt-2 mb-2"><input id="depMemCrvd_' + depId.join('_')  + 
                            '" type="' + (parentRadio ? 'radio' : 'checkbox' ) + '" name="depMemCrvd[]" value="' + depId.join(',') + 
                            '" /><label for="depMemCrvd_' + depId.join('_')  + 
                            '">' + depCodeFullName + '[' + depName.join(',') + ']' + '</label></div>';
                    } else {
                        memCvrdStr += '<div class="col-12 m-1 mt-2 mb-2"><input id="depMemCrvd_' + depId.join('_') + '"' + 
                            (x.toLowerCase() == 'e'? 'disabled checked' : '' )  +
                            ' type="checkbox" name="depMemCrvd[]" value="' + depId.join(',') + 
                            '" /><label for="depMemCrvd_' + depId.join('_')  + 
                            '">' + depCodeFullName + '[' + depName.join(',') + ']' + '</label></div>';
                    }
                }
            });
        }
        /* existingDependent.forEach(function(dep, index, currArr){
            console.log(dep);
        }); */
        //console.log(existingDependent);
        $('#memcvrd' + subCatId).html(memCvrdStr);
        //return memCvrdStr;
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
                    $('#policyCalcPoints' + planId).html($('#planDetails' + planId).attr('data-opplpt'));
                    $('#planName' + subCatId).html($('#planDetails' + planId).attr('data-name'));    
                });
                triggerInitialClick();
            },
            error: function(error){

            }
        });
    }, 3000);
}

function saveEnrollment(catId){
    var polData = new Object();
    var fypmapId = 0;
    if ($('form#subCategoryForm' + catId).attr('data-ispv')==1) {
        var checkboxCounter = 0;
        var policySelected = [];
        $('form#subCategoryForm' + catId).find('input[type=checkbox]:checked').each(function(item){
            checkboxCounter++;
            policySelected.push($(this).val());
        });
        //console.log(policySelected); 
        if (checkboxCounter && policySelected.length) {
            let savePoints = [];
            let isvbsd = 0;
            let polData = [];
            let summary = [];
            policySelected.forEach(function(policyID){
                polDet = $('#planDetails' + policyID);
                isvbsd = parseInt(polDet.attr('data-isvbsd'));
                var fypmap = polDet.attr('data-fypmap');
                polDetJs = document.getElementById('planDetails' + policyID);
                for (var i = 0; i < polDetJs.attributes.length; i++) {            
                    attr = polDetJs.attributes[i];    // current attr
                    if (/^data-/.test(attr.nodeName)) { // If attribute nodeName starts with 'data-'
                        attrName = attr.nodeName.replace(/^data-/, '');
                        summary.push(fypmap + ':' + attrName + ':' + attr.nodeValue);    // array of array
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
                    type:"POST",
                    data:{
                        "_token": '{{ csrf_token() }}',
                        'savePoints':btoa(unescape(encodeURIComponent(JSON.stringify(savePoints)))),
                        'catId': catId,
                        //'summary' : btoa(unescape(encodeURIComponent(JSON.stringify(summary)))),
                        'dependants': 'N.A.'
                    },
                    success:function(response) {
                        if (response.status) {
                            title = 
                            $('#launchEnrollmentModal .modal-title').html('Yay!! Selection Saved').addClass('text-success');
                            $('#launchEnrollmentModal .btn.modal_close').html('Proceed Ahead');
                        } else {                            
                            $('#launchEnrollmentModal .modal-title').html('OOPS.. :( Something went wrong').addClass('text-danger');
                            $('#launchEnrollmentModal .btn.modal_close').html('Try Again');
                            $("#enrollmentModal_trigger").click();
                        }
                        $('#launchEnrollmentModal .modal-body>p').html(response.message);                        
                        $("#enrollmentModal_trigger").click();
                        //updatePoints();
                    }
                });
            }
        } else {
            $('#launchEnrollmentModal .modal-title').html('No benefits selected!').addClass('text-danger');
            $('#launchEnrollmentModal .modal-body>p').html('Please choose available benefits as you still have <b>' +
                @php echo Auth::user()->points_available @endphp + '</b> points');
            $('#launchEnrollmentModal .btn.modal_close').html('I\'ll select again');
            $("#enrollmentModal_trigger").click();
        }
    } else {
        $('form#subCategoryForm' + catId).find('input[type=radio]:checked').each(function(item){
            let policyID = $(this).val();
            polDet = $('#planDetails' + policyID);
            let points = 0;
            let depSelected = [];
            
            var polDet = document.getElementById('planDetails' + policyID);
            // iterate each attribute
            if (parseInt(polDet.attributes['data-isbp'].nodeValue)) {
                $('#launchEnrollmentModal .modal-title').html('Invalid Selection!').addClass('text-danger');
                $('#launchEnrollmentModal .modal-body>p').html('Base plan is already present. Please select top-up to increase coverage.');
                $('#launchEnrollmentModal .btn.modal_close').html('I\'ll select again');
                $("#enrollmentModal_trigger").click();
            } else {
                for (var i = 0; i < polDet.attributes.length; i++) {            
                    attr = polDet.attributes[i];    // current attr
                    if (/^data-/.test(attr.nodeName)) { // If attribute nodeName starts with 'data-'
                        attrName = attr.nodeName.replace(/^data-/, '');
                        polData[attrName] = attr.nodeValue;
                        if(attrName=='fypmap') {
                            fypmapId = parseInt(attr.nodeValue);
                        }
                        if (attrName == 'totptwocurr') {
                            points = attr.nodeValue;
                        }
                    }
                }
                // members saved
                $('#memcvrd'+catId).find('input[type="checkbox"]:checked').each(function(){
                    depSelected.push($(this).val())
                });          
                if (fypmapId) {
                    $.ajax({
                        url: "/enrollment/save",
                        type:"POST",
                        data:{
                            "_token": '{{ csrf_token() }}',
                            'fypmap':fypmapId,
                            'catId': catId,
                            'policyId' : policyID,
                            'points': points,
                            'sd': depSelected.join('###'),  // selected dependants
                            //'summary' : btoa(unescape(encodeURIComponent(JSON.stringify(polData))))
                        },
                        success:function(response) {
                            if (response.status) {
                                title = 
                                $('#launchEnrollmentModal .modal-title').html('Yay!! Selection Saved').addClass('text-success');
                                $('#launchEnrollmentModal .btn.modal_close').html('Proceed Ahead');
                            } else {                            
                                $('#launchEnrollmentModal .modal-title').html('OOPS.. :( Something went wrong').addClass('text-danger');
                                $('#launchEnrollmentModal .btn.modal_close').html('Try Again');
                                $("#enrollmentModal_trigger").click();
                            }
                            $('#launchEnrollmentModal .modal-body>p').html(response.message);                        
                            $("#enrollmentModal_trigger").click();
                            //updatePoints();
                        }
                    });
                } else {

                }
            }        
        });
    }
}

function resetSelection(catId, buttonObj){
    $(buttonObj).hide();
    $.ajax({
        url: "/enrollment/resetCategory",
        type:"POST",
        data:{
            "_token": '{{ csrf_token() }}',
            'catId': catId,
        },
        success:function(response) {
            $(buttonObj).show();
            response = JSON.parse(response);
            if (response.status) {
                updatePoints();
                $('#resetSelectionModalClose' + catId).click();                
            }
        }
    });
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

        }
    });
}
</script>
<script src="/assets/js/number-rush.js"></script>

{{-- JTABLE GRID --}}
@endsection

@section('document_ready')
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

$('#enrollment-summary').on('click', function(){
    $('#summary_content').load('/enrollment/summary');
});
$('[id^="fyClick"]').on('click', function(){
    $('#enrollment_history_year').html($(this).text());
    $('#enrollment_history_content').load('/enrollment/summary?fid=' + $(this).attr('data-id'));
});

{{-- // trigger for radio button --}}
    {{-- $('[id^=planId]').click(function(){
        var subCatId = $(this).attr('data-sc-id');
        var planId = $(this).attr('data-plan-id');
        let planDetailArr = ['ptf','pt','osa','allo','currs','avail','tots','effecp','prorf','annup','totdc','psd','ped'];

        planDetailArr.forEach(function (item,index) {
            itemVal = $('#' + item + 'subCatId').html($('#planDetails' + planId).attr('data-' + item));
        });
        let currPlanValue = 5607;
        let allPlanValue = 565607;
        let balancePlanValue = 20569;

        countNumber('currentPlanValue', currPlanValue);        
        countNumber('allPlanValue', allPlanValue);
        countNumber('remainingPlanValue', balancePlanValue);
    }); --}}
    
    {{-- // trigger click for plan which is marked as selected --}}
    {{-- $('[id^=planId]').each(function(){
        if ($(this).attr('data-default-select') == 1) {
            $(this).click();
            console.log($(this).attr('data-plan-id'));
        }
    }); --}}

        
@endsection