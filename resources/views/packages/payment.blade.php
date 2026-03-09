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
            'cancelled' => 'Отменен',
        ];

    @endphp

    <div class="container">
        <h1>Оплата пакета</h1>

        <article>
            <h3>{{ $packageNames[$package->code] }}</h3>
            @if (!$purchase)
                @if ($hasDiscount)
                    <p>
                        <span style="text-decoration: line-through; color: #999;">Обычная цена:
                            {{ number_format($package->price, 0, ',', ' ') }} ₽</span>
                    </p>
                    <p style="font-size: 1.2em; font-weight: bold;">
                        Цена со скидкой: {{ number_format($finalPrice, 0, ',', ' ') }} ₽
                    </p>
                @else
                    <p>Сумма: {{ number_format($package->price, 0, ',', ' ') }} ₽</p>
                @endif

                {{-- Заглушка для QR-кода --}}
                <div style="text-align: center; padding: 20px; background: #f5f5f5;">
                    <p>Здесь будет QR-код для оплаты</p>
                </div>
                <div style="text-align: center; margin: 20px 0;">
                    <form action="{{ route('packages.store', $package) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 15px;">
                            <label for="name">Имя</label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', auth()->user()->name ?? '') }}" required
                                >
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label for="phone">Номер телефона для связи</label>
                            <input type="tel" name="phone" id="phone" class="phone"
                                value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                                placeholder="+7 (___) ___-__-__" >
                            <small style="color: #666;">На этот номер придет уведомление о покупке</small>
                        </div>
                        <button type="submit">Я оплатил</button>
                    </form>
                    <p style="font-size: 0.9em; color: #666; margin-top: 10px;">
                        Нажмите после совершения платежа. После проверки администратором вам откроется доступ к пакету.
                    </p>


                </div>
            @else
                <p>Статус: {{ $statusNames[$purchase->payment_status] }}</p>
            @endif

            <p>Контакты:</p>
            @foreach ($admins as $admin)
                {{ $admin->email }}<br>
            @endforeach
        </article>
    </div>
@endsection
