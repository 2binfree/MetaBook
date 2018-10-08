<?php

namespace App\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class Sentence
 * @package App\Entity
 * @OGM\Node(label="Sentence", repository="App\Repository\SentenceRepository")
 */
class SentenceNode
{
    /**
     * @OGM\Relationship(targetEntity="WordNode", type="FIRST_WORD", collection=false, direction="OUTGOING", mappedBy="sentences")
     * @var WordNode
     */
    private $startWord;

    /**
     * @OGM\Relationship(targetEntity="SentenceNode", type="NEXT_SENTENCE", collection=false, direction="OUTGOING", mappedBy="prevSentence")
     * @var SentenceNode
     */
    private $nextSentence;

    /**
     * @OGM\Relationship(targetEntity="SentenceNode", type="NEXT_SENTENCE", collection=false, direction="INCOMING", mappedBy="nextSentence")
     * @var SentenceNode
     */
    private $prevSentence;

    /**
     * @OGM\GraphId()
     * @var int
     */
    private $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    private $uid;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    private $orderNumber;

    public function __construct(string $bookName)
    {
        $this->uid = uniqid($bookName . "_", true);
    }

    /**
     * @return WordNode
     */
    public function getStartWord(): WordNode
    {
        return $this->startWord;
    }

    /**
     * @param WordNode $startWord
     * @return SentenceNode
     */
    public function setStartWord(WordNode $startWord): SentenceNode
    {
        $this->startWord = $startWord;
        return $this;
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
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return SentenceNode|null
     */
    public function getNextSentence(): ?SentenceNode
    {
        return $this->nextSentence;
    }

    /**
     * @param SentenceNode|null $nextSentence
     * @return SentenceNode
     */
    public function setNextSentence(?SentenceNode $nextSentence): SentenceNode
    {
        $this->nextSentence = $nextSentence;
        return $this;
    }

    /**
     * @return SentenceNode|null
     */
    public function getPrevSentence(): ?SentenceNode
    {
        return $this->prevSentence;
    }

    /**
     * @param SentenceNode|null $prevSentence
     * @return SentenceNode
     */
    public function setPrevSentence(?SentenceNode $prevSentence): SentenceNode
    {
        $this->prevSentence = $prevSentence;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

    /**
     * @param int $orderNumber
     * @return SentenceNode
     */
    public function setOrderNumber(int $orderNumber): SentenceNode
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "orderNumber" => $this->orderNumber,
            "uid" => $this->uid,
        ];
    }

    public function getType()
    {
        return "Sentence";
    }


}