<?php

namespace App\Command;

use App\Service\BookManager;
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
    /** @var BookManager  */
    private $bManager;

    /**
     * ImportBookCommand constructor.
     * @param BookManager $bManager
     */
    public function __construct(BookManager $bManager)
    {
        $this->bManager = $bManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:book:import')
            ->setDescription('Import une new book')
            ->setHelp('Use this command to import a new book into BDD')
            ->addArgument('bookName', InputArgument::REQUIRED, 'The name of the book')
            ->addOption("reset", "r", null, "Reset BDD before import");
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
        $result = $this->bManager->import($bookName, $output);
    }

    /**
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function resetCall(OutputInterface $output)
    {
        $command = $this->getApplication()->find('app:bdd:reset');
        $greetInput = new ArrayInput([]);
        $returnCode = $command->run($greetInput, $output);
    }

}
