<?php

namespace Bbrist\Console;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;

class ServiceProvider extends LaravelServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/logging.php', 'logging');

        $adapter = new ConsoleLogAdapter(new ConsoleOutput());

        $this->app->instance(ConsoleLogAdapter::class, $adapter);
        $this->app->extend('log', function ($logger) use ($adapter) {
            $logMethods = Config::get('logging.console.logMethods', []);
            $logToFile = Config::get('logging.console.logToFile', false);

            return new ConsoleAwareLogger($logger, $adapter, $logMethods, $logToFile);
        });
    }

}