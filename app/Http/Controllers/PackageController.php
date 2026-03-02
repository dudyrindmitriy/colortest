<?php

namespace App\Http\Controllers;

use App\Models\PackagePurchase;
use App\Models\ServicePackage;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function purchase(ServicePackage $package)
    {
        $user = Auth::user();

        // Проверяем email
        if (empty($user->email)) {
            return redirect()->back()->with('error', 'Укажите email в профиле');
        }

        // Проверяем все тесты
        $totalTests = Test::count();
        $passedTests = $user->results()->distinct('test_id')->count('test_id');

        if ($passedTests < $totalTests) {
            return redirect()->back()->with('error', 'Пройдите все тесты');
        }

        // Проверяем, есть ли уже непросроченная/неоплаченная покупка этого пакета
        $existingPurchase = PackagePurchase::where('user_id', $user->id)
            ->where('service_package_id', $package->id)
            ->whereIn('payment_status', ['pending', 'paid']) // ожидает или уже оплачен
            ->first();

        if ($existingPurchase) {
            if ($existingPurchase->payment_status === 'paid') {
                return redirect()->back()->with('info', 'У вас уже куплен этот пакет');
            } else {
                // Если есть ожидающая оплаты - редиректим на страницу оплаты
                return redirect()->route('packages.payment', $existingPurchase)
                    ->with('info', 'Продолжите оплату');
            }
        }

        // Создаем новую покупку
        $purchase = PackagePurchase::create([
            'user_id' => $user->id,
            'service_package_id' => $package->id,
            'payment_status' => 'pending'
        ]);

        return redirect()->route('packages.payment', $purchase);
    }

    public function payment(PackagePurchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) {
            abort(403);
        }
        $admins = User::where('isAdmin', 1)->get();

        return view('packages.payment', compact('purchase', 'admins'));
    }
}
