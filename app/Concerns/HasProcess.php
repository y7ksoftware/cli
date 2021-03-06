<?php
namespace App\Concerns;


use Symfony\Component\Process\Process;

trait HasProcess
{

    public function runProcess(string $command, $silent = false)
    {
//        var_dump($this->verbosity);
//        $this->line("Executing command: \"{$command}\"");

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($silent) {
            if(!$silent) {
                $buffer = str_replace(array("\r", "\n"), '', $buffer);
                $this->line($buffer);
            }
        });

        return $process->getOutput();
    }

    public function runProcessSequence(array $commands, $silent = false)
    {
        return $this->runProcess(implode(' && ', $commands), $silent);
    }

}
