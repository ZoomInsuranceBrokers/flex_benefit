<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary</title>
    <style>
        body {
            font-family: 'Calibri', sans-serif;
        }

        .bold-text {
            font-weight: bold;
            font-size: 1.5rem;
            /* Adjust the font size as needed */
        }

        .light-text {
            font-weight: lighter;
        }

        .first-part {
            width: 100%;
            height: auto;
        }

        .first-part img {
            width: 100%;
            height: 30vh;
        }

        .left-image {
            width: 3%;
            height: inherit;
            float: left;
        }

        .second-part {
            width: 100%;
            height: 10vh;
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20px;
        }

        .lines {
            margin-right: 20vh;
            display: flex;
        }

        .second1 {
            display: flex;
        }

        .semi-circle {
            position: relative;
            width: 4vh;
            height: 7vh;
            border-width: 15px 15px 15px 0;
            border-style: solid;
            border-color: #B5B5B5;
            border-top-right-radius: 60px;
            border-bottom-right-radius: 60px;
        }

        .right-semi-circle {
            position: relative;
            width: 5vh;
            height: 8vh;
            border-width: 15px 0 15px 15px;
            border-style: solid;
            border-color: #03313D;
            border-top-left-radius: 60px;
            border-bottom-left-radius: 60px;
        }

        .diagonal-line {
            width: 2px;
            height: 14vh;
            background-color: #03313D;
            transform: rotate(45deg);
            transform-origin: 0 100%;
            margin: 10px;
            position: relative;
            bottom: 35px;
        }

        .box {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 7vh;
            border-radius: 10px;
            text-align: center;
            font-weight: 900;
            background-color: #B5B5B5;
        }

        .box1 {
            width: 38%;
            margin: 0 1%;
        }

        .box2 {
            width: 59%;
            margin-right: 1%;
        }

        .box1 ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .box1 li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 10px;
        }

        .box1 li::before {
            content: "";
            position: absolute;
            left: 0;
        }

        .inner-li {
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .inner-li img {
            vertical-align: middle;
            margin-right: 5px;
            width: 3vh;
        }

        .third-part {
            width: 100%;
        }

        .forth-part {
            width: 100%;
            height: 20vh;
            display: flex;
            flex-direction: column-reverse;
            align-items: flex-end;
        }

        .fifth-part {
            width: 100%;
            margin-top: 3vh;
            height: auto;
        }

        .fifth-part img {
            width: 100%;
            height: 20vh;
        }


        @media (max-width: 600px) {
            .first-part {
                width: 100%;
                height: 10vh;
            }

            .first-part img {
                width: 100%;
                height: auto;
            }
        }

        .top-div {
            width: 70%;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Adjust child elements as needed */
        .first-part,
        .second-part,
        .third-part,
        .forth-part,
        .fifth-part {
            text-align: center;
            /* Center the content within each section */
        }
    </style>
</head>

<body>

    <!-- Include the html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.js"></script>


    @php
    $data['gradeAmtData'] = session('gradeData');
    $formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
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
    ksort($summaryData);;
    @endphp
    @if(count($summaryData))
    <!-- <button id="download-summary-pdf">Download PDF</button> -->

    <div class="top-div">
        <div class="first-part">
            <img src="{{asset('assets/images/invoice-01.jpg')}}" alt="">
        </div>
        <div class="second-part">
            <div class="second1">
                <div class="semi-circle"></div>
                <div class="">
                    <h3 class="light-text" style="margin-left:7vh;">{{ Auth::user()->fname . ' '. Auth::user()->lname }}</h3>
                    <h3 class="light-text" style="margin-left:7vh;">{{ Auth::user()->employee_id  }}</h3>
                </div>
            </div>
            <div class="lines">
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
                <div class="diagonal-line"></div>
            </div>
        </div>

        <div class="third-part">
            <div style="display: flex;margin-top: 5vh;">
                <div class="bold-text box box1">Name</div>
                <div class="bold-text box box2">Detail</div>
            </div>
            @foreach($summaryData as $item)
            <div style="display: flex;margin-top: 1vh; border-bottom: 3px solid #B5B5B5;">
                <div class="box1 bold-text ">
                    <ul>
                        <li>{{ $item['category'] }}
                            <ul class="inner-li" style="margin-top: 2vh;">
                                <li>
                                    <img src="{{asset('assets/images/forward.png')}}" alt="Image Description">
                                    {{ $item['subcategory'] }}
                                    <ul class="inner-li" style="margin-top: 2vh;">
                                        <li>
                                            <img src="{{asset('assets/images/forward.png')}}" alt="Image Description">
                                            {{ $item['policy'] }}
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="box2 ">
                    <div style="display:flex;justify-content:space-between;align-items:center; border-bottom: 1px solid #B5B5B5;">
                        <p style="width: 30%;padding: 10px; display: inline; margin: 0px;" class="bold-text">Base Plan Coverage <br><span class="light-text">
                                @php
                                echo !$item['is_base_default'] && strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.';
                                //echo strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.';
                                @endphp</span></p>
                        <p style="width: 30%;padding: 10px; display: inline; margin: 0px;" class="bold-text">Optional Coverage <br><span class="light-text">
                                {{ !$item['is_base_default'] ? $item['summary']->opplsa : 'N.A.' }}
                            </span></p>
                        <p style="width: 30%;padding: 10px; display: inline; margin: 0px;" class="bold-text">Total Coverage <br> <span class="light-text">

                        @php
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
                        </span></p>
                    </div>
                    <p style="width: 100%;padding:10px; margin: 0px; text-align: left;" class="bold-text">Point Used <br> <span class="light-text">{{  $formatter->formatCurrency($item['points'], 'INR') }}</span></p>
                </div>
            </div>
            @endforeach


            <div style="display: flex;justify-content: center;flex-direction: column; height:30vh;  border-bottom: 3px solid #B5B5B5; " class="bold-text">
                <p style="margin:0px; margin-left: 47%;  display: inline;">Total Points Used: {{number_format(Auth::user()->points_used,2) }}</p>
                <p style="margin:0px; margin-left: 47%; display: inline;">Salary Contribuation: {{number_format(Auth::user()->points_used - 5000,2) }}</p>
                <p style="margin:0px; margin-left: 47%; display: inline;">Monthly Installment: {{ number_format((Auth::user()->points_used - 5000) / 6, 2) }} </p>
            </div>
        </div>

        <div class="forth-part">
            <div class="right-semi-circle"></div>
        </div>
        <div class="fifth-part">
            <img src="{{asset('assets/images/bottom-design.png')}}" alt="">
        </div>


    </div>
    @else
    echo 'Enrollment details not found. Please select appropriate policies and submit before <Enrollment Date>';
        @endif

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('download-summary-pdf').addEventListener('click', function() {
                    // Get the target div
                    const claimData = document.getElementById('top-div');

                    // Use html2canvas to capture the div's content as an image
                    html2canvas(claimData).then(canvas => {
                        // Convert the canvas to a data URL
                        const imgData = canvas.toDataURL('image/png');

                        // Create a temporary link element
                        const downloadLink = document.createElement('a');
                        downloadLink.href = imgData;
                        downloadLink.download = 'claim.png';

                        // Trigger a click event on the link to start the download
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    });
                });
            });
        </script>



</body>

</html>