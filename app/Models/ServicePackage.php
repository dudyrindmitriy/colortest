<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(PackagePurchase::class, 'service_package_id');
    }

    public function getPriceForUser($userId)
    {
        $hasBasic = PackagePurchase::hasPackage($userId, 'basic');
        $hasStandard = PackagePurchase::hasPackage($userId, 'standard');
        $hasPro = PackagePurchase::hasPackage($userId, 'pro');

        return $this->calculateDiscountedPrice($hasBasic, $hasStandard, $hasPro);
    }

    public function calculateDiscountedPrice($hasBasic, $hasStandard, $hasPro)
    {
        $price = $this->price;

        if ($hasPro) {
            return 0;
        }

        if ($hasStandard) {
            if ($this->code == 'basic') {
                return 0;
            }
            if ($this->code == 'pro') {
                $standardPackage = self::where('code', 'standard')->first();
                $standardPrice = $standardPackage ? $standardPackage->price : 0;
                return max(0, $price - $standardPrice);
            }
        }

        if ($hasBasic) {
            if ($this->code == 'standard') {
                $basicPackage = self::where('code', 'basic')->first();
                $basicPrice = $basicPackage ? $basicPackage->price : 0;
                return max(0, $price - $basicPrice);
            }
            if ($this->code == 'pro') {
                $basicPackage = self::where('code', 'basic')->first();
                $basicPrice = $basicPackage ? $basicPackage->price : 0;
                return max(0, $price - $basicPrice);
            }
        }

        return $price;
    }
}
