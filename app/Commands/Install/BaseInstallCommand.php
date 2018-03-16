<?php
namespace App\Commands\Install;


use App\Commands\BaseCommand;
use App\Concerns\HasProcess;
use App\Concerns\InstallsRepository;

abstract class BaseInstallCommand extends BaseCommand
{
    use InstallsRepository, HasProcess;

    public function installPlate($options, $remote){
        $installRepositorycommand = ($remote) ? 'installRepositoryFromGitHub' : 'installRepositoryFromLocalSource';
        $this->{$installRepositorycommand}('y7k/plate', $options);
    }

    public function createDestinationPath($destinationPath)
    {
        if (!is_dir($destinationPath) && !mkdir($destinationPath) && !is_dir($destinationPath)) {
            $this->abort("Directory {$destinationPath} already exists or could not be created");
        }
    }

    public function runPostInstallComposerCommands($destinationPath)
    {
        // Run Composer Commands
        $this->info('Run Composer...');

        $commands = [
            "cd {$destinationPath}",
            "composer install --no-scripts",
            "composer run-script post-root-package-install"
        ];

        $this->runProcessSequence($commands);
    }

}