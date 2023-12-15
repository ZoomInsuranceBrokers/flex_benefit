@php
    //dd($mapUserFYPolicyData);
    $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
    $summaryData = [];
    foreach($mapUserFYPolicyData as $item) {
        if(!$item['fy_policy']['policy']['is_base_plan'] && !$item['fy_policy']['policy']['is_default_selection']) {
            $summaryData[$item['id']]['category'] = $item['fy_policy']['policy']['subcategory']['categories']['name'];
            $summaryData[$item['id']]['subcategory'] = $item['fy_policy']['policy']['subcategory']['name'];
            $summaryData[$item['id']]['policy'] = $item['fy_policy']['policy']['name'];
            $summaryData[$item['id']]['summary'] = json_decode(base64_decode($item['encoded_summary']));
            $summaryData[$item['id']]['points'] = $item['points_used'];
        }
    }
    //dd($summaryData);
@endphp
@if(count($summaryData)) 
    <table class="table" id="summary_tbl" style="border-bottom:2px solid #000;">
        <thead class="thead-light">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Detail</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
        @php $totalPoints = 0; @endphp
        @foreach($summaryData as $item)
            
            @php 
            //dd(array_key_exists('data-isvbsd',$item) ? $item['data-isvbsd'] : '0');
             $totalPoints += $item['points']; 
             @endphp
                <tr style="border-top:2px solid #000;">
                    <th rowspan="2" scope="row" class="fs-14">
                        <span>{{ $item['category'] }}</span><br>
                        <span class="summarySubCat">{{ $item['subcategory'] }}</span><br>
                        <span class="summaryPolName">{{ $item['policy'] }}</span>
                    </th>
                    <td>
                        <dl>
                            <dt>Base Plan Coverage</dt>
                            <dd>
                                @php
                                    echo strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.';
                                @endphp
                            </dd>
                        </dl>
                    </td>
                    <td>                    
                        <dl>
                            <dt>Additional Coverage</dt>
                            <dd>{{ $item['summary']->opplsa }}</dd>
                        </dl>
                    </td>
                    <td>
                        <dl>
                            <dt>Total Coverage</dt>
                            <dd>{{ $item['summary']->totsa }}</dd>
                        </dl>
                    </td>
                </tr>
                <tr>                   
                    <td>
                        <dl>
                            <dt>Points Used</dt>
                            <dd>{{  $formatter->formatCurrency($item['points'], 'INR') }}</dd>
                        </dl>
                    </td>        
                </tr>
        @endforeach
                {{-- <tr style="border-top:2px solid #000;">
                    <th colspan="3" scope="row" class="fs-14">
                        <span>{{ $item['category'] }}</span><br>
                        <span class="summarySubCat">{{ $item['subcategory'] }}</span><br>
                        <span class="summaryPolName">{{ $item['policy'] }}</span>
                    </th>
                </tr> --}}
                <tr style="border-top:2px solid #000;">                    
                    <td colspan="4">
                        <dl>
                            <dt style="text-align:right;">Total Points Used</dt>
                            <dd style="text-align:right;">{{ $formatter->formatCurrency($totalPoints, 'INR') }}</dd>
                        </dl>
                    </td>        
                </tr>
    
        </tbody>
    </table>
    <br>
    @php //if (Auth::users()->points_available > 0) { 
    @endphp
    @php //} 
    @endphp

    

@else
    <h4 class="mb-5 mt-5 text-center text-danger">Enrollment details not found.<br>Please select appropriate policies and submit before enrollment window ends</h4>
@endif
