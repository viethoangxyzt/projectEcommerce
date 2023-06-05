<?php

namespace App\Services;

use App\Repository\Eloquent\OrderRepository;
use App\Repository\Eloquent\UserRepository;
use Carbon\Carbon;

class DashboardService 
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * DashboardService constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $revenue = $this->orderRepository->getRevenue();
        $orders = $this->orderRepository->getOrderTotal();
        $products = $this->orderRepository->getProductTotal();
        $profit =  $this->orderRepository->getProfit();
        $productSold = $this->orderRepository->getTotalProductSold();
        $users = count($this->userRepository->all());
        $admins = count($this->userRepository->admins());
        $salesStatisticsByDays = $this->orderRepository->salesStatisticsByDay();

        // Get the current month and year
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Get the number of days in the month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Loop through each day of the month and store it in an array
        $daysArray = [];
        $parameters = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $daysArray[] = $day;
            $check = false;
            for ($i = 0; $i < count($salesStatisticsByDays); $i++) {
                if ($salesStatisticsByDays[$i]->day == $day) {
                    $check = true;
                    $parameters[$day - 1] = $salesStatisticsByDays[$i]->total;
                }
            }
            if ($check == false) {
                $parameters[$day - 1] = 0;
            }
        }

        //get best selling product
        $bestSellProducts = $this->orderRepository->bestSellProducts();

        $labelBestSellProduct = [];
        $parameterBestSellProduct = [];
        foreach ($bestSellProducts as $product) {
            $labelBestSellProduct[] = $product->name;
            $parameterBestSellProduct[] = $product->sum;
        }

        // get best products review
        $bestProductReviews = $this->orderRepository->bestProductReviews();
        $labelBestProductReview = [];
        $parameterBestProductReview = [];
        foreach ($bestProductReviews as $product) {
            $labelBestProductReview[] = $product->name;
            $parameterBestProductReview[] = $product->sum;
        }
        // Get list order
        $list = $this->orderRepository->getNewOrders();
        $tableCrud = [
            'headers' => [
                [
                    'text' => 'Mã HD',
                    'key' => 'id',
                ],
                [
                    'text' => 'Tên KH',
                    'key' => 'user_name',
                ],
                [
                    'text' => 'Email',
                    'key' => 'user_email',
                ],
                [
                    'text' => 'Tổng Tiền',
                    'key' => 'total_money',
                    'format' => true,
                ],
                [
                    'text' => 'PT Thanh Toán',
                    'key' => 'payment_name',
                ],
                [
                    'text' => 'Ngày Đặt Hàng',
                    'key' => 'created_at',
                ],
                [
                    'text' => 'Trạng Thái',
                    'key' => 'order_status',
                    'status' => [
                        [
                            'text' => 'Chờ Xử Lý',
                            'value' => 0,
                            'class' => 'badge badge-warning'
                        ],
                        [
                            'text' => 'Đang Giao Hàng',
                            'value' => 1,
                            'class' => 'badge badge-info'
                        ],
                        [
                            'text' => 'Đã Hủy',
                            'value' => 2,
                            'class' => 'badge badge-danger'
                        ],
                        [
                            'text' => 'Đã Nhận Hàng',
                            'value' => 3,
                            'class' => 'badge badge-success'
                        ],
                    ],
                ],
            ],
            'actions' => [
                'text'          => "Thao Tác",
                'create'        => false,
                'createExcel'   => false,
                'edit'          => true,
                'deleteAll'     => false,
                'delete'        => true,
                'viewDetail'    => false,
            ],
            'routes' => [
                'delete' => 'admin.orders_delete',
                'edit' => 'admin.orders_edit',
            ],
            'list' => $list,
        ];
        return [
            'title' => TextLayoutTitle("dashboard"),
            'revenue' => $revenue,
            'orders' => $orders,
            'products' => $products,
            'profit' => $profit,
            'users' => $users,
            'days' => json_encode($daysArray),
            'parameters' => json_encode($parameters),
            'year' => $year,
            'month' => $month,
            'tableCrud' => $tableCrud,
            'labelBestSellProduct' => json_encode($labelBestSellProduct),
            'parameterBestSellProduct' => json_encode($parameterBestSellProduct),
            'labelBestProductReview' => json_encode($labelBestProductReview),
            'parameterBestProductReview' => json_encode($parameterBestProductReview),
            'admins' => $admins
        ];
    }

}
?>