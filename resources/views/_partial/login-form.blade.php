<style>
.btn{background: #4b8ef1;}
.btn_red{background: #4b8ef1;}
</style>

<div id="modal" class="popupContainer" style="display:none;">
    <div class="popupHeader">
        <span class="header_title">Login</span>
        <span class="modal_close"><i class="fa fa-times"></i></span>
    </div>

    <section class="popupBody">
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
                    <div class="one_half">
                        <a href="#" class="btn back_btn">
                        <i class="fa fa-angle-double-left"></i> Back</a>
                    </div>

                    <div class="one_half last"> 
                        <i class="fa fa-angle-double-right"></i> 
                        <input type="submit" class="btn btn_red" value="Login" />
                    </div>
                </div>
            </form>

            <a href="#" class="forgot_password">Forgot password?</a>
        </div>
    </section>
</div>
{{-- @section('script') --}}
@section('document_ready')
{{-- <script> --}}
//$(document).ready(function(){
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
                window.location.href = '{{ env('APP_URL') }}';
            },
            error: function(response) {
                $.each(JSON.parse(response.responseText).errors, function(k,v){
                    $('#' + k + 'ErrorMsg').html(v);
                });
               
                //$('#messageErrorMsg').text(response.responseJSON.errors.message);
            },
        });
    });
//});
{{-- </script> --}}
@endsection