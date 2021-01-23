<?php

namespace App\Console;

use App\Console\Commands\CreateConfigurationMigrationTable;
use App\Console\Commands\CreateNewUser;
use App\Console\Commands\CreateSchema;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'create-schema' => CreateSchema::class,
        'create-user' => CreateNewUser::class,
        'create-config-migration' => CreateConfigurationMigrationTable::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
