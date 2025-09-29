@extends('layouts.app')

@section('content')
<style>
    .main-content {
        max-width: 1600px;
    }
</style>
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

<!-- Оберните таблицу в div для адаптивности -->
<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Месяц</th>
                <th>Тесты</th>
                <th>Абс. прирост (цеп)</th>
                <th>Абс. прирост (баз)</th>
                <th>Темп роста % (цеп)</th>
                <th>Темп роста % (баз)</th>
                <th>Темп прироста % (цеп)</th>
                <th>Темп прироста % (баз)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats as $index => $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td>{{ $row['total_tests'] }}</td>
                <td class="{{ isset($row['chain_abs']) && $row['chain_abs'] >= 0 ? 'positive' : 'negative' }}">{{ $row['chain_abs'] ?? '-' }}</td>
                <td class="{{ isset($row['base_abs']) && $row['base_abs'] >= 0 ? 'positive' : 'negative' }}">{{ $row['base_abs'] ?? '-' }}</td>
                <td>{{ $row['chain_growth'] ?? '-' }}</td>
                <td>{{ $row['base_growth'] ?? '-' }}</td>
                <td class="{{ isset($row['chain_increase']) && $row['chain_increase'] >= 0 ? 'positive' : 'negative' }}">{{ $row['chain_increase'] ?? '-' }}</td>
                <td class="{{ isset($row['base_increase']) && $row['base_increase'] >= 0 ? 'positive' : 'negative' }}">{{ $row['base_increase'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Месяц</th>
                <th>Тесты</th>
                <th>MA(3)</th>
                <th>MA(3) restored</th>
                <th>MA(5)</th>
                <th>MA(5) restored</th>
                <th>MA(7)</th>
                <th>MA(7) restored</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats as $index => $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td>{{ $row['total_tests'] }}</td>
                <td>{{ $ma3_raw[$index] ?? '-' }}</td>
                <td>{{ $ma3[$index] ?? '-' }}</td>
                <td>{{ $ma5_raw[$index] ?? '-' }}</td>
                <td>{{ $ma5[$index] ?? '-' }}</td>
                <td>{{ $ma7_raw[$index] ?? '-' }}</td>
                <td>{{ $ma7[$index] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<canvas id="statsChartTests" width="400" height="200"></canvas>
<h2>На представленных графиках отражена динамика ключевых метрик платформы за период с марта 2023 по март 2024 года.</h2>
<ul>
    <li>
        <p style="color: #4e73df; font-weight:600">Количество тестов</p>
        <p>Демонстрирует выраженную сезонность с пиком в сентябре , что может быть связано с началом учебного года и подготовкой к поступлению. Наблюдается значительный спад активности в зимние месяцы и к концу рассматриваемого периода, что указывает на снижение интереса пользователей.</p>
    </li>
    <li>
        <p style="color: #36b9cc; font-weight:600">Количество новых пользователей</p>
        <p>Сильно коррелирует с количеством тестов, что может говорить о том, что пользователи не решаются проходить тестирование несколько раз</p>
    </li>
    <li>
        <p style="color: #1cc88a; font-weight:600">Средний % совпадений</p>
        <p>сохраняет относительную стабильность, колеблясь в умеренном диапазоне. Незначительное снижение показателя может свидетельствовать о росте числа пользователей, чьи результаты менее точно соответствуют эталонным профилям.</p>
    </li>
</ul>
<div>
    <h3>Прогноз на следующий месяц:</h3>
    <p>MA(3): {{ $forecast['ma3'] ?? 'Нет данных' }}</p>
    <p>MA(5): {{ $forecast['ma5'] ?? 'Нет данных' }}</p>
    <p>MA(7): {{ $forecast['ma7'] ?? 'Нет данных' }}</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('statsChartTests').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                    label: 'Количество тестов',
                    data: @json($testsData),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    tension: 0.1,

                    yAxisID: 'y'
                },
                {
                    label: 'Средний % совпадения',
                    data: @json($matchesData),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y2'
                },
                {
                    label: 'Новые пользователи',
                    data: @json($usersData),
                    borderColor: '#36b9cc',
                    backgroundColor: 'rgba(54, 185, 204, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y'
                },
                {
                    label: 'Скользящая средняя (l=3)',
                    data: @json($ma3_raw),
                    borderColor: '#ff6384',
                    tension: 0.1,
                    hidden: true
                },
                {
                    label: 'Скользящая средняя (l=3) восстановленная',
                    data: @json($ma3),
                    borderColor: '#ff6384',
                    tension: 0.1,
                    hidden: true
                },
                {
                    label: 'Скользящая средняя (l=5)',
                    data: @json($ma5_raw),
                    borderColor: '#cc65fe',
                    tension: 0.1,
                    hidden: true
                },
                {
                    label: 'Скользящая средняя (l=5) восстановленная',
                    data: @json($ma5),
                    borderColor: '#cc65fe',
                    tension: 0.1,
                    hidden: true
                },
                {
                    label: 'Скользящая средняя (l=7)',
                    data: @json($ma7_raw),
                    borderColor: '#ffce56',
                    tension: 0.1,
                    hidden: true
                },
                {
                    label: 'Скользящая средняя (l=7) восстановленная',
                    data: @json($ma7),
                    borderColor: '#ffce56',
                    tension: 0.1,
                    hidden: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Количество тестов и новых пользователей'
                    },
                },
                y2: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: '% совпадения'
                    },
                },
            }
        }
    });
</script>
@endsection