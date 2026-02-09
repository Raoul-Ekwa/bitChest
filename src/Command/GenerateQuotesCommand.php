<?php

namespace App\Command;

use App\Service\QuoteGeneratorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-quotes',
    description: 'Generate cryptocurrency quotations',
)]
class GenerateQuotesCommand extends Command
{
    public function __construct(
        private QuoteGeneratorService $quoteGeneratorService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('daily', 'd', InputOption::VALUE_NONE, 'Generate only today\'s quotations')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Number of days of history to generate', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('daily')) {
            $io->info('Generating daily quotations...');
            $count = $this->quoteGeneratorService->generateDailyQuotes();
            $io->success("Generated {$count} new quotations.");
        } else {
            $days = (int) $input->getOption('days');
            $io->info("Generating {$days} days of quotation history...");
            $this->quoteGeneratorService->generateInitialQuotes($days);
            $io->success("Generated quotation history for all cryptocurrencies.");
        }

        return Command::SUCCESS;
    }
}
