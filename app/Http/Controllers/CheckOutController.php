<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckOutRequest;
use App\Services\CheckOutService;
use Illuminate\Http\Request;

class CheckOutController extends Controller
{
    /**
     * @var CheckOutService
     */
    private $checkOutService;

    /**
     * CheckOutController constructor.
     *
     * @param CheckOutService $checkOutService
     */
    public function __construct(CheckOutService $checkOutService)
    {
        $this->checkOutService = $checkOutService;
    }
    /**
     * Displays home website.
     *
     * @return \Illuminate\View\View
     */
    public function index() 
    {
        if (count(\Cart::getContent()) <= 0) {
            return back();
        }
        return view('client.checkout', $this->checkOutService->index());
    }

    public function store(CheckOutRequest $request)
    {
        // check payment method is momo
        if ($request->payment_method == 2) {
            return $this->checkOutService->paymentMomo();
        }
        
        return $this->checkOutService->store($request);
    }

    public function callbackMomo(Request $request)
    {
        return $this->checkOutService->callbackMomo($request);
    }
}
