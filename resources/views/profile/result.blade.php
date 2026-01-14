@extends('layouts.app')

@section('content')
<h1>Результат тестирования</h1>
    <div class="test-grid grid">

        <div class="svg">

            <div style=" text-align: center;">
                {!! $result->user_image !!}
            </div>
        </div>
        <div>
            <p>Дата прохождения: {{ $result->created_at }}</p>
            <p>Рекомендация: @if ($result->ml_predictions && count($result->ml_predictions) > 0)
                                    @php
                                        $predictions = collect($result->ml_predictions)->sortBy('rank')->take(5);

                                        // Получаем коды программ
                                        $codes = $predictions->pluck('class')->toArray();
                                        $programs = App\Models\EducationProgram::whereIn('code', $codes)
                                            ->get()
                                            ->keyBy('code');
                                    @endphp

                                    @foreach ($predictions as $prediction)
                                        @php $program = $programs[$prediction['class']] ?? null; @endphp
                                            <p>{{ $program ? $program->code . ' ' . $program->name : $prediction['class'] }} -
                                           {{ round($prediction['probability'], 1) }}%</p>
                                    @endforeach
                                @else
                                    <span class="text-muted">Не анализировано</span>
                                @endif</p>
        </div>
    </div>
        <a href="{{ route('profile') }}">Профиль</a>
@endsection
