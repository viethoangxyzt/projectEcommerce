<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $databases = [
            [
                'table' => 'roles',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Quản trị viên',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Nhân Viên',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Khách hàng',
                    ]
                ],
            ],
            [
                'table' => 'users',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Admin',
                        'email' => 'admin@gmail.com',
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                        'phone_number' => '0000000000',
                        'active' => 1,
                        'role_id' => 1
                    ]
                ]
            ],
            [
                'table' => 'brands',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Nike'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Gucci'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Adidas'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Chanel'
                    ],
                    [
                        'id' => 5,
                        'name' => 'Uniqlo'
                    ],
                ]
            ],
            [
                'table' => 'categories',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Thời Trang Nam',
                        'parent_id' => 0,
                        'slug' => 'thoi-trang-nam'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Thời Trang Nữ',
                        'parent_id' => 0,
                        'slug' => 'thoi-trang-nu'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Áo polo',
                        'parent_id' => 1,
                        'slug' => 'ao-polo'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Áo thể thao',
                        'parent_id' => 1,
                        'slug' => 'ao-the-thao'
                    ],
                    [
                        'id' => 5,
                        'name' => 'Áo Sơ Mi',
                        'parent_id' => 1,
                        'slug' => 'ao-so-mi'
                    ],
                    [
                        'id' => 6,
                        'name' => 'Áo Thun',
                        'parent_id' => 1,
                        'slug' => 'ao-thun'
                    ],
                    [
                        'id' => 7,
                        'name' => 'Quần Jeans',
                        'parent_id' => 1,
                        'slug' => 'quan-jeans'
                    ],
                    [
                        'id' => 8,
                        'name' => 'Quần Shorts',
                        'parent_id' => 1,
                        'slug' => 'quan-shorts'
                    ],
                    [
                        'id' => 9,
                        'name' => 'Áo Thun',
                        'parent_id' => 2,
                        'slug' => 'ao-thun-1'
                    ],
                    [
                        'id' => 10,
                        'name' => 'Đầm Váy',
                        'parent_id' => 2,
                        'slug' => 'dam-vay'
                    ],
                    [
                        'id' => 11,
                        'name' => 'Áo Sơ Mi',
                        'parent_id' => 2,
                        'slug' => 'ao-so-mi-1'
                    ],
                    [
                        'id' => 12,
                        'name' => 'Chân Váy',
                        'parent_id' => 2,
                        'slug' => 'chan-vay'
                    ],
                    [
                        'id' => 13,
                        'name' => 'Quần Jeans',
                        'parent_id' => 2,
                        'slug' => 'quan-jeans-1'
                    ],
                ]
            ],
            [
                'table' => 'payments',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Khi nhận hàng',
                        'status' => 1,
                        'img' => '1682960154.png',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Ví điện tử Momo',
                        'status' => 1,
                        'img' => '1682960202.png',
                    ],
                ]
            ],
            [
                'table' => 'colors',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Trắng'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Đen'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Xám'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Đỏ'
                    ],
                    [
                        'id' => 5,
                        'name' => 'Vàng'
                    ],
                    [
                        'id' => 6,
                        'name' => 'Xanh'
                    ],
                    [
                        'id' => 7,
                        'name' => 'Tím'
                    ],
                ]
            ],
            [
                'table' => 'sizes',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'S'
                    ],
                    [
                        'id' => 2,
                        'name' => 'M'
                    ],
                    [
                        'id' => 3,
                        'name' => 'L'
                    ],
                    [
                        'id' => 4,
                        'name' => 'XL'
                    ],
                ]
            ],
            [
                'table' => 'setting',
                'data' => [
                    [
                        'id' => 1,
                        'logo' => 'logo.png',
                        'name' => 'VHCLOTHINGS',
                        'email' => 'vhclothings@gmail.com',
                        'address' => '1024 Vạn Phúc, quận Hà Đông, thành phố Hà Nội',
                        'phone_number' => '123125934093',
                        'maintenance' => 2,
                        'notification' => '<b>WEBSITE tạm thời bảo trì để nâng cấp xin  quay lại sau</b>',
                        'introduction' => '
                            <h3 style="text-align: center; ">
                            <b>GIỚI THIỆU VỀ VHCLOTHINGS</b>
                            </h3><h5><br></h5><h5><span style="font-family: " source="" sans="" pro";"="" times="" new="" roman"; "="">
                            Tại VH Clothings, chúng tôi tự hào là cửa hàng trực tuyến hàng đầu về thời trang, nơi bạn có thể tìm thấy những sản phẩm thời trang đa dạng và phong phú, giúp bạn tự tin thể hiện phong cách cá nhân độc đáo của mình.
                            </span><br></h5><h5><br></h5><h5>
                            Với sự đam mê và tâm huyết với thời trang, chúng tôi không chỉ cung cấp những sản phẩm thời trang đẹp và chất lượng, mà còn mang đến cho bạn những trải nghiệm mua sắm tuyệt vời. Đội ngũ nhân viên chuyên nghiệp và thân thiện của chúng tôi luôn sẵn sàng hỗ trợ bạn trong quá trình mua sắm, từ việc tìm kiếm sản phẩm, đặt hàng cho đến khi bạn nhận được sản phẩm mong đợi.
                            <br></h5><h5><br></h5><h5>
                            Chúng tôi cam kết chỉ cung cấp những sản phẩm thời trang được làm từ chất liệu tốt nhất, đảm bảo độ bền cao và giá trị sử dụng lâu dài. Mỗi sản phẩm đều được kiểm tra kỹ lưỡng trước khi đưa vào bán hàng, đảm bảo rằng chúng đáp ứng các tiêu chuẩn chất lượng cao mà chúng tôi đặt ra.
                            <br></h5><h5><br></h5><h5>
                            Với sự đa dạng của thiết kế, chúng tôi mang đến cho bạn một bộ sưu tập phong phú, từ những bộ cánh casual hàng ngày cho đến những thiết kế sang trọng cho các dịp đặc biệt. Bạn có thể khám phá các sản phẩm áo thun, quần jean, áo khoác bomber và nhiều thiết kế streetwear đẳng cấp, hoặc tìm kiếm những bộ cánh lịch sự như váy dạ hội, đầm maxi, áo sơ mi hay quần tây cho các buổi tiệc hoặc sự kiện quan trọng.
                            <br></h5><h5><br></h5><h5>
                            Ngoài ra, chúng tôi cũng cung cấp các sản phẩm thời trang thể thao, đáp ứng nhu cầu của những người yêu thích hoạt động thể thao và tìm kiếm sự thoải mái khi mặc.
                            <br></h5><h5><br></h5><h5>
                            Đặc biệt, chúng tôi không chỉ chú trọng vào sản phẩm mà còn chú trọng đến trải nghiệm của khách hàng. Chúng tôi nỗ lực không ngừng để đáp ứng nhu cầu của bạn, từ việc cập nhật xu hướng thời trang mới nhất cho đến việc cải tiến dịch vụ khách hàng, nhằm mang đến cho bạn trải nghiệm mua sắm thú vị và tiện lợi nhất.
                            <br>
                            Hãy truy cập vào website của chúng tôi ngay hôm nay để khám phá thêm về bộ sưu tập thời trang đa dạng và phong phú của chúng tôi. Chúng tôi tin rằng, bạn sẽ tìm thấy những sản phẩm ưng ý và phù hợp với phong cách cá nhân của mình. VH Clothings luôn sẵn lòng đồng hành cùng bạn trên hành trình thời trang của bạn!</h5>
                        '
                    ],
                ]
            ]
        ];

        foreach ($databases as $database) {
            $recordNumber = DB::table($database['table'])->count();
            foreach ($database['data'] as $key => $record) {
                if ($key >= $recordNumber)
                    DB::table($database['table'])->insert($record);
            }
        }
    }
}
