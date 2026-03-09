@extends('layouts.app')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    @media (max-width: 425px) {
        .grid-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@section('content')
<article class="instruction">На прохождение всех тестов потребуется примерно 1,5 часа. Результаты сохраняются автоматически после завершения каждого теста — можно проходить их в несколько подходов.</article>
    {{-- <div class="grid-container">
        @foreach ($tests as $test)
            @php
                $userTest = $userTests[$test->id] ?? null;

                if ($userTest && $userTest->isCompleted()) {
                    $class = 'outline secondary'; // Завершен
                } elseif ($userTest && $userTest->isStarted()) {
                    $class = 'secondary'; // Начат, но не завершен
                } else {
                    $class = ''; // Не начат
                }
            @endphp

            <a href="{{ route('tests.show', $test->id) }}" role="button" class="{{ $class }}">{{ $test->name }}</a>
        @endforeach


    </div> --}}

        <div class="grid-container">

            @foreach ($tests as $test)
                @php
                    $userTest = $userTests[$test->id] ?? null;

                    if ($userTest) {
                        $class = 'outline secondary'; // Завершен
                        $text = 'Пройти повторно';
                    } else {
                        $class = '';
                        $text = 'Начать';
                    }
                @endphp

                <article style="display: flex; flex-direction:column;">
                    <div style="margin-bottom: 15px">
                    {{ $test->name }}
                    </div>
                     <div style="margin-top: auto; text-align: right;">
                    <a href="{{ route('tests.show', $test->id) }}" role="button"
                        class="{{ $class }}">{{$text}}</a>
                     </div>
                </article>
            @endforeach
        </div>
@endsection
