<?php

namespace App\Entity;

use ToBinFree\Hydrator\Hydrator;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\Common\Collections\ArrayCollection as Collection;

/**
 * Class WordNode
 * @package App\Entity
 */
class WordNode
{
    use Hydrator;

    /**
     * @var CollectionInterface
     */
    private $nextWords;

    /**
     * @var CollectionInterface
     */
    private $prevWords;

    /**
     * @var CollectionInterface
     */
    private $sentences;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $word;

    public function __construct()
    {
        $this->sentences = new Collection();
        $this->prevWords = new Collection();
        $this->nextWords = new Collection();
    }

    /**
     * @return Collection
     */
    public function getNextWords(): CollectionInterface
    {
        return $this->nextWords;
    }

    /**
     * @return Collection
     */
    public function getPrevWords(): CollectionInterface
    {
        return $this->prevWords;
    }

    /**
     * @return Collection
     */
    public function getSentences(): CollectionInterface
    {
        return $this->sentences;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @param string $word
     * @return WordNode
     */
    public function setWord(string $word): WordNode
    {
        $this->word = $word;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "word" => $this->word,
        ];
    }

    public function getType()
    {
        return "Word";
    }
}