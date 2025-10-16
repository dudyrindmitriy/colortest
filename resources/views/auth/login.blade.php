@extends('layouts.auth')

@section('content')
    <div class="main-content">
        <h1>Вход</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email:</label>
            <input required type="email" id="email" name="email">
            <label for="password">Пароль:</label>
            <input required type="password" id="password" name="password">
            <input type="submit" value="Войти">
        </form>
        <a href="{{ route('register') }}" role="button" class="secondary outline">Зарегестрироваться</a>
    </div>
@endsection
