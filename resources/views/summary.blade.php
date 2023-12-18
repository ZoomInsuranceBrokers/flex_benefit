@php
    //dd($mapUserFYPolicyData);
    $data['gradeAmtData'] = session('gradeData');
    $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
    $summaryData = [];
    $summaryDataBaseDefault = [];
    foreach($mapUserFYPolicyData as $item) {
        if(!$item['fy_policy']['policy']['is_base_plan'] && !$item['fy_policy']['policy']['is_default_selection']) {
            $summaryData[$item['id']]['category'] = $item['fy_policy']['policy']['subcategory']['categories']['name'];
            $summaryData[$item['id']]['subcategory'] = $item['fy_policy']['policy']['subcategory']['name'];
            $summaryData[$item['id']]['subcategory_id'] = $item['fy_policy']['policy']['subcategory']['id'];
            $summaryData[$item['id']]['policy'] = $item['fy_policy']['policy']['name'];
            $summaryData[$item['id']]['policyDetail'] = $item['fy_policy']['policy'];
            $summaryData[$item['id']]['summary'] = json_decode(base64_decode($item['encoded_summary']));
            $summaryData[$item['id']]['points'] = $item['points_used'];
            $summaryData[$item['id']]['is_base_default'] = FALSE;
        } else {
            $summaryDataBaseDefault[$item['id']]['category'] = $item['fy_policy']['policy']['subcategory']['categories']['name'];
            $summaryDataBaseDefault[$item['id']]['subcategory'] = $item['fy_policy']['policy']['subcategory']['name'];
            $summaryDataBaseDefault[$item['id']]['subcategory_id'] = $item['fy_policy']['policy']['subcategory']['id'];
            $summaryDataBaseDefault[$item['id']]['policy'] = $item['fy_policy']['policy']['name'];
            $summaryDataBaseDefault[$item['id']]['policyDetail'] = $item['fy_policy']['policy'];
            $summaryDataBaseDefault[$item['id']]['summary'] = json_decode(base64_decode($item['encoded_summary']));
            $summaryDataBaseDefault[$item['id']]['points'] = $item['points_used'];
            $summaryDataBaseDefault[$item['id']]['is_base_default'] = TRUE;
        }
    }

    // delete sub category row for which no optional policy is selected
    foreach ($summaryDataBaseDefault as $baseDefaultKey => $baseDefaultRow) {
        foreach ($summaryData as $summaryRow){
            if ($summaryRow['subcategory_id'] == $baseDefaultRow['subcategory_id']) {
                unset($summaryDataBaseDefault[$baseDefaultKey]);
            }
        }
    }
    $summaryData = $summaryData + $summaryDataBaseDefault;
    //dd([$summaryDataBaseDefault, $summaryData]);
    //dd($summaryData);
    ksort($summaryData);
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
                                    echo !$item['is_base_default'] && strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.';
                                    //echo strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.';
                                @endphp
                            </dd>
                        </dl>
                    </td>
                    <td>                    
                        <dl>
                            <dt>Additional Coverage</dt>
                            <dd>{{ !$item['is_base_default'] ? $item['summary']->opplsa : 'N.A.' }}</dd>
                        </dl>
                    </td>
                    <td>
                        <dl>
                            <dt>Total Coverage</dt>
                            <dd> @php
                                    $encryptedData =  Auth::user()->salary;
                                    $encryptionKey = 'QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4=';
                                    $initializationVector = 'G4bfDHjL3gXiq5NCFFGnqQ==';

                                    // Decrypt the data
                                    $cipher = "aes-256-cbc";
                                        $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

                                        $decryptedData = openssl_decrypt(base64_decode($encryptedData), $cipher, base64_decode($encryptionKey), $options, base64_decode($initializationVector));

                                        if ($decryptedData === false) {
                                            echo "Error during decryption: " . openssl_error_string() . PHP_EOL;
                                        } else {
                                            $decryptedData = floatval(rtrim($decryptedData, "\0"));
                                        }

    
                            if (!$item['is_base_default']) {
                                echo $item['summary']->totsa;
                            } else {                                
                                if (count($data['gradeAmtData']) && array_key_exists($item['policyDetail']['subcategory']['categories']['id'], $data['gradeAmtData'])) {
                                    $bpsa = (int)$data['gradeAmtData'][$item['policyDetail']['subcategory']['categories']['id']];
                                    $is_grade_based = TRUE;
                                } else {
                                    $sa = !is_null($item['policyDetail']['sum_insured']) ? $item['policyDetail']['sum_insured'] : 0;
                                    $sa_si = !is_null($item['policyDetail']['si_factor']) ?
                                            $sa_si = $item['policyDetail']['si_factor'] * $decryptedData : 0;
                                    if($sa_si > $sa) {
                                        $bpsa = (int)$sa_si;
                                        $is_si_sa = TRUE;
                                        $base_si_factor = $item['policyDetail']['si_factor'];
                                    } else {
                                        $bpsa = (int)$sa;
                                        $is_sa = TRUE;
                                    }
                                }
                                echo $formatter->formatCurrency(round($bpsa), 'INR');
                            }                            
                            @endphp
                            </dd>
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
