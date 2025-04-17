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
<div id="areaChart" style="width: 100%; height: 400px;"></div>
<div id="bubbleChart" style="width: 100%; height: 500px;"></div>
<div id="calendarChart" style="width: 100%; height: 200px;"></div>
<div id="orgChart" style="width: 100%; height: 200px;"></div>
<div id="sankeyChart" style="width: 100%; height: 500px;"></div>
<div id="timelineChart" style="width: 100%; height: 1900px;"></div>
<a target="_blank" href="{{ route('home.downloadPDF') }}" class="btn btn-primary">
    Сохранить методичку в PDF
</a>
<br>
<script type="text/javascript">
    var globalIsaData = @json($isaStats);
    var globalChessData = @json($chessStats);
    var globalTimeData = @json($timeStats);
    var globalAreaData = @json($areaStats);
    var globalBubbleData = @json($bubbleStats);
    var globalCalendarData = @json($calendarStats);
    var globalOrgData = @json($orgStats);
    var globalSankeyData = @json($sankeyStats);
    var globalTimelineData = @json($timelineStats);
    google.charts.load('current', {
        'packages': ['corechart', 'calendar', 'orgchart', 'sankey', 'timeline']
    });
    google.charts.setOnLoadCallback(function() {
        drawIsaChart(globalIsaData);
        drawChessChart(globalChessData);
        drawTimeChart(globalTimeData);
        drawAreaChart(globalAreaData);
        drawBubbleChart(globalBubbleData);
        drawCalendarChart(globalCalendarData);
        drawOrgChart(globalOrgData);
        drawSankeyChart(globalSankeyData);
        drawTimelineChart(globalTimelineData);
    });

    function drawIsaChart(isaData) {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Стиль активности');
        data.addColumn('number', 'Количество');

        data.addRows(isaData.map(item => [item.style, item.count]));

        var options = {
            title: 'Распределение стилей активности',
            pieHole: 0.4,
            chartArea: {
                width: '90%',
                height: '90%'
            }
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
            legend: {
                position: 'none'
            },
            colors: ['#e12100'],
            bar: {
                groupWidth: '75%'
            }
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

    function drawAreaChart(areaData) {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Месяц');
        data.addColumn('number', 'Ожидаемое количество тестов');
        data.addColumn('number', 'Фактическое количество тестов');

        data.addRows(areaData.map(item => [item.month, item.expected, item.actual]));

        var options = {
            title: 'Динамика прохождения тестов по месяцам',
            hAxis: {
                title: 'Месяц'
            },
            vAxis: {
                title: 'Количество тестов'
            },
            isStacked: false,
            colors: ['#FFC107', '#4CAF50'],
            chartArea: {
                width: '85%',
                height: '75%'
            }
        };

        var chart = new google.visualization.AreaChart(
            document.getElementById('areaChart')
        );
        chart.draw(data, options);
    }

    
    function drawBubbleChart(bubbleData) {
    
    const uniqueColors = [...new Set(bubbleData.map(item => item.color_code))];
    const colorMap = new Map(uniqueColors.map((color, index) => [color, index]));

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Цвет');
    data.addColumn('number', 'Яркость');
    data.addColumn('number', 'Контраст');
    data.addColumn('number', 'Цветовой индекс');  
    data.addColumn('number', 'Частота');

    bubbleData.forEach(item => {
        data.addRow([
            item.color_name,
            item.brightness,
            item.contrast,
            colorMap.get(item.color_code),  
            item.frequency
        ]);
    });

    var options = {
        title: 'Цветовые предпочтения по яркости и контрасту',
        hAxis: { title: 'Яркость', minValue: 0, maxValue: 1 },
        vAxis: { title: 'Контраст', minValue: 0, maxValue: 1 },
        bubble: {
            textStyle: { fontSize: 11 },
        },
        colorAxis: {
            minValue: 0,
            maxValue: uniqueColors.length - 1,
            colors: uniqueColors  
        },
        sizeAxis: { minValue: 0, minSize: 1 },
        tooltip: {
            trigger: 'focus',
            isHtml: true,
            textStyle: { fontSize: 12 }
        }
    };

    var chart = new google.visualization.BubbleChart(
        document.getElementById('bubbleChart')
    );
    chart.draw(data, options);
}

    
    function drawCalendarChart(calendarData) {
        var data = new google.visualization.DataTable();
        data.addColumn({
            type: 'date',
            id: 'Date'
        });
        data.addColumn({
            type: 'number',
            id: 'Tests'
        });

        calendarData.forEach(item => {
            data.addRow([new Date(item.year, item.month - 1, item.day), item.count]);
        });

        var options = {
            title: "Активность прохождения тестов по дням",
            calendar: {
                cellSize: 15,
                monthOutlineColor: {
                    stroke: '#4CAF50',
                    strokeOpacity: 0.8,
                    strokeWidth: 2
                },
                unusedMonthOutlineColor: {
                    stroke: '#9E9E9E',
                    strokeOpacity: 0.8,
                    strokeWidth: 1
                },
                focusedCellColor: {
                    stroke: '#FF5722'
                }
            },
            colorAxis: {
                minValue: 0,
                colors: ['#E0F7FA', '#4CAF50']
            }
        };

        var chart = new google.visualization.Calendar(
            document.getElementById('calendarChart')
        );
        chart.draw(data, options);
    }

    
    function drawOrgChart(orgData) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'ID');
    data.addColumn('string', 'Parent');
    data.addColumn('string', 'ToolTip');
    data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}}); 

    orgData.forEach(item => {
        data.addRow([
            {
                v: item.id, 
                f: item.display || item.tooltip || item.id 
            },
            item.parent || '',
            item.tooltip || '',
            item.tooltip ? `<div class="custom-tooltip">${item.tooltip}</div>` : '' 
        ]);
    });

    var options = {
        title: 'Структура цветовых ассоциаций с профессиями',
        allowHtml: true, 
        size: 'small',
        allowCollapse: true,
        tooltip: { 
            isHtml: true 
        }
    };

    var chart = new google.visualization.OrgChart(
        document.getElementById('orgChart')
    );
    chart.draw(data, options);
}
function drawSankeyChart(sankeyData) {
    google.charts.load('current', { packages: ['sankey'] });
    
    google.charts.setOnLoadCallback(function() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'From');
        data.addColumn('string', 'To');
        data.addColumn('number', 'Weight');
        
        
        sankeyData.links.forEach(function(link) {
            data.addRow([link.source, link.target, link.value]);
        });
        
        var options = {
           
            sankey: {
                node: {
                    width: 20,
                    label: { fontName: 'Arial', fontSize: 12 }
                },
                link: {
                    colorMode: 'gradient'
                }
            }
        };
        
        var chart = new google.visualization.Sankey(
            document.getElementById('sankeyChart')
        );
        chart.draw(data, options);
    });
}

    
    function drawTimelineChart(timelineData) {
    
    if (!timelineData || timelineData.length === 0) {
        console.error("Нет данных для построения диаграммы");
        document.getElementById('timelineChart').innerHTML = 
            '<div class="alert alert-info">Нет данных для отображения</div>';
        return;
    }

    try {
        var data = new google.visualization.DataTable();
        data.addColumn({ type: 'string', id: 'User' });
        data.addColumn({ type: 'string', id: 'UserID', role: 'tooltip' });
        data.addColumn({ type: 'date', id: 'Start' });
        data.addColumn({ type: 'date', id: 'End' });

        
        timelineData.forEach(user => {
            try {
                data.addRow([
                    user.user_name, 
                    `ID: ${user.user_id}\nТестов: ${user.duration_days} дней`, 
                    new Date(user.start_year, user.start_month - 1, user.start_day),
                    new Date(user.end_year, user.end_month - 1, user.end_day)
                ]);
            } catch (e) {
                console.error("Ошибка обработки пользователя:", user, e);
            }
        });

        
        var options = {
            title: 'Хронология тестирования пользователей',
            timeline: {
                groupByRowLabel: false,
                colorByRowLabel: false,
                showRowLabels: true
            },
            colors: ['#4CAF50', '#2196F3', '#FFC107', '#F44336'],
            tooltip: { isHtml: true },
            height: Math.max(400, timelineData.length * 40 + 100),
            chartArea: { width: '80%' }
        };

        
        var container = document.getElementById('timelineChart');
        var chart = new google.visualization.Timeline(container);
        chart.draw(data, options);

        
        window.addEventListener('resize', function() {
            chart.draw(data, options);
        });

    } catch (error) {
        console.error("Ошибка построения диаграммы:", error);
        document.getElementById('timelineChart').innerHTML = 
            `<div class="alert alert-danger">Ошибка: ${error.message}</div>`;
    }
}
</script>
@endsection