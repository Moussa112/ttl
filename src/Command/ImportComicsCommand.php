<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\ImportComicsMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-comics',
    description: 'Import comics from Marvel API as products',
)]
class ImportComicsCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'batch-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'The number of comics to process in each batch.',
                10
            )
            ->addOption(
                'total',
                null,
                InputOption::VALUE_OPTIONAL,
                'The total number of comics to process.',
                100
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = (int) $input->getOption('batch-size');
        $totalComics = (int) $input->getOption('total');

        for ($offset = 0; $offset < $totalComics; $offset += $batchSize) {
            $this->commandBus->dispatch(new ImportComicsMessage($batchSize, $offset));
        }

        return Command::SUCCESS;
    }
}
