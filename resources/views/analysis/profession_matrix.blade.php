@php
    $professions = $test['interpretation']['professions'] ?? [];
@endphp

<div style="page-break-inside: auto;">
    <h4>Склонность к профессиям:</h4>

    @if(!empty($professions))
        <div style="margin: 15px 0;">
            @foreach($professions as $profession)
                <div style="margin: 3px 0;">
                    {{ $profession }}
                </div>
            @endforeach
        </div>
    @else
        <p>Недостаточно данных для подбора профессий.</p>
    @endif
</div>
