<?php

namespace Bbrist\Console;

use Illuminate\Log\LogManager;

class ConsoleAwareLogger
{

    private LogManager $root;
    private ConsoleLogAdapter $consoleLogAdapter;
    private bool $logToFile;
    private array $logMethods;

    public function __construct(LogManager $root, ConsoleLogAdapter $adapter, array $logMethods = [], bool $logToFile = false)
    {
        $this->root = $root;
        $this->consoleLogAdapter = $adapter;
        $this->logToFile = $logToFile;
        $this->logMethods = $logMethods;
    }

    public function logToFile(bool $shouldLogToFile = true): void
    {
        $this->logToFile = $shouldLogToFile;
    }

    public function __call($method, $parameters)
    {
        if ($adapterHasMethod = method_exists($this->consoleLogAdapter, $method)) {
            $this->consoleLogAdapter->$method(...$parameters);
        }

        if (!$adapterHasMethod || $this->logToFile || $this->includedMethod($method)) {
            $this->root->$method(...$parameters);
        }
    }

    protected function includedMethod(string $method)
    {
        return in_array($method, $this->logMethods);
    }

}