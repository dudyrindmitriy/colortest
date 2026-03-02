@php
    $interests = $test['interpretation'];

    // Проверяем, если это массив с ключом 'interests', то берем его
    if (isset($interests['interests']) && is_array($interests['interests'])) {
        $interests = $interests['interests'];
    }
@endphp

<div style="page-break-inside: auto;">
    <h4>Сферы профессиональных интересов:</h4>

    @foreach($interests as $key => $value)
        @php
            $name = is_array($value) ? $value['name'] : $key;
            $score = is_array($value) ? $value['score'] : $value;
        @endphp
        <div style="margin: 3px 0;">
            {{ $name }}: {{ $score }}
        </div>
    @endforeach
</div>
