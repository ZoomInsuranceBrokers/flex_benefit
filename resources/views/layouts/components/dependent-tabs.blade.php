@section('link_rel')
<link href="assets/css/jtable/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css" />
@stop

@section('inline-page-style')
<style>.ui-dialog{z-index:2 !important;}</style>
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
                options: [@php echo config('constant.relationship_type_jTable') @endphp]
            },
            gender: {
                title: 'Gender',
                width: 'auto',
                edit: false,
                options: [@php echo config('constant.gender_jTable') @endphp]
            },
            dob: {
                title: 'Date of Birth',
                width: 'auto',
                type: 'date',
            },
            nominee_percentage: {
                title: 'Nomination Percentage',
                width: 'auto'
            },
            is_deceased: {
                title: 'Deceased',
                width: 'auto',
                options: [@php echo config('constant.boolean_jTable') @endphp],
                create: false,
                edit: false,
            },
            approval_status: {
                title: 'Approval Status',
                width: 'auto',
                options: [@php echo config('constant.approval_status_jTable') @endphp],
                create: false,
                edit: false,
                list: true
            }
        }
    });
    	
    $('#dependent_list').jtable('load');

@endsection