<?php

namespace App\Repository\Eloquent;

use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductReviewRepository
 * @package App\Repositories\Eloquent
 */
class ProductReviewRepository extends BaseRepository
{
    /**
     * ProductReviewRepository constructor.
     *
     * @param ProductReview $productReview
     */
    public function __construct(ProductReview $productReview)
    {
        parent::__construct($productReview);
    }

    public function checkUserBuyProduct($productId, $userId)
    {
        $currentTime = date('Y-m-d H:i:s');
        return DB::select("
            select * from products 
            join products_color on products.id = products_color.product_id
            join products_size on products_color.id = products_size.product_color_id
            join order_details on order_details.product_size_id = products_size.id
            join orders on orders.id = order_details.order_id
            where orders.order_status = 3 and products.id = $productId 
            and orders.user_id = $userId and orders.can_review_time < '$currentTime';
        ");
    }

    public function checkUserProductReview($productId, $userId)
    {
        return DB::table('order_details')
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->join('products_size', 'products_size.id', '=', 'order_details.product_size_id')
        ->join('products_color', 'products_color.id', '=', 'products_size.product_color_id')
        ->join('products', 'products.id', '=', 'products_color.product_id')
        ->where('products.id', $productId)
        ->where('orders.user_id', $userId)
        ->where('orders.order_status', 3)
        ->where('orders.can_review_time', '<',now())
        ->orderBy('orders.can_review_time', 'desc')
        ->select('order_details.*')
        ->first();

    }

    public function checkLimitReview($productId, $userId) {
        return $this->model->where('product_id', $productId)
        ->where('user_id', $userId)
        ->where('created_at', '>=', now()->subDays(7))->count();
    }

    public function getRatingByProduct($productId)
    {
        return DB::select("
            select count(*) as sum, product_reviews.product_id, product_reviews.rating from products 
            join product_reviews on products.id = product_reviews.product_id 
            where products.id = $productId
            and product_reviews.deleted_at is null
            group by product_reviews.product_id, product_reviews.rating
        ");
    }

    public function getProductReview($productId)
    {
        return $this->model
        ->join('products', 'products.id', '=', 'product_reviews.product_id')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'product_reviews.user_id')
                 ->where('users.active', '=', 1)
                 ->whereNull('users.deleted_at')
                 ->whereNull('product_reviews.deleted_at');
        })
        ->select('users.name as user_name', 'product_reviews.*')
        ->where('product_reviews.product_id', '=', $productId)
        ->orderBy('id', 'desc')
        ->paginate(ProductReview::PRODUCT_REVIEW_NUMBER_ITEM);
    }

    public function avgRatingProduct($productId)
    {
        return DB::table('product_reviews')
        ->join('products', 'products.id', '=', 'product_reviews.product_id')
        ->select(DB::raw('sum(product_reviews.rating) / count(*) as avg_rating'))
        ->where('products.id', $productId)
        ->first();
    }

}

?>