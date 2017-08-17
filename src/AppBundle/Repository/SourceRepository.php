<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Source;
use AppBundle\Entity\Url;

/**
 * SourceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SourceRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Url $url
     * @return Source
     */
    public function findByUrl(Url $url)
    {
        $query = $this->createQueryBuilder('m');
        $query->where('m.url LIKE :url')->setParameter('url', '%' . $url->getUrlHost() . '%');

        $result = $query->getQuery()->getResult();
        if ($result) {
            return $result[0];
        }
        return null;
    }
}
