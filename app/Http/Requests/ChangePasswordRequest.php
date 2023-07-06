<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:24',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
            'confirm_password' => 'required_with:new_password|same:new_password',
        ];
    }

    public function messages()
    {
        return [
            'confirm_password.same' => 'Xác nhận mật khẩu không trùng khớp',
            'new_password.regex' => 'Mật khẩu mới phải có ít nhất 1 chữ cái thường, 1 chữ cái in hoa và 1 kí tự đặc biệt.'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->matchCurrentPassword($this->current_password) && $this->current_password != null) {
                $validator->errors()->add('current_password', 'Mật khẩu hiện tại không đúng');
            }
        });
    }

    /**
     * Check current password
     *
     * @param string $currentPassword
     * @return bool
     */
    private function matchCurrentPassword($currentPassword)
    {
        return !Hash::check($currentPassword, Auth::user()->password);
    }

    public function attributes()
    {
        return [
            'current_password' => 'mật khẩu hiện tại',
            'new_password' => 'mật khẩu mới',
            'confirm_password' => 'xác nhận mật khẩu',
        ];
    }

}
