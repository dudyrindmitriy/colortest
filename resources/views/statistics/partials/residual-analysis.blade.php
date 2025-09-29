<div class="residual-analysis">
    <h3>Анализ остатков</h3>

    <!-- <div class="row mb-4">
        @foreach(['linear', 'parabolic', 'exponential'] as $model)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Остатки {{ $model }} модели</div>
                <div class="card-body">
                    <canvas id="{{ $model }}ResidualsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        @endforeach
    </div> -->

    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Модель</th>
                <th>Случайность (критерий знаков)</th>
                <th>Нормальность</th>
                <th>Независимость (Дарбин-Уотсон)</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['linear', 'parabolic', 'exponential'] as $model)
            @php
            $dwTable = $controller->buildDurbinWatsonTable($actualValues, $models[$model]['predicted'], $timeVariable, $model);
            @endphp
            <tr>
                <td>{{ ucfirst($model) }}</td>
                <td>
                    <div class="formula-details">
                        <strong>Критерий знаков:</strong>
                        <div>Положительных изменений: {{ $residualAnalysis[$model]['signTest']['positive'] }}</div>
                        <div>Отрицательных изменений: {{ $residualAnalysis[$model]['signTest']['negative'] }}</div>
                        <div>Z-значение: {{ number_format($residualAnalysis[$model]['signTest']['zValue'], 2) }}</div>
                        <div class="conclusion {{ $residualAnalysis[$model]['signTest']['isRandom'] ? 'text-success' : 'text-danger' }}">
                            {{ $residualAnalysis[$model]['signTest']['isRandom'] ? "Случайны (|Z| < 1.96)" : "Не случайны (|Z| ≥ 1.96)" }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="formula-details">
                        <strong>Асимметрия (A):</strong>
                        <div>{{ number_format($residualAnalysis[$model]['normalityTest']['skewness'], 4) }}</div>
                        <div>Критерий: |A| < {{ number_format($residualAnalysis[$model]['normalityTest']['criticalValues']['A']['criticalA1'], 4) }}</div>

                                <strong>Эксцесс (Э):</strong>
                                <div>{{ number_format($residualAnalysis[$model]['normalityTest']['kurtosis'], 4) }}</div>
                                <div>Критерий: |Э + {{ number_format($residualAnalysis[$model]['normalityTest']['criticalValues']['E']['termE'], 4) }}| < {{ number_format($residualAnalysis[$model]['normalityTest']['criticalValues']['E']['criticalE1'], 4) }}</div>

                                        <div class="conclusion {{ $residualAnalysis[$model]['normalityTest']['conclusionClass'] }}">
                                            {{ $residualAnalysis[$model]['normalityTest']['normalityConclusion'] }}
                                        </div>
                                </div>
                </td>
                <td>
                    <div class="formula-details">
                        <strong>Статистика Дарбина-Уотсона:</strong>
                        <div>d = {{ number_format($residualAnalysis[$model]['durbinWatsonTest']['dStatistic'], 4) }}</div>
                        <div class="conclusion 
                            {{ $residualAnalysis[$model]['durbinWatsonTest']['conclusion'] === 'Автокорреляция отсутствует' ? 'text-warning' : 
                              ($residualAnalysis[$model]['durbinWatsonTest']['conclusion'] === 'Положительная автокорреляция' ? 'text-success' : 'text-danger') }}">
                            {{ $residualAnalysis[$model]['durbinWatsonTest']['conclusion'] }}
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @foreach(['linear', 'parabolic', 'exponential'] as $model)
    @php
    $dwTable = $controller->buildDurbinWatsonTable($actualValues, $models[$model]['predicted'], $timeVariable, $model);
    @endphp
    <div class="dw-table mt-4" style="display: none;">
        <h4>РАСЧЕТ ДЛЯ {{ $dwTable['modelTitle'] }}</h4>
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>№</th>
                    <th>y<sub>t</sub></th>
                    <th>t</th>
                    <th>ŷ<sub>t</sub></th>
                    <th>e<sub>t</sub> = y<sub>t</sub> - ŷ<sub>t</sub></th>
                    <th>e<sub>t</sub><sup>2</sup></th>
                    <th>(e<sub>t</sub> - e<sub>t-1</sub>)<sup>2</sup></th>
                </tr>
            </thead>
            <tbody>
                @foreach($actualValues as $i => $value)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ number_format($value, 0) }}</td>
                    <td>{{ $timeVariable[$i] }}</td>
                    <td>{{ number_format($models[$model]['predicted'][$i], 2) }}</td>
                    <td>{{ number_format($dwTable['residuals'][$i], 3) }}</td>
                    <td>{{ number_format($dwTable['squaredResiduals'][$i], 2) }}</td>
                    <td>{{ $i > 0 ? number_format($dwTable['squaredDifferences'][$i], 2) : '—' }}</td>
                </tr>
                @endforeach
                <tr class="table-info">
                    <td>Σ</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($dwTable['sumSquaredResiduals'], 2) }}</td>
                    <td>{{ number_format($dwTable['sumSquaredDifferences'], 2) }}</td>
                </tr>
            </tbody>
        </table>
        <div class="alert alert-info">
            <strong>Статистика Дарбина-Уотсона:</strong> d = Σ(e<sub>t</sub> − e<sub>t-1</sub>)² / Σe<sub>t</sub>² =
            {{ number_format($dwTable['sumSquaredDifferences'], 2) }} / {{ number_format($dwTable['sumSquaredResiduals'], 2) }} =
            {{ number_format($dwTable['dStatistic'], 4) }}
        </div>
    </div>
    @endforeach
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Графики остатков
    // @foreach(['linear', 'parabolic', 'exponential'] as $model)
    // const {{ $model }}ResidualsCtx = document.getElementById('{{ $model }}ResidualsChart').getContext('2d');
    // new Chart({{ $model }}ResidualsCtx, {
    //     type: 'scatter',
    //     data: {
    //         datasets: [{
    //             label: 'Остатки',
    //             data: [
    //                 @foreach($residualAnalysis[$model]['residuals'] as $i => $residual)
    //                 { x: {{ $i + 1 }}, y: {{ $residual }} },
    //                 @endforeach
    //             ],
    //             backgroundColor: 'rgba(255, 99, 132, 0.7)',
    //             showLine: false
    //         }]
    //     },
    //     options: {
    //         responsive: true,
    //         scales: {
    //             x: {
    //                 title: {
    //                     display: true,
    //                     text: 'Период'
    //                 }
    //             },
    //             y: {
    //                 title: {
    //                     display: true,
    //                     text: 'Остаток'
    //                 }
    //             }
    //         },
    //         plugins: {
    //             title: {
    //                 display: true,
    //                 text: 'Остатки {{ $model }} модели'
    //             }
    //         }
    //     }
    // });
    // @endforeach
</script>