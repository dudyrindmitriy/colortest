@extends('layouts.app')

@section('content')
@if(session('success'))
<div style="color: green;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="color: red;">
    {{ session('error') }}
</div>
@endif
<h2>Профиль</h2>
<p>Логин: {{ $user->login }}</p>
<p>Email: {{ $user->email }}</p>
<p>Адрес: {{ $user->address }}</p>
<a href="{{ route('results') }}" class="nav-button">Просмотреть результаты тестирований</a>
<br>
<br>

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

<h1>Ваши подписки</h1>



<form action="{{ route('profile.updateSubscriptions') }}" method="POST">
    @csrf
    <div class="form-group">
        @foreach ($topics as $topic)
        <div class="form-check">
            <label >
            <input style="width: auto;" class="form-check-input" type="checkbox" name="topic_ids[]" value="{{ $topic->id }}" id="topic-{{ $topic->id }}"
            @if (in_array($topic->id, $subscribedTopics)) checked @endif>{{ $topic->name }}
            </label>
            

        </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="nav-button" style="background-color: red;">Выйти</button>
</form>
@endsection