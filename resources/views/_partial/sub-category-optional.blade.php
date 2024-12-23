@php
    //dd($userPolData[0]->ip_id);
@endphp

<div class="col-5 additional-table">
    <form id="subCategoryForm{{ $subCatId }}" class="table-responsive">
    <table class="tab-content-table table table-bordered boder-primary mb-3 fs-11">
        <th>Additional Coverage</th>
        <th>TopUp Value</th>
        <th>Points</th>
        @foreach($activePolicyForSubCategoryFY as $key => $item)
            @php //$subCatId = $item['ins_subcategory_id_fk']; 
                $currenySymbol = html_entity_decode($item['policy']['currency']['symbol']);
            @endphp
            <tr @php echo ($item['policy']['is_base_plan']) ? 'style="display:none"':'';@endphp>
                <td >
                    <input name="plan{{ $subCatId }}" data-sc-id="{{ $subCatId }}" data-plan-id="{{  $item['policy']["id"] }}"
                    data-default-select="{{ $item['policy']['is_default_selection'] }}"
                    type="{{ $item['policy']['is_multi_selectable'] ? 'checkbox' : 'radio' }}" 
                    @php
                        if (count($userPolData)) {
                            foreach($userPolData as $upRow) {
                                if($item['policy']['id']==$upRow->ip_id){
                                    echo 'checked';
                                }
                            }
                        } else if ($item['policy']['is_default_selection']) {
                            echo 'checked';
                        }
                    @endphp 
                    id='planId{{ $item['policy']["id"] }}' 
                            value="{{ $item['policy']['id'] }}" />
                    <label for='planId{{ $item['policy']["id"] }}'>
                        {{ $item['policy']["name"] }}
                    </label>
                </td>
                <td>{{ number_format($item['policy']["sum_insured"], 0, '.', ',') }} </td>
                <td id="policyCalcPoints{{ $item['policy']['id'] }}">{{ number_format($item['policy']["points"], 0, '.', ',') }} </td>

            </tr>
        @endforeach                        
    </table>
    </form>
</div>
<div class="col-7">
    <div class="row">
        <div class="col-6 text-center custom-heading">
            <div class="section-heading">
                <h4 class="mt-1 mb-1">Coverage Period</h4>                                        
            </div>   
            <div class="fp-numbers col" id="fp-numbers-summary{{ $subCatId }}">                                         
                <div class="row fs-12">
                    <div class="col-12">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" title="">
                                    @php
                                        $fyStartDate = session('fy')['start_date'];        // @todo replace with account FY start date
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
        <div class="col-6 text-center custom-heading">
            <div class="section-heading">
                <h4 class="mt-1 mb-1">Premium Calculations</h4>                                        
            </div>   
            <div class="fp-numbers col" id="fp-numbers-premiumcalc{{ $subCatId }}">                                                         
                <div class="row fs-12">
                    <div class="col-12">
                        {{-- <dl id="ptfContainer{{ $subCatId }}" style="display:none;">
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top">Price Tag Factor</dt>
                            <dd class="col">Price tag per thousand of sum assured : <label id="ptf{{ $subCatId }}"></label></dd>
                        </dl>
                        <dl id="ptsContainer{{ $subCatId }}" style="display:none">
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top">Flex Points</dt>
                            <dd class="col">Fixed flex points: <label id="pt{{ $subCatId }}"></label></dd>
                        </dl> --}}                        
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top">Plan Name</dt>
                            <dd class="col"><label id="planName{{ $subCatId }}"></label></dd>
                        </dl> 
                    </div>
                    <div class="col-12">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" title="Points selected across plans">Proration Factor(%)</dt>
                            <dd class="col" id="prorationFactor{{ $subCatId }}"><label id="prorf{{ $subCatId }}"></label></dd>
                        </dl>
                    </div>
                    <div class="col-6 pe-0 col-left">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="right" title="Price Tag for currently selected plan">Annual Premium</dt>
                            <dd class="col" id="annualPremium{{ $subCatId }}"><label id="annup{{ $subCatId }}"></label></dd>
                        </dl>
                    </div>
                    <div class="col-6 ps-0 col-right">
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
        <div class="col text-center custom-heading">
            <div class="section-heading">
                <h4 class="mt-1 mb-1">Summary</h4>                                        
            </div>   
            <div class="fp-numbers" id="fp-numbers-coverage{{ $subCatId }}">                                            
                <div class="row fs-13">
                    <div class="col-4">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" >
                                Benefit
                            </dt>
                        </dl>
                    </div>
                    <div class="col-4">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" >
                                Sum Assured
                            </dt>
                        </dl>
                    </div>
                    <div class="col-4">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" >
                                Points/Value
                            </dt>
                        </dl>
                    </div>
                </div>                               
                <div class="row fs-13" style="display:none;" id="coresumRow{{ $subCatId }}">
                    <div class="col-4">
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
                    <div class="col-4">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" title="Total allocated points for current user">
                                Optional/TopUp
                                </dt>
                        </dl>
                    </div>
                    <div class="col-4">
                        <dl>
                            <dd class="col">
                                <label id="opplsa{{ $subCatId }}"></label>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-4">
                        <dl>
                            <dd class="col">
                                <label id="opplpt{{ $subCatId }}"></label>
                            </dd>
                        </dl>
                    </div>
                </div>                                      
                <div class="row fs-13">
                    <div class="col-4">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" title="Total allocated points for current user">
                                Total
                                </dt>
                        </dl>
                    </div>
                    <div class="col-4">
                        <dl>
                            <dd class="col">
                                <label id="totsa{{ $subCatId }}"></label>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-4">
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
        <div class="col text-center custom-heading">
            <div class="section-heading">
                <h4 class="mt-1 mb-1">Member Covered</h4>                                        
            </div>   
            <div>                                            
                <div class="row fs-12">
                    <div class="col-12">
                        <dl>
                            <dt data-toggle="tooltip"
                                data-placement="top" style="display:flex;     font-size: 14px;">
                                <div id="memcvrd{{ $subCatId }}" ></div>
                            </dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col" id="parentSubLimit{{ $subCatId }}" style="display:none;">
            <div class="section-heading">
                <h4 class="mt-1 mb-1">Parent Sub Limit</h4>                                        
            </div>   
            <div>                                            
                <div class="row fs-13 text-center">
                    <div class="col">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" >
                                <label id="prntSbLim{{ $subCatId }}"></label>
                            </dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    <div class="row mb-3">
        <div class="col custom-heading" id="parentSubLimit{{ $subCatId }}" style="display:none;">
            <div class="section-heading">
                <h4 class="mt-1 mb-1">Parent Sub Limit</h4>                                        
            </div>   
            <div>                                            
                <div class="row fs-13 text-center">
                    <div class="col">
                        <dl>
                            <dt class="col" data-toggle="tooltip"
                                data-placement="top" >
                                <label id="prntSbLim{{ $subCatId }}"></label>
                            </dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  