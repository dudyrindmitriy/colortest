@extends('layouts.app')

@section('content')

<div class="container">
    <h1>Общий форум</h1>

    @auth
    <form action="{{ route('forum.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <textarea name="content" class="form-control" rows="3" placeholder="Ваше сообщение..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
    @endauth

    @foreach($messages as $message)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">
                {{ $message->user->login}}
            </h5>
            <p class="card-text">{{ $message->content }}</p>

            <div class="d-flex align-items-center mb-2">


                @auth
                <div class="reaction-buttons">
                    <form action="{{ route('forum.react') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="message_id" value="{{ $message->id }}">
                        <button type="submit" name="type" value="like" class="reaction-btn" title="Like"> {{ $message->likesCount() }}❤️</button>
                        <button type="submit" name="type" value="dislike" class="reaction-btn" title="Dislike">{{ $message->dislikesCount() }}👎</button>
                    </form>
                </div>
                @endauth
            </div>
            @foreach($message->replies as $reply)
            <div class="card mt-2">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        {{ $reply->user->login}} ({{ $reply->created_at->diffForHumans() }})
                    </h6>
                    <p>{{ $reply->content }}</p>
                </div>
            </div>
            @endforeach

            @auth
            <form action="{{ route('forum.store') }}" method="POST" class="mt-2">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $message->id }}">
                <div class="input-group">
                    <input type="text" name="content" class="form-control" placeholder="Ответить...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">➤</button>
                    </div>
                </div>
            </form>
            @endauth
        </div>
    </div>
    @endforeach

    {{ $messages->links() }}
</div>
@endsection