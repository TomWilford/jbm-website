<?php

declare(strict_types=1);

namespace App\Application\Console;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QueryCommand extends Command
{
    private string $query;

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
        $this->query = $input->getArgument('query');

        $io = new SymfonyStyle($input, $output);
        $io->title('Running query: ' . $this->query);

        $commandStatus = self::SUCCESS;
        try {
            $result = $this->connection->executeQuery($this->query);
            $this->handleOutput($result, $io);
        } catch (\Throwable $e) {
            $commandStatus = self::FAILURE;
            $io->error($e->getMessage());
        } finally {
            return $commandStatus;
        }
    }

    /**
     * @throws Exception
     */
    private function handleOutput(Result $result, SymfonyStyle $io): void
    {
        switch (true) {
            case str_starts_with(strtolower($this->query), 'select'):
                $output = $result->fetchAllAssociative();
                $columns = array_keys($output[0]);
                $io->section('Results');
                $io->table($columns, $output);
                break;
            default:
                $io->success($result->rowCount() . ' Rows Affected');
                break;
        }
    }
}
