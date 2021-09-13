<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\SplFileInfo;
use TwohundredCouches\GpxMerger\GpxMerger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class MergeGpxFilesCommand extends Command
{
    protected static $defaultName = 'app:merge-gpx-files';
    protected static $defaultDescription = 'Select a folder and merge all gpx files in it into one';

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED, 'Path to gpx files to merge (absolute or relative to project root)')
            ->addArgument('dest', InputArgument::REQUIRED, 'Filename of merged file (absolute relative to project root)')
            ->addOption('compression', 'c', InputOption::VALUE_OPTIONAL, 'Compression (0.0 - 1.0)', 0.0)
            ->setHelp('Merge all files in source path into destination.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectRoot = __DIR__ . '/../../';

        $io = new SymfonyStyle($input, $output);
        $source = $input->getArgument('source');
        $dest = $input->getArgument('dest');
        $compression = floatval($input->getOption('compression'));

        if (!str_starts_with($source, '/')) {
            $source = $projectRoot . $source;
        }

        if (!str_starts_with($dest, '/')) {
            $dest = $projectRoot . $dest;
        }

        $files = [];
        $finder = new Finder();

        $io->writeln(sprintf('Searching files in .%s..', $source));

        $finder
            ->files()
            ->in($source)
            ->depth(0)
            ->filter(static function (SplFileInfo $file) {
                return $file->isFile() && $file->getExtension() === 'gpx';
            });

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $files[] = $file->getRealPath();
            }

            $io->writeln(sprintf('Found %s file(s)', count($files)));

            if (count($files) > 0) {
                $io->writeln(sprintf('Merging into %s...', $dest));

                GpxMerger::merge($files, $dest, null, $compression);

                $io->writeln('Done!');
            }
        }

        return Command::SUCCESS;
    }
}
