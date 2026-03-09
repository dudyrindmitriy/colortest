<!DOCTYPE html>
<html>

<head>
    <title>Отчет по результатам тестирования</title>

</head>

<body>
    <div class="header">
        <h1 style="font-size:14pt">Отчет по результатам профессиональной диагностики</h1>
        <p>Пользователь: {{ $analysis['user']['login'] }}</p>
        <p>Email: {{ $analysis['user']['email'] }}</p>
        <p>Дата отчета: {{ $analysis['generated_at'] }}</p>
    </div>

    @foreach ($analysis['tests'] as $index => $test)
        <div class="test-card">
            <div class="test-header">
                <span class="test-number">{{ $index + 1 }}</span>
                <div class="test-title">
                    <strong>{{ $test['name'] }}</strong>
                    <div class="test-date">{{ $test['date'] }}</div>
                </div>
            </div>
            <div class="test-content">
                @php
                    $viewName = 'analysis.' . $test['code'];
                @endphp

                @if (view()->exists($viewName))
                    <div class="{{ $test['code'] }}-content">
                        @include($viewName, ['test' => $test])
                    </div>
                @else
                    <p>Результаты теста</p>
                @endif
            </div>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach


    <div class="footer">
        Сгенерировано автоматически {{ $analysis['generated_at'] }}
    </div>
</body>

</html>
