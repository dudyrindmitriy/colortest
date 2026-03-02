{{-- resources/views/tests/holland.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>{{ $test->name ?? 'Методика Дж. Холланда' }}</h1>

    <article class="instruction">
       <strong>Инструкция:</strong> Вам предлагается 43 пары профессий, причем каждой паре Вы обязаны выбрать одну:
            наиболее желательную или наименее «противную». Выбранную Вами профессию отметьте галочкой.
    </article>

    <form id="hollandTestForm" method="POST" action="{{ route('tests.save', $testId) }}">
        @csrf

        <div class="questions-grid">
            @for ($i = 1; $i <= 43; $i++)
                <article>
                    <div class="question-header">
                        <span class="question-number">{{ $i }}</span>
                    </div>
<hr>
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="A">
                            <span class="option-text">
                                @if ($i == 1)
                                    Автомеханик
                                @elseif($i == 2)
                                    Егерь
                                @elseif($i == 3)
                                    Кондитер
                                @elseif($i == 4)
                                    Пасечник
                                @elseif($i == 5)
                                    Радиооператор
                                @elseif($i == 6)
                                    Астроном
                                @elseif($i == 7)
                                    Бактериолог
                                @elseif($i == 8)
                                    Зоолог
                                @elseif($i == 9)
                                    Минеролог
                                @elseif($i == 10)
                                    Гувернантка
                                @elseif($i == 11)
                                    Священник
                                @elseif($i == 12)
                                    Консультант по профориентации
                                @elseif($i == 13)
                                    Финансовый контролер
                                @elseif($i == 14)
                                    Шифровальщик
                                @elseif($i == 15)
                                    Директор магазина
                                @elseif($i == 16)
                                    Горный инженер
                                @elseif($i == 17)
                                    Животновод
                                @elseif($i == 18)
                                    Маляр каталогов
                                @elseif($i == 19)
                                    Охотовед рынка
                                @elseif($i == 20)
                                    Электротехник
                                @elseif($i == 21)
                                    Биолог
                                @elseif($i == 22)
                                    Вирусолог
                                @elseif($i == 23)
                                    Генетик
                                @elseif($i == 24)
                                    Гидробиолог
                                @elseif($i == 25)
                                    Воспитатель детского сада
                                @elseif($i == 26)
                                    Инструктор по плаванию
                                @elseif($i == 27)
                                    Медицинская сестра
                                @elseif($i == 28)
                                    Наборщик типографии
                                @elseif($i == 29)
                                    Переписчик нот
                                @elseif($i == 30)
                                    Начальник стройки
                                @elseif($i == 31)
                                    Машинист тепловоза
                                @elseif($i == 32)
                                    Портной
                                @elseif($i == 33)
                                    Рулевой-моторист
                                @elseif($i == 34)
                                    Штукатур
                                @elseif($i == 35)
                                    Садовник
                                @elseif($i == 36)
                                    Редактор научного журнала
                                @elseif($i == 37)
                                    Физик-теоретик
                                @elseif($i == 38)
                                    Ихтиолог
                                @elseif($i == 39)
                                    Ученый-теоретик
                                @elseif($i == 40)
                                    Преподаватель иностранного языка
                                @elseif($i == 41)
                                    Тренер по лечебной физкультуре
                                @elseif($i == 42)
                                    Социальный работник
                                @elseif($i == 43)
                                    Продюсер телевидения
                                @endif
                            </span>
                        </label>

                        <label class="option">
                            <input type="radio" name="{{ $i }}" value="B">
                            <span class="option-text">
                                @if ($i == 1)
                                    Авиаконструктор
                                @elseif($i == 2)
                                    Интервьюер
                                @elseif($i == 3)
                                    Делопроизводитель
                                @elseif($i == 4)
                                    Администратор
                                @elseif($i == 5)
                                    Актер
                                @elseif($i == 6)
                                    Гид-экскурсовод
                                @elseif($i == 7)
                                    Корректор текстов
                                @elseif($i == 8)
                                    Брокер
                                @elseif($i == 9)
                                    Актер цирка
                                @elseif($i == 10)
                                    Работник архива
                                @elseif($i == 11)
                                    Глава администрации
                                @elseif($i == 12)
                                    Драматург
                                @elseif($i == 13)
                                    Директор
                                @elseif($i == 14)
                                    Искусствовед
                                @elseif($i == 15)
                                    Композитор
                                @elseif($i == 16)
                                    Биофизик
                                @elseif($i == 17)
                                    Репетитор
                                @elseif($i == 18)
                                    Составитель
                                @elseif($i == 19)
                                    Директор
                                @elseif($i == 20)
                                    Карикатурист
                                @elseif($i == 21)
                                    Семейный врач
                                @elseif($i == 22)
                                    Контролер-кассир
                                @elseif($i == 23)
                                    Менеджер
                                @elseif($i == 24)
                                    Писатель
                                @elseif($i == 25)
                                    Чертежник
                                @elseif($i == 26)
                                    Начальник отдела сбыта
                                @elseif($i == 27)
                                    Манекенщица
                                @elseif($i == 28)
                                    Оптовый торговец
                                @elseif($i == 29)
                                    Музыкальный аранжировщик
                                @elseif($i == 30)
                                    Музыкант-исполнитель
                                @elseif($i == 31)
                                    Инженер-исследователь
                                @elseif($i == 32)
                                    Консультант службы знакомств
                                @elseif($i == 33)
                                    Регистратор
                                @elseif($i == 34)
                                    Предприниматель
                                @elseif($i == 35)
                                    Танцор
                                @elseif($i == 36)
                                    Учитель
                                @elseif($i == 37)
                                    Копировальщик чертежей
                                @elseif($i == 38)
                                    Президент банка
                                @elseif($i == 39)
                                    Художник по интерьеру
                                @elseif($i == 40)
                                    Контролер качества продукции
                                @elseif($i == 41)
                                    Снабженец
                                @elseif($i == 42)
                                    Художник-мультипликатор
                                @elseif($i == 43)
                                    Режиссер
                                @endif
                            </span>
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
