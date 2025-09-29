<div class="accuracy-analysis">
    <h3>Точность моделей</h3>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Модель</th>
                <th>SSE</th>
                <th>MSE</th>
                <th>S (RMSE)</th>
                <th>MAPE, %</th>
             
            </tr>
        </thead>
        <tbody>
            @foreach(['linear', 'parabolic', 'exponential'] as $model)
            <tr>
                <td>{{ ucfirst($model) }}</td>
                <td>{{ number_format($accuracyAnalysis[$model]['sse'], 2) }}</td>
                <td>{{ number_format($accuracyAnalysis[$model]['mse'], 2) }}</td>
                <td>{{ number_format($accuracyAnalysis[$model]['rmse'], 2) }}</td>
                <td>{{ number_format($accuracyAnalysis[$model]['mape'], 2) }}</td>
               
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @php
        $bestModel = '';
        $bestScore = 0;
        $scores = [];
        
        foreach (['linear', 'parabolic', 'exponential'] as $model) {
            $score = 0;
            $score += ($accuracyAnalysis[$model]['sse'] == min(array_column($accuracyAnalysis, 'sse'))) ? 1 : 0;
            $score += ($accuracyAnalysis[$model]['mse'] == min(array_column($accuracyAnalysis, 'mse'))) ? 1 : 0;
            $score += ($accuracyAnalysis[$model]['rmse'] == min(array_column($accuracyAnalysis, 'rmse'))) ? 1 : 0;
            $score += ($accuracyAnalysis[$model]['mape'] == min(array_column($accuracyAnalysis, 'mape'))) ? 1 : 0;
            
            $scores[$model] = $score;
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestModel = $model;
            }
        }
    @endphp
    
    <div class="conclusion mt-4 p-3 bg-light">
        <h4>Вывод:</h4>
        @if($bestScore == 4)
        <p>На основании анализа всех представленных метрик точности (SSE, MSE, RMSE, MAPE), {{ $bestModel }} модель лучше всего описывает исходные данные. Это подтверждается тем, что по всем показателям ошибок она имеет наименьшие значения по сравнению с другими моделями.</p>
        @else
        <p>Наиболее подходящей моделью является {{ $bestModel }} модель, так как она демонстрирует наилучшие результаты по {{ $bestScore }} из 4 основных метрик точности.</p>
        
       @php
    // Определяем лучшие модели для каждой метрики
    $bestModels = [];
    foreach (['sse', 'mse', 'rmse', 'mape'] as $metric) {
        $minValue = min(array_column($accuracyAnalysis, $metric));
        $bestForMetric = array_search($minValue, array_column($accuracyAnalysis, $metric));
        $bestModels[$metric] = [
            'model' => $bestForMetric,
            'value' => $minValue
        ];
    }
@endphp

<p>Детализация:</p>
<ul>
    @foreach(['sse', 'mse', 'rmse', 'mape'] as $metric)
        <li>{{ strtoupper($metric) }}: Лучшая модель - {{ ucfirst($bestModels[$metric]['model']) }} 
            (значение: {{ number_format($bestModels[$metric]['value'], 2) }}{{ $metric == 'mape' ? '%' : '' }})
        </li>
    @endforeach
</ul>
        @endif
    </div>
</div>