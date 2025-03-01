<?php

declare(strict_types=1);

namespace App\Console;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QueryCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('db:run');
        $this->setDescription('Run a query against the database');

        $this->addArgument('query', InputArgument::REQUIRED, 'The query to run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $input->getArgument('query');

        $io = new SymfonyStyle($input, $output);
        $io->title('Running query: ' . $query);

        $result = self::SUCCESS;
        try {
            $this->connection->executeQuery($query);
            $io->success('Query executed successfully');
        } catch (\Throwable $e) {
            $result = self::FAILURE;
            $io->error($e->getMessage());
        } finally {
            return $result;
        }
    }
}
