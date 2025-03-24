<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Методичка</title>
    <!-- <link rel="stylesheet" href="{{ asset('css/styles.css') }}"> -->
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
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .image-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .description {
            margin-left: 20px;
            width: 60%;
        }
        img {
            max-width: 300px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .chess-image{
            max-width: 200px;
        }
    </style>
</head>

<body>
<div class="section">
        <h2>Типы ISA</h2>
        @foreach($isasWithImages as $index => $data)
        <div class="image-container">
            <img src="{{ $data['base64Png'] }}">
            <div class="description">
                <h3>Тип {{ $index + 1 }}</h3>
                <p>{{ $descriptions[$index] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="section">
        <h2>Шахматные структуры</h2>
        @foreach($chessWithImages as $data)
        <div class="image-container">
            <img class="chess-image" src="{{ $data['base64Png'] }}">
            <div class="description">
                <p>Степень выраженности: {{ $data['chess']->chess_structure }}</p>
            </div>
        </div>
        @endforeach
    </div>
</body>

</html>