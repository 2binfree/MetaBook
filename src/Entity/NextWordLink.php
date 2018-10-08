<?php

namespace App\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class NextWordLink
 * @package App\Entity
 * @OGM\RelationshipEntity(type="NEXT")
 */
class NextWordLink
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    private $id;

    /**
     * @OGM\StartNode(targetEntity="WordNode")
     * @var WordNode
     */
    private $fromWord;

    /**
     * @OGM\EndNode(targetEntity="WordNode")
     * @var WordNode
     */
    private $toWord;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    private $sentenceId;

    /**
     * @var int
     * @OGM\Property(type="int")
     */
    private $wordOrder;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return WordNode
     */
    public function getFromWord(): WordNode
    {
        return $this->fromWord;
    }

    /**
     * @param WordNode $fromWord
     * @return NextWordLink
     */
    public function setFromWord(WordNode $fromWord): NextWordLink
    {
        $this->fromWord = $fromWord;
        return $this;
    }

    /**
     * @return WordNode
     */
    public function getToWord(): WordNode
    {
        return $this->toWord;
    }

    /**
     * @param WordNode $toWord
     * @return NextWordLink
     */
    public function setToWord(WordNode $toWord): NextWordLink
    {
        $this->toWord = $toWord;
        return $this;
    }

    /**
     * @return string
     */
    public function getSentenceId(): string
    {
        return $this->sentenceId;
    }

    /**
     * @param string $sentenceId
     * @return NextWordLink
     */
    public function setSentenceId(string $sentenceId): NextWordLink
    {
        $this->sentenceId = $sentenceId;
        return $this;
    }

    /**
     * @return int
     */
    public function getWordOrder(): int
    {
        return $this->wordOrder;
    }

    /**
     * @param int $wordOrder
     * @return NextWordLink
     */
    public function setWordOrder(int $wordOrder): NextWordLink
    {
        $this->wordOrder = $wordOrder;
        return $this;
    }
}