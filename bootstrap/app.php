<?php

use App\Console\Commands\SendContractAlertsCommand;
use App\Console\Commands\SendDailyTankReport;
use App\Console\Commands\SendMonthlyTankReport;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'restrict.to.role' => \App\Http\Middleware\RestrictToRole::class,
            'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
            'restrict.client.no.tanks' => \App\Http\Middleware\RestrictClientWithNoTanks::class,

            // Localization middleware aliases
            'localizationRoutes' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    // ->withSchedule(function ($schedule) {
    //     // $schedule->command(SendDailyTankReport::class)->dailyAt('15:00');
    //     // $schedule->command(SendMonthlyTankReport::class)->monthlyOn(1, '10:00');
    //     // $schedule->command(SendContractAlertsCommand::class)->dailyAt('08:00');

    //     $schedule->command(SendDailyTankReport::class)->dailyAt('01:25');
    //     $schedule->command(SendMonthlyTankReport::class)->dailyAt('01:25');
    //     $schedule->command(SendContractAlertsCommand::class)->dailyAt('01:25');
    // })
    ->withSchedule(function ($schedule) {
        $schedule->command(SendDailyTankReport::class)
            ->dailyAt('04:25')
            ->timezone('Europe/Helsinki') // EEST (UTC+3)
           ;

        $schedule->command(SendMonthlyTankReport::class)
            ->dailyAt('04:25')
            ->timezone('Europe/Helsinki') // EEST (UTC+3)
            // ->onSuccess(function () {
            //     Log::info('Monthly tank report sent successfully at ' . now());
            // })
            // ->onFailure(function () {
            //     Log::error('Failed to send monthly tank report at ' . now());
            // })
            ;

        $schedule->command(SendContractAlertsCommand::class)
            ->dailyAt('04:25')
            ->timezone('Europe/Helsinki') // EEST (UTC+3)
            // ->onSuccess(function () {
            //     Log::info('Contract alerts sent successfully at ' . now());
            // })
            // ->onFailure(function () {
            //     Log::error('Failed to send contract alerts at ' . now());
            // })
            ;
    })
    ->create();
