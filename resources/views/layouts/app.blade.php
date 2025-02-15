
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <header class="site-header">
        
        <nav>
            <a href="/" class="nav-button">Главная</a>
            <a href="{{ route('profile') }}" class="nav-button">Личный профиль</a>
            <a href="{{ route('reviews') }}" class="nav-button">Отзывы</a>
            <a href="{{ route('test') }}" class="nav-button">Тестирование</a>
            <a href="{{ route('chat') }}" class="nav-button">Задать вопрос</a>

        </nav>
    </header>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>