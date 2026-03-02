@php
    $interpretation = $test['interpretation'];
    $score = $interpretation['score'];
    $percentage = $interpretation['percentage'];
    $level = $interpretation['level'];

    // Преобразуем уровень в нижний регистр для вставки в предложение
    $levelText = mb_strtolower($level);
@endphp

<div style="page-break-inside: auto;">
    <div>
        Уровень выраженности качеств лидера - {{ $levelText }}. ({{ $percentage }}%)
    </div>
</div>
