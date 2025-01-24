@extends('layouts.auth')

@section('content')
<div class="main-content">
    <h1>Регистрация</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label for="name">Логин:</label>
        <input type="text" id="name" name="login"><br><br>
        <label for="name">Email:</label>
        <input type="email" id="name" name="email"><br><br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password"><br><br>
        <label for="address">Адрес:</label>
        <input type="text" id="address" name="address"><br><br>
        <input type="submit" value="Зарегистрироваться">
    </form>
    <br>
    <a href="{{route('login')}}">Войти</a>
</div>
@endsection