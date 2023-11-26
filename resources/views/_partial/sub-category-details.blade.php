@if(isset($activePolicyForSubCategoryFY) && count($activePolicyForSubCategoryFY))
    @php
    
        $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);  
        //dd($activePolicyForSubCategoryFY[0]);
        $subCatId = array_key_exists('policy', $activePolicyForSubCategoryFY[0]) ? 
            $activePolicyForSubCategoryFY[0]['policy']['ins_subcategory_id_fk'] : 0;
    @endphp
    <div class="row optnBenft" data-sc-id="{{ $subCatId }}">
        <div class="col-12">
            <hr class="my-2">
            <div class="row">                                
                <div class="section-heading">
                    <h4 class="py-1">Optional Benefits</h4>                                        
                </div>
                <div class="col-3 offset-1">
                    <form id="subCategoryForm{{ $subCatId }}">
                    <table class="tab-content-table table-responsive mb-3 fs-11 col-3">
                        <th>Additional Coverage</th>
                        <th>TopUp Value</th>
                        @foreach($activePolicyForSubCategoryFY as $key => $item)
                            @php //$subCatId = $item['ins_subcategory_id_fk']; 
                                $currenySymbol = html_entity_decode($item['policy']['currency']['symbol']);
                                
                            @endphp
                            <tr @php echo ($item['policy']['is_base_plan']) ? 'style="display:none"':'';@endphp>
                                <td>
                                    <input name="plan{{ $subCatId }}" data-sc-id="{{ $subCatId }}" data-plan-id="{{  $item['policy']["id"] }}"
                                    data-default-select="{{ $item['policy']['is_default_selection'] }}"
                                    type="{{ $item['policy']['is_multi_selectable'] ? 'checkbox' : 'radio' }}" 
                                    {{ $item['policy']['is_default_selection'] ? 'checked' : '' }}
                                    id='planId{{ $item['policy']["id"] }}' 
                                            value="{{ $item['policy']['id'] }}" />
                                    <label for='planId{{ $item['policy']["id"] }}'>
                                        {{ $item['policy']["name"] }}
                                    </label>
                                </td>
                                <td>{{ $formatter->formatCurrency($item['policy']["sum_insured"], 'INR') }}</td>
                            </tr>
                        @endforeach                        
                    </table>
                    </form>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-6">
                            <div class="section-heading">
                                <h4 class="mt-1 mb-1">Coverage Period</h4>                                        
                            </div>   
                            <div class="fp-numbers col-10 offset-1" id="fp-numbers-summary{{ $subCatId }}">                                         
                                <div class="row fs-12">
                                    <div class="col-12">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="">
                                                    @php
                                                        $fyStartDate = '2023-04-01';        // @todo replace with account FY start date
                                                        $joiningDate = Auth::user()->hire_date;
                                                        echo ($joiningDate > $fyStartDate) ? 'Joining /' : '';
                                                    @endphp
                                                    Policy Start Date
                                            </dt>
                                            <dd class="col"><label id="psd{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                    <div class="col-12">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="">Policy End Date</dt>
                                            <dd class="col"><label id="ped{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                    <div class="col-12">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Total allocated points for current user">Total Days Covered</dt>
                                            <dd class="col"><label id="totdc{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="section-heading">
                                <h4 class="mt-1 mb-1">Premium Calculations</h4>                                        
                            </div>   
                            <div class="fp-numbers col-10 offset-1" id="fp-numbers-premiumcalc{{ $subCatId }}">                                                         
                                <div class="row fs-12">
                                    <div class="col-12">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Price Tag shows xyz lorem ipsum">Price Tag Factor</dt>
                                            <dd class="col">Price tag per thousand of sum assured : <label id="ptf{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                    <div class="col-12">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Points selected across plans">Proration Factor(%)</dt>
                                            <dd class="col" id="prorationFactor{{ $subCatId }}"><label id="prorf{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                    <div class="col-6">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="right" title="Price Tag for currently selected plan">Annual Premium</dt>
                                            <dd class="col" id="annualPremium{{ $subCatId }}"><label id="annup{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                    <div class="col-6">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Points available after selections">Effective Premium</dt>
                                            <dd class="col" id="effectivePremium{{ $subCatId }}"><label id="effecp{{ $subCatId }}"></label></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="section-heading">
                                <h4 class="mt-1 mb-1">Summary</h4>                                        
                            </div>   
                            <div class="fp-numbers" id="fp-numbers-coverage{{ $subCatId }}">                                            
                                <div class="row fs-13">
                                    <div class="col-4 offset-1">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Optional Sum Insured shows xyz lorem ipsum">
                                                Benefit
                                            </dt>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Optional Sum Insured shows xyz lorem ipsum">
                                                Sum Assured
                                            </dt>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Optional Sum Insured shows xyz lorem ipsum">
                                                Points/Value
                                            </dt>
                                        </dl>
                                    </div>
                                </div>                               
                                <div class="row fs-13" style="display:none;" id="coresumRow{{ $subCatId }}">
                                    <div class="col-4 offset-1">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Total allocated points for current user">
                                                <label id="bpName{{ $subCatId }}"></label>
                                            </dt>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dd class="col" id="currentPlanValue{{ $subCatId }}">
                                                <label id="bpsa{{ $subCatId }}"></label>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dd class="col">
                                                <label>-</label>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>                               
                                <div class="row fs-13">
                                    <div class="col-4 offset-1">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Total allocated points for current user">
                                                Optional/TopUp
                                                </dt>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dd class="col">
                                                <label id="opplsa{{ $subCatId }}"></label>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dd class="col">
                                                <label id="opplpt{{ $subCatId }}"></label>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>                                      
                                <div class="row fs-13">
                                    <div class="col-4 offset-1">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Total allocated points for current user">
                                                Total
                                                </dt>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dd class="col">
                                                <label id="totsa{{ $subCatId }}"></label>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <dl>
                                            <dd class="col">
                                                <label id="totpt{{ $subCatId }}"></label>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="fp-numbers-mcoverage{{ $subCatId }}">
                        <div class="col">
                            <div class="section-heading">
                                <h4 class="mt-1 mb-1">Member Covered</h4>                                        
                            </div>   
                            <div>                                            
                                <div class="row fs-12 text-center">
                                    <div class="col-10 offset-1">
                                        <dl>
                                            <dt data-toggle="tooltip"
                                                data-placement="top" title="Optional Sum Insured shows xyz lorem ipsum">
                                                <div id="memcvrd{{ $subCatId }}"></div>
                                            </dt>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col" id="parentSubLimit{{ $subCatId }}" style="display:none;">
                            <div class="section-heading">
                                <h4 class="mt-1 mb-1">Parent Sub Limit</h4>                                        
                            </div>   
                            <div>                                            
                                <div class="row fs-13 text-center">
                                    <div class="col">
                                        <dl>
                                            <dt class="col" data-toggle="tooltip"
                                                data-placement="top" title="Optional Sum Insured shows xyz lorem ipsum">
                                                <label id="prntSbLim{{ $subCatId }}"></label>
                                            </dt>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                                           
                <hr class="my-2">                                      
            </div>
        </div>
        
        <div class="col-12 text-center">
                <button onclick="saveEnrollment('{{ $subCatId }}')" class="col-3 my-2 p-3 fs-15 btn-primary text-uppercase"> Save Selection  </button>
                <button name="closeSubCategory" class="col-3 closeSubCategory my-2 p-3 fs-15 btn-info  text-uppercase" style="color:#FFF"> Close Sub Category</button>
        </div>
    @foreach($activePolicyForSubCategoryFY as $key => $item)
        @php
        $bpsa = 0;
        $bpName = '';
        $is_lumpsum = $is_si_sa = $is_sa = FALSE;
        $base_si_factor = 0;
        if($item['policy']['is_base_plan']) {
            // first priority will be given to lumpsum value
            $lumpsum = $item['policy']['lumpsum_amount']; //@todo check Data and verify logic of SA
            if (!is_null($lumpsum)) {
                $bpsa = (int)$lumpsum;
                $is_lumpsum = TRUE;
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
            data-pt="{{ $item['policy']['price_tag'] }}"
            {{-- data-osa="{{ $currenySymbol . $item['policy']['sum_insured'] }}" --}}
            data-osa="{{ $formatter->formatCurrency($item['policy']['sum_insured'], 'INR') }}"
            data-allo="0" data-currs="4324" data-avail="675343"
            data-tots="{{ $item['policy']['price_tag'] }}"
            data-is-sa="{{ $is_sa }}"
            data-is-si-sa="{{ $is_si_sa }}"
            data-is-lupsm="{{ $is_lumpsum }}"
            data-fypmap="{{ $item['id'] }}"
            data-isbp ="{{ $item['policy']['is_base_plan'] ? 1 : 0 }}"
            data-bpsa="@php echo $bpsa > 0 ? $formatter->formatCurrency($bpsa, 'INR') : ''; @endphp"
            data-opplsa="{{ (!$item['policy']['is_base_plan'] ? 
                $formatter->formatCurrency($item['policy']['sum_insured'], 'INR') : 0) }}"
            data-totsa="@php
                $tsa = $bpsa + (!$item['policy']['is_base_plan'] ? (int)$item['policy']['sum_insured'] : 0);
                echo $formatter->formatCurrency($tsa, 'INR');
                @endphp"

            data-annup="@php
                //echo ($item['policy']['sum_insured']) * $item['policy']['price_tag']
                echo $formatter->formatCurrency($item['policy']['points'], 'INR');
                @endphp"                
            data-psd="@php
                    $fyStartDate = '2023-04-01';    // @todo replace with account FY start date
                    $joiningDate = Auth::user()->hire_date;
                    $policyStartDate = $joiningDate > $fyStartDate ? $joiningDate : $fyStartDate;
                    echo date_format(date_create($policyStartDate), 'd-M-Y');
                @endphp"
            data-ped="@php
                    $fyEndDate = '2024-03-31';    // @todo replace with account FY end date
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
                echo $currenySymbol . $item['policy']['base_plan_sum_assured_text'];
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