<?php

namespace App\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\SplFileInfo;
use TwohundredCouches\GpxMerger\GpxMerger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class MergeAllGpxFilesCommand extends Command
{
    protected static $defaultName = 'app:merge-all-gpx-files';
    protected static $defaultDescription = 'Looks through every subdirectory of a given directory and merges all gpx files of a subdirectory.';

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED, 'Path to directory that contains subdirectories with gpx files to merge (relative to project root)')
            ->addOption('compression', 'c', InputOption::VALUE_OPTIONAL, 'Compression (0.0 - 1.0)', 0.0)
            ->setHelp('Looks through every subdirectory of a given directory (source) and merges all gpx files of a subdirectory.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mergeCommand = $this->getApplication()->find('app:merge-gpx-files');

        $io = new SymfonyStyle($input, $output);

        $source = $input->getArgument('source');

        if (!str_starts_with($source, '/')) {
            $projectRoot = __DIR__ . '/../../';
            $source = $projectRoot . $source;
        }

        $compression = floatval($input->getOption('compression'));

        $finder = new Finder();

        $io->writeln('Searching directories...');

        $finder
            ->directories()
            ->in($source)
            ->depth(0);

        if ($finder->hasResults()) {
            foreach ($finder as $directory) {
                $io->writeln(sprintf('Scanning %s', $directory->getRealPath()));

                $gpxFinder = new Finder();

                $gpxFinder
                    ->files()
                    ->in($directory->getRealPath())
                    ->depth(0)
                    ->filter(static function (SplFileInfo $file) {
                        return $file->isFile() && $file->getExtension() === 'gpx';
                    });

                $io->writeln(sprintf('%s gpx file(s) found.', $gpxFinder->count()));

                if ($gpxFinder->hasResults()) {
                    $arguments = [
                        'source' => $directory->getRealPath(),
                        'dest' => $source . '/' . $directory->getBasename() . '_' . str_replace('.', '_', $compression) . '.gpx',
                        '--compression' => $compression
                    ];

                    $mergeCommandInput = new ArrayInput($arguments);

                    $mergeCommand->run($mergeCommandInput, $output);
                }
            }
        }

        return Command::SUCCESS;
    }
}
