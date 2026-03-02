<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfService
{
    /**
     * Сгенерировать PDF из шаблона
     */
    public function generateFromView(string $view, array $data)
    {
        // $tempDir = storage_path('app/temp');
        // if (!file_exists($tempDir)) {
        //     mkdir($tempDir, 0755, true);
        // }

        // ini_set('sys_temp_dir', $tempDir);
        // $pdf = Pdf::loadView($view, $data);
        $html = view($view, $data)->render();
        $pdf = Pdf::loadHTML($html);



        return $pdf;
    }

    /**
     * Скачать PDF
     */
    public function download(string $view, array $data, string $filename = 'report.pdf')
    {
        $pdf = $this->generateFromView($view, $data);
        return $pdf->download($filename);
    }

    /**
     * Отобразить PDF в браузере
     */
    public function stream(string $view, array $data, string $filename = 'document.pdf')
    {
        $pdf = $this->generateFromView($view, $data);
        return $pdf->stream($filename);
    }
}
