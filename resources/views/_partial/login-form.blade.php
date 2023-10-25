<div id="modal" class="popupContainer" style="display:none;">
    <div class="popupHeader">
        <span class="header_title">Login</span>
        <span class="modal_close"><i class="fa fa-times"></i></span>
    </div>

    <section class="popupBody">
        <!-- Social Login -->
        {{-- <div class="social_login">
            <div class="">
                <a href="#" class="social_box fb">
                    <span class="icon"><i class="fab fa-facebook"></i></span>
                    <span class="icon_title">Connect with Facebook</span>

                </a>

                <a href="#" class="social_box google">
                    <span class="icon"><i class="fab fa-google-plus"></i></span>
                    <span class="icon_title">Connect with Google</span>
                </a>
            </div>

            <div class="centeredText">
                <span>Or use your Email address</span>
            </div>

            <div class="action_btns">
                <div class="one_half"><a href="#" id="login_form" class="btn">Login</a></div>
                <div class="one_half last"><a href="#" id="register_form" class="btn">Sign up</a></div>
            </div>
        </div> --}}

        <!-- Username & Password Login form -->
        <div class="user_login">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="emp_login" method="POST" action="/user/login">
                <label>Email / Employee ID</label>
                <input type="text" id="username"/>
                <span class="text-danger" id="usernameErrorMsg"></span>
                <br />

                <label>Password</label>
                <input type="password" id="password" />                
                <span class="text-danger" id="passwordErrorMsg"></span>
                <br />

                <div class="checkbox">
                    <input id="remember" type="checkbox" name="remember_me" />
                    <label for="remember">Remember me on this computer</label>
                </div>

                <div class="action_btns">
                    <div class="one_half"><a href="#" class="btn back_btn"><i class="fa fa-angle-double-left"></i> Back</a></div>
                    <div class="one_half last">                        
                        <input type="submit" id="loginSubmission" class="btn btn_red" value="Login" />
                    </div>
                </div>
            </form>

            <a href="#" class="forgot_password">Forgot password?</a>
        </div>

        <!-- Register Form -->
        {{-- <div class="user_register">
            <form>
                <label>Full Name</label>
                <input type="text" />
                <br />

                <label>Email Address</label>
                <input type="email" />
                <br />

                <label>Password</label>
                <input type="password" />
                <br />

                <div class="checkbox">
                    <input id="send_updates" type="checkbox" />
                    <label for="send_updates">Send me occasional email updates</label>
                </div>

                <div class="action_btns">
                    <div class="one_half"><a href="#" class="btn back_btn"><i class="fa fa-angle-double-left"></i> Back</a></div>
                    <div class="one_half last"><a href="#" class="btn btn_red">Register</a></div>
                </div>
            </form>
        </div> --}}
    </section>
</div>
@section('script')
<script>
$(document).ready(function(){
    $('#emp_login').on('submit', function(e){
        e.preventDefault();
        $('[id$=ErrorMsg]').text('');
        let username = $('#username').val();
        let password = $('#password').val();
        let rememberMe = $('#remember').val();

        $.ajax({
            url: "/user/login",
            type:"POST",
            data:{
            "_token": '{{ csrf_token() }}',
            username:username,
            password:password,
            rememberMe:rememberMe
            },
            success:function(response) {
                //console.log(response);
                window.location.href = '{{ env('APP_URL') }}';
            },
            error: function(response) {
                //console.log('error:',JSON.parse(response.responseText));
                $.each(JSON.parse(response.responseText).errors, function(k,v){
                    //console.log(k,v);
                    $('#' + k + 'ErrorMsg').html(v);
                });
               
                //$('#messageErrorMsg').text(response.responseJSON.errors.message);
            },
        });
    });
});
</script>
@endsection