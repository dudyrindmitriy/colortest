@extends('layouts.app')

@section('content')
<h2>Профиль</h2>
<p>Логин: {{ $user->login }}</p>
<p>Email: {{ $user->email }}</p>
<p>Адрес: {{ $user->address }}</p>
<a href="{{ route('results') }}" class="nav-button">Просмотреть результаты тестирований</a>
<br>
<br>
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<form method="POST" action="{{ route('profile.sendMessage') }}">
    @csrf
    <div>
        <label for="message">Ваш вопрос:</label>
        <textarea id="message" name="message" rows="4" required></textarea>
    </div>
    <br>
    <button type="submit" class="nav-button">Отправить</button>
</form>
<br>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="nav-button" style="background-color: red;">Выйти</button>
</form>
@endsection