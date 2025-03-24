<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\Results;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function downloadResultPDF($id)
    {
        $userId = Auth::id();
        $result = Results::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$result) {
            return redirect()->route('profile.result')->with('error', 'Результат не найден');
        }

        $svgCode = $result->user_image;
        $svgCode = '
        <svg>
        <style>
        svg {
            background-color: white;

            width: 50%;
            height: auto;
        }

        path {
            stroke: black;
            stroke-width: 0.5;
            fill: white;
            cursor: pointer;
        }
        </style>
        ' . $svgCode . '
        </svg>';
        $svgFile = tempnam(sys_get_temp_dir(), 'svg') . '.svg';
        file_put_contents($svgFile, $svgCode);

        $pngFile = tempnam(sys_get_temp_dir(), 'png') . '.png';
        $command = "magick $svgFile $pngFile";
        exec($command);

        $pngData = file_get_contents($pngFile);
        $base64Png = 'data:image/png;base64,' . base64_encode($pngData);

        unlink($svgFile);
        unlink($pngFile);

        $pdf = Pdf::loadView('pdf.resultPdf', [
            'result' => $result,
            'base64Png' => $base64Png, 
        ]);

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans', 
        ]);

        return $pdf->stream('result_' . $result->id . '.pdf');
    }

    public function downloadResultsPDF()
    {
        $userId = Auth::id();

        $results = Results::where('user_id', $userId)->get();
    
        if ($results->isEmpty()) {
            return redirect()->route('profile.results')->with('error', 'Результаты не найдены');
        }
    
        $resultsWithImages = [];
        foreach ($results as $result) {
            $svgCode = $result->user_image;
            $svgCode = '
            <svg>
            <style>
            svg {
                background-color: white;
                width: 50%;
                height: auto;
            }
            path {
                stroke: black;
                stroke-width: 0.5;
                fill: white;
                cursor: pointer;
            }
            </style>
            ' . $svgCode . '
            </svg>';
    
            $svgFile = tempnam(sys_get_temp_dir(), 'svg') . '.svg';
            file_put_contents($svgFile, $svgCode);
    
            $pngFile = tempnam(sys_get_temp_dir(), 'png') . '.png';
            $command = "magick $svgFile $pngFile";
            exec($command);
    
            $pngData = file_get_contents($pngFile);
            $base64Png = 'data:image/png;base64,' . base64_encode($pngData);
    
            unlink($svgFile);
            unlink($pngFile);
    
            $resultsWithImages[] = [
                'result' => $result,
                'base64Png' => $base64Png,
            ];
        }
    
        $pdf = Pdf::loadView('pdf.resultsPdf', [
            'resultsWithImages' => $resultsWithImages,
        ]);
    
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
    
        return $pdf->stream('all_results.pdf');
    }
    
    public function downloadHomePDF()
    {
        $isas = Isa::all(); 
        $chess = Chess::all();
        $isasWithImages = [];
        foreach ($isas as $isa) {
            $svgCode = $isa->image;
            $svgCode = '
            <svg>
            <style>
            svg {
                background-color: white;
                width: 50%;
                height: auto;
            }
            path {
                stroke: black;
                stroke-width: 0.5;
                fill: white;
                cursor: pointer;
            }
            </style>
            ' . $svgCode . '
            </svg>';
    
            $svgFile = tempnam(sys_get_temp_dir(), 'svg') . '.svg';
            file_put_contents($svgFile, $svgCode);
    
            $pngFile = tempnam(sys_get_temp_dir(), 'png') . '.png';
            $command = "magick $svgFile $pngFile";
            exec($command);
    
            $pngData = file_get_contents($pngFile);
            $base64Png = 'data:image/png;base64,' . base64_encode($pngData);
    
            unlink($svgFile);
            unlink($pngFile);
    
            $isasWithImages[] = [
                'isa' => $isa,
                'base64Png' => $base64Png,
            ];
        }
    
        $chessWithImages = [];
        foreach ($chess as $ches) {
            $svgCode = $ches->image;
            $svgCode = '
            <svg>
            <style>
            svg {
                background-color: white;
                width: 50%;
                height: auto;
            }
            path {
                stroke: black;
                stroke-width: 0.5;
                fill: white;
                cursor: pointer;
            }
            </style>
            ' . $svgCode . '
            </svg>';
    
            $svgFile = tempnam(sys_get_temp_dir(), 'svg') . '.svg';
            file_put_contents($svgFile, $svgCode);
    
            $pngFile = tempnam(sys_get_temp_dir(), 'png') . '.png';
            $command = "magick $svgFile $pngFile";
            exec($command);
    
            $pngData = file_get_contents($pngFile);
            $base64Png = 'data:image/png;base64,' . base64_encode($pngData);
    
            unlink($svgFile);
            unlink($pngFile);
    
            $chessWithImages[] = [
                'chess' => $ches,
                'base64Png' => $base64Png,
            ];
        }
    
        $descriptions = [
            'Творец - это человек, который проявляет высокий уровень креативности и склонен к инновациям. Творцы обычно ищут новые идеи, оригинальные решения и уникальные подходы. Они часто ориентированы на самовыражение и могут легко адаптироваться к изменениям.',
            'Рационал — это человек, который основывает свои решения на логике и анализе. Он предпочитает структуризированный подход к решению проблем и принимает решения, опираясь на факты и доказательства. Рационалы обычно менее эмоциональны и стремятся к обоснованности своих действий.',
            'Скептик — это человек, который задает вопросы и сомневается в принятых утверждениях или идеях. Скептики обычно требуют доказательства и обосновывают свои взгляды, основываясь на критическом мышлении. Они могут быть очень внимательны к деталям и часто предпочитают не принимать информацию за чистую монету.',
            'Авангардист — это человек, который находится на переднем крае изменений и нововведений. Он может быть похож на творца, но с акцентом на социальные или культурные изменения. Авангардисты часто стремятся бросать вызов традиционным нормам и экспериментируют с новыми формами искусства, мышления или общественного устройства.',
            'Смешанный тип подразумевает сочетание различных подходов и стилей мышления. Это может быть человек, который использует как творческие, так и рациональные методы для решения задач, либо тот, кто умеет быть как авангардистом, так и скептиком в зависимости от ситуации. Смешанные типы могут быть очень адаптивными и способны эффективно работать в различных контекстах.',
            ];
    
        $pdf = Pdf::loadView('pdf.homePdf', [
            'isasWithImages' => $isasWithImages,
            'chessWithImages' => $chessWithImages,
            'descriptions' => $descriptions,
        ]);
    
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans', 
        ]);
    
        return $pdf->stream('isa_chess_results.pdf');
    }
 
}
