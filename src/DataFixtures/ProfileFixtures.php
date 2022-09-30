<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setName('Instagram');
        $profile->setUrl('https://www.instagram.com/theodor_brn/');
        $manager->persist($profile);

        $profile2 = new Profile();
        $profile2->setName('LinkedIn');
        $profile2->setUrl('https://www.linkedin.com/');
        $manager->persist($profile2);

        $profile3 = new Profile();
        $profile3->setName('Facebook');
        $profile3->setUrl('https://www.facebook.com/');
        $manager->persist($profile3);

        $profile4 = new Profile();
        $profile4->setName('Twitter');
        $profile4->setUrl('https://twitter.com/');
        $manager->persist($profile4);


        $manager->flush();
    }
}
