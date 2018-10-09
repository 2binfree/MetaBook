<?php

namespace App\Command;

use App\Service\BookManager;
use App\Service\ImportManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportBookCommand
 * @package App\Command
 */
class ImportBookCommand extends Command
{
    /** @var ImportManager  */
    private $iManager;

    /**
     * ImportBookCommand constructor.
     * @param ImportManager $iManager
     */
    public function __construct(ImportManager $iManager)
    {
        $this->iManager = $iManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:book:import')
            ->setDescription('Import une new book')
            ->setHelp('Use this command to import a new book into BDD')
            ->addArgument('bookName', InputArgument::REQUIRED, 'The name of the book')
            ->addOption("reset", "r", null, "Reset BDD before import")
            ->addOption("detail", "d", null, "Show detailed information during importation");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption("reset")) {
            $this->resetCall($output);
        }
        $bookName = $input->getArgument('bookName');
        $output->writeln([
            '',
            'Book importation',
            '================',
            '',
            'Starting book import : ' . $bookName,
            '',
        ]);
        $result = $this->iManager->import($bookName, $output, $input->getOption('detail'));
        $output->writeln([
            '',
            "Import done in " . $result["time"] . " s, " . $result["sentences"] . ' sentences found',
            '',
        ]);
    }

    /**
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function resetCall(OutputInterface $output)
    {
        $command = $this->getApplication()->find('app:bdd:reset');
        $greetInput = new ArrayInput([]);
        $command->run($greetInput, $output);
    }

}
