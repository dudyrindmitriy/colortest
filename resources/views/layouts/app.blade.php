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
<dialog id="alertModal">
    <article>
        <header>
            <button aria-label="Close" rel="prev" onclick="closeModal(this.closest('dialog'))"></button>
            <p id="alertTitle">Уведомление</p>
        </header>
        <div id="alertContent">
            <p id="alertMessage"></p>
        </div>

    </article>
</dialog>
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
    <header>
        <nav class="container">
            <div role="group">
                <a href="{{ route('profile') }}" role="button"><svg xmlns="http://www.w3.org/2000/svg" height="48px"
                        viewBox="0 -960 960 960" width="48px" fill="#EFEFEF">
                        <path
                            d="M222-255q63-44 125-67.5T480-346q71 0 133.5 23.5T739-255q44-54 62.5-109T820-480q0-145-97.5-242.5T480-820q-145 0-242.5 97.5T140-480q0 61 19 116t63 109Zm257.81-195q-57.81 0-97.31-39.69-39.5-39.68-39.5-97.5 0-57.81 39.69-97.31 39.68-39.5 97.5-39.5 57.81 0 97.31 39.69 39.5 39.68 39.5 97.5 0 57.81-39.69 97.31-39.68 39.5-97.5 39.5Zm.66 370Q398-80 325-111.5t-127.5-86q-54.5-54.5-86-127.27Q80-397.53 80-480.27 80-563 111.5-635.5q31.5-72.5 86-127t127.27-86q72.76-31.5 155.5-31.5 82.73 0 155.23 31.5 72.5 31.5 127 86t86 127.03q31.5 72.53 31.5 155T848.5-325q-31.5 73-86 127.5t-127.03 86Q562.94-80 480.47-80Zm-.47-60q55 0 107.5-16T691-212q-51-36-104-55t-107-19q-54 0-107 19t-104 55q51 40 103.5 56T480-140Zm0-370q34 0 55.5-21.5T557-587q0-34-21.5-55.5T480-664q-34 0-55.5 21.5T403-587q0 34 21.5 55.5T480-510Zm0-77Zm0 374Z" />
                    </svg></a>
                <a href="{{ route('home') }}" role="button"><svg xmlns="http://www.w3.org/2000/svg" height="48px"
                        viewBox="0 -960 960 960" width="48px" fill="#EFEFEF">
                        <path
                            d="M220-180h150v-250h220v250h150v-390L480-765 220-570v390Zm-60 60v-480l320-240 320 240v480H530v-250H430v250H160Zm320-353Z" />
                    </svg></a>
                <a href="{{ route('test') }}" role="button"><svg xmlns="http://www.w3.org/2000/svg" height="48px"
                        viewBox="0 -960 960 960" width="48px" fill="#EFEFEF">
                        <path
                            d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-85 32-158t87.5-127q55.5-54 130-84.5T489-880q79 0 150 26.5T763.5-780q53.5 47 85 111.5T880-527q0 108-63 170.5T650-294h-75q-18 0-31 14t-13 31q0 27 14.5 46t14.5 44q0 38-21 58.5T480-80Zm0-400Zm-233 26q20 0 35-15t15-35q0-20-15-35t-35-15q-20 0-35 15t-15 35q0 20 15 35t35 15Zm126-170q20 0 35-15t15-35q0-20-15-35t-35-15q-20 0-35 15t-15 35q0 20 15 35t35 15Zm214 0q20 0 35-15t15-35q0-20-15-35t-35-15q-20 0-35 15t-15 35q0 20 15 35t35 15Zm131 170q20 0 35-15t15-35q0-20-15-35t-35-15q-20 0-35 15t-15 35q0 20 15 35t35 15ZM480-140q11 0 15.5-4.5T500-159q0-14-14.5-26T471-238q0-46 30-81t76-35h73q76 0 123-44.5T820-527q0-132-100-212.5T489-820q-146 0-247.5 98.5T140-480q0 141 99.5 240.5T480-140Z" />
                    </svg></a>
            </div>
        </nav>
    </header>
    <main class="container">
        <article >
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
