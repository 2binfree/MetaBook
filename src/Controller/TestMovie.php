<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserInMovie;
use GraphAware\Neo4j\OGM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 05/09/18
 * Time: 12:24
 */

/**
 * Class TestMovie
 * @package App\Controller
 * @Route("/movie")
 */
class TestMovie extends Controller
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

        $movie1 = new Movie();
        $movie1->setName("Starwar");

        $manager->persist($movie1);
        $manager->flush();
        return new Response("Data created");
    }

    /**
     * @Route("/update/{name}")
     * @throws \Exception
     */
    public function update(string $name)
    {
        /** @var EntityManager $manager */
        $manager = $this->get("app.neo4j.entity_manager");
        /** @var Movie $movie */
        $movie = $manager->getRepository("App\Entity\Movie")->findOneBy(["name" => "Starwar"]);
        $exist = false;
        /** @var User $actor */
        foreach($movie->getActors() as $actor) {
            if ($actor->getName() == $name) {
                $exist = true;
                break;
            }
        }
        if (!$exist) {
            $user = new User();
            $user->setName($name);

            $userInMovie = new UserInMovie();
            $userInMovie->setDate("01/01/1999");
            $userInMovie->setSalary("5555");

            $userInMovie->setUser($user);
            $userInMovie->setMovie($movie);
            $user->getMovies()->add($userInMovie);
            $movie->getActors()->add($userInMovie);

            $manager->persist($user);
            $manager->persist($movie);
            $manager->flush();
        }
        return new Response("Updated");
    }
    /**
     * @Route("/read")
     */
    public function read()
    {
        /** @var EntityManager $manager */
        $manager = $this->get("app.neo4j.entity_manager");
        /** @var Movie $movie */
        $movie = $manager->getRepository("App\Entity\Movie")->findOneBy(["name" => "Starwar"]);
        echo  "Movie : " . $movie->getName() . "</br>";
        /** @var UserInMovie $actor */
        foreach ($movie->getActors() as $actor) {
            echo "    Acteur : " . $actor->getUser()->getName() . "</br>";
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