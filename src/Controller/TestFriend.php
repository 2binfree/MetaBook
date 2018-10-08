<?php

namespace App\Controller;

use App\Entity\Friend;
use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserInMovie;
use GraphAware\Neo4j\OGM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestMovie
 * @package App\Controller
 * @Route("/friend")
 */
class TestFriend extends Controller
{
    /**
     * @Route("/create")
     * @throws \Exception
     */
    public function create()
    {
        $this->reset();
        /** @var EntityManager $manager */
        $manager = $this->get("app.neo4j.entity_manager");
        $user1 = new User();
        $user1->setName("Gérard");
        $manager->persist($user1);
        $user2 = new User();
        $user2->setName("Marcel");
        $manager->persist($user2);
        $friend = new Friend();
        $friend->setFromUser($user1);
        $friend->setToUser($user2);
        $manager->persist($friend);

        $user1->getMyFriends()->add($friend);
        $user2->getFriendOf()->add($friend);

        $manager->flush();
        return new Response("Data created");
    }

    /**
     * @Route("/read")
     */
    public function read()
    {
        /** @var EntityManager $manager */
        $manager = $this->get("app.neo4j.entity_manager");
        /** @var User $user1 */
        $user1 = $manager->getRepository("App\Entity\User")->findOneBy(["name" => "Gérard"]);
        echo  "User1 : " . $user1->getName() . "</br>";
        foreach ($user1->getMyFriends() as $friend) {
            echo "    My friend : " . $friend->getToUser()->getName() . "</br>";
        }
        foreach ($user1->getFriendOf() as $friend) {
            echo "    Friend of : " . $friend->getFromUser()->getName() . "</br>";
        }
        /** @var User $user2 */
        $user2 = $manager->getRepository("App\Entity\User")->findOneBy(["name" => "Marcel"]);
        echo  "User2 : " . $user2->getName() . "</br>";
        foreach ($user2->getMyFriends() as $friend) {
            echo "    My friend : " . $friend->getToUser()->getName() . "</br>";
        }
        foreach ($user2->getFriendOf() as $friend) {
            echo "    Friend of : " . $friend->getFromUser()->getName() . "</br>";
        }
        return new Response("End");
    }

    /**
     * @throws \Exception
     */
    public function reset()
    {
        /** @var EntityManager $manager */
        $manager = $this->get("app.neo4j.entity_manager");
        $query = $manager->createQuery("match (n) optional match (n)-[r]-() delete n,r");
        $query->execute();
    }
}