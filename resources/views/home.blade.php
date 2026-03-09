@extends('layouts.app')

@section('content')
    <div class="container">
        <section class="section">
            <h2 class="text-center">12 комплексных методик для точной диагностики</h2>

            <div class="grid tests-grid">
                <article class="text-center">
                    <h4>Цветовой тест</h4>
                    <p>Интуитивное раскрашивание шаблона для анализа творческого мышления и эмоционального состояния.
                        Основанный на методах машинного обучения тест, настроенный на определение направления подготовки.
                    </p>
                </article>

                <article class="text-center">
                    <h4>Дж. Холланд</h4>
                    <p>Определение профессионального типа личности: реалистический, интеллектуальный, социальный,
                        конвенциальный, предприимчивый, артистичный</p>
                </article>

                <article class="text-center">
                    <h4>Карта интересов</h4>
                    <p>Выявление сфер профессиональных интересов: физика, математика, техника, химия, биология, медицина,
                        история, искусство, педагогика и другие</p>
                </article>

                <article class="text-center">
                    <h4>ДДО Климова</h4>
                    <p>Дифференциально-диагностический опросник: человек-природа, человек-техника, человек-человек,
                        человек-знаковая система, человек-художественный образ</p>
                </article>

                <article class="text-center">
                    <h4>Л. Йовайши</h4>
                    <p>Опросник профессиональных склонностей: работа с людьми, исследовательская работа, практическая
                        деятельность, эстетические виды, экстремальные виды, планово-экономическая деятельность</p>
                </article>

                <article class="text-center">
                    <h4>Матрица профессий</h4>
                    <p>Подбор профессий на пересечении предпочитаемых объектов труда и видов деятельности</p>
                </article>

                <article class="text-center">
                    <h4>Айзенк</h4>
                    <p>Самооценка психических состояний: тревожность, фрустрация, агрессивность, ригидность</p>
                </article>

                <article class="text-center">
                    <h4>К. Юнг</h4>
                    <p>Определение типа характера: интроверт, амбиверт, экстраверт</p>
                </article>

                <article class="text-center">
                    <h4>Томас</h4>
                    <p>Типы поведения в конфликте: соперничество, сотрудничество, компромисс, избегание, приспособление</p>
                </article>

                <article class="text-center">
                    <h4>Д. Кейрси</h4>
                    <p>Определение типа темперамента и соционического типа личности с подробным описанием</p>
                </article>

                <article class="text-center">
                    <h4>Организаторские способности</h4>
                    <p>Оценка лидерских качеств и склонности к управленческой деятельности</p>
                </article>

                <article class="text-center">
                    <h4>Саморазвитие</h4>
                    <p>Оценка способности к саморазвитию и самообразованию</p>
                </article>
            </div>

            <div class="text-center" style="margin-top: 30px;">
                <p>Пройдите все 12 тестов для получения полного профиля профессиональных склонностей</p>
            </div>
        </section>

        <hr>
        <div class="text-center">
            <h2>Цветовое тестирование</h2>

        </div>
        <section class="section">
            <div class="grid">
                <article class="text-center">


                    <h3>Творческое раскрытие</h3>
                    <div class="ico">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#EFEFEF">
                            <path
                                d="M437-80q-24 0-42-17.63-18-17.62-18-42.37v-200H220q-24 0-42-18t-18-42v-303q0-55 39.66-96 39.65-41 95.34-41h505v440q0 24-18 42t-42 18H583v200q0 24.75-18 42.37Q547-80 523-80h-86ZM220-554h520v-226h-56v171h-60v-171h-71v85h-60v-85H295q-32 0-53.5 23T220-703v149Zm0 154h520v-94H220v94Zm0 0v-94 94Z" />
                        </svg>
                    </div>
                    <p>Раскрашивайте шаблон интуитивно, доверяя своим ощущениям и позволяя цветам выразить ваше внутреннее
                        состояние.</p>
                </article>

                <article class="text-center">
                    <h3>Глубокий
                        анализ</h3>
                    <h3> </h3>
                    <div class="ico">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#EFEFEF">
                            <path
                                d="M284-277h60v-205h-60v205Zm332 0h60v-420h-60v420Zm-166 0h60v-118h-60v118Zm0-205h60v-60h-60v60ZM180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h600q24 0 42 18t18 42v600q0 24-18 42t-42 18H180Zm0-60h600v-600H180v600Zm0-600v600-600Z" />
                        </svg>
                    </div>
                    <p>Система анализирует цветовые предпочтения и композиционные решения, раскрывая особенности мышления.
                    </p>
                </article>

                <article class="text-center">

                    <h3>Персональные рекомендации</h3>
                    <div class="ico">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#EFEFEF">
                            <path
                                d="M716-120H272v-512l278-288 39 31q6 5 9 14t3 22v10l-45 211h299q24 0 42 18t18 42v81.84q0 7.16 1.5 14.66T915-461L789-171q-8.88 21.25-29.59 36.12Q738.69-120 716-120Zm-384-60h397l126-299v-93H482l53-249-203 214v427Zm0-427v427-427Zm-60-25v60H139v392h133v60H79v-512h193Z" />
                        </svg>
                    </div>
                    <p>Получите список направлений подготовки, соответствующих вашему уникальному стилю и потенциалу.</p>
                </article>
            </div>
            <div class="text-center">
                <a href="{{ route('tests.index') }}" role="button"
                    style="border: 2px solid var(--pico-primary);">Приступить к тестированию</a>
            </div>
        </section>
        <hr>

        <section class="section">
            <h2 class="text-center">Примеры результатов тестирования</h2>

            <div class="grid">
                <article class="text-center">
                    <img src="images/Frame 1.png" alt="">
                </article>
                <article class="text-center">
                    <img src="images/Frame 2.png" alt="">
                </article>
                <article class="text-center">
                    <img src="images/Frame 3.png" alt="">
                </article>
                <article class="text-center">
                    <img src="images/Frame 4.png" alt="">
                </article>
            </div>
        </section>
        <hr>
        <section>
            <div class="text-center">
                <h2>Ваш творческий подход имеет значение</h2>
                <p>Для точных результатов важно полностью погрузиться в процесс</p>
            </div>

            <div class="grid">
                <article class="text-center">
                    <h4>Доверяйте интуиции</h4>
                    <div class="ico">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#EFEFEF">
                            <path
                                d="M240-80v-172q-57-52-88.5-121.5T120-520q0-150 105-255t255-105q125 0 221.5 73.5T827-615l55 218q4 14-5 25.5T853-360h-93v140q0 24.75-17.62 42.37Q724.75-160 700-160H600v80h-60v-140h160v-200h114l-45-180q-24-97-105-158.5T480-820q-125 0-212.5 86.5T180-522.46q0 64.42 26.32 122.39Q232.65-342.09 281-297l19 18v199h-60Zm257-370Zm-17 130q17 0 28.5-11.5T520-360q0-17-11.5-28.5T480-400q-17 0-28.5 11.5T440-360q0 17 11.5 28.5T480-320Zm-30-128h61q0-25 6.5-40.5T544-526q18-20 35-40.5t17-53.5q0-42-32.5-71T483-720q-40 0-72.5 23T365-637l55 23q7-22 24.5-35.5T483-663q22 0 36.5 12t14.5 31q0 21-12.5 37.5T492-549q-20 21-31 42t-11 59Z" />
                        </svg>
                    </div>
                    <p>Выбирайте цвета, которые отражают ваше настроение, а не кажутся «правильными»</p>
                </article>

                <article class="text-center">
                    <h4>Создавайте гармонию</h4>
                    <div class="ico"><svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                            width="48px" fill="#EFEFEF">
                            <path
                                d="M377-80v-297H80v-60h297v-87H80v-60h297v-296h60v296h87v-296h60v296h296v60H584v87h296v60H584v297h-60v-297h-87v297h-60Zm60-357h87v-87h-87v87Z" />
                        </svg></div>
                    <p>Стремитесь к композиции, которая воспринимается целостной и отражает ваше видение</p>
                </article>

                <article class="text-center">
                    <h4>Избегайте шаблонов</h4>
                    <div class="ico"><svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                            width="48px" fill="#EFEFEF">
                            <path
                                d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
                        </svg></div>
                    <p>Не пытайтесь нарисовать конкретный объект — ценность в абстрактном выражении</p>
                </article>
            </div>
            <div class="text-center">
                <a href="{{ route('tests.index') }}" role="button"
                    style="border: 2px solid var(--pico-primary);">Приступить к тестированию</a>

            </div>
        </section>
        <hr>
        <section>
            <h2 class="text-center">Выберите свою роль</h2>

            <div class="grid">
                <article class="text-center">
                    <h3>Для абитуриентов</h3>
                    <div class="ico"><svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                            width="48px" fill="#EFEFEF">
                            <path
                                d="M480.08-734q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM373-80v-533q-68-5-131.5-14T120-650l15-60q85 20 169 28.5t176 8.5q92 0 176-8.5T825-710l15 60q-58 14-121.5 23T587-612.88V-80h-60v-260h-94v260h-60Z" />
                        </svg></div>
                    <p>Откройте направления обучения, которые полностью соответствуют вашему складу ума и творческим
                        способностям</p>
                </article>

                <article class="text-center">
                    <h3>Для студентов</h3>
                    <div class="ico"><svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                            width="48px" fill="#EFEFEF">
                            <path
                                d="M479-120 189-279v-240L40-600l439-240 441 240v317h-60v-282l-91 46v240L479-120Zm0-308 315-172-315-169-313 169 313 172Zm0 240 230-127v-168L479-360 249-485v170l230 127Zm1-240Zm-1 74Zm0 0Z" />
                        </svg></div>
                    <p>Помогите обучать аналитическую систему своими результатами и получите обратную связь о своих
                        склонностях</p>
                </article>
            </div>
            <div class="text-center">
                <a href="{{ route('tests.index') }}" role="button"
                    style="border: 2px solid var(--pico-primary);">Приступить к тестированию</a>

            </div>
        </section>
    </div>
@endsection
