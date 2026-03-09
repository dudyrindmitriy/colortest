@php
    use App\Models\Test;
    use App\Models\ServicePackage;
    use App\Models\PackagePurchase;

    $packages = ServicePackage::orderBy('sort_order')->get();
    $totalTests = Test::count();
    $passedTests = Auth::user()->results()->distinct('test_id')->count('test_id');
    $allTestsPassed = $passedTests >= $totalTests;
    $hasEmail = !empty(Auth::user()->email);
    $canPurchase = $allTestsPassed && $hasEmail;
    $userId = Auth::id();

    // Получаем все покупки пользователя
    $allUserPurchases = PackagePurchase::where('user_id', $userId)->get();
    $userPurchases = $allUserPurchases->whereIn('payment_status', ['pending', 'paid'])->keyBy('service_package_id');

    $packageNames = [
        'basic' => 'Базовый',
        'standard' => 'Стандарт',
        'pro' => 'VIP',
    ];

    $packageDescriptions = [
        'basic' => 'Полный анализ результатов всех пройденных тестов с подробной интерпретацией',
        'standard' => 'Анализ тестов и часовая консультация с психологом',
        'pro' => 'Анализ тестов, часовая консультация с психологом, подбор вузов, направлений подготовки и предметов ЕГЭ для поступления',
    ];

@endphp

<div class="packages-section">
    <h3>Доступные пакеты</h3>

    <div class="grid">
        @foreach ($packages as $package)
            @php
                $purchase = $userPurchases[$package->id] ?? null;
                $discountedPrice = $package->getPriceForUser($userId);
                $hasDiscount = $discountedPrice < $package->price;
            @endphp

            <article style="display: flex; flex-direction:column; justify-content:space-between;">
                <h4>{{ $packageNames[$package->code] }}</h4>

                <div class="price">
                    @if ($purchase && $purchase->payment_status === 'paid')
                        <span style="font-weight: bold;">Приобретено</span>
                    @elseif($hasDiscount)
                        <span
                            style="text-decoration: line-through; color: #999;">{{ number_format($package->price, 0, ',', ' ') }}
                            ₽</span>
                        <span
                            style="font-weight: bold; margin-left: 10px;">{{ number_format($discountedPrice, 0, ',', ' ') }}
                            ₽</span>
                    @else
                        {{ number_format($package->price, 0, ',', ' ') }} ₽
                    @endif
                </div>

                <p>{{ $packageDescriptions[$package->code] }}</p>

                @if ($purchase)
                    @if ($purchase->payment_status === 'paid')
                        <button class="success" disabled>Приобретено</button>
                    @elseif($purchase->payment_status === 'pending')
                        <a href="{{ route('packages.payment', $package) }}" role="button">Ожидает</a>
                    @endif
                @elseif($canPurchase)
                    <a href="{{ route('packages.payment', $package) }}" role="button">Приобрести</a>
                @else
                    <button class="disabled" disabled>Недоступно</button>
                @endif
            </article>
        @endforeach
    </div>

    @if (!$hasEmail)
        <div class="alert alert-warning">
            Для покупки пакета необходимо указать email в профиле
        </div>
    @elseif(!$allTestsPassed)
        <div class="alert alert-info">
            Вы прошли {{ $passedTests }} из {{ $totalTests }} тестов. Для покупки пакета необходимо пройти все тесты.
            <a href="{{ route('tests.index') }}">Пройти тесты</a>
        </div>
    @endif
</div>
