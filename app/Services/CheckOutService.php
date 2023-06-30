<?php

namespace App\Services;

use App\Http\Requests\CheckOutRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\ProductSize;
use App\Models\TemporaryAddress;
use App\Repository\Eloquent\OrderDetailRepository;
use App\Repository\Eloquent\OrderRepository;
use App\Repository\Eloquent\TemporaryAddressRepository;
use Darryldecode\Cart\Cart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

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
     * @var TempotaryAddressRepository
     */
    private $temporaryAddressRepository;

    /**
     * CheckOutService constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository, TemporaryAddressRepository $temporaryAddressRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->temporaryAddressRepository = $temporaryAddressRepository;
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
            'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $citys = json_decode($response->body(), true);

        $response = Http::withHeaders([
            'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
            'province_id' => $city,
        ]);
        $districts = json_decode($response->body(), true);

        $response = Http::withHeaders([
            'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $district,
        ]);
        $wards = json_decode($response->body(), true);

        $payments = Payment::where('status', Payment::STATUS['active'])->get();
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
            $fromDistrict = "1542";
            $shopId = "4237150";
            $toDistrict = $request->district;
            $response = Http::withHeaders([
                'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
            ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                "shop_id" => $shopId,
                "from_district" => $fromDistrict,
                "to_district" => $toDistrict,
            ]);
            $serviceId = $response['data'][0]['service_id'];
            $weightOrder = $this->getWeightOrder();
            //data get fee
            $dataGetFee = [
                "service_id" => $serviceId,
                "insurance_value" => 500000,
                "coupon" => null,
                "from_district_id" => $fromDistrict,
                "to_district_id" => $request->district,
                "to_ward_code" => $request->ward,
                "height" => 20,
                "length" => 20,
                "weight" => $weightOrder,
                "width" => 20
            ];
            $response = Http::withHeaders([
                'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
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
                'city' => $request->city,
                'district' => $request->district,
                'ward' => $request->ward,
                'apartment_number' => $request->apartment_number
            ];
            DB::beginTransaction();
            // create order
            $order = $this->orderRepository->create($dataOrder);
            // create order detail
            foreach (\Cart::getContent() as $product) {
                // data order detail
                // dd(\Cart::getContent());
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
            dd($e);
            DB::rollBack();
            // check quantity product
            foreach (\Cart::getContent() as $product) {
                $productSize = ProductSize::find($product->id);
                dd($productSize->quantity);
                if ($productSize->quantity < $product->quantity) {
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

    public function paymentMomo(CheckOutRequest $request)
    {
        $dataTemporaryAddress = [
            'user_id' => Auth::user()->id,
            'city' => $request->city,
            'district' => $request->district,
            'ward' => $request->ward,
            'apartment_number' => $request->apartment_number,
            'transport_fee' => $this->getTransportFee($request)
        ];
        TemporaryAddress::create($dataTemporaryAddress);
        return $this->payWithMoMo(time() . mt_rand(111, 999) . "", \Cart::getTotal() + $this->getTransportFee($request) . "", route('checkout.callback_momo'), route('cart.index'));
    }

    public function getTransportFee(CheckoutRequest $request)
    {
        //get service id
        $fromDistrict = "1542";
        $shopId = "4237150";
        $toDistrict = $request->district;
        $response = Http::withHeaders([
            'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
            "shop_id" => $shopId,
            "from_district" => $fromDistrict,
            "to_district" => $toDistrict,
        ]);
        $serviceId = $response['data'][0]['service_id'];
        $weightOrder = $this->getWeightOrder();
        //data get fee
        $dataGetFee = [
            "service_id" => $serviceId,
            "insurance_value" => 500000,
            "coupon" => null,
            "from_district_id" => $fromDistrict,
            "to_district_id" => $request->district,
            "to_ward_code" => $request->ward,
            "height" => 20,
            "length" => 20,
            "weight" => $weightOrder,
            "width" => 2
        ];
        $response = Http::withHeaders([
            'token' => 'd2852b91-09c4-11ee-a967-deea53ba3605'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', $dataGetFee);

        return $response['data']['total'];
    }

    public function callbackMomo(Request $request)
    {
        if ($request->errorCode != 0) {
            TemporaryAddress::where('user_id', Auth::user()->id)->first()->delete();
            return redirect()->route('cart.index')->with('error', $request->localMessage);
           
        }
        try {
            if (!$this->checkSignature($request)) {
                return redirect()->route('user.home');
            }
            $temporaryAddress = TemporaryAddress::where('user_id', Auth::user()->id)->first();
            //data order
            $dataOrder = [
                'id' => $request->orderId,
                'payment_id' => 2,
                'user_id' => Auth::user()->id,
                'total_money' => $request->amount,
                'order_status' => Order::STATUS_ORDER['wait'],
                'transport_fee' => $temporaryAddress->transport_fee,
                'note' => null,
                'payment_status' => 1,
                'city' => $temporaryAddress->city,
                'district' => $temporaryAddress->district,
                'ward' => $temporaryAddress->ward,
                'apartment_number' => $temporaryAddress->apartment_number,
            ];
            DB::beginTransaction();
            // create order
            $order = $this->orderRepository->create($dataOrder);
            TemporaryAddress::where('user_id', Auth::user()->id)->first()->delete();
            // create order detail
            foreach (\Cart::getContent() as $product) {
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
            foreach (\Cart::getContent() as $product) {
                $productSize = ProductSize::where('id', $product->id)->first();
                if ($productSize->quantity < $product->quantity) {
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
        $requestId = $request->requestId . "";
        $amount = $request->amount . "";
        $orderId = $request->orderId . "";
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
            "&transId=" . $transId .
            "&message=" . $message .
            "&localMessage=" . $localMessage .
            "&responseTime=" . $responseTime .
            "&errorCode=" . $errorCode .
            "&payType=" . $payType .
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
        $requestId = time() . mt_rand(111, 999) . "";
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
            'signature' => $signature,
        );
        $result = Http::acceptJson([
            'application/json'
        ])->post($endPoint, $data);
        $jsonResult = json_decode($result->body(), true);  // decode json
        return redirect($jsonResult['payUrl']);
    }

    public function getWeightOrder()
    {
        $weightOrder = 0;
        foreach (\Cart::getContent() as $product) {
            ($product->weight) ? $weightOrder += $product->weight * $product->quantity : $weightOrder += 200 * $product->quantity;
        }
        return $weightOrder;
    }
}
