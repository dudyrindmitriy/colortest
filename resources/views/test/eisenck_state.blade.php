{{-- resources/views/tests/eysenck.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>{{ $test->name ?? 'Тест "Самооценка психических состояний" (по Айзенку)' }}</h1>

    <article class="instruction">
        <strong>Инструкция:</strong> Вам предлагается описание различных психических состояний. Оцените, насколько каждое состояние Вам свойственно

    </article>

    <form id="eysenckTestForm" method="POST" action="{{ route('tests.save', $testId) }}">
        @csrf

        {{-- Заголовки групп вопросов --}}
        <article class="text-center">I. Тревожность</article>

        <div class="questions-grid">
            @php
                $questions = [
                    1 => 'Не чувствую в себе уверенности',
                    2 => 'Часто из-за пустяков краснею',
                    3 => 'Мой сон беспокоен',
                    4 => 'Легко впадаю в уныние',
                    5 => 'Беспокоюсь о только воображаемых еще неприятностях',
                    6 => 'Меня пугают трудности',
                    7 => 'Люблю копаться в своих недостатках',
                    8 => 'Меня легко убедить',
                    9 => 'Я мнительный',
                    10 => 'Я с трудом переношу время ожидания',
                ];
            @endphp

            @for ($i = 1; $i <= 10; $i++)
                <article>
                    <div class="question-header">
                        <span class="question-number">{{ $i }}</span>
                        <div class="question-text">{{ $questions[$i] }}</div>
                    </div>
                    <hr>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="2">
                            <span class="option-text">часто</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="1">
                            <span class="option-text">изредка</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="0">
                            <span class="option-text">не подходит</span>
                        </label>
                    </div>
                </article>
            @endfor
        </div>

        <article class="text-center">II. Фрустрация</article>

        <div class="questions-grid">
            @php
                $questions = [
                    11 => 'Нередко мне кажутся безвыходными положения, из которых всё-таки можно найти выход',
                    12 => 'Неприятности меня сильно расстраивают, я падаю духом',
                    13 => 'При больших неприятностях я склонен без достаточных оснований винить себя',
                    14 => 'Несчастья и неудачи ничему меня не учат',
                    15 => 'Я часто отказываюсь от борьбы, считая ее бесплодной',
                    16 => 'Я нередко чувствую себя беззащитным',
                    17 => 'Иногда у меня бывает состояние отчаяния',
                    18 => 'Я чувствую растерянность перед трудностями',
                    19 => 'В трудные минуты жизни иногда веду себя по-детски, хочу, чтобы пожалели',
                    20 => 'Считаю недостатки своего характера неисправимыми',
                ];
            @endphp

            @for ($i = 11; $i <= 20; $i++)
                <article>
                    <div class="question-header">
                        <span class="question-number">{{ $i }}</span>
                        <div class="question-text">{{ $questions[$i] }}</div>
                    </div>
                    <hr>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="2">
                            <span class="option-text">часто</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="1">
                            <span class="option-text">изредка</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="0">
                            <span class="option-text">не подходит</span>
                        </label>
                    </div>
                </article>
            @endfor
        </div>

        <article class="text-center">III. Агрессивность</article>

        <div class="questions-grid">
            @php
                $questions = [
                    21 => 'Оставляю за собой последнее слово',
                    22 => 'Нередко в разговоре перебиваю собеседника',
                    23 => 'Меня легко рассердить',
                    24 => 'Люблю делать замечания другим',
                    25 => 'Хочу быть авторитетом для других',
                    26 => 'Не довольствуюсь малым, хочу наибольшего',
                    27 => 'Когда разгневаюсь, плохо себя сдерживаю',
                    28 => 'Предпочитаю лучше руководить, чем подчиняться',
                    29 => 'У меня резкая, грубоватая жестикуляция',
                    30 => 'Я мстителен',
                ];
            @endphp

            @for ($i = 21; $i <= 30; $i++)
                <article>
                    <div class="question-header">
                        <span class="question-number">{{ $i }}</span>
                        <div class="question-text">{{ $questions[$i] }}</div>
                    </div>
                    <hr>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="2">
                            <span class="option-text">часто</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="1">
                            <span class="option-text">изредка</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="0">
                            <span class="option-text">не подходит</span>
                        </label>
                    </div>
                </article>
            @endfor
        </div>

        <article class="text-center">IV. Ригидность</article>

        <div class="questions-grid">
            @php
                $questions = [
                    31 => 'Мне трудно менять привычки',
                    32 => 'Нелегко переключать внимание',
                    33 => 'Очень настороженно отношусь ко всему новому',
                    34 => 'Меня трудно переубедить',
                    35 => 'Нередко у меня не выходит из головы мысль, от которой следовало бы освободиться',
                    36 => 'Нелегко сближаюсь с людьми',
                    37 => 'Меня расстраивают даже незначительные нарушения плана',
                    38 => 'Нередко я проявляю упрямство',
                    39 => 'Неохотно иду на риск',
                    40 => 'Резко переживаю отклонения от принятого мною режима дня',
                ];
            @endphp

            @for ($i = 31; $i <= 40; $i++)
                <article>
                    <div class="question-header">
                        <span class="question-number">{{ $i }}</span>
                        <div class="question-text">{{ $questions[$i] }}</div>
                    </div>
                    <hr>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="2">
                            <span class="option-text">часто</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="1">
                            <span class="option-text">изредка</span>
                        </label>
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="0">
                            <span class="option-text">не подходит</span>
                        </label>
                    </div>
                </article>
            @endfor
        </div>

        <div class="test-actions">
            <button type="submit" id="saveResult" data-route="{{ route('tests.save', $testId) }}"
                data-redirect="{{ route('profile') }}">Сохранить результаты</button>
            <button type="button" id="clearForm" class="secondary">Очистить форму</button>
        </div>
    </form>

@endsection
