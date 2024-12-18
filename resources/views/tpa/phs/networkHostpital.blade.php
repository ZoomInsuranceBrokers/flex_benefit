@extends('layouts.layout')

@section('content')

<style>
    /* * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Calibri, sans-serif;
    } */

    .bg-colour {
        position: relative;
        height: calc(105vh + 5vh);
        height: 92vh;
        width: 100vw;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #e5e5e5;
    }

    .top-part {
        position: absolute;

        top: 1vh;
        background-color: #ffffff;
        /* height: auto; */
        width: 90vw;
        border-radius: 20px;
        margin: 10px 0px 10px 0px;
    }

    .heading {
        text-align: center;
        margin: 6vh 0vh 0vh 0vh;
        font-size: 28px;
        font-weight: 600;
        color: #03313D;
    }

    .input-div {
        text-align: center;
    }

    .input-div input {
        text-align: center;
        width: 15%;
        height: 5vh;
        border: 2px solid #d3d3d3;
        transition: border-color 0.3s;
    }

    .input-div input:focus {
        border: 2px solid #d3d3d3;
    }

    .search_button {
        display: inline-block;
        text-decoration: none;
        border: none;
        color: #ffffff;
        background-color: #03313e;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        margin-left: 1rem;
        transition: background-color 0.3s;

    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .video-container1 {
        margin-top: 0.5rem;
        text-align: center;
    }

    video {
        max-width: 100%;
        border-radius: 10px;
    }

    .down-part {
        background-color: #ffffff;
        width: 100vw;
    }

    .footer_part {
        width: 100vw;
        display: flex;
        justify-content: center;
        margin-top: 5vh;
    }

    .fotter_image {
        width: 90vw;
        background-image: url("{{ asset('assets/images/banner1.png') }}");
        aspect-ratio: 16/1.6;
        /* background-position: center; */
        background-size: cover;
    }

    .para_text {
        padding: 3vh 2vw;
        font-size: 15px;
        text-align: center;
        /* color: #b2b2b2; */
    }

    .para_footer_text {
        width: 100%;
        margin: 1vh 0 1vh 0;
        /* color: #b2b2b2; */
        text-align: start;
        font-size: 15px;
    }

    .hospital_list_container {
        display: flex;
        margin-top: 3vh;
    }

    .left-container {
        width: 60%;
    }

    .right-container {
        width: 50%;
        text-align: center;
    }



    .hospital_list {
        width: 100%;
        padding: 1vh 1vw;
        margin-top: 1vh;
        position: relative;
        left: 1vw;
        border-radius: 10px;
        margin-bottom: 2vh;
        text-align: start;
        height: 60vh;
        overflow-y: scroll;
    }

    .each_hospital {
        padding: 1vh 0vw 1vh 1vw;
        border-radius: 15px;
        margin-bottom: 2vh;
    }

    .each_hospital:hover {
        border: 1px solid #0fa2d5;
    }

    .hospital_adress {
        margin: 0px;
        width: 87%;
    }


    .icons_div {
        text-align: center;
        display: inline;

    }

    .hospital_name {
        margin-bottom: 0px;
        font-size: 1.1rem;
        font-weight: 510;
        display: inline-block;
        width: 87%;
    }

    .icons {
        width: 2vw;
        margin-left: 10px;
    }



    @media screen and (max-width: 768px) {
        .hospital_list_container {
            flex-direction: column;
        }

        .left-container {
            width: 100%;
        }

        .right-container {
            width: 100%;
        }

        .bg-colour {
            height: calc(105vh + 117vh);
        }

        .input-div input {
            width: 42%;
        }

        .search_button {
            padding: 0.4rem 0.4rem;
            margin-left: 0.2rem;
        }

        .each_hospital {
            width: 90%;
        }

        .pagination {
            position: relative;
            right: 5vw;
        }

        .pagination button {
            margin: 0 0.2rem;
        }

        .heading {
            margin: 1vh 0vh 1vh 0vh;
        }

        .icons {
            width: 10vw;
        }

        .para_text {
            text-align: left;
        }

    }
</style>

<div class="bg-colour">
    <div class="top-part">
        <h2 class="heading">Locate Network Hospitals Effortlessly</h2>
        <p class="para_text">
            Experience the ease of finding healthcare facilities covered under
            your policy. With just a few clicks, uncover the list network
            hospitals ready to provide quality care whenever you need it. Your
            health matters, and so does your convenience.
        </p>

        <div class="input-div">
            <input type="number" id="pincode" placeholder="Enter Pincode" />
            <input type="hidden" name="policy_no" value="{{ $policy_details->policy_number }}">
            <button id="search" class="search_button">Search Hospital</button>
        </div>
        <!-- this is second page when user click search hospital then this div is showing -->
        <div class="hospital_list_container" style="display:none">
            <div class="left-container">
                <!-- <div class="search-container">
                    <input type="text" id="searchInput" onkeyup="searchHospital()" placeholder="Search for hospital names...">
                </div> -->
                <div class="hospital_list">
                </div>
               
            </div>

            <div class="right-container">
                <video autoplay loop muted id="myVideo1" width="600" height="400">
                    <source src="{{ asset('assets/videos/Map.mp4') }}" type="video/mp4" />
                </video>
            </div>
        </div>

        <!-- this is first div when user load this page (before ajex call)-->

        <div class="video-container1">
            <video autoplay loop muted id="myVideo" width="800" height="300">
                <source src="{{ asset('assets/videos/111.mp4') }}" type="video/mp4" />
            </video>
        </div>
    </div>
</div>
<div class="down-part">
    <div class="footer_part">
        <div class="fotter_image"></div>
    </div>
    <h2 class="heading">What is a Cashless or Network Hospital?</h2>

    <div class="footer_part" style="margin-top: 1vh;">
        <div style="width: 85%">
            <p class="para_footer_text">
                A cashless or network hospital is a hospital that is part of your
                health insurer's network. This simply means that if you claim at
                these hospitals for your treatment, you can opt for a cashless
                claim, ie, go ahead with your treatment without paying any cash up
                front.
            </p>
            <p class="para_footer_text">
                When you get treated at a network hospital and opt for cashless
                claims, the bills will be directly taken care of between the network
                hospital and your health insurer
            </p>
            
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var jsonResponse; // Define jsonResponse variable in a broader scope

        $('#search').click(function(e) {
            e.preventDefault(); // Prevent the default form submission
            showLoader();

            var pincode = $('#pincode').val(); // Get the pincode from the input field
            var policy_no = $('input[name="policy_no"]').val(); // Get the policy number from the input field

            $.ajax({
                type: 'POST',
                url: '/claim/phsHospitalList',
                data: {
                    _token: '{{ csrf_token() }}',
                    pincode: pincode,
                    policy_no: policy_no
                },
                success: function(response) {
                    hideLoader();
                    jsonResponse = JSON.parse(response); // Assign response to jsonResponse

                    // Log the parsed response for debugging
                    console.log("Parsed Response:", jsonResponse);

                    // Check if the Records property exists in the parsed response
                    if (jsonResponse && jsonResponse.Records) {
                        renderHospitalList(jsonResponse.Records); // Display only first two records
                        // renderPagination(jsonResponse.Records.length); // Render pagination
                        $('.hospital_list_container').show();
                        $('.video-container1').hide();
                        setHeight();
                    } else {
                        console.error("Records property not found in the response.");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error(xhr.responseText);
                }
            });
        });

        // Function to render hospital list
        function renderHospitalList(records) {
            var hospitalListHTML = '';

            $.each(records, function(index, record) {
                hospitalListHTML += '<div class="each_hospital">';
                hospitalListHTML += '<div style="display: flex;justify-content: space-between;">';
                hospitalListHTML += '<h5 class="hospital_name">' + record.HOSPITAL_NAME + '</h5>';
                hospitalListHTML += '<div class="icons_div">';
                hospitalListHTML += '<a href="tel:' + record.PHONE_NO + '"><img class="icons" src="{{asset('assets/images/call.png')}}" alt="loading...."></a>';
                hospitalListHTML += '<a href="https://www.google.co.in/maps/place/' + record.LATITUDE + ',' + record.LONGITUDE + '"><img class="icons" src="{{asset('assets/images/dir.png')}}" alt="loading...."></a>';
                hospitalListHTML += '</div>';
                hospitalListHTML += '</div>';
                hospitalListHTML += '<p class="hospital_adress">' + record.ADDRESS1 + ', ' + record.ADDRESS2 + ', ' + record.ADDRESS_AREA + ', ' + record.CITY_NAME + ', ' + record.PIN_CODE + '</p>';

                hospitalListHTML += '</div>';
            });

            $('.hospital_list').html(hospitalListHTML);
        }

        // Function to render pagination
        // function renderPagination(totalRecords) {
        //     var totalPages = Math.ceil(totalRecords / 2); 
        //     var paginationHTML = '';

        //     if (totalPages > 1) {
        //         paginationHTML += '<div class="pagination">';
        //         paginationHTML += '<button id="prev">Prev</button>';
        //         for (var i = 1; i <= totalPages; i++) {
        //             paginationHTML += '<button class="page">' + i + '</button>';
        //         }
        //         paginationHTML += '<button id="next">Next</button>';
        //         paginationHTML += '</div>';
        //     }

        //     $('.pagination_container').html(paginationHTML);
        // }

        // Event listener for pagination buttons
        // $(document).on('click', '.page', function() {
        //     var page = parseInt($(this).text());
        //     var start = (page - 1) * 2; // Assuming 2 records per page
        //     var end = start + 2; // Assuming 2 records per page
        //     var records = jsonResponse.Records.slice(start, end);
        //     renderHospitalList(records);
        // });

        function setHeight() {
            var topPartHeight = $('.top-part').outerHeight();
            var bgColourHeight = topPartHeight + (0.08 * $(window).height());
            $('.bg-colour').css('height', bgColourHeight + 'px');
        }
        setHeight();



        // function set_hight_leftcontainer(){
        //     var topPartHeight = $('.myVideo1').outerHeight();
        //     var bgColourHeight = topPartHeight + (0.08 * $(window).height()); 
        //     $('.hospital_list').css('height', bgColourHeight + 'px');
        // }
        // set_hight_leftcontainer();



    });
</script>

@stop