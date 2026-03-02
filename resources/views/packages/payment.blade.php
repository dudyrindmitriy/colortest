@extends('layouts.app')

@section('content')
    @php
        use App\Models\ServicePackage;
        use App\Models\PackagePurchase;

        $packageNames = [
            'basic' => 'Базовый',
            'standard' => 'Стандарт',
            'pro' => 'Расширенный',
        ];

        $statusNames = [
            'pending' => 'Ожидает',
            'paid' => 'Оплачен',
            'cancelled' => 'Отменен'
        ];

        // Проверяем наличие оплаченных пакетов для расчета скидки
        $hasBasic = PackagePurchase::hasPackage(Auth::id(), 'basic');
        $hasStandard = PackagePurchase::hasPackage(Auth::id(), 'standard');
        $hasPro = PackagePurchase::hasPackage(Auth::id(), 'pro');

        // Функция для расчета цены со скидкой
        function getDiscountedPrice($package, $hasBasic, $hasStandard, $hasPro) {
            $price = $package->price;

            if ($hasPro) {
                return 0;
            }

            if ($hasStandard) {
                if ($package->code == 'basic') {
                    return 0;
                }
                if ($package->code == 'pro') {
                    $standardPackage = ServicePackage::where('code', 'standard')->first();
                    $standardPrice = $standardPackage ? $standardPackage->price : 0;
                    return max(0, $price - $standardPrice);
                }
            }

            if ($hasBasic) {
                if ($package->code == 'standard') {
                    $basicPackage = ServicePackage::where('code', 'basic')->first();
                    $basicPrice = $basicPackage ? $basicPackage->price : 0;
                    return max(0, $price - $basicPrice);
                }
                if ($package->code == 'pro') {
                    $basicPackage = ServicePackage::where('code', 'basic')->first();
                    $basicPrice = $basicPackage ? $basicPackage->price : 0;
                    return max(0, $price - $basicPrice);
                }
            }

            return $price;
        }

        $originalPrice = $purchase->package->price;
        $finalPrice = getDiscountedPrice($purchase->package, $hasBasic, $hasStandard, $hasPro);
        $hasDiscount = $finalPrice < $originalPrice;
    @endphp

    <div class="container">
        <h1>Оплата пакета</h1>

        <article>
            <h3>{{ $packageNames[$purchase->package->code] }}</h3>

            @if($hasDiscount)
                <p>
                    <span style="text-decoration: line-through; color: #999;">Обычная цена: {{ number_format($originalPrice, 0, ',', ' ') }} ₽</span>
                </p>
                <p style="font-size: 1.2em; font-weight: bold;">
                    Цена со скидкой: {{ number_format($finalPrice, 0, ',', ' ') }} ₽
                </p>
            @else
                <p>Сумма: {{ number_format($purchase->package->price, 0, ',', ' ') }} ₽</p>
            @endif

            <p>Статус: {{ $statusNames[$purchase->payment_status] }}</p>

            {{-- Заглушка для QR-кода --}}
            <div style="text-align: center; padding: 20px; background: #f5f5f5;">
                <p>Здесь будет QR-код для оплаты</p>
                <p>Ожидайте подтверждения оплаты. После проверки администратором вам откроется доступ к пакету</p>
            </div>
            <p>Контакты:</p>
            @foreach ($admins as $admin)
                {{$admin->email}}<br>
            @endforeach
        </article>
    </div>
@endsection
