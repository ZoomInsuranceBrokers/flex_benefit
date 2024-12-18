@php //dd(Auth::user());
$formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
//dd(Auth::user());
@endphp

<div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <center><h5 class="modal-title" id="userProfileModalLabel">User Profile</h5></center>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Content for the user profile -->
        <section>
          <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
              <div class="col-md-12">
                <div>
                  <div class="card-body text-center">
                    <!-- <a type="button" style="float:right;" class="modal_close close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </a> -->
                    <div class="mt-3 mb-4">
                      <img src="{{ asset('assets/images/' . strtolower(config('constant.gender')[Auth::user()->gender]) . '.png') }}" class="rounded-circle img-fluid" style="width: 100px;" />
                    </div>
                    <h4>
                      {{ (!is_null(Auth::user()->title) ? config('constant.title')[Auth::user()->title] : '') . ' ' . implode(' ',[Auth::user()->fname,Auth::user()->mname, Auth::user()->lname])}}
                    </h4>
                    <span class="mb-2">({{ Auth::user()->employee_id }})</span>
                    <div class="mb-4">
                      <p class="text-muted">{{ Auth::user()->suffix }}</p>
                      <p><a href="#">{{ Auth::user()->email }}</a></p>
                      <p><a href="#">{{ Auth::user()->mobile_number }}</a></p>
                    </div>
                    <div>
                      <button class="btn btn-primary" onclick="window.location.href='/password-reset/'">Reset Password</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</div>