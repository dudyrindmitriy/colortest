<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TrainModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:train';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'training ML model by training_data.csv';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pythonPath = env('PYTHON_PATH');
        $scriptPath = base_path('app/Services/train.py');
        if (!file_exists($scriptPath)) {
            $this->error("Скрипт обучения не найден: {$scriptPath}");
            return 1;
        }

        $command = escapeshellcmd("{$pythonPath} {$scriptPath}");

        $this->info("Выполняем: {$command}");

        $output = [];
        $returnCode = null;

        exec($command . ' 2>&1', $output, $returnCode);

        foreach ($output as $line) {
            $this->line($line);
        }

        if ($returnCode === 0) {
            $this->info('✅ Обучение завершено успешно');
            return 0;
        } else {
            $this->error('❌ Ошибка при обучении модели');
            return 1;
        }
    }
}
