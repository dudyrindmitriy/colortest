<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfService
{

    public function generateFromView(string $view, array $data)
    {
        $html = view($view, $data)->render();
        $pdf = Pdf::loadHTML($html);
        return $pdf;
    }


    public function download(string $view, array $data, string $filename = 'report.pdf')
    {
        $pdf = $this->generateFromView($view, $data);
        return $pdf->download($filename);
    }


    public function stream(string $view, array $data, string $filename = 'document.pdf')
    {
        $pdf = $this->generateFromView($view, $data);
        return $pdf->stream($filename);
    }
}
