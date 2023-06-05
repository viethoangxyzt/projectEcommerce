<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\TextSystemConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserVerify;
use App\Notifications\VerifyUserRegister;
use App\Repository\Eloquent\AddressRepository;
use App\Repository\Eloquent\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     * @param AddressRepository $addressRepository
     */
    public function __construct(UserRepository $userRepository, AddressRepository $addressRepository)
    {
        $this->userRepository = $userRepository;
        $this->addressRepository = $addressRepository;
    }
    /**
     * Display the register view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $response = Http::withHeaders([
                'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
            $citys = json_decode($response->body(), true);
            $response = Http::withHeaders([
                'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                'province_id' => old('city') ?? $citys['data'][0]['ProvinceID'],
            ]);
            $districts = json_decode($response->body(), true);
            $response = Http::withHeaders([
                'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                'district_id' => old('district') ?? $districts['data'][0]['DistrictID'],
            ]);
            $wards = json_decode($response->body(), true);
    
            $rules = [
                'email' => [
                    'required' => true,
                    'email' => true,
                ],
                'password' => [
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 24,
                    'checklower' => true,
                    'checkupper' => true,
                    'checkdigit' => true,
                    'checkspecialcharacter' => true,
                ],
                'password_confirm'=> [
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 24,
                    'checklower' => true,
                    'checkupper' => true,
                    'checkdigit' => true,
                    'checkspecialcharacter' => true,
                    'equalTo'=> "#password"
                ],
                'name' => [
                    'required' => true,
                    'minlength' => 1,
                    'maxlength' => 30,
                ],
                'apartment_number' => [
                    'required' => true,
                ],
                'city' => [
                    'required' => true,
                ],
                'district' => [
                    'required' => true,
                ],
                'ward' => [
                    'required' => true,
                ],
                'phone_number' => [
                    'required' => true,
                    'minlength' => 10,
                    'maxlength' => 11,
                ],
            ];
    
            // Messages eror rules
            $messages = [
                'name' => [
                    'required' => __('message.required', ['attribute' => 'Họ và tên']),
                    'minlength' => __('message.min', ['min' => 1, 'attribute' => 'Họ và tên']),
                    'maxlength' => __('message.max', ['max' => 30, 'attribute' => 'Họ và tên']),
                ],
                'email' => [
                    'required' => __('message.required', ['attribute' => 'email']),
                    'email' => __('message.email'),
                ],
                'password' => [
                    'required' => __('message.required', ['attribute' => 'mật khẩu']),
                    'minlength' => __('message.min', ['attribute' => 'Mật khẩu', 'min' => 8]),
                    'maxlength' => __('message.max', ['attribute' => 'Mật khẩu', 'max' => 24]),
                    'checklower' => __('message.password.at_least_one_lowercase_letter_is_required'),
                    'checkupper' => __('message.password.at_least_one_uppercase_letter_is_required'),
                    'checkdigit' => __('message.password.at_least_one_digit_is_required'),
                    'checkspecialcharacter' => __('message.password.at_least_special_characte_is_required'),
                ],
                'password_confirm' => [
                    'required' => __('message.required', ['attribute' => 'mật khẩu']),
                    'minlength' => __('message.min', ['attribute' => 'Mật khẩu', 'min' => 8]),
                    'maxlength' => __('message.max', ['attribute' => 'Mật khẩu', 'max' => 24]),
                    'checklower' => __('message.password.at_least_one_lowercase_letter_is_required'),
                    'checkupper' => __('message.password.at_least_one_uppercase_letter_is_required'),
                    'checkdigit' => __('message.password.at_least_one_digit_is_required'),
                    'checkspecialcharacter' => __('message.password.at_least_special_characte_is_required'),
                    'equalTo' => 'Xác nhận mật khẩu không đúng',
                ],
                'phone_number' => [
                    'required' => __('message.required', ['attribute' => 'số điện thoại']),
                    'minlength' => __('message.min', ['attribute' => 'số điện thoại', 'min' => 10]),
                    'maxlength' => __('message.max', ['attribute' => 'số điện thoại', 'max' => 10]),
                ],
                'city' => [
                    'required' =>  __('message.required', ['attribute' => 'tỉnh, thành phố']),
                ],
                'district' =>[
                    'required' =>  __('message.required', ['attribute' => 'quận, huyện']),
                ],
                'ward' => [
                    'required' => __('message.required', ['attribute' => 'phường, xã']),
                ],
                'apartment_number' => [
                    'required' =>  __('message.required', ['attribute' => 'số nhà']),
                ],
            ];
    
            return view('auth.register', [
                'citys' => $citys['data'],
                'districts' => $districts['data'],
                'wards' => $wards['data'],
                'rules' => $rules,
                'messages' => $messages,
            ]);
        } catch (Exception) {
            return redirect()->route('user.login');
        }
        
    }

    public function store(UserRegisterRequest $request)
    {
        try {
            $data = $request->validated();
            // user data request
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone_number' => $data['phone_number'],
                'role_id' => Role::ROLE['user'],
            ];
            
            // address data request
            $addressData = [
                'city' => $data['city'],
                'district' => $data['district'],
                'ward' => $data['ward'],
                'apartment_number' => $data['apartment_number'],
            ];
            
            $token = Str::random(64);
            $time = Config::get('auth.verification.expire.resend', 60);
            DB::beginTransaction();
            $user = $this->userRepository->create($userData);
            UserVerify::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $token,
                    'expires_at' => Carbon::now()->addMinutes($time),
                ]
            );
            $user->notify(new VerifyUserRegister($token));
            $addressData['user_id'] = $user->id;
            $this->addressRepository->updateOrCreate($addressData);
            DB::commit();
            return redirect()->route('user.verification.notice', $user->id);
        } catch (Exception $e) {
            dd(123);
            Log::error($e);
            DB::rollBack();
            return back()->with('error', TextSystemConst::CREATE_FAILED);
        }
    }

    public function verifyEmail(User $user)
    {
        return view('auth.verify-email', [
            'user' => $user,
        ]);
    }

    public function resendEmail(Request $request)
    {
        try {
            $user = $this->userRepository->find($request->id);
            if(! $user) {
                return redirect('user.home');
            }
            if ($user->hasVerifiedEmail()) {
                return redirect()->route('user.home');
            }
            $token = Str::random(64);
            $time = Config::get('auth.verification.expire.resend', 60);
            DB::beginTransaction();
            UserVerify::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $token,
                    'expires_at' => Carbon::now()->addMinutes($time),
                ]
            );
            $user->notify(new VerifyUserRegister($token));
            DB::commit();
            return back()->with('status', 'verification-link-sent');
        } catch (Exception $e) {
            Log::error($e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function success()
    {
        if (session('status')) {
            return view('auth.verify-success')->with('verify_user_success');
        }
        return back();
    }

}
