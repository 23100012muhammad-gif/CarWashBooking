<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Cuci Luar',
                'description' => 'Layanan cuci eksterior mobil, termasuk pembersihan bodi, kaca, dan velg.',
                'price' => 35000,
            ],
            [
                'name' => 'Cuci Dalam',
                'description' => 'Layanan cuci interior mobil, termasuk vacuum, pembersihan dashboard dan jok.',
                'price' => 50000,
            ],
            [
                'name' => 'Cuci Full',
                'description' => 'Layanan cuci lengkap bagian luar dan dalam mobil.',
                'price' => 75000,
            ],
            [
                'name' => 'Salon Mobil',
                'description' => 'Layanan salon lengkap termasuk cuci, poles, wax, dan detailing interior.',
                'price' => 150000,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
