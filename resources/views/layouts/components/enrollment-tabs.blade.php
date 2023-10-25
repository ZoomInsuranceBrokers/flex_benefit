@section('link_rel')
<link href="{{ asset('assets/css/jtable/themes/metro/blue/jtable.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@php

//dd($data['sub_categories_data']);
    /* foreach($data['category'] as $key => $value) {
        echo '<pre>';
        print_r($value);
    }
    die; */
@endphp


<section class="tab-wrapper">
   <div class="tab-content">
      <!-- Tab links -->
      <div class="tabs">
            <button class="tablinks active" data-country="how-it-works">
                <p data-title="How It Works">How It Works</p>
            </button>
            @if(Auth::check())
                @foreach($data['category'] as $item)
                    <button class="tablinks" data-country="{{ 'content-tab-' . $item['id'] }}">
                        <p data-title="{{ $item['name'] }}">{{ $item['name'] }}</p>
                    </button>
                @endforeach         
                <button class="tablinks" data-country="summary">
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
                <h2>Core Benefits: These are mandatory minimum level of benefitsoffered:</h2>
                <p>
                    <ul>
                        <li>Medical Insurance – INR 500,000 for Employee only</li>
                        <li>Personal Accident Insurance – Grade- wise ranging from INR 1200,000 to INR, 10,000,000</li>
                        <li>Life Insurance - 36 months’ total fixed salarywith a minimum of INR 2,000,000 and a maximum of
                        INR 60,000,000. Thepolicy features of the existing Insurance Benefits (Medical/Accident/Life) continue
                        to be the same</li>
                        <li>For employees joined before 31st March 2023, benefits which were opted in the previous year will 
                        act as default cover however, for employees who have joined after 31st March 2023 default cover will 
                        be E +S+2C - 500,000</li>
                        <li>In case any employee does not participate in the enrollment process, he/she will stay in the plan
                        opted in the previous year</li>
                    </ul>
                </p>

                <p><h3>Optional benefits</h3>
                These are combination of Insurance and Flexi cash benefits. Insurance Benefits consist of voluntary
                plan enhancement and multiple top up options. Flexi cash benefits consist of fitness, health & wellness,
                learning & development related plans.</p>

                <h3>Under the program</h3>
                <li>Select your benefit options by utilizing your flex points. You may also exceed your allocated flex
                pointsforinsured benefits by opting forpayroll deduction</li>
                <li>Opt foroptional benefits fromthe defined catalogue</li>
                <li>For Group Medical Insurance, a two year rolling lock-in was introduced from 2020 onwards.
                During the lock-in period, employees can only select higher level plans. Upon completion of lock-in period,
                they may move down by up to two levels of Sum Insured</li>


                {{-- <p>Paris is in the Paris department of the Paris-Isle-of-France region The French historic,
                political and economic capital, with a population of only 2.5 million is located in the northern
                part of France. One of the most beautiful cities in the world. Home to historical monuments such as Notre Dame, the Eiffel tower (320m), Bastille, Louvre and many more. </p> --}}
            </div>
         </div>
        @if(count($data['category']))
            @foreach($data['category'] as $item)
                @include('_partial.category-tab') 
            @endforeach 
        @elseif($condition)
            {{-- @todo: Error to show on no category list available --}}
        @endif                      
         <div id="summary" class="tabcontent">
            <h3>Summary</h3>
            <div id="summary"></div>
         </div>
         <div id="enrollment-history" class="tabcontent">
            <h3>Enrollment History</h3>
            <div id="enrollment_history"></div>
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
            if (ceil < countTo) {
                $(this).text(ceil);
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
        $dependentTextArr = [];
        foreach(config('constant.dependent_code') as $dcode => $reltionshipTypes) {
            $relationText = [];
            foreach ($reltionshipTypes as $relationKey) {
                $relationText[] = config('constant.relationship_type')[$relationKey];
            }
            $dependentTextArr[$dcode] = implode(',', $relationText);
        }

        echo json_encode($dependentTextArr);    
    @endphp;
    return code + ': ' + reltionshipArr[code];
}

function getDependentList(dependentStructure){
    // search for / first and remove from original string
    let dsArr = [];
    if (dependentStructure.search('/') > -1) {
        dsArr.push('/');
        dependentStructure = dependentStructure.replace('/', '');
    }
    //search for PIL and remove from original string
    if (dependentStructure.search('PIL') > -1) {
        dsArr.push('PIL');
        dependentStructure = dependentStructure.replace('PIL', '');
    }
    return dsArr.concat(dependentStructure.split(''));
}

function planBindEvents() {
        // trigger for radio button 
        $('[id^=planId]').click(function(){ 
            if ($(this).is(':checked')) {
                var subCatId = $(this).attr('data-sc-id');
                var planId = $(this).attr('data-plan-id');
                let planDetailArr = ['ptf','pt','osa','allo','currs','avail','tots','effecp','prorf','annup','totdc','psd','ped','bpsa',
                'opplpt', 'opplsa', 'totpt', 'totsa', 'corem', 'coresa'];

                planDetailArr.forEach(function (item,index) {
                    itemVal = $('#' + item + subCatId).html($('#planDetails' + planId).attr('data-' + item));
                });

                // Conditional UI
                $('#coresumRow' + subCatId).show();
                //console.log('BPSA:' + $('#planDetails' + planId).attr('data-bpsa'));
                if ($('#planDetails' + planId).attr('data-bpsa') == '') {
                    $('.coresumToggle' + subCatId).hide();
                    $('#coresumRow' + subCatId).hide();
                }

                // dependent structure
                const depList = getDependentList($('#planDetails' + planId).attr('data-memcvrd'));
                membersCovered = depList.flatMap((x) => getMemberFullNames(x));
                $('#memcvrd' + subCatId).html(membersCovered.join(','));
                //$('#fp-numbers-mcoverage' + subCatId).show();

                $('#parentSubLimit' + subCatId).hide();
                let parent_sublimit_amount = $('#planDetails' + planId).attr('data-prntSbLim');
                if( parent_sublimit_amount > 0) {
                    console.log('#planDetails' + planId + '----' +parent_sublimit_amount);
                    $('#parentSubLimit' + subCatId).show();
                    $('#prntSbLim' + subCatId).html(parent_sublimit_amount);
                }

                let currPlanValue = 5607;
                let allPlanValue = 565607;
                let balancePlanValue = 20569;

                //countNumber('currentPlanValue', currPlanValue);        
                //countNumber('allPlanValue', allPlanValue);
                //countNumber('remainingPlanValue', balancePlanValue);
            }            
    });
}

function triggerInitialClick() {
    // trigger click for plan which is marked as selected
    $('[id^=planId]').each(function(){
        if ($(this).attr('data-default-select') == 1) {
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
                //console.log(subCatId);
                $('#policySubCategoryList' + subCatId).html(response.html);
                planBindEvents();
                triggerInitialClick();
                
            },
            error: function(error){

            }
        });
    }, 3000);
}


</script>
<script src="/assets/js/number-rush.js"></script>

{{-- JTABLE GRID --}}
@endsection

@section('document_ready')
$('[id^=header_]').removeClass('active');
$('#header_enrollment').addClass('active');
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });  

    $('[id^=enrollmentSubCategory]').click(function() {
        $('[id^=subCtgryDetail]').hide();
        $('#subCtgryDetail' + $(this).attr('data-cat-id')).fadeIn(1000);    
        let offset = $('#subCtgryDetail' + $(this).attr('data-cat-id')).offset().top;
    });

$('[id^=policySubCategoryList]').each(function(){
    policyDetailsforSubCategory($(this).attr('data-scid'));
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