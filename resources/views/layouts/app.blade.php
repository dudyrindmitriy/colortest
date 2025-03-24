<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <header class="site-header">

        <nav>
            <a href="{{ route('home') }}" class="nav-button">Главная</a>
            <a href="{{ route('profile') }}" class="nav-button">Личный профиль</a>
            <a href="{{ route('reviews') }}" class="nav-button">Отзывы</a>
            <a href="{{ route('test') }}" class="nav-button">Тестирование</a>

        </nav>
    </header>
    <div class="container">
        <div class="main-content">
            @yield('content')
        </div>
    </div>
    <div id="chart_div"></div>

</body>

</html>