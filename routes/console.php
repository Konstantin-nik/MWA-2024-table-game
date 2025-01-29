<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('php artisan backup:clean')->dailyAt('03:10')->timezone('Europe/Madrid');
Schedule::command('backup:run --only-db')->dailyAt('03:15')->timezone('Europe/Madrid');
Schedule::command('backup:run')->sundays()->at('03:20')->timezone('Europe/Madrid');
