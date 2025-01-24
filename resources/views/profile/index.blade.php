@extends('layouts.app')

@section('content')

<div class="profile-container">
    <p>Логин: {{ $user->login }}</p>
    <p>Email: {{ $user->email }}</p>
    <p>Адрес: {{ $user->address }}</p>
    <a href="{{ route('results') }}" class="nav-button">Просмотреть результаты тестирований</a>
    <br>
    <br>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-button" style="background-color: red;">Выйти</button>
    </form>
</div>
@endsection