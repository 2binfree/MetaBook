<?php

namespace App\Service;

use App\Entity\NextWordLink;
use App\Entity\SentenceNode;
use App\Entity\WordNode;
use App\Repository\SentenceRepository;
use GraphAware\Neo4j\OGM\EntityManager;
use Symfony\Component\Console\Output\OutputInterface;

class BookManager
{
    const SENTENCE_NOT_FOUND = -1;

    /** @var EntityManager  */
    private $manager;

    /** @var string */
    private $bookBaseDirectory;

    /** @var array */
    private $currentWords;

    /**
     * DBTools constructor.
     * @param EntityManager $manager
     * @param $bookBaseDirectory
     */
    public function __construct(EntityManager $manager, $bookBaseDirectory)
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
     * @return bool
     * @throws \Exception
     */
    public function import(string $bookName, OutputInterface $output)
    {
        $book = new \SplFileObject($this->bookBaseDirectory . '/' . $bookName . ".txt");
        $prevSentenceNode = null;
        $sentenceNumber = 0;
        $delay = 0;
        while (!$book->eof()) {
            $sentence = [];
            foreach ($book as $line){
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
                                    $prevSentenceNode = $this->addSentence($bookName, $prevSentenceNode, $sentence, $sentenceNumber, $delay);
                                    $output->writeln([
                                       'Import sentence ' . $sentenceNumber . ' done in ' . $delay . '(' . memory_get_usage() . ')',
                                       '',
                                    ]);
                                    if ($sentenceNumber % 20 === 0) {
                                        $this->manager->flush();
                                        $this->manager->clear();
                                        $output->writeln([
                                           'Flushing ...',
                                           '',
                                        ]);
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
        return true;
    }

    /**
     * @param string $bookName
     * @param SentenceNode|null $prevSentenceNode
     * @param array $sentence
     * @param int $sentenceNumber
     * @param float $delay
     * @return SentenceNode
     * @throws \Exception
     */
    public function addSentence(
        string $bookName,
        SentenceNode $prevSentenceNode = null,
        array $sentence,
        int $sentenceNumber,
        float &$delay
    )
    {
        $start = microtime(true);
        $currentWords = [];
        $wordOrder = 1;
        $sentenceNode = new SentenceNode($bookName);
        $sentenceNode->setOrderNumber($sentenceNumber);
        if (!is_null($prevSentenceNode)) {
            $sentenceNode->setPrevSentence($prevSentenceNode);
            $prevSentenceNode->setNextSentence($sentenceNode);
        }
        $firstWord = array_shift($sentence);
        $firstWordNode = $this->getWordNode($firstWord);
        $firstWordNode->getSentences()->add($sentenceNode);
        $this->manager->persist($sentenceNode);
        $this->manager->persist($firstWordNode);
        /** @var WordNode $prevWordNode */
        $prevWordNode = null;
        foreach ($sentence as $word)
        {
            $nextWordLink = new NextWordLink();
            $nextWordLink->setWordOrder($wordOrder++);
            $wordNode = $this->getWordNode($word);
            if (is_null($prevWordNode)) {
                $nextWordLink->setFromWord($firstWordNode);
                $firstWordNode->getNextWords()->add($nextWordLink);
            } else {
                $nextWordLink->setFromWord($prevWordNode);
                $prevWordNode->getNextWords()->add($nextWordLink);
            }
            $prevWordNode = $wordNode;
            $nextWordLink->setToWord($wordNode);
            $wordNode->getPrevWords()->add($nextWordLink);
            $nextWordLink->setSentenceId($sentenceNode->getUid());
            $this->manager->persist($wordNode);
            $this->manager->persist($nextWordLink);
            unset($wordNode);
            unset($nextWordLink);
        }
        $delay = round(microtime(true) - $start, 4);
        return $sentenceNode;
    }

    /**
     * @param string $word
     * @return WordNode
     */
    public function getWordNode(string $word)
    {
        if (isset($this->currentWords[$word])) {
            return $this->currentWords[$word];
        }
        $wordNode = $this->manager->getRepository(WordNode::class)->findOneBy(["word" => $word]);
        if (is_null($wordNode)) {
            $wordNode = new wordNode();
            $wordNode->setWord($word);
        }
        $this->currentWords[$word] = $wordNode;
        return $wordNode;
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