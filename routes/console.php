<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('setup',function () {
    echo "\n !STARING SETUP! \n";

    echo "\n GENERATING APPLICATION KEY ... \n";
    Artisan::call('key:generate');

    echo "\n MIGRATING FRESH DATABASE STRUCTURE ... \n";
    Artisan::call('migrate:fresh');

    echo "\n DATABASE SEEDING ... \n";
    Artisan::call('db:seed');

    echo "\n !SETUP FINISHED! \n";
})->purpose('Setup initial-environment for the project');
