<?php

namespace Bbrist\Console;

use Illuminate\Log\LogManager;

class ConsoleAwareLogger
{

    private LogManager $root;
    private ConsoleLogAdapter $consoleLogAdapter;
    private bool $logToFile;
    private array $logMethods;
    private ConsoleVerbosityResolver $verbosity;

    private static array $methods = [
        'debug' => 3,
        'notice' => 2,
        'info' => 2,
        'warning' => 1,
        'error' => 0,
        'critical' => 0,
        'alert' => 0,
        'emergency' => 0,
    ];

    public function __construct(LogManager $root, ConsoleLogAdapter $adapter, array $logMethods = [], bool $logToFile = false)
    {
        $this->root = $root;
        $this->consoleLogAdapter = $adapter;
        $this->logToFile = $logToFile;
        $this->logMethods = $logMethods;
        $this->verbosity = new ConsoleVerbosityResolver();
    }

    public function logToFile(bool $shouldLogToFile = true): void
    {
        $this->logToFile = $shouldLogToFile;
    }

    public function __call($method, $parameters)
    {
        if (!app()->runningInConsole()) {
            $this->callRoot($method, $parameters);
            return;
        }

        if ($adapterHasMethod = in_array($method, array_keys(static::$methods))) {
            if ($this->verbosityTest($method)) {
                $this->consoleLogAdapter->$method(...$parameters);
            }
        }

        if (!$adapterHasMethod || $this->logToFile || $this->includedMethod($method)) {
            $this->callRoot($method, $parameters);
        }
    }

    protected function callRoot($method, $parameters)
    {
        $this->root->$method(...$parameters);
    }

    protected function includedMethod(string $method)
    {
        return in_array($method, $this->logMethods);
    }

    protected function verbosityTest(string $method): bool
    {
        if ($this->verbosity->isQuiet()) {
            return false;
        }

        $threshold = $this->verbosity->getVerbosity();

        $level = static::$methods[$method] ?? 0;
        return $level <= $threshold;
    }

}