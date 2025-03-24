<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат теста</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        h1 {
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<h1>Результат теста</h1>
<div style=" text-align: center;">
    <img src="{!! $base64Png !!}" style="max-width: 50%; height: auto;">

    <p>Ваше изображение</p>
</div>
<p>Дата прохождения: {{ $result->created_at }}</p>
<p>Индивидуальный стиль активности: {{$result->isa->individual_style_of_activity}}</p>
<p>Степень выраженности шахматной структуры: {{$result->chess_structure}}</p>
<p>Рекомендация:</p>

<p>{!!$result->recommendation!!}</p>