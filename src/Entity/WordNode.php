<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection as CollectionInterface;
use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

;

/**
 * Class WordNode
 * @package App\Entity
 * @OGM\Node(label="Word")
 */
class WordNode
{
    /**
     * @OGM\Relationship(relationshipEntity="NextWordLink", type="NEXT", collection=true, direction="OUTGOING", mappedBy="fromWord")
     * @var Collection
     */
    private $nextWords;

    /**
     * @OGM\Relationship(relationshipEntity="NextWordLink", type="NEXT", collection=true, direction="INCOMING", mappedBy="toWord")
     * @var Collection
     */
    private $prevWords;

    /**
     * @OGM\Relationship(targetEntity="SentenceNode", type="FIRST_WORD", collection=true, direction="INCOMING", mappedBy="startWord")
     * @var Collection
     */
    private $sentences;

    /**
     * @OGM\GraphId()
     * @var int
     */
    private $id;

    /**
     * @OGM\Property(type="string")
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
}