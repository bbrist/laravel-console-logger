<?php

namespace Bbrist\Console;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;

class ConsoleVerbosityResolver
{

    private InputInterface $input;

    public function __construct(?InputInterface $input = null)
    {
        $this->input = $input ?: new ArgvInput();
    }

    public function getVerbosity(): int
    {
        if ($this->isQuiet()) {
            return -1;
        }

        if ($this->isVeryVeryVerbose()) {
            return 3;
        }

        if ($this->isVeryVerbose()) {
            return 2;
        }

        if ($this->isVerbose()) {
            return 1;
        }

        return 0;
    }

    public function isQuiet(): bool
    {
        return $this->input->hasParameterOption(['--quiet', '-q'], true);
    }

    public function isVeryVeryVerbose(): bool
    {
        return $this->isVerboseOption(3);
    }

    public function isVeryVerbose(): bool
    {
        return $this->isVerboseOption(2);
    }

    public function isVerbose(): bool
    {
        return $this->isVerboseOption(1);
    }

    protected function isVerboseOption(int $i): bool
    {
        return $this->input->hasParameterOption('-'.str_repeat('v', $i), true) ||
            $this->input->hasParameterOption("--verbose=$i", true) ||
            $i === $this->input->getParameterOption('--verbose', false, true);
    }

}