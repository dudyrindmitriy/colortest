@extends('layouts.app')

@section('content')
<h2>Все отзывы</h2>
<form action="{{ route('reviews.search') }}" class="input-group" method="GET">
    <input type="text" name="query" placeholder="Поиск по отзывам..." required>
    <button type="submit">Поиск</button>
</form>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
<br>
<br>
<a href="{{ route('reviews.create') }}" class="nav-button">Добавить отзыв</a>
<br><br>
@foreach($reviews as $review)
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title"><strong>{{ $review->user->login }}</strong></h5>
        <p class="card-text">Рейтинг: {{ $review->rating }}</p>
        <p class="card-text">{{ $review->review_text }}</p>

        <div class="comments-section">
            <h4>Комментарии:</h4>
            @foreach($review->comments as $comment)
            <div class="comment mb-2">
                <strong>{{ $comment->user->login }}</strong>: {{ $comment->comment_text }}
            </div>
            @endforeach

            <form action="{{ route('comments.store', $review->id) }}" method="POST" class="comment-form">
                @csrf
                <div class="input-group">
                    <input type="text" name="comment_text" class="form-control" placeholder="Введите ваш комментарий..." required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection