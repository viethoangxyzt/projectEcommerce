@extends('layouts.client')
@section('content-client')
<div class="container_fullwidth content-page">
  <div class="container">
    <div class="col-md-2"></div>
    <div class="col-md-8 container-page ">
      <div class="checkout-page">
        <ol class="checkout-steps">
          <li class="steps active">
            <h4 class="step-title text-center" style="font-weight: bold; color: blue; font-size: 24px;">Đăng Nhập</h4>
            <div class="step-description">
              <div class="row">
                <div class="col-md-12 col-sm-12" style="margin-top: 30px">
                  <div class="run-customer">
                    <form action="{{ route('user.login') }}" method="POST" id="login-form__js">
                      @csrf          
                      <div class="form-row ">
                        <label class="lebel-abs">
                          Email
                          <strong class="red">
                            *
                          </strong>
                        </label>
                        <input type="text" class="input namefild" value="{{ old('email') }}" id="email" name="email">
                        <span id="email-error" class="error invalid-feedback" style="display: block; color: red;">
                          {{ implode(", ",$errors->get('email')) }}
                        </span>
                      </div>
                      <div class="form-row" style="margin-top:40px;">
                        <label class="lebel-abs">
                          Mật khẩu
                          <strong class="red">
                            *
                          </strong>
                        </label>
                        <input type="password" class="input namefild" id="password" name="password">
                        <span id="password-error" class="error invalid-feedback" style="display: block; color: red;">
                          {{ implode(", ",$errors->get('password')) }}
                        </span>
                      </div>
                      <div class="text-center" style="margin-top: 30px">
                        <button style="height: 45px;">Đăng Nhập</button>
                      </div>
                      <div class="content-footer text-right" style="margin-top: 20px;">
                        <a href="{{ route('user.forgot_password_create') }}">Quên mật khẩu</a> |
                        <a href="{{ route('user.register') }}">Đăng kí tài khoản</a>
                      </div>
                    </form>
                    <a href="{{ route('login.google') }}" class="btn btn-danger btn-block mt-3">Login with Google</a>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ol>
      </div>
    </div>
    <div class="col-md-2"></div>
  </div>
</div>
@vite(['resources/common/js/login.js'])
@vite(['resources/common/css/validation.css'])
@endsection