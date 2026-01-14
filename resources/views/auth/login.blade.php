@extends('layouts.auth')

@section('content')
    <div class="main-content">
        <h1>Вход</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="login">Логин</label>
            <input required type="text" id="login" name="login">
            <label for="password">Пароль</label>
            <input required type="password" id="password" name="password">
            <input type="submit" value="Войти">
        </form>
        <a href="{{ route('register') }}" role="button" class="secondary outline">Зарегистрироваться</a>
    </div>
@endsection
