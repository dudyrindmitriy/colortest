@extends('layouts.app')

@section('content')
<h1>Результат теста</h1>
<div class="svg">

    <div style=" text-align: center;">
        {!! $result->user_image; !!}
    </div>
</div>
<p>Дата прохождения: {{ $result->created_at }}</p>
<p>Индивидуальный стиль активности: {{$result->isa->individual_style_of_activity}}</p>
<p>Степень выраженности шахматной структуры: {{$result->chess_structure}}</p>
<p>Рекомендация:</p>

<p>{!!$result->recommendation!!}</p>
<a target="_blank" href="{{ route('result.downloadPDF', $result->id) }}" class="btn btn-primary">
    Сохранить результат в PDF
</a>
<br>
<a href="{{ route('results') }}">Вернуться к списку результатов</a>

@endsection