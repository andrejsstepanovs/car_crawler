<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Source;


class LoadSourceData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $entities = [];


        $entity = new Source();
        $entity->setUrl('suchen.mobile.de');
        $entity->setName('Mobile');
        $entities[] = $entity;

        $entity = new Source();
        $entity->setUrl('ss.com');
        $entity->setName('ss');
        $entities[] = $entity;

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}