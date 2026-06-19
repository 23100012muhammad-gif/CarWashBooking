<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = [
            [
                'name' => 'Transfer Bank',
                'type' => 'bank_transfer',
                'description' => 'Transfer ke rekening bank kami',
                'config' => [
                    'banks' => [
                        [
                            'name' => 'BCA',
                            'account_number' => '1234567890',
                            'account_name' => 'CarWash Connect',
                            'branch' => 'Cabang Utama'
                        ],
                        [
                            'name' => 'Mandiri',
                            'account_number' => '0987654321',
                            'account_name' => 'CarWash Connect',
                            'branch' => 'Cabang Utama'
                        ],
                        [
                            'name' => 'BNI',
                            'account_number' => '1122334455',
                            'account_name' => 'CarWash Connect',
                            'branch' => 'Cabang Utama'
                        ]
                    ]
                ],
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'E-Wallet',
                'type' => 'ewallet',
                'description' => 'Pembayaran dengan GoPay, OVO, atau Dana',
                'config' => [
                    'gateway' => 'midtrans',
                    'methods' => ['gopay', 'ovo', 'dana'],
                    'sandbox' => true,
                ],
                'active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}

