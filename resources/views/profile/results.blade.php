@extends('layouts.app')

@section('content')
    <h1>Результаты тестирований</h1>
    <form action="{{ route('results.search') }}" class="input-group" method="GET">
    <select name="field" required>
        <option value="">Выберите поле</option>
        <option value="isa">Стиль активности</option>
        <option value="chess_structure">Шахматная структура</option>
        <option value="created_at">Дата</option>
        <option value="recommendation">Рекомендации</option>
    </select>
    
    <input type="text" name="query" placeholder="Поиск..." required>
    <button type="submit">Поиск</button>
</form>
    @if ($results->count() > 0)
        <ul>
            @foreach ($results as $result)
                <li>
                    <a href="{{ route('result', $result->id) }}">
                        <p>Дата: {{ $result->created_at }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>У вас пока нет результатов тестирований.</p>
    @endif
    <a target="_blank" href="{{ route('results.downloadPDF') }}" class="btn btn-primary">
    Сохранить результаты в PDF
</a>
<br>
<br>
<br>

    <a href="{{ route('profile') }}" class="nav-button">Вернуться в профиль</a>
@endsection