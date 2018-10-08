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
        $query = $this->manager->createQuery("CREATE CONSTRAINT ON (w:WordNode) ASSERT w.word IS UNIQUE");
        $query->execute();
    }
}