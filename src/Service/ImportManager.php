<?php

namespace App\Service;

use App\Entity\NextWordLink;
use App\Entity\SentenceNode;
use App\Entity\WordNode;
use App\Repository\SentenceRepository;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ImportManager
{
    const SENTENCE_NOT_FOUND = -1;

    /** @var DBTools  */
    private $manager;

    /** @var string */
    private $bookBaseDirectory;

    /** @var array */
    private $currentWords;

    /**
     * DBTools constructor.
     * @param DBTools $manager
     * @param $bookBaseDirectory
     */
    public function __construct(DBTools $manager, $bookBaseDirectory)
    {
        $this->manager = $manager;
        $this->bookBaseDirectory = $bookBaseDirectory;
        $this->currentWords = [];
    }

    /**
     * @return string
     */
    public function getBookBaseDirectory()
    {
        return $this->bookBaseDirectory;
    }

    /**
     * @param string $bookName
     * @param OutputInterface $output
     * @return array
     * @throws \Exception
     */
    public function import(string $bookName, OutputInterface $output, $verbose = false)
    {
        $book = new \SplFileObject($this->bookBaseDirectory . '/' . $bookName . ".txt");
        $book->seek(PHP_INT_MAX);
        $lines = $book->key() + 1;
        $book->seek(0);
        $progress = 0;
        $progressBar = new ProgressBar($output, $lines);
        $progressBar->setFormat('debug');
        $prevSentenceNodeId = null;
        $sentenceNumber = 0;
        $delay = 0;
        $totalDelay = microtime(true);
        while (!$book->eof()) {
            $sentence = [];
            foreach ($book as $line){
                $progress++;
                $line =  str_replace(["\r", "\n"], '', $line);
                if (!empty($line)) {
                    $words = explode(" ", $line);
                    while (!empty($words)) {
                        $word = array_shift($words);
                        if (!empty($word)) {
                            if (strpos($word, "'")) {
                                $subWords = explode("'", $word);
                                $sentence[] = $subWords[0];
                                $sentence[] = "'";
                                array_unshift($words, $subWords[1]);
                                continue;
                            }
                            if (strpos($word, "-")) {
                                $subWords = explode("-", $word);
                                $sentence[] = $subWords[0];
                                $sentence[] = "-";
                                array_unshift($words, $subWords[1]);
                                continue;
                            }
                            switch (substr($word, -1)) {
                                case ",":
                                    $sentence[] = substr($word, 0, -1);
                                    $sentence[] = ",";
                                    break;
                                case ".":
                                case "?":
                                case "!":
                                    $sentence[] = substr($word, 0, -1);
                                    $sentence[] = substr($word, -1);
                                    $sentenceNumber++;
                                    $prevSentenceNodeId = $this->addSentence($bookName, $prevSentenceNodeId, $sentence, $sentenceNumber, $delay);
                                    if (true === $verbose) {
                                        $output->writeln([
                                            'Import sentence ' . $sentenceNumber . ' done in ' . $delay . '(' . memory_get_usage() . ')',
                                        ]);
                                    } else {
                                        $progressBar->setProgress($progress);
                                        $progressBar->display();
                                    }
                                    $sentence = [];
                                    break;
                                default:
                                    $sentence[] = $word;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        $progressBar->finish();
        return [
            "time" => round(microtime(true) - $totalDelay, 2),
            "sentences" => $sentenceNumber,
        ];
    }

    /**
     * @param string $bookName
     * @param int|null $prevSentenceNodeId
     * @param array $sentence
     * @param int $sentenceNumber
     * @param float $delay
     * @return SentenceNode
     * @throws \Exception
     */
    public function addSentence(
        string $bookName,
        int $prevSentenceNodeId = null,
        array $sentence,
        int $sentenceNumber,
        float &$delay
    )
    {
        $start = microtime(true);
        $wordOrder = 1;
        $sentenceNode = new SentenceNode($bookName);
        $sentenceNode->setOrderNumber($sentenceNumber);
        $sentenceNodeId = $this->manager->createNode($sentenceNode);
        if (!is_null($prevSentenceNodeId)) {
            $this->manager->createLink("Word", $prevSentenceNodeId, "Word", $sentenceNodeId, "NEXT_SENTENCE");
        }
        $firstWord = array_shift($sentence);
        $firstWordNodeId = $this->getWordNode($firstWord);
        $this->manager->createLink("Sentence", $sentenceNodeId, "Word", $firstWordNodeId, "FIRST_WORD");
        /** @var WordNode $prevWordNode */
        $prevWordNodeId = null;
        foreach ($sentence as $word)
        {
            $nextWordLink = new NextWordLink();
            $nextWordLink->setWordOrder($wordOrder++);
            $nextWordLink->setSentenceId($sentenceNode->getUid());
            $wordNodeId = $this->getWordNode($word);
            if (is_null($prevWordNodeId)) {
                $this->manager->createLink("Word", $firstWordNodeId, "Word", $wordNodeId, "NEXT", $nextWordLink);
            } else {
                $this->manager->createLink("Word", $prevWordNodeId, "Word", $wordNodeId, "NEXT", $nextWordLink);
            }
            $prevWordNodeId = $wordNodeId;
        }
        $delay = round(microtime(true) - $start, 4);
        return $sentenceNodeId;
    }

    /**
     * @param string $word
     * @return int
     * @throws \Exception
     */
    public function getWordNode(string $word)
    {
        if (isset($this->currentWords[$word])) {
            return $this->currentWords[$word];
        }
        $wordNodeId = $this->manager->getNodeByField("Word", "word", $word);
        if (is_null($wordNodeId)) {
            $wordNode = new WordNode();
            $wordNode->setWord($word);
            $wordNodeId = $this->manager->createNode($wordNode);
        }
        $this->currentWords[$word] = $wordNodeId;
        return $wordNodeId;
    }

    /**
     * @param int $orderNumber
     * @return string
     * @throws \Exception
     */
    public function getSentence(int $orderNumber)
    {
        /** @var SentenceRepository $repository */
        $repository = $this->manager->getRepository(SentenceNode::class);
        $sentence = $repository->getSentence($orderNumber);
        return $sentence;
    }
}