@php
    //dd($mapUserFYPolicyData);
    $summaryData = [];
    foreach($mapUserFYPolicyData as $item) {
        if(!$item['fy_policy']['policy']['is_base_plan']) {
            $summaryData[$item['id']]['category'] = $item['fy_policy']['policy']['subcategory']['categories']['name'];
            $summaryData[$item['id']]['subcategory'] = $item['fy_policy']['policy']['subcategory']['name'];
            $summaryData[$item['id']]['policy'] = $item['fy_policy']['policy']['name'];
            $summaryData[$item['id']]['summary'] = json_decode(base64_decode($item['encoded_summary']));
        }
    }
    //dd($summaryData[$item['id']]['summary']);
@endphp
<style>
table, table tr, table tr td { border:1px solid #000; }

</style>
@if(count($summaryData)) 
    <table>
        <thead class="thead-light">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Detail</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($summaryData as $item)        
                <tr>
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
                                    //$formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
                                    //echo $formatter->formatCurrency(2500000, 'INR');
                                    echo strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.';
                                @endphp
                            </dd>
                        </dl>
                    </td>
                    <td>                    
                        <dl>
                            <dt>Optional Coverage</dt>
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
                    {{-- <td>
                        <dl>
                            <dt>Annual Points</dt>
                            <dd>{{ $item['summary']->annup }}</dd>
                        </dl> 
                    </td>
                    <td>                           
                        <dl>
                            <dt>Effective Points</dt>
                            <dd>{{ $item['summary']->opplpt }}</dd>
                        </dl>
                    </td> --}}
                    <td>
                        <dl>
                            <dt>Total Points</dt>
                            <dd>{{ $item['summary']->totpt }}</dd>
                        </dl>
                    </td>        
                </tr>
        @endforeach
    
        </tbody>
    </table>
@else
    echo 'Enrollment details not found. Please select appropriate policies and submit before <Enrollment Date>';
@endif
