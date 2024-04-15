<style>
    .btn {
        background: #4b8ef1;
    }

    .btn_red {
        background: #4b8ef1;
    }

    .popupContainer {
        margin-left: 0% !important;
    }
</style>

<!-- Modal Popup for login -->
<div class="modal fade loginModal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-overlay"></div>
            <div class="modal-body">
                <div class="col-12">
                    <div class="user-cicle">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <form id="emp_login" method="POST" action="/user/login">
                        <center><div id="errorMessage" style="color: red; margin-bottom: 10px;"></div></center>

                        <div class="my-3 custom-form">
                            <input type="text" class="form-control position-relative" id="username" placeholder="User name" />
                            <div class="login-icon"><img src="{{asset('assets/images/username-01.png') }}" alt="password"></div>
                        </div>
                        <div class="custom-form">
                            <input type="password" class="form-control position-relative" id="password" placeholder="Password" />
                            <div class="pass-icon"><img src="{{asset('assets/images/pass-01.png') }}" alt="password"></div>
                            <div class="pass-eye"><img src="{{asset('assets/images/pass-eye.png') }}" alt="password"></div>

                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <div class="mb-3 form-check">
                                <input type="checkbox" id="remember" class="form-check-input" name="remember">
                                <label class="form-check-label login-label" for="loginCheck">Remember me</label>
                            </div>
                            <div class="login-label"><a href="{{ url('/forgot-password') }} ">Forgot Password</a></div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary d-block mx-auto">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#emp_login').on('submit', function(e) {
            console.log("hi");
            e.preventDefault();
            $('[id$=ErrorMsg]').text('');
            let username = $('#username').val();
            let password = $('#password').val();
            let rememberMe = $('#remember').val();

            $.ajax({
                url: "/user/login",
                type: "POST",
                data: {
                    "_token": '{{ csrf_token() }}',
                    username: username,
                    password: password,
                    rememberMe: rememberMe
                },
                success: function(response) {
                    window.location.href = '{{ env('APP_URL') }}';
                },
                error: function(response) {

                    $('#errorMessage').html(JSON.parse(response.responseText).error);

                },
            });

        });

        $('.pass-eye').click(function(){
            var passwordInput = $('#password');
            var passwordFieldType = passwordInput.attr('type');
            if(passwordFieldType === 'password') {
            passwordInput.attr('type', 'text');
            } else {
            passwordInput.attr('type', 'password');
            }
        });
    });

    
</script>