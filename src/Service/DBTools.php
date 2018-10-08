<?php

namespace App\Service;

use GraphAware\Neo4j\OGM\EntityManager;

/**
 * Class DBTools
 * @package App\Service
 */
class DBTools
{
    private $manager;

    /**
     * DBTools constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @throws \Exception
     */
    public function reset()
    {
        $query = $this->manager->createQuery("match (n) optional match (n)-[r]-() delete n,r");
        $query->execute();
        $query = $this->manager->createQuery("CREATE CONSTRAINT ON (w:WordNode) ASSERT w.word IS UNIQUE");
        $query->execute();
        $query = $this->manager->createQuery("CREATE CONSTRAINT ON (s:Sentence) ASSERT s.uid IS UNIQUE");
        $query->execute();
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
        $query = $this->manager->createQuery($strQuery);
        $result = $query->execute();
        if (empty($result)) {
            return null;
        } else {
            return $result[0]["id"];
        }
    }

    /**
     * @param string $fromLabel
     * @param int $fromId
     * @param string $toLabel
     * @param int $toId
     * @param string $linkType
     * @param null $linkObject
     * @return array|mixed
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
        $query = $this->manager->createQuery($strQuery);
        $result = $query->execute();
        return $result;
    }

    /**
     * @param string $type
     * @param string $field
     * @param string|int $value
     * @return \GraphAware\Neo4j\OGM\Query
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
        $query = $this->manager->createQuery($strQuery);
        $result = $query->execute();
        if (empty($result)) {
            return null;
        }
        return $result[0]["id"];
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
