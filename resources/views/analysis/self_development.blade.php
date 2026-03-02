@php
    $interpretation = $test['interpretation'];
    $levelName = $interpretation['level_name'];
    $percentage = $interpretation['percentage'];

    // Приводим к нижнему регистру для вставки в предложение
    $levelText = mb_strtolower($levelName);
@endphp

<div style="page-break-inside: auto;">
    <div>
        Уровень способностей к саморазвитию и самообразованию - {{ $levelText }}. ({{ $percentage }}%)
    </div>
</div>
