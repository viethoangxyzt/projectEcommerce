@extends('layouts.client')
@section('content-client')
<div class="container_fullwidth">
    <div class="container">
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <ol class="checkout-steps">
            <li class="steps active">
              <h4 class="title-steps">
                Thông Tin Cá Nhân
              </h4>
              <div class="step-description">
                <div class="your-details row">
                  <form action="{{ route('profile.change_profile') }}" method="post">
                    @csrf
                    <div class="form-group">
                      <label for="exampleInputPassword1">Họ Và Tên</label>
                      <input type="text" class="form-control" value="{{ $fullName }}" id="name" name="name" placeholder="Nhập họ và tên">
                      @if ($errors->get('name'))
                        <span id="name-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('name')) }}
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Email</label>
                      <input type="text" class="form-control" value="{{ $email }}" id="email" name="email" placeholder="Nhập địa chỉ email">
                      @if ($errors->get('email'))
                        <span id="email-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('email')) }}
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Số điện thoại</label>
                      <input type="text" class="form-control" value="{{ $phoneNumber }}" id="phone_number" name="phone_number" placeholder="Nhập số điện thoại">
                      @if ($errors->get('phone_number'))
                        <span id="phone_number-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('phone_number')) }}
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Tỉnh, Thành Phố</label>
                      <select class="form-control form-select" id="city" name="city">
                        @foreach ($citys as $item)
                            <option value="{{ $item['ProvinceID'] }}"
                            @if ( $item['ProvinceID'] == $city)
                                selected
                            @endif
                            >{{ $item['NameExtension'][1] }}</option>
                        @endforeach
                      </select>
                      @if ($errors->get('city'))
                        <span id="city-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('city')) }}
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Quận, Huyện</label>
                      <select class="form-control form-select" id="district" name="district">
                        @foreach ($districts as $item)
                            <option value="{{ $item['DistrictID'] }}"
                            @if ( $item['DistrictID'] == $district)
                                selected
                            @endif
                            >{{ $item['DistrictName'] }}</option>
                        @endforeach
                      </select>
                      @if ($errors->get('district'))
                        <span id="district-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('district')) }}
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Phường Xã</label>
                      <select class="form-control form-select" id="ward" name="ward">
                        @foreach ($wards as $item)
                            <option value="{{ $item['WardCode'] }}"
                            @if ( $item['WardCode'] == $ward)
                              selected
                            @endif
                            >{{ $item['WardName'] }}</option>
                        @endforeach
                      </select>
                      @if ($errors->get('ward'))
                        <span id="ward-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('ward')) }}
                        </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Địa Chỉ Nhà</label>
                      <input type="text" class="form-control" value="{{ $apartment_number}}" id="apartment_number" name="apartment_number" aria-describedby="emailHelp" placeholder="Nhập địa chỉ nhà">
                      @if ($errors->get('apartment_number'))
                        <span id="apartment_number-error" class="error invalid-feedback" style="display: block">
                          {{ implode(", ",$errors->get('apartment_number')) }}
                        </span>
                      @endif
                    </div>
                    <div style="padding-top: 5px;" class="text-center">
                      <button>Xác nhận</button>
                    </div>
                  </form>
                </div>
              </div>
            </li>
          </ol>
        </div>
        <div class="col-md-2"></div>
      </div>
      <div class="clearfix">
      </div>
    </div>
  </div>
@vite(['resources/client/css/checkout.css', 'resources/client/js/profile.js'])

@endsection