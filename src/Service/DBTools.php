<?php

namespace App\Service;
use GraphAware\Common\Result\Result;
use GraphAware\Neo4j\Client\Client;

/**
 * Class DBTools
 * @package App\Service
 */
class DBTools
{
    /** @var Client  */
    private $client;

    /**
     * DBTools constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \Exception
     */
    public function reset()
    {
        $this->client->run("match (n) optional match (n)-[r]-() delete n,r");
        $this->client->run("CREATE CONSTRAINT ON (w:WordNode) ASSERT w.word IS UNIQUE");
        $this->client->run("CREATE CONSTRAINT ON (s:Sentence) ASSERT s.uid IS UNIQUE");
    }

    /**
     * @param $object
     * @return array|mixed
     * @throws \Exception
     */
    public function createNode($object = null)
    {
        $type = $object->getType();
        $strQuery = "CREATE (n:$type " . $this->createPropertiesSet($object) . ") return ID(n) as id";
        /** @var \GraphAware\Neo4j\Client\Formatter\Result $result */
        $result = $this->client->run($strQuery);
        if (empty($result)) {
            return null;
        } else {
            $record = $result->getRecord();
            return $record->get("id");
        }
    }

    /**
     * @param string $fromLabel
     * @param int $fromId
     * @param string $toLabel
     * @param int $toId
     * @param string $linkType
     * @param null $linkObject
     * @throws \Exception
     */
    public function createLink(string $fromLabel, int $fromId, string $toLabel, int $toId, string $linkType, $linkObject = null)
    {
        $set = $this->createPropertiesSet($linkObject);
        $strQuery = "
            MATCH (n1:$fromLabel), (n2:$toLabel)
            WHERE ID(n1) = $fromId and ID(n2) = $toId
            CREATE (n1)-[r:$linkType $set]->(n2)
            RETURN type(r)  
        ";
        /** @var \GraphAware\Neo4j\Client\Formatter\Result $result */
        $result = $this->client->run($strQuery);
    }

    /**
     * @param string $type
     * @param string $field
     * @param string|int $value
     * @return Result|null
     * @throws \Exception
     */
    public function getNodeByField(string $type, string $field, $value)
    {
        if (is_numeric($value)){
            $search = $value;
        } else {
            $search = '"' . $value . '"';
        }
        $strQuery = "
            MATCH (n:$type) 
            WHERE n.$field = $search
            RETURN ID(n) as id
        ";
        /** @var \GraphAware\Neo4j\Client\Formatter\Result $result */
        $result = $this->client->run($strQuery);
        if (!$result->hasRecord()) {
            return null;
        }
        return $result->getRecord()->get("id");
    }

    /**
     * @param $object
     * @return bool|string
     */
    private function createPropertiesSet($object)
    {
        $set = '';
        if (!is_null($object)) {
            $properties = $object->toArray();
            $set = '{ ';
            foreach ($properties as $field => $value) {
                if (is_numeric($value)) {
                    $set .= $field . ': ' . $value . ', ';
                } else {
                    $set .= $field . ': ' . '"' . $value . '", ';
                }
            }
            $set = substr($set, 0, -2);
            $set .= " }";
        }
        return $set;
    }
}
