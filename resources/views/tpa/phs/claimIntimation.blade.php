@extends('layouts.layout')

@section('content')
<style>
    .bg-color {
        height: 93vh;
        width: 100vw;
        background-color: #D3D3D3;

    }

    .bg-image {
        height: 89vh;
        width: 98vw;
        position: relative;
        left: 1vw;
        border-bottom-left-radius: 150px;
        background-image: url('{{ asset('assets/images/bg-image.jpeg') }}');
        background-size: cover;
        margin-bottom: 5px;
    }

    .foam {
        width: 43%;
        position: absolute;
        left: 52%;
        top: 14%;
        border-radius: 30px;
        background-color: #FFFFFF;
        z-index: 1;
    }

    .left-content {
        position: absolute;
        top: 20%;
        left: 6%;
        color: #ffffff;
        width: 40%;

    }

    .form_name {
        margin: 0px;
        color: #03313E;
        font-family: sans-serif;
        margin: 2vh 0vh 2vh 5%;
        font-size: 30px;
    }


    .main_content_in_foam {
        padding: 1% 5%;
    }

    .row_inputs {
        display: flex;
        justify-content: space-between;

    }

    .inputs-div {
        width: 48%;
        margin-bottom: 10px;
    }

    .inputs-div input,
    .inputs-div select {
        font-family: Calibri;
        /* font-style: italic; */
        padding: 8px;
        font-size: 16px;
        border-radius: 10px;
        border: 1px solid #03313E;
        width: 100%;
    }

    .inputs-div input:focus {
        border: 2px solid #03313E;
    }

    .submit {
        text-align: center;
    }

    .claim-button {
        background-color: #03313E;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 10px;
        transition: background-color 0.3s ease;
        box-shadow: 0px 4px 6px rgba(128, 128, 128, 0.3);
    }

    .main-heading {
        font-family: Calibri, sans-serif;
        font-weight: bold;
        font-size: 50px;
        margin: 0px;
    }

    .sub-heading {
        font-family: Calibri, sans-serif;
        font-weight: bold;
        font-size: 30px;
        margin: 0px;
        margin-top: 1vh;
        margin-bottom: 2vh;
    }

    .des-content {
        font-family: Calibri, sans-serif;
        font-weight: normal;
        font-size: 18px;
        margin: 0px;
    }

    .disclaimer {
        font-family: Calibri, sans-serif;
        font-weight: normal;
        margin-top: 5px;

    }

    @media only screen and (max-width: 768px) {
        .bg-color {
            height: 100%;
        }

        .main-heading {
            font-size: 37px;
        }

        .sub-heading {
            font-size: 20px;
        }

        .des-content {
            font-size: 15px;
        }

        .left-content {
            width: 90%;
            top: 13%;
            left: 5%;
        }

        .foam {
            width: 100%;
            left: 0px;
            top: 53%;
        }

        .row_inputs {
            flex-direction: column;
        }

        .inputs-div {
            width: 100%;
        }

        .inputs-div input,
        .inputs-div select {
            width: 100%;
        }

        .disclaimer {
            margin-top: 15px;
        }
    }
</style>


<div class="bg-color">
    <div class="bg-image"></div>

    <div class="left-content">
        <h1 class="main-heading">Notify Us of
            Your Claim</h1>
        <h2 class="sub-heading">
            Effortlessly Intimate Your Claim through our Online Form</h2>
        <p class="des-content">Welcome
            to our claim intimation page. Please fill out the form below to notify us of your claim, and our
            team will promptly assist you in processing your request. Thank you for choosing us to handle your
            claim.</p>

    </div>

    <div class="foam">
        <h2 class="form_name">Claim Intimation</h2>
        @if(session('success'))
        <script>
            window.onload = function() {
                swal("Success!", "{!! session('success') !!}", "success");
            };
        </script>
        @endif


        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <form action="{{ url('/claim/phs/initiate') }}" method="post">
            @csrf
            <input name="claim_type" type="hidden" value="CASHLESS">
            <input type="hidden" name="policy_no" value="{{ $policy_details->policy_number }}">
            <input type="hidden" name="tpa_id" value="{{ $policy_details->tpa_id }}">
            <input name="phs_tpa_id" type="hidden" value="{{ $phs_tpa_id }}">

            <div class="main_content_in_foam">
                <div class="row_inputs">
                    <div class="inputs-div">
                        <select name="dependent_relation" id="slct_relation" onchange="show_dependents2(this.value)" required>
                            @foreach ($relations as $relation)
                            <option value="{{ $relation }}">
                                {{ $relation }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inputs-div">
                        <select name="dependent_name" id="slct_person2" required>

                        </select>
                    </div>
                </div>
                <div class="row_inputs">

                    <div class="inputs-div">
                        <input type="text" id="fromDate" name="claim_date_of_admission" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date of Admission" required>
                    </div>
                    <div class="inputs-div">
                        <input type="text" name="claim_disease" placeholder="Enter Ailment/Disease" required />
                        <!-- <span style="color: red; font-size: 14px; position: relative; left:10px;top: 5px; display:inline-block;width: 100%;">*Details
                            of your Disease will be confidential and will only be
                            shared
                            with the insurance company.</span> -->
                    </div>

                </div>
                <div class="row_inputs">
                    <div class="inputs-div">
                        <input type="text" id="toDate" name="claim_date_of_discharge" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date of Discharge" required>
                    </div>
                    <div class="inputs-div">
                        <input type="number" name="claim_amt" placeholder="Amount" required>
                    </div>
                </div>
                <div class="row_inputs">
                    <div class="inputs-div">
                        <input type="text" name="claim_name_of_doctor" placeholder="Enter Doctor Name" required>
                    </div>
                    <div class="inputs-div">
                        <input type="text" name="claim_name_of_hospital" placeholder="Enter Hospital Name" required>
                    </div>
                </div>
                <div class="row_inputs">
                    <div class="inputs-div">
                        <input type="number" name="mobile" placeholder="Enter Contact Number" required>
                    </div>

                </div>
                <div class="submit">
                    <button type="submit" class="claim-button">Intimate Claim</button>
                </div>
                <p class="disclaimer"><span style="font-weight: bold; font-family: calibri;">Disclaimer:</span> By submitting this claim intimation form, you acknowledge and agree that the information provided is accurate and complete to the best of your knowledge. Any false or misleading information may result in delays or denial of your claim. Our team will review your submission and contact you for further details if necessary. Please note that submitting this form does not guarantee approval or processing of your claim
                </p>
            </div>
        </form>

    </div>


</div>



@php
$dependentsArray = json_decode($dependents, true);
@endphp

<script>
    let dependents = @json($dependentsArray);
    let relations = @json($relations);

    function show_dependents2(rel) {
        let html = '';
        dependents.forEach(dependent => {
            if (dependent.relation === rel) {
                html += '<option value="' + dependent.dependent + '">' + dependent.dependent + '</option>';
            }
        });
        document.getElementById('slct_person2').innerHTML = html;
    }

    show_dependents2(relations[0]);
</script>


@stop