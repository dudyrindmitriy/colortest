<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\EducationProgram;

class FetchEducationPrograms extends Command
{
    protected $signature = 'parse:education-programs';
    protected $description = 'Стягивает направления подготовки с сайта МГУ им. Огарёва';

    public function handle()
    {
        $this->info('Начало парсинга направлений подготовки...');

        $url = 'https://mrsu.ru/ru/education/graduate/';

        $response = Http::get($url);

        if ($response->failed()) {
            $this->error('Не удалось загрузить страницу. Код ответа: ' . $response->status());
            return Command::FAILURE;
        }

        $html = $response->body();

        $crawler = new Crawler($html);
        $rawPrograms = [];

        $crawler->filter('.container-mrsu-pad')->each(function (Crawler $container) use (&$rawPrograms) {
            $container->filter('.card.spec-card')->each(function (Crawler $card) use (&$rawPrograms) {
                $codeNode = $card->filter('.spec-card-title-text-mini');
                $nameNode = $card->filter('h5.spec-card-title-text');

                if ($codeNode->count() > 0 && $nameNode->count() > 0) {
                    $code = trim(str_replace('<br>', '', $codeNode->text()));
                    $name = trim($nameNode->text());

                    if (preg_match('/^[A-Za-z\s]+$/', $name)) {
                        return;
                    }

                    $rawPrograms[] = [
                        'code' => $code,
                        'name' => $name,
                    ];
                }
            });
        });

        $this->info('Сырые данные успешно собраны. Всего найдено карточек: ' . count($rawPrograms));

        $updatedCount = 0;
        $createdCount = 0;
        $activeIds = [];

        foreach ($rawPrograms as $program) {
            $cleanName = mb_convert_encoding($program['name'], 'UTF-8', 'UTF-8');
            $cleanName = str_replace(['М?неджмент', 'м?неджмент'], ['Менеджмент', 'менеджмент'], $cleanName);
            $cleanName = trim(str_replace('?', '', $cleanName));
            $cleanName = preg_replace('/\s+/', ' ', $cleanName);

            $model = EducationProgram::firstOrNew([
                'code' => $program['code'],
            ]);

            if ($model->exists) {
                if ($model->name !== $cleanName) {
                    $oldName = $model->name;
                    $model->name = $cleanName;
                    $model->save();

                    $this->line("<comment>[Изменено]</comment> Код {$program['code']}: Было '{$oldName}' -> Стало '{$cleanName}'");
                } else {
                    $this->line("[Без изменений] Код {$program['code']}: {$cleanName}");
                }
                $updatedCount++;
            } else {
                $model->name = $cleanName;
                $model->save();

                $this->line("<info>[Новое]</info> Создан код {$program['code']}: {$cleanName}");
                $createdCount++;
            }

            $activeIds[] = $model->id;
        }
        $activeIds = array_unique($activeIds);

        $missingPrograms = EducationProgram::whereNotIn('id', $activeIds)->get();
        $deletedCount = $missingPrograms->count();

        if ($deletedCount > 0) {
            $this->line("");
            $this->error("--- СЛЕДУЮЩИХ НАПРАВЛЕНИЙ БОЛЬШЕ НЕТ НА САЙТЕ ВУЗА ---");
            foreach ($missingPrograms as $missing) {
                $this->line("<error>[Удалено на сайте]</error> Код {$missing->code}: {$missing->name}");

                $this->line("");
            }
        }
        $this->info("Синхронизация с базой завершена!");
        $this->comment("Проверено / обновлено существующих: {$updatedCount}");
        $this->comment("Добавлено абсолютно новых: {$createdCount}");
        $this->comment("Отсутствуют на сайте (архивные): {$deletedCount}");

        return Command::SUCCESS;
    }
}
