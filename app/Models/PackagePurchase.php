<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PackagePurchase extends Model
{

    protected $fillable = [
        'user_id',
        'service_package_id',
        'payment_status',
        'admin_verified_at',
        'admin_verified_by',
        'paid_at'
    ];

    protected $casts = [
        'admin_verified_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public static function getUserPurchases($userId)
    {
        return self::where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->get()
            ->keyBy('service_package_id');
    }

    public static function hasPackage($userId, $packageCode)
    {
        $package = ServicePackage::where('code', $packageCode)->first();
        if (!$package) return false;

        return self::where('user_id', $userId)
            ->where('service_package_id', $package->id)
            ->where('payment_status', 'paid')
            ->exists();
    }
}
