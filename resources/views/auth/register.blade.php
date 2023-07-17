@extends('layouts.client')
@section('content-client')
<div class="container_fullwidth content-page">
  <div class="container">
    <div class="col-md-12 container-page">
      <div class="checkout-page">
        <ol class="checkout-steps">
          <li class="steps active">
            <h4 class="step-title text-center" style="font-weight: bold; color: blue; font-size: 24px;">
              Đăng Kí Tài Khoản
            </h4>
            <div class="step-description">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="run-customer">
                    <div id="form-data" hidden data-rules="{{ json_encode($rules) }}" data-messages="{{ json_encode($messages) }}"></div>
                    <form action="{{ route('user.register') }}" method="POST" id="form__js">
                      @csrf
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Họ Và Tên (<strong class="red">
                            *
                          </strong>)</label>
                            <input type="text" class="form-control" value="{{ old('name') }}" id="name" name="name" aria-describedby="emailHelp" placeholder="Nhập họ và tên">
                            @if ($errors->get('name'))
                            <span id="name-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('name')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail1">Email (<strong class="red">
                            *
                          </strong>)</label>
                            <input type="email" class="form-control" value="{{ old('email') }}" id="email" name="email" aria-describedby="emailHelp" placeholder="Nhập email">
                            @if ($errors->get('email'))
                            <span id="email-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('email')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Mật Khẩu (<strong class="red">
                            *
                          </strong>)</label>
                            <input type="password" class="form-control" value="{{ old('password') }}" id="password" name="password" placeholder="Nhập mật khẩu">
                            @if ($errors->get('password'))
                            <span id="password-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('password')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Xác Nhận Mật Khẩu</label>
                            <input type="password" class="form-control" value="{{ old('password_confirmation') }}" id="password_confirm" name="password_confirm" placeholder="Xác nhận mật khẩu">
                            @if ($errors->get('password_confirm'))
                            <span id="password_confirm-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('password_confirm')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail1">Số Điện Thoại (<strong class="red">
                            *
                          </strong>)</label>
                            <input type="text" class="form-control" value="{{ old('phone_number') }}" id="phone_number" name="phone_number" aria-describedby="emailHelp" placeholder="Nhập số điện thoại">
                            @if ($errors->get('phone_number'))
                            <span id="phone_number-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('phone_number')) }}
                            </span>
                            @endif
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Tỉnh, Thành Phố (<strong class="red">
                            *
                          </strong>)</label>
                            <select class="form-control form-select" id="city" name="city">
                              @foreach ($citys as $city)
                              <option value="{{ $city['ProvinceID'] }}" @if ( $city['ProvinceID']==old('city')) selected @endif>{{ $city['NameExtension'][1] }}</option>
                              @endforeach
                            </select>
                            @if ($errors->get('city'))
                            <span id="city-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('city')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail1">Quận, Huyện (<strong class="red">
                            *
                          </strong>)</label>
                            <select class="form-control form-select" id="district" name="district">
                              @foreach ($districts as $district)
                              <option value="{{ $district['DistrictID'] }}" @if ( $district['DistrictID']==old('district')) selected @endif>{{ $district['DistrictName'] }}</option>
                              @endforeach
                            </select>
                            @if ($errors->get('district'))
                            <span id="district-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('district')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail1">Phường Xã (<strong class="red">
                            *
                          </strong>)</label>
                            <select class="form-control form-select" id="ward" name="ward">
                              @foreach ($wards as $ward)
                              <option value="{{ $ward['WardCode'] }}" @if ( $ward['WardCode']==old('ward')) selected @endif>{{ $ward['WardName'] }}</option>
                              @endforeach
                            </select>
                            @if ($errors->get('ward'))
                            <span id="ward-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('ward')) }}
                            </span>
                            @endif
                          </div>
                          <div class="form-group">
                            <label for="exampleInputEmail1">Địa Chỉ Nhà (<strong class="red">
                            *
                          </strong>)</label>
                            <input type="text" class="form-control" value="{{ old('apartment_number') }}" id="apartment_number" name="apartment_number" aria-describedby="emailHelp" placeholder="Nhập địa chỉ nhà">
                            @if ($errors->get('apartment_number'))
                            <span id="apartment_number-error" class="error invalid-feedback" style="display: block; color: red;">
                              {{ implode(", ",$errors->get('apartment_number')) }}
                            </span>
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="text-center">
                        <button>
                          Đăng Kí
                        </button>
                      </div>
                      <div class="content-footer">
                        <span>Bạn đã có tài khoản?</span>
                        <a href="{{ route('user.login') }}">
                          Đăng nhập
                        </a>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
        </ol>
      </div>
    </div>
  </div>
</div>
@vite(['resources/client/js/register.js'])
@endsection