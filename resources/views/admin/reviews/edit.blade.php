@extends('layouts.app')

@section('content')
<div class="main-content">
    <h2>Редактировать отзыв</h2>
    <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="review_text">Отзыв:</label>
            <textarea name="review_text" id="review_text" class="form-control" required>{{ old('review_text', $review->review_text) }}</textarea>
        </div>
        <div class="form-group">
            <label for="rating">Рейтинг:</label>
            <select name="rating" id="rating" class="form-control" required>
                <option value="">Выберите рейтинг</option>
                <option value="1" @if($review->rating == 1) selected @endif>1</option>
                <option value="2" @if($review->rating == 2) selected @endif>2</option>
                <option value="3" @if($review->rating == 3) selected @endif>3</option>
                <option value="4" @if($review->rating == 4) selected @endif>4</option>
                <option value="5" @if($review->rating == 5) selected @endif>5</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Обновить отзыв</button>
    </form>
</div>
@endsection