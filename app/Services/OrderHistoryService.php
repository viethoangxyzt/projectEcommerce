<?php

namespace App\Services;

use App\Helpers\TextSystemConst;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSize;
use App\Repository\Eloquent\OrderRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderHistoryService 
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderService constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        $orderHistorys = $this->orderRepository->getOrderByUser(Auth::user()->id);

        return ['orderHistorys' => $orderHistorys];
    }

    public function show(Order $order)
    {
        $address['apartment_number'] = $order->apartment_number;
        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $data = json_decode($response->body(), true);
        foreach ($data['data'] as $item) {
            if ($order->city == $item['ProvinceID']) {
                $address['city'] = $item['NameExtension'][1];
            }
        }
        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
            'province_id' => $order->city,
        ]);
        $data = json_decode($response->body(), true);
        foreach ($data['data'] as $item) {
            if ($order->district == $item['DistrictID']) {
                 $address['district'] = $item['DistrictName'];
            }
        }

        $response = Http::withHeaders([
            'token' => '24d5b95c-7cde-11ed-be76-3233f989b8f3'
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $order->district,
        ]);
        $data = json_decode($response->body(), true);
        foreach ($data['data'] as $item) {
            if ($order->ward == $item['WardCode']) {
                $address['ward'] = $item['NameExtension'][0];
            }
        }
        return [
            'order' => $order,
            'order_details' => $this->orderRepository->getOrderDetail($order->id),
            'infomationUser' => $this->orderRepository->getInfoUserOfOrder($order->id),
            'address' => $address
        ];
    }

    public function update(Order $order)
    {
        try {
            switch($order->order_status){
                
                case 0:
                    $this->orderRepository->update($order, ['order_status' => Order::STATUS_ORDER['cancel'], 'user_cancel' => 1]);
                    $orderDetails = OrderDetail::where('order_id', $order->id)->get();
                    foreach($orderDetails as $orderDetail) {
                        $productSize = ProductSize::where('id', $orderDetail->product_size_id)->first();
                        $productSize->update(['quantity' => $productSize->quantity + $orderDetail->quantity]);
                    }
                    return back()->with('success', TextSystemConst::MESS_ORDER_HISTORY['cancel']);
                case 1:
            
                    $this->orderRepository->update($order, ['order_status' => Order::STATUS_ORDER['received'], 'can_review_time' => now()->addDays(7)]);
                    return back()->with('success', TextSystemConst::MESS_ORDER_HISTORY['confirm']);
                case 2:
                    $this->orderRepository->delete($order);
                    return back()->with('success', TextSystemConst::MESS_ORDER_HISTORY['delete']);
                case 3:
                    $this->orderRepository->delete($order);
                    return back()->with('success', TextSystemConst::MESS_ORDER_HISTORY['delete']);
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
?>