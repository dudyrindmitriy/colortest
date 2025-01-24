@extends('layouts.app')

@section('content')
<div class="main-content" >
    <h1>Результат теста "{{ $result->id }}"</h1>
    <div style=" text-align: center;">
        <?= $result->user_image; ?>
        <p>Ваше изображение</p>
    </div>
    <p>Дата прохождения: {{ $result->created_at }}</p>
    <p>Индивидуальный стиль активности: {{$result->isa->individual_style_of_activity}}</p>
    <p>Степень выраженности шахматной структуры: {{$result->chess_structure}}</p>
    <p>Рекомендация:</p>
    
    <p>{!!$result->recommendation!!}</p>

    <a href="{{ route('results') }}">Вернуться к списку результатов</a>
</div>
@endsection