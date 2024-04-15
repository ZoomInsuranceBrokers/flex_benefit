@extends('layouts.layout')

@section('content')
<style>
    .bg-colour {
        position: relative;
        /* height: 110vh; */
        width: 100vw;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #e5e5e5;
    }

    .top-part {
        position: relative;
        background-color: #ffffff;
        height: auto;
        top: 1vh;
        bottom: 1vh;
        width: 90vw;
        border-radius: 20px;
    }

    .heading {
        text-align: center;
        margin: 6vh 0vh 0vh 0vh;
        font-size: 1.8rem;
        font-weight: 600;
        color: #03313D;
        /* font-family: Calibri, sans-serif; */
    }

    .video-container1 {
        margin-top: 0.5rem;
        text-align: center;
    }

    .footer {
        display: flex;
    }
    .left_side_fotter{
        width: 50%;
    }

    .right_side_fotter{
        width: 50%;
        margin-right: -1px;
    }

    video {
        max-width: 100%;
        border-radius: 10px;
    }

    .para_text {
        padding: 3vh 2vw;
        font-size: 15px;
        text-align: center;
        /* color: #b2b2b2; */
        margin: 0px;
        /* font-family: Calibri, sans-serif; */
    }

    .heading_table {
        width: 100%;
        border-collapse: collapse;
        background-color: #cce5ff;
        border-radius: 10px;
        /* font-family: Calibri, sans-serif; */

    }

    .down-part {
        background-color: #ffffff;
        width: 100vw;
    }

    .heading_table th {
        border: none;
        padding: 10px;
        width: 14.285%;
    }

    .table1 {
        width: 100%;
        margin-top: 2vh;
        border-collapse: separate;
        border-spacing: 1vw 1vh;
        /* font-family: Calibri, sans-serif; */
    }

    .table1 td {
        border: 2px solid #aaaaaa;
        text-align: center;
        padding: 10px;
        width: 14.285%;
    }

    .table1 tr td:last-child {
        background-color: #677B7F;
        color: #ffffff;
    }

    @media screen and (max-width: 768px) {
        .heading_table{
            font-size: 9px;
        }
        .table1 td{
            font-size: 9px;
        }
        .table1 tr td:last-child{
            font-size: 9px;
        }
        
    }
</style>

<div class="bg-colour">
    <div class="top-part">
        <h2 class="heading">Track Claim</h2>
        <p class="para_text">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum nulla nobis mollitia tempora autem hic maiores
            beatae quisquam ducimus, iusto, delectus laboriosam molestias accusamus ullam deserunt nesciunt, rem
            exercitationem doloribus!
        </p>
        <div style="display: flex; justify-content: center;">
            <div style="width: 93%;">
                <table class="heading_table">
                    <tbody>
                        <tr>
                            <th>Sr.No</th>
                            <th>Policy Name</th>
                            <th>Policy No</th>
                            <th>Patient Name</td>
                            <th>Patient Relation</th>
                            <th>Claim Amount</th>
                            <th>Claim Status</th>
                        </tr>
                    </tbody>
                </table>
                <table class="table1">
                    @foreach ($claims as $claim)
                    <tr>
                        @if ($claim['message'] == 'Claim data not found !!')
                        <td colspan="7" class="text-center"><b>{{ 'Claim data not found !!' }}</b></td>
                        @else
                        <td>{{ $loop->iteration }}</td>
                        <td><b>{{ $claim['policy_name'] }}</b></td>
                        <td><b>{{ $claim['policy_number'] }}</b></td>
                        <td><b>{{ $claim['patient_name'] }}</b></td>
                        <td><b>{{ $claim['patient_relation'] }}</b></td>
                        <td><b>{{ !empty($claim['claim_amount']) ? $claim['claim_amount'] : 0 }}</b></td>
                        <td><b>{{ $claim['claim_status'] }}</b></td>
                        @endif
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="video-container1">
            <video autoplay loop muted width="800" height="300">
                <source src="{{ asset('assets/videos/tracking.mp4') }}" type="video/mp4" />
            </video>
        </div>
    </div>


</div>

<div class="down-part">
<div class="footer">
    <div class="left_side_fotter"></div>
    <div class="right_side_fotter">
        <video autoplay loop muted width="800" height="300">
            <source src="{{ asset('assets/videos/trackclaim2.mp4') }}" type="video/mp4" />
        </video>
    </div>
</div>
</div>
<script>
    $(document).ready(function() {
        function setHeight() {
            var topPartHeight = $('.top-part').outerHeight();
            var bgColourHeight = topPartHeight + (0.05 * $(window).height()); // 5vh in pixels
            $('.bg-colour').css('height', bgColourHeight + 'px');
        }
        setHeight();
    })
</script>

@stop