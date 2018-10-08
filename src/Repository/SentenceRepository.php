<?php

namespace App\Repository;

use App\Entity\WordNode;
use GraphAware\Neo4j\OGM\Repository\BaseRepository;

class SentenceRepository extends BaseRepository
{

    /**
     * @param int $number
     * @return string
     * @throws \Exception
     */
    public function getSentence(int $number) {
        $strQuery = "
            MATCH (s:Sentence), (n1:Word)-[n:NEXT]->(n2:Word) 
            WHERE s.orderNumber = {number} and n.sentenceId = s.uid 
            RETURN n1.word as word, n2.word as word2 order by n.wordOrder
        ";
        $query = $this->entityManager->createQuery($strQuery);
        $query->setParameter('number', $number);
        $result = $query->execute();
        $sentence = "";
        $words = [];
        if (!empty($result)){
            $words[] = $result[0]["word"];
            foreach ($result as $item){
                $words[] = $item['word2'];
            }
            foreach ($words as $key => $word){
                switch ($word){
                    case "'":
                    case ",":
                    case ".":
                    case "!":
                    case "?":
                    case "-":
                        $sentence .= $word;
                        break;
                    default:
                        if ("'" !== $words[$key - 1] && "-" !== $words[$key - 1]) {
                            $sentence .= " ";
                        }
                        $sentence .= $word;
                        break;
                }
            }
        }
        return $sentence;
    }
}