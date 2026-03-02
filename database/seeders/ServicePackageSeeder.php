<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServicePackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'code' => 'basic',
                'price' => 1990.00,
                'sort_order' => 1,
            ],
            [
                'code' => 'standard',
                'price' => 3990.00,
                'sort_order' => 2,
            ],
            [
                'code' => 'pro',
                'price' => 5990.00,
                'sort_order' => 3,
            ]
        ];

        foreach ($packages as $package) {
            ServicePackage::updateOrCreate(
                ['code' => $package['code']],
                $package
            );
        }
    }
}
