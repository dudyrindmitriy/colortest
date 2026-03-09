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
    public function payment(ServicePackage $package)
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

        // Проверяем существующие покупки
        $existingPurchase = PackagePurchase::where('user_id', $user->id)
            ->where('service_package_id', $package->id)
            ->whereIn('payment_status', ['pending', 'paid'])
            ->first();

        $admins = User::where('isAdmin', 1)->get();

        $finalPrice = $package->getPriceForUser($user->id);
        $hasDiscount = $finalPrice < $package->price;

        return view('packages.payment', [
            'package' => $package,
            'purchase' => $existingPurchase, // может быть null
            'admins' => $admins,
            'finalPrice' => $finalPrice,
            'hasDiscount' => $hasDiscount
        ]);
    }

    /**
     * Создание новой покупки (нажатие кнопки "Оплатил")
     */
    public function store(Request $request, ServicePackage $package)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);
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

        // Проверяем, не создана ли уже покупка
        $existingPurchase = PackagePurchase::where('user_id', $user->id)
            ->where('service_package_id', $package->id)
            ->whereIn('payment_status', ['pending', 'paid'])
            ->first();

        if ($existingPurchase) {
            return redirect()->route('packages.payment', $package)
                ->with('info', 'Уже есть заявка на этот пакет');
        }
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        $finalPrice = $package->getPriceForUser($user->id);

        // Создаем новую покупку
        $purchase = PackagePurchase::create([
            'user_id' => $user->id,
            'service_package_id' => $package->id,
            'amount' => $finalPrice,
            'payment_status' => 'pending'
        ]);

        return redirect()->route('packages.payment', $package)
            ->with('success', 'Заявка на оплату создана');
    }
}
