<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\Results;
use Illuminate\Http\Request;

class GeneralPageController extends Controller
{
    public function showGeneralPage()
    {
        $isas = Isa::all(); 
        $chess = Chess::all();
        $chartController = new ChartController();
        // $isaStats = $chartController->isaDistribution();
        // $chessStats = $chartController->chessDistribution();
        // $timeStats = $chartController->testPassagesOverTime();
        $descriptions = [
            'Творец - это человек, который проявляет высокий уровень креативности и склонен к инновациям. Творцы обычно ищут новые идеи, оригинальные решения и уникальные подходы. Они часто ориентированы на самовыражение и могут легко адаптироваться к изменениям.',
            'Рационал — это человек, который основывает свои решения на логике и анализе. Он предпочитает структуризированный подход к решению проблем и принимает решения, опираясь на факты и доказательства. Рационалы обычно менее эмоциональны и стремятся к обоснованности своих действий.',
            'Скептик — это человек, который задает вопросы и сомневается в принятых утверждениях или идеях. Скептики обычно требуют доказательства и обосновывают свои взгляды, основываясь на критическом мышлении. Они могут быть очень внимательны к деталям и часто предпочитают не принимать информацию за чистую монету.',
            'Авангардист — это человек, который находится на переднем крае изменений и нововведений. Он может быть похож на творца, но с акцентом на социальные или культурные изменения. Авангардисты часто стремятся бросать вызов традиционным нормам и экспериментируют с новыми формами искусства, мышления или общественного устройства.',
            'Смешанный тип подразумевает сочетание различных подходов и стилей мышления. Это может быть человек, который использует как творческие, так и рациональные методы для решения задач, либо тот, кто умеет быть как авангардистом, так и скептиком в зависимости от ситуации. Смешанные типы могут быть очень адаптивными и способны эффективно работать в различных контекстах.',
            ];
            return view('home', [
                'isas' => $isas,
                'chess' => $chess,
                'descriptions' => $descriptions,
                'isaStats' => $chartController->isaDistribution(),
                'chessStats' => $chartController->chessDistribution(),
                'timeStats' => $chartController->testPassagesOverTime(),
                'areaStats' => $chartController->areaDistribution(),
                'bubbleStats' => $chartController->bubbleDistribution(),
                'calendarStats' => $chartController->calendarDistribution(),
                'orgStats' => $chartController->orgDistribution(),
                'sankeyStats' => $chartController->sankeyDistribution(),
                'timelineStats' => $chartController->timelineDistribution()
            ]);
    }
}
