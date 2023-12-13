@section('link_rel')
<link href="assets/css/jtable/themes/metro/blue/jtable.css" rel="stylesheet" type="text/css" />
<link href="assets/css/jquery/validationEngine.jquery.min.css" rel="stylesheet" type="text/css" />
@stop

@section('inline-page-style')
<style>
.ui-widget-overlay{opacity:0.4}
</style>
@stop

<section class="tab-wrapper">
   <div class="tab-content">
      <!-- Tab links -->
      <div class="tabs">
         <button class="tablinks" data-country="existing-dependent"><p data-title="Existing Dependent">Existing Dependents</p></button>
         {{-- <button class="tablinks active" data-country="add-new-dependent"><p data-title="Add New Dependent">Add New Dependent</p></button> --}}
      </div>

      <!-- Tab content -->
      <div class="wrapper_tabcontent">
         <div id="existing-dependent" class="tabcontent active">
            <h3>Existing</h3>
            <div id="dependent_list"></div>
         </div>
{{-- 
         <div id="add-new-dependent" class="tabcontent">
            <h3>New</h3>
            <p>Paris is in the Paris department of the Paris-Isle-of-France region The French historic, political and economic capital, with a population of only 2.5 million is located in the northern part of France. One of the most beautiful cities in the world. Home to historical monuments such as Notre Dame, the Eiffel tower (320m), Bastille, Louvre and many more. </p>
         </div> --}}
      </div>
   </div>
   <a id="depError_trigger" style="display:none;" href="#depErrorModal">Logout</a>

    <!-- Modal -->
    <div class="modal" id="depErrorModal" style="display:none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="depErrorModalTitle">Nomination Limit Exceeds</h5>
                <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"><p id="depErrorModalBody" class="text-danger"></p>              
            </div>
            <div class="modal-footer">
                <button class="btn-success btn modal_close" data-dismiss="modal">Change Nomination Percentage</button>
            </div>
            </div>
        </div>
        </div>
</section>

@section('script')
<script>
// tabs
var tabLinks = document.querySelectorAll(".tablinks");
var tabContent = document.querySelectorAll(".tabcontent");


tabLinks.forEach(function(el) {
   el.addEventListener("click", openTabs);
});


function openTabs(el) {
   var btnTarget = el.currentTarget;
   var country = btnTarget.dataset.country;

   tabContent.forEach(function(el) {
      el.classList.remove("active");
   });

   tabLinks.forEach(function(el) {
      el.classList.remove("active");
   });

   document.querySelector("#" + country).classList.add("active");
   
   btnTarget.classList.add("active");
}

function checkNominationAllocation(field, rules, i, options){
    returnVal = false;
    hideAdd = hideEdit = false;
    $('#AddRecordDialogSaveButton,#EditDialogSaveButton').show();   
    if ($.trim($(field).val()) != '') {
        $.ajax({
            url:'/dependents/nominationCount?nomAlloc=' + $(field).val()+'&editId=' + $('#Edit-id').val(),
            type:'GET',
            async:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success:function(result) {
                response = JSON.parse(result);
                if (!response.status) {
                    {{-- options.allrules.validate2fields.alertText = 'Nomination execeeding 100%. Existing Allocation: ' 
                    + response.msg; --}}
                    {{-- $(field).validationEngine('showPrompt', 'Nomination execeeding 100%. Existing Allocation: ' 
                    + response.msg);  --}}
                    $('#depErrorModalBody').html('Nomination execeeding 100%. Existing Allocation: ' + response.msg);
                    $('#depError_trigger').click();
                    $('#AddRecordDialogSaveButton,#EditDialogSaveButton').hide();
                    {{-- alert('Nomination execeeding 100%. Existing Allocation: ' + response.msg); --}}
                } else {
                    $('#AddRecordDialogSaveButton,#EditDialogSaveButton').show();
                    returnVal =  true;
                }
            }
        });
        return returnVal;
        
    }
    
}
</script>

{{-- JTABLE GRID --}}
@endsection

@section('document_ready')
    $('[id^=header_]').removeClass('active');
    $('#header_dependents').addClass('active');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $('#dependent_list').jtable({
        title: 'Existing Dependents',
        toolbar:{
            show:true
        },
        dialogShowEffect:'scale',
        actions: {
            listAction: '/dependents/list',
            createAction: '/dependents/create',
            updateAction: '/dependents/update',
            deleteAction: '/dependents/delete'
        },
        fields: {
            id: {
                title: 'Id',
                width: 'auto',
                create: false,
                edit: false,
                list: false,
                key:true
            },

            dependent_name: {
                title: 'Dependent Name',
                width: 'auto'
            },
            relationship_type: {
                title: 'Relation Type',
                width: 'auto',
                edit: false,
                options: [@php echo config('constant.relationshipDep_type_jTable') @endphp]
                //options: '/dependents/getRelationshipTypes'
            },
            gender: {
                title: 'Gender',
                dependsOn: 'relationship_type',
                width: 'auto',
                edit: false,
                options: [@php echo config('constant.gender_jTable') @endphp]
                {{-- options: function (data){
                    if (data.source == 'list') {
                        //Return url all options for optimization. 
                        return [@php echo config('constant.gender_jTable') @endphp];
                    }
                    return '/dependents/getGender?rltntype=' + data.dependedValues.relationship_type;
                } --}}
            },
            dob: {
                title: 'Date of Birth',
                width: 'auto',
                type: 'date',
                displayFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                maxDate: "+0D"
            },
            nominee_percentage: {
                title: 'Nomination Percentage',
                width: 'auto',
            },
            approval_status: {
                title: 'Approval Status',
                width: 'auto',
                options: [@php echo config('constant.approval_status_jTable') @endphp],
                create: false,
                edit: false,
                list: true
            }
        },
        //Initialize validation logic when a form is created
        formCreated: function (event, data) {
            data.form.find('input[name="nominee_percentage"]').addClass('validate[required,min[0],max[100],funcCall[checkNominationAllocation]]');
            data.form.find('input[name="dob"]').addClass('validate[required]');
            {{-- data.form.find('input[name="gender"]').addClass('validate[required]');
            data.form.find('input[name="relationship_type"]').addClass('validate[required]'); --}}
            //data.form.find('input[name="dependent_name"]').addClass('validate[required]');
            data.form.validationEngine({promptPosition:"topLeft", focusFirstField : false, autoHidePrompt: true,  autoHideDelay: 4000});
        },
        //Validate form when it is being submitted
        formSubmitting: function (event, data) {
            return data.form.validationEngine('validate');
        },
        //Dispose validation logic when form is closed
        formClosed: function (event, data) {
            data.form.validationEngine('hide');
            data.form.validationEngine('detach');
        }
    });
    	
    $('#dependent_list').jtable('load');

@endsection