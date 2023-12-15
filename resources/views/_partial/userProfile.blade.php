@php //dd(Auth::user());
$formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY); 
//dd(Auth::user()); 
@endphp

<div class="modal" id="userProfile" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <section >
      <div class="container">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-md-12">
            <div>
              <div class="card-body text-center">
                <a type="button" style="float:right;"class="modal_close close"  data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </a>
                <div class="mt-3 mb-4">
                  <img src="@php echo asset('assets/images/' . strtolower(config('constant.gender')[Auth::user()->gender]) . '.png');
                  @endphp"
                    class="rounded-circle img-fluid" style="width: 100px;" />
                </div>
                <h4>
                  {{ (!is_null(Auth::user()->title) ? config('constant.title')[Auth::user()->title] : '') . ' ' . implode(' ',[Auth::user()->fname,Auth::user()->mname, Auth::user()->lname])}}
                </h4>
                <span class="mb-2">({{ Auth::user()->employee_id }})</span>
                <div class="mb-4">  
                  <p class="text-muted">{{ Auth::user()->suffix }}</p>
                  {{-- <span class="mx-2">|</span> --}}
                  <p><a href="#!">{{ Auth::user()->email }}</a> </p>
                  {{-- <span class="mx-2">|</span> --}}
                  <p> <a href="#!">{{ Auth::user()->mobile_number }}</a> </p>
                </div>
                
                <div>
                  <button class="btn btn-primary" onclick="window.location.href='/password-reset/'">Reset Password</button>
                </div>
                {{-- <div class="mb-4 pb-2">
                  <button type="button" class="btn btn-outline-primary btn-floating">
                    <i class="fab fa-facebook-f fa-lg"></i>
                  </button>
                  <button type="button" class="btn btn-outline-primary btn-floating">
                    <i class="fab fa-twitter fa-lg"></i>
                  </button>
                  <button type="button" class="btn btn-outline-primary btn-floating">
                    <i class="fab fa-skype fa-lg"></i>
                  </button>
                </div> --}}
                {{-- <button type="button" class="btn btn-primary btn-rounded btn-lg">
                  Message now
                </button> --}}
                {{-- <div class="d-flex justify-content-between text-center mt-5 mb-2">
                  <div>
                    <h4 class="mb-2">{{ Auth::user()->points_used + Auth::user()->points_available }}</h4>
                    <p class="text-muted mb-0">Total Points</p>
                  </div>
                  <div class="px-3">
                    <h4 class="mb-2">{{ Auth::user()->points_used }}</h4>
                    <p class="text-muted mb-0">Points Used</p>
                  </div>
                  <div>
                    <h4 class="mb-2">{{ Auth::user()->points_available }}</h4>
                    <p class="text-muted mb-0">Points Available</p>
                  </div>
                </div>
                <div class="d-flex justify-content-between text-center mt-5 mb-2">
                  <div>
                    <h4 class="mb-2 h5">{{ $formatter->formatCurrency(Auth::user()->salary, 'INR') }}</h4>
                    <p class="text-muted mb-0">Salary</p>
                  </div>
                  <div class="px-3">
                    <h4 class="mb-2 h5">{{ date('d-M-Y', strtotime(Auth::user()->hire_date)) }}</h4>
                    <p class="text-muted mb-0">Joining Date</p>
                  </div>
                </div> --}}
                
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
      {{-- <div class="modal-header">
        <h5 class="modal-title">Hi, {{ implode(' ', [Auth::user()->fname, Auth::user()->mname, Auth::user()->lname]) }}</h5>
        <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <a class="btn-danger btn" href="/logout">Yes, I'm Going</a>
        <button class="btn-success btn modal_close" data-dismiss="modal">No, I will Stay!</button>
      </div> --}}
    </div>
  </div>
</div>