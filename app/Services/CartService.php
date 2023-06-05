<?php

namespace App\Services;

use App\Helpers\TextSystemConst;
use App\Repository\Eloquent\ProductSizeRepository;
use Illuminate\Http\Request;

class CartService 
{
     /**
     * @var ProductSizeRepository
     */
    private $productSizeRepository;

    /**
     * CartService constructor.
     *
     * @param ProductSizeRepository $categoryRepository
     */
    public function __construct(ProductSizeRepository $productSizeRepository)
    {
        $this->productSizeRepository = $productSizeRepository;
    }
    public function index()
    {
        return ['carts' => \Cart::getContent()];
    }

    public function store(Request $request)
    {
        $product = $this->productSizeRepository->getProductSize($request->id);
        if (! $product) {
            return route('user.home');
        }
        //get list cart
        $carts = \Cart::getContent()->toArray();
        //If the product is not in the cart, add it to the cart
        if (! empty($carts) && array_key_exists($request->id, $carts)) {
            //If more products are added than in stock, go back to the previous page
            if ($carts[$request->id]['quantity'] + $request->quantity > $product->products_size_quantity) {
                return back()->with('error', TextSystemConst::ADD_CART_ERROR_QUANTITY);
            }
        }
        if ($request->quantity > $product->products_size_quantity) {
            return back()->with('error', TextSystemConst::ADD_CART_ERROR_QUANTITY);
        }
        \Cart::add([
            'id' => $request->id,
            'name' => $product->product_name,
            'price' => $product->product_price_sell,
            'quantity' => $request->quantity,
            'attributes' => array(
                'image' => $product->product_img,
                'size' => $product->size_name,
                'color' => $product->color_name,
            )
        ]);

        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        $product = $this->productSizeRepository->getProductSize($request->id);
        if($request->quantity > $product->products_size_quantity) {
            return back()->with('error', TextSystemConst::ADD_CART_ERROR_QUANTITY);
        }

        \Cart::update(
            $request->id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
            ]
        );

        return redirect()->route('cart.index');
    }

    public function delete($id)
    {
        \Cart::remove($id);

        return redirect()->route('cart.index');
    }

    public function clearAllCart()
    {
        \Cart::clear();

        return redirect()->route('cart.index');
    }
}
?>