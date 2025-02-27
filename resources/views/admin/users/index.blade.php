@extends('layouts.app')

@section('content')

<style>
         .table {
    border-collapse: collapse; 
    width: 100%; 
}

.table th, .table td {
    border: 1px solid #000;
    padding: 8px;
    text-align: left;
}
      
    </style>
    <h2>Управление пользователями</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Логин</th>
                <th>Email</th>
                <th>Написать</th>
                <th>Редактирование</th>
                <th>Удаление</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->login }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('admin.users.message', $user) }}" class="nav-button">Написать</a>
                </td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="nav-button">Редактировать</a>
                </td>
                <td>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button_red">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@endsection