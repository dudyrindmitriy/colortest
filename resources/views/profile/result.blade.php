@extends('layouts.app')

@section('content')
<h1>Результат тестирования</h1>
    <div class="test-grid grid">

        <div class="svg">

            <div style=" text-align: center;">
                {!! $result->user_image !!}
            </div>
        </div>
        <div>
            <p>Дата прохождения: {{ $result->created_at }}</p>
            <p>Рекомендация:</p>
        </div>
    </div>
        <a href="{{ route('profile') }}">Профиль</a>
@endsection
