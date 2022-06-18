<?php

namespace Bbrist\Console;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLogAdapter
{

    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function emergency($message, array $context = []): void
    {
        $this->style('critical', 'bright-white', 'red');

        $this->line("EMERGENCY: " . $message, $context, 'critical');
    }

    public function alert($message, array $context = []): void
    {
        $this->style('critical', 'bright-white', 'red');

        $this->line("ALERT: " . $message, $context, 'critical');
    }

    public function critical($message, array $context = []): void
    {
        $this->style('critical', 'bright-white', 'red');

        $this->line("CRITICAL: " . $message, $context, 'critical');
    }

    public function error($message, array $context = []): void
    {
        $this->line($message, $context, 'error');
    }

    public function warning($message, array $context = []): void
    {
        $this->style('warning', 'yellow');

        $this->line($message, $context, 'warning');
    }

    public function notice($message, array $context = []): void
    {
        $this->style('notice', 'black', 'yellow');

        $this->line("NOTICE: " . $message, $context, 'notice');
    }

    public function info($message, array $context = []): void
    {
        $this->line($message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->line($message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        if ($level < 0) {
            return;
        }

        if ($this->level($level, 0, 200)) {
            $this->debug($message, $context);
            return;
        }

        if ($this->level($level, 200, 250)) {
            $this->info($message, $context);
            return;
        }

        if ($this->level($level, 250, 300)) {
            $this->notice($message, $context);
            return;
        }

        if ($this->level($level, 300, 400)) {
            $this->warning($message, $context);
            return;
        }

        if ($this->level($level, 400, 500)) {
            $this->error($message, $context);
            return;
        }

        if ($this->level($level, 500, 550)) {
            $this->critical($message, $context);
            return;
        }

        if ($this->level($level, 550, 600)) {
            $this->alert($message, $context);
            return;
        }

        if ($this->level($level, 600)) {
            $this->emergency($message, $context);
        }
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @param  array  $context
     * @param  string|null  $style
     * @return void
     */
    public function line(string $string, array $context, ?string $style = null)
    {
        if (sizeof($context) > 0) {
            $context = json_encode($context);
            $string .= ": $context";
        }

        if ($style !== null) {
            $styled = $style ? "<$style>$string</$style>" : $string;
        }
        else {
            $styled = $string;
        }

        $this->output->writeln($styled);
    }

    protected function style(string $name, string $foreground, string $background = null)
    {
        if (! $this->output->getFormatter()->hasStyle($name)) {
            $style = new OutputFormatterStyle($foreground, $background);

            $this->output->getFormatter()->setStyle($name, $style);
        }
    }

    protected function level(int $value, int $min, ?int $max = null): bool
    {
        if ($max === null) {
            return $value >= $min;
        }

        return $value >= $min && $value < $max;
    }

}