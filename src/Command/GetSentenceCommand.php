<?php
/**
 * Copyright (c) 2018. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Command;

use App\Service\BookManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportBookCommand
 * @package App\Command
 */
class GetSentenceCommand extends Command
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
            ->setName('app:sentence:get')
            ->setDescription('Display sentence')
            ->setHelp('Use this command to get sentence by order number from a book')
            ->addArgument('sentenceNumber', InputArgument::REQUIRED, 'The number of sentence');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sentenceNumber = $input->getArgument('sentenceNumber');
        $output->writeln([
            '',
            'Get sentence number ' . $sentenceNumber,
            '================',
            '',
        ]);
        $result = $this->bManager->getSentence($sentenceNumber);
        if (empty($result)) {
            $output->writeln([
               'Sentence not found',
               '',
            ]);
        } else {
            $output->writeln([
                'sentence : ' . $result,
                '',
            ]);
        }
    }
}
