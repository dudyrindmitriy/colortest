@extends('layouts.app')

@section('content')
    <h1>Результат тестирования</h1>
    <p><strong>Стиль активности:</strong> {{ $result->industry }}</p>
    <p><strong>Рекомендации:</strong></p>
    <p>{{ $result->recommendation }}</p>
@endsection