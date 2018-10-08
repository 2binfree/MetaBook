<?php

namespace App\Command;

use App\Service\DBTools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportBookCommand
 * @package App\Command
 */
class ResetBDDCommand extends Command
{
    private $dbTools;

    public function __construct(DBTools $dbTools)
    {
        $this->dbTools = $dbTools;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:bdd:reset')
            ->setDescription('reset the whole database');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            'Reset BDD',
            '================',
            '',
        ]);
        $this->dbTools->reset();
        $output->writeln([
            'Done !',
            '',
        ]);
    }
}
