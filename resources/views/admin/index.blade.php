@extends('layouts.app')

@section('content')
<h1>Панель администратора</h1>
<div>
    <a href="{{ route('admin.users.index') }}" class="nav-button">Управление пользователями</a>
</div>
<br><br>
<div>
    <a href="{{ route('admin.reviews.index') }}" class="nav-button">Управление отзывами</a>
</div>
<br><br>
<div>
    <a href="{{ route('admin.results.index') }}" class="nav-button">Управление результатами тестирований</a>
</div>
<br><br>
@if (count($messages)!=0 and isset($messages))
<h3>У вас есть непрочитанные сообщения</h3>
@foreach ($messages as $message)
<div class="message" data-id="{{ $message->id }}">
    <div>{{ $message->user->login }}</div>
    <div>{{ $message->message }}</div>
    <div><button class="reply-button">Ответить</button></div>
</div>
@endforeach
@endif
<div id="reply-area" style="display: none;">
    <textarea id="reply-message" placeholder="Введите ваше сообщение"></textarea>
    <button id="send-reply-button">Отправить ответ</button>
</div>

@if (!empty($topicStats))
<h1>Статистика рассылок по темам</h1>
<ul>
    @foreach ($topicStats as $topicId => $topicData)
    <li>
        Тема: {{ $topicData['name'] }} ({{ $topicData['message_count'] }} сообщений)
    </li>
    @endforeach
</ul>
@endif
<a href="{{ route('newsletter.create') }}">Создать рассылку</a>
<!-- 
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif -->
<script>
    let currentMessageId = null;
    const replyArea = document.getElementById('reply-area');
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            currentMessageId = this.closest('.message').dataset.id;
            replyArea.style.display = 'block';
        });
    });

    document.getElementById('send-reply-button').addEventListener('click', function() {
        const message = document.getElementById('reply-message').value;
        console.log(message);

        // Отправка ответа
        fetch('admin/users/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: currentMessageId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ваш ответ отправлен');
                    console.log(data);
                    document.querySelector(`.message[data-id="${currentMessageId}"]`).remove();
                    replyArea.style.display = 'none';
                }
            })
            .catch(error => console.error('Ошибка:', error));
    });
</script>
@endsection