@extends('layouts.app')

   @section('content')
   <div class="main-content">
       <h1>Панель администратора</h1>
       <a href="{{ route('admin.users.index') }}" class="nav-button">Управление пользователями</a><br><br><br>
       <a href="{{ route('admin.reviews.index') }}" class="nav-button">Управление отзывами</a>
       <a href="{{ route('admin.results.index') }}" class="nav-button">Управление результатами тестирований</a>
       @if (session('success'))
           <div class="alert alert-success">{{ session('success') }}</div>
       @endif
   </div>
   @endsection