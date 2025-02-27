@extends('layouts.app')

@section('content')
<h1>Сообщение пользователю {{ $user->login }}</h1>
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
<form action="{{ route('admin.users.message', $user) }}" method="POST">
    @csrf
    <textarea name="message" required placeholder="Введите ваше сообщение"></textarea>
    <button type="submit">Отправить</button>
</form>

@endsection