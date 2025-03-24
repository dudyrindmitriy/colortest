<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты всех тестов</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        h1 { text-align: center; }
        .center { text-align: center; }
        .image { max-width: 100%; height: auto; }
        .result { page-break-inside: avoid; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Результаты всех тестов</h1>

    @foreach ($resultsWithImages as $resultWithImage)
        <div class="result">
            <h2>Результат теста #{{ $loop->iteration }}</h2>
            <div class="center">
                <img src="{{ $resultWithImage['base64Png'] }}" style="max-width: 50%; height: auto;">
                <p>Ваше изображение</p>
            </div>
            <p>Дата прохождения: {{ $resultWithImage['result']->created_at }}</p>
            <p>Индивидуальный стиль активности: {{ $resultWithImage['result']->isa->individual_style_of_activity }}</p>
            <p>Степень выраженности шахматной структуры: {{ $resultWithImage['result']->chess_structure }}</p>
            <p>Рекомендация:</p>
            <p>{!! $resultWithImage['result']->recommendation !!}</p>
        </div>
    @endforeach
</body>
</html>