@extends('layouts.app')

@section('content')
    <div class="main-content">
        <h2>Добавить новый отзыв</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="rating">Рейтинг:</label>
                <select name="rating" id="rating" class="form-control" required>
                    <option value="">Выберите рейтинг</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group">
                <label for="review_text">Отзыв:</label>
                <textarea name="review_text" id="review_text" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Добавить отзыв</button>
        </form>
    </div>
@endsection