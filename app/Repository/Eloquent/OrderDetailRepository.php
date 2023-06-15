<?php

namespace App\Repository\Eloquent;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Repository\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderDetailRepository
 * @package App\Repositories\Eloquent
 */
class OrderDetailRepository extends BaseRepository
{
    /**
     * OrderDetailRepository constructor.
     *
     * @param OrderDetail $orderDetail
     */
    public function __construct(OrderDetail $orderDetail)
    {
        parent::__construct($orderDetail);
    }

    /**
 * Get the latest order detail by user_id and product_id.
 */
public function updateStatusReview($userId, $productId)
{
    $orderDetail = DB::table('order_details')
    ->join('orders', 'orders.id', '=', 'order_details.order_id')
    ->join('products_size', 'products_size.id', '=', 'order_details.product_size_id')
    ->join('products_color', 'products_color.id', '=', 'products_size.product_color_id')
    ->join('products', 'products.id', '=', 'products_color.product_id')
    ->where('products.id', $productId)
    ->where('orders.user_id', $userId)
    ->where('orders.order_status', 3)
    ->where('order_details.review_status', 0)
    ->orderBy('orders.can_review_time', 'desc')
    ->select('order_details.*')
    ->first();
    if ($orderDetail) {
        DB::table('order_details')
            ->where('id', $orderDetail->id)
            ->update(['review_status' => 1]);
    }
    return $orderDetail;
}
}
