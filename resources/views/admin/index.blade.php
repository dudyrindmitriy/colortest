@extends('layouts.app')

@section('content')
<h1>Панель администратора</h1>
<div>
    {{-- <a href="{{ route('admin.users.index') }}" class="nav-button">Управление пользователями</a> --}}
</div>
<br><br>
<div>
    {{-- <a href="{{ route('admin.reviews.index') }}" class="nav-button">Управление отзывами</a> --}}
</div>
<br><br>
<div>
    <a href="{{ route('admin.results.index') }}" class="nav-button">Управление результатами тестирований</a>
</div>

<!--
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif -->

@endsection
