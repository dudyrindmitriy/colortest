@extends('layouts.app')

@section('content')
<style>
    /* Основные стили таблицы */
     .text-success {
        color: #28a745; /* Яркий зелёный */
        font-weight: 500;
    }

    /* Стиль для опасных сообщений (красный) */
    .text-danger {
        color: #dc3545; /* Яркий красный */
        font-weight: 500;
    }

    /* Стиль для предупреждений (оранжевый) */
    .text-warning {
        color: #ffc107; /* Золотисто-жёлтый */
        font-weight: 500;
    }
    .main-content {
        max-width: 1600px;
    }
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
    
    /* Дополнительные стили для вкладок */
    .nav-tabs .nav-link {
        font-weight: 600;
    }
    .tab-content {
        padding: 20px 0;
    }
</style>

<div class="container">
    <h2>Анализ моделей временных рядов</h2>
    
    <ul class="nav nav-tabs" id="seriesTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tests-tab" data-toggle="tab" href="#tests" role="tab">Общее количество тестов</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="matches-tab" data-toggle="tab" href="#matches" role="tab">Среднее соответствие</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab">Новые пользователи</a>
        </li>
    </ul>
    
    <div class="tab-content" id="seriesTabsContent">
        @foreach(['total_tests' => 'tests', 'avg_match' => 'matches', 'new_users' => 'users'] as $series => $tabId)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <canvas id="{{ $tabId }}ComparisonChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body" id="accuracyAnalysis{{ ucfirst($tabId) }}">
                            @include('statistics.partials.accuracy-analysis', [
                                'accuracyAnalysis' => $analysisResults[$series]['accuracy_analysis'],
                                'series' => $tabId
                            ])
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body" id="residualAnalysis{{ ucfirst($tabId) }}">
                            @include('statistics.partials.residual-analysis', [
                                'residualAnalysis' => $analysisResults[$series]['residual_analysis'],
                                'actualValues' => $analysisResults[$series]['actual_values'],
                                'timeVariable' => $timeVariable,
                                'models' => $analysisResults[$series]['models'],
                                'series' => $tabId
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Графики сравнения моделей для каждого ряда
    @foreach(['total_tests' => 'tests', 'avg_match' => 'matches', 'new_users' => 'users'] as $series => $tabId)
    new Chart(document.getElementById('{{ $tabId }}ComparisonChart'), {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [
                {
                    label: 'Фактические значения',
                    data: @json($analysisResults[$series]['actual_values']),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                },
                @foreach(['linear', 'parabolic', 'exponential'] as $model)
                {
                    label: '{{ ucfirst($model) }} модель',
                    data: @json($analysisResults[$series]['models'][$model]['predicted']),
                    borderColor: 'rgb({{ $model === 'linear' ? '28, 200, 138' : ($model === 'parabolic' ? '246, 194, 62' : '231, 74, 59') }})',
                    tension: 0.1,
                    fill: false,
                },
                @endforeach
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Сравнение моделей ({{ $series === 'total_tests' ? 'Общее количество тестов' : ($series === 'avg_match' ? 'Среднее соответствие' : 'Новые пользователи') }})'
                }
            }
        }
    });
    @endforeach
</script>
@endsection