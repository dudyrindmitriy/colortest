@extends('layouts.app')

@section('content')


<h2>О сайте</h2>
<p>Профориентация является важным этапом в жизни каждого человека, который помогает определить наиболее подходящие профессии и направления деятельности. В этом процессе важным аспектом является индивидуальный стиль активности, который позволяет понять, какие виды работ могут приносить удовлетворение и успех.</p>
<ul>
    <li><strong>Цветовые ассоциации и профессии:</strong> Разные цвета могут ассоциироваться с определёнными профессиями. Например, яркие и динамичные цвета могут указывать на склонность к творческим областям, таким как дизайн или искусство; в то время как спокойные и нейтральные цвета могут быть связаны с аналитическими и научными профессиями.</li>
    <li><strong>Анализ предпочтений в контексте карьеры:</strong> Сопоставление цветовых предпочтений с известными ассоциациями может осветить стиль активности человека, ориентированный на конкретные профессии. Например, выбор ярких цветов может указывать на желание работать в динамичной и Интерктивной среде.</li>
</ul>
<p>Процесс тестирования включает в себя раскрашивание предложенного шаблона, после чего осуществляется анализ результатов для выявления индивидуального стиля активности и уровня выраженности шахматной структуры. На основе полученных данных пользователю будут рекомендованы подходящие профессии.</p>
<div id="timeChart" style="width: 100%; height: 400px;"></div>

@foreach($isas as $index => $isa)
<div class="svg">
    <div class="isa-container" style="display: flex; align-items: center; margin-bottom: 10px;justify-content: flex-start;">
        <?= $isa->image; ?>
        <p style="max-width: 60%;">{{$descriptions[$index]}}</p>
    </div>
</div>
@endforeach
<div id="isaChart" style="width: 100%; height: 400px;"></div>

@foreach($chess as $index => $ches)
<div class="svg">
    <div class="chess-container" style="display: flex;justify-content: center; align-items: center; margin-bottom: 10px;">
        <div style="max-width: 300px;  "><?= $ches->image; ?></div>
        <p style="max-width: 60%;">Степень выраженности шахматной структуры - {{$ches->chess_structure}}</p>
    </div>
</div>
@endforeach
<div id="chessChart" style="width: 100%; height: 400px;"></div>

<a target="_blank" href="{{ route('home.downloadPDF') }}" class="btn btn-primary">
    Сохранить методичку в PDF
</a>
<br>
<script type="text/javascript">
    var globalIsaData = @json($isaStats);
    var globalChessData = @json($chessStats);
    var globalTimeData = @json($timeStats);
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(function() {
        drawIsaChart(globalIsaData);   
        drawChessChart(globalChessData);
        drawTimeChart(globalTimeData);
});

function drawIsaChart(isaData) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Стиль активности');
    data.addColumn('number', 'Количество');
    
    data.addRows(isaData.map(item => [item.style, item.count]));

    var options = {
        title: 'Распределение стилей активности',
        pieHole: 0.4,
        chartArea: {width: '90%', height: '90%'}
    };

    var chart = new google.visualization.PieChart(
        document.getElementById('isaChart')
    );
    chart.draw(data, options);
}
function drawChessChart(chessData) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Шахматная структура');
    data.addColumn('number', 'Количество');
    
    chessData.sort((a, b) => b.count - a.count);
    
    data.addRows(chessData.map(item => [item.chess_structure, item.count]));

    var options = {
        title: 'Распределение шахматных структур',
        width: '100%',
        height: 400,
        chartArea: {
            width: '90%',
            height: '90%'
        },
        hAxis: {
            title: 'Тип структуры',
        },
        vAxis: {
            title: 'Количество результатов',
            minValue: 0
        },
        legend: { position: 'none' },
        colors: ['#e12100'],
        bar: { groupWidth: '75%' }
    };

    var chart = new google.visualization.ColumnChart(
        document.getElementById('chessChart')
    );
    chart.draw(data, options);
    
    window.addEventListener('resize', function() {
        chart.draw(data, options);
    });
}
function drawTimeChart(timeData) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Дата');
    data.addColumn('number', 'Количество прохождений');

    data.addRows(timeData.map(item => [item.date, item.count]));

    var options = {
        title: 'Количество прохождений тестов по времени',
        legend: {
            position: 'bottom'
        },
        chartArea: {
            width: '80%',
            height: '70%'
        },
        hAxis: {
            title: 'Дата'
        },
        vAxis: {
            title: 'Количество'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('timeChart')); 
    chart.draw(data, options);

    window.addEventListener('resize', function() {
        chart.draw(data, options);
    });
}
</script>
@endsection