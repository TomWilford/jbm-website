<?php

declare(strict_types=1);

namespace App\Console;

use App\Database\Seeds\SeedInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $output->writeln('<info>Importing Seed Data</info>');

        foreach ($this->seeds as $seed) {
            $output->writeln(sprintf('<info>Seeding %s</info>', $seed->getName()));

            $progressBar = new ProgressBar($output, count($seed->getData()));

            foreach ($seed->getData() as $entity) {
                $seed->getRepository()->store($entity);
                $progressBar->advance();
            }
            $progressBar->finish();
        }

        $output->writeln('');
        $output->writeln('<info>Seed Data Imported</info>');

        return 0;
    }
}
