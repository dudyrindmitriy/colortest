@extends('layouts.auth')

@section('content')
<div class="main-content">
    <h1>Вход</h1>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>
    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Войти">
</form>
<br>
<a href="{{route('register')}}">Зарегестрироваться</a>
</div>
@endsection