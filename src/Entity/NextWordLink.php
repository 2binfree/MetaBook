<?php

namespace App\Entity;
use ToBinFree\Hydrator\Hydrator;

/**
 * Class NextWordLink
 * @package App\Entity
 */
class NextWordLink
{
    use Hydrator;

    /**
     * @var int
     */
    private $id;

    /**
     * @var WordNode
     */
    private $fromWord;

    /**
     * @var WordNode
     */
    private $toWord;

    /**
     * @var string
     */
    private $sentenceId;

    /**
     * @var int
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

    public function toArray()
    {
        return [
            "wordOrder" => $this->wordOrder,
            "sentenceId" => $this->sentenceId,
        ];
    }

    public function getType()
    {
        return "NEXT";
    }
}