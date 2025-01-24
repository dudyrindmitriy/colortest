@extends('layouts.app')

@section('content')
<div class="main-content">
    <h2>Редактировать пользователя</h2>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" name="login" id="login" class="form-control" value="{{ old('name', $user->login) }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Обновить пользователя</button>
    </form>
</div>
@endsection