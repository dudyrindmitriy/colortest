{{-- resources/views/tests/matrix.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>{{ $test->name ?? 'Матрица профессий' }}</h1>

    <article class="instruction">
        <p><strong>Инструкция:</strong> Вам предлагается два вопроса. К каждому из них есть 10 вариантов ответов. Выберите вариант ответа, который более всего соответствует Вашим желаниям и стремлениям. Количество выбранных ответов в каждом вопросе не более 3.</p>
        <p>Задайте себе следующие вопросы:</p>
    </article>

    <form id="matrixTestForm" method="POST" action="{{ route('tests.save', $testId) }}">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="questions-grid">
            <article>
                <div class="question-header">
                    <span class="question-number">1</span>
                    <div class="question-text">
                        С кем или с чем Вы бы хотели работать?
                    </div>
                </div>
                <hr>
                <div class="options matrix-options">
                    @php
                        $objectOptions = [
                            '1' => 'Человек (дети и взрослые, ученики и студенты, клиенты и пациенты, покупатели и пассажиры, зрители и читатели, сотрудники и т.д.)',
                            '2' => 'Информация (тексты, формулы, схемы, коды, чертежи, иностранные языки, языки программирования)',
                            '3' => 'Финансы (деньги, акции, фонды, лимиты, кредиты)',
                            '4' => 'Техника (механизмы, станки, здания, конструкции, приборы, машины)',
                            '5' => 'Искусство (литература, музыка, театр, кино, балет, живопись и т.д.)',
                            '6' => 'Животные (служебные, дикие, домашние, промысловые)',
                            '7' => 'Растения (сельскохозяйственные, дикорастущие, декоративные)',
                            '8' => 'Продукты питания (мясные, рыбные, молочные, кондитерские и хлебобулочные изделия, консервы, плоды, овощи, фрукты)',
                            '9' => 'Изделия (металл, ткани, мех, кожа, дерево, камень, лекарства)',
                            '10' => 'Природные ресурсы (земли, леса, горы, водоемы, месторождения)',
                        ];
                    @endphp

                    @foreach ($objectOptions as $value => $text)
                        <label class="option">
                            <input type="checkbox" name="object[{{ $value }}]" value="{{ $value }}">
                            <span class="option-text">{{ $text }}</span>
                        </label>
                    @endforeach
                </div>
            </article>
            <article>
                <div class="question-header">
                    <span class="question-number">2</span>
                    <div class="question-text">
                        Чем бы Вы хотели заниматься?
                    </div>
                </div>
                <hr>
                <div class="options matrix-options">
                    @php
                        $activityOptions = [
                            '1' => 'Управление (руководство чьей-то деятельностью)',
                            '2' => 'Обслуживание (удовлетворение чьих-то потребностей)',
                            '3' => 'Образование (воспитание, обучение, формирование личности)',
                            '4' => 'Оздоровление (избавление от болезней и их предупреждение)',
                            '5' => 'Творчество (создание оригинальных произведений искусства)',
                            '6' => 'Производство (изготовление продукции)',
                            '7' => 'Конструирование (проектирование деталей и объектов)',
                            '8' => 'Исследование (научное изучение чего-либо или кого-либо)',
                            '9' => 'Защита (охрана от враждебных действий)',
                            '10' => 'Контроль (проверка и наблюдение)',
                        ];
                    @endphp

                    @foreach ($activityOptions as $value => $text)
                        <label class="option">
                            <input type="checkbox" name="activity[{{ $value }}]" value="{{ $value }}">
                            <span class="option-text">{{ $text }}</span>
                        </label>
                    @endforeach
                </div>
            </article>
        </div>

        <div class="test-actions">
            <button type="submit" id="saveResult" data-route="{{ route('tests.save', $testId) }}"
                data-redirect="{{ route('profile') }}">Сохранить результаты</button>
            <button type="button" id="clearForm" class="secondary">Очистить форму</button>
        </div>
    </form>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const objectCheckboxes = document.querySelectorAll('input[name^="object["]');
            const activityCheckboxes = document.querySelectorAll('input[name^="activity["]');

            function limitSelection(checkboxes, limit) {
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

                        if (checkedCount > limit) {
                            this.checked = false;
                            if (typeof ModalManager !== 'undefined') {
                                ModalManager.showAlert(`Можно выбрать не более ${limit} вариантов`, 'Внимание');
                            } else {
                                alert(`Можно выбрать не более ${limit} вариантов`);
                            }
                        }
                    });
                });
            }

            limitSelection(objectCheckboxes, 3);
            limitSelection(activityCheckboxes, 3);
        });
    </script>
@endsection
