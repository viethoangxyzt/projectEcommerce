<?php

namespace App\Services;

use App\Repository\Eloquent\ProductRepository;
use App\Repository\Eloquent\ProductReviewRepository;

class HomeService 
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductReviewRepository
     */
    private $productReviewRepository;

    /**
     * ProductService constructor.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository, ProductReviewRepository $productReviewRepository)
    {
        $this->productRepository = $productRepository;
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get list payments
        $bellingProducts = $this->productRepository->getBestSellingProduct();
        foreach($bellingProducts as $key => $bellingProduct) {
            $bellingProducts[$key]->avg_rating = $this->productReviewRepository->avgRatingProduct($bellingProduct->id)->avg_rating ?? 0;
        }

        $newProducts = $this->productRepository->getNewProducts();
        foreach($newProducts as $key => $newProduct) {
            $newProducts[$key]->avg_rating = $this->productReviewRepository->avgRatingProduct($newProduct->id)->avg_rating ?? 0;
            $newProducts[$key]->sum = $this->productRepository->getQuantityBuyProduct($newProduct->id);
        }

        return [
            'title' => TextLayoutTitle("payment_method"),
            'bellingProducts' => $bellingProducts,
            'newProducts' => $newProducts,
        ];
    }
}
?>