<?php

declare(strict_types=1);

namespace App\Console;

use App\Database\Seeds\SeedInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SeedCommand extends Command
{
    /**
     * @param array<SeedInterface> $seeds
     */
    public function __construct(
        private readonly array $seeds = []
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('db:seed');
        $this->setDescription('Seed the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing Seed Data');

        foreach ($this->seeds as $seed) {
            $io->section(sprintf('Seeding %s', $seed->getName()));

            $io->progressStart(count($seed->getData()));

            foreach ($seed->getData() as $entity) {
                $seed->getRepository()->store($entity);
                $io->progressAdvance();
            }
            $io->progressFinish();
        }
        $io->newLine();

        $io->success('Seed Data Imported');

        return 0;
    }
}
