<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // 1. Importa la fachada Schedule

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 2. Programa el comando de respaldo aquí
Schedule::command('backup:run')->daily(); 
