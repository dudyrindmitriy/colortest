<div style=" text-align: center;">
    @php

        // var_dump($test['color_result']['ml_predictions']);
    @endphp
    <img src="{!! $test['color_result']['user_image'] !!}" width="70%"  alt="Result Image"/>
     @if ($test['color_result']['ml_predictions'] && count($test['color_result']['ml_predictions']) > 0)
            @php
                $predictions = collect($test['color_result']['ml_predictions'])->sortBy('rank')->take(5);

                // Получаем коды программ
                $codes = $predictions->pluck('class')->toArray();
                $programs = App\Models\EducationProgram::whereIn('code', $codes)->get()->keyBy('code');
            @endphp

            @foreach ($predictions as $prediction)
                @php $program = $programs[$prediction['class']] ?? null; @endphp
                <p>{{ $program ? $program->code . ' ' . $program->name : $prediction['class'] }} -
                    {{ round($prediction['probability'], 1) }}%</p>
            @endforeach
        @else
            <span class="text-muted">Не анализировано</span>
        @endif

</div>
