@extends('layouts.layout')

@section('content')


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('error'))
<script>
    // Display Swal dialog with the error message
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
       
    });
</script>
@endif
@if(!session('is_submitted'))

<script>
    // Display Swal dialog with the error message
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Currently you have a open enrollment portal you can add or edit your dependents there!',
    });
</script>
@endif
@if(session('success'))
<script>
    // Display Swal dialog with the success message
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
    });
</script>
@endif
<section class="banner__section life-event__banner">
    <div class="container-fluid">
        <div class="row h-100">
            <div class="col-xl-6 ms-auto right-col">
                <div class="main-banner">
                    <h1>
                        <span class="color-black">keep Your</span>
                    </h1>
                    <h1>Life Event Updated</h1>
                    <p>
                        The system has already uploaded your details based on the
                        information available from the company's records. If you need to
                        make any changes to the dependant information, please use the
                        editing function located in the tab
                    </p>
                </div>
            </div>
            <div class="col-xl-6 left-col"></div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<!--about dependent start-->
<section>
    <div class="container py-3 py-md-5 my-0 my-xl-5">
        <div class="row">
            <!--after record added table-->
            <div class="col-12 table-responsive{{ count($dependants) > 0 ? '' : ' d-none' }}">
                <table id="dependanttRecordsTbl" class="table table-striped nowrap table-bordered table--custom" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Dependant Name</th>
                            <th>Relation Type</th>
                            <th id="currSelectionHeadCol1006">Gender</th>
                            <th>Date of Birth</th>
                            <th>Nomination Percentage</th>
                            <th>Approval Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        function getRelationName($relationID) {
                        switch ($relationID) {
                        case 2:
                        return "Father";
                        case 3:
                        return "Mother";
                        case 4:
                        return "Brother";
                        case 5:
                        return "Sister";
                        case 6:
                        return "Spouse";
                        case 7:
                        return "Father-in-Law";
                        case 8:
                        return "Mother-in-Law";
                        case 9:
                        return "Son";
                        case 10:
                        return "Daughter";
                        case 11:
                        return "Live-In Partner";
                        case 12:
                        return "Others";
                        default:
                        return "";
                        }
                        }
                        @endphp
                        @foreach ($dependants as $dependant)
                        <tr>
                            <td>{{ $dependant['dependent_name'] }}</td>
                            <td>
                                @php
                                $relationName = getRelationName($dependant['relationship_type']);
                                @endphp

                                <script>
                                    document.write("{{ $relationName }}");
                                </script>
                            </td>


                            <td>
                                @php
                                switch ($dependant['gender']) {
                                case 1:
                                $gender = 'Male';
                                break;
                                case 2:
                                $gender = 'Female';
                                break;
                                case 3:
                                $gender = 'Others';
                                break;
                                default:
                                $gender = '';
                                }
                                @endphp
                                {{ $gender }}
                            </td>
                            <td>{{ $dependant['dob'] }}</td>
                            <td>{{ $dependant['nominee_percentage'] }}</td>
                            <td> @php
                                switch ($dependant['approval_status']) {
                                case 1:
                                $approval_status = 'Approved';
                                break;
                                case 2:
                                $approval_status = 'Rejected';
                                break;
                                case 3:
                                $approval_status = 'In-progress';
                                break;
                                default:
                                $approval_status = '';
                                }
                                @endphp
                                {{ $approval_status }}
                            </td>
                           
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo" class="btn seconday-btn mx-auto d-block">
                    add dependant
                </button>
            </div>
            @if(count($dependants) == 0 )
            <div class="col-lg-8 col-md-9 card-dependent card-dependent__event mx-auto">
                <div>
                    <h2>Click on the button to add Dependants.</h2>
                    @if(session('is_submitted'))
                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo" class="btn seconday-btn">
                        add dependant
                    </button>
                    @else
                    <button type="button" class="btn seconday-btn" onclick="showbox()">
                        add dependant
                    </button>
                    @endif
                </div>
                <img src="{{asset('assets/images/about-img.png') }}" alt="dependent image" />
            </div>
            @endif
        </div>
    </div>
