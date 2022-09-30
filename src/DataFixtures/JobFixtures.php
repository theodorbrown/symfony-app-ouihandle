<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class JobFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jobs = [
            "Programmer",
            "Truck driver",
            "Fisher",
            "Teacher",
            "Dancer",
            "Race car driver",
            "Spy",
            "Dentist",
            "Actor",
            "Builder"
        ];

        foreach ($jobs as $j) {
            $job = new Job();
            $job->setName($j);
            $manager->persist($job);
        }
        $manager->flush();
    }
}
