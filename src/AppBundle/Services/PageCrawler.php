<?php

namespace AppBundle\Services;

use AppBundle\Entity\Car;
use AppBundle\Entity\Source;
use AppBundle\Entity\Url;
use AppBundle\Services\Site\SiteInterface;
use Doctrine\ORM\EntityManager;


class PageCrawler
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function crawl(Url $url, Source $source, SiteInterface $site)
    {
        while ($cars = $site->getCars($url)) {
            $this->saveCars($cars, $source);
        }
    }

    /**
     * @param Car[] $cars
     * @param Source $source
     */
    public function saveCars(array $cars, Source $source)
    {
        foreach ($cars as $car) {
            $car->setSource($source);

            /** @var Car $exists */
            $criteria = ['externalId' => $car->getExternalId(), 'source' => $car->getSource()];
            $exists   = $this->entityManager->getRepository(Car::class)->findOneBy($criteria);
            if ($exists) {
                $exists->setPrice($car->getPrice());
                $exists->setName($car->getName());
                $exists->setMileage($car->getMileage());
                $car = $exists;
            } else {
                $car->setCreated(new \DateTime());
            }
            $this->entityManager->persist($car);
        }
        $this->entityManager->flush();
    }
}