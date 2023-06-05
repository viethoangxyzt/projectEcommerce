<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\HomeService;

class HomeController extends Controller
{
    /**
     * @var HomeService
     */
    private $homeService;

    /**
     * HomeController constructor.
     *
     * @param HomeService $homeService
     */
    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }
    /**
     * Displays home website.
     *
     * @return \Illuminate\View\View
     */
    public function index() 
    {
        // dd(category_header());
        return view('client.index', $this->homeService->index());
    }

    public function maintenance()
    {
        $setting = Setting::first();
        return view('client.maintenance', ['setting' => $setting]);
    }

    public function introduction()
    {
        $setting = Setting::first();
        return view('client.introduction', ['setting' => $setting]);
    }
}
