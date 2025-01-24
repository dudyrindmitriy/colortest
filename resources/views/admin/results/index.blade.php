@extends('layouts.app')

@section('content')
<div class="main-content" style="max-width: 1000px;">
    <h2>Управление отзывами</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <style>
        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
    </style>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Результат</th>
                <th>ISA</th>
                <th>CHESS</th>
                <th>Рекомендации</th>
                <th>Редактирование</th>
                <th>Удаление</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr>
                <td>{{ $result->id }}</td>
                <td>{{ $result->user->login }}</td>
                <td>{!! $result->user_image !!}</td>
                <td>{{ $result->isa->individual_style_of_activity }}</td>
                <td>{{ $result->chess_structure }}</td>
                <td>{!! $result->recommendation !!}</td>

                <td>
                    <a href="{{ route('admin.results.edit', $result) }}" class="nav-button">Редактировать</a>
                </td>
                <td>
                    <form action="{{ route('admin.results.destroy', $result) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button_red">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection