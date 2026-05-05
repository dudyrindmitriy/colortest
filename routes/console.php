<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('generate:training-data')
    ->monthly()
    ->onSuccess(function () {
        Log::info("Сработала команда generate:training-data");
        Artisan::call('model:train');
        Log::info("Сработала команда model:train");
    })
   ;
