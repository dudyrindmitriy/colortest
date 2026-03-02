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

    // Получаем все покупки пользователя
    $allUserPurchases = PackagePurchase::where('user_id', Auth::id())->get();
    $userPurchases = $allUserPurchases->whereIn('payment_status', ['pending', 'paid'])->keyBy('service_package_id');

    // Проверяем наличие оплаченных пакетов
    $hasBasic = PackagePurchase::hasPackage(Auth::id(), 'basic');
    $hasStandard = PackagePurchase::hasPackage(Auth::id(), 'standard');
    $hasPro = PackagePurchase::hasPackage(Auth::id(), 'pro');

    $packageNames = [
        'basic' => 'Базовый',
        'standard' => 'Стандарт',
        'pro' => 'Расширенный',
    ];

    $packageDescriptions = [
        'basic' => 'Полный анализ результатов всех пройденных тестов с подробной интерпретацией',
        'standard' => 'Анализ тестов и часовая консультация с психологом',
        'pro' => 'Углубленный анализ и расширенная консультация',
    ];

    // Функция для расчета цены со скидкой
    function getDiscountedPrice($package, $hasBasic, $hasStandard, $hasPro) {
        $price = $package->price;

        if ($hasPro) {
            return 0; // Pro дает все пакеты бесплатно
        }

        if ($hasStandard) {
            if ($package->code == 'basic') {
                return 0; // Basic бесплатен при Standard
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

    // Проверяем, должен ли пакет быть автоматически доступен
    function isAutoAvailable($packageCode, $hasBasic, $hasStandard, $hasPro) {
        if ($hasPro) return true;
        if ($hasStandard && $packageCode == 'basic') return true;
        return false;
    }
@endphp

<div class="packages-section">
    <h3>Доступные пакеты</h3>

    <div class="grid">
        @foreach ($packages as $package)
            @php
                $purchase = $userPurchases[$package->id] ?? null;
                $autoAvailable = isAutoAvailable($package->code, $hasBasic, $hasStandard, $hasPro);
                $discountedPrice = getDiscountedPrice($package, $hasBasic, $hasStandard, $hasPro);
                $hasDiscount = $discountedPrice < $package->price;
            @endphp

            <article style="display: flex; flex-direction:column; justify-content:space-between;">
                <h4>{{ $packageNames[$package->code] }}</h4>

                {{-- ВЫВОД СКИДОК --}}
                <div class="price">
                    @if($purchase && $purchase->payment_status === 'paid')
                        <span style=" font-weight: bold;">Приобретено</span>
                    @elseif($autoAvailable)
                        <span style="text-decoration: line-through; color: #999;">{{ number_format($package->price, 0, ',', ' ') }} ₽</span>
                        <span style=" font-weight: bold; margin-left: 10px;">Бесплатно</span>
                    @elseif($hasDiscount)
                        <span style="text-decoration: line-through; color: #999;">{{ number_format($package->price, 0, ',', ' ') }} ₽</span>
                        <span style=" font-weight: bold; margin-left: 10px;">{{ number_format($discountedPrice, 0, ',', ' ') }} ₽</span>
                    @else
                        {{ number_format($package->price, 0, ',', ' ') }} ₽
                    @endif
                </div>

                <p>{{ $packageDescriptions[$package->code] }}</p>

                @if($purchase)
                    @if($purchase->payment_status === 'paid')
                        <button class="success" disabled>Приобретено</button>
                    @elseif($purchase->payment_status === 'pending')
                        <a href="{{ route('packages.payment', $purchase) }}" role="button">Ожидает</a>
                    @endif
                @elseif($canPurchase)
                    <a href="{{ route('packages.purchase', $package) }}" role="button">Приобрести</a>
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
