<?php

namespace App\DataFixtures;

use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $d = new DateTime();

        for ($i = 0; $i < 20; $i++) {

            $post = new Post();

            $post->setTitle('titre du post n° '.$i);

            $post->setContent('bla bla bla bla bli bli bli bli');

            $post->setCreatedAt($d);

            $manager->persist($post);

        }

        $manager->flush();
    }
}
