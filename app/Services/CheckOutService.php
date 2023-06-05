<?php

namespace App\Services;

use App\Http\Requests\CheckOutRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductSize;
use App\Repository\Eloquent\OrderDetailRepository;
use App\Repository\Eloquent\OrderRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckOutService 
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderDetailRepository
     */
    private $orderDetailRepository;

    /**
     * CheckOutService constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
    }
    public function index()
    {
        $city = old('city') ?? Auth::user()->address->city;
        $district = old('district') ?? Auth::user()->address->district;
        $ward = old('ward') ?? Auth::user()->address->ward;
        $apartment_number = old('apartment_number') ?? Auth::user()->address->apartment_number;
        $phoneNumber = old('phone_number') ?? Auth::user()->phone_number;
        $fullName = old('full_name') ?? Auth::user()->name;
        $email = old('email') ?? Auth::user()->email;

        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $citys = json_decode($response->body(), true);

        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
            'province_id' => $city,
        ]);
        $districts = json_decode($response->body(), true);

        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $district,
        ]);
        $wards = json_decode($response->body(), true);

        $payments = Payment::where('status', Payment::STATUS['active'])-> get();
        return [
            'citys' => $citys['data'],
            'districts' => $districts['data'],
            'wards' => $wards['data'],
            'city' => $city,
            'district' => $district,
            'ward' => $ward,
            'apartment_number' => $apartment_number,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
            'fullName' => $fullName,
            'payments' => $payments,
        ];
    }

    public function store(CheckOutRequest $request)
    {
        try {
            //get service id
            $fromDistrict = "1530";
            $shopId = "3577591";
            $toDistrict = Auth::user()->address->district;
            $response = Http::withHeaders([
                'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                "shop_id" => $shopId,
                "from_district" => $fromDistrict,
                "to_district" => $toDistrict,
            ]);
            $serviceId = $response['data'][0]['service_id'];
            
            //data get fee
            $dataGetFee = [
                "service_id" => $serviceId,
                "insurance_value" => 500000,
                "coupon" => null,
                "from_district_id" => $fromDistrict,
                "to_district_id" => Auth::user()->address->district,
                "to_ward_code" => Auth::user()->address->ward,
                "height" => 15,
                "length" => 15,
                "weight" => 1000,
                "width" => 15
            ];
            $response = Http::withHeaders([
                'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', $dataGetFee);
            $fee = $response['data']['total'];
            //data order
            $dataOrder = [
                'id' => time() . mt_rand(111, 999),
                'payment_id' => $request->payment_method,
                'user_id' => Auth::user()->id,
                'total_money' => \Cart::getTotal() + $fee,
                'order_status' => Order::STATUS_ORDER['wait'],
                'transport_fee' => $fee,
                'note' => null,
            ];
            DB::beginTransaction();
            // create order
            $order = $this->orderRepository->create($dataOrder);

            // create order detail
            foreach(\Cart::getContent() as $product){
                // data order detail
                $orderDetail = [
                    'order_id' => $order->id,
                    'product_size_id' => $product->id,
                    'unit_price' => $product->price,
                    'quantity' => $product->quantity,
                ];
                $this->orderDetailRepository->create($orderDetail);
            }
            DB::commit();
            // remove cart
            \Cart::clear();

            return redirect()->route('order_history.index');
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            // check quantity product
            foreach(\Cart::getContent() as $product){
                $productSize = ProductSize::where('id', $product->id)->first();
                if($productSize->quantity < $product->quantity) {
                    \Cart::update(
                        $product->id,
                        [
                            'quantity' => [
                                'relative' => false,
                                'value' => $productSize->quantity
                            ],
                        ]
                    );
                }
            }
            return redirect()->route('cart.index')->with('error', 'Có lỗi xảy ra vui lòng kiểm tra lại');
        }
    }

    public function paymentMomo() 
    {
        return $this->payWithMoMo(time() . mt_rand(111, 999)."", \Cart::getTotal() + $this->getTransportFee()."", route('checkout.callback_momo'), route('cart.index'));
    }

    public function getTransportFee()
    {
        //get service id
        $fromDistrict = "1530";
        $shopId = "3577591";
        $toDistrict = Auth::user()->address->district;
        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
            "shop_id" => $shopId,
            "from_district" => $fromDistrict,
            "to_district" => $toDistrict,
        ]);
        $serviceId = $response['data'][0]['service_id'];
        
        //data get fee
        $dataGetFee = [
            "service_id" => $serviceId,
            "insurance_value" => 500000,
            "coupon" => null,
            "from_district_id" => $fromDistrict,
            "to_district_id" => Auth::user()->address->district,
            "to_ward_code" => Auth::user()->address->ward,
            "height" => 15,
            "length" => 15,
            "weight" => 1000,
            "width" => 15
        ];
        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', $dataGetFee);

        return $response['data']['total'];
    }

    public function callbackMomo(Request $request)
    {
        try {
            if (! $this->checkSignature($request)) {
                return redirect()->route('user.home');
            }
            //data order
            $dataOrder = [
                'id' => $request->orderId,
                'payment_id' => 2,
                'user_id' => Auth::user()->id,
                'total_money' => $request->amount,
                'order_status' => Order::STATUS_ORDER['wait'],
                'transport_fee' => $this->getTransportFee(),
                'note' => null,
            ];
            DB::beginTransaction();
            // create order
            $order = $this->orderRepository->create($dataOrder);

            // create order detail
            foreach(\Cart::getContent() as $product){
                // data order detail
                $orderDetail = [
                    'order_id' => $order->id,
                    'product_size_id' => $product->id,
                    'unit_price' => $product->price,
                    'quantity' => $product->quantity,
                ];
                $this->orderDetailRepository->create($orderDetail);
            }
            DB::commit();
            // remove cart
            \Cart::clear();

            return redirect()->route('order_history.index');
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            // check quantity product
            foreach(\Cart::getContent() as $product){
                $productSize = ProductSize::where('id', $product->id)->first();
                if($productSize->quantity < $product->quantity) {
                    \Cart::update(
                        $product->id,
                        [
                            'quantity' => [
                                'relative' => false,
                                'value' => $productSize->quantity
                            ],
                        ]
                    );
                }
            }
            return redirect()->route('cart.index')->with('error', 'Có lỗi xảy ra vui lòng kiểm tra lại');
        }
    }

    public function checkSignature(Request $request)
    {
        $partnerCode = $request->partnerCode;
        $accessKey = $request->accessKey;
        $requestId = $request->requestId."";
        $amount = $request->amount."";
        $orderId = $request->orderId."";
        $orderInfo = $request->orderInfo;
        $orderType = $request->orderType;
        $transId = $request->transId;
        $message = $request->message;
        $localMessage = $request->localMessage;
        $responseTime = $request->responseTime;
        $errorCode = $request->errorCode;   
        $payType = $request->payType;
        $extraData = $request->extraData;
        $secretKey = env('MOMO_SECRET_KEY');
        $extraData = "";

        $rawHash = "partnerCode=" . $partnerCode .
            "&accessKey=" . $accessKey . 
            "&requestId=" . $requestId . 
            "&amount=" . $amount . 
            "&orderId=" . $orderId . 
            "&orderInfo=" . $orderInfo . 
            "&orderType=" . $orderType .
            "&transId=" . $transId. 
            "&message=" . $message .
            "&localMessage=" . $localMessage.
            "&responseTime=" . $responseTime.
            "&errorCode=" . $errorCode. 
            "&payType=" . $payType. 
            "&extraData=" . $extraData;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        if (hash_equals($signature, $request->signature)) {
            return true;
        }
        
        return false;
    }

    public function payWithMoMo($orderId, $amount, $returnUrl, $notifyurl)
    {
        $endPoint = env('MOMO_END_POINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $orderInfo = "Thanh toán qua MoMo";
        $bankCode = env('MOMO_BANK_CODE');
        $requestId = time().mt_rand(111, 999)."";
        $requestType = "captureMoMoWallet";
        $extraData = "";
        $rawHash = "partnerCode=" . $partnerCode .
            "&accessKey=" . $accessKey . 
            "&requestId=" . $requestId . 
            "&amount=" . $amount . 
            "&orderId=" . $orderId . 
            "&orderInfo=" . $orderInfo . 
            "&returnUrl=" . $returnUrl . 
            "&notifyUrl=" . $notifyurl . 
            "&extraData=" . $extraData;
        // $rawHash = "partnerCode=".$partnerCode."&accessKey=".$accessKey."&requestId=".$requestId."&bankCode=".$bankCode."&amount=".$amount."&orderId=".$orderId."&orderInfo=".$orderInfo."&returnUrl=".$returnUrl."&notifyUrl=".$notifyurl."&extraData=".$extraData."&requestType=".$requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data =  array(
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'bankCode' => $bankCode,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = Http::acceptJson([
            'application/json'
        ])->post($endPoint, $data);
        $jsonResult = json_decode($result->body(), true);  // decode json
        return redirect($jsonResult['payUrl']);
    }
}
?>