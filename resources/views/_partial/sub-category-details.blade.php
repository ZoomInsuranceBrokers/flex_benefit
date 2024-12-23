@if(isset($activePolicyForSubCategoryFY) && count($activePolicyForSubCategoryFY))
    @php
    
        $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
        //dd($userPolData);
        $subCatId = array_key_exists('policy', $activePolicyForSubCategoryFY[0]) ? 
            $activePolicyForSubCategoryFY[0]['policy']['ins_subcategory_id_fk'] : 0;
        $is_point_value_based = array_key_exists('policy', $activePolicyForSubCategoryFY[0]) ? 
            $activePolicyForSubCategoryFY[0]['policy']['is_point_value_based'] : 0;
    @endphp
    <div class="row optnBenft" data-sc-id="{{ $subCatId }}">
        <div class="col-12">
            <hr class="my-2">
            <div class="row custom-heading">                                
                <div class="section-heading">
                    <h4 class="py-1">Optional Benefits</h4>                                        
                </div>
                @if($is_point_value_based)
                    @include('_partial.sub-category-pv-based')  {{-- Point Value Based --}}
                @else
                    @include('_partial.sub-category-optional')
                @endif
                <hr class="my-2">                                      
            </div>
        </div>
       
        <div class="col-12 text-center">
                @if(!session('is_submitted'))
                    <script type="text/javascript">
                     $("#resetSelection_trigger" + {{ $subCatId }}).leanModal({
                            top: 100,
                            left: 0,
                            overlay: 0.6,
                            closeButton: ".modal_close"
                    });
                    </script>
                    <style>
                    .modal-content{
                        left:-30% !important;
                    }
                    </style>
                    <button onclick="saveEnrollment('{{ $subCatId }}')" class="col-3 my-2 p-3 fs-15 btn-primary text-uppercase"> Save Selection  </button>
                    <button id="resetSelection_trigger_selection{{ $subCatId }}"  onclick="resetSelection('{{ $subCatId }}',this)" class="col-3 p-3 btn btn-secondary text-uppercase fs-15"> Reset Selection  </button>
                    <!-- FINAL SUMBIT ENROLLMENT MODAL -->
                    <div class="modal" id="resetSelectionModal{{ $subCatId }}" style="display:none;left:40%;">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 id="resetSelectionModalTitle" class="modal-title">Confirm before "Reset"</h5>
                                <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <p id="resetSelectionModalBody" class="bg-light text-dark">
                                Do you really want to rollback to step zero for current policy type, as this step will 
                                <b class="text-uppercase">clear</b> your current selection</p>
                            </div>
                            <div class="modal-footer">
                                <button onclick="resetSelection('{{ $subCatId }}',this)" class="btn-primary btn-small">Yes, Clear Selection </button>
                                <button id="resetSelectionModalClose{{ $subCatId }}" class="btn-success btn-small modal_close"
                                data-dismiss="modal">No, Changed Mind</button>
                            </div>
                            </div>
                        </div>
                    </div>
                @endif
                <button name="closeSubCategory" class="col-3 closeSubCategory my-2 p-3 fs-15 btn-info  text-uppercase" style="color:#FFF"> Close Sub Category</button>
        </div>
    @foreach($activePolicyForSubCategoryFY as $key => $item)
        @php
        $bpsa = 0;
        $bpName = '';
        $is_lumpsum = $is_si_sa = $is_sa = $is_grade_based = FALSE;
        $base_si_factor = 0;
        if($item['policy']['is_base_plan']) {
            // first priority will be given to lumpsum value
            /* $lumpsum = $item['policy']['lumpsum_amount'];  --}}
             if (!is_null($lumpsum)) {
                $bpsa = (int)$lumpsum;
                $is_lumpsum = TRUE;
            }*/
            if ($gradeAmount) {
                $bpsa = (int)$gradeAmount;
                $is_grade_based = TRUE;
            } else {
                $sa = !is_null($item['policy']['sum_insured']) ? $item['policy']['sum_insured'] : 0;
                $sa_si = !is_null($item['policy']['si_factor']) ?
                        $sa_si = $item['policy']['si_factor'] * Auth::user()->salary : 0;
                if($sa_si > $sa) {
                    $bpsa = (int)$sa_si;
                    $is_si_sa = TRUE;
                    $base_si_factor = $item['policy']['si_factor'];
                } else {
                    $bpsa = (int)$sa;
                    $is_sa = TRUE;
                }
            }
            // name of base policy
            $bpName = $item['policy']['name'];
            break;
        }
        @endphp
    @endforeach
    @foreach($activePolicyForSubCategoryFY as $key => $item)
        @php
            //$currenySymbol = html_entity_decode($item['policy']['currency']['symbol']);
            //echo $formatter->formatCurrency(2500000, 'INR');
            //if(!$item['policy']['is_base_plan']) {
        @endphp
        <span id="planDetails{{ $item['policy']['id'] }}" style="display:none;"
            data-ptf="{{ $item['policy']['price_tag'] }}"
            data-bpName="{{ $bpName }}"
            data-pt="{{ $formatter->formatCurrency($item['policy']['points'], 'INR') }}"
            data-name="{{ $item['policy']['name'] }}"
            data-osa="{{ $formatter->formatCurrency($item['policy']['sum_insured'], 'INR') }}"
            {{-- data-allo="0" data-currs="4324" data-avail="675343"
            data-tots="{{ $item['policy']['price_tag'] }}" --}}
            data-is-sa="{{ $is_sa }}"
            data-is-si-sa="{{ $is_si_sa }}"
            {{-- data-is-lupsm="{{ $is_lumpsum }}" --}}
            data-is-grdbsd="{{ $is_grade_based }}"
            data-fypmap="{{ $item['id'] }}"
            data-isbp ="{{ $item['policy']['is_base_plan'] ? 1 : 0 }}"
            data-bpsa="@php echo $bpsa > 0 ? $formatter->formatCurrency($bpsa, 'INR') : ''; @endphp"
            data-opplsa="{{ (!$item['policy']['is_base_plan'] ? 
                $formatter->formatCurrency($item['policy']['sum_insured'], 'INR') : 0) }}"
            data-totsa="@php
                $tsa = $bpsa + (!$item['policy']['is_base_plan'] ? (int)$item['policy']['sum_insured'] : 0);
                echo $formatter->formatCurrency($tsa, 'INR');
                @endphp"
            data-isvp="{{ $item['policy']['is_point_value_based'] }}"
            data-isvbsd="{{ $item['policy']['show_value_column'] }}"
            data-annup="@php
                //echo ($item['policy']['sum_insured']) * $item['policy']['price_tag']
                echo $formatter->formatCurrency($item['policy']['points'], 'INR');
                @endphp"                
            data-annupwocurr="@php
                echo $item['policy']['points'];
                @endphp"                
            data-psd="@php
                    $fyStartDate = session('fy')['start_date'];    // @todo replace with account FY start date
                    $joiningDate = Auth::user()->hire_date;
                    $policyStartDate = $joiningDate > $fyStartDate ? $joiningDate : $fyStartDate;
                    echo date_format(date_create($policyStartDate), 'd-M-Y');
                @endphp"
            data-ped="@php
                    $fyEndDate = session('fy')['end_date'];    // @todo replace with account FY end date
                    echo date_format(date_create($fyEndDate), 'd-M-Y');
                @endphp"
            data-totdc="@php
                    $totalDays = date_diff(date_create($policyStartDate), date_create($fyEndDate));
                    echo $totalDays->days . ' Days';
                @endphp"                
            data-prorf="@php
                $prorationfactor = number_format(($totalDays->days/date_diff(date_create($fyStartDate), 
                date_create($fyEndDate))->days) * 100, '2', '.', '');
                echo $prorationfactor;
            @endphp"
            data-opplpt="@php
                $pts =0;
                if (!is_null($item['policy']['price_tag']) && $item['policy']['price_tag'] > 0) {
                    $pts = ($item['policy']['sum_insured']) * $item['policy']['price_tag'] * ($prorationfactor/100);
                } else if (!is_null($item['policy']['points'])){
                    $pts = $item['policy']['points'] * ($prorationfactor/100);
                }
                echo !$item['policy']['is_base_plan'] ? $formatter->formatCurrency(round($pts), 'INR') : 0;
                @endphp"
            data-effecp="@php // Sum Insured * price_tag * (proration_factor/100)
                $pts = 0;
                if (!is_null($item['policy']['price_tag']) && $item['policy']['price_tag'] > 0) {
                    $pts = ($item['policy']['sum_insured']) * $item['policy']['price_tag'] * ($prorationfactor/100);
                } else if (!is_null($item['policy']['points'])){
                    $pts = $item['policy']['points'] * ($prorationfactor/100);
                }
                echo !$item['policy']['is_base_plan'] ? $formatter->formatCurrency(round($pts), 'INR') : 0;
                @endphp"                
            data-totpt="@php    
                $pts = 0;
                if (!is_null($item['policy']['price_tag']) && $item['policy']['price_tag'] > 0) {
                    $pts = ($item['policy']['sum_insured']) * $item['policy']['price_tag'] * ($prorationfactor/100);
                } else if (!is_null($item['policy']['points'])){
                    $pts = $item['policy']['points'] * ($prorationfactor/100);
                }
                echo !$item['policy']['is_base_plan'] ? $formatter->formatCurrency(round($pts), 'INR') : 0;
                @endphp"
            data-totptwocurr="@php    
                $pts = 0;
                if (!is_null($item['policy']['price_tag']) && $item['policy']['price_tag'] > 0) {
                    $pts = ($item['policy']['sum_insured']) * $item['policy']['price_tag'] * ($prorationfactor/100);
                } else if (!is_null($item['policy']['points'])){
                    $pts = $item['policy']['points'] * ($prorationfactor/100);
                }
                echo !$item['policy']['is_base_plan'] ? round($pts) : 0;
                @endphp"
            data-memcvrd="@php
                echo ($item['policy']['dependent_structure'])
                @endphp"
            data-prntSbLim="@php
                echo $item['policy']['is_parent_sublimit'] ? $formatter->formatCurrency($item['policy']['parent_sublimit_amount'], 'INR') : 0;
                @endphp"
            data-corem="@php
                echo $base_si_factor . 'X of CTC';
                @endphp"
            data-coresa="@php
                echo $formatter->formatCurrency($bpsa, 'INR');
                @endphp"
            data-jongDate="@php
                echo Auth::user()->hire_Date;
                @endphp"
        >&nbsp;</span>
        @php
            //}
        @endphp
    @endforeach        
@else 
    <div class="row">
        <div class="col-12">
            <hr class="my-2">
            <div class="row">                                
                <div class="section-heading">
                    <h4 class="py-1">Optional Benefits</h4>                                        
                </div>
                <div class="col-10 offset-1 mb-3 text-center alert-danger">
                    No optional benefits present under this category!!!
                </div>
            </div>                                        
        </div>
        <div class="col-10 offset-1 text-center">
            <br>
        </div>
    </div>
@endif