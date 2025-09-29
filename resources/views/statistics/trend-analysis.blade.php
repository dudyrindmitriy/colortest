@extends('layouts.app')

@section('content')
<style>
    /* Основные стили таблицы */
     
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    /* Шапка таблицы */
    .table thead.thead-dark {
        background-color: rgb(67, 95, 255);
        color: white;
    }

    .table thead th {
        padding: 12px 15px;
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
        position: sticky;
        top: 0;
    }

    /* Ячейки таблицы */
    .table td {
        padding: 12px 15px;
        text-align: center;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    /* Чередование строк */
    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
        transition: background-color 0.3s ease;
    }

    /* Границы таблицы */
    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    /* Стили для заголовков столбцов */
    th {
        white-space: nowrap;
    }

    /* Стили для числовых значений */
    td {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 500;
    }

    /* Подсветка положительных и отрицательных значений */
    .positive {
        color: #28a745;
    }

    .negative {
        color: #dc3545;
    }

    /* Адаптивность */
    @media (max-width: 992px) {
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            font-size: 0.9rem;
        }

        .table thead th,
        .table td {
            padding: 8px 10px;
        }
    }
</style>
<div class="container-fluid">
    @foreach($analysis as $key => $item)
    <div class="card shadow mb-4">
       
        <div class="card-body">
             <div class="card-header py-3">
            <h2 class="h4 text-gray-800">Анализ для ряда: {{ ucfirst($key) }}</h2>
        </div>
        
            <h3>Задание 1: Проверка тренда</h3>
            <h4>Критерий серий, основанный на медиане выборки</h4>
            
            @php
                $sorted_y = $item['data'];
                sort($sorted_y);
                $median_test = $item['trend']['median_test'];
            @endphp
            
           <p><strong>Вариационный ряд:</strong> <?= implode(', ', array_map(function($v) { 
    return round($v, 2); 
}, $sorted_y)) ?></p>
            <p><strong>Медиана (Me):</strong> {{ round($median_test['median'],2) }}</p>
            <p><strong>Знакопоследовательность:</strong> {{ implode(' ', $median_test['signs']->toArray()) }}</p>
            <p><strong>Число серий v(n):</strong> {{ $median_test['v'] }}</p>
            <p><strong>Максимальная длина серии τ_max(n):</strong> {{ $median_test['max_len'] }}</p>
            <p><strong>Критическое значение для v(n):</strong> {{ round($median_test['left_v'],2) }}</p>
            <p><strong>Критическое значение для τ_max(n):</strong> {{ $median_test['right_t'] }}</p>
            
            <p>v(n) = {{ $median_test['v'] }} --> {{ $median_test['v'] > $median_test['left_v'] ? "OK" : "НАРУШЕНО" }}</p>
            <p>τ_max(n) = {{ $median_test['max_len'] }} --> {{ $median_test['max_len'] < $median_test['right_t'] ? "OK" : "НАРУШЕНО" }}</p>
            
            @if($median_test['has_trend'])
                <p>Гипотеза H0 ОТВЕРГАЕТСЯ</p>
                <p>Тренд существует</p>
            @else
                <p>Нет оснований отвергать гипотезу H0</p>
                <p>Тренд не существует</p>
            @endif
            
            <h4>Критерий "восходящих и нисходящих" серий</h4>
            
            @php
                $direction_test = $item['trend']['direction_test'];
            @endphp
            
            <p><strong>Вспомогательная последовательность знаков:</strong> {{ implode(' ', $direction_test['signs']) }}</p>
            <p><strong>Число серий v(n):</strong> {{ $direction_test['v'] }}</p>
            <p><strong>Максимальная длина серии τ_max(n):</strong> {{ $direction_test['max_len'] }}</p>
            <p><strong>Критическое значение для v(n):</strong> {{ round($direction_test['left_v'],2) }}</p>
            <p><strong>Критическое значение для τ_max(n):</strong> {{ $direction_test['right_t'] }}</p>
            
            <p>v(n) = {{ $direction_test['v'] }} --> {{ $direction_test['v'] > $direction_test['left_v'] ? "OK" : "НАРУШЕНО" }}</p>
            <p>τ_max(n) = {{ $direction_test['max_len'] }} --> {{ $direction_test['max_len'] < $direction_test['right_t'] ? "OK" : "НАРУШЕНО" }}</p>
            
            @if($direction_test['has_trend'])
                <p>Гипотеза H0 ОТВЕРГАЕТСЯ</p>
                <p>Тренд существует</p>
            @else
                <p>Нет оснований отвергать гипотезу H0</p>
                <p>Тренд не существует</p>
            @endif
            
            <h3>Задание 2: Модели тренда</h3>
            
            <table class="table table-bordered">
                <tr>
                    <th>№</th>
                    <th>y(t)</th>
                    <th>t</th>
                    <th>y(t)t</th>
                    <th>t²</th>
                    <th>y(t)t²</th>
                    <th>t⁴</th>
                    <th>ln(y(t))</th>
                    <th>ln(y(t))t</th>
                </tr>
                <p><strong>Расчет параметров моделей</strong></p>
                
                @foreach($item['data'] as $index => $value)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ round($value, 2) }}</td>
                    <td>{{ $item['calculations']['t'][$index] }}</td>
                    <td>{{ round($item['calculations']['yt'][$index], 2) }}</td>
                    <td>{{ $item['calculations']['t2'][$index] }}</td>
                    <td>{{ round($item['calculations']['yt2'][$index], 2) }}</td>
                    <td>{{ $item['calculations']['t4'][$index] }}</td>
                    <td>{{ round($item['calculations']['lny'][$index], 2) }}</td>
                    <td>{{ round($item['calculations']['lnyt'][$index], 2) }}</td>
                </tr>
                @endforeach
                
                <tr>
                    <td>Σ</td>
                    <td>{{ round(array_sum($item['data']), 2) }}</td>
                    <td></td>
                    <td>{{ round(array_sum($item['calculations']['yt']), 2) }}</td>
                    <td>{{ round(array_sum($item['calculations']['t2']), 2) }}</td>
                    <td>{{ round(array_sum($item['calculations']['yt2']), 2) }}</td>
                    <td>{{ round(array_sum($item['calculations']['t4']), 2) }}</td>
                    <td>{{ round(array_sum($item['calculations']['lny']), 2) }}</td>
                    <td>{{ round(array_sum($item['calculations']['lnyt']), 2) }}</td>
                </tr>
            </table>
            
            <p><strong>Линейная модель:</strong> {{ $item['models']['linear']['equation'] }}</p>
            <p><strong>Параболическая модель:</strong> {{ $item['models']['parabolic']['equation'] }}</p>
            <p><strong>Показательная модель:</strong> {{ $item['models']['exponential']['equation'] }}</p>
            
            <div class="mt-4">
                <canvas id="chart_{{ $key }}" height="100"></canvas>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @foreach($analysis as $key => $item)
    new Chart(document.getElementById('chart_{{ $key }}'), {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [
                {
                    label: 'Фактические значения',
                    data: @json($item['data']),
                    borderColor: '#4e73df',
                    fill: false
                },
                {
                    label: 'Линейная модель',
                    data: @json($item['models']['linear']['predicted']),
                    borderColor: '#1cc88a',
                    borderDash: [5,5],
                    fill: false
                },
                {
                    label: 'Параболическая модель',
                    data: @json($item['models']['parabolic']['predicted']),
                    borderColor: '#36b9cc',
                    borderDash: [5,5],
                    fill: false
                },
                {
                    label: 'Показательная модель',
                    data: @json($item['models']['exponential']['predicted']),
                    borderColor: '#f6c23e',
                    borderDash: [5,5],
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: false }
            }
        }
    });
    @endforeach
</script>
@endsection