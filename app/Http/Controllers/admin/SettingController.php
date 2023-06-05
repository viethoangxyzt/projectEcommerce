<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Displays setting website.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.setting.index', [
            'title' => 'Cấu Hình Website',
            'setting' => Setting::first()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            if ($request->logo) {
                $imageName = time() . '.' . request()->logo->getClientOriginalExtension();
                $logo = Image::make($request->logo);
                $logo->fit(124, 63); // Resize the image to the desired dimensions
                $logo->save(public_path('asset/client/images/') . $imageName);
                $data['logo'] = $imageName;
            }
            $setting = Setting::first();
            $setting->update($data);
            return back()->with('success', 'Cập nhật thông tin thành công');
        } catch (Exception) {
            return back()->with('error', 'Cập nhật thông tin thất bại');
        }
    }
}
