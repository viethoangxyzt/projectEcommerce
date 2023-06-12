@extends('layouts.client')
@section('content-client')
<div class="container_fullwidth">
    <div class="container">
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div>
              <ol class="checkout-steps">
                <li class="steps active">
                  <h4 class="title-steps">
                    Đổi Mật Khẩu
                  </h4>
                  <div class="step-description">
                    <form action="{{ route('profile.change_password') }}" method="post">
                      @csrf
                      <div class="form-group">
                        <label for="exampleInputEmail1">Mật Khẩu Hiện Tại</label>
                        <input type="password" class="form-control" value="{{ old('current_password') }}" id="current_password" name="current_password" aria-describedby="emailHelp" placeholder="Nhập mật khẩu hiện tại">
                        @if ($errors->get('current_password'))
                          <span id="current_password-error" class="error invalid-feedback" style="display: block">
                            {{ implode(", ",$errors->get('current_password')) }}
                          </span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Mật Khẩu Mới</label>
                        <input type="password" class="form-control" value="{{ old('new_password') }}" id="new_password" name="new_password" aria-describedby="emailHelp" placeholder="Nhập mật khẩu mới">
                        @if ($errors->get('new_password'))
                          <span id="new_password-error" class="error invalid-feedback" style="display: block">
                            {{ implode(", ",$errors->get('new_password')) }}
                          </span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Xác Nhận Mật Khẩu Mới</label>
                        <input type="password" class="form-control" value="{{ old('confirm_password') }}" id="confirm_password" name="confirm_password" aria-describedby="emailHelp" placeholder="Xác nhận mật khẩu mới">
                        @if ($errors->get('confirm_password'))
                          <span id="confirm_password-error" class="error invalid-feedback" style="display: block">
                            {{ implode(", ",$errors->get('confirm_password')) }}
                          </span>
                        @endif
                      </div>
                      <div style="padding-top: 5px;" class="text-center">
                        <button>Xác nhận</button>
                      </div>
                    </div>
                    </form>
                    <div class="your-details row">
                  </div>
                </li>
              </ol>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="clearfix">
      </div>
    </div>
  </div>
@vite(['resources/client/css/checkout.css', 'resources/client/js/profile.js'])

@endsection