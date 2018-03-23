<?php

namespace Y7K\Cli\Commands\Install\Resources;

use RuntimeException;

use Y7K\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Y7K\Cli\Util;

class Stylesheets extends Command
{

    protected function configure()
    {
        $this->setName('install:stylesheets')
            ->setDescription('⏳  Install SCSS Boilerplate')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where is the output folder?')
            ->addOption('remote', 'r', InputOption::VALUE_NONE, 'Load Stylesheets from online repository instead from local?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Get Paths
        $path = $input->getArgument('path');
        $filepath = $this->dir() . ($path ? '/' . $path : '');

        $output->writeln('');
        $output->writeln('Installing the <info>Default</info> SCSS Boilerplate...');
        $output->writeln('');

        $assetsDir = $filepath . '/resources/assets';
        if (!file_exists($assetsDir)) {
            mkdir($assetsDir, 0777, true);
        }

        if($input->getOption('remote')) {
            // Install the repo
            $this->installFromRemote([
                'repo' => 'y7k/style',
                'branch' => 'master',
                'path' => $assetsDir,
                'output' => $output,
                'subfolders' => ['source'],
                'success' => 'The SCSS boilerplate has been loaded from remote!',
                'checkPath' => false
            ]);
        } else {
            $this->installFromLocal([
                'sourcePath' => getenv('PATH_STYLE'),
                'subfolders' => ['source'],
                'destPath' => $assetsDir,
                'output' => $output,
                'success' => 'The SCSS boilerplate has been loaded from local!',
            ]);
        }

        // Merge the package.json files
        $packageJson = $filepath. '/package.json';
        $newPackageJsonFilepath = $filepath . '/resources/assets/package.json';

        $originalPackageJson = is_file($packageJson) ? json_decode(file_get_contents($packageJson), true) : [];
        $newPackageJson = is_file($newPackageJsonFilepath) ? json_decode(file_get_contents($newPackageJsonFilepath), true) : [];
        $mergedPackageJson = Util::mergeJsonArrays($originalPackageJson, $newPackageJson);

        // Delete the js package.json
        unlink($newPackageJsonFilepath);

        // Write to project package.json
        file_put_contents($packageJson, json_encode($mergedPackageJson, JSON_PRETTY_PRINT));
    }


}