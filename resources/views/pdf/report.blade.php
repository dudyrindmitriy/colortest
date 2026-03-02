<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Отчет по результатам тестирования</title>
    <style>
        body {
            /* font-family: DejaVu Sans, sans-serif; */
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }

        .header {
            text-align: center;
        }

        /* Карточка теста */
        .test-card {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        /* Заголовок теста */
        .test-header {
            background-color: #f0f0f0;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }

        .test-number {
            display: flex;
            width: 25px;
            height: 25px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            text-align: center;
            margin-right: 15px;
            font-weight: bold;

            align-items: center;
            justify-content: center;

        }

        .test-title {
            flex: 1;
        }

        .test-title strong {
            font-size: 14px;
        }

        .test-date {
            color: #666;
            font-size: 11px;
        }

        /* Контент теста */
        .test-content {
            padding: 15px;
            page-break-inside: auto;
        }

        /* Для длинного контента Holland */
        .holland-content {
            page-break-inside: auto;
        }

        /* Стили для имитации таблицы внутри Holland (если нужно) */
        .mini-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            page-break-inside: avoid;
        }

        .mini-table td,
        .mini-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin-top: 0">Отчет по результатам профессиональной диагностики</h1>
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
