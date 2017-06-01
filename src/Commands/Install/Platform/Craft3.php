<?php

namespace Y7K\Cli\Commands\Install\Platform;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Y7K\Cli\Util;

class Craft3 extends Command
{

    protected function configure()
    {
        $this->setName('install:craft3')
            ->setDescription('Install Craft 3 CMS. Plus some Y7K Magic Sugar.')
            ->addArgument('path', InputArgument::REQUIRED, 'Where shall that Project live in?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $path = $input->getArgument('path');
        $filepath = $this->dir() . '/' . $path;

        $this->install([
            'repo' => 'y7k/plate',
            'branch' => 'develop',
            'path' => $filepath,
            'output' => $output,
            'subfolders' => ['1-base', '2-platforms/craft3'],
            'success' => 'The craft boilerplate is installed!',
        ]);

//        $this->install([
//            'url' => 'http://craftcms.com/latest.zip?accept_license=yes',
//            'path' => $filepath . '/craft/app',
//            'output' => $output,
//            'subfolders' => ['craft/app'],
//            'success' => 'The craft app folder is installed!',
//        ]);

        Util::findAndReplaceInFile($filepath . '/.env.example', '{name}', $path);
        Util::findAndReplaceInFile($filepath . '/composer.json', '{name}', $path);

        $commands = [
            'install --no-scripts',
            'run-script post-root-package-install'
        ];

        $this->runComposerCommands($input, $output, $path, $commands);

    }

}