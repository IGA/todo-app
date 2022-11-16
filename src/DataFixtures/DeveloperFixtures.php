<?php

namespace App\DataFixtures;

use App\Entity\Developer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeveloperFixtures extends Fixture
{
    private const DEVELOPER_COUNT = 5;
    private const WEEKLY_HOURS = 45;

    public function load(ObjectManager $manager): void
    {

        for ($i=1; $i<self::DEVELOPER_COUNT+1; $i++) {
            $developer = new Developer();
            $developer
                ->setName('DEV' . $i)
                ->setPower($i)
                ->setWorkingHours(self::WEEKLY_HOURS);
            $manager->persist($developer);
        }

        $manager->flush();
    }
}
