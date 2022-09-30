<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HobbyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $hobbies = [
            "Computer programming",
            "Fashion design",
            "Karaoke",
            "Writing music",
            "Candy making",
            "Car Spotting",
            "Watching documentaries",
            "Cooking",
            "Puzzles",
            "Robot combat"
        ];

        foreach ($hobbies as $h) {
            $hobby = new Hobby();
            $hobby->setName($h);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
