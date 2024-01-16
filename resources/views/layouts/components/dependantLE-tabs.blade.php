@section('link_rel')
<link href="{{ asset('assets/css/jtable/themes/metro/blue/jtable.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('inline-page-style')
<style>.ui-dialog{z-index:2 !important;}</style>
@stop

<section class="tab-wrapper">
   <div class="tab-content">
      <!-- Tab links -->
      <div class="tabs">
         <button class="tablinks" data-country="Update Life Event">
            <p data-title="Update Life Event">Update Life Event</p>
        </button>
      </div>

      <!-- Tab content -->
      <div class="wrapper_tabcontent">
         <div id="existing-dependant" class="tabcontent active">
            <h3>Existing</h3>
            <div id="dependentLE_list"></div>
         </div>
{{-- 
         <div id="add-new-dependant" class="tabcontent">
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
    $('#header_dependantsLE').addClass('active');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    $('#dependentLE_list').jtable({
        title: 'Life Event dependants',
        toolbar:{
            show:true
        },
        dialogShowEffect:'scale',
        actions: {
            listAction: '/dependants/list',
            createAction: '/dependants/saveLifeEvent',
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
                title: 'Dependant Name',
                width: 'auto'
            },
            relationship_type: {
                title: 'Relation Type',
                width: 'auto',
                edit: false,
                options: [@php echo $relationLE_Table; @endphp]
            },
            gender: {
                title: 'Gender',
                dependsOn:'relationship_type'
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