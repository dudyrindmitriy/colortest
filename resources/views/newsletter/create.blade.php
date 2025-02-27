@extends('layouts.app') {{-- Предполагается наличие макета --}}

@section('content')
    <h1>Создать рассылку</h1>

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

    <form action="{{ route('newsletter.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="topic_id">Выберите тему:</label>
            <select name="topic_id" id="topic_id" required>
                @foreach ($newslettertopics as $newslettertopic)
                    <option value="{{ $newslettertopic->id }}">{{ $newslettertopic->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="text">Текст рассылки:</label>
            <textarea name="text" id="text" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
@endsection