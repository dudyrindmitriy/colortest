@extends('layouts.app')

@section('content')
<style>
    .main-content{
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-direction: column;
    }
    .box {
        display: flex;
        justify-content: space-between;
        align-items: center;

    }
    .for_test{
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        align-items: center;
    }

    @media (max-width: 768px) {
        .box {
            flex-direction: column;
            align-items: center;
        }

        .color-input {
            margin-top: 16px;
            /* Достаточное расстояние для удобства */
            width: 90%;
            height: 200px;
            /* Увеличиваем ширину для мобильного удобства */
        }

        svg {
            width: 100%;
            /* Уменьшаем размер SVG для мобильного экрана */
            height: auto;
            /* Сохраняем пропорции */
        }
    }
</style>
<div id="loading" style="display: none;">
    <div class="spinner"></div>
</div>
<div class="main-content">

    <div class="box">
        <div class="for_test">
            <h2>Раскрасьте данный шаблон</h2>
        <svg width="756" height="963" viewBox="0 0 756 963" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_2_545)">
                <rect width="756" height="963" fill="white" />
                <path d="M69.9214 940.036L69.9214 580.315L104.5 563.793L104.5 895.329L69.9214 940.036Z" stroke="#FF0111" data-x="1" data-y="1" data-z="1" />
                <path d="M104.98 894.711L104.98 563.524L136.75 547.805L136.75 853.635L104.98 894.711Z" stroke="#FF0112" data-x="1" data-y="1" data-z="2" />
                <path d="M137.153 853.046L137.153 547.648L171 531.786L171 809.329L137.153 853.046Z" stroke="#FF0000" data-x="1" data-y="1" data-z="3" />
                <path d="M171.089 809.051L171.089 531.58L204.936 515.295L204.936 765.827L171.089 809.051Z" stroke="#FF0114" data-x="1" data-y="1" data-z="4" />
                <path d="M205 765.059L205 515.314L232 502.296L232 730.827L205 765.059Z" stroke="#FF0115" data-x="1" data-y="1" data-z="5" />
                <path d="M232.25 730.526L232.25 502.065L260 488.793L260 694.33L232.25 730.526Z" stroke="#FF0116" data-x="1" data-y="1" data-z="6" />
                <path d="M260 694.536L260 488.816L288.375 475.292L288.375 657.829L260 694.536Z" stroke="#FF0117" data-x="1" data-y="1" data-z="7" />
                <path d="M288.5 657.543L288.5 475.195L315.5 462.293L315.5 622.828L288.5 657.543Z" stroke="#FF0118" data-x="1" data-y="1" data-z="8" />
                <path d="M315.56 622.749L315.56 462.248L343.5 448.796L343.5 586.828L315.56 622.749Z" stroke="#FF0119" data-x="1" data-y="1" data-z="9" />
                <path d="M343.636 586.645L343.636 448.751L371.576 435.299L371.5 550.828L343.636 586.645Z" stroke="#FF0000" data-x="1" data-y="1" data-z="10" />
                <path d="M371.5 550.805L371.5 435.312L398 422.302L398 516.33L371.5 550.805Z" stroke="#FF0000" data-x="1" data-y="1" data-z="11" />
                <path d="M398 516.346L398 422.319L424.995 409.786L424.501 482.327L398 516.346Z" stroke="#FF0000" data-x="1" data-y="1" data-z="12" />
                <path d="M104.829 563.587L70 580.207L70 331.677L104.829 332.489L104.829 563.587Z" stroke="#FF0000" data-x="1" data-y="2" data-z="1" />
                <path d="M137.079 547.691L105 563.692L105 332.512L137.079 333.303L137.079 547.691Z" stroke="#FF0122" data-x="1" data-y="2" data-z="2" />
                <path d="M171.001 531.683L137 547.712L137 333.345L171.499 333.991L171.001 531.683Z" stroke="#FF0000" data-x="1" data-y="2" data-z="3" />
                <path d="M205 515.187L171.002 531.7L171.499 334.015L205 334.986L205 515.187Z" stroke="#FF0000" data-x="1" data-y="2" data-z="4" />
                <path d="M232 502.186L205 515.204L205 335.012L232 335.672L232 502.186Z" stroke="#FF0000" data-x="1" data-y="2" data-z="5" />
                <path d="M259.75 488.966L232 502.207L232 335.678L259.75 336.347V488.966Z" stroke="#FF0000" data-x="1" data-y="2" data-z="6" />
                <path d="M288 475.176L259.5 488.701L259.5 336.342L288 336.981L288 475.176Z" stroke="#FF0000" data-x="1" data-y="2" data-z="7" />
                <path d="M315.5 462.186L288.5 475.204L288.5 337.013L315.5 337.717L315.5 462.186Z" stroke="#FF0000" data-x="1" data-y="2" data-z="8" />
                <path d="M343.5 448.686L315.5 462.203L315.5 337.739L343.5 338.487L343.5 448.686Z" stroke="#FF0000" data-x="1" data-y="2" data-z="9" />
                <path d="M371.5 435.186L343.5 448.703L343.5 338.512L371.5 339.165V435.186Z" stroke="#FF0000" data-x="1" data-y="2" data-z="10" />
                <path d="M398 422.188L371.5 435.198L371.5 339.157L398 339.825L398 422.188Z" stroke="#FF0000" data-x="1" data-y="2" data-z="11" />
                <path d="M424.998 409.682L398 422.217L398 339.827L424.503 340.488L424.998 409.682Z" stroke="#FF0000" data-x="1" data-y="2" data-z="12" />
                <path d="M70.0031 331.514L71.9938 16.9741L103.501 39.7564L104.997 332.486L70.0031 331.514Z" stroke="#FF0000" data-x="1" data-y="3" data-z="1" />
                <path d="M104.998 332.515L103.505 39.9866L137.5 64.754L137.001 333.485L104.998 332.515Z" stroke="#FF0000" data-x="1" data-y="3" data-z="2" />
                <path d="M137.001 333.283L137.499 64.7598L171.5 89.5668L171.5 333.65L137.001 333.283Z" stroke="#FF0000" data-x="1" data-y="3" data-z="3" />
                <path d="M171.5 334.007L171.555 89.7179L205.499 114.752L205.001 334.493L171.5 334.007Z" stroke="#FF0000" data-x="1" data-y="3" data-z="4" />
                <path d="M205.001 335.152L205.498 114.487L232 133.975L232 335.819L205.001 335.152Z" stroke="#FF0000" data-x="1" data-y="3" data-z="5" />
                <path d="M232 335.304L232 133.984L259.5 154.04L259.5 335.878L232 335.304Z" stroke="#FF0000" data-x="1" data-y="3" data-z="6" />
                <path d="M259.5 336.016L259.5 153.983L288.001 174.755L288.498 336.983L259.5 336.016Z" stroke="#FF0000" data-x="1" data-y="3" data-z="7" />
                <path d="M288.499 336.517L288.003 174.978L315.5 194.756L315.5 337.482L288.499 336.517Z" stroke="#FF0000" data-x="1" data-y="3" data-z="8" />
                <path d="M315.75 337.517L315.75 194.984L343.5 215.254L343.5 338.482L315.75 337.517Z" stroke="#FF0000" data-x="1" data-y="3" data-z="9" />
                <path d="M371.002 339L343.75 339L343.75 215.422L371.499 235.753L371.002 339Z" stroke="#FF0000" data-x="1" data-y="3" data-z="10" />
                <path d="M371.002 339.018L371.495 235.482L398.499 255.253L398.003 339.982L371.002 339.018Z" stroke="#FF0000" data-x="1" data-y="3" data-z="11" />
                <path d="M398.003 340.009L398.494 255.474L424.5 274.256L424.5 340.491L398.003 340.009Z" stroke="#FF0000" data-x="1" data-y="3" data-z="12" />
                <path d="M72.59 16.4219L281.386 16.0693L297.54 39.9971L107.747 41.7807L72.59 16.4219Z" stroke="black" data-x="2" data-y="1" data-z="1" />
                <path d="M107.407 42.0318L298.325 40.5916L314.947 64.5103L139.13 64.8072L107.407 42.0318Z" stroke="black" data-x="2" data-y="1" data-z="2" />
                <path d="M139.213 65.401L315.754 64.2651L332.882 89.0346L172.85 90.1302L139.213 65.401Z" stroke="black" data-x="2" data-y="1" data-z="3" />
                <path d="M174.052 90.7378L333.675 89.6315L348.871 113.276L206.583 114.376L174.052 90.7378Z" stroke="black" data-x="2" data-y="1" data-z="4" />
                <path d="M206.891 114.949L349.611 113.872L361.846 133.712L233.13 133.929L206.891 114.949Z" stroke="black" data-x="2" data-y="1" data-z="5" />
                <path d="M234.248 134.383L362.067 134.167L374.796 154.706L262.054 154.725L234.248 134.383Z" stroke="black" data-x="2" data-y="1" data-z="6" />
                <path d="M262.481 155.214L375.038 155.309L389.144 175L289.867 175.168L262.481 155.214Z" stroke="black" data-x="2" data-y="1" data-z="7" />
                <path d="M290.477 175.487L389.47 175.319L402.158 194.723L317.227 194.867L290.477 175.487Z" stroke="black" data-x="2" data-y="1" data-z="8" />
                <path d="M317.817 195.193L402.436 195.05L416.524 214.185L344.589 214.862L317.817 195.193Z" stroke="black" data-x="2" data-y="1" data-z="9" />
                <path d="M345.168 215.4L416.85 214.729L428.632 235.054L372.168 235.149L345.168 215.4Z" stroke="black" data-x="2" data-y="1" data-z="10" />
                <!-- <path d="M672.626 330.494L673.33 35.5432L657.38 60.351L657.616 331.465L672.626 330.494Z" stroke="#0DFF00" data-x="2" data-y="1" data-z="12" /> -->
                <path d="M373.483 236.008L428.82 235.641L441.039 255.049L398.792 255.12L373.483 236.008Z" stroke="black" data-x="2" data-y="1" data-z="11" />
                <path d="M399.996 255.724L440.789 255.655L452.934 273.233L425.389 273.28L399.996 255.724Z" stroke="black" data-x="2" data-y="1" data-z="12" />
                <path d="M281.517 16.0691L492.834 15.7122L493.581 38.9203L298.158 40.5521L281.517 16.0691Z" stroke="black" data-x="2" data-y="2" data-z="1" />
                <path d="M315.581 64.5087L297.997 40.3033L493.352 38.8527L493.394 63.6459L315.581 64.5087Z" stroke="black" data-x="2" data-y="2" data-z="2" />
                <path d="M315.416 64.265L493.395 63.9645L493.437 88.7587L333.016 89.0296L315.416 64.265Z" stroke="black" data-x="2" data-y="2" data-z="3" />
                <path d="M333.31 89.349L492.94 89.0794L492.98 112.464L349.459 113.269L333.31 89.349Z" stroke="black" data-x="2" data-y="2" data-z="4" />
                <path d="M362.419 133.565L349.707 113.585L492.981 112.784L493.015 133.344L362.419 133.565Z" stroke="black" data-x="2" data-y="2" data-z="5" />
                <!-- <path d="M672.626 330.494L673.33 35.5432L657.38 60.351L657.616 331.465L672.626 330.494Z" stroke="#0DFF00" data-x="2" data-y="2" data-z="6" /> -->
                <path d="M375.133 154.707L362.634 134.165L493.002 133.665L492.567 154.509L375.133 154.707Z" stroke="black" data-x="2" data-y="2" data-z="6" />
                <!-- <path d="M672.626 330.494L673.33 35.5432L657.38 60.351L657.616 331.465L672.626 330.494Z" stroke="#0DFF00" data-x="2" data-y="2" data-z="8" /> -->
                <path d="M390.29 175.387L375.7 155.133L492.541 154.936L492.105 174.828L390.29 175.387Z" stroke="black" data-x="2" data-y="2" data-z="7" />
                <!-- <path d="M672.626 330.494L673.33 35.5432L657.38 60.351L657.616 331.465L672.626 330.494Z" stroke="#0DFF00" data-x="2" data-y="2" data-z="10" /> -->
                <path d="M390.088 175.319L492.092 175.146L492.125 194.21L402.762 194.361L390.088 175.319Z" stroke="black" data-x="2" data-y="2" data-z="8" />
                <!-- <path d="M672.626 330.494L673.33 35.5432L657.38 60.351L657.616 331.465L672.626 330.494Z" stroke="#0DFF00" data-x="2" data-y="3" data-z="1" /> -->
                <path d="M416.7 214.178L403.084 195.046L492.111 194.618L491.675 214.331L416.7 214.178Z" stroke="black" data-x="2" data-y="2" data-z="9" />
                <path d="M428.663 234.925L416.902 214.503L491.649 214.932L491.213 234.819L428.663 234.925Z" stroke="black" data-x="2" data-y="2" data-z="10" />
                <path d="M429.402 235.639L491.201 235.535L491.234 255.248L441.533 255.053L429.402 235.639Z" stroke="black" data-x="2" data-y="2" data-z="11" />
                <path d="M453.576 273.232L441.9 255.658L491.214 255.85L490.637 273.17L453.576 273.232Z" stroke="black" data-x="2" data-y="2" data-z="12" />
                <path d="M493.384 38.9159L492.724 15.7543L685.599 15.4286L671.45 38.5021L493.384 38.9159Z" stroke="black" data-x="2" data-y="3" data-z="1" />
                <path d="M493.396 63.4153L493.252 39.4149L670.709 39.1153L655.596 62.8068L493.396 63.4153Z" stroke="black" data-x="2" data-y="3" data-z="2" />
                <path d="M493.413 63.5828L655.379 63.4083L640.231 88.4165L493.413 88.6644L493.413 63.5828Z" stroke="black" data-x="2" data-y="3" data-z="3" />
                <path d="M493.052 89.2328L640.068 88.0905L624.327 112.417L493.052 112.417L493.052 89.2328Z" stroke="black" data-x="2" data-y="3" data-z="4" />
                <path d="M492.746 112.349L624.075 112.349L610.827 132.917L492.746 132.917L492.746 112.349Z" stroke="black" data-x="2" data-y="3" data-z="5" />
                <path d="M597.622 153.67L492.866 153.847L492.831 133.285L610.283 133.087L597.622 153.67Z" stroke="black" data-x="2" data-y="3" data-z="6" />
                <path d="M493.333 154.827L597.872 154.651L585.23 174.669L492.584 174.826L493.333 154.827Z" stroke="black" data-x="2" data-y="3" data-z="7" />
                <path d="M492.395 175.145L584.977 174.71L572.347 194.443L492.428 194.298L492.395 175.145Z" stroke="black" data-x="2" data-y="3" data-z="8" />
                <path d="M492.415 194.899L572.103 194.764L559.462 214.781L491.979 214.336L492.415 194.899Z" stroke="black" data-x="2" data-y="3" data-z="9" />
                <path d="M491.941 214.936L559.207 215.099L546.084 235.403L491.087 235.062L491.941 214.936Z" stroke="black" data-x="2" data-y="3" data-z="10" />
                <path d="M533.691 255.271L491.156 255.343L491.123 235.537L545.851 235.72L533.691 255.271Z" stroke="black" data-x="2" data-y="3" data-z="11" />
                <path d="M521.784 273.117L490.956 273.169L490.927 255.853L533.412 255.781L521.784 273.117Z" stroke="black" data-x="2" data-y="3" data-z="12" />
                <path d="M689.17 329.484L685.714 16.2311L673.46 35.1133L672.753 330.457L689.17 329.484Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="1" />
                <path d="M672.626 330.284L673.331 35.3847L657.38 60.3498L657.617 331.255L672.626 330.284Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="2" />
                <path d="M657.489 331.267L657.254 60.5542L641.3 85.4032L641.3 331.635L657.489 331.267Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="3" />
                <path d="M641.173 331.999L641.147 85.7551L625.22 110.831L625.455 332.485L641.173 331.999Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="4" />
                <path d="M625.329 333.156L625.094 110.762L612.687 130.239L612.687 333.823L625.329 333.156Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="5" />
                <path d="M612.56 333.309L612.56 130.446L599.68 150.498V333.883L612.56 333.309Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="6" />
                <path d="M599.554 334.03L599.554 150.638L586.201 171.413L585.966 334.997L599.554 334.03Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="7" />
                <path d="M585.839 334.536L586.073 171.834L573.195 191.608L573.195 335.5L585.839 334.536Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="8" />
                <path d="M572.95 335.546L572.95 192.036L559.953 212.303L559.953 336.511L572.95 335.546Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="9" />
                <path d="M546.945 337.038L559.707 337.038L559.707 212.671L546.711 232.999L546.945 337.038Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="10" />
                <path d="M546.818 337.061L546.586 232.924L533.942 252.687L534.175 338.025L546.818 337.061Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="11" />
                <path d="M534.048 338.059L533.817 253.106L521.644 271.874L521.644 338.541L534.048 338.059Z" stroke="#0DFF00" data-x="3" data-y="1" data-z="12" />
                <path d="M672.832 563.841L689.177 580.493L689.177 329.919L672.832 330.732V563.841Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="1" />
                <path d="M657.579 547.794L672.624 563.814L672.624 330.762L657.579 331.554L657.579 547.794Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="2" />
                <path d="M641.536 531.629L657.49 547.685L657.49 331.602L641.301 332.249L641.536 531.629Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="3" />
                <path d="M625.456 514.975L641.408 531.516L641.174 332.28L625.456 333.252L625.456 514.975Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="4" />
                <path d="M612.687 501.849L625.329 514.861L625.329 333.286L612.687 333.946L612.687 501.849Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="5" />
                <path d="M599.562 488.501L612.56 501.741L612.56 333.959L599.562 334.627L599.562 488.501Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="6" />
                <path d="M586.201 474.577L599.553 488.105L599.553 334.629L586.201 335.267L586.201 474.577Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="7" />
                <path d="M573.195 461.463L585.838 474.476L585.838 335.307L573.195 336.011L573.195 461.463Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="8" />
                <path d="M559.953 447.833L573.068 461.35L573.068 336.04L559.953 336.787L559.953 447.833Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="9" />
                <path d="M546.71 434.203L559.826 447.719L559.826 336.819L546.71 337.473L546.71 434.203Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="10" />
                <path d="M534.177 421.08L546.583 434.082L546.583 337.471L534.177 338.139L534.177 421.08Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="11" />
                <path d="M521.409 408.451L534.05 420.981L534.05 338.148L521.642 338.808L521.409 408.451Z" stroke="#0DFF00" data-x="3" data-y="2" data-z="12" />
                <path d="M687.708 939.157L689.213 580.9L672.987 564.35L672.987 898.822L687.708 939.157Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="1" />
                <path d="M672.634 897.848L672.633 563.948L657.735 548.211L657.735 856.727L672.634 897.848Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="2" />
                <path d="M657.417 855.782L657.417 547.921L641.536 532.033L641.537 811.993L657.417 855.782Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="3" />
                <path d="M641.367 811.365L641.367 531.696L625.486 515.385L625.486 768.072L641.367 811.365Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="4" />
                <path d="M625.329 766.952L625.329 515.273L612.687 502.261L612.687 732.733L625.329 766.952Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="5" />
                <path d="M612.441 732.077L612.441 501.897L599.444 488.627L599.444 695.886L612.441 732.077Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="6" />
                <path d="M599.317 695.742L599.317 488.52L586.024 474.995L586.024 659.032L599.317 695.742Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="7" />
                <path d="M585.838 658.394L585.838 474.768L573.195 461.871L573.195 623.694L585.838 658.394Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="8" />
                <path d="M573.04 623.265L573.04 461.695L559.953 448.244L559.953 587.346L573.04 623.265Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="9" />
                <path d="M559.761 586.813L559.761 448.068L546.674 434.617L546.71 550.999L559.761 586.813Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="10" />
                <path d="M546.583 550.623L546.583 434.499L534.177 421.497L534.177 516.168L546.583 550.623Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="11" />
                <path d="M534.05 515.836L534.05 421.383L521.411 408.855L521.643 481.836L534.05 515.836Z" stroke="#0DFF00" data-x="3" data-y="3" data-z="12" />
                <path d="M493.743 898.676L492.857 940.267L687.425 940.267L672.886 898.676L493.743 898.676Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="1" />
                <path d="M656.889 855.658L493.347 854.66L493.347 898.341L672.41 898.341L656.889 855.658Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="2" />
                <path d="M493.427 854.842L656.929 855.339L641.385 810.326L493.427 810.326L493.427 854.842Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="3" />
                <path d="M624.893 768.238L492.752 768.238L492.752 809.841L640.886 809.841L624.893 768.238Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="4" />
                <path d="M611.39 731.159L492.757 731.159L492.757 767.841L624.904 767.841L611.39 731.159Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="5" />
                <path d="M598.386 694.159L492.757 694.159L492.757 731.341L611.424 731.341L598.386 694.159Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="6" />
                <path d="M493.243 693.841L598.914 693.841L585.888 657.659L492.439 657.659L493.243 693.841Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="7" />
                <path d="M492.257 657.842L585.907 658.338L572.889 622.66L492.257 623.157L492.257 657.842Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="8" />
                <path d="M492.248 622.841L572.914 622.841L559.889 586.66L491.766 587.655L492.248 622.841Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="9" />
                <path d="M491.741 587.339L559.906 586.845L546.391 550.16L490.829 550.932L491.741 587.339Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="10" />
                <path d="M533.887 514.994L490.874 514.994L490.874 550.839L546.422 550.346L533.887 514.994Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="11" />
                <path d="M521.872 483.408L490.642 483.408L490.642 514.841L533.889 514.841L521.872 483.408Z" stroke="#FF00BF" data-x="4" data-y="1" data-z="12" />
                <path d="M280.111 940.341L493.243 940.341L493.243 898.657L296.607 897.16L280.111 940.341Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="1" />
                <path d="M314.601 853.659L296.647 896.846L493.743 898.339L493.743 854.657L314.601 853.659Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="2" />
                <path d="M314.134 854.841L493.743 854.841L493.743 810.159L332.104 810.159L314.134 854.841Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="3" />
                <path d="M332.111 810.341L493.244 810.341L493.244 768.157L348.608 767.16L332.111 810.341Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="4" />
                <path d="M361.612 731.159L348.585 767.345L493.244 768.339L493.244 731.159L361.612 731.159Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="5" />
                <path d="M374.369 693.659L361.569 730.843L493.235 731.34L492.752 693.659L374.369 693.659Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="6" />
                <path d="M389.594 656.969L374.64 693.651L492.735 693.651L492.252 657.657L389.594 656.969Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="7" />
                <path d="M389.098 657.841L492.244 657.841L492.244 623.312L402.11 622.66L389.098 657.841Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="8" />
                <path d="M416.105 587.658L402.127 622.844L492.234 623.34L491.752 587.161L416.105 587.658Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="9" />
                <path d="M428.098 551.361L416.052 588.334L491.735 587.344L491.252 551.36L428.098 551.361Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="10" />
                <path d="M428.58 550.841L491.244 550.841L491.244 515.161L441.019 515.658L428.58 550.841Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="11" />
                <path d="M453.109 483.408L441.107 515.335L491.23 514.844L490.605 483.408L453.109 483.408Z" stroke="#FF00BF" data-x="4" data-y="2" data-z="12" />
                <path d="M69.5942 940.341L280.392 940.341L296.892 897.15L105.518 894.553L69.5942 940.341Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="1" />
                <path d="M104.59 894.852L297.395 896.84L314.376 853.659L137.06 853.659L104.59 894.852Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="2" />
                <path d="M136.569 853.35L314.894 854.84L332.381 810.153L170.949 808.688L136.569 853.35Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="3" />
                <path d="M171.579 808.351L332.889 809.84L348.412 767.153L204.855 765.625L171.579 808.351Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="4" />
                <path d="M204.583 765.353L348.886 766.84L361.426 730.899L231.536 730.899L204.583 765.353Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="5" />
                <path d="M232.08 730.841L361.386 730.841L374.423 693.66L260.607 693.966L232.08 730.841Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="6" />
                <path d="M260.452 693.849L374.397 693.341L388.862 657.659L288.561 657.659L260.452 693.849Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="7" />
                <path d="M288.59 657.841L388.89 657.841L401.902 622.673L316.06 622.673L288.59 657.841Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="8" />
                <path d="M316.076 622.841L401.899 622.841L416.353 588.15L343.561 587.16L316.076 622.841Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="9" />
                <path d="M343.569 586.947L416.381 587.93L428.448 551.131L371.279 551.131L343.569 586.947Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="10" />
                <path d="M372.043 550.35L428.387 550.84L440.917 515.665L398.037 515.665L372.043 550.35Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="11" />
                <path d="M398.637 515.341L440.394 515.341L452.879 483.408L424.774 483.408L398.637 515.341Z" stroke="#FF00BF" data-x="4" data-y="3" data-z="12" />
            </g>
            <defs>
                <clipPath id="clip0_2_545">
                    <rect width="756" height="963" fill="white" />
                </clipPath>
            </defs>
        </svg></div>


        <div class="coordinates-tooltip" id="coordinatesTooltip"></div>
        <div class="for_test"><h2>Выберите цвет</h2>
        <input type="color" class="color-input" id="colorPicker" value="#ffffff" onchange="setColor(this.value)"></div>
    </div>
    <button id="saveResult">Сохранить результат</button>
    <script>
        let currentColor = 'white';

        function setColor(color) {
            currentColor = color;
        }

        const paths = document.querySelectorAll('path');
        const tooltip = document.getElementById('coordinatesTooltip');

        paths.forEach(path => {
            path.addEventListener('mouseenter', function(event) {
                const x = this.getAttribute('data-x');
                const y = this.getAttribute('data-y');
                const z = this.getAttribute('data-z');
                tooltip.textContent = `x: ${x}, y: ${y}, z: ${z}`;
                tooltip.style.left = `${event.clientX + 10}px`;
                tooltip.style.top = `${event.clientY + 10}px`;
                tooltip.style.display = 'block';
            });

            path.addEventListener('mouseleave', function() {
                tooltip.style.display = 'none';
            });

            path.addEventListener('click', function() {
                this.style.fill = currentColor;
            });
        });

        document.getElementById('saveResult').addEventListener('click', function() {
            const svgCode = document.querySelector('svg').outerHTML;
            const rectangles = [];
            const paths = document.querySelectorAll('path');

            paths.forEach(path => {
                rectangles.push({
                    color: path.style.fill || 'rgb(255, 255, 255)',
                    x: path.getAttribute('data-x'),
                    y: path.getAttribute('data-y'),
                    z: path.getAttribute('data-z'),
                });
            });

            const loading = document.getElementById('loading');

            fetch('/color-test-app/public/save-result', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        rectangles: rectangles,
                        svg: svgCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert('Результат сохранен!');
                    console.log(data);
                    window.location.href = '/color-test-app/public/results';
                })
                .catch(error => console.error('Ошибка:', error))
                .finally(() => {
                    loading.style.display = 'none';
                });


            loading.style.display = 'flex';
        });

        function generateSvgCode(rectangles) {
            let svgHeader = '<svg xmlns="http://www.w3.org/2000/svg" width="500" height="500">';
            let svgContent = '';

            rectangles.forEach(rectangle => {
                svgContent += `<rect x="${rectangle.x}" y="${rectangle.y}" width="50" height="50" fill="${rectangle.color}" />`;
            });

            let svgFooter = '</svg>';
            return svgHeader + svgContent + svgFooter;
        }
    </script>

</div>
@endsection