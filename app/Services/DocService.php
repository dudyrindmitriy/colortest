<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\View;

class DocService
{
    /**
     * Сгенерировать DOC из Blade шаблона
     */
    public function generateFromView(string $view, array $data)
    {
        $html = View::make($view, $data)->render();

        // Добавляем мета-тег для корректного открытия в Word
        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        </head>
        <body>
            ' . $html . '
        </body>
        </html>';

        return $html;
    }

    /**
     * Скачать DOC (как HTML в .doc обертке)
     */
    public function download(string $view, array $data, string $filename = 'report.doc')
    {
        $html = $this->generateFromView($view, $data);

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $filename, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Скачать настоящий DOCX с конвертацией из Blade
     */
    public function downloadDocx(string $view, array $data, string $filename = 'report.docx')
    {
        $html = View::make($view, $data)->render();
        $html = preg_replace('/<div class="test-header"/', '<div class="test-header" style="font-size: 14pt"', $html);
        // Убираем DOCTYPE и лишние теги
        $html = preg_replace('/<!DOCTYPE[^>]*>/i', '', $html);
        $html = preg_replace('/<html[^>]*>/i', '', $html);
        $html = preg_replace('/<\/html>/i', '', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        $html = preg_replace('/<br([^>]*)(?<!\s\/)>/i', '<br$1 />', $html);
        $html = preg_replace('/<hr([^>]*)(?<!\s\/)>/i', '<hr$1 />', $html);
        // Настройка временной директории
        $tempDir = storage_path('app/temp');
        $phpWordTemp = $tempDir . '/phpword';

        if (!file_exists($phpWordTemp)) {
            mkdir($phpWordTemp, 0777, true);
        }

        // Критически важно!
        \PhpOffice\PhpWord\Settings::setTempDir($phpWordTemp);
        putenv("TMPDIR={$phpWordTemp}");
        putenv("TEMP={$phpWordTemp}");
        putenv("TMP={$phpWordTemp}");

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

        $tempFile = $tempDir . '/' . uniqid('doc_', true) . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Сохранить DOCX в файл (без скачивания)
     */
    public function saveDocx(string $view, array $data, string $outputPath)
    {
        $html = View::make($view, $data)->render();
        $html = preg_replace('/<div class="test-header"/', '<div class="test-header" style="font-size: 14pt"', $html);
        // Убираем DOCTYPE и лишние теги
        $html = preg_replace('/<!DOCTYPE[^>]*>/i', '', $html);
        $html = preg_replace('/<html[^>]*>/i', '', $html);
        $html = preg_replace('/<\/html>/i', '', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        $html = preg_replace('/<br([^>]*)(?<!\s\/)>/i', '<br$1 />', $html);
        $html = preg_replace('/<hr([^>]*)(?<!\s\/)>/i', '<hr$1 />', $html);
        // Настройка временной директории
        $tempDir = storage_path('app/temp');
        $phpWordTemp = $tempDir . '/phpword';

        if (!file_exists($phpWordTemp)) {
            mkdir($phpWordTemp, 0777, true);
        }

        // Критически важно!
        \PhpOffice\PhpWord\Settings::setTempDir($phpWordTemp);
        putenv("TMPDIR={$phpWordTemp}");
        putenv("TEMP={$phpWordTemp}");
        putenv("TMP={$phpWordTemp}");

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($outputPath);

         return file_exists($outputPath);
    }
}
