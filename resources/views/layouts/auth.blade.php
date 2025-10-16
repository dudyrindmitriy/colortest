<!DOCTYPE html>
<html lang="ru" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColorTest</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/pico.js') }}"></script>
    <script>
        const availableThemes = [
            'red', 'pink', 'fuchsia', 'purple', 'violet',
            'indigo', 'blue', 'cyan', 'jade', 'green',
            'orange'
        ];

        const randomTheme = availableThemes[Math.floor(Math.random() * availableThemes.length)];
        document.write(
            `<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.${randomTheme}.min.css">`
        );
    </script>
</head>
<div class="background-pattern"></div>
<div class="background-gradient"></div>
 @if ($errors->any())
                <dialog open>
                    <article>
                        <header>
                            <button aria-label="Close" rel="prev" onclick="closeModal(this.closest('dialog'))"></button>
                            <p>Ошибка</p>
                        </header>
                        <div role="alert" class="error">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                </dialog>
            @endif
            @if (session('error'))
                <dialog open>
                    <article>
                        <header>
                            <button aria-label="Close" rel="prev" onclick="closeModal(this.closest('dialog'))"></button>
                            <p>Ошибка</p>

                        </header>
                        <div role="alert" class="error">
                            {{ session('error') }}
                        </div>
                    </article>
                </dialog>
            @endif
<body>

    <main class="container">
        <article>
            @yield('content')
        </article>
    </main>
    <button id="themeToggler"><svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
            width="48px" fill="#EFEFEF">
            <path
                d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q8 0 17 .5t23 1.5q-36 32-56 79t-20 99q0 90 63 153t153 63q52 0 99-18.5t79-51.5q1 12 1.5 19.5t.5 14.5q0 150-105 255T480-120Zm0-60q109 0 190-67.5T771-406q-25 11-53.67 16.5Q688.67-384 660-384q-114.69 0-195.34-80.66Q384-545.31 384-660q0-24 5-51.5t18-62.5q-98 27-162.5 109.5T180-480q0 125 87.5 212.5T480-180Zm-4-297Z" />
        </svg></button>
</body>

</html>