</section>
<!--about dependent end-->
<!-- Work Section Start -->
<section class="life-event-about">
    <div class="row">
        <div class="col-lg-6 m-0 p-0 hire-bg">
            <div class="hire">
                <div>
                    <h2 class="text-center">How It Works ?</h2>
                    <ul class="custom-icon text-white">
                        <li>
                            Health insurance for self, spouse, children, and parents falls
                            under section 80D.
                        </li>
                        <li>
                            To add newly married spouse or a new born baby after the
                            closure of enrollment window.
                        </li>
                        <li>
                            To add newly married spouse or a new born baby after the
                            closure of enrollment window.
                        </li>
                        <li>
                            To add newly married spouse or a new born baby after the
                            closure of enrollment window.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6 m-0 p-0 work-bg">
            <div class="work p-5">
                <div>
                    <h2 class="text-center">Who can be added ?</h2>
                    <ul class="custom-icon text-white">
                        <li>
                            Health insurance for self, spouse, children, and parents falls
                            under section 80D.
                        </li>
                        <li>
                            To add newly married spouse or a new born baby after the
                            closure of enrollment window.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<!--add dependent model popup start-->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Dependants</h5>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <form id="dependentForm" action="/dependants/saveLifeEvent" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="dependant-name" class="col-form-label">Dependant Name</label>
                            <input type="text" name="dependent_name" class="form-control" id="dependant-name" />
                            <div id="dependent-errorMessage" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="relation-type" class="col-form-label">Relation Type</label>
                            <select class="form-select" name="relationship_type" aria-label="Default select example" id="relation-type">
                                <option value="">----select---</option>
                            </select>
                            <div id="relation-errorMessage" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="Gender-type" class="col-form-label">Gender</label>
                            <select class="form-select" name="gender" aria-label="Default select example" id="gender-type">
                                <option value="">----select---</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                                <option value="3">Others</option>
                            </select>
                            <div id="gender-errorMessage" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="dob-name" class="col-form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control "max="<?php echo date('Y-m-d'); ?>" id="dob-name" />
                            <div id="dob-errorMessage" class="text-danger"></div>
                        </div>
                        <div class="mb-3" style="display: none;" id="doe-box">
                            <label for="doe-name" class="col-form-label">Date of Event</label>
                            <input type="date" name="doe" class="form-control" id="doe" max="<?php echo date('Y-m-d'); ?>"  value="<?php echo date('Y-m-d'); ?>"/>
                            <div id="doe-errorMessage" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="percentage-name" class="col-form-label">Nomination Percentage</label>
                            <input type="number" name="nominee_percentage" class="form-control" id="percentage-name" placeholder="Max 100" max="100" />
                        </div>
                        <div id="nomine-errorMessage" class="text-danger"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer mx-3">
                <button type="button" class="btn primary--btn" data-dismiss="modal" id="cancelButton">Cancel</button>
                <button type="button" class="btn primary--btn" id="saveDependantBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    // For  Data Table
    document.getElementById('cancelButton').addEventListener('click', function() {
        $('#exampleModal').modal('hide');
    });

    let currentDate = new Date().toISOString().split('T')[0];

    document.getElementById('dob-name').setAttribute('max', currentDate);

    $(document).ready(function() {
        loadRelations();

        $('#saveDependantBtn').click(function(event) {
            event.preventDefault();

            if (validateForm()) {
                document.getElementById("dependentForm").submit();
            }
        });

    });
    document.getElementById("relation-type").addEventListener("change", function() {
        var selectElement = document.getElementById("relation-type");
        var selectedValue = selectElement.value;
        var divToShow = document.getElementById("doe-box");
        console.log(selectedValue);
            if (selectedValue === "6") {
                divToShow.style.display = "block";
            } else {
                divToShow.style.display = "none";
            }
        });


    $(document).ready(function() {
        new DataTable('#eventRecordsTbl', {
            responsive: true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
            "lengthChange": false
        });
    });

    function showbox(){
        Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Currently you have a open enrollment portal you can add or edit your dependents there!',
    });
    }

    function loadRelations() {
        $.ajax({
            url: '/dependants/getRelations?isLEList=1',
            type: 'GET',
            success: function(response) {
                console.log("Response:", response);
                let relations = response.split(',');
                console.log("Split relations:", relations);
                let select = $('#relation-type');
                $.each(relations, function(index, value) {
                    if (value !== "-1") {
                        let relationName = getRelationName(value);
                        console.log("Relation ID:", value, "Relation Name:", relationName);
                        select.append('<option value="' + value + '">' + relationName + '</option>');
                    }
                });
            },
            error: function(xhr, status, error) {}
        });
    }


    function getRelationName(relationID) {
        console.log("relationID", relationID)
        switch (relationID) {
            case "2":
                return "Father";
            case "3":
                return "Mother";
            case "4":
                return "Brother";
            case "5":
                return "Sister";
            case "6":
                return "Spouse";
            case "7":
                return "Father-in-Law";
            case "8":
                return "Mother-in-Law";
            case "9":
                return "Son";
            case "10":
                return "Daughter";
            case "11":
                return "Live-In Partner";
            case "12":
                return "Others";
            default:
                return "";
        }
    }


    function validateForm() {
        // Clear all error messages
        $('#dependent-errorMessage').text('');
        $('#relation-errorMessage').text('');
        $('#gender-errorMessage').text('');
        $('#dob-errorMessage').text('');
        $('#nomine-errorMessage').text('');

        let isValid = true;

        if ($('#dependant-name').val().trim() === '') {
            $('#dependent-errorMessage').text('Dependant Name is required.');
            isValid = false;
        }

        if ($('#relation-type').val() === '') {
            $('#relation-errorMessage').text('Relation Type is required.');
            isValid = false;
        }

        if ($('#gender-type').val() === '') {
            $('#gender-errorMessage').text('Gender is required.');
            isValid = false;
        }

        if ($('#dob-name').val().trim() === '') {
            $('#dob-errorMessage').text('Date of Birth is required.');
            isValid = false;
        }

        if ($('#percentage-name').val().trim() === '') {
            $('#nomine-errorMessage').text('Nomination Percentage is required.');
            isValid = false;
        } else if ($('#percentage-name').val().trim() > 100) {
            $('#nomine-errorMessage').text('Nomination Percentage Max 100.');
            isValid = false;
        } else {
            isValid = checkNominationAllocation($('#percentage-name'));
        }

        return isValid;
    }

    function checkNominationAllocation(field) {
        let isValid = true;
        let fieldValue = $.trim(field.val());

        $.ajax({
            url: '/dependants/nominationCount',
            type: 'GET',
            data: {
                nomAlloc: fieldValue,
                editId: $('#Edit-id').val()
            },
            async: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(result) {
                let response = JSON.parse(result);
                if (!response.status) {
                    // Show error message in Swal box
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Nomination exceeding 100%. Existing Allocation: ' + response.msg
                    });
                    isValid = false;
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors here
                console.error(xhr.responseText);
            }
        });

        return isValid;
    }
</script>

@stop