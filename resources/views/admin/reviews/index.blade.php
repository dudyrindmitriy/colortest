@extends('layouts.app')

@section('content')
<div class="main-content">
    <h2>Управление отзывами</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Отзыв</th>
                <th>Рейтинг</th>
                <th>Редактирование</th>
                <th>Удаление</th>

            </tr>
        </thead>
        <tbody>
            @foreach($reviews as $review)
            <tr>
                <td>{{ $review->id }}</td>
                <td>{{ $review->user->login }}</td>
                <td>{{ $review->review_text }}</td>
                <td>{{ $review->rating }}</td>
                <td>
                    <a href="{{ route('admin.reviews.edit', $review) }}" class="nav-button">Редактировать</a></td> <td>
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display:inline;">
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